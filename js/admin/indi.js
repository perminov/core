var Indi = function(indi) {
    "use strict";
    var process = function() {
        indi.proto = {};

        /**
         * Collect callbacks, for further execution
         *
         * @param callback Callback function
         * @param component Component name, which initialization should fire all stored callbacks
         */
        indi.ready = function(callback, component) {
            indi.callbacks = indi.callbacks || {};
            indi.callbacks[component] = indi.callbacks[component] || [];
            indi.callbacks[component].push(callback);
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
    };
    process();
    return indi;
}(Indi || {});
