# Moodle JSXGraph filter [![Build Status](https://travis-ci.org/jsxgraph/moodle-filter_jsxgraph.svg?branch=master)](https://travis-ci.org/jsxgraph/moodle-filter_jsxgraph)

Also listed in [Moodle plugins directory](https://moodle.org/plugins/filter_jsxgraph).

### About JSXGraph

JSXGraph is a cross-browser JavaScript library for interactive geometry, function plotting, charting, and data visualization in the web browser.
JSXGraph is implemented in pure JavaScript and does not rely on any other library. Special care has been taken to optimize the performance.

Have a look at [www.jsxgraph.org](https://jsxgraph.org/).

##### Features
- Euclidean and projective Geometry
- Curve plotting
- Open source
- High-performance
- Small footprint
- No dependencies
- Multi-touch support
- Backward compatible down to IE 6

### Our filter

This is a plugin for [Moodle](http://moodle.org) to enable function plotting and dynamic geometry constructions with [JSXGraph](http://jsxgraph.org) within a Moodle platform.
Using the [JSXGraph](http://jsxgraph.org) filter makes it a lot easier to embed [JSXGraph](http://jsxgraph.org) constructions into Moodle online documents, e.g. in contents like page, quiz, link,... .

## Installation
### Installation with Moodle routine (by Moodle admin)

To install the filter for moodle2.9+ you can follow the steps below:

1. Download the entire `master` branch as a ZIP-compressed folder via the GitHub download button<br>
   **Do not unpack the ZIP directory!**
2. In Moodle, navigate to `Site administration -> Plugins -> Install plugins`
3. Under `Install plugin from ZIP file`, drag and drop the downloaded ZIP directory into input field und click on `Show more...`
4. Choose the plugin type `Text filter (filter)`
5. Rename the root directory to `jsxgraph` by filling the input (be sure to write correctly)
6. Click on `Install plugin from ZIP the file` and follow the instructions
7. After installing go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters` and switch the `Active?`-attribute of JSXGraph to `on`

### Installation in Moodle directory (by file server admin)

Otherwise, you can also install the filter with the following steps:

1. Download the entire `master` branch as a ZIP-compressed folder via the github download button
2. Create a folder `jsxgraph` in the directory `moodle -> filter` of your Moodle installation (be sure to write correctly)
3. Upload the files and folders contained in the ZIP directory to the directory just created (`screenshots` directory is not needed anymore) 
4. Open site root of your Moodle installation and follow the steps to install plugin 
5. After installing go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters` and switch the `Active?`-attribute of JSXGraph to `on`

## Usage

1. In a Moodle course you can add an board to different types of content, i.e.:
   - `Add an activity or resource -> Page`
   - `Add an activity or resource -> Link`
   - `Add an activity or resource -> Quiz`
   - ...
2. Write content. At the position the construction should appear, create a construction by:
	* switching to the code input, i.e. to "HTML source editor"
	* inserting a `<jsxgraph>` tag with all required parameters
	* Each <code><div\></code> that contains a JSXGraph board needs a unique ID on the page. You can specify this ID within the tag (see [here](#jsxgraph-tag-attributes)). Otherwise an ID is generated automatically. Reference it within the JavaScript using the constant <code>BOARDID</code>.
   
   Examples: 

   ```html
   <jsxgraph width="600" height="500">
       var brd = JXG.JSXGraph.initBoard('box0', {boundingbox:[-5,5,5,-5], axis:true});
       var p = brd.create('point', [1,2]);
   </jsxgraph>
   
   <jsxgraph width="600" height="500">
       var brd = JXG.JSXGraph.initBoard(BOARDID, {boundingbox:[-5,5,5,-5], axis:true});
       var p = brd.create('point', [1,2]);
   </jsxgraph>
   ```
   
***For tag attributes and global settings have a look at [Attributes and settings](#attributes-and-settings) in this documentation.*** 
 
Be aware of the fact, that you don't see the construction unless you leave the editor and save your document.
On reopening it later, you will notice the code rather than the `<jsxgraph>` tag. To edit your content later, again switch to the code input. 

Using JSXGraph in quiz questions needs a workaround: <br>
When adding or editing a question, insert the `<jsxgraph>` tag into the `Question tag`-input and choose "HTML format".

### JSXGraph and formulas - a filter extension

To use an JSXGraph board in a formulas question you can use <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph" target="_blank">our filter extension for formulas</a>. Please note the <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph/blob/master/README.md" target="_blank">documentation</a> of this extension, especially the installation instructions.

### JSXGraph and STACK 

This filter is not necessary to use JSXGraph with [STACK](https://moodle.org/plugins/qtype_stack). STACK has its own extension for JSXGraph.
Please refer 
[STACK Documentation](https://stack2.maths.ed.ac.uk/demo2018/question/type/stack/doc/doc.php/Authoring/JSXGraph.md) and
[GitHub](https://github.com/maths/moodle-qtype_stack/blob/master/doc/en/Authoring/JSXGraph.md).

<i>Note that this STACK extension is not developed or managed by the JSXGraph developing team.</i>


## Attributes and settings
### Admin settings

As moodle administrator, you can make the following settings:
<table>
    <tr>
        <th>JSXGraph from server</th>
        <td>You can decide whether the used JSXGraph core is loaded from server or if the filter uses the locally provided one supplied with the plugin.</td>
    </tr>
    <tr>
        <th>server version</th>
        <td>Type the version number, which should be loaded, when <code>JSXGraph from server</code> is checked.</td>
    </tr>
    <tr>
        <th>extension for question type <a href="https://moodle.org/plugins/qtype_formulas" target="_blank">formulas</a></th>
        <td>Here you can determine whether the external library for <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph" target="_blank">using JSXGraph in fomulas questions</a> is loaded or not.<br>If you want to use the library in individual tags (and global setting says "deactivated"), set the corresponding attribute to true.</td>
    </tr>
    <tr>
        <th>HTML entities</th>
        <td>If this setting is set to <code>true</code>, HTMLentities like "&", "<", etc. are supported within the JavaScript code for JSXGraph.</td>
    </tr>
    <tr>
        <th>convert encoding</th>
        <td>Decide wether the encoding of the text between the JSXGraph tags should be converted to UTF-8 or not.</td>
    </tr>
    <tr>
        <th>global JavaScript</th>
        <td>In this textbox you can type a general JavaScript code to be loaded before loading specific tag code.</td>
    </tr>
    <tr>
        <th>width and height</th>
        <td>Default dimensions of JSXGraph container. Is used if no information is given in the tag.</td>
    </tr>
    <tr>
        <th>divid</th>
        <td><b>Deprecated</b><br><small>Prefix for the automatically generated divid of every JSX construction.</small></td>
    </tr>
</table>

### `<jsxgraph>` tag attributes

Within the `<jsxgraph>` tag different attributes can be declared, e.g. `<jsxgraph width="..." height="..." entities="..." useGlobalJS="...">` 
<table>
    <tr>
        <th><code>title</code> and <code>description</code></th>
        <td>This information is used for better accessibility. Since JSXGraph version 1.2, the board attributes <code>title</code> and <code>description</code> are used to create elements for <code>aria-labelledby</code> and <code>aria-describedby</code> of the board. Title ans description are set by specification in this tag attributes.</td>
    </tr>
    <tr>
        <th><code>width</code> and <code>height</code></th>
        <td>Dimensions of JSXGraph container. Overrides the global settings locally. You can use any CSS unit here. If no unit but only an integer is specified, "px" is automatically added.</td>
    </tr>
     <tr>
        <th><code>ext_formulas</code></th>
        <td>Determine whether the external library for <a href="https://github.com/jsxgraph/moodleformulas_jsxgraph" target="_blank">using JSXGraph in fomulas questions</a> is loaded or not.<br>Possible values: <code>"true"</code>, <code>"false"</code>.</td>
    </tr>
    <tr>
        <th><code>entities</code></th>
        <td>If HTMLentities like "&", "<", etc. should be supported within the JavaScript code set the attribute to <code>"true"</code>. To override a global <code>true</code> type <code>"false"</code>.</td>
    </tr>
    <tr>
        <th><code>useGlobalJS</code></th>
        <td>Decide whether global JavaScript from admin settings should be loaded before your code.<br>Possible values: <code>"true"</code>, <code>"false"</code>.</td>
    </tr>
    <tr>
        <th><code>boardid</code> or <code>box</code></th>
        <td><b>Deprecated</b><br><small>This attribute defines, which id the graph of JSXGraph will have. Please use the id stored in the constant <code>BOARDID</code> within the JavaScript block, especially for the first parameter in <code>JXG.JSXGraph.initBoard(...)</code>. Look at the examples at <a href="#usage">Usage</a>.</small></td>
    </tr>
</table>

## Using MathJax within the board

To use the pre-installed `MathJax` notation within the board, your **Moodle admin** first has to make some settings:

1. Go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters`
2. If not already done, enable the `MathJax` filter
3. Arrange the filters so, that `MathJax` is before `JSXGraph`.
4. If the `TeX notation` filter is activated, this must be arranged below `MathJax`

After this changes **everyone** can use `MathJax` notation `$$(...)$$` within the board of JSXGraph as follows:

- Instead of using ` \ ` between `<jsxgraph>` tags you have to escape the backslash by using ` \\ ` <br>
  e.g. `\frac` --> `\\frac`
- To prevent unpredictable behavior you should set `parse: false`
- *optional:* To make the font bigger, use the `fontSize`-attribute

Look at this example:

```html
<jsxgraph width="100%" height="600">
    var brd = JXG.JSXGraph.initBoard(BOARDID, {boundingbox:[-6,6,6,-6], axis:true});
    var t = brd.create('text', [1,4, '$$( \\sqrt{1},\\frac {8}{2} )$$'],{parse: false, fixed: true, fontSize: 20});
    var s = brd.create('text', [-5,2.5, '$$( 1-6,\\sum_{n=0}^\\infty (3/5)^n )$$'], {parse: false});
</jsxgraph>
```

Using the `MathJax` filter within the board is supported in `moodle2.x` and `moodle3.x`. 

## Build Plugin (how to release a new version)

This plugin no longer needs to be explicitly build. To release a **new version of JSXGraph** into the filter follow the steps below:

- replace the file `jsxgraphcode.js` in branches `master` and `MOODLE_2`.
- if formulas extension hat been updated, replace file libs/fomulas_extension/JSXQuestion.js
- update version tag in `thirdpartylibs.xml`!
- the value of `$plugin->version` should be updated in the file `version.php` to the current date (`YYYYMMDD00`)
- Draft a new release in GitHub
- submit a new version of the filter to the [Moodle plugins directory](https://moodle.org/plugins/filter_jsxgraph) (maintained by Andreas Walter) 

## Feedback

All bugs, feature requests, feedback, etc., are welcome.

## Travis support

[![Build Status](https://travis-ci.org/jsxgraph/moodle-filter_jsxgraph.svg?branch=master)](https://travis-ci.org/jsxgraph/moodle-filter_jsxgraph)

https://travis-ci.org/jsxgraph/moodle-filter_jsxgraph

## License

http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

[![ITEMS](img/items_logo_blue.png)](https://itemspro.eu)
[![Cofunded by the Erasmus+ programme of the European union](img/eu_flag_co_funded_pos_rgb_left_small.jpg)](https://ec.europa.eu/programmes/erasmus-plus/)
