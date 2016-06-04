/**
 * This plugin is got from https://www.sencha.com/forum/showthread.php?242382-Input-mask-plugin-jquery-port
 */
Ext.define('Ext.ux.form.field.plugin.InputMask', {
    extend: 'Ext.AbstractPlugin',
    alias: 'plugin.inputmask',

    mixins: {
        observable: 'Ext.util.Observable'
    },

    constructor: function(format, settings) {
        var me = this;

        var config = {
            pasteEventName: (Ext.isIE ? 'paste' : 'input'),
            iPhone: (window.orientation != undefined),
            mask: {
                definitions: {
                    '9': "[0-9]",
                    'a': "[A-Za-z]",
                    '*': "[A-Za-z0-9]"
                },
                dataName: "rawMaskFn"
            },
            format: format,
            settings: settings || {}
        };

        me.addEvents('unmask');

        me.callParent([config]);
        me.mixins.observable.constructor.call(me);
    },

    jqmap: function(array, callback) {
        var result = [];
        Ext.each(array, function(item, index) {
            var intermediate = callback(index, item);
            if (intermediate) {
                if (Ext.isArray(intermediate)) {
                    for (var i = 0; i < intermediate.length; i += 1) {
                        this.push(intermediate[i]);
                    }
                }
                else {
                    this.push(intermediate);
                }
            }
        }, result);
        return result;
    },

    init: function(field) {
        var me = this;
        me.field = field;
        field.enableKeyEvents = true;

        var mask = me.format;

        if (!mask && this.length > 0) {
            var input = this[0];
            return input[field.mask.dataName]();
        }
        me.settings = Ext.applyIf(me.settings, {
            placeholder: "_",
            completed: null
        });

        me.defs = me.mask.definitions;
        me.tests = [];
        me.partialPosition = mask.length;
        me.firstNonMaskPos = null;

        Ext.each(mask.split(""), function(c, i) {
            if (c == '?') {
                me.format.length--;
                me.partialPosition = i;
            } else if (me.defs[c]) {
                me.tests.push(new RegExp(me.defs[c]));
                if (me.firstNonMaskPos == null)
                    me.firstNonMaskPos = me.tests.length - 1;
            } else {
                me.tests.push(null);
            }
        });

        me.fireEvent("unmask");

        var input = me.field;

        me.buffer = me.jqmap(mask.split(""), function(i, c) {
            if (c != '?') {
                return me.defs[c] ? me.settings.placeholder : c;
            }
        });
        me.focusText = input.getRawValue();

        input[me.mask.dataName] = function() {
            return me.jqmap(buffer, function(i, c) {
                return tests[i] && c != settings.placeholder ? c : null;
            }).join('');
        };

        if (!input.readOnly) {

            input.on('unmask', function() {
                this.un('.mask');
                this[plugin.mask.dataName] = undefined;
            }, input, { single: true });

            input.on('focus', me.onFocus, me);

            input.on('blur', function(f) {
                me.checkVal();
                if (f.getRawValue() != me.focusText) {
                    f.fireEvent('change', f.getRawValue(), me.focusText);
                    //was f.change();
                }
            });

            input.on('keydown', function(f, e) {
                if (me.keydownEvent(f, e) == false) {
                    e.preventDefault();
                }
            }, me);
            input.on('keypress', function(f, e) {
                if (me.keypressEvent(f, e) == false) {
                    e.preventDefault();
                }
            }, me);

            input.on('render', function(f) {
                input.inputEl.on(me.pasteEventName, function() {
                    setTimeout(function() { input.caret(me.checkVal(true)); }, 0);
                });
            }, undefined, { single: true });
        }

        me.checkVal(); //Perform initial check for existing values

        input.caret = me.caret;


        me.oriGet = Ext.Function.bind(field.getValue, field);
    },

    caret: function(begin, end) {
        if (this.length == 0) return;
        var el = Ext.get(this.inputId);
        if (typeof begin == 'number') {
            end = (typeof end == 'number') ? end : begin;
            if (el.dom.setSelectionRange) {
                el.dom.setSelectionRange(begin, end);
            } else if (el.dom.createTextRange) {
                var range = el.dom.createTextRange();
                range.collapse(true);
                range.moveEnd('character', end);
                range.moveStart('character', begin);
                range.select();
            }
            return this;
        } else {
            if (el.dom.setSelectionRange) {
                begin = el.dom.selectionStart;
                end = el.dom.selectionEnd;
            } else if (document.selection && document.selection.createRange) {
                var range = document.selection.createRange();
                begin = 0 - range.duplicate().moveStart('character', -100000);
                end = begin + range.text.length;
            }
            return { begin: begin, end: end };
        }
    },

    unmask: function() {
        return this.fireEvent("unmask");
    },

    /*===============================================*/

    onFocus: function(f) {
        var me = this;

        me.focusText = f.getRawValue();
        var pos = me.checkVal();
        me.writeBuffer();
        var moveCaret = function() {
            if (pos == me.format.length)
                f.caret(0, pos);
            else
                f.caret(pos);
        };
        (Ext.isIE ? moveCaret : function() {
            setTimeout(moveCaret, 0)
        })();
    },

    /*===============================================*/

    seekNext: function(pos) {
        var me = this;

        while (++pos <= me.format.length && !me.tests[pos]);
        return pos;
    },
    seekPrev: function(pos) {
        var me = this;

        while (--pos >= 0 && !me.tests[pos]);
        return pos;
    },

    shiftL: function(begin, end) {
        var me = this;
        if (begin < 0)
            return;
        for (var i = begin, j = me.seekNext(end); i < me.format.length; i++) {
            if (me.tests[i]) {
                if (j < me.format.length && me.tests[i].test(me.buffer[j])) {
                    me.buffer[i] = me.buffer[j];
                    me.buffer[j] = me.settings.placeholder;
                } else
                    break;
                j = me.seekNext(j);
            }
        }
        me.writeBuffer();
        me.field.caret(Math.max(me.firstNonMaskPos, begin));
    },

    shiftR: function(pos) {
        var me = this;

        for (var i = pos, c = me.settings.placeholder; i < me.format.length; i++) {
            if (me.tests[i]) {
                var j = me.seekNext(i);
                var t = me.buffer[i];
                me.buffer[i] = c;
                if (j < me.format.length && me.tests[j].test(t))
                    c = t;
                else
                    break;
            }
        }
    },

    keydownEvent: function(f, e) {
        var me = this;

        var k = e.getKey();

        //backspace, delete, and escape get special treatment
        if (k == 8 || k == 46 || (me.iPhone && k == 127)) {
            var pos = me.field.caret(),
                begin = pos.begin,
                end = pos.end;

            if (end - begin == 0) {
                begin = k != 46 ? me.seekPrev(begin) : (end = me.seekNext(begin - 1));
                end = k == 46 ? me.seekNext(end) : end;
            }
            me.clearBuffer(begin, end);
            me.shiftL(begin, end - 1);

            return false;
        } else if (k == 27) {//escape
            me.field.setRawValue(me.focusText);
            me.field.caret(0, me.checkVal());
            return false;
        }
    },

    keypressEvent: function(f, e) {
        var me = this;

        if (e.charCode == 0) {
            return true;
        }

        var k = e.getCharCode(),
            pos = me.field.caret();
        if (e.ctrlKey || e.altKey || e.metaKey || k < 32) {//Ignore
            return true;
        } else if (k) {
            if (pos.end - pos.begin != 0) {
                me.clearBuffer(pos.begin, pos.end);
                me.shiftL(pos.begin, pos.end - 1);
            }

            var p = me.seekNext(pos.begin - 1);
            if (p < me.format.length) {
                var c = String.fromCharCode(k);
                if (me.tests[p].test(c)) {
                    me.shiftR(p);
                    me.buffer[p] = c;
                    me.writeBuffer();
                    var next = me.seekNext(p);
                    me.field.caret(next);
                    if (me.settings.completed && next >= me.format.length)
                        me.settings.completed.call(f);
                }
            }
            return false;
        }
    },

    clearBuffer: function(start, end) {
        var me = this;

        for (var i = start; i < end && i < me.format.length; i++) {
            if (me.tests[i])
                me.buffer[i] = me.settings.placeholder;
        }
    },

    writeBuffer: function() {
        var me = this;
        return me.field.setRawValue(me.buffer.join(''));
    },

    checkVal: function(allow) {
        var me = this;
        var input = me.field;
        //try to place characters where they belong
        var test = input.getRawValue();
        var lastMatch = -1;
        for (var i = 0, pos = 0; i < me.format.length; i++) {
            if (me.tests[i]) {
                me.buffer[i] = me.settings.placeholder;
                while (pos++ < test.length) {
                    var c = test.charAt(pos - 1);
                    if (me.tests[i].test(c)) {
                        me.buffer[i] = c;
                        lastMatch = i;
                        break;
                    }
                }
                if (pos > test.length)
                    break;
            } else if (me.buffer[i] == test.charAt(pos) && i != me.partialPosition) {
                pos++;
                lastMatch = i;
            }
        }
        if (!allow && lastMatch + 1 < me.partialPosition) {
            input.setRawValue('');
            me.clearBuffer(0, me.format.length);
        } else if (allow || lastMatch + 1 >= me.partialPosition) {
            me.writeBuffer();
            if (!allow) input.setRawValue(input.getRawValue().substring(0, lastMatch + 1));
        }
        return (me.partialPosition ? i : me.firstNonMaskPos);
    },

    prepareValueToExternalWorld: function() {
        var me = this;

        var result = [], valid = true;
        for (var i = 0; i < me.tests.length; i += 1) {
            if (me.tests[i] != null) {
                result.push(me.buffer[i]);
            }
        }
        return Ext.Array.indexOf(result, me.settings.placeholder) == -1 ? result.join('') : '';
    },

    prepareValueToInternalWorld: function(val) {
        var me = this;

        var p = -1;
        Ext.each(val.split(''), function(c) {
            p = me.seekNext(p);
            if (me.tests[p].test(c)) {
                me.buffer[p] = c;
            }
        });
        me.writeBuffer();
    }
});
