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
 * @copyright  2022 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
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
 * @copyright  2022 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_jsxgraph extends moodle_text_filter {
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

    private const REQUIRE_WITHOUT   = 0;
    private const REQUIRE_WITH_KEY  = 1;
    private const REQUIRE_WITH_PATH = 2;

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
        $constantnameboardids = "BOARDIDS";

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
        $require = self::REQUIRE_WITHOUT;
        $error = false;

        if (!empty($taglist)) {
            $tmp = $this->load_jsxgraph(
                $setting['jsxfromserver'],
                $setting['serverversion']
            );
            if ($tmp[0] === 'error') {
                $error = $tmp[1];
            } else {
                $require = $tmp[1];
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

                // Create div id.
                $divid = $this->string_or($tagattribute['boardid'][$b], $tagattribute['box'][$b]);
                if ($setting['usedivid']) {
                    $divid = $this->string_or($divid, $setting['divid'] . $i);
                } else {
                    $divid = $this->string_or($divid, 'JSXGraph_' . strtoupper(uniqid()));
                }
                $divids[$b] = $divid;

                // Create new div element containing JSXGraph.
                $dims = [
                    "width" => $this->string_or($tagattribute['width'][$b], $setting['fixwidth']),
                    "height" => $this->string_or($tagattribute['height'][$b], $setting['fixheight']),
                    "aspect-ratio" => $this->string_or($tagattribute['aspect-ratio'][$b], $setting['aspectratio']),
                    "max-width" => $this->string_or($tagattribute['max-width'][$b], $setting['maxwidth']),
                    "max-height" => $this->string_or($tagattribute['max-height'][$b], $setting['maxheight']),
                ];
                $div = $this->get_board_html(
                    $divid,
                    $dims,
                    $tagattribute['class'][$b],
                    $tagattribute['wrapper-class'][$b],
                    $tagattribute['force-wrapper'][$b],
                    $setting['fallbackaspectratio'],
                    $setting['fallbackwidth']
                );

                $divdom = new DOMDocument;
                libxml_use_internal_errors(true);
                $divdom->loadHTML($div);
                libxml_use_internal_errors(false);

                $out->appendChild($dom->importNode($divdom->documentElement, true));
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
            for ($j = 0; $j < $tagattribute['numberOfBoards']; $j++) {
                $name = $constantnameboardid . $j;
                $generalcode .= "const $name = '" . $divids[$j] . "';\n";
                $generalcode .= "console.log('$name = `'+$name+'` has been prepared');\n";
            }
            $generalcode .= "const $constantnameboardid = $constantnameboardid" . "0" . ";\n";
            $generalcode .= "const $constantnameboardids = ['" . implode("', '", $divids) . "'];\n";

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

            switch ($require) {
                case self::REQUIRE_WITH_KEY:
                    $codeprefix = "require(['jsxgraphcore'], function (JXG) { \n if ($cond) { \n";
                    $codepostfix = "}\n });\n";
                    break;
                case self::REQUIRE_WITH_PATH:
                    $jsx_url = new moodle_url('/filter/jsxgraph/core/jsxgraphcore-1.5.0.js');
                    $codeprefix = "require(['" . $jsx_url . "'], function (JXG) { \nif ($cond) {";
                    $codepostfix = "}\n });\n";
                    /*
                    $jsx_url = new moodle_url('/filter/jsxgraph/core/jsxgraphcore-1.5.0.mjs');
                    $codeprefix = "import JXG from '$jsx_url';  \nconsole.log('Hi', JXG); \nif ($cond) { ";
                    $codepostfix = "\n }\n";
                     */
                    break;
                case self::REQUIRE_WITHOUT:
                default:
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
            $t = $dom->createElement('script', $code);
            $a = $dom->createAttribute('type');
            $a->value = 'module';
            $t->appendChild($a);
            $dom->appendChild($t);
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
     * Build a <div> for board.
     *
     * This function creates the HTML for a board according to the given dimensions. It is possible, to define an aspect-ratio.
     * If there are given width and height, aspect-ratio is ignored.
     *
     * There are the following use-cases:
     *  ===========================================================================================================================
     *  |  nr  |              given              |                                    behavior                                    |
     *  ===========================================================================================================================
     *  |   1  |  width and height in any com-   |  The dimensions are applied to the boards <div>. Layout is like in the css     |
     *  |      |  bination (min-/max-/...)       |  specification defined. See notes (a) and (b). Aspect-ratio is ignored in      |
     *  |      |                                 |  this case. Please note also (c).                                              |
     *  ---------------------------------------------------------------------------------------------------------------------------
     *  |   2  |  aspect-ratio and               |  The boards width ist fix according its value. The height is automatically     |
     *  |      |  (min-/max-)width               |  regulated following the given aspect-ratio.                                   |
     *  ---------------------------------------------------------------------------------------------------------------------------
     *  |   3  |  aspect-ratio and               |  The boards height ist fix according its value. The width is automatically     |
     *  |      |  (min-/max-)height              |  regulated following the given aspect-ratio. This case doesn't work on         |
     *  |      |                                 |  browsers which doesn't support aspect-ratio. The css trick (see (a)) can      |
     *  |      |                                 |  not help here.                                                                |
     *  ---------------------------------------------------------------------------------------------------------------------------
     *  |   4  |  only aspect-ratio              |  The $defaultwidth is used. Apart from that see case 2.                       |
     *  ---------------------------------------------------------------------------------------------------------------------------
     *  |   5  |  nothing                        |  Aspect-ratio is set to $defaultaspectratio and then see case 4.             |
     *  ===========================================================================================================================
     *
     * Notes:
     *  (a) Pay attention: the <div> uses the css attribute "aspect-ratio" which is not supported by every browser. If the browser
     *      does not support this, a trick with a wrapping <div> and padding-bottom is applied. This trick only works, if
     *      aspect-ratio and (min-/max-)width are given, not in combination with (min-/max-)height! For an overview of browsers
     *      which support aspect-ratio see {@link https://caniuse.com/mdn-css_properties_aspect-ratio.}
     *  (b) If the css trick is not needed, the result is only the <div> with id $id for the board. The value of $wrapperclasses
     *      is ignored.
     *      In the trick the div is wrapped by a <div> with id $id + '-wrapper'. This wrapper contains the main dimensions and the
     *      board-<div> gets only relative dimensions according to the case, e.g. width: 100%.
     *      You can force adding an wrapper by setting $forcewrapper to true.
     *  (c) If only width is given, the height will be 0 like in css. You have to define an aspect-ratio or height to display the
     *      board!
     *
     * @param string  $id
     * @param object  $dimensions with possible attributes
     *                                      aspect-ratio  (the ratio of width / height)
     *                                      width         (px, rem, vw, ...; if only a number is given, its interpreted as px)
     *                                      height        (px, rem, vh, ...; if only a number is given, its interpreted as px)
     *                                      max-width     (px, rem, vw, ...; if only a number is given, its interpreted as px)
     *                                      min-width     (px, rem, vw, ...; if only a number is given, its interpreted as px)
     *                                      max-height    (px, rem, vh, ...; if only a number is given, its interpreted as px)
     *                                      min-height    (px, rem, vh, ...; if only a number is given, its interpreted as px)
     * @param string  $classes Additional css classes for the board.
     * @param string  $wrapperclasses Additional css classes for the boards container.
     *                                      (If it is needed. In the other case this is merged with $classes.)
     * @param boolean $forcewrapper Default: false.
     * @param string  $defaultaspectratio Default: "1 / 1".
     * @param string  $defaultwidth Default: "100%".
     * @param boolean $perventjsdimreg Default: false.
     *
     * @return string                       The <div> for the board.
     */
    private function get_board_html(
        $id, $dimensions = [], $classes = "", $wrapperclasses = "", $forcewrapper = false,
        $defaultaspectratio = "1 / 1", $defaultwidth = "100%",
        $perventjsdimreg = false
    ) {

        if (!function_exists("empty_or_0_or_default")) {
            /**
             * Returns true if variable is empty, 0 or equal to $default.
             *
             * @param mixed $var Some variable
             * @param null  $default Default value
             *
             * @return bool
             */
            function empty_or_0_or_default($var, $default = null) {
                return empty($var) || $var === 0 || $var === '0' || $var === '0px' || $var === $default;
            }
        }

        if (!function_exists("css_norm")) {
            /**
             * Returns a css value or $default,
             *
             * @param mixed  $var Some variable
             * @param string $default Default value
             *
             * @return string
             */
            function css_norm($var, $default = '') {
                if (substr('' . $var, 0, 1) === '0') {
                    $var = 0;
                } else if (empty($var)) {
                    $var = $default;
                } else if (is_numeric($var)) {
                    $var .= 'px';
                }

                return "" . $var;
            }
        }

        // Constants.
        define('ALLOWED_DIMS', ["aspect-ratio", "width", "height", "max-width", "max-height"]);
        define('AR', "aspect-ratio");
        define('ALLOWED_DIMS_EXCEPT_AR', ["width", "height", "max-width", "max-height"]);
        define('WIDTHS', ["width", "max-width"]);

        // Tmp vars.
        $styles = "";
        $wrapperstyles = "";

        $tmp = true;
        foreach (ALLOWED_DIMS_EXCEPT_AR as $attr) {
            $tmp = $tmp && empty_or_0_or_default($dimensions[$attr]);
        }
        if ($tmp && empty_or_0_or_default($dimensions[AR])) {
            $dimensions[AR] = $defaultaspectratio;
            $dimensions["width"] = $defaultwidth;
        }

        // At this point there is at least an aspect-ratio.

        foreach (ALLOWED_DIMS as $attr) {
            if (!empty_or_0_or_default($dimensions[$attr])) {
                $styles .= "$attr: " . css_norm($dimensions[$attr]) . "; ";
            }
        }

        $styles = substr($styles, 0, -1);
        $classes = !empty($classes) ? ' ' . $classes : '';
        $board = '<div id="' . $id . '" class="jxgbox' . $classes . '" style="' . $styles . '"></div>';

        if (!$perventjsdimreg) {

            foreach (WIDTHS as $attr) {
                if (!empty_or_0_or_default($dimensions[$attr])) {
                    $wrapperstyles .= "$attr: " . css_norm($dimensions[$attr]) . "; ";
                }
            }

            $js = "\n" .
                '<script type="module">
    (function() {
        let addWrapper = function (boardid, classes = [], styles = "") {
            let board = document.getElementById(boardid),
                wrapper, wrapperid = boardid + "-wrapper";

            wrapper = document.createElement("div");
            wrapper.id = wrapperid;
            wrapper.classList.add("jxgbox-wrapper");

            for (let c of classes)
                wrapper.classList.add(c);

            wrapper.style = styles;

            board.parentNode.insertBefore(wrapper, board.nextSibling);
            wrapper.appendChild(board);
        }

        const FORCE_WRAPPER = false || ' . ($forcewrapper ? 'true' : 'false') . ';

        let boardid = "' . $id . '",
            wrapper_classes = "' . $wrapperclasses . '".split(" "),
            wrapper_styles = "' . $wrapperstyles . '",
            board = document.getElementById(boardid),
            ar, ar_h, ar_w, padding_bottom;

        if (!CSS.supports("aspect-ratio", "1 / 1") && board.style["aspect-ratio"] !== "") {

            ar = board.style["aspect-ratio"].split("/", 3);
            ar_w = ar[0].trim();
            ar_h = ar[1].trim();
            padding_bottom = ar_h / ar_w * 100;

            if (wrapper_styles !== "")
                addWrapper(boardid, wrapper_classes, wrapper_styles);

            board.style = "height: 0; padding-bottom: " + padding_bottom + "%; /*" + board.style + "*/";

        } else if (FORCE_WRAPPER) {

            wrapper_styles = "";
            if (board.style.width.indexOf("%") > -1) {
                wrapper_styles += "width: " + board.style.width + "; "
                board.style.width = "100%";
            }
            if (board.style.height.indexOf("%") > -1) {
                wrapper_styles += "height: " + board.style.height + "; "
                board.style.height = "100%";
            }
            addWrapper(boardid, wrapper_classes, wrapper_styles);
        }
    })();
        </script>';

        } else {
            $js = "";
        }

        return $board . $js;
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

        // defaults:
        $result = ['success', self::REQUIRE_WITH_PATH];

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
                $result[1] = self::REQUIRE_WITHOUT;
            } else if ($version[0] >= 1 && $version[1] >= 5 && $version[2] >= 0) {
                $result[1] = self::REQUIRE_WITH_PATH;
            } else {
                $result[1] = self::REQUIRE_WITH_KEY;
            }
        }

        // $PAGE->requires->js(new moodle_url($url));

        $url = '/filter/jsxgraph/jsxgraphcore.mjs';

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

        $recommended = get_config('filter_jsxgraph', 'recommendedJSX');
        if (!$recommended) {
            $recommended = '1.1.0';
        }

        // Set defaults.
        $defaults = [
            'jsxfromserver' => false,
            'serverversion' => $recommended,
            'formulasextension' => true,
            'HTMLentities' => true,
            'convertencoding' => true,
            'globalJS' => '',
            'usedivid' => false,
            'divid' => 'box',
            'fixwidth' => '',
            'fixheight' => '',
            'aspectratio' => '',
            'maxwidth' => '',
            'maxheight' => '',
            'fallbackaspectratio' => '1 / 1',
            'fallbackwidth' => '100%',
        ];

        $bools = [
            'jsxfromserver',
            'formulasextension',
            'HTMLentities',
            'convertencoding',
            'usedivid',
        ];

        $trims = [
            'globalJS'
        ];

        // Read and save settings.
        foreach ($defaults as $a => &$default) {
            $tmp = get_config('filter_jsxgraph', $a);

            if (in_array($a, $bools)) {
                $tmp = $this->convert_bool($tmp);
            }
            if (in_array($a, $trims)) {
                $tmp = trim($tmp);
            }
            $default = $tmp;
        }

        return $defaults;
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
            'aspect-ratio' => '',
            'max-width' => '',
            'max-height' => '',
            'class' => '',
            'wrapper-class' => '',
            'force-wrapper' => '',
            'entities' => '',
            'useGlobalJS' => '',
            'ext_formulas' => '',
            'box' => '',
            'boardid' => '',
        ];
        $boolattributes = [
            'force-wrapper' => false,
            'entities' => true,
            'useGlobalJS' => true,
            'ext_formulas' => null,
        ];
        $possiblearrayattributes = [
            'title',
            'description',
            'width',
            'height',
            'aspect-ratio',
            'max-width',
            'max-height',
            'box',
            'boardid',
        ];

        $numberofboardsval =
            $node->getAttribute($numberofboardsattr) ? :
                $node->getAttribute(strtolower($numberofboardsattr)) ? : $numberofboardsval;

        foreach ($attributes as $attr => $value) {
            $a = $node->getAttribute($attr) ? : $node->getAttribute(strtolower($attr));
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
                    $attributes[$attr][$i] = $this->get_bool_value($node, $attr, $attributes[$attr][$i], $boolattributes[$attr]);
                }
            }
        }

        $attributes[$numberofboardsattr] = $numberofboardsval;

        return $attributes;
    }

    /**
     * Gives the value of $attribute in $node as bool. If the attribute does not exist, $stdval is returned.
     *
     * @param HTMLNode    $node
     * @param string      $attribute
     * @param string      $givenval
     * @param bool|string $stdval
     *
     * @return bool
     */
    private function get_bool_value($node, $attribute, $givenval, $stdval) {
        if ($node->hasAttribute($attribute)) {
            if ($givenval == '') {
                return true;
            } else {
                return $this->convert_bool($givenval, $stdval);
            }
        } else {
            return $stdval;
        }
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
