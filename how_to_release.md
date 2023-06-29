# Steps for releasing a new JSXGraph Moodle filter version

You can find instructions to build the latest version of jsxgraphcore.js here: https://github.com/jsxgraph/jsxgraph#build-jsxgraph

To release a new version of JSXGraph into the filter follow the steps below:

1. Add file `amd/build/jsxgraphcore-v_._._-lazy.js` and replace file `jsxgraph.css` in branch `main` of https://github.com/jsxgraph/moodle-filter_jsxgraph.
2. Recompile [`styles.less`](styles.less).
3. Reformat [`styles.css`](styles.css) (indentation of 4 spaces).
4. If formulas extension hat been updated, replace directory `libs/fomulas_extension`.
5. Add version tag in [`thirdpartylibs.xml`](thirdpartylibs.xml)!
6. Update string for `recommendedJSX` in [`db/upgrade.php`](db/upgrade.php).
7. Update `plugin->release` in the files [`db/upgrade.php`](db/upgrade.php) and [`version.php`](version.php).
8. The value of `plugin->version` should be updated in [`version.php`](version.php) to the current date (`YYYYMMDD00`).
9. Check in your local Moodle installation if everything works fine.
10. Draft a new release in GitHub.
11. Submit a new version of the filter to the https://moodle.org/plugins/filter_jsxgraph (maintained by Andreas Walter).
    Use the release notes from GitHub.
12. Comment under the Google Groups post of JSXGraph release: https://groups.google.com/g/jsxgraph.

For seeing travis prechecks go here: https://travis-ci.org/github/jsxgraph/moodle-filter_jsxgraph

Moodle Plugin CI: https://moodlehq.github.io/moodle-plugin-ci/