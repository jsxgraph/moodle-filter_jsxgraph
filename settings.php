<?php
// This file is part of JSXGraph Moodle Filter.
//
// Moodle is free software: you can redistribute it and/or modify
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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Add the placeholder to the description of a setting that should be separated from the following setting
    $placeholder = '<div class="placeholder" style="height: 50px;padding-top: 25px;padding-bottom: 1rem;margin-left: -33%;"><hr></div>';
    $last = '<div class="placeholder" style="height: 50px;"></div>';
    
    $recommended_version = '0.99.6'; // use first supported version if class has not been loaded yet
    if (class_exists('filter_jsxgraph')) {
        $recommended_version = filter_jsxgraph::$recommended_version;
    }
    
    $settings->add(new admin_setting_configcheckbox('filter_jsxgraph/jsxfromserver',
                                                    get_string('jsxfromserver', 'filter_jsxgraph'),
                                                    get_string('jsxfromserver_desc', 'filter_jsxgraph'),
                                                    '0'));
    
    $settings->add(new admin_setting_configtext_with_maxlength('filter_jsxgraph/serverversion',
                                                               get_string('serverversion', 'filter_jsxgraph'),
                                                               get_string('serverversion_desc', 'filter_jsxgraph') . $placeholder,
                                                               $recommended_version, PARAM_TEXT, 6, 6));
    
    $settings->add(new admin_setting_configselect('filter_jsxgraph/HTMLentities',
                                                  get_string('HTMLentities', 'filter_jsxgraph'),
                                                  get_string('HTMLentities_desc', 'filter_jsxgraph') . $placeholder,
                                                  '1', [get_string('no', 'filter_jsxgraph'), get_string('yes', 'filter_jsxgraph')]));
    
    $settings->add(new admin_setting_configtextarea('filter_jsxgraph/globalJS',
                                                    get_string('globalJS', 'filter_jsxgraph'),
                                                    get_string('globalJS_desc', 'filter_jsxgraph') . $placeholder,
                                                    '', PARAM_RAW, 60, 20));
    
    $settings->add(new admin_setting_configtext('filter_jsxgraph/divid',
                                                get_string('divid', 'filter_jsxgraph'),
                                                get_string('divid_desc', 'filter_jsxgraph') . $placeholder,
                                                'box'));
    
    $settings->add(new admin_setting_configtext('filter_jsxgraph/width',
                                                get_string('width', 'filter_jsxgraph'),
                                                get_string('width_desc', 'filter_jsxgraph'),
                                                '500', PARAM_INT));
    
    $settings->add(new admin_setting_configtext('filter_jsxgraph/height',
                                                get_string('height', 'filter_jsxgraph'),
                                                get_string('height_desc', 'filter_jsxgraph') . $last,
                                                '400', PARAM_INT));
}
