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
 * Upgrade Script for filter_jsxgraph
 *
 * @package    filter_jsxgraph
 * @copyright  2022 ISB Bayern
 *             Peter Mayer <peter.mayer@isb.bayern.de>
 * and
 *             JSXGraph team - Center for Mobile Learning with Digital Technology – Universität Bayreuth
 *             Matthias Ehmann,
 *             Michael Gerhaeuser,
 *             Carsten Miller,
 *             Andreas Walter <andreas.walter@uni-bayreuth.de>,
 *             Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * xmldb_filter_jsxgraph_upgrade
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_jsxgraph_upgrade($oldversion) {

    $release = 'v1.4.3'; // This value should be the same as in version.php!
    $recommendedjsx = '1.4.3';
    $deliveredjsx = '1.4.3';

    try {
        set_config('release', $release, 'filter_jsxgraph');
        set_config('recommendedJSX', $recommendedjsx, 'filter_jsxgraph');
        set_config('deliveredJSX', $deliveredjsx, 'filter_jsxgraph');
    } catch (Exception $e) {
        // Exception is not handled because it is not necessary.
        // This has to be here for code prechecks.
        echo '';
    }

    return true;
}
