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
     * Path to jsxgraphcores
     *
     * @var string
     */
    public const PATH_FOR_CORES = '/filter/jsxgraph/amd/build/';
    /**
     * Path to library folders
     *
     * @var string
     */
    public const PATH_FOR_LIBS = '/filter/jsxgraph/libs/';

    private const REQUIRE_WITHOUT   = 0;
    private const REQUIRE_WITH_KEY  = 1;
    private const REQUIRE_WITH_PATH = 2;

    private const TAG = "jsxgraph";

    private const BOARDID_CONST  = "BOARDID";
    private const BOARDIDS_CONST = "BOARDIDS";

    private const ENCODING = "UTF-8";

    private $DOM            = null;
    private $TAGLIST        = null;
    private $SETTINGS       = null;
    private $IDS            = [];
    private $VERSION_JSX    = null;
    private $VERSION_MOODLE = null;

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
        if (!is_int(strpos($text, '<' . self::TAG))) {
            return $text;
        }

        // 0. STEP: Do some initial stuff.
        //////////////////////////////////

        $this->SETTINGS = $this->get_adminsettings();
        $this->set_versions($this->SETTINGS['versionJSXGraph']);
        if (!isset($this->VERSION_JSX) || !isset($this->VERSION_MOODLE)) {
            return $text;
        }

        // 1. STEP: Convert HTML string to a dom object.
        ////////////////////////////////////////////////

        // Create a new dom object.
        $this->DOM = new domDocument('1.0', self::ENCODING);
        $this->DOM->formatOutput = true;

        // Load the html into the object.
        libxml_use_internal_errors(true);
        if ($this->SETTINGS["convertencoding"]) {
            $this->DOM->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', self::ENCODING));
        } else {
            $this->DOM->loadHTML($text);
        }
        libxml_use_internal_errors(false);

        // Discard white space.
        $this->DOM->preserveWhiteSpace = false;
        $this->DOM->strictErrorChecking = false;
        $this->DOM->recover = true;

        // 2. STEP: Get tag elements.
        /////////////////////////////

        $this->TAGLIST = $this->DOM->getElementsByTagname(self::TAG);

        // 3.+4. STEP: Load library (if needed) and iterate backwards through the jsxgraph tags.
        ////////////////////////////////////////////////////////////////////////////////////////

        if (!empty($this->TAGLIST)) {
            $this->load_jsxgraph();

            for ($i = $this->TAGLIST->length - 1; $i > -1; $i--) {
                $node = $this->TAGLIST->item($i);
                $this->IDS = [];
                $new = $this->get_replaced_node($node, $i);

                // Replace <jsxgraph>-node.
                $node->parentNode->replaceChild($this->DOM->appendChild($new), $node);

                $this->apply_js($node);
            }
        }

        // 5. STEP: Paste new div node in web page.
        ///////////////////////////////////////////

        // Remove DOCTYPE.
        $this->DOM->removeChild($this->DOM->firstChild);
        // Remove <html><body></body></html>.
        $str = $this->DOM->saveHTML();
        $str = str_replace("<body>", "", $str);
        $str = str_replace("</body>", "", $str);
        $str = str_replace("<html>", "", $str);
        $str = str_replace("</html>", "", $str);

        // Cleanup.
        $this->TAGLIST = null;
        $this->DOM = null;
        $this->SETTINGS = null;

        return $str;
    }

    private function get_replaced_node($node, $index) {
        $ATTRIBS = $this->get_tagattributes($node);

        // Create div node.
        $new = $this->DOM->createElement('div');
        $a = $this->DOM->createAttribute('class');
        $a->value = 'jsxgraph-boards';
        $new->appendChild($a);

        for ($i = 0; $i < $ATTRIBS['numberOfBoards']; $i++) {

            // Create div id.
            $divid = $this->string_or($ATTRIBS['boardid'][$i], $ATTRIBS['box'][$i]);
            if ($this->SETTINGS['usedivid']) {
                $divid = $this->string_or($divid, $this->SETTINGS['divid'] . $index);
            } else {
                $divid = $this->string_or($divid, 'JSXGraph_' . strtoupper(uniqid()));
            }
            $this->IDS[$i] = $divid;

            // Create new div element containing JSXGraph.
            $dimensions = [
                "width" => $this->string_or($ATTRIBS['width'][$i], $this->SETTINGS['fixwidth']),
                "height" => $this->string_or($ATTRIBS['height'][$i], $this->SETTINGS['fixheight']),
                "aspect-ratio" => $this->string_or($ATTRIBS['aspect-ratio'][$i], $this->SETTINGS['aspectratio']),
                "max-width" => $this->string_or($ATTRIBS['max-width'][$i], $this->SETTINGS['maxwidth']),
                "max-height" => $this->string_or($ATTRIBS['max-height'][$i], $this->SETTINGS['maxheight']),
            ];
            $div = $this->get_board_html(
                $divid,
                $dimensions,
                $ATTRIBS['class'][$i],
                $ATTRIBS['wrapper-class'][$i],
                $ATTRIBS['force-wrapper'][$i],
                $this->SETTINGS['fallbackaspectratio'],
                $this->SETTINGS['fallbackwidth']
            );

            $divdom = new DOMDocument;
            libxml_use_internal_errors(true);
            $divdom->loadHTML($div);
            libxml_use_internal_errors(false);

            $new->appendChild($this->DOM->importNode($divdom->documentElement, true));

            // Load formulas extension.
            if ($this->SETTINGS['formulasextension'] || $ATTRIBS['ext_formulas'][$i]) {
                $this->load_library('formulas');
            }
        }

        return $new;
    }

    private function apply_js($node) {
        global $PAGE;
        $ATTRIBS = $this->get_tagattributes($node);
        $CODE = "";

        // Load global JavaScript code from administrator settings.
        ///////////////////////////////////////////////////////////

        if ($this->SETTINGS['globalJS'] !== '' && $ATTRIBS['useGlobalJS'][0]) {
            $CODE .=
                "// Global JavaScript code from administrator settings.\n" .
                "//////////////////////////////////////////////////////\n\n" .
                $this->SETTINGS['globalJS'] .
                "\n\n";
        }

        // Define BOARDID constants and some accessibility.
        ///////////////////////////////////////////////////

        $CODE .=
            "// Define BOARDID constants.\n" .
            "////////////////////////////\n\n";
        for ($i = 0; $i < sizeof($this->IDS); $i++) {
            $name = self::BOARDID_CONST . $i;
            $CODE .=
                "const $name = '" . $this->IDS[$i] . "';\n" .
                "console.log('$name = `'+$name+'` has been prepared.');\n";
        }
        $CODE .=
            "const " . self::BOARDID_CONST . " = " . self::BOARDID_CONST . "0" . ";\n" .
            "const " . self::BOARDIDS_CONST . " = ['" . implode("', '", $this->IDS) . "'];\n" .
            "\n";

        $CODE .=
            "// Accessibility.\n" .
            "/////////////////\n\n";
        $CODE .=
            "if(JXG.exists(JXG.Options.board)) {\n" .
            "JXG.Options.board.title = '" . $ATTRIBS['title'][0] . "';\n" .
            "JXG.Options.board.description = '" . $ATTRIBS['description'][0] . "';\n" .
            "}\n";

        // Load code from <jsxgraph>-node.
        //////////////////////////////////

        $usercode = $this->DOM->saveHTML($node);
        // Remove <jsxgraph> tags.
        $usercode = preg_replace("(</?" . self::TAG . "[^>]*\>)i", "", $usercode);
        // In order not to terminate the JavaScript part prematurely, the backslash has to be escaped.
        $usercode = str_replace("</script>", "<\/script>", $usercode);

        $CODE .=
            "// Code from user input.\n" .
            "////////////////////////\n";
        $CODE .= $usercode;

        // Surround the code with version-specific strings.
        ///////////////////////////////////////////////////

        $surroundings = $this->get_code_surroundings();
        $CODE = $surroundings["pre"] . "\n\n" . $CODE . $surroundings["post"];

        // Convert HTML-entities in code.
        /////////////////////////////////

        if ($this->SETTINGS['HTMLentities'] && $ATTRIBS['entities']) {
            $CODE = html_entity_decode($CODE);
        }

        // Paste the code
        /////////////////

        // POI
        if ($this->VERSION_MOODLE["needs_unnamed_require"]) {

            if ($this->VERSION_JSX["version_number"] >= $this->jxg_to_version_number("1.5.0")) { // version 1.5.0

                $PAGE->requires->js_init_call($CODE);

            } else {

                $PAGE->requires->js_init_call($CODE);

            }

        } else {

            $PAGE->requires->js_init_call($CODE);

        }

        /*
        $t = $this->DOM->createElement('script', $CODE);
        $a = $this->DOM->createAttribute('type');
        $a->value = 'module';
        $t->appendChild($a);
        $this->DOM->appendChild($t);
        */
        /*
        $PAGE->requires->js_init_call($CODE);
        */
        /*
        $t = $this->DOM->createElement('script', '');
        $a = $this->DOM->createAttribute('type');
        $a->value = 'text/javascript';
        $t->appendChild($a);
        $a = $this->DOM->createAttribute('src');
        $a->value = new moodle_url('/filter/jsxgraph/core/jsxgraphcore-1.4.6.js');
        $t->appendChild($a);
        $this->DOM->appendChild($t);
        $t = $this->DOM->createElement('script', $CODE);
        $a = $this->DOM->createAttribute('type');
        $a->value = 'text/javascript';
        $t->appendChild($a);
        $this->DOM->appendChild($t);
        */
    }

    private function get_code_surroundings() {
        $result = [
            'pre' => '',
            'post' => ''
        ];

        $condition = '';
        for ($i = 0; $i < sizeof($this->IDS); $i++) {
            $condition .= "document.getElementById('" . $this->IDS[$i] . "') != null && ";
        }
        $condition = substr($condition, 0, -4);

        // Build from the inside out.

        // POI
        if ($this->VERSION_MOODLE["needs_unnamed_require"]) {

            if ($this->VERSION_JSX["version_number"] >= $this->jxg_to_version_number("1.5.0")) { // version 1.5.0

                $result["pre"] =
                    "require(['" . $this->get_core_url() . "'], function (JXG) {\n" .
                    "if ($condition) {\n" .
                    $result["pre"];
                $result["post"] =
                    $result["post"] .
                    "}\n " .
                    "});\n";

            } else {

                $result["pre"] =
                    "if ($condition) {\n" .
                    $result["pre"];
                $result["post"] =
                    $result["post"] .
                    "}\n ";

            }

        } else {

            if ($this->VERSION_JSX["version_number"] > $this->jxg_to_version_number("0.99.6")) { // version 0.99.6

                $result["pre"] =
                    "require(['jsxgraphcore'], function (JXG) {\n" .
                    "if ($condition) { \n" .
                    $result["pre"];
                $result["post"] =
                    $result["post"] .
                    "}\n " .
                    "});\n";

            } else {

                $result["pre"] =
                    "if ($condition) {\n" .
                    $result["pre"];
                $result["post"] =
                    $result["post"] .
                    "}\n ";

            }

        }
        /*
        $jsx_url = new moodle_url('/filter/jsxgraph/core/jsxgraphcore-1.4.6.js');
        $result["pre"] =
            "require(['" . $jsx_url . "'], function (JXG) { \nif ($condition) {" .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "}\n });\n";
        */
        /*
        $jsx_url = new moodle_url('/filter/jsxgraph/amd/build/jsxgraphcore-v1.5.0-lazy.js');
        $result["pre"] =
            "import JXG from '$jsx_url';  \nif ($condition) { " .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "\n }\n";
        */
        /*
        $result["pre"] =
            "require(['jsxgraphcore'], function (JXG) { if ($condition) { \n" .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "}\n });\n";
        */
        /*
        $result["pre"] =
            "\nif ($condition) {" .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "};";
        */

        /////////////////

        $result["pre"] =
            "\n//< ![CDATA[\n" .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "\n//]]>\n";

        $result["pre"] =
            "\n\n// ###################################################" .
            "\n// JavaScript code for JSXGraph board '" . $this->IDS[0] . "' and other\n" .
            $result["pre"];
        $result["post"] =
            $result["post"] .
            "\n// End code for JSXGraph board '" . $this->IDS[0] . "' and other " .
            "\n// ###################################################\n\n";

        return $result;
    }

    private function get_core_url() {
        return new moodle_url(self::PATH_FOR_CORES . $this->VERSION_JSX["file"]);
    }

    /**
     * Load JSXGraph code from local or from server
     */
    private function load_jsxgraph() {
        global $PAGE;

        // POI
        if ($this->VERSION_MOODLE["needs_unnamed_require"]) {

            if ($this->VERSION_JSX["version_number"] >= $this->jxg_to_version_number("1.5.0")) { // version 1.5.0

                // Nothing to do!

            } else {

                $t = $this->DOM->createElement('script', '');
                $a = $this->DOM->createAttribute('type');
                $a->value = 'text/javascript';
                $t->appendChild($a);
                $a = $this->DOM->createAttribute('src');
                $a->value = $this->get_core_url();
                $t->appendChild($a);
                $this->DOM->appendChild($t);

            }

        } else {

            $PAGE->requires->js($this->get_core_url());

        }
    }

    private function set_versions($jsxversion) {
        $this->VERSION_JSX = null;
        $this->VERSION_MOODLE = null;

        // resolve JSXGraph version
        $versions = json_decode(get_config('filter_jsxgraph', 'versions'), true);
        if (empty($jsxversion) || $jsxversion === 'auto') {
            $jsxversion = $versions[1]["id"];
        }
        foreach ($versions as $v) {
            if ($v["id"] === $jsxversion) {
                $this->VERSION_JSX = $v;
                break;
            }
        }
        $this->VERSION_JSX["version"] = $this->VERSION_JSX["id"];
        $this->VERSION_JSX["version_number"] = $this->jxg_to_version_number($this->VERSION_JSX["version"]);

        // resolve Moodle version
        $this->VERSION_MOODLE = [
            "version" => get_config('moodle', 'version'),
            "is_supported" => get_config('moodle', 'version') >= get_config('filter_jsxgraph', 'requires'),
            "needs_unnamed_require" => get_config('moodle', 'version') >= 2021051700,
        ];

        if (!$this->VERSION_MOODLE["is_supported"]) {
            $this->VERSION_MOODLE = null;

            return;
        }

        // echo '<pre>' . print_r($this->VERSION_JSX, true) . '</pre>';
        // echo '<pre>' . print_r($this->VERSION_MOODLE, true) . '</pre>';
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
                '<script type="text/javascript">
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
     * Load additional library
     *
     * @param string $libname
     *
     */
    private function load_library($libname) {
        global $PAGE;

        $libs = [
            'formulas' => 'formulas_extension/JSXQuestion.js'
        ];

        if (!array_key_exists($libname, $libs)) {
            return;
        }
        $url = self::PATH_FOR_LIBS . $libs[$libname];

        // POI
        if ($this->VERSION_MOODLE["needs_unnamed_require"]) {

            $t = $this->DOM->createElement('script', '');
            $a = $this->DOM->createAttribute('type');
            $a->value = 'text/javascript';
            $t->appendChild($a);
            $a = $this->DOM->createAttribute('src');
            $a->value = new moodle_url($url);
            $t->appendChild($a);
            $this->DOM->appendChild($t);

        } else {

            $PAGE->requires->js(new moodle_url($url));

        }
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
     * Get settings made by administrator
     *
     * @return array settings from administration
     */
    private function get_adminsettings() {
        // Set defaults.
        $defaults = [
            'versionJSXGraph' => 'auto',
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

    private function jxg_to_version_number($versionstring) {
        $arr = explode('.', $versionstring);

        return
            intval($arr[0]) * 10000 +
            intval($arr[1]) * 100 +
            intval($arr[2]) * 1;
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
