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
 * Tests for the handling of closing script tags and HTML entities in the code of a tag.
 *
 * @package    filter_jsxgraph
 * @category   test
 * @copyright  2026 Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_jsxgraph\text_filter
 */
final class text_filter_script_end_test extends \advanced_testcase {
    /**
     * Filters the given text and returns the JavaScript code, which has been added to the page footer.
     *
     * @param string $text
     * @return string
     */
    private function get_footer_code(string $text): string {
        global $PAGE;

        $filter = new text_filter(\context_system::instance(), []);
        $filter->filter($text);
        return $PAGE->requires->get_end_code();
    }

    /**
     * Closing script tags must not survive the decoding of HTML entities and the opt-out for entities must work.
     */
    public function test_closing_script_tag_is_neutralised_violation(): void {
        $this->resetAfterTest();

        $footer = $this->get_footer_code('<jsxgraph>var x = 1;&lt;/script&gt;&lt;img src=x onerror=alert(1)&gt;//</jsxgraph>');
        $this->assertStringNotContainsString('</script><img src=x', $footer);
        $this->assertStringContainsString('<\/script><img src=x', $footer);

        $footer = $this->get_footer_code('<jsxgraph>var y = 1;&lt;/SCRIPT &gt;&lt;img src=y onerror=alert(1)&gt;//</jsxgraph>');
        $this->assertStringNotContainsString('</SCRIPT ><img src=y', $footer);
        $this->assertStringContainsString('<\/SCRIPT ><img src=y', $footer);

        // With entities="false" the code is not decoded at all.
        $footer = $this->get_footer_code('<jsxgraph entities="false">var z = 1;&lt;/script&gt;//</jsxgraph>');
        $this->assertStringNotContainsString('</script>//', $footer);
        $this->assertStringContainsString('var z = 1;&lt;/script&gt;//', $footer);
    }

    /**
     * HTML entities in the code are still decoded by default.
     */
    public function test_closing_script_tag_is_neutralised_verification(): void {
        $this->resetAfterTest();

        $footer = $this->get_footer_code('<jsxgraph>if (a &lt; 3 &amp;&amp; b) { var s = "x"; }</jsxgraph>');
        $this->assertStringContainsString('if (a < 3 && b) { var s = "x"; }', $footer);
    }
}
