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
 * @copyright  2018 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 *             Matthias Ehmann, Michael Gerhaeuser, Carsten Miller, Andreas Walter, Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $PAGE, $CFG;

require_once($CFG->libdir . '/pagelib.php');

class filter_jsxgraph extends moodle_text_filter {
    
    public static $recommended_version = '0.99.7';
    public static $jsxcore             = '/filter/jsxgraph/jsxgraphcore.js';
    
    /**
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter($text, array $options = array()) {
        // to optimize speed, search for a <jsxgraph>-tag (avoiding to parse everything on every text)
        if (!is_int(strpos($text, '<jsxgraph'))) {
            return $text;
        }
        
        return $this->getTextBetweenTags("jsxgraph", $text, "UTF-8");
    }
    
    /**
     * @get text between tags
     * @param string $tag The tag name
     * @param string $html The HTML string
     * @param string $encoding
     * @return string
     */
    private function getTextBetweenTags($tag, $html, $encoding = "UTF-8") {
        global $PAGE;
        
        $setting = $this->getAdminSettings();
        
        
        /////////////////////////////////////////
        // convert HTML-String to a dom object //
        /////////////////////////////////////////
        
        // $dom = new domDocument;
        $dom = new domDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        // load the html into the object
        libxml_use_internal_errors(true);
        // $htmlutf8 = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
        // $htmlutf8 = mb_convert_encoding($html, $encoding);
        // $dom->loadHTML($htmlutf8);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        
        // discard white space
        $dom->preserveWhiteSpace = false;
        $dom->strictErrorChecking = false;
        $dom->recover = true;
        
        
        //////////////////////
        // get tag elements //
        //////////////////////
        
        $taglist = $dom->getElementsByTagname($tag);
        $withREQUIRE = false;
        $error = false;
        
        if (!empty($taglist)) {
            $tmp = $this->loadJSXGraph(
                $setting['JSXfromServer'],
                $setting['serverversion']
            );
            if ($tmp[0] === 'error') {
                $error = $tmp[1];
            } else {
                $withREQUIRE = $tmp[1] === 'withREQUIRE';
            }
        }
        
        
        /////////////////////////////////////////////////
        // Iterate backwards through the jsxgraph tags //
        /////////////////////////////////////////////////
        
        for ($i = $taglist->length - 1; $i > -1; $i--) {
            
            $item = $taglist->item($i);
            $tagattribute = $this->getTagAttributes($item);
            
            // Create new div element containing JSXGraph
            $out = $dom->createElement('div');
            
            $a = $dom->createAttribute('id');
            $divID = $this->stringOR($tagattribute['box'], $setting['divID'] . $i);
            $a->value = $divID;
            $out->appendChild($a);
            
            $a = $dom->createAttribute('class');
            $a->value = 'jxgbox';
            $out->appendChild($a);
            
            $a = $dom->createAttribute('style');
            $w = $this->stringOR($tagattribute['width'], $setting['width']);
            $h = $this->stringOR($tagattribute['height'], $setting['height']);
            if (is_numeric($w)) {
                $w .= 'px';
            }
            if (is_numeric($h)) {
                $h .= 'px';
            }
            $a->value = 'width:' . $w . '; height:' . $h . '; ';
            $out->appendChild($a);
            
            // Replace <jsxgraph>-node by <div>-node
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
            
            
            ////////////////////
            // Construct code //
            ////////////////////
            
            $globalCode = '';
            
            // Load global JavaScript code from administrator settings
            if ($setting['globalJS'] !== '' && $tagattribute['useGlobalJS']) {
                $globalCode .= "\n// Global JavaScript code of the administrator\n";
                $globalCode .= $setting['globalJS'];
                if (substr_compare($setting['globalJS'], ';', $setting['globalJS'] . length - 1) < 0) {
                    $globalCode .= ';';
                }
            }
            $globalCode .= "\n\n";
            
            // Load code from <jsxgraph>-node
            $plainJSCode = "\n// Specific JavaScript code\n";
            $plainJSCode .= $dom->saveHTML($item);
            // Remove <jsxgraph>-tags
            $plainJSCode = preg_replace("(</?" . $tag . "[^>]*\>)i", "", $plainJSCode);
            // In order not to terminate the JavaScript part prematurely, the backslash has to be escaped
            $plainJSCode = str_replace("</script>", "<\/script>", $plainJSCode);
            
            // Convert HTML-Entities in Code
            if ($setting['convertEntities'] && $tagattribute['entities']) {
                $globalCode = html_entity_decode($globalCode);
                $plainJSCode = html_entity_decode($plainJSCode);
            }
            
            // Complete the code
            $code = '';
            if ($withREQUIRE) {
                $code_pre = "require(['jsxgraphcore'], function (JXG) { if (document.getElementById('" . $divID . "') != null) { \n";
                $code_post = "}\n });\n";
                $code = $globalCode . $code_pre . $plainJSCode . $code_post;
            } else {
                $code_pre = "\nif (document.getElementById('" . $divID . "') != null) {";
                $code_post = "};";
                $code = $code_pre . $globalCode . $plainJSCode . $code_post;
            }
            
            // Place JavaScript code at the end of the page.
            $PAGE->requires->js_init_call($code);
        }
        
        
        ////////////////////////////////////
        // Paste new div node in web page //
        ////////////////////////////////////
        
        // remove DOCTYPE
        $dom->removeChild($dom->firstChild);
        // remove <html><body></body></html>
        $str = $dom->saveHTML();
        $str = str_replace("<body>", "", $str);
        $str = str_replace("</body>", "", $str);
        $str = str_replace("<html>", "", $str);
        $str = str_replace("</html>", "", $str);
        
        return $str;
    }
    
