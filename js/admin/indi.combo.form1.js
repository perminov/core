    var process = function () {
        /**
         * Setup `form` property of indi.proto.combo object - this will be main prototype for all combos
         */
        indi.proto.combo.form = function(){

            /**
             * This is for context stabilization
             *
             * @type {*}
             */
            var instance = this;

            /**
             * Options data storage
             *
             * @type {Object}
             */
            this.store = {};

            /**
             * This will be used at the stage of request uri constructing while within remoteFetch()
             * and also, is used to get a proper stack of callbacks that should be called in run()
             *
             * @type {String}
             */
            this.componentName = 'combo.form';

            /**
             * Configuration
             *
             * @type {Object}
             */
            this.options = {
                removeComboDataDivs: true
            }

            /**
             * Setup the dom item, that options html should be appended to
             *
             * @param name
             * @return {*}
             */
            this.getComboDataAppendToEl = function(name) {
                return $('#'+name+'-keyword').parents('.i-combo');
            }


            /**
             * The enter point.
             */
            this.run = function() {

                /**
                 * Bind keyUpHandler on keyup event for keyword html-input
                 */
                $(instance.keywordSelector()).keyup(instance.keyUpHandler);

                /**
                 * Bind keyDownHandler on keyup event for keyword html-input
                 */
                $(instance.keywordSelector()).keydown(instance.keyDownHandler);

                /**
                 * Bind handlers for click, blur events and misc things for keyword html-input
                 */
                $(instance.keywordSelector()).each(function(index){

                    // Get name
                    var name = $(this).attr('lookup');


                    /*$('#'+name+'-suggestions').scroll(function(){
                        $('#'+name+'-keyword').focus();
                    });*/

                });
            }
        }
    };
