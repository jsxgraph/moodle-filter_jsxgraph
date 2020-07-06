/**
 * MIT License
 * Copyright (c) 2020 JSXGraph
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
*/
"use strict";

/**
 *
 * @param {String} elID ID of the HTML element containing JSXGraph
 * @param {Function} jsxCode JavaScript function containing the construction
 * @param {Boolean} debug  Debug flag. If false the input elements of the formulas question are hidden.
 */
var JSXQuestion = function (elID, jsxCode, debug) {
    var that = this,
        topEl;

    /**
     * HTML element containing the board
     * 
     * @type {String}
     */
    this.elm = document.getElementById(elID);

    // Get the first ancestor of the board having class ".formulaspart" (should be a div)
    topEl = this.elm.closest('.formulaspart');

    /**
     * Stores the input tags from the formulas question.
     * 
     * @type {Array}
     */
    //this.inputs = $(this.elm).closest(".formulaspart").find("input");
    this.inputs = topEl.querySelectorAll('input');

    // Hide the outcome div
    // Seems to be useless since the dot before formulaspartoutcome is missing.
    // $(this.elm).closest(".formulaspart").children("formulaspartoutcome").hide();

    // Hide the input elements
    if (debug !== true) {
        // this.inputs.hide();
        this.inputs.forEach(el => { el.style.display = 'none'; });
    }

    /**
     * Indicator if the question has been solved.
     * 
     * @type {Boolean}
     */
    if (this.inputs && this.inputs[0]) {
        this.isSolved = this.inputs[0].readOnly;
    } else {
        this.isSolved = false;
    }
    this.solved = this.isSolved;

    /**
     * Fill input element of index idx of the formulas question with value.
     * 
     * @param {Number} idx Index of the input element, starting at 0.
     * @param {Number} val Number to be set.
     */
    this.set = function (idx, val) {
        if (!that.isSolved && that.inputs && that.inputs[idx]) {
            that.inputs[idx].value = Math.round(val * 100) / 100;
        }
    };

    /**
     * Set values for all formulas inpout fields
     *
     * @param {Array} values Array containing the values to be set.
     *
     */
    this.setAllValues = function (values) {
        var idx, len = values.length;

        for (idx = 0; idx < len; idx++) {
            if (!that.isSolved && that.inputs && that.inputs[idx]) {
                that.inputs[idx].value = Math.round(values[idx] * 100) / 100;
            }
        }
    };

    /**
     * Get the content of input element of index idx of the formulas question.
     *
     * @param {Number} idx Index of the input form, starting at 0.
     */
    this.get = function (idx) {
        if (that.inputs && that.inputs[idx]) {
            var n = parseFloat(that.inputs[idx].value);
            if (isNaN(n)) {
                return null;
            }
            return n;
        }
        return null;
    };

    /**
     * Fetch all values from the formulas input fields
     *
     * @param {Number} number_of_fields Number of formulas input fields
     * @param {Number} default_value Default values if the fields are empty.
     * @returns {Array} Array of length number_of_fields containing the entries of the formulas
     * input fields.
     */
    this.getAllValues = function(number_of_fields, default_value) {
        var idx, n,
            values = [];

        for (idx = 0; idx < number_of_fields; idx++) {
            n = that.get(idx);
            if (n === null) {
                n = default_value;
            }
            values.push(n);
        }
        return values;
    };

    this.brd = null;
    this.board = null;

    // Execute the JSXGraph / JavaScript code
    jsxCode(this);

    /**
     * Reload the construction
     */
    this.reload = function () {
        jsxCode(that);
    };
};

// Polyfill for element.closest:
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector ||
                                Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;

        do {
            if (Element.prototype.matches.call(el, s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}
