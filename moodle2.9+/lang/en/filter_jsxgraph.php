<?php
    /**
     * Version details
     *
     * @package    jsxgraph filter
     * @copyright  2017 Michael Gerhaeuser, Matthias Ehmann, Carsten Miller, Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>, Andreas Walter
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    
    $string['filtername'] = 'JSXGraph';
    
    $string['yes'] = 'yes';
    $string['no'] = 'no';
    
    $string['error0.99.6'] = 'ERROR: Unfortunately the JSX filter does not support the core version 0.99.6. Please contact your admin.';
    $string['errorNotFound_pre'] = 'ERROR: There is no JSX version ';
    $string['errorNotFound_post'] = ' on CDN. The JSX-Graph core could not be loaded. Please contact your admin.';
    
    $string['jsxfromserver'] = 'JSXGraph from server';
    $string['jsxfromserver_desc'] = 'Select whether the plugin is using the server version of JSXGraph core, or the locally provided one supplied with the plugin. <b>Attention:</b> there must be entered a valid version number in "<a href="#admin-filter_jsxgraph_serverversion">server version</a>"!';
    
    $string['serverversion'] = 'server version';
    $string['serverversion_desc'] = 'If "<a href="#admin-filter_jsxgraph_jsxfromserver">JSXGraph from server</a>" is chosen, the version entered here is loaded by the server. Look at <a href="http://jsxgraph.uni-bayreuth.de/wp/previousreleases/" target="_blank">http://jsxgraph.uni-bayreuth.de/wp/previousreleases/</a> to see, which version is loaded from CDN. Type only the version number.';
    
    $string['HTMLentities'] = 'HTMLentities';
    $string['HTMLentities_desc'] = 'Decide wether HTMLentities like "&", "<",... are supported within the JavaScript code for JSXGraph.';
    
    $string['globalJS'] = 'Global JavaScript';
    $string['globalJS_desc'] = 'Define a general JavaScript code that is loaded in each jsxgraph tag before the code contained in it. To type special characters like "<" use HTMLentities.';
    
    $string['divid'] = 'div id';
    $string['divid_desc'] = 'ID of the division containing JSXGraph. Number is added automatically, e.g. box0, box1, ...';
    
    $string['boardvar'] = 'board var name';
    $string['boardvar_desc'] = 'Variable name of the JSXGraph board (needed only if file is read)';
    
    $string['width'] = 'width';
    $string['width_desc'] = 'Width of JSXGraph container';
    
    $string['height'] = 'height';
    $string['height_desc'] = 'Height of JSXGraph container';
    