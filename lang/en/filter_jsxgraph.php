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
 * @copyright  2021 JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 *             Matthias Ehmann,
 *             Michael Gerhaeuser,
 *             Carsten Miller,
 *             Andreas Walter <andreas.walter@uni-bayreuth.de>,
 *             Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['filtername'] = 'JSXGraph';

$string['yes'] = 'yes';
$string['no'] = 'no';

$string['on'] = 'activated';
$string['off'] = 'deactivated';

$string['error'] = 'ERROR:';
$string['error0.99.5'] = 'Unfortunately the JSX filter does not support JSXGraph core version 0.99.5 due to a CDN error. Please contact your admin.';
$string['error0.99.6'] = 'Unfortunately the JSX filter does not support JSXGraph core version 0.99.6. Please contact your admin.';
$string['errorNotFound_pre'] = 'There is no JSX version ';
$string['errorNotFound_post'] = ' on CDN. The JSXGraph core could not be loaded. Please contact your admin.';

$string['header_docs'] = 'General information';
$string['docs'] = 'Thank your for using our JSXGraph filter. For current information about JSXGraph, visit our <a href="http://jsxgraph.uni-bayreuth.de/" target="_blank">homepage</a>.<br>Please note our <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md" target="_blank">detailed documentation for our filter on GitHub</a>.<br>Information on using JSXGraph can be found <a href="http://jsxgraph.uni-bayreuth.de/wp/docs/index.html" target="_blank">in the docs</a>.<br><br>Make <b>global settings</b> for the filter on this page. Some of these can be overwritten locally in tag attributes. Look at the <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md#jsxgraph-tag-attributes" target="_blank">documentation</a> for this.';
$string['header_versions'] = 'Version info';
$string['filterversion'] = 'You are using version the following <b>version of the JSXGraph filter</b> for Moodle:';
$string['deliveredversion'] = 'The following <b>JSXGraph version</b> is supplied with this filter:';

$string['header_jsxversion'] = 'Version of the used JSXGraph library';
$string['header_libs'] = 'Extensions for the JSXGraph filter';
$string['header_codingbetweentags'] = 'Coding between the tags';
$string['header_globaljs'] = 'Global JavaScript';
$string['header_dimensions'] = 'Standard dimensions';
$string['header_deprecated'] = 'Deprecated settings';

$string['jsxfromserver'] = 'JSXGraph from server';
$string['jsxfromserver_desc'] = 'Select whether the plugin is using the server version of JSXGraph core, or the locally provided one supplied with the plugin. <b>Attention:</b> there must be entered a valid version number in "<a href="#admin-serverversion">server version</a>"!';

$string['serverversion'] = 'server version';
$string['serverversion_desc'] = 'If "<a href="#admin-jsxfromserver">JSXGraph from server</a>" is checked, the version entered here is loaded by the server. Look at <a href="http://jsxgraph.uni-bayreuth.de/wp/previousreleases/" target="_blank">http://jsxgraph.uni-bayreuth.de/wp/previousreleases/</a> to see, which version is loaded from CDN. Type only the version number (0.99.5 and 0.99.6 are not supported).';

$string['formulasextension'] = 'question type formulas';
$string['formulasextension_desc'] = 'If this option is activated, another JavaScript library is loaded, which helps to use a JSXGraph board in a question of the type "formulas". (This question type must be installed!)<br>A documentation of the extension can be found in the <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph" target="_blank">associated repository at GitHub</a>.';

$string['HTMLentities'] = 'HTML entities';
$string['HTMLentities_desc'] = 'Decide whether HTMLentities like "&", "<",... are supported within the JavaScript code for JSXGraph.';

$string['convertencoding'] = 'convert encoding';
$string['convertencoding_desc'] = 'Decide whether the encoding of the text between the JSXGraph tags should be converted to UTF-8 or not.';

$string['globalJS'] = 'global JavaScript';
$string['globalJS_desc'] = 'Define a general JavaScript code that is loaded in each JSXGraph tag before the code contained in it. To type special characters like "<" use HTMLentities.';

