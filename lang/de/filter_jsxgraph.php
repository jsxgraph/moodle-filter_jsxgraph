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

$string['yes'] = 'ja';
$string['no'] = 'nein';

$string['on'] = 'aktiviert';
$string['off'] = 'deaktiviert';

$string['error'] = 'FEHLER:';
$string['error0.99.5'] = 'Leider wird die Core-Version 0.99.5 aufgrund eines CDN-Fehlers vom JSXGraph-Filter nicht unterstützt. Bitte kontaktieren Sie Ihren Administrator.';
$string['error0.99.6'] = 'Leider wird die Core-Version 0.99.6 vom JSXGraph-Filter nicht unterstützt. Bitte kontaktieren Sie Ihren Administrator.';
$string['errorNotFound_pre'] = 'Es existiert keine JSXGraph-Version ';
$string['errorNotFound_post'] = ' auf CDN. Der JSXGraph-Core konnte nicht geladen werden. Bitte kontaktieren Sie Ihren Administrator.';

$string['header_docs'] = 'Allgemeine Informationen';
$string['docs'] = 'Vielen Dank, dass sie den JSXGraph-Filter benutzen. Für aktuelle Informationen über JSXGraph besuchen Sie einfach unsere <a href="http://jsxgraph.uni-bayreuth.de/" target="_blank">Homepage</a>.<br>Beachten Sie unsere <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md" target="_blank">detaillierte Filter-Dokumentation auf GitHub</a>.<br>Informationen über die Verwendung von JSXGraph finden sie <a href="http://jsxgraph.uni-bayreuth.de/wp/docs/index.html" target="_blank">in den docs</a>.<br><br>Nehmen Sie auf dieser Seite <b>globale Einstellungen</b> für den Filter vor. Einige davon lassen sich in Tag-Attributen lokal überschreiben. Siehe hierzu die <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md#jsxgraph-tag-attributes" target="_blank">Dokumentation.</a>';
$string['header_versions'] = 'Versionsinformationen';
$string['filterversion'] = 'Sie benutzen derzeit die folgende <b>Version des JSXGraph-Filters</b> für Moodle:';
$string['deliveredversion'] = 'Mit diesem Filter wird die folgende <b>JSXGraph-Version</b> ausgeliefert:';

$string['header_jsxversion'] = 'Version der verwendeten JSXGraph-Bibliothek';
$string['header_libs'] = 'Erweiterungen für den JSXGraph-Filter';
$string['header_codingbetweentags'] = 'Codierung zwischen den Tags';
$string['header_globaljs'] = 'Globales JavaScript';
$string['header_dimensions'] = 'Standard-Dimensionen';
$string['header_deprecated'] = 'Veraltete Einstellungen';

$string['jsxfromserver'] = 'JSXGraph vom Server';
$string['jsxfromserver_desc'] = 'Wählen Sie aus, ob für das Plugin die Server-Version des JSXGraph-Cores genutzt wird, oder die lokal vorliegende, die mit dem Plugin installiert wurde. <b>Achtung:</b> Es muss eine gültige Versionsnummer unter "<a href="#admin-serverversion">Serverversion</a>" eingetragen sein!';

$string['serverversion'] = 'Serverversion';
$string['serverversion_desc'] = 'Ist "<a href="#admin-jsxfromserver">JSXGraph vom Server</a>" gewählt, wird die hier eingetragene Version vom Server geladen. Unter <a href="http://jsxgraph.uni-bayreuth.de/wp/previousreleases/" target="_blank">http://jsxgraph.uni-bayreuth.de/wp/previousreleases/</a> finden Sie die Versionen, die von CDN geladen werden können. Geben Sie nur die Versionsnummer ein (0.99.5 und 0.99.6 werden leider nicht unterstützt).';

$string['formulasextension'] = 'Fragetyp formulas';
$string['formulasextension_desc'] = 'Ist diese Option aktiviert, wird eine weitere JavaScript Bibliothek geladen, mit deren Hilfe ein JSXGraph-Board in einer Frage des Typs "formulas" verwendet werden kann. (Hierzu muss dieser Fragetyp installiert sein!)<br>Eine Dokumentation der Erweiterung findet sich im <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph" target="_blank">zugehörigen Repository bei GitHub</a>.';

