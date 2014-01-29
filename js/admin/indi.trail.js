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

            /**
             * Prepare trail item row title for use at a part of bread crumbs
             *
             * @param item
             */
            this.breadCrumbsRowTitle = function(item){

                // At first, we strip newline characters, html '<br>' tags, and cut it's first 50 characters
                var title = item.row.title.replace(/[\n\r]/g, '').replace(/<br>/g, ' ').substr(0, 50);

                // Then we check if title contains a date or datetime, and if so we check if there was format
                // params set and in that case we convert date or datetime for it to be in needed format
                for (var f = 0; f < item.fields.length; f++) {
                    if (item.fields[f].alias == 'title' && [12, 17].indexOf(parseInt(item.fields[f].elementId))) {
                        if (item.fields[f].params) {
                            if (item.fields[f].params.displayFormat) {
                                title = Ext.Date.format(
                                    Ext.Date.parse(title, 'Y-m-d'),
                                    item.fields[f].params.displayFormat
                                );
                            } else if (item.fields[f].params.displayDateFormat) {
                                title = Ext.Date.format(
                                    Ext.Date.parse(title, 'Y-m-d H:i:s'),
                                    item.fields[f].params.displayDateFormat + ' ' +
                                        item.fields[f].params.displayTimeFormat
                                );
                            }
                        }
                    }
                }

                return title;
            }

            this._crumbHref = function(section, hero) {
                var href = indi.pre + '/' + section + '/';

                if (hero) {
                    href += (hero.row && hero.section.alias == section) ? 'form' : 'index';

                    href += '/id/' + hero.row.id + '/' +
                        (hero.section.primaryHash ? 'ph/' + hero.section.primaryHash + '/' : '') +
                        (hero.section.rowIndex ? 'aix/' + hero.section.rowIndex + '/' : '');
                }

                return href;
            }

            /**
             * Build the breadcrumbs
             */
            this.breadCrumbs = function(){

                var crumbA = [];

                crumbA.push('<span class="i-trail-section-group">' + instance.store[0].section.title + '</span>');

                for (var i = 1; i < instance.store.length; i++) {
                    // Define a shortcut for current trail item
                    var item = instance.store[i];

                    // Define a shortcut for previous trail item
                    if (i > 1) var prev = instance.store[i-1];

                    var sd = [];

                    if (i > 1 && prev.sections.length > 1) {
                        var sd = ['<div class="i-trail-item-sections">'];
                        sd.push('<ul>');
                        for (var s = 0; s < prev.sections.length; s++) {
                            var sibling = prev.sections[s];

                            if (sibling.id != item.section.id) {
                                sd.push(
                                    '<li>' +
                                        '<a href="' + instance._crumbHref(sibling.alias, prev) + '">' +
                                            '&raquo; ' + sibling.title +
                                        '</a>' +
                                    '</li>'
                                );
                            }
                        }
                        sd.push('</ul>');
                        sd.push('</div>');
                    }

                    // We append a section name (with link) as a trail item
                    crumbA.push(sd.join('') +
                        '<a href="'+instance._crumbHref(item.section.alias, prev)+'" class="i-trail-item-section">' +
                            item.section.title +
                        '</a>'
                    );

                    if (item.row) {
                        if (parseInt(item.row.id)) {
                            if (instance.store[i+1]) {

                                crumbA.push(
                                    '<a href="' + instance._crumbHref(item.section.alias, item) + '" ' +
                                        'class="i-trail-item-row-title">' +
                                        instance.breadCrumbsRowTitle(item) +
                                    '</a>'
                                );

                            } else {
                                crumbA.push(
                                    '<span class="i-trail-item-row-title">' +
                                        instance.breadCrumbsRowTitle(item) +
                                    '</span>'
                                );
                                crumbA.push('<span>' + item.action.title + '</span>');
                            }
                        } else if (item.action.alias == 'form') {
                            crumbA.push('<span>' + indi.lang.ACTION_CREATE + '</span>');
                        }
                    }
                }

                top.window.$('#i-center-north-trail').html(crumbA.join('<span> &raquo; </span>'));
                top.window.$('#i-center-north-trail a').click(function(){
                    top.window.Indi.load($(this).attr('href'));
                    return false;
                });

                top.window.$('.i-trail-item-section').hover(function(){
                    $('.i-trail-item-sections').hide();
                    if ($(this).prev().hasClass('i-trail-item-sections')) {
                        $(this).prev().css({
                            'min-width': (parseInt($(this).width()) + 21) + 'px',
                            display: 'inline-block'
                        });
                    }
                }, function(e){
                    if (parseInt(e.pageY) < parseInt($(this).offset().top) ||
                        parseInt(e.pageX) < parseInt($(this).offset().left))
                        top.window.$('.i-trail-item-sections').hide();
                });
                top.window.$('.i-trail-item-sections').mouseleave(function(){
//                    $(this).hide();
                });

            };

            /**
             * Apply the store
             *
             * @param store
             */
            this.apply = function(store){
                // Update trail data
                this.store = store;

                // Setup a 'href' property for each store's item's section object, as a shortcut, which will be used
                // in configuring urls for all system interface components, that are used for navigation
                for (var i = 0; i < this.store.length; i++)
                    this.store[i].section.href = indi.pre + '/' + this.store[i].section.alias + '/'

                // Run
                indi.action = indi.action || {};
                (indi.action.index = new indi.proto.action[indi.trail.item().action.alias]()).run();

                instance.breadCrumbs();
            }

            this.item = function(stepsUp) {
                if (typeof stepsUp == 'undefined') stepsUp = 0;
                return this.store[this.store.length - 1 - stepsUp];
            }
        }

        indi.trail = new indi.proto.trail(indi.trail);
        top.Indi.trail.store = eval(JSON.stringify(indi.trail.store));
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