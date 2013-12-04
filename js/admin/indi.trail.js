var Indi = (function (indi) {
    "use strict";
    var process = function () {

        /**
         * Setup prototype of indi.trail
         */
        indi.proto.trail = function(store){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'trail';


            /**
             * The data object, that indi.trail will be operating with.
             * Data will be set by php's json_encode($this->trail->toArray()) call
             *
             * @type {Object}
             */
            this.store = store;

            this.apply = function(store){
                this.store = store;
            }

            this.item = function(stepsUp) {
                if (typeof stepsUp == 'undefined') stepsUp = 0;
                return this.store[this.store.length - 1 - stepsUp];
            }
        }

        indi.trail = new indi.proto.trail(indi.trail);
        top.Indi.trail = indi.trail;
    };

    /**
     * Wait until jQuery is ready, and then start all operations
     */
    (function () {
        var checkRequirementsId = setInterval(function () {
            if (typeof indi !== 'undefined') {
                clearInterval(checkRequirementsId);
                $(document).ready(function(){
                    process();
                });
            }
        }, 25);
    }());

    return indi;

}(Indi || {}));