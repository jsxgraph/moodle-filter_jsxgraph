# Moodle JSXGraph plugin for Moodle 1.9

**_Caution: <br>The current version has to be regarded as a pre alpha development version. Use with caution!_**

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

## Installation: (by file server admin)
1. Upload all files of this branch into the directory `moodle -> filter` of your Moodle installation
2. In Moodle, navigate to `Moodle -> Administration -> Configuration -> Filter` and click on the entry `jsxgraph` to activate the filter

## Usage

1. In a Moodle course: -> Add a resource -> Compose a website
2. Write content. At the position the construction should appear, create a construction by:
	* switching to the code input
	* inserting a <jsxgraph>-tag with all required parameters

Be aware of the fact, that you dont't see the construction unless you leave the editor and save your document.
On reopening it later, you will notice the code rather than the jsxgraph-tag. 

## Feedback

All bugs, feature requests, feedback, etc., are welcome.

## License

http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


