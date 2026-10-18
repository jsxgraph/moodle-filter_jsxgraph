<?php
// This file is part of Moodle - http://moodle.org/
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

namespace filter_jsxgraph;

/**
 * Tests for the escaping of tag attributes in the generated HTML and JavaScript.
 *
 * @package    filter_jsxgraph
 * @category   test
 * @copyright  2026 Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_jsxgraph\text_filter
 */
final class text_filter_escaping_test extends \advanced_testcase {
    /**
     * Attribute values must stay data in the generated HTML and in the generated JavaScript.
     */
    public function test_attributes_are_escaped_violation(): void {
        global $PAGE;
        $this->resetAfterTest();

        $filter = new text_filter(\context_system::instance(), []);
        $output = $filter->filter(
            '<jsxgraph boardid="a&quot; onmouseover=&quot;alert(1)" title="\'; alert(1); //">/* code */</jsxgraph>'
        );

        // HTML: the quote must not close the id attribute, so no event handler attribute is created.
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($output);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $this->assertSame(0, $xpath->query('//*[@onmouseover]')->length);
        $this->assertSame(1, $xpath->query('//div[@id=\'a" onmouseover="alert(1)\']')->length);

        // JavaScript: the quote must not end the string literal, neither in the script of the board nor in the footer code.
        $this->assertStringNotContainsString('boardid = "a" onmouseover', $output);
        $footer = $PAGE->requires->get_end_code();
        $this->assertStringNotContainsString("title = ''; alert(1); //", $footer);
        $this->assertStringContainsString('JXG.Options.board.title = "\u0027; alert(1); \/\/";', $footer);
        $this->assertStringNotContainsString("const BOARDID0 = 'a\" onmouseover", $footer);
        $this->assertStringContainsString('const BOARDID0 = "a\u0022 onmouseover=\u0022alert(1)";', $footer);
    }

    /**
     * Harmless attributes still lead to a board with the given id, class and accessibility attributes.
     */
    public function test_attributes_are_escaped_verification(): void {
        global $PAGE;
        $this->resetAfterTest();

        $filter = new text_filter(\context_system::instance(), []);
        $output = $filter->filter(
            '<jsxgraph boardid="board1" class="mybox" title="Titel äöü" description="Beschreibung">/* code */</jsxgraph>'
        );

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($output);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        $this->assertSame(1, $xpath->query('//div[@id="board1" and contains(@class, "jxgbox mybox")]')->length);

        $footer = $PAGE->requires->get_end_code();
        $this->assertMatchesRegularExpression('/const BOARDID0 = ["\']board1["\'];/', $footer);
        $this->assertMatchesRegularExpression('/const BOARDIDS = \[["\']board1["\']\];/', $footer);
        $this->assertMatchesRegularExpression('/JXG\.Options\.board\.description = ["\']Beschreibung["\'];/', $footer);
        $this->assertMatchesRegularExpression('/document\.getElementById\(["\']board1["\']\) != null/', $footer);
    }
}
