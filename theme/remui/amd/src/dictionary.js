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

/**
 * @module     theme_remui/dictionary
 * @copyright (c) 2023 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

'use strict';
define(['jquery', 'core/notification'], function($, Notification) {
    /**
     * Function getSelected data will give the selected text
     * @return {String} Selection string
     */
    function getSelectedText() {
        if (window.getSelection) {
            return window.getSelection().toString();
        } else if (document.selection) {
            return document.selection.createRange().text;
        }
        return '';
    }

    /**
     * Ajax to fetch the word meaning
     * @param  {Number} x      screen x cordinate
     * @param  {Number} y      screen y cordinate
     * @param  {String} token  token name
     * @param  {String} search Search query
     */
    function getWord(x, y, token, search) {
        var searchword = token + '=' + search;
        var url = "https://api.pearson.com/v2/dictionaries/entries?" + searchword;
        var settings = {
            "async": true,
            "crossDomain": true,
            "url": url,
            "method": "GET",
            "success": function(response) {

                if (response !== '' && response !== null) {
                    $("#dictionarypopup").remove();

                    var meaning = 'No definition found.';
                    var i = 0;
                    for (i = 0; i < response.results.length; i++) {
                        if (response.results[i].senses[0].definition !== null) {
                            meaning = response.results[i].senses[0].definition;
                            break;
                        }
                    }
                    var data = "<div id='dictionarypopup' style='position:fixed; cursor:pointer;left:" + x +
                    ";top:" + y + ";z-index:9999;'>";
                    data += '<div class="popover bs-popover-bottom "><div class="arrow"></div>';
                    data += '<h3 class="popover-header">' + search + '</h3>';
                    data += '<div class="popover-body"><p>' + meaning + '</p></div></div>';
                    data += '</div>';
                    $("body").append(data);

                }
            },
            "failure": Notification.exception
        };
        $.ajax(settings);
    }

    /**
     * Get location of selected text
     * @return {Object}     Mouse click cordinates
     */
    function getSelectionCoords() {
        var doc = window.document;
        var sel = doc.selection,
            range, rects, rect;
        var x = 0,
            y = 0;
        if (sel) {
            if (sel.toString().trim() == "" || sel.toString().trim() == "undefined") {
                return false;
            }
            if (sel.type != "Control" && sel.toString != "") {
                range = sel.createRange();
                range.collapse(true);
                x = range.boundingLeft;
                y = range.boundingTop;
            }
        } else if (window.getSelection) {
            sel = window.getSelection();
            if (sel.toString().trim() == "" || sel.toString().trim() == "undefined") {
                return false;
            }
            if (sel.rangeCount && sel.toString() != "") {
                range = sel.getRangeAt(0).cloneRange();
                if (range.getClientRects) {
                    range.collapse(true);
                    rects = range.getClientRects();
                    if (rects.length > 0) {
                        rect = rects[0];
                    }
                    x = rect.left;
                    y = rect.top;
                }
                // Fall back to inserting a temporary element.
                if (x == 0 && y == 0) {
                    var span = doc.createElement("span");
                    if (span.getClientRects) {
                        // Ensure span has dimensions and position by adding a zero-width space character.
                        span.appendChild(doc.createTextNode("\u200b"));
                        range.insertNode(span);
                        rect = span.getClientRects()[0];
                        x = rect.left;
                        y = rect.top;
                        var spanParent = span.parentNode;
                        spanParent.removeChild(span);

                        // Glue any broken text nodes back together.
                        spanParent.normalize();
                    }
                }
            }
        }
        return {
            x: x,
            y: y
        };
    }

    /**
     * Initialise events
     */
    function initialise() {
        // Event trigger for text selection.
        $('body').mouseup(function() {
            var search = getSelectedText();
            if (search.trim() == "") {
                return;
            }
            var obj = getSelectionCoords();
            if (obj.x != 'undefined' && obj.y != 'undefined') {
                // Old code var x = obj.x-152; end.
                var x = obj.x;
                x += 'px';
                // Old code var y = obj.y+12; end.
                var y = obj.y + 17;
                y += 'px';
                getWord(x, y, 'headword', search);
            }
        });

        // Close the Tooltip Event.
        $(window).scroll(function() {
            $("#dictionarypopup").remove();
        });

        $('body').click(function() {
            $("#dictionarypopup").remove();
        });
    }

    return {
        init: initialise
    };
});
