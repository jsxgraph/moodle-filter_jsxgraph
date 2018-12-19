<?php
    /**
     * Version details
     *
     * @package    jsxgraph filter
     * @copyright  2018 Michael Gerhaeuser, Matthias Ehmann, Carsten Miller, Alfred Wassermann <alfred.wassermann@uni-bayreuth.de>, Andreas Walter
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    
    $string['filtername'] = 'JSXGraph';
    
    $string['yes'] = 'ja';
    $string['no'] = 'nein';
    
    $string['error'] = 'FEHLER:';
    $string['error0.99.6'] = 'Leider wird die Core-Version 0.99.6 vom JSXGraph-Filter nicht unterstützt. Bitte kontaktieren Sie Ihren Administrator.';
    $string['errorNotFound_pre'] = 'Es existiert keine JSXGraph-Version ';
    $string['errorNotFound_post'] = ' auf CDN. Der JSXGraph-Core konnte nicht geladen werden. Bitte kontaktieren Sie Ihren Administrator.';
    
    $string['jsxfromserver'] = 'JSXGraph vom Server';
    $string['jsxfromserver_desc'] = 'Wählen Sie aus, ob für das Plugin die Server-Version des JSXGraph-Cores genutzt wird, oder die lokal vorliegende, die mit dem Plugin installiert wurde. <b>Achtung:</b> Es muss eine gültige Versionsnummer unter "<a href="#admin-filter_jsxgraph_serverversion">Serverversion</a>" eingetragen sein!';
    
    $string['serverversion'] = 'Serverversion';
    $string['serverversion_desc'] = 'Ist "<a href="#admin-filter_jsxgraph_jsxfromserver">JSXGraph vom Server</a>" gewählt, wird die hier eingetragene Version vom Server geladen. Unter <a href="http://jsxgraph.uni-bayreuth.de/wp/previousreleases/" target="_blank">http://jsxgraph.uni-bayreuth.de/wp/previousreleases/</a> finden Sie die Versionen, die von CDN geladen werden können. Geben Sie nur die Versionsnummer ein.';
    
    $string['HTMLentities'] = 'HTMLentities';
    $string['HTMLentities_desc'] = 'Einstellung, ob HTMLentities wie z.B. "&", "<",... innerhalb des JavaScript-Codes für JSXGraph unterstützt werden.';
    
    $string['globalJS'] = 'Globales JavaScript';
    $string['globalJS_desc'] = 'Definieren Sie hier einen allgemein gültigen JavaScript-Code, der in jedem JSXGraph-Tag vor dem darin enthalteten Code geladen wird. Um Sonderzeichen wie beispielsweise "<" zu nutzen, verwenden Sie die entsprechende HTMLentity.';
    
    $string['divid'] = 'Div-ID';
    $string['divid_desc'] = 'ID des <div\>, das das JSXGraph-Board enthält. Eine fortlaufende Nummer wird automatisch ergänzt, z.B. box0, box1,...';
    
    $string['width'] = 'Breite';
    $string['width_desc'] = 'Standardbreite des JSXGraph-Containers.';
    
    $string['height'] = 'Höhe';
    $string['height_desc'] = 'Standardhöhe des JSXGraph-Containers.';
    