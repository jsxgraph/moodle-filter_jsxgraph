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

// English translation of JSXGraph Moodle filter.

/**
 * JSXGraph is a cross-browser JavaScript library for interactive geometry,
 * function plotting, charting, and data visualization in the web browser.
 * JSXGraph is implemented in pure JavaScript and does not rely on any other
 * library. Special care has been taken to optimize the performance.
 *
 * This is a plugin for Moodle to enable function plotting and dynamic
 * geometry constructions with JSXGraph within a Moodle platform.
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

$string['filtername'] = 'JSXGraph';

$string['yes'] = 'yes';
$string['no'] = 'no';

$string['error'] = 'ERROR:';
$string['error0.99.5'] = 'Unfortunately the JSX filter does not support JSXGraph core version 0.99.5 due to a CDN error. Please contact your admin.';
$string['error0.99.6'] = 'Unfortunately the JSX filter does not support JSXGraph core version 0.99.6. Please contact your admin.';
$string['errorNotFound_pre'] = 'There is no JSX version ';
$string['errorNotFound_post'] = ' on CDN. The JSXGraph core could not be loaded. Please contact your admin.';

$string['settings_header'] = 'Global settings';
$string['docs_header'] = 'General information';
$string['docs'] = 'Thank your for using our JSXGraph filter. For current information about JSXGraph, visit our <a href="http://jsxgraph.uni-bayreuth.de/" target="_blank">homepage</a>.<br>Please note our <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md" target="_blank">detailed documentation for our filter on GitHub</a>.<br>Information on using JSXGraph can be found <a href="http://jsxgraph.uni-bayreuth.de/wp/docs/index.html" target="_blank">in the docs</a>.';

$string['jsxfromserver'] = 'JSXGraph from server';
$string['jsxfromserver_desc'] = 'Select whether the plugin is using the server version of JSXGraph core, or the locally provided one supplied with the plugin. <b>Attention:</b> there must be entered a valid version number in "<a href="#admin-serverversion">server version</a>"!';

$string['serverversion'] = 'server version';
$string['serverversion_desc'] = 'If "<a href="#admin-jsxfromserver">JSXGraph from server</a>" is checked, the version entered here is loaded by the server. Look at <a href="http://jsxgraph.uni-bayreuth.de/wp/previousreleases/" target="_blank">http://jsxgraph.uni-bayreuth.de/wp/previousreleases/</a> to see, which version is loaded from CDN. Type only the version number (0.99.5 and 0.99.6 are not supported).';

$string['HTMLentities'] = 'HTML entities';
$string['HTMLentities_desc'] = 'Decide wether HTMLentities like "&", "<",... are supported within the JavaScript code for JSXGraph.';

$string['convertencoding'] = 'convert encoding';
$string['convertencoding_desc'] = 'Decide wether the encoding of the text between the JSXGraph tags should be converted to UTF-8 or not.';

$string['globalJS'] = 'global JavaScript';
$string['globalJS_desc'] = 'Define a general JavaScript code that is loaded in each JSXGraph tag before the code contained in it. To type special characters like "<" use HTMLentities.';

$string['width'] = 'width';
$string['width_desc'] = 'Width of JSXGraph container.';

$string['height'] = 'height';
$string['height_desc'] = 'Height of JSXGraph container.';

$string['divid'] = 'dynamic div id';
$string['divid_desc'] =
    '<b>Deprecated! You should now use the constant "<code>BOARDID</code>" within the <jsxgraph\> tag.</b><br>' .
    '<small>Each <code><div\></code> that contains a JSXGraph board needs a unique ID on the page. If this ID is specified in the JSXGraph tag (see <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md#jsxgraph-tag-attributes" target="_blank">documentation</a>), it can be used in the complete JavaScript included.<br>' .
    'If no board ID is specified in the tag, it is generated automatically. The prefix specified here is used for this and supplemented by a consecutive number per page, e.g. box0, box1, ...<br>' .
    'The user does not need to know the ID. In any case, it can be referenced within the JavaScript via the constant "<code>BOARDID</code>".</small>';

$string['privacy'] = 'This plugin is only used to display JSXGraph constructions typed in the editor using the jsxgraph tag. It does not store or transmit any personally identifiable information. The possibly externally integrated library jsxgraphcore.js does not process any personal data either.';