    private function loadJSXGraph($fromServer, $serverVersion = "") {
        global $PAGE, $CFG;
        
        $result = ['success', 'withREQUIRE'];
        
        $url = $CFG->wwwroot . filter_jsxgraph::$jsxcore;
        
        if ($this->convertBool($fromServer)) {
            // Handle several special cases
            switch ($serverVersion) {
                case '':
                    break;
                case '0.99.6': // Error with requirejs in version 0.99.6
                    $result[0] = 'error';
                    $result[1] = get_string('error0.99.6', 'filter_jsxgraph');
                    
                    return $result;
                case '0.99.5': // Cloudfare-error with version 0.99.5
                    $url = 'https://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore-0.99.5.js';
                    break;
                default:
                    $url = 'https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/' . $serverVersion . '/jsxgraphcore.js';
            }
            
            // Check if the entered version exists on the server
            if ($tmp = fopen($url, 'r') === false) {
                $result[0] = 'error';
                $result[1] = get_string('errorNotFound_pre', 'filter_jsxgraph') . $serverVersion . get_string('errorNotFound_post', 'filter_jsxgraph');
                
                return $result;
            } else {
                if (isset($tmp)) {
                    fclose($tmp);
                }
            }
            
            // Decide how the code should be included.
            // For versions after 0.99.6, it must be included with "require"
            $tmp = $serverVersion;
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
     * @get settings from administration
     * @return array
     */
    private function getAdminSettings() {
        global $PAGE, $CFG;
        
        // set defaults
        $tmp = [
            'JSXfromServer' => false,
            'serverversion' => filter_jsxgraph::$recommended_version,
            'convertEntities' => true,
            'globalJS' => '',
            'divID' => 'box',
            'width' => '500',
            'height' => '400'
        ];
        
        // read and save settings
        $tmp_cfg = get_config('filter_jsxgraph', 'jsxfromserver');
        if (isset($tmp_cfg)) {
            $tmp['JSXfromServer'] = $this->convertBool($tmp_cfg);
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'serverversion');
        if (isset($tmp_cfg)) {
            $tmp['serverversion'] = $tmp_cfg;
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'HTMLentities');
        if (isset($tmp_cfg)) {
            $tmp['convertEntities'] = $this->convertBool($tmp_cfg);
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'globalJS');
        if (isset($tmp_cfg)) {
            $tmp['globalJS'] = trim($tmp_cfg);
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'divid');
        if (isset($tmp_cfg)) {
            $tmp['divID'] = $tmp_cfg;
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'width');
        if (isset($tmp_cfg)) {
            $tmp['width'] = $tmp_cfg;
        }
        $tmp_cfg = get_config('filter_jsxgraph', 'height');
        if (isset($tmp_cfg)) {
            $tmp['height'] = $tmp_cfg;
        }
        
        return $tmp;
    }
    
    private function getTagAttributes($node) {
        $attributes = [
            'width' => '',
            'height' => '',
            'box' => '',
            'entities' => '',
            'useGlobalJS' => ''
        ];
        $boolAttributes = [
            'entities' => true,
            'useGlobalJS' => true
        ];
        foreach ($attributes as $attr => $value) {
            if (array_key_exists($attr, $boolAttributes)) {
                $attributes[$attr] = $this->convertBool($node->getAttribute($attr), $boolAttributes[$attr]);
            } else {
                $attributes[$attr] = $node->getAttribute($attr);
            }
        }
        
        return $attributes;
    }
    
    private function convertBool($string, $default = false) {
        if ($string === false || $string === "false" || $string === 0 || $string === "0") {
            return false;
        } else if ($string === true || $string === "true" || $string === 1 || $string === "1") {
            return true;
        } else {
            return $default;
        }
    }
    
    private function stringOR($firstChoice, $secondChoice) {
        if (!empty($firstChoice))
            return $firstChoice;
        else
            return $secondChoice;
    }
}
