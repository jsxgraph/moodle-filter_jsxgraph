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
'use strict';

var JXG = JXG || {};
window.JXG = JXG;

/**
 * @param {String} boardID ID of the HTML element containing the JSXGraph board. Has to be set with local const BOARDID.
 * @param {Function} jsxGraphCode JavaScript function containing the construction code.
 * @param {Boolean} [allowInputEntry=false] Should the original inputs from formulas be displayed and linked to the construction?
 * @param {Number} [decimalPrecision=2] Number of digits to round to.
 */
var JSXQuestion = function(boardID, jsxGraphCode, allowInputEntry, decimalPrecision) {
    var that = this,
        topEl;

    if (allowInputEntry === undefined || allowInputEntry === null) {
        allowInputEntry = false;
    }
    if (decimalPrecision === undefined || decimalPrecision === null) {
        decimalPrecision = 2;
    }

    /**
     * ID of the board.
     * @type {String}
     */
    this.BOARDID = boardID;

    // Get the first ancestor of the board having class ".formulaspart" (should be a div).
    // ATTENTION!!! The class used here depends on formulas and can make the extension useless when updating formulas!
    topEl = document.getElementById(this.BOARDID).closest('.formulaspart');

    /**
     * Stores the input tags from the formulas question.
     * @type {Array}
     */
    this.inputs = topEl.querySelectorAll('input');

    // Hide the input elements
    if (allowInputEntry) {
        this.inputs.forEach(function(el) {
            el.addEventListener('input', function() {
                that.update();
            });
        });
        this.inputs.forEach(function(el) {
            el.addEventListener('change', function() {
                that.update();
            });
        });
    } else {
        this.inputs.forEach(function(el) {
            el.style.display = 'none';
        });
    }

    /**
     * Stored JSXGraph board.
     * @type {JXG.Board}
     */
    this.board = null;
    /**
     * @deprecated
     * @type {JXG.Board}
     */
    this.brd = null;

    /**
     * Initializes the board, saves it in the attributes of JSXQuestion and returns the board.
     *
     * @param {Object} [attributes={}] Attributes for function JXG.JSXGraph.initBoard(...).
     * @param {Object} [attributesIfBoxIsGiven={}] Guarantees backward compatibility with the function JXG.JSXGraph.initBoard(...).
     *                                             The ID that was then passed in the first parameter is ignored!
     *
     * @returns {JXG.Board}                       JSXGraph board
     */
    this.initBoard = function(attributes, attributesIfBoxIsGiven) {
        var board;

        if (attributes === undefined || attributes === null) {
            attributes = {};
        }
        if (attributesIfBoxIsGiven === undefined || attributesIfBoxIsGiven === null) {
            attributesIfBoxIsGiven = {};
        }

        if (typeof attributes === 'string' || attributes instanceof String) {
            attributes = attributesIfBoxIsGiven;
        }

        board = JXG.JSXGraph.initBoard(that.BOARDID, attributes);
        that.brd = board;
        that.board = board;

        return board;
    };

    /**
     * Links the board to the inputs. If a change has been made in the board,
     * the input with the number inputNumber is assigned the value that the function valueFunction returns.
     *
     * @param {Number} inputNumber
     * @param {Function} valueFunction
     */
    this.bindInput = function(inputNumber, valueFunction) {
        that.board.on('update', function() {
            that.set(inputNumber, valueFunction());
        });
        that.board.update();
    };

    /**
     * Indicator if the question has been solved.
     * @type {Boolean}
     */
    this.isSolved = false;
    if (this.inputs && this.inputs[0]) {
        this.isSolved = this.inputs[0].readOnly;
    }
    /**
     * @deprecated
     * @type {Boolean}
     */
    this.solved = this.isSolved;

    /**
     * Fill input element of index inputNumber of the formulas question with value.
     *
     * @param {Number} inputNumber Index of the input element, starting at 0.
     * @param {Number} value  Number to be set.
     */
    this.set = function(inputNumber, value) {
        if (!that.isSolved && that.inputs && that.inputs[inputNumber]) {
            that.inputs[inputNumber].value = Math.round(value * Math.pow(10, decimalPrecision)) / Math.pow(10, decimalPrecision);
        }
    };

    /**
     * Set values for all formulas input fields
     *
     * @param {Array} values Array containing the numbers to be set.
     */
    this.setAllValues = function(values) {
        var inputNumber,
            len = values.length;

        for (inputNumber = 0; inputNumber < len; inputNumber++) {
            that.set(inputNumber, values[inputNumber]);
        }
    };

    /**
     * Get the content of input element of index inputNumber of the formulas question.
     *
     * @param {Number} inputNumber Index of the input form, starting at 0.
     * @param {Number} [defaultValue=0] Number that is returned if the value of the input could not be read or is not a number.
     *
     * @returns {Number} Entry of the formulas input field.
     */
    this.get = function(inputNumber, defaultValue) {
        var n;

        if (defaultValue === undefined || defaultValue === null) {
            defaultValue = 0;
        }

        if (that.inputs && that.inputs[inputNumber]) {
            n = parseFloat(that.inputs[inputNumber].value);
            if (!isNaN(n)) {
                return Math.round(n * Math.pow(10, decimalPrecision)) / Math.pow(10, decimalPrecision);
            }
        }
        return defaultValue;
    };

    /**
     * Fetch all values from the formulas input fields.
     *
     * @param {Number|Array} [defaultValues=0] Default values if the fields are empty.
     *
     * @returns {Array} Array containing the entries of all associated formulas input fields.
     */
    this.getAllValues = function(defaultValues) {
        var inputNumber,
            len = that.inputs.length,
            values = [],
            defaultValue;

        if (defaultValues === undefined || defaultValues === null) {
            defaultValues = 0;
        }

        if (Array.isArray(defaultValues)) {
            if (defaultValues.length !== len) {
                return null;
            }
        } else {
            if (isNaN(defaultValues)) {
                return null;
            } else {
                defaultValue = defaultValues;
            }
        }

        for (inputNumber = 0; inputNumber < len; inputNumber++) {
            values.push(
                that.get(inputNumber, defaultValue || defaultValues[inputNumber])
            );
        }
        return values;
    };

    /**
     * Reload the construction.
     */
    this.reload = this.update = function() {
        jsxGraphCode(that);
    };

    // Execute the JSXGraph JavaScript code.
    jsxGraphCode(this);
};

JSXQuestion.toString = function() {
    return JSXQuestion.name;
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
            if (Element.prototype.matches.call(el, s)) {
                return el;
            }
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}
