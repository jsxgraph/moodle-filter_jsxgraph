# Description of JSXGraph import into moodle

You can find instructions to build the latest version of jsxgraphcore.js here: https://github.com/jsxgraph/jsxgraph#build-jsxgraph

To release a new version of JSXGraph into the filter follow the steps below:

- replace the file `jsxgraphcore.js` in branches `master` and `MOODLE_2` of https://github.com/jsxgraph/moodle-filter_jsxgraph.
- if formulas extension hat been updated, replace file libs/fomulas_extension/JSXQuestion.js
- update version tag in `thirdpartylibs.xml`!
- update strings for `recommendedJSX` and `deliveredJSX` in `version.php`
- the value of `plugin->version` should be updated in the file `version.php` to the current date (`YYYYMMDD00`)
- update `plugin->release` in the file `version.php`
- draft a new release in GitHub
- submit a new version of the filter to the https://moodle.org/plugins/filter_jsxgraph (maintained by Andreas Walter)

For seeing travis prechecks go here: https://travis-ci.com/

Moodle Plugin CI: https://blackboard-open-source.github.io/moodle-plugin-ci/