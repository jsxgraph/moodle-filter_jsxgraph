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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Add the placeholder to the description of a setting that should be separated from the following setting.
    // Deprecated.
    $placeholder = '<div class="placeholder" style="height: 50px; padding: 1px 0 25px 0; margin-left: -33%;"><hr></div>';
    $last = '<div class="placeholder" style="height: 50px;"></div>';

    // Use first supported version if class has not been loaded yet.
    $recommended = '1.1.0';
    if (class_exists('filter_jsxgraph')) {
        $recommended = filter_jsxgraph::$recommended;
    }

    if (!function_exists('get_jsxfilter_version')) {
        /**
         * Get the filter version as a HTML-String.
         *
         * @return string
         */
        function get_jsxfilter_version() {
            $version = get_config('filter_jsxgraph', 'version');
            if (substr($version, 8, 2) === '00') {
                $version = substr($version, 0, 8);
            } else {
                $version = substr_replace($version, ' (', 8, 0) . ')';
            }
            $version = substr_replace($version, '-', 6, 0);
            $version = substr_replace($version, '-', 4, 0);

            return '<div style="text-align: center;margin-top: -0.75rem;margin-bottom: 1rem;"><b><i>v' .
                $version .
                '</i></b></div>';
        }
    }


    $settings->add(new admin_setting_heading('filter_jsxgraph/docs',
                                             get_string('header_docs', 'filter_jsxgraph'),
                                             get_string('docs', 'filter_jsxgraph')));

    $settings->add(new admin_setting_heading('filter_jsxgraph/filterversion',
                                             get_string('header_filterversion', 'filter_jsxgraph'),
                                             get_string('filterversion', 'filter_jsxgraph') . get_jsxfilter_version()));

    $settings->add(new admin_setting_heading('filter_jsxgraph/jsxversion',
                                             get_string('header_jsxversion', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configcheckbox('filter_jsxgraph/jsxfromserver',
                                                    get_string('jsxfromserver', 'filter_jsxgraph'),
                                                    get_string('jsxfromserver_desc', 'filter_jsxgraph'),
                                                    '0'));

    $settings->add(new admin_setting_configtext_with_maxlength('filter_jsxgraph/serverversion',
                                                               get_string('serverversion', 'filter_jsxgraph'),
                                                               get_string('serverversion_desc', 'filter_jsxgraph'),
                                                               $recommended, PARAM_TEXT, 6, 6));

    $settings->add(new admin_setting_heading('filter_jsxgraph/libs',
                                             get_string('header_libs', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configselect('filter_jsxgraph/formulasextension',
                                                  get_string('formulasextension', 'filter_jsxgraph'),
                                                  get_string('formulasextension_desc', 'filter_jsxgraph'),
                                                  '1',
                                                  [get_string('off', 'filter_jsxgraph'), get_string('on', 'filter_jsxgraph')]));

    $settings->add(new admin_setting_heading('filter_jsxgraph/codingbetweentags',
                                             get_string('header_codingbetweentags', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configselect('filter_jsxgraph/HTMLentities',
                                                  get_string('HTMLentities', 'filter_jsxgraph'),
                                                  get_string('HTMLentities_desc', 'filter_jsxgraph'),
                                                  '1',
                                                  [get_string('no', 'filter_jsxgraph'), get_string('yes', 'filter_jsxgraph')]));

    $settings->add(new admin_setting_configselect('filter_jsxgraph/convertencoding',
                                                  get_string('convertencoding', 'filter_jsxgraph'),
                                                  get_string('convertencoding_desc', 'filter_jsxgraph'),
                                                  '1',
                                                  [get_string('no', 'filter_jsxgraph'), get_string('yes', 'filter_jsxgraph')]));

    $settings->add(new admin_setting_heading('filter_jsxgraph/globaljs',
                                             get_string('header_globaljs', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configtextarea('filter_jsxgraph/globalJS',
                                                    get_string('globalJS', 'filter_jsxgraph'),
                                                    get_string('globalJS_desc', 'filter_jsxgraph'),
                                                    '', PARAM_RAW, 60, 20));

    $settings->add(new admin_setting_heading('filter_jsxgraph/dimensions',
                                             get_string('header_dimensions', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configtext('filter_jsxgraph/width',
                                                get_string('width', 'filter_jsxgraph'),
                                                get_string('width_desc', 'filter_jsxgraph'),
                                                '500', PARAM_INT));

    $settings->add(new admin_setting_configtext('filter_jsxgraph/height',
                                                get_string('height', 'filter_jsxgraph'),
                                                get_string('height_desc', 'filter_jsxgraph'),
                                                '400', PARAM_INT));

    $settings->add(new admin_setting_heading('filter_jsxgraph/deprecated',
                                             get_string('header_deprecated', 'filter_jsxgraph'),
                                             ''));

    $settings->add(new admin_setting_configtext('filter_jsxgraph/divid',
                                                get_string('divid', 'filter_jsxgraph'),
                                                get_string('divid_desc', 'filter_jsxgraph'),
                                                'box'));
    $settings->add(new admin_setting_heading('filter_jsxgraph/last',
                                             '', '<br><br>'));
}
