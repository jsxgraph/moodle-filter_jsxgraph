<?php
// This file is part of JSXGraph Moodle Filter.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JSXGraph is a cross-browser JavaScript library for interactive geometry,
 * function plotting, charting, and data visualization in the web browser.
 * JSXGraph is implemented in pure JavaScript and does not rely on any other
 * library. Special care has been taken to optimize the performance.
 *
 * This is a plugin for Moodle to enable function plotting and dynamic
 * geometry constructions with JSXGraph within a Moodle platform.
 *
 * @package    jsxgraph filter
 * @copyright  2020 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 *             Matthias Ehmann,
 *             Michael Gerhaeuser,
 *             Carsten Miller,
 *             Andreas Walter <andreas.walter@uni-bayreuth.de>,
 *             Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $PAGE, $CFG;

require_once($CFG->libdir . '/pagelib.php');

class filter_jsxgraph extends moodle_text_filter {
    public static $recommended = '1.00.0';
    public static $jsxcore     = '/filter/jsxgraph/jsxgraphcore.js';

    /**
     * Main filter function
     *
     * @param string $text
     * @param array  $options
     *
     * @return string
     */
    public function filter($text, array $options = array()) {
        // To optimize speed, search for a <jsxgraph> tag (avoiding to parse everything on every text).
        if (!is_int(strpos($text, '<jsxgraph'))) {
            return $text;
        }

        return $this->get_text_between_tags("jsxgraph", $text);
    }

    /**
     * Replace <jsxgraph ...> tag
     *
     * @get text between tags
     *
     * @param string $tag The tag name
     * @param string $html The HTML string
     * @param string $encoding
     *
     * @return string
     */
    private function get_text_between_tags($tag, $html) {
        global $PAGE;

        $encoding = "UTF-8";
        $setting = $this->get_adminsettings();

        $ref_boardid_title = "BOARDID";

        /* 1. STEP ---------------------------
         * Convert HTML-String to a dom object
         */

        // Create a new dom object
        $dom = new domDocument('1.0', $encoding);
        $dom->formatOutput = true;

        // Load the html into the object
        libxml_use_internal_errors(true);
        if ($setting["convertencoding"]) {
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $encoding));
        } else {
            $dom->loadHTML($html);
        }
        libxml_use_internal_errors(false);

        // Discard white space
        $dom->preserveWhiteSpace = false;
        $dom->strictErrorChecking = false;
        $dom->recover = true;

        /* 2. STEP --------
         * Get tag elements
         */

        $taglist = $dom->getElementsByTagname($tag);
        $require = false;
        $error = false;

        if (!empty($taglist)) {
            $tmp = $this->load_jsxgraph(
                $setting['jsxfromserver'],
                $setting['serverversion']
            );
            if ($tmp[0] === 'error') {
                $error = $tmp[1];
            } else {
                $require = $tmp[1] === 'withREQUIRE';
            }
        }

        /* 3. STEP -----------------------------------
         * Iterate backwards through the jsxgraph tags
         */

        for ($i = $taglist->length - 1; $i > -1; $i--) {

            $item = $taglist->item($i);
            $tagattribute = $this->get_tagattributes($item);

            // Create new div element containing JSXGraph
            $out = $dom->createElement('div');

            $a = $dom->createAttribute('id');
            $divid = $this->string_or($tagattribute['boardid'], $tagattribute['box']);
            $divid = $this->string_or($divid, $setting['divid'] . $i);
            $a->value = $divid;
            $out->appendChild($a);

            $a = $dom->createAttribute('class');
            $a->value = 'jxgbox';
            $out->appendChild($a);

            $a = $dom->createAttribute('style');
            $w = $this->string_or($tagattribute['width'], $setting['width']);
            $h = $this->string_or($tagattribute['height'], $setting['height']);
            if (is_numeric($w)) {
                $w .= 'px';
            }
            if (is_numeric($h)) {
                $h .= 'px';
            }
            $a->value = 'width:' . $w . '; height:' . $h . '; ';
            $out->appendChild($a);

            // Replace <jsxgraph>-node by a <div>-node
            $item->parentNode->replaceChild($dom->appendChild($out), $item);

            if ($error !== false) {
                $t = $dom->createElement('p');
                $a = $dom->createAttribute('class');
                $a->value = 'jxg-error';
                $t->appendChild($a);
                $a = $dom->createElement('b');
                $a->textContent = get_string('error', 'filter_jsxgraph');
                $t->appendChild($a);
                $a = $dom->createElement('span');
                $a->textContent = $error;
                $t->appendChild($a);
                $out->appendChild($t);
                continue;
            }

            /* 4. STEP ------
             * Construct code
             */

            $generalcode = '';
            $globalcode = '';

            // Define boardid const
            $generalcode .= "\n/** Define boardid const */\n";
            $generalcode .= "const $ref_boardid_title = '$divid';\n";
            $generalcode .= "console.log('board `'+$ref_boardid_title+'` has been integrated');\n";

            // Load global JavaScript code from administrator settings
            if ($setting['globalJS'] !== '' && $tagattribute['useGlobalJS']) {
                $globalcode .= "\n// Global JavaScript code of the administrator\n";
                $globalcode .= $setting['globalJS'];
                if (substr_compare($setting['globalJS'], ';', $setting['globalJS'] . length - 1) < 0) {
                    $globalcode .= ';';
                }
            }
            $globalcode .= "\n\n";

            // Load code from <jsxgraph>-node
            $jscode = "\n// Specific JavaScript code\n";
            // Integrate specific JavaScript
            $jscode .= $dom->saveHTML($item);
            // Remove <jsxgraph> tags
            $jscode = preg_replace("(</?" . $tag . "[^>]*\>)i", "", $jscode);
            // In order not to terminate the JavaScript part prematurely, the backslash has to be escaped
            $jscode = str_replace("</script>", "<\/script>", $jscode);

            // Convert HTML-Entities in code
            if ($setting['HTMLentities'] && $tagattribute['entities']) {
                $globalcode = html_entity_decode($globalcode);
                $jscode = html_entity_decode($jscode);
            }

            $jscode = $generalcode . $jscode;

            // Complete the code
            $code = '';
            if ($require) {
                $codeprefix = "require(['jsxgraphcore'], function (JXG) { if (document.getElementById('$divid') != null) { \n";
                $codepostfix = "}\n });\n";
                $code = $globalcode . $codeprefix . $jscode . $codepostfix;
            } else {
                $codeprefix = "\nif (document.getElementById('$divid') != null) {";
                $codepostfix = "};";
                $code = $codeprefix . $globalcode . $jscode . $codepostfix;
            }
            $code = "\n//< ![CDATA[\n" . $code . "\n//]]>\n";
            $code =
                "\n\n// #########################################" .
                "\n// JavaScript code for JSXGraph board '$divid'\n" .
                $code .
                "\n// End Code for JSXGraph board '$divid' " .
                "\n// #########################################\n\n";

            // Place JavaScript code at the end of the page.
            $PAGE->requires->js_init_call($code);
        }

        /* 5. STEP ----------------------
         * Paste new div node in web page
         */

        // Remove DOCTYPE
        $dom->removeChild($dom->firstChild);
        // Remove <html><body></body></html>
        $str = $dom->saveHTML();
        $str = str_replace("<body>", "", $str);
        $str = str_replace("</body>", "", $str);
        $str = str_replace("<html>", "", $str);
        $str = str_replace("</html>", "", $str);

        return $str;
    }

    private function load_jsxgraph($fromserver, $serverversion = "") {
        global $PAGE, $CFG;

        $result = ['success', 'withREQUIRE'];

        $url = self::$jsxcore;

        if ($this->convert_bool($fromserver)) {
            // Handle several special cases
            switch ($serverversion) {
                case '':
                    break;
                case '0.99.6': // Error with requirejs in version 0.99.6
                    $result[0] = 'error';
                    $result[1] = get_string('error0.99.6', 'filter_jsxgraph');

                    return $result;
                case '0.99.5': // Cloudfare-error with version 0.99.5
                    $result[0] = 'error';
                    $result[1] = get_string('error0.99.5', 'filter_jsxgraph');

                    return $result;
                default:
                    $url = 'https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/' . $serverversion . '/jsxgraphcore.js';
            }

            // Check if the entered version exists on the server
            if ($tmp = fopen($url, 'r') === false) {
                $result[0] = 'error';
                $result[1] =
                    get_string('errorNotFound_pre', 'filter_jsxgraph') .
                    $serverversion .
                    get_string('errorNotFound_post', 'filter_jsxgraph');

                return $result;
            } else {
                if (isset($tmp)) {
                    fclose($tmp);
                }
            }

            // Decide how the code should be included.
            // For versions after 0.99.6, it must be included with "require".
            $tmp = $serverversion;
            $version = [];
            while ($pos = strpos($tmp, '.')) {
                array_push($version, intval(substr($tmp, 0, $pos)));
                $tmp = substr($tmp, $pos + 1);
            }
            array_push($version, $tmp);
            if ($version[0] <= 0 && $version[1] <= 99 && $version[2] <= 6) {
                $result[1] = 'withoutREQUIRE';
            } else {
                $result[1] = 'withREQUIRE';
            }
        }

        $PAGE->requires->js(new moodle_url($url));

        return $result;
    }

    /**
     * Get settings made by administrator
     *
     * @get settings from administration
     * @return array
     */
    private function get_adminsettings() {
        global $PAGE, $CFG;

        // Set defaults
        $tmp = [
            'jsxfromserver' => false,
            'serverversion' => self::$recommended,
            'HTMLentities' => true,
            'convertencoding' => true,
            'globalJS' => '',
            'divid' => 'box',
            'width' => '500',
            'height' => '400'
        ];

        // Read and save settings
        $tmpcfg = get_config('filter_jsxgraph', 'jsxfromserver');
        if (isset($tmpcfg)) {
            $tmp['jsxfromserver'] = $this->convert_bool($tmpcfg);
        }
        $tmpcfg = get_config('filter_jsxgraph', 'serverversion');
        if (isset($tmpcfg)) {
            $tmp['serverversion'] = $tmpcfg;
        }
        $tmpcfg = get_config('filter_jsxgraph', 'HTMLentities');
        if (isset($tmpcfg)) {
            $tmp['HTMLentities'] = $this->convert_bool($tmpcfg);
        }
        $tmpcfg = get_config('filter_jsxgraph', 'convertencoding');
        if (isset($tmpcfg)) {
            $tmp['convertencoding'] = $this->convert_bool($tmpcfg);
        }
        $tmpcfg = get_config('filter_jsxgraph', 'globalJS');
        if (isset($tmpcfg)) {
            $tmp['globalJS'] = trim($tmpcfg);
        }
        $tmpcfg = get_config('filter_jsxgraph', 'divid');
        if (isset($tmpcfg)) {
            $tmp['divid'] = $tmpcfg;
        }
        $tmpcfg = get_config('filter_jsxgraph', 'width');
        if (isset($tmpcfg)) {
            $tmp['width'] = $tmpcfg;
        }
        $tmpcfg = get_config('filter_jsxgraph', 'height');
        if (isset($tmpcfg)) {
            $tmp['height'] = $tmpcfg;
        }

        return $tmp;
    }

    private function get_tagattributes($node) {
        $attributes = [
            'width' => '',
            'height' => '',
            'boardid' => '',
            'box' => '',
            'entities' => '',
            'useGlobalJS' => ''
        ];
        $boolattributes = [
            'entities' => true,
            'useGlobalJS' => true
        ];
        foreach ($attributes as $attr => $value) {
            if (array_key_exists($attr, $boolattributes)) {
                $attributes[$attr] = $this->convert_bool($node->getAttribute($attr), $boolattributes[$attr]);
            } else {
                $attributes[$attr] = $node->getAttribute($attr);
            }
        }

        return $attributes;
    }

    private function convert_bool($string, $default = false) {
        if ($string === false || $string === "false" || $string === 0 || $string === "0") {
            return false;
        } else if ($string === true || $string === "true" || $string === 1 || $string === "1") {
            return true;
        } else {
            return $default;
        }
    }

    private function string_or($choice1, $choice2) {
        if (!empty($choice1)) {
            return $choice1;
        } else {
            return $choice2;
        }
    }
}