$string['HTMLentities'] = 'HTMLentities';
$string['HTMLentities_desc'] = 'Einstellung, ob HTMLentities wie z.B. "&", "<",... innerhalb des JavaScript-Codes für JSXGraph unterstützt werden.';

$string['convertencoding'] = 'Konvertiere Text-Codierung';
$string['convertencoding_desc'] = 'Einstellung, ob die Codierung des Texts zwischen den JSXGraph-Tags in UTF-8 konvertiert werden soll oder nicht.';

$string['globalJS'] = 'Globales JavaScript';
$string['globalJS_desc'] = 'Definieren Sie hier einen allgemein gültigen JavaScript-Code, der in jedem JSXGraph-Tag vor dem darin enthalteten Code geladen wird. Um Sonderzeichen wie beispielsweise "<" zu nutzen, verwenden Sie die entsprechende HTMLentity.';

$string['dimensions'] =
    '<p>Hier können Sie die Standard-Dimensionen für Ihre Boards definieren. Bitte beachten Sie, dass lokale Tag-Attribute nur Teile der hier definierten Werte überschreiben und es dadurch zu unvorhergesehenen Überschneidungen kommen kann. Benutzen Sie diese Einstellungen deshalb mit Bedacht!</p>' .
    '<p><b>Um die Responsivität von Boards nutzen zu können, dürfen nicht Höhe und Breite gleichzeitig angegeben werden. Stattdessen sollten Sie width und aspect-ratio verwenden,</b> denn bei gegebener Höhe und Breite wird das Seitenverhältnis ignoriert.</p>' .
    '<p>Es gibt die folgenden Fälle:</p>' .
    '<table class="table table-bordered table-sm table-striped">' .
    '<thead class="table-dark">' .
    '<tr>' .
    '     <td>#</td>' .
    '     <td>gegebene Werte</td>' .
    '     <td>Verhalten</td>' .
    '</tr>' .
    '</thead>' .
    '<tbody>' .
    '<tr>' .
    '     <td>1</td>' .
    '     <td>width und height in irgendeiner Kombination (max-/...)</td>' .
    '     <td>Die Dimensionen werden auf das <code>div</code> des Boards angewandt, wobei das Layout sich an der CSS-Spezifikation orientiert. Beachten Sie die Fußnoten (a) und (b). Aspect-ratio wird in diesem Fall ignoriert. Beachten Sie außerdem die Anmerkung (c).</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>2</td>' .
    '     <td>aspect-ratio und (max-)width</td>' .
    '     <td>Die Breite des Boards wird durch den Wert von width festgelegt. Die Höhe wird entsprechend dem gegebenen Seitenverhältnis reguliert.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>3</td>' .
    '     <td>aspect-ratio und (max-)height</td>' .
    '     <td>Die Höhe des Boards wird durch den Wert von height festgelegt. Die Breite wird entsprechend dem gegebenen Seitenverhältnis reguliert. Dieser Fall wird auf Browsern, die die CSS-Eigenschaft aspect-ratio nicht unterstützen, nicht funktionieren, da der CSS-Trick (siehe (a)) hier nicht anwendbar ist.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>4</td>' .
    '     <td>nur aspect-ratio</td>' .
    '     <td>Hier wird die <a href="#admin-fallbackwidth">Fallback-Breite</a> benutzt, sodass anschließend Fall 2 Anwendung findet.</td>' .
    '</tr>' .
    '<tr>' .
    '     <td>5</td>' .
    '     <td>nichts</td>' .
    '     <td>Aspect-ratio wird mit dem Wert aus <a href="#admin-fallbackaspectratio">Fallback-Seitenverhältnis</a> versehen. Anschließend wird Fall 4 angewandt.</td>' .
    '</tr>' .
    '</tbody>' .
    '</table>' .
    '<p class="mb-0"><b>Anmerkungen:</b></p>' .
    '<p><b>(a)</b> Achtung: Das <code>div</code> verwendet das Attribut "aspect-ratio", das nicht von jedem Browser unterstützt wird. In diesem fAll wird ein Trick angewandt: das Board wird mit einem <code>div</code> umgeben und mit padding-bottom versehen. Dieser Trick funktioniert nur, wenn Seitenverhältnis und <i>(max-)width</i> gegeben sind, nicht in Kombination mit height! Eine Übersicht, in welchem Browser dieser Trick nicht notwendig ist, liefert <a href="https://caniuse.com/mdn-css_properties_aspect-ratio." target="_blank">caniuse.com</a></p>' .
    '<p><b>(b)</b> WEnn der trick nicht verwendet wird, besteht das Board nur aus einem <code>div</code> mit der id <code>BOARDID</code>. Der Wert aus dem Tag-Attribut wrapper-class wird ignoriert. Beim Trick wird dieses <code>div</code> von einem weiteren <code>div</code> mit der id <code>BOARDID</code>-wrapper umgeben. Dieser "Wrapper" enthält die Dimensionen des Boards, während die Größe des Boards selbst relativ zum umgebenden <code>div</code> reguliert ist.</p>' .
    '<p><b>(c)</b> Falls nur die Breite gegeben ist, die Höhe bleibt <code>0</code> (ähnlich zu einem <code>div</code>, das kienen Inhalt und nur eine Breite hat). Sie müssen also ein Seitenverhältnis oder eine Höhe für das Board definieren!</p>' .
    '<p>&nbsp;</p>';