$string['dimensions'] =
    '<p>Here you can define standard dimensions for your boards. Please be aware, that local tag attributes override only parts of them, so be careful.</p>' .
    '<p><b>To use the responsiveness of the boards, you have to change settings from given width and height to given width and aspect-ratio, </b> because if there are given width and height, aspect-ratio is ignored.</p>' .
    '<p>There are the following use-cases:</p>' .
    '<table class="table table-bordered table-sm table-striped">' .
    '<thead class="table-dark">' .
    '<tr>' .
    '     <td>#</td>' .
    '     <td>given</td>' .
    '     <td>behavior</td>' .
    '</tr>' .
    '</thead>' .
    '<tbody>' .
    '<tr>' .
    '     <td>1</td>' .
    '     <td>width and height in any combination (max-/...)</td>' .
    '     <td>The dimensions are applied to the boards <code>div</code>. Layout is like in the css specification defined. See notes (a) and (b). Aspect-ratio is ignored in this case. Please note also (c).</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>2</td>' .
    '     <td>aspect-ratio and (max-)width</td>' .
    '     <td>The boards width ist fix according its value. The height is automatically regulated following the given aspect-ratio.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>3</td>' .
    '     <td>aspect-ratio and (max-)height</td>' .
    '     <td>The boards height ist fix according its value. The width is automatically regulated following the given aspect-ratio. This case doesn\'t work on browsers which doesn\'t support aspect-ratio. The css trick (see (a)) can not help here.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>4</td>' .
    '     <td>only aspect-ratio</td>' .
    '     <td>The <a href="#admin-fallbackwidth">fallback width</a> is used. Apart from that see case 2.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>5</td>' .
    '     <td>nothing</td>' .
    '     <td>Aspect-ratio is set to <a href="#admin-fallbackaspectratio">fallback aspect-ratio</a> and then see case 4.</td>' .
    '</tr>' .
    '</tbody>' .
    '</table>' .
    '<p class="mb-0"><b>Notes:</b></p>' .
    '<p><b>(a)</b> Pay attention: the <code>div</code> uses the css attribute "aspect-ratio" which is not supported by every browser. If the browser does not support this, a trick with a wrapping <code>div</code> and padding-bottom is applied. This trick only works, if aspect-ratio and (max-)width are given, not in combination with (max-)height! For an overview of browsers which support aspect-ratio see <a href="https://caniuse.com/mdn-css_properties_aspect-ratio." target="_blank">caniuse.com</a></p>' .
    '<p><b>(b)</b> If the css trick is not needed, the result is only the <code>div</code> with id <code>BOARDID</code> for the board. The value of tag attribute wrapper-class is ignored. In the trick the <code>div</code> is wrapped by a <code>div</code> with id <code>BOARDID</code>-wrapper. This wrapper contains the main dimensions and the board-<code>div</code> gets only relative dimensions according to the case, e.g. width: 100%.</p>' .
    '<p><b>(c)</b> If only width is given, the height will be <code>0</code> like in css. You have to define an aspect-ratio or height to display the board!</p>' .
    '<p>&nbsp;</p>';

$string['aspectratio'] = 'aspect-ratio';
$string['aspectratio_desc'] = 'Format e.g. "1 / 1"';

$string['fixwidth'] = 'width';
$string['fixwidth_desc'] = 'We recommend to use here an relative value e.g. 100%.';

$string['fixheight'] = 'height';
$string['fixheight_desc'] = 'We recommend to leave this empty and use aspect-ratio and width instead.';

$string['maxwidth'] = 'max-width';
$string['maxwidth_desc'] = '';

$string['maxheight'] = 'max-height';
$string['maxheight_desc'] = '';

$string['fallbackaspectratio'] = 'fallback aspect-ratio';
$string['fallbackaspectratio_desc'] = 'See description of standard dimensions.';

$string['fallbackwidth'] = 'fallback width';
$string['fallbackwidth_desc'] = 'See description of standard dimensions.';

$string['usedivid'] = 'use div prefix';
$string['usedivid_desc'] =
    'For better compatibility you should select "No" here. This means that the ids are not made with the prefix "<a href="#admin-divid">divid</a>" and a number but with an unique identifier. <br>If you are still using old constructions, you should select "Yes". Then the deprecated setting "<a href="#admin-divid">divid</a>" will continue to be used.';

$string['divid'] = 'dynamic div id';
$string['divid_desc'] =
    '<b>Deprecated! You should now use the constant "<code>BOARDID</code>" within the <jsxgraph\> tag.</b><br>' .
    '<small>Each <code><div\></code> that contains a JSXGraph board needs a unique ID on the page. If this ID is specified in the JSXGraph tag (see <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md#jsxgraph-tag-attributes" target="_blank">documentation</a>), it can be used in the complete JavaScript included.<br>' .
    'If no board ID is specified in the tag, it is generated automatically. The prefix specified here is used for this and supplemented by a consecutive number per page, e.g. box0, box1, ...<br>' .
    'The user does not need to know the ID. In any case, it can be referenced within the JavaScript via the constant "<code>BOARDID</code>".</small>';

$string['privacy'] = 'This plugin is only used to display JSXGraph constructions typed in the editor using the jsxgraph tag. It does not store or transmit any personally identifiable information. The possibly externally integrated library jsxgraphcore.js does not process any personal data either.';
