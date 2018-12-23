<?php
    /**
     * Privacy Subsystem implementation for moodle-filter_jsxgraph.
     *
     * @package    jsxgraph moodle filter
     * @copyright  2018 Michael Gerhaeuser, Matthias Ehmann, Carsten Miller, Alfred Wassermann, Andreas Walter
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    
    namespace filter_jsxgraph\privacy;
    
    defined('MOODLE_INTERNAL') || die();
    
    class provider implements \core_privacy\local\metadata\null_provider {
        
        /**
         * Get the language string identifier with the component's language
         * file to explain why this plugin stores no data.
         *
         * @return  string
         */
        public static function get_reason(): string {
            return 'privacy';
        }
    }