$string['aspectratio'] = 'Seitenverhältnis';
$string['aspectratio_desc'] = 'Format z.B. "1 / 1"';

$string['fixwidth'] = 'Breite';
$string['fixwidth_desc'] = 'Wir empfehlen, hier einen relativen Wert zu verwenden, z.B. 100%.';

$string['fixheight'] = 'Höhe';
$string['fixheight_desc'] = 'Wir empfehlen, dieses Feld leer zu lassen und stattdessen Höhe und Seitenverhältnis zu verwenden.';

$string['maxwidth'] = 'Maximale Breite';
$string['maxwidth_desc'] = '';

$string['maxheight'] = 'Maximale Höhe';
$string['maxheight_desc'] = '';

$string['fallbackaspectratio'] = 'Fallback-Seitenverhältnis';
$string['fallbackaspectratio_desc'] = 'Siehe Beschreibung der Standard-Dimensionen.';

$string['fallbackwidth'] = 'Fallback-Breite';
$string['fallbackwidth_desc'] = 'Siehe Beschreibung der Standard-Dimensionen.';

$string['usedivid'] = 'Benutze div-Präfix';
$string['usedivid_desc'] =
    'Für bessere Kompatibilität sollten Sie hier "Nein" wählen. Dadurch werden die IDs nicht mit dem Präfix aus "<a href="#admin-divid">divid</a>" und einer Nummer versehen, sondern mit einer eindeutigen ID. <br>Verwenden Sie noch alte Konstruktionen, sollten Sie "Ja" auswählen. Dann wird die veraltete Einstellung "<a href="#admin-divid">divid</a>" weiter verwendet.';

$string['divid'] = 'Dynamische Board-ID';
$string['divid_desc'] =
    '<b>Veraltet! Sie sollten von nun an die Konstante "<code>BOARDID</code>" innerhalb des <jsxgraph\>-Tags benutzen.</b><br>' .
    '<small>Jedes <code><div\></code>, das ein JSXGraph-Board enthält, benötigt eine eindeutige ID auf der Seite. Wird diese ID im JSXGraph-Tag angegeben (siehe <a href="https://github.com/jsxgraph/moodle-filter_jsxgraph/blob/master/README.md#jsxgraph-tag-attributes" target="_blank">Dokumentation</a>), so gilt sie für das komplette enthaltene JavaScript.<br>' .
    'Ist im Tag keine Board-ID angegeben, wird diese automatisch erzeugt. Hierzu wird das hier angegebene Präfix verwendet und um eine fortlaufende Nummer pro Seite ergänzt, z.B. box0, box1,...<br>' .
    'Der Benutzer braucht die ID nicht zu kennen. Sie kann in jedem Fall innerhalb des JavaScript über die Konstante "<code>BOARDID</code>" referenziert werden.</small>';

$string['privacy'] = 'Dieses Plugin dient lediglich dazu, JSXGraph-Konstruktionen, die mithilfe des jsxgraph-Tags im Editor eingegeben werden, anzuzeigen. Es speichert und übermittelt selbst keine personenbezonenen Daten. Die eventuell extern eingebundene Bibliothek jsxgraphcore.js verarbeitet ebenfalls keinerlei personenbezogene Daten.';
