Description of JSXGraph import into moodle

You can find instructions to build the latest version of jsxgraphcore.js here: https://github.com/jsxgraph/jsxgraph#build-jsxgraph

To release a new version of JSXGraph into the filter follow the steps below:
- replace the file `jsxgraphcode.js` in branches `master` and `MOODLE_2` of https://github.com/jsxgraph/moodle-filter_jsxgraph.
- update version tag in `thirdpartylibs.xml`!
- the value of `$plugin->version` should be updated in the file `version.php` to the current date (`YYYYMMDD00`)

For seeing travis prechecks go here: https://travis-ci.org/