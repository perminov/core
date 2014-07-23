var Indi = (function (indi) {
    "use strict";
    var process = function () {

        /**
         * Setup `filter` property of indi.proto.combo object
         */
        indi.proto.combo.filter = function(){

            /**
             * Builds a path to make a fetch request to
             *
             * @return string
             */
            this.fetchRelativePath = function() {
                return indi.pre + '/' + indi.trail.item().section.alias + '/form';
            }
        }
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi.combo !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));