# Steps for releasing a new JSXGraph Moodle filter version

You can find instructions to build the latest version of jsxgraphcore.js here: https://github.com/jsxgraph/jsxgraph#build-jsxgraph

To release a new version of JSXGraph into the filter follow the steps below:

- Replace the files `jsxgraphcore.js` and `jsxgraph.css` in branch `master` of https://github.com/jsxgraph/moodle-filter_jsxgraph.
- Recompile [`styles.less`](styles.less).
- Reformat [`styles.css`](styles.css) (indentation of 4 spaces).
- If formulas extension hat been updated, replace directory `libs/fomulas_extension`.
- Update version tag in [`thirdpartylibs.xml`](thirdpartylibs.xml)!
- Update strings for `recommendedJSX` and `deliveredJSX` in [`db/upgrade.php`](db/upgrade.php).
- Update `plugin->release` in the files [`db/upgrade.php`](db/upgrade.php) and [`version.php`](version.php).
- The value of `plugin->version` should be updated in [`version.php`](version.php) to the current date (`YYYYMMDD00`).
- Check in your local Moodle installation if everything works fine.
- Draft a new release in GitHub.
- Submit a new version of the filter to the https://moodle.org/plugins/filter_jsxgraph (maintained by Andreas Walter). 
  Use the release notes from GitHub. 
- Comment under the Google Groups post of JSXGraph release: https://groups.google.com/g/jsxgraph.

For seeing travis prechecks go here: https://travis-ci.org/github/jsxgraph/moodle-filter_jsxgraph

Moodle Plugin CI: https://moodlehq.github.io/moodle-plugin-ci/