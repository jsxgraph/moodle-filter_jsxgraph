<?php
// This file is part of Moodle - http://moodle.org/
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
 * Upgrade Script for filter_jsxgraph
 *
 * @package    filter_jsxgraph
 * @copyright  2022, ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_filter_jsxgraph_upgrade($oldversion) {

    if ($oldversion < 2022032900) {
        $release = 'v1.4.2';
        $recommendedJSX = '1.4.2';
        $deliveredJSX = '1.4.2';
        try {
            set_config('release', $release, 'filter_jsxgraph');
            set_config('recommendedJSX', $recommendedJSX, 'filter_jsxgraph');
            set_config('deliveredJSX', $deliveredJSX, 'filter_jsxgraph');
        } catch (Exception $e) {
            // Exception is not handled because it is not necessary.
            // This has to be here for code prechecks.
            echo '';
        }
    }
    return true;
}
