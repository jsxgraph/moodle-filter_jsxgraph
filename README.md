# Moodle JSXGraph plugin

### About JSXGraph

JSXGraph is a cross-browser JavaScript library for interactive geometry, function plotting, charting, and data visualization in the web browser.
JSXGraph is implemented in pure JavaScript and does not rely on any other library. Special care has been taken to optimize the performance.

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

To install the filter for moodle2 or moodle2.9+ you can follow the steps below:

1. Download the ZIP-compressed directory [`install_jsxgraph_plugin_moodle2.9+.zip`](moodle2.9+/install/install_jsxgraph_plugin_moodle2.9+.zip) or [`install_jsxgraph_plugin_moodle2.zip`](moodle2/install/install_jsxgraph_plugin_moodle2.zip) from respective `install` subdirectory<br>
   **Do not unpack the ZIP directory!**
2. In Moodle, navigate to `Site administration -> Plugins -> Install plugins`
3. Under `Install plugin from ZIP file`, drag and drop the downloaded ZIP directory into input field und click on `Show more...`
4. Choose the plugin type `Text filter (filter)`
5. Rename the root directory to `jsxgraph` by filling the input (be sure to write correctly)
6. Click on `Install plugin from ZIP the file` and follow the instructions
7. After installing go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters` and switch the `Active?`-attribute of JSXGraph to `on`

### Installation in Moodle directory (by file server admin)

Otherwise, you can also install the filter with the following steps:

1. Download the whole [`moodle2.9+`](moodle2.9+/) or [`moodle2`](moodle2/) folder
2. Delete `install` directory therein
3. Upload the rest of the plugin folder into the directory `moodle -> filter` of your Moodle installation
4. Rename the folder to `jsxgraph` (be sure to write correctly)
5. Open site root of your Moodle installation and follow the steps to install plugin 
6. After installing go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters` and switch the `Active?`-attribute of JSXGraph to `on`

### Installation for Moodle 1.9: (by file server admin)

1. Upload the complete plugin folder `moodle1.9` into the directory `moodle -> filter` of your Moodle installation
2. Follow the instructions from [`README.md` therein](moodle1.9/README.md).

## Usage

1. In a Moodle course you can add an board to different types of content, i.e.:
   - `Add an activity or resource -> Page`
   - `Add an activity or resource -> Link`
   - `Add an activity or resource -> Quiz`
   - ...
2. Write content. At the position the construction should appear, create a construction by:
	* switching to the code input, i.e. to "HTML source editor"
	* inserting a `<jsxgraph>` tag with all required parameters
   
   Examples: 

   ```html
   <jsxgraph width="600" height="500">
       (function() {
           var brd = JXG.JSXGraph.initBoard('box0', {boundingbox:[-5,5,5,-5], axis:true});
           var p = brd.create('point', [1,2]);
       })();
   </jsxgraph>
    
   <jsxgraph width="600" height="500" box="mybox">
       (function() {
           var brd = JXG.JSXGraph.initBoard('mybox', {boundingbox:[-5,5,5,-5], axis:true});
           var p = brd.create('point', [1,2]);
       })();
   </jsxgraph>
   ```
   
***For tag attributes and global settings have a look at [Attributes and settings](#attributes-and-settings) in this documentation.*** 
 
Be aware of the fact, that you don't see the construction unless you leave the editor and save your document.
On reopening it later, you will notice the code rather than the `<jsxgraph>` tag. To edit your content later, again switch to the code input. 

Using JSXGraph in quiz questions needs a workaround: <br>
When adding or editing a question, insert the `<jsxgraph>` tag into the `Question tag`-input and choose "HTML format".

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
        <th>HTMLentities</th>
        <td>If this setting is set to <code>true</code>, HTMLentities like "&", "<", etc. are supported within the JavaScript code for JSXGraph.</td>
    </tr>
    <tr>
        <th>Global JavaScript</th>
        <td>In this textbox you can type a general JavaScript code to be loaded before loading specific tag code.</td>
    </tr>
    <tr>
        <th>div id</th>
        <td>ID of the division containing JSXGraph.</td>
    </tr>
    <tr>
        <th>width and height</th>
        <td>Dimensions of JSXGraph container.</td>
    </tr>
</table>

### `<jsxgraph>` tag attributes

Within the `<jsxgraph>` tag different attributes can be declared: `<jsxgraph width="..." height="..." entities="..." useGlobalJS="..." box="..." board="...">` 
<table>
    <tr>
        <th>width and height</th>
        <td>Dimensions of JSXGraph container. Overrides the global settings locally. Type only Integers without "px".</td>
    </tr>
    <tr>
        <th>entities</th>
        <td>If HTMLentities like "&", "<", etc. should be supported within the JavaScript code set the attribute to <code>"true"</code>. To override a global <code>true</code> type <code>"false"</code>.</td>
    </tr>
    <tr>
        <th>useGlobalJS</th>
        <td>Decide whether global JavaScript from admin settings should be loaded before your code. Possible values: <code>"true"</code>, <code>"false"</code>.</td>
    </tr>
    <tr>
        <th>box</th>
        <td>This attribute defines, which id the graph of JSXGraph will have. It has to be equal to the first parameter in <code>JXG.JSXGraph.initBoard(...)</code>. Look at the second example at <a href="#usage">Usage</a>.</td>
    </tr>
</table>

## Using MathJax within the board

To use the pre-installed `MathJax` notation within the board, your **Moodle admin** first has to make some settings:

1. Go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters`
2. If not already done, enable the `MathJax` filter
3. Arrange the filters so, that `MathJax` is before `JSXGraph`.
4. If the `TeX notation` filter is activated, this must be arranged below `MathJax`

After this changes **everyone** can use `MathJax` notation `$$(...)$$` within the board of JSXGraph as follows:

- Instead of using ` \ ` between `<jsxgraph>`-tags you have to escape the backslash by using ` \\ ` <br>
  e.g. `\frac` --> `\\frac`
- To prevent unpredictable behavior you should set `parse: false`
- *optional:* To make the font bigger, use the `fontSize`-attribute

Look at this example:

```html
<jsxgraph width="600" height="600" box="box">
    (function() {
        var brd = JXG.JSXGraph.initBoard('box', {boundingbox:[-6,6,6,-6], axis:true});
        var t = brd.create('text', [1,4, '$$( \\sqrt{1},\\frac {8}{2} )$$'],{parse: false, fixed: true, fontSize: 20});
        var s = brd.create('text', [-5,2.5, '$$( 1-6,\\sum_{n=0}^\\infty (3/5)^n )$$'], {parse: false});
    })();
</jsxgraph>
```

Using the `MathJax` filter within the board is supported in `moodle2.x` and `moodle3.x`. 

## Build Plugin

To build JSXGraph Moodle Plugin run

    $ make server

in the plugin root directory. This will download the newest JSXGraph core from [http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js](http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js) and create the ZIP-compressed directories for moodle2 and moodle2.9+.

You also can use

    $ make local

if you pulled the whole [JSXGraph project](https://github.com/jsxgraph) from GitHub. Then the newest JSXGraph core will be copied from `../jsxgraph/build/`.

## Feedback

All bugs, feature requests, feedback, etc., are welcome.

## License

http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


