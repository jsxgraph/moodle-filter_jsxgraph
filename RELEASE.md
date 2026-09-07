# Steps for releasing a new JSXGraph Moodle filter version

You can find instructions to build the latest version of jsxgraphcore.js here: https://github.com/jsxgraph/jsxgraph#build-jsxgraph

To release a new version of JSXGraph into the filter follow the steps below:

1. Add file `amd/build/jsxgraphcore-v_._._-lazy.js` and replace file [`styles/jsxgraph.css`](styles/jsxgraph.css).
    - Add version tag in [`thirdpartylibs.xml`](thirdpartylibs.xml)!
    - Update `$version` array and string for `$recommendedjsx` 
      in [`db/install.php`](db/install.php) and [`db/upgrade.php`](db/upgrade.php).
2. Recompile [`styles.less`](styles.less).
3. Reformat [`styles.css`](styles.css) (indentation of 4 spaces).
4. If formulas extension hat been updated, replace directory `libs/fomulas_extension`.
5. Update `release` in the files
   [`version.php`](version.php), [`db/install.php`](db/install.php) and [`db/upgrade.php`](db/upgrade.php).
6. The value of `plugin->version` should be updated in [`version.php`](version.php) to the current date (`YYYYMMDD00`).
7. Check in your local Moodle installation if everything works fine.
8. Draft a new release in GitHub.
9. Submit a new version of the filter to the https://marketplace.moodle.com/plugins/1762 (maintained by Andreas Walter).

Moodle Plugin CI: https://moodlehq.github.io/moodle-plugin-ci/ (?)