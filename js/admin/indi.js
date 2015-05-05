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
    appFolder: '/js/admin/app',

    lastActiveWindow: null,

    // @inheritdoc
    constructor: function (cfg) {
        var me = this;

        // Normalize appFolder path
        me.appFolder = cfg.statics.std + me.appFolder;

        // Call parent
        me.callParent(arguments);
    },

    /**
     * Static properties and methods
     */
    statics: {

        /**
         * A list of function names, that are declared within Indi object, but should be accessible within global scope
         * @type {Array}
         */
        share: ['alias', 'hide', 'show', 'number', 'mt'],

        /**
         * Microtime
         */
        _mt: 0,

        /**
         * Global fields storage. Contains all fields that were even initialised
         */
        fields: {},

        /**
         * Get field by id, from fields storage
         *
         * @param id
         * @return {*}
         */
        field: function(id) {
            return this.fields[id];
        },

        /**
         * Shortcut to trail singleton instance
         *
         * @return {Indi.lib.trail.Trail}
         */
        trail: function() {
            return Indi.Trail;
        },

        /**
         * Retrieve query string ($_GET) any param, identified by `param` key
         *
         * @param param
         * @return {*}
         */
        get: function(param) {

            // Setup auxilliary variables
            var pairA = document.location.search.substr(1).split('&'), pairI, getO = {};

            // Build getO object
            for (var i = 0; i < pairA.length; i++) {

                // Get the param-value pair
                pairI = pairA[i].split('=');

                // Append to `getO` object as a value under certain property
                getO[pairI[0]] = pairI[1];
            }

            // Return whole object or a certain param
            return param ? getO[param] : getO;
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
                'Ç','Ò','È','Ó','É','Ô','Ê','Õ','Ö','ê','Ù','ë','Ú','î','Û','ï','Ü','ô','Ý','õ','â','û','ã','ÿ','ç','&', '/'];

            // Replacements
            var r = ['a','b','v','g','d','e','yo','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','shh',
                '','y','','e','yu','ya','#','-','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s',
                't','u','v','w','x','y','z','-','0','1','2','3','4','5','6','7','8','9','e','a','i','a','i','a','i','a','i',
                'c','o','e','o','e','o','e','o','o','e','u','e','u','i','u','i','u','o','u','o','a','u','a','y','c','-and-', '-'];

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
            try {
                eval(js);
            } catch (e) {
                Ext.log(me.name);
                Ext.log(js);
            }
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
         * Destroy the contents of center panel, and all objects related to it
         */
        clearCenter: function() {
            //if (Ext.getCmp(Indi.centerId)) Ext.defer(function(){Ext.getCmp(Indi.centerId).destroy();}, 1);
            if (Ext.getCmp(Indi.centerId)) Ext.getCmp(Indi.centerId).destroy();
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
         * Load the contents got from `uri` param
         *
         * @param {String} uri
         * @param {Object} cfg Request config
         */
        load: function(uri, cfg) {
            var centerPnl = Ext.getCmp(Indi.centerId);

            // Normalize `cfg` argument
            cfg = cfg || {};

            // Get data for remember
            if (centerPnl) Ext.merge(cfg, {params: {forScope: Ext.JSON.encode(centerPnl.forScope())}});

            // Make the request
            Ext.Ajax.request(Ext.merge({
                url: Indi.pre + uri,
                success: function(response, request){

                    // In no 'into' property given within `cfg` object - destroy center panel
                    if (!cfg.into) void(0); //Indi.clearCenter();

                    // Else if 'insteadOf' property is additionally given within `cfg` object
                    else if (cfg.insteadOf) {

                        // Set title for a container, that results will be injected in
                        if (cfg.title) Ext.getCmp(cfg.into).setTitle(cfg.title);

                        // Destroy the component, that will have a one to replace it
                        Ext.getCmp(cfg.insteadOf).destroy();
                    }

                    // Process response. Here we use Ext.defer to provide a visual
                    // 'white-blink' effect between destroying old and creating new
                    Ext.defer(function(){

                        // Try to convert responseText to json-object
                        var json = response.responseText.json();

                        // If responseText converstion to json-object was successful
                        if (json) {

                            // If `json` has `trail` property, apply/dispatch it
                            if (json.route) Indi.trail(true).apply(Ext.merge(json, {uri: uri, cfg: cfg}));

                            // Else if
                            else if (json.plain !== null) Ext.get('i-center-center-body').update(json.plain, true);

                        // Run response
                        } else Ext.get('i-center-center-body').update(response.responseText, true);

                    }, 10);
                }
            }, cfg));
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
                                        this.splice(i--, 1);
                        }

                        // Return array itself
                        return this;
                    }
                });
            },

            /**
             * Get the las item of the array
             *
             * @param i {Number}
             * @return {*}
             */
            'Array.prototype.last': function() {
                Object.defineProperty(Array.prototype, 'last', {
                    enumerable: false,
                    configurable: false,
                    value: function(i) {
                        return this[this.length - 1 - (isNaN(i) ? 0 : i)];
                    }
                });
            },

            /**
             * Get the las item of the array
             *
             * @param i {Number}
             * @return {*}
             */
            'String.prototype.json': function() {
                Object.defineProperty(String.prototype, 'json', {
                    enumerable: false,
                    configurable: false,
                    value: function() {
                        var r; return this.substr(0, 1).match(/[{\[]/) && (r = JSON.parse(this)) ? r : false
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
        },

        /**
         * Parse get given uri and convert it into plain object, containing section, action and all othe params
         *
         * @param uri
         * @return {Object}
         */
        parseUri: function(uri) {
            var o = {}, i, uriA = Ext.String.trim(uri).replace(/^\//, '').replace('/\/$/', '').split('/');

            // Setup all params
            for (i = 0; i < uriA.length; i++)

                // Setup section
                if (i == 0 && uriA[i]) o.section = uriA[i];

                // Setup action
                else if (i == 1) o.action = uriA[i];

                // Setup all other params
                else if (uriA.length > i && uriA[i].length) {
                    o[uriA[i]] = uriA[i + 1];
                    i++;
                }

            // Return object containing section, action and all other params
            return o;
        },

        /**
         *  Equivalent to PHP's microtime(). Got from http://phpjs.org/functions/microtime/
         *
         * @param get_as_float
         * @return {Number}
         */
        microtime: function (get_as_float) {
            var now = new Date().getTime() / 1000, s = parseInt(now, 10);
            return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
        },

        /**
         * Measure the count of milliseconds, passed from the last call of this function.
         * This function is useful for checking how much time any operation takes
         *
         * @return {Number}
         */
        mt: function(msg) {
            var m = Indi.microtime(true), d = parseInt((m - Indi._mt)*1000);
            Indi._mt = m; if (msg) console.log(msg, d); return d;
        }
    },

    /**
     * Windows storage
     */
    windows: new Ext.util.MixedCollection(),

    getActiveWindow: function () {
        var win = null, zmgr = Indi.app.getDesktopZIndexManager();

        if (zmgr) {
            // We cannot rely on activate/deactive because that fires against non-Window
            // components in the stack.

            zmgr.eachTopDown(function (comp) {
                if (comp.isWindow && !comp.hidden) {
                    win = comp;
                    return false;
                }
                return true;
            });
        }

        return win;
    },

    getDesktopZIndexManager: function () {
        var windows = this.windows;
        // TODO - there has to be a better way to get this...
        return (windows.getCount() && windows.getAt(0).zIndexManager) || null;
    },


    updateActiveWindow: function () {
        var me = this, activeWindow = me.getActiveWindow(), last = me.lastActiveWindow;

        if (activeWindow === last) return;

        if (last) {
            if (last.el.dom) {
                last.addCls(me.inactiveWindowCls);
                last.removeCls(me.activeWindowCls);
            }
            last.active = false;
        }

        me.lastActiveWindow = activeWindow;

        if (activeWindow) {
            activeWindow.addCls(me.activeWindowCls);
            activeWindow.removeCls(me.inactiveWindowCls);
            activeWindow.minimized = false;
            activeWindow.active = true;
        }

        me.taskbar.setActiveButton(activeWindow && activeWindow.taskButton);
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
        Indi.app.taskbar = Ext.getCmp('i-center-north');
    }
}, function() {

    // Apply system object's additional prototype functions, because some browsers do not have it as built-in
    for (var i in this.modernizer) this.modernizer[i]();

    // Share some Indi's functions with window object
    this.shareWith(window);
});