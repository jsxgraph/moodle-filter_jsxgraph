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
 * This is a plugin to enable function plotting and dynamic geometry constructions with JSXGraph within a Moodle platform.
 *
 * JSXGraph is a cross-browser JavaScript library for interactive geometry,
 * function plotting, charting, and data visualization in the web browser.
 * JSXGraph is implemented in pure JavaScript and does not rely on any other
 * library. Special care has been taken to optimize the performance.
 *
 * @package    filter_jsxgraph
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

/**
 * Class filter_jsxgraph
 *
 * @package    filter_jsxgraph
 * @copyright  2020 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_jsxgraph extends moodle_text_filter {
    /**
     * Recommended version
     *
     * @var string
     */
    public static $recommended = '1.1.0';
    /**
     * Path to jsxgraphcore.js
     *
     * @var string
     */
    public static $jsxcore = '/filter/jsxgraph/jsxgraphcore.js';
    /**
     * Path to library folders
     *
     * @var string
     */
    public static $libpath = '/filter/jsxgraph/libs/';

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
     * @param string $tag The tag name
     * @param string $html The HTML string
     *
     * @return string text between tags
     */
    private function get_text_between_tags($tag, $html) {
        global $PAGE;

        $encoding = "UTF-8";
        $setting = $this->get_adminsettings();

        $constantnameboardid = "BOARDID";

        /* 1. STEP ---------------------------
         * Convert HTML string to a dom object
         */

        // Create a new dom object.
        $dom = new domDocument('1.0', $encoding);
        $dom->formatOutput = true;

        // Load the html into the object.
        libxml_use_internal_errors(true);
        if ($setting["convertencoding"]) {
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $encoding));
        } else {
            $dom->loadHTML($html);
        }
        libxml_use_internal_errors(false);

        // Discard white space.
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

            $out = $dom->createElement('div');
            $a = $dom->createAttribute('class');
            $a->value = 'jsxgraph-boards';
            $out->appendChild($a);

            $divids = [];

            for ($b = 0; $b < $tagattribute['numberOfBoards']; $b++) {
                // Create new div element containing JSXGraph.
                $div = $dom->createElement('div');

                $a = $dom->createAttribute('id');
                $divid = $this->string_or($tagattribute['boardid'][$b], $tagattribute['box'][$b]);
                if ($setting['usedivid']) {
                    $divid = $this->string_or($divid, $setting['divid'] . $i);
                } else {
                    $divid = $this->string_or($divid, 'JSXGraph_' . strtoupper(uniqid()));
                }

                $divids[$b] = $divid;

                $a->value = $divid;
                $div->appendChild($a);

                $a = $dom->createAttribute('class');
                $a->value = 'jxgbox';
                $div->appendChild($a);

                $a = $dom->createAttribute('style');
                $w = $this->string_or($tagattribute['width'][$b], $setting['width']);
                $h = $this->string_or($tagattribute['height'][$b], $setting['height']);
                if (is_numeric($w)) {
                    $w .= 'px';
                }
                if (is_numeric($h)) {
                    $h .= 'px';
                }
                $a->value = 'width:' . $w . '; height:' . $h . '; ';
                $div->appendChild($a);

                $out->appendChild($div);
            }
            $b = 0;

            // Replace <jsxgraph>-node.
            $item->parentNode->replaceChild($dom->appendChild($out), $item);

            if ($error !== false) {
                $t = $dom->createElement('p');
                $a = $dom->createAttribute('class');
                $a->value = 'jxg-error';
                $t->appendChild($a);
                $a = $dom->createElement('b', get_string('error', 'filter_jsxgraph'));
                $t->appendChild($a);
                $a = $dom->createElement('span', $error);
                $t->appendChild($a);
                $out->parentNode->replaceChild($dom->appendChild($t), $out);
                continue;
            }

            if ($setting['formulasextension']) {
                $this->load_library('formulas');
            } else {
                if ($tagattribute['ext_formulas'][$b]) {
                    $this->load_library('formulas');
                }
            }

            /* 4. STEP ------
             * Construct code
             */

            $generalcode = '';
            $globalcode = '';

            // Define boardid const.
            $generalcode .= "\n/** Define boardid const */\n";
            for ($id = 0; $id < $tagattribute['numberOfBoards']; $id++) {
                $name = $constantnameboardid . $id;
                $generalcode .= "const $name = '" . $divids[$id] . "';\n";
                $generalcode .= "console.log('$name = `'+$name+'` has been prepared');\n";
            }
            $generalcode .= "const $constantnameboardid = $constantnameboardid" . "0" . ";\n";

            $generalcode .= "\n/** Accessibility */\n";
            $generalcode .= "JXG.Options.board.title = '" . $tagattribute['title'][$b] . "';\n";
            $generalcode .= "JXG.Options.board.description = '" . $tagattribute['description'][$b] . "';\n";
            $generalcode .= "\n";

            // Load global JavaScript code from administrator settings.
            if ($setting['globalJS'] !== '' && $tagattribute['useGlobalJS'][$b]) {
                $globalcode .= "\n// Global JavaScript code of the administrator\n";
                $globalcode .= $setting['globalJS'];
                if (substr_compare($setting['globalJS'], ';', strlen($setting['globalJS']) - 1) < 0) {
                    $globalcode .= ';';
                }
            }
            $globalcode .= "\n\n";

            // Load code from <jsxgraph>-node.
            $jscode = "\n// Specific JavaScript code\n";
            // Integrate specific JavaScript.
            $jscode .= $dom->saveHTML($item);
            // Remove <jsxgraph> tags.
            $jscode = preg_replace("(</?" . $tag . "[^>]*\>)i", "", $jscode);
            // In order not to terminate the JavaScript part prematurely, the backslash has to be escaped.
            $jscode = str_replace("</script>", "<\/script>", $jscode);

            // Convert HTML-Entities in code.
            if ($setting['HTMLentities'] && $tagattribute['entities']) {
                $globalcode = html_entity_decode($globalcode);
                $jscode = html_entity_decode($jscode);
            }

            $jscode = $generalcode . $jscode;

            // Complete the code.
            $code = '';
            $cond = '';
            for ($id = 0; $id < $tagattribute['numberOfBoards']; $id++) {
                $cond .= "document.getElementById('" . $divids[$id] . "') != null &&";
            }
            $cond = substr($cond, 0, -3);
            if ($require) {
                $codeprefix = "require(['jsxgraphcore'], function (JXG) { if ($cond) { \n";
                $codepostfix = "}\n });\n";
            } else {
                $codeprefix = "\nif ($cond) {";
                $codepostfix = "};";
            }
            $code = $codeprefix . $globalcode . $jscode . $codepostfix;

            $code = "\n//< ![CDATA[\n" . $code . "\n//]]>\n";
            $code =
                "\n\n// ###################################################" .
                "\n// JavaScript code for JSXGraph board '" . $divids[0] . "' and other\n" .
                $code .
                "\n// End Code for JSXGraph board '" . $divids[0] . "' and other " .
                "\n// ###################################################\n\n";

            // Place JavaScript code at the end of the page.
            $PAGE->requires->js_init_call($code);
        }

        /* 5. STEP ----------------------
         * Paste new div node in web page
         */

        // Remove DOCTYPE.
        $dom->removeChild($dom->firstChild);
        // Remove <html><body></body></html>.
        $str = $dom->saveHTML();
        $str = str_replace("<body>", "", $str);
        $str = str_replace("</body>", "", $str);
        $str = str_replace("<html>", "", $str);
        $str = str_replace("</html>", "", $str);

        return $str;
    }

    /**
     * Load JSXGraph code from local or from server
     *
     * @param bool   $fromserver
     * @param string $serverversion
     *
     * @return string[]
     */
    private function load_jsxgraph($fromserver, $serverversion = "") {
        global $PAGE, $CFG;

        $result = ['success', 'withREQUIRE'];

        $url = self::$jsxcore;

        if ($this->convert_bool($fromserver)) {
            // Handle several special cases.
            switch ($serverversion) {
                case '':
                    break;
                case '0.99.6': // Error with requirejs in version 0.99.6!
                    $result[0] = 'error';
                    $result[1] = get_string('error0.99.6', 'filter_jsxgraph');

                    return $result;
                case '0.99.5': // Cloudfare-error with version 0.99.5!
                    $result[0] = 'error';
                    $result[1] = get_string('error0.99.5', 'filter_jsxgraph');

                    return $result;
                default:
                    $url = 'https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/' . $serverversion . '/jsxgraphcore.js';
            }

            // Check if the entered version exists on the server.
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
     * Load additional library
     *
     * @param string $libname
     *
     */
    private function load_library($libname) {
        global $PAGE, $CFG;

        $libs = [
            'formulas' => 'formulas_extension/JSXQuestion.js'
        ];

        if (!array_key_exists($libname, $libs)) {
            return;
        }
        $url = self::$libpath . $libs[$libname];
        $PAGE->requires->js(new moodle_url($url));
    }

    /**
     * Get settings made by administrator
     *
     * @return array settings from administration
     */
    private function get_adminsettings() {
        global $PAGE, $CFG;

        // Set defaults.
        $tmp = [
            'jsxfromserver' => false,
            'serverversion' => self::$recommended,
            'formulasextension' => true,
            'HTMLentities' => true,
            'convertencoding' => true,
            'globalJS' => '',
            'usedivid' => false,
            'divid' => 'box',
            'width' => '500',
            'height' => '400'
        ];

        // Read and save settings.
        $tmpcfg = get_config('filter_jsxgraph', 'jsxfromserver');
        if (isset($tmpcfg)) {
            $tmp['jsxfromserver'] = $this->convert_bool($tmpcfg);
        }
        $tmpcfg = get_config('filter_jsxgraph', 'serverversion');
        if (isset($tmpcfg)) {
            $tmp['serverversion'] = $tmpcfg;
        }
        $tmpcfg = get_config('filter_jsxgraph', 'formulasextension');
        if (isset($tmpcfg)) {
            $tmp['formulasextension'] = $this->convert_bool($tmpcfg);
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
        $tmpcfg = get_config('filter_jsxgraph', 'usedivid');
        if (isset($tmpcfg)) {
            $tmp['usedivid'] = $this->convert_bool($tmpcfg);
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

    /**
     * Determine the attributes
     *
     * @param HTMLnode $node
     *
     * @return string[]
     */
    private function get_tagattributes($node) {
        $numberofboardsattr = 'numberOfBoards';
        $numberofboardsval = 1;
        $attributes = [
            'title' => '',
            'description' => '',
            'width' => '',
            'height' => '',
            'entities' => '',
            'useGlobalJS' => '',
            'ext_formulas' => '',
            'box' => '',
            'boardid' => '',
        ];
        $boolattributes = [
            'entities' => true,
            'useGlobalJS' => true,
            'ext_formulas' => null,
        ];
        $possiblearrayattributes = [
            'title',
            'description',
            'width',
            'height',
            'box',
            'boardid',
        ];
        if (!function_exists('get_bool_value')) {
            /**
             * @param $attr
             * @param $tagval
             * @param $node
             * @param $boolattributes
             *
             * @return bool|mixed
             */
            function get_bool_value($attr, $tagval, $node, $boolattributes) {
                if ($node->hasAttribute($attr)) {
                    if ($tagval == '') {
                        return true;
                    } else {
                        return $this->convert_bool($tagval, $boolattributes[$attr]);
                    }
                } else {
                    return $boolattributes[$attr];
                }
            }
        }

        $numberofboardsval =
            $node->getAttribute($numberofboardsattr) ?: $node->getAttribute(strtolower($numberofboardsattr)) ?: $numberofboardsval;

        foreach ($attributes as $attr => $value) {
            $a = $node->getAttribute($attr) ?: $node->getAttribute(strtolower($attr));
            if (isset($a) && !empty($a)) {
                $a = explode(',', $a);
            } else {
                $a = [''];
            }
            $attributes[$attr] = [];
            $arrattr = in_array($attr, $possiblearrayattributes);

            for ($i = 0; $i < $numberofboardsval; $i++) {
                if (!isset($a[$i]) || empty($a[$i]) || !$arrattr) {
                    $attributes[$attr][$i] = $a[0];
                } else {
                    $attributes[$attr][$i] = $a[$i];
                }
                if (array_key_exists($attr, $boolattributes)) {
                    $attributes[$attr][$i] = get_bool_value($attr, $attributes[$attr][$i], $node, $boolattributes);
                }
            }
        }

        $attributes[$numberofboardsattr] = $numberofboardsval;

        return $attributes;
    }

    /**
     * Convert string to bool
     *
     * @param string $string
     * @param bool   $default
     *
     * @return bool
     */
    private function convert_bool($string, $default = false) {
        if ($string === false || $string === "false" || $string === 0 || $string === "0") {
            return false;
        } else if ($string === true || $string === "true" || $string === 1 || $string === "1") {
            return true;
        } else {
            return $default;
        }
    }

    /**
     * Decide between two strings
     *
     * @param string $choice1
     * @param string $choice2
     *
     * @return string
     */
    private function string_or($choice1, $choice2) {
        if (!empty($choice1)) {
            return $choice1;
        } else {
            return $choice2;
        }
    }
}
