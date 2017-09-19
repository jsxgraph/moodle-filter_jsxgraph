# moodle-jsxgraph-plugin

This is a plug-in for [moodle](//moodle.org) to enable function plotting and dynamic geometry constructions 
with [JSXGraph](http://jsxgraph.org).
Using the [JSXGraph](http://jsxgraph.org) filter makes it a lot easier to embed [JSXGraph](http://jsxgraph.org) constructions into moodle online documents.

## Installation
### Installation for moolde2.9 and moodle3+: (by Moodle Admin)
To install the filter in moodle2.9 and moodle3+ you can follow the steps in the "Installation for moodle 2.x" section in this documentation. Make sure downloading the whole `moodle2.9_and_3+` folder and delete folder `install` therein.
 
Alternatively, you can follow the steps below:

1. Download the ZIP-compressed directory [`install_jsxgraph_plugin.zip`](moodle2.9_and_3+/install/install_jsxgraph_plugin.zip) from `moodle2.9_and_3+/install`<br>
   **Do not unpack `install_jsxgraph_plugin.zip`!**
2. In moodle, navigate to `Moodle -> Site administration -> Plugins -> Plugins -> Install plugins`
3. Under `Install plugin from ZIP file`, drag and drop `install_jsxgraph_plugin.zip` und click on `Show more...`
4. Choose the plugin type `Text filter (filter)`
5. Rename the root directory to `jsxgraph` by filling the input (be sure to write correctly)
6. Click on `Install plugin from ZIP the file` and follow the instructions
7. After installing go to `Moodle -> Site administration -> Plugins -> Filters -> Manage filters` and switch the `Active?`-attribute of JSXGraph to `on`

**To use MathJax, please refer to the "MathJax" section in this documentation**

### Installation for moodle 2.x: (by Moodle Admin)
1. Upload the complete plug-in folder `moodle2` into the folder `moodle-->filter`
2. Rename the folder to `jsxgraph`
3. Open site root of your moodle installation and follow the steps to install plugin 
3. In moodle, navigate to `Moodle -> Administration -> Configuration -> "Filter"` and click on the entry
   `jsxgraph` to activate the filter
   
**To use MathJax, please refer to the "MathJax" section in this documentation**

### Installation for moodle 1.9: (by Moodle Admin)
1. Upload the complete plug-in folder `moodle1.9` into the folder `moodle-->filter`
2. Follow the instructions from [`README.md` therein](moodle1.9/README.md).

## Usage
1. In a moodle course you can add an board to different types of content, i.e.:
   - `Add an activity or resource -> Page`
   - `Add an activity or resource -> Link`
   - `Add an activity or resource -> Quiz`
   - ...
2. Write content. At the position the construction should appear, create a construction by:
	* switching to the code input, i.e. to "HTML source editor"
	* inserting a `<jsxgraph>`-tag with all required parameters
    * Examples: 

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
3. To use HTMLentities within the Javascript code for JSXGraph, add the attribute htmlentities="1" to the <jsxgraph>-tag. (Global setting through Moodle Admin) 
 
Be aware of the fact, that you don't see the construction unless you leave the editor and save your document.
On reopening it later, you will notice the code rather than the jsxgraph-tag. To edit your content later, again switch to the code input. 

Using JSXGraph in quiz questions needs a workaround: <br>
When adding or editing a question, insert the jsxgraph tag into the `Question tag`-input and choose "HTML format".

## Using MathJax within the board
To use the pre-installed `MathJax` notation within the board, the **Moodle Admin** must first make some settings:

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

To build JSXGraph-Moodle-Plugin run

    $ make server

in the plugin root directory. This will download the newest JSXGraph-Core from [http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js](http://jsxgraph.uni-bayreuth.de/distrib/jsxgraphcore.js) and create  the ZIP-compressed directories for moodle2 and moodle3+.

You also can use

    $ make local

if pou pulled the whole [jsxgraph project](https://github.com/jsxgraph) from GitHub. Then the newest JSXGraph-Core will be copied from `../jsxgraph/build/`.

## Feedback

All bugs, feature requests, feedback, etc., are welcome.

## License

http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


