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
         * Check whether `item` argument is exists within `array` argument.
         * `array` arguments can be given not only as an array, but also as
         * a string containing comma-separated values
         *
         * @param item
         * @param array
         * @return {Boolean}
         */
        in: function(item, array) {
            var a, i;

            // If `array` arg is null/undefined return false
            if (array === null || array == undefined || typeof array == 'function') return false;

            // If `array` arg is a string/number
            if (typeof array == 'string' || typeof array == 'number') {

                // Cast `array` arg as string
                a = array + '';

                // If `array` arg is an empty string return false
                if (!a.length) return false;

                // If `array` arg is null/undefined return false
                if (item === null || item == undefined || typeof item == 'function' || typeof item == 'object') return false;

                // Split `array` arg by comma
                a = a.split(',');

                // Cast `item` as string
                if (typeof item == 'boolean') {
                    i = (item ? 1 : 0) + '';
                } else if (typeof item == 'number' || typeof item == 'string') {
                    i = item + '';
                }

                // Return
                return a.indexOf(i) != -1;
            }

            // Return
            return array.indexOf(item) != -1;
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
         * @param url User-defined url for picking GET param from, instead of document.locaton.toString()
         * @return {*}
         */
        get: function(param, url) {
            var qs = (url || document.location.toString()).split('?')[1];

            // If no GET params - return
            if (!qs) return;

            // Setup auxilliary variables
            var pairA = qs.split('&'), pairI, getO = {};

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
            if (!Ext.isString(js) || !js.length) return;
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

            // If `cfg` argument has `responseText` property
            if (cfg.responseText) {

                // Update title, and destroy target panel, if needed
                Indi._beforeApplyResponse(cfg);

                // Apply response with no any additional actual ajax-request
                Indi._applyResponse(cfg.responseText, cfg, uri);

            // Else make the ajax-request
            } else Ext.Ajax.request(Ext.merge({
                url: Indi.pre + (uri = uri.replace(/^\/admin\//, '\/')),
                timeout: 300000,
                success: function(response){

                    // Update title, and destroy target panel, if needed
                    Indi._beforeApplyResponse(cfg);

                    // Process response. Here we use Ext.defer to provide a visual
                    // 'white-blink' effect between destroying old and creating new
                    Ext.defer(function(){ Indi._applyResponse(response.responseText, cfg, uri); }, 10);
                },
                failure: Indi.ajaxFailure
            }, cfg));
        },

        /**
         * Detect json-stringified error messages, wrapped with <error/> tag, within the raw responseText,
         * convert each error to JSON-object, and return an array of such objects
         *
         * @param rt Response text, for trying to find errors in
         * @return {Array} Found errors
         */
        serverErrorObjectA: function(rt, entitiesEncoded) {

            // If response text is empty - return false
            if (!rt.length) return ['Empty response'];

            // If `entitiesEncoded` arg is `true`, we decode back htmlentities
            if (entitiesEncoded) rt = rt.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

            // Define variables
            var errorA = [], errorI;

            // Pick errors
            Indi.fly('<response>'+rt+'</response>').select('error').each(function(item){
                if (errorI = Ext.JSON.decode(item.getHTML(), true)) errorA.push(errorI);
            });

            // Return errors
            return errorA;
        },

        /**
         * Builds a string representation of a given error objects, suitable for use as Ext.MessageBox contents
         *
         * @param {Array} serverErrorObjectA
         * @return {Array}
         */
        serverErrorStringA: function(serverErrorObjectA) {

            // Define auxilliary variables
            var errorSA = [], typeO = {1: 'PHP Fatal error', 2: 'PHP Warning', 4: 'PHP Parse error', 0: 'MySQL query', 3: 'MYSQL PDO'},
                type, seoA = serverErrorObjectA;

            // Convert each error message object to a string
            for (var i = 0; i < seoA.length; i++)
                errorSA.push(((type = typeO[seoA[i].code]) ? type + ': ' : '') + seoA[i].text + ' at ' +
                    seoA[i].file + ' on line ' + seoA[i].line);

            // Return error strings array
            return errorSA;
        },

        /**
         * Common function for handling ajax/iframe responses
         * It detects <error>...</error> elements in responseText prop of `response` arg,
         * show them along with trimming them from responseText. It also detects whether
         * the trimmed responseText can be decoded into JSON, and if so, does it have
         * `mismatch`, `confirm` and `success` props and if so - handle them certain ways
         * and return `success` prop that can be undefined, null, boolean or other value
         *
         * @param response
         * @return {Boolean}
         */
        parseResponse: function(response, arg2) {
            var json, wholeFormMsg = [], mismatch, errorByFieldO, msg,
                form = arg2 && arg2.scope && arg2.scope.form ? arg2.scope.form : null, trigger,
                certainFieldMsg, cmp, seoA = Indi.serverErrorObjectA(response.responseText), sesA,
                logger = console && (console.log || console.error), boxA = [], urlOwner = form || response.request.options;

            // Remove 'answer' param, if it exists within url
            urlOwner.url = urlOwner.url.replace(/\banswer=(ok|no|cancel)/, '');

            // Hide loadmask
            if (Indi.loadmask) Indi.loadmask.hide();

            // Try to detect error messages, wrapped in <error/> tag, within responseText
            if (seoA.length) {

                // Build array of error strings from error objects
                sesA = Indi.serverErrorStringA(seoA);

                // Write php-errors to the console, additionally
                if (logger) for (var i in sesA) logger(sesA[i]);

                // Show errors within a message box
                boxA.push({
                    title: 'Server error',
                    msg: sesA.join('<br><br>'),
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });

                // Strip errors from response
                response.responseText = response.responseText.split('</error>').pop();
            }

            // Parse response text as JSON, and if no success - return
            if (!(json = Ext.JSON.decode(response.responseText, true))) {

                // Show box
                if (boxA.length) Ext.Msg.show(boxA[0]);

                // Return success as true or false
                return boxA.length ? false : true;
            }

            // The the info about invalid fields from the response, and mark the as invalid
            if ('mismatch' in json && Ext.isObject(json.mismatch)) {

                // Shortcut to json.mismatch
                mismatch = json.mismatch;

                // Error messages storage
                errorByFieldO = mismatch.errors;

                // Detect are error related to current form fields, or related to fields of some other entry,
                // that is set up to be automatically updated (as a trigger operation, queuing after the primary one)
                trigger = form ? mismatch.entity.title != form.owner.ctx().ti().model.title || mismatch.entity.entry != form.owner.ctx().ti().row.id : true;

                // Collect all messages for them to be bit later displayed within Ext.MessageBox
                Object.keys(errorByFieldO).forEach(function(i){

                    // If mismatch key starts with a '#' symbol, we assume that message, assigned
                    // under such key - is not related to any certain field within form, so we
                    // collect al such messages for them to be bit later displayed within Ext.MessageBox
                    if (i.substring(0, 1) == '#' || trigger) wholeFormMsg.push(errorByFieldO[i]);

                    // Else if mismatch key doesn't start with a '#' symbol, we assume that message, assigned
                    // under such key - is related to some certain field within form, so we get that field's
                    // component and mark it as invalid
                    else if (form && (cmp = Ext.getCmp(form.owner.ctx().bid() + '-field$' + i))) {

                        // Get the mismatch message
                        certainFieldMsg = errorByFieldO[i];

                        // If mismatch message is a string
                        if (Ext.isString(certainFieldMsg))

                            // Cut off field title mention from message
                            certainFieldMsg = certainFieldMsg.replace('"' + cmp.fieldLabel + '"', '').replace(/""/g, '');

                        // Mark field as invalid
                        cmp.markInvalid(certainFieldMsg);

                        // If field is currently hidden - we duplicate error message for it to be shown within
                        // Ext.MessageBox, additionally
                        if (cmp.hidden) wholeFormMsg.push(errorByFieldO[i]);

                    // Else mismatch message is related to field, that currently, for some reason, is not available
                    // within the form - push that message to the wholeFormMsg array
                    } else wholeFormMsg.push(errorByFieldO[i]);
                });

                // If we collected at least one error message, that is related to the whole form rather than
                // some certain field - use an Ext.MessageBox to display it
                if (wholeFormMsg.length) {

                    msg = (wholeFormMsg.length > 1 || trigger ? '&raquo; ' : '') + wholeFormMsg.join('<br><br>&raquo; ');

                    // If this is a mismatch, caused by background php-triggers
                    if (trigger) msg = 'При выполнении вашего запроса, одна из автоматически производимых операций, в частности над записью типа "'
                        + mismatch.entity.title + '"'
                        + (parseInt(mismatch.entity.entry) ? ' [id#' + mismatch.entity.entry + ']' : '')
                        + ' - выдала следующие ошибки: <br><br>' + msg;

                    // Show message box
                    boxA.push({
                        title: Indi.lang.I_ERROR,
                        msg: msg,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }

            // Else if `confirm` prop is set - show it within Ext.MessageBox
            } else if ('confirm' in json) boxA.push({
                title: Indi.lang.I_MSG,
                msg: json.msg,
                buttons: Ext.Msg.OKCANCEL,
                icon: Ext.Msg.QUESTION,
                modal: true,
                fn: function(answer) {

                    // Append new answer param
                    urlOwner.url = urlOwner.url.split('?')[0] + '?answer=' + answer
                        + (urlOwner.url.split('?')[1] ? '&' + urlOwner.url.split('?')[1] : '');

                    // If answer is 'ok' show load mask
                    if (answer == 'ok' && Indi.loadmask) Indi.loadmask.show();

                    // Make new request
                    if (form) form.owner.submit({
                        submitEmptyText: false,
                        dirtyOnly: true
                    }); else Ext.Ajax.request(response.request.options);
                }

            // Else if `success` prop is set
            }); else if ('success' in json && 'msg' in json) {

                // If `msg` prop is set - show it within Ext.MessageBox
                boxA.push({
                    title: Indi.lang[json.success ? 'I_MSG' : 'I_ERROR'],
                    msg: json.msg,
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg[json.success ? 'INFO' : 'WARNING'],
                    modal: true
                });
            }

            // Else if `throwOutMsg` prop is set - reload page (throwOutMsg will be shown after that)
            else if (json.throwOutMsg) top.window.location.reload();

            // If no boxes should be shown - return
            if (!boxA.length) return json.success;

            // Ensure second box will be shown after first box closed
            if (boxA[1]) boxA[0].fn = function() { Ext.Msg.show(boxA[1]); }

            // Show first box
            Ext.Msg.show(boxA[0]);

            // Return
            return json.success;
        },

        /**
         * Update title, and destroy target panel, if needed
         *
         * @param cfg
         * @private
         */
        _beforeApplyResponse: function(cfg) {

            // If 'insteadOf' property is given within `cfg` object
            if (cfg.insteadOf) {

                // Set title for a container, that results will be injected in
                if (cfg.into && cfg.title) Ext.getCmp(cfg.into).setTitle(cfg.title);

                // Destroy the component, that will have a one to replace it
                Ext.getCmp(cfg.insteadOf).destroy();
            }
        },

        /**
         * Apply response
         *
         * @param responseText
         * @param cfg
         * @param uri
         * @private
         */
        _applyResponse: function(responseText, cfg, uri) {

            // Try to convert responseText to json-object
            var json = responseText.json();

            // If responseText conversion to json-object was successful
            if (json) {

                // If `json` has `trail` property, apply/dispatch it
                if (json.route) Indi.trail(true).apply(Ext.merge(json, {uri: uri, cfg: cfg}));

                // Else if
                else if (json.plain !== null) Ext.get('i-center-center-body').update(json.plain, true);

                // Run response
            } else Ext.get('i-center-center-body').update(responseText, true);
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
                        var r; return this.substr(0, 1).match(/[{\[]/) && (r = Ext.JSON.decode(this)) ? r : false
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
        mt: function() {
            var m = Indi.microtime(true), d = parseInt((m - Indi._mt)*1000);
            Indi._mt = m; if (arguments.length) console.log(d, arguments); return d;
        },

        /**
         * Add the measure version to a given quantity
         *
         * @param q
         * @param versions012
         * @param showNumber
         * @return {String}
         */
        tbq: function(q, versions012, showNumber) {
            var versions210, formatKA = ['0,11-19,5-9', '1', '2-4'], formatA = {}, formatK, formatV, spanA, k, interval, m;

            // Set up default values for arguments
            if (arguments.length < 1) q = 2;
            if (arguments.length < 2) versions012 = '';
            if (arguments.length < 3) showNumber = true;
            if (q !== 0 && !q) q = 0;

            // Force q arg to be string
            q += '';

            // Get versions reversed array
            versions210 = versions012.split(',').reverse();

            // Distribute quantity measure spell versions
            formatA['2-4'] = versions210[0]; formatA['1'] = versions210[1]; formatA['0,11-19,5-9'] = versions210[2];

            // Foreach format
            for (var i in formatKA) {

                formatK = formatKA[i];
                formatV = formatA[formatK];

                // Extract the intervals from format key
                spanA = formatK.split(',');

                // Foreach interval
                for (k = 0; k < spanA.length; k++) {

                    // If current interval is actually not interval, e.g it constits from only one digit
                    if (spanA[k].indexOf('-') == -1) {

                        // If quantity count ends with that digit
                        if (q.match(new RegExp(spanA[k] + '$')))

                            // Return the quantity (if showNumber argument is true), with appended spell version
                            return (showNumber ? q + ' ' : '') + formatV;

                    // Else current interval really is an interval
                    } else {

                        // Get the start and end digits of that interval
                        interval = spanA[k].split('-');

                        // Foreach digit within start and end interval digits
                        for (m = parseInt(interval[0]); m <= parseInt(interval[1]); m ++) {

                            // If quantity count ends with that digit
                            if (q.match(new RegExp(m + '$')))

                                // Return the quantity (if showNumber argument is true), with appended spell version
                                return (showNumber ? q + ' ' : '') + formatV;
                        }
                    }
                }
            }
            return q || '';
        },

        /**
         * Calculate the time, left until certain datetime
         *
         * @param to
         * @return {Object}
         */
        timeleft: function(to, ago, append){
            var interval = ago ? (new Date - Date.parse(to)) : (Date.parse(to) - new Date + (append || 0) * 60 * 1000), r = {
                days: Math.floor(interval/(60*60*1000*24)*1),
                hours: Math.floor((interval%(60*60*1000*24))/(60*60*1000)*1),
                minutes: Math.floor(((interval%(60*60*1000*24))%(60*60*1000))/(60*1000)*1),
                seconds: Math.floor((((interval%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1)
            };

            // Get total time
            r.none = r.days + r.hours + r.minutes + r.seconds ? false : true;

            // Get string representation
            r.str = (r.days ? r.days + 'д ' : '')
                + (r.hours ? ((r.hours + '').length == 1 ? '0' : '') + r.hours + ':' : '')
                + ((r.minutes + '').length == 1 ? '0' : '') + r.minutes + ':'
                + ((r.seconds + '').length == 1 ? '0' : '') + r.seconds;

            // Return
            return r;
        }
    },

    /**
     * Windows storage
     */
    windows: new Ext.util.MixedCollection(),

    /**
     * Get active window
     *
     * @return {*}
     */
    getActiveWindow: function () {
        var win = null, zmgr = Indi.app.getDesktopZIndexManager();

        // Walk through z-indexed windows and find the top one
        if (zmgr) zmgr.eachTopDown(function (comp) {
            if (comp.isWindow && !comp.hidden) {
                win = comp;
                return false;
            }
            return true;
        });

        // Return
        return win;
    },

    /**
     * Get active window
     *
     * @return {*}
     */
    getTopMaximizedWindow: function () {
        var win = null, zmgr = Indi.app.getDesktopZIndexManager();

        // Walk through z-indexed windows and find the top one that is maximized
        if (zmgr)
            zmgr.eachTopDown(function (comp) {
                if (comp.isWindow && !comp.hidden && comp.maximized) {
                    win = comp;
                    return false;
                }
                return true;
            });

        // Return
        return win;
    },

    /**
     * Get windows zIndex manager
     *
     * @return {*}
     */
    getDesktopZIndexManager: function () {
        var windows = this.windows;

        // Return
        return (windows.getCount() && windows.getAt(0).zIndexManager) || null;
    },

    /**
     * This function does all things, that are required to be done each time focus moves from one window to another
     */
    updateActiveWindow: function () {

        var me = this, activeWindow = me.getActiveWindow(), last = me.lastActiveWindow;

        // If currently active window - is the last focused - update bread crumb trail and return
        if (activeWindow === last) return Indi.app.updateTrail();

        // If we previously had active window, and that window is still exists
        if (last) {

            // Remove active style and add inactive style
            if (last.el.dom) {
                last.addCls(me.inactiveWindowCls);
                last.removeCls(me.activeWindowCls);
            }

            // Set up `active` prop as false`
            last.active = false;
        }

        // Set up currently active window as last active window
        me.lastActiveWindow = activeWindow;

        // Misc things
        if (activeWindow) {

            // Remove active style and add inactive style
            activeWindow.addCls(me.activeWindowCls);
            activeWindow.removeCls(me.inactiveWindowCls);

            // Set up window as non-mimimized, and set it's `active` flag to On
            activeWindow.minimized = false;
            activeWindow.active = true;
        }

        // Make button, likned to window, as active, too
        me.taskbar.setActiveButton(activeWindow && activeWindow.taskButton);
    },

    /**
     * Get window, containing wrapper-panel having given id, assuming that `wrapperId`
     * argument - is that wrapper-panel id
     *
     * @param wrapperId
     * @return {*}
     */
    getWindowByWrapperId: function(wrapperId) {
       return Ext.ComponentQuery.query('desktopwindow[wrapperId="' + wrapperId + '"]')[0];
    },

    /**
     * This function assumes, that `a` argument is a DOM <a>-element, contained within a certain tab,
     * that currently contains a placeholder for a wrapper-panel, rather than wrapper-panel itself,
     * so it determines that exact tab, and the window, where desired wrapper-panel is currently opened.
     * After that, function closes that window and reload the tab and remove placeholder
     *
     * @param a
     */
    putWindowBackToTab: function(wrapperId) {
        var wrp = Ext.getCmp(wrapperId), holder = Ext.getCmp(wrapperId + '-holder'), tab = holder.up('[isSouthItem]'),
            load = wrp.$ctx.uri, name = wrp.$ctx.ti().section.alias, window = Indi.app.getWindowByWrapperId(wrapperId);

        // Set up special flag to prevent looping
        window.isGettingBack = true;

        // Close separate window, containing contents that we want to put back to tab
        window.close();

        // Re-add tab
        tab.add({
            xtype: 'actiontabrowset',
            id: wrapperId,
            load: load,
            back: true,
            name: name
        });

        // Destroy placeholder
        holder.destroy();
    },

    /**
     * Update bread crumb trail contents to represent current window
     */
    updateTrail: function() {
        var me = this, topMaximized = me.getTopMaximizedWindow();

        // If we've found window that is the top most within maximized window
        // set up bread crumb trail to represent it's location within the system,
        // or erase bread crumb trail contents
        Ext.get('i-center-north-trail').setHTML(
            topMaximized ? Indi.trail(true).breadCrumbs(topMaximized.ctx.route) : ''
        );
    },

    /**
     * Launch callback
     */
    launch: function() {
        var me = this;

        // Merge static properties, passed within construction, with prototype's static properties
        me.self = Ext.merge(me.self, me.statics);

        if (Ext.get('i-login-box')) {
            Ext.create('Indi.view.LoginBox', {title: Indi.title});
        } else {

            // Create viewport
            Indi.viewport = Ext.create('Indi.view.Viewport');

            // Create loadmask
            Indi.loadmask = new Ext.LoadMask(Indi.viewport);

            // If websockets enabled
            if (Indi.ini.ws && parseInt(Indi.ini.ws.enabled))
                Ext.Loader.loadScriptFile(Indi.std + '/js/admin/ws.js', Ext.emptyFn, Ext.emptyFn, this, false);

            // Load dashboard
            if (Indi.user.dashboard) Indi.load(Indi.user.dashboard);
        }

        // Static shortcut to this app
        Indi.app = this;

        // Static shortcut to this app's taskbar
        Indi.app.taskbar = Ext.getCmp('i-center-north');
    }
}, function() {
    var me = this;

    // Apply system object's additional prototype functions, because some browsers do not have it as built-in
    for (var i in me.modernizer) me.modernizer[i]();

    // Share some Indi's functions with window object
    me.shareWith(window);
});