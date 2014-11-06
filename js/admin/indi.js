Ext.define('Indi', {

    /**
     * Setup inheritance
     */
    extend: 'Ext.app.Application',

    /**
     * Disable quick tips, as we use tooltips instead
     */
    enableQuickTips: false,
    name: 'Indi',
    appFolder: './js/admin/app',

    /**
     * Static properties and methods
     */
    statics: {

        /**
         * Shortcut to trail data
         *
         * Indi.trail(true) - return Indi.Trail.instance object
         * Indi.trail([0-9]*) - return Indi.Trail.instance.item($1) object
         *
         * @return {*}
         */
        trail: function() {
            if (arguments[0] === true) {
                return Indi.Trail;
            } else {
                return Indi.Trail.item(arguments.length ? parseInt(arguments[0]) : 0);
            }
        },

        /**
         * Equivalent for php's strip_tags function. Source code got from http://phpjs.org/functions/strip_tags/
         *
         * @param input
         * @param allowed
         * @return {*}
         */
        stripTags: function(input, allowed) {
            // Making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
            allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

            // Regular expression for html tags, php tags and php comments
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

            // Stripping
            return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        },

        /**
         * Formats a given number to a format, specified by passed params.
         * Function does the same as number_format php function.
         * Javascript version was got from phpjs.org.
         *
         * @param number
         * @param decimals
         * @param decPoint
         * @param thousandsSep
         * @return {*}
         */
        numberFormat: function(number, decimals, decPoint, thousandsSeparator) {
            var number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousandsSeparator === 'undefined') ? ',' : thousandsSeparator,
                dec = (typeof decPoint === 'undefined') ? '.' : decPoint,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        },

        /**
         * Creates a deep copy of a passed object and return that copy
         *
         * @param obj An object to be copied
         * @return {Object}
         */
        copy: function(obj) {
            var ret = {};
            if (typeof(obj) == 'object') {
                if (obj && typeof(obj.length) != 'undefined') var ret = [];
                for (var key in obj) {
                    if (typeof(obj[key]) == 'object') {
                        ret[key] = Indi.copy(obj[key]);
                    } else if (typeof(obj[key]) == 'string') {
                        ret[key] = obj[key];
                    } else if (typeof(obj[key]) == 'number') {
                        ret[key] = obj[key];
                    } else if (typeof(obj[key]) == 'boolean') {
                        ((obj[key] == true) ? ret[key] = true : ret[key] = false);
                    }
                }
            }
            return ret;
        },

        // Setup text metrics, for text width detection
        //metrics: (window.parent.Indi ? window.parent.Indi.metrics : new Ext.util.TextMetrics()) || new Ext.util.TextMetrics(),

        urldecode: function(str){
            return decodeURIComponent((str + '').replace(/\+/g, '%20'));
        },

        /**
         * Callbacks store
         *
         * @type {Object}
         */
        callbacks: {},

        /**
         * Collect callbacks, for further execution
         *
         * @param callback Callback function
         * @param component Component name, which initialization should fire all stored callbacks
         */
        ready: function(callback, component, context) {
            if (typeof context == 'undefined') context = window;
            context.Indi.callbacks = Indi.callbacks || {};
            context.Indi.callbacks[component] = Indi.callbacks[component] || [];
            context.Indi.callbacks[component].push(callback);
        },

        /**
         * A list of function names, that are declared within Indi object, but should be accessible within global scope
         * @type {Array}
         */
        share: ['alias', 'hide', 'show', 'number'],

        /**
         * Converts passed string to it's url equivalent
         *
         * @param title
         * @return {String}
         */
        alias: function(title){

            // Symbols
            var s = ['а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ',
                'ъ','ы','ь','э','ю','я','№',' ','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s',
                't','u','v','w','x','y','z','-','0','1','2','3','4','5','6','7','8','9','Ë','À','Ì','Â','Í','Ã','Î','Ä','Ï',
                'Ç','Ò','È','Ó','É','Ô','Ê','Õ','Ö','ê','Ù','ë','Ú','î','Û','ï','Ü','ô','Ý','õ','â','û','ã','ÿ','ç','&'];

            // Replacements
            var r = ['a','b','v','g','d','e','yo','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','shh',
                '','y','','e','yu','ya','#','-','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s',
                't','u','v','w','x','y','z','-','0','1','2','3','4','5','6','7','8','9','e','a','i','a','i','a','i','a','i',
                'c','o','e','o','e','o','e','o','o','e','u','e','u','i','u','i','u','o','u','o','a','u','a','y','c','-and-'];

            // Declare variable for alias
            var alias = '';

            // Convert passed title to loweк case and trim whitespaces
            title = title.toLowerCase().trim();

            // Find a replacement for each char of title and append it to alias
            for (var i = 0; i < title.length; i++) {
                var c = title.substr(i, 1);
                if (s.indexOf(c) != -1) alias += r[s.indexOf(c)];
            }

            // Strip '-' symbols from alias beginning, ending and replace multiple '-' symbol occurence with single occurence
            alias = alias.replace(/^\-+/, '');
            alias = alias.replace(/\-+$/, '');
            alias = alias.replace(/\-{2,}/g, '-');

            // Got as we need
            return alias;
        },

        /**
         * Hide all dom elements, that have ids, passed in comma-separated `ids` param
         * @param ids
         */
        hide: function(ids){
            ids.split(',').forEach(function(i){
                if (Ext.getCmp(i)) Ext.getCmp(i).hide();
                else $('#'+i).hide();
            });
        },

        /**
         * Show all dom elements, that have ids, passed in comma-separated `ids` param
         * @param ids
         */
        show: function(ids){
            ids.split(',').forEach(function(i){
                if (Ext.getCmp(i)) Ext.getCmp(i).show();
                else $('#'+i).show();
            });
        },


        /**
         * This function is used to wrap native 'eval' usage, to check if javascript (to be executed by 'eval')
         * contains expressions like "hide('tr-field1Alias, tr-field2Alias')" or "show('tr-field1Alias, tr-field2Alias')"
         *
         * Such expressions have been used already a long time, and involved in a number of existing project,
         * in most cases, for showing/hiding some form fields. So this function is need to provide a backward-compability
         * for such cases, because, earlier, form fields weren't built by Ext components usage, but were built by
         * native HTML and JavaScript, and hiding/showing was based on jQuery calls like this -
         * $('#tr-someField1Alias, #tr-someField2Alias').hide();
         *
         * So, this function checks if 'js' argument contains 'hide' or 'show', and checks if target (that are to be
         * shown/hidden) identifiers starts from 'tr-' string, and if so - function will replace 'tr-' with calculated
         * id prefix, and only after that executes the 'js' arguments as a JavaScript-string
         *
         * 'me' argument - is a component, that fires the execution. It's used to determine a proper id-prefix, for it
         * to be used as a replacement for 'tr-'
         *
         * @param js
         * @param me
         */
        eval: function(js, me) {
            if (!Ext.isString(js)) return;
            var s = "(hide|show)\\('((tr-[a-zA-Z0-9]+,?)+)'\\);?", ttrMRex = new RegExp(s, 'g'), ttrSRex = new RegExp(s),
                ttrMMatch, ttrSMatch, ttrSFoundTrA, ttrSReplace, ttrSReplaceTrA = [], trA, replace = [], trueTr;
            if (ttrMMatch = js.match(ttrMRex)) {
                var pid = me.ownerCt.id;
                for (var i = 0; i < ttrMMatch.length; i++) {
                    ttrSReplaceTrA = [];
                    ttrSMatch = ttrMMatch[i].match(ttrSRex);
                    ttrSFoundTrA = ttrSMatch[2].split(',');
                    for (var j = 0; j < ttrSFoundTrA.length; j++) {
                        var found = Ext.getCmp(pid).query('> [name="' + ttrSFoundTrA[j].replace(/^tr-/, '') +'"]');
                        ttrSReplaceTrA.push(found.length ? found[0].id : ttrSFoundTrA[j]);
                    }
                    ttrSReplace = ttrMMatch[i].replace(ttrSFoundTrA.join(','), ttrSReplaceTrA.join(','));
                    js = js.replace(ttrMMatch[i], ttrSReplace);
                }
            }
            eval(js);
        },

        /**
         * Removes all non-numeric symbols from `str` param
         *
         * @param str
         * @return {String}
         */
        number: function(str) {
            var number = '';
            for (var i = 0; i < str.length; i++) {
                var code = str.charCodeAt(i);
                if (code >= 48 && code <= 57) {
                    number += str.charAt(i);
                }
            }
            return number;
        },

        /**
         * Share all functions/objects/variables, that have names, existing in indi.share array  -  to global scope.
         */
        shareWith: function(context){
            for (var i = 0; i < Indi.share.length; i++) {
                eval('context.' + Indi.share[i] + '= Indi.'+ Indi.share[i] +';');
            }
        },

        /**
         * Returns the Ext center region component
         *
         * @return {*}
         */
        getCenter: function() {
            return Ext.getCmp('i-center');
        },

        /**
         * Destroy the contents of center panel, and all objects related to it
         */
        clearCenter: function() {
            if (Ext.getCmp('i-center-center-wrapper')) Ext.getCmp('i-center-center-wrapper').destroy();
        },

        /**
         * A JavaScript equivalent of PHP’s ucfirst
         *
         * @param str
         * @return {String}
         */
        ucfirst: function(str) {
            return str.charAt(0).toUpperCase() + str.substr(1);
        },

        /**
         * Uri history
         */
        story: [],

        /**
         * Load the contents got from `uri` param
         *
         * @param uri
         */
        load: function(uri) {

            // Push the given url to a story stack
            Indi.story.push(uri);

            // Make the request
            Ext.Ajax.request({
                url: uri,
                success: function(response){
                    Indi.clearCenter();
                    Ext.get('i-center-center-body').update(response.responseText, true);
                }
            });
        },

        /**
         * Clock
         */
        timer: setInterval(function(){
            Indi.time++;
        }, 1000),

        /**
         * Quotes string that later will be used in regular expression.
         *
         * @param str
         * @param delimiter
         * @return {String}
         */
        pregQuote: function(str, delimiter) {
            return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
        },

        /**
         * Apply system object's additional prototype functions, because some browsers do not have it as built-in
         */
        modernizer: {

            /**
             * Append indexOf() method definition in Array.prototype if need, because IE<9 doest not have it
             */
            'Array.prototype.indexOf': function() {

                if (!Array.prototype.indexOf) {
                    Array.prototype.indexOf = function(elt) {
                        var len = this.length >>> 0;

                        var from = Number(arguments[1]) || 0;
                        from = (from < 0) ? Math.ceil(from) : Math.floor(from);
                        if (from < 0) from += len;

                        for (; from < len; from++) {
                            if (from in this && this[from] === elt)
                                return from;
                        }
                        return -1;
                    };
                }
            },

            /**
             * Append getOwnPropertynames() method definition in Object if need, because IE<9 doest not have it
             */
            'Object.getOwnPropertyNames': function() {
                if (typeof Object.getOwnPropertyNames !== "function") {
                    Object.getOwnPropertyNames = function (obj) {
                        var keys = [];

                        // Only iterate the keys if we were given an object, and
                        // a special check for null, as typeof null == "object"
                        if (typeof obj === "object" && obj !== null) {
                            // Use a standard for in loop
                            for (var x in obj) {
                                // A for in will iterate over members on the prototype
                                // chain as well, but Object.getOwnPropertyNames returns
                                // only those directly on the object, so use hasOwnProperty.
                                if (obj.hasOwnProperty(x)) {
                                    keys.push(x);
                                }
                            }
                        }

                        return keys;
                    }
                }
            },

            /**
             * Append trim() method definition in String.prototype if need, because IE<9 doest not have it
             */
            'String.prototype.trim': function() {
                if (typeof String.prototype.trim !== 'function') {
                    String.prototype.trim = function() {
                        return this.replace(/^\s+|\s+$/g, '');
                    }
                }
            },

            /**
             * Define a global JSON object if need, because IE<8 does not have it
             */
            'window.JSON': function() {
                if (typeof window.JSON!=="object"){window.JSON={}}(function(){"use strict";function f(e){return e<10?"0"+e:e}function quote(e){escapable.lastIndex=0;return escapable.test(e)?'"'+e.replace(escapable,function(e){var t=meta[e];return typeof t==="string"?t:"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+e+'"'}function str(e,t){var n,r,i,s,o=gap,u,a=t[e];if(a&&typeof a==="object"&&typeof a.toJSON==="function"){a=a.toJSON(e)}if(typeof rep==="function"){a=rep.call(t,e,a)}switch(typeof a){case"string":return quote(a);case"number":return isFinite(a)?String(a):"null";case"boolean":case"null":return String(a);case"object":if(!a){return"null"}gap+=indent;u=[];if(Object.prototype.toString.apply(a)==="[object Array]"){s=a.length;for(n=0;n<s;n+=1){u[n]=str(n,a)||"null"}i=u.length===0?"[]":gap?"[\n"+gap+u.join(",\n"+gap)+"\n"+o+"]":"["+u.join(",")+"]";gap=o;return i}if(rep&&typeof rep==="object"){s=rep.length;for(n=0;n<s;n+=1){if(typeof rep[n]==="string"){r=rep[n];i=str(r,a);if(i){u.push(quote(r)+(gap?": ":":")+i)}}}}else{for(r in a){if(Object.prototype.hasOwnProperty.call(a,r)){i=str(r,a);if(i){u.push(quote(r)+(gap?": ":":")+i)}}}}i=u.length===0?"{}":gap?"{\n"+gap+u.join(",\n"+gap)+"\n"+o+"}":"{"+u.join(",")+"}";gap=o;return i}}if(typeof Date.prototype.toJSON!=="function"){Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf()}}var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","	":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;if(typeof window.JSON.stringify!=="function"){window.JSON.stringify=function(e,t,n){var r;gap="";indent="";if(typeof n==="number"){for(r=0;r<n;r+=1){indent+=" "}}else if(typeof n==="string"){indent=n}rep=t;if(t&&typeof t!=="function"&&(typeof t!=="object"||typeof t.length!=="number")){throw new Error("JSON.stringify")}return str("",{"":e})}}if(typeof window.JSON.parse!=="function"){window.JSON.parse=function(text,reviver){function walk(e,t){var n,r,i=e[t];if(i&&typeof i==="object"){for(n in i){if(Object.prototype.hasOwnProperty.call(i,n)){r=walk(i,n);if(r!==undefined){i[n]=r}else{delete i[n]}}}}return reviver.call(e,t,i)}var j;text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(e){return"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})}if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){j=eval("("+text+")");return typeof reviver==="function"?walk({"":j},""):j}throw new SyntaxError("JSON.parse")}}})();
            },

            /**
             * Get array of values of a single property picked from each object-item within current array
             *
             * @param name The name of the property, that should be got
             * @return {Array}
             */
            'Array.prototype.column': function() {
                Object.defineProperty(Array.prototype, 'column', {
                    enumerable: false,
                    configurable: false,
                    value: function(name) {

                        // Declare array for pushing needed property values
                        var column = [];

                        // Get those values
                        for (var i = 0; i < this.length; i++)
                            if (this[i].hasOwnProperty(name))
                                column.push(this[i][name]);

                        // Return them
                        return column;
                    }
                });
            },

            /**
             * Force array to contain only item, that have keys, mentioned in `val` argument
             * If `limit` argument is specified, the return value won't contain greater number of items than
             * specified in that arguments.
             * If `clone` argument is set to true (this is it's default value), a clone of current array will be
             * filtered and returned. Otherwise - method will operate on current array instead of on it's clone
             *
             * @param val String, may be comma-separated
             * @param key String
             * @param limit Number
             * @param clone bool
             * @return Array
             */
            'Array.prototype.select': function() {
                Object.defineProperty(Array.prototype, 'select', {
                    enumerable: false,
                    configurable: false,
                    value: function(val, key, limit, clone) {

                        // Get the selected items
                        var selected = (clone !== false ? Ext.clone(this) : this).exclude(val, key, true);

                        // Trim the selected items array, for it to contain only certain number of items
                        if (limit && !isNaN(parseInt(limit))) selected.splice(limit, selected.length - limit);

                        // Return
                        return selected;
                    }
                });
            },

            /**
             * Does the same as Array.select() method, but returns first selected item, directly
             *
             * @param val String, may be comma-separated
             * @param key String
             */
            'Array.prototype.r': function() {
                Object.defineProperty(Array.prototype, 'r', {
                    enumerable: false,
                    configurable: false,
                    value: function(val, key) {

                        // Return
                        return this.select(val, key, 1)[0];
                    }
                });
            },

            /**
             * Exclude items from current array, that have keys, mentioned in `val` argument.
             * If `inverse` argument is set to true, then function will return only items,
             * which keys are mentioned in `val` argument
             *
             * @param val String, may be comma-separated
             * @param key String
             * @param inverse bool
             * @return Array
             */
            'Array.prototype.exclude': function() {
                Object.defineProperty(Array.prototype, 'exclude', {
                    enumerable: false,
                    configurable: false,
                    value: function(val, key, inverse) {

                        // If no explicit key name was given in `key`
                        if (!key) {

                            // Setup array of other possible key names, what will be tried to use instead
                            var defaultKeyNameA = ['id', 'alias', 'key'];

                            // Check if any of these key names are exists as one of each item's properties
                            for (var i = 0; i < defaultKeyNameA.length; i++)

                                // And if so, we will use that key name instead of value of `key` argument
                                if (this.column(defaultKeyNameA[i]).length) {
                                    key = defaultKeyNameA[i];
                                    break;
                                }
                        }

                        // Get the array of values, by comma-splitting 'val' arg
                        val = (val+'').split(',');

                        // If 'inverse' arg is true
                        if (inverse) {

                            // Remove from 'this' array all object-items,
                            // that have value of 'key' prop not in selection list
                            for (var i = 0; i < this.length; i++)
                                if (val.indexOf(this[i][key]+'') == -1)
                                    this.splice(i--, 1);
                        // Else
                        } else {

                            // Remove from 'this' array all object-items,
                            // that have value of 'key' prop in exclusion list
                            for (var i = 0; i < this.length; i++)
                                if (val.indexOf(this[i][key]+'') != -1)
                                    this.splice(i, 1);
                        }

                        // Return array itself
                        return this;
                    }
                });
            }
        },

        /**
         * Custom implementation of Ext.fly() method, that allows to treat any html blob as Ext.dom.Element.Fly object,
         * and makes possible to do with it such things as dom queries, etc
         *
         * @param html
         * @return {*}
         */
        fly: function(html) {
            return Ext.fly(Ext.query('> *:first-child', Ext.DomHelper.createDom({tag: 'div', html: html})).pop());
        },

        /**
         * Convert integer value to file-size expression, e.g '10485760' => '10mb'
         *
         * @param size
         * @return {Number}
         */
        size2str: function(size) {

            // Setup auxiliary variables
            var pow, str, postfix = {0: 'b', 1: 'kb', 2: 'mb', 3: 'gb', 4: 'tb', 5: 'pb'};

            // Get the uploaded file size grade
            pow = Math.floor(size.toString().length/3);

            // Get the string representation of a filesize
            return Math.floor((size/Math.pow(1024, pow))*100)/100 + postfix[pow];
        }
    },

    /**
     * Launch callback
     */
    launch: function() {

        // Merge static properties, passed within construction, with prototype's static properties
        this.self = Ext.merge(this.self, this.statics);

        if (Ext.get('i-login-box')) {
            Ext.create('Indi.view.LoginBox', {title: Indi.title});
        } else {
            Indi.viewport = Ext.create('Indi.view.Viewport');
        }

        Indi.app = this;
    }
}, function() {

    // Apply system object's additional prototype functions, because some browsers do not have it as built-in
    for (var i in this.modernizer) this.modernizer[i]();

    // Share some Indi's functions with window object
    this.shareWith(window);
});