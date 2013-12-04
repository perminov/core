var Indi = function(indi) {
    "use strict";
    var process = function() {
        /**
         * A list of function names, that are declared within Indi object, but should be accessible within global scope
         * @type {Array}
         */
        indi.share = ['alias', 'hide', 'show', 'number'];

        indi.story = [];

        /**
         * Prototypes store
         *
         * @type {Object}
         */
        indi.proto = {};

        /**
         * Collect callbacks, for further execution
         *
         * @param callback Callback function
         * @param component Component name, which initialization should fire all stored callbacks
         */
        indi.ready = function(callback, component, context) {
            if (typeof context == 'undefined') context = window;
            context.Indi.callbacks = Indi.callbacks || {};
            context.Indi.callbacks[component] = Indi.callbacks[component] || [];
            context.Indi.callbacks[component].push(callback);
        }

        /**
         * Equivalent for php's strip_tags function. Source code got from http://phpjs.org/functions/strip_tags/
         *
         * @param input
         * @param allowed
         * @return {*}
         */
        indi.stripTags = function(input, allowed) {
            // Making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
            allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

            // Regular expression for html tags, php tags and php comments
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;

            // Stripping
            return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        }

        /**
         * Converts passed string to it's url equivalent
         *
         * @param title
         * @return {String}
         */
        indi.alias = function(title){
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
        }

        /**
         * Hide all dom elements, that have ids, passed in comma-separated `ids` param
         * @param ids
         */
        indi.hide = function(ids){
            $('#'+ids.split(',').join(', #')).hide();
        }

        /**
         * Show all dom elements, that have ids, passed in comma-separated `ids` param
         * @param ids
         */
        indi.show = function(ids){
            $('#'+ids.split(',').join(', #')).show();
        }

        /**
         * Removes all non-numeric symbols from `str` param
         *
         * @param str
         * @return {String}
         */
        indi.number = function(str) {
            var number = '';
            for (var i = 0; i < str.length; i++) {
                var code = str.charCodeAt(i);
                if (code >= 48 && code <= 57) {
                    number += str.charAt(i);
                }
            }
            return number;
        }

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
        indi.numberFormat = function(number, decimals, decPoint, thousandsSeparator) {
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
        }

        /**
         * Creates a deep copy of a passed object and return that copy
         *
         * @param obj An object to be copied
         * @return {Object}
         */
        indi.copy = function(obj) {
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
        }

        /**
         * Returns the Ext center region component
         *
         * @return {*}
         */
        indi.getCenter = function() {
            return Ext.getCmp('center-all');
        }


        indi.clearCenter = function() {
            if (Ext.getCmp('i-center-content')) Indi.getCenter().remove('i-center-content');
        }

        /**
         * Load the contents got from `url` param
         *
         * @param url
         * @param iframe  bool
         */
        indi.load = function(url, iframe) {

            // Push the given url to a story stack
            indi.story.push(url);

            if (url.match(/\/form\//) || iframe) {

                Indi.clearCenter();

                Indi.getCenter().getComponent('center-content').hide();

                Indi.getCenter().add(Ext.create('Ext.Panel', {
                    region: 'center',
                    border: 1,
                    align: 'stretch',
                    html: '<div id="iframe-wrapper"><iframe src="' + url + '?width=' + Math.floor(($('#center-content-body').width()-36)/2) +
                        '" width="100%" scrolling="auto" frameborder="0" id="form-frame" name="form-frame"></iframe></div>',
                    id: 'i-center-content',
                    iframed: true,
                    height: '100%',
                    listeners: {
                        afterlayout: function(panel){
                            $('#form-frame').height($(panel.el.dom).find('#'+panel.id+'-body').height());
                            Indi.getCenter().getComponent('center-content').show();
                            Indi.getCenter().getComponent('center-content').remove();
                        }
                    }
                }));


                //indi.iframeMask = new Ext.LoadMask(top.window.$('#iframe-wrapper')[0], {});
                //indi.iframeMask.show();

            } else {
                $.post(url, function(response){
                    Indi.clearCenter();
                    Indi.viewport.doComponentLayout();
                    $('#center-content-body').html(response);
                });
            }
        }

        /**
         * Share all functions/objects/variables, that have names, existing in indi.share array  -  to global scope.
         */
        indi.shareWith = function(context){
            for (var i = 0; i < indi.share.length; i++) {
                eval('context.' + indi.share[i] + '= top.window.Indi.'+ indi.share[i] +';');
            }
        }
        indi.shareWith(window);
    };

    /**
     * Wait until jQuery and Ext are ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof jQuery !== 'undefined' &&
                typeof Ext !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;
}(Indi || {});
