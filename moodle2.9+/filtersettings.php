<?php
    /**
     * Version details
     *
     * @package    jsxgraph filter
     * @copyright  2017 Michael Gerhaeuser, Matthias Ehmann, Carsten Miller, Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>, Andreas Walter
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    
    defined('MOODLE_INTERNAL') || die();
    
    if ($ADMIN->fulltree) {
        
        $settings->add(new admin_setting_configcheckbox('filter_jsxgraph_jsxfromserver',
                                                        get_string('jsxfromserver', 'filter_jsxgraph'),
                                                        get_string('jsxfromserver_desc', 'filter_jsxgraph'),
                                                        '0'));
        
        $settings->add(new admin_setting_configtext_with_maxlength('filter_jsxgraph_serverversion',
                                                                   get_string('serverversion', 'filter_jsxgraph'),
                                                                   get_string('serverversion_desc', 'filter_jsxgraph'),
                                                                   '0.99.6', PARAM_TEXT, 6, 6));
        
        $settings->add(new admin_setting_configtext('filter_jsxgraph_divid',
                                                    get_string('divid', 'filter_jsxgraph'),
                                                    get_string('divid_desc', 'filter_jsxgraph'),
                                                    'box'));
        
        $settings->add(new admin_setting_configtext('filter_jsxgraph_boardvar',
                                                    get_string('boardvar', 'filter_jsxgraph'),
                                                    get_string('boardvar_desc', 'filter_jsxgraph'),
                                                    'board'));
        
        $settings->add(new admin_setting_configtext('filter_jsxgraph_width',
                                                    get_string('width', 'filter_jsxgraph'),
                                                    get_string('width_desc', 'filter_jsxgraph'),
                                                    '500', PARAM_INT));
        
        $settings->add(new admin_setting_configtext('filter_jsxgraph_height',
                                                    get_string('height', 'filter_jsxgraph'),
                                                    get_string('height_desc', 'filter_jsxgraph'),
                                                    '400', PARAM_INT));
        
        $settings->add(new admin_setting_configselect('filter_jsxgraph_HTMLentities',
                                                      get_string('HTMLentities', 'filter_jsxgraph'),
                                                      get_string('HTMLentities_desc', 'filter_jsxgraph'),
                                                      '0', [get_string('yes', 'filter_jsxgraph'), get_string('no', 'filter_jsxgraph')]));
        
        $settings->add(new admin_setting_configtextarea('filter_jsxgraph_globalJS',
                                                        get_string('globalJS', 'filter_jsxgraph'),
                                                        get_string('globalJS_desc', 'filter_jsxgraph'),
                                                        '', PARAM_RAW, 60, 20));
        
    }
