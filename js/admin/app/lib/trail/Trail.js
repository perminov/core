/**
 * Trail object. Is used to handle all levels of Indi Engine interface places hierarchy
 */
Ext.define('Indi.lib.trail.Trail', {

    // @inheritdoc
    alternateClassName: 'Indi.Trail',

    // @inheritdoc
    singleton: true,

    /**
     * Configuration
     *
     * @type {Object}
     */
    options: {
        crumbs: {
            pop: 0,
            home: false
        }
    },

    /**
     * The data array, that indi.trail will be operating with.
     * Data will be set by php's json_encode($this->trail->toArray()) call
     *
     * @type Array
     */
    store: [],

    /**
     * Prepare trail item row title for use at a part of bread crumbs
     *
     * @param item
     */
    breadCrumbsRowTitle: function(item){

        // At first, we strip newline characters, html '<br>' tags
        var title = item.row.title ?
            item.row.title.replace(/[\n\r]/g, '').replace(/<br>/g, ' ') : (item.row._title || 'No title');

        // Detect color
        var colorDetected = title.match(/color[:=][ ]*[\'"]{0,1}([#a-zA-Z0-9]+)/i);

        // Strip the html tags from title, and extract first 50 characters
        title = Indi.stripTags(title).substr(0, 50);

        // Append a color to title
        if (colorDetected) title = '<span style="color: ' + colorDetected[1] + ';">' + title + '</span>';

        // Then we check if title contains a date or datetime, and if so we check if there was format
        // params set and in that case we convert date or datetime for it to be in defined format
        if (item.fields)
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

        // Return title
        return title;
    },

    /**
     * Builds a href for a crumb's 'a' tag
     *
     * @param section Current section
     * @param hero Trail item, that is a source of additional info, that will be used while href building
     * @return {String} href
     * @private
     */
    _crumbHref: function(section, hero) {

        // All hrefs start from project's root, concatenated with a current section alias
        var href = Indi.pre + '/' + section + '/';

        // If source of a scope of additional info is provided
        if (hero) {

            // We determine an action
            href += (hero.row && hero.section.alias == section) ? 'form' : 'index';

            // We append an extra params and their values to href
            href += '/id/' + hero.row.id + '/' +
                (hero.section.primaryHash ? 'ph/' + hero.section.primaryHash + '/' : '') +
                (hero.section.rowIndex ? 'aix/' + hero.section.rowIndex + '/' : '');
        }

        // Return builded href
        return href;
    },

    /**
     * Build the breadcrumbs
     */
    breadCrumbs: function(){

        // Define an array for crumbs items
        var crumbA = [];

        // Prepend trail bread crumbs with 'Home' link, if needed
        if (this.options.crumbs.home)
            crumbA.push('<a href="' + Indi.pre + '/"><img src="' + Indi.std + '/i/admin/trl-icon-home.gif"/></a>');

        // If no trail items exist yet
        if (this.store.length == 0) {

            // Replace the current contents of #i-center-north-trail DOM node with 'Home' link
            Ext.get('i-center-north-trail').setHTML(crumbA[0]);

            // Return
            return;
        }

        // Push the first item - section group
        crumbA.push('<span class="i-trail-section-group">' + this.store[0].section.title + '</span>');

        // For each remaining trail items
        for (var i = 1; i < this.store.length; i++) {

            // Define a shortcut for current trail item
            var item = this.store[i];

            // Define a shortcut for previous trail item
            if (i > 1) var prev = this.store[i-1];

            // Define an array for html-nodes, for representing current trail item siblings
            var sd = [];

            // If this is at least second iteration within current 'for' loop, and item, that was current at
            // first iteration ( - previous item, actually) has > 1 nested sections, we start building
            // html for siblings.
            if (i > 1 && prev.sections.length > 1) {

                // Append opening '<div>' and '<ul>' tags
                var sd = ['<div class="i-trail-item-sections">'];
                sd.push('<ul>');

                // Foreach nested sections, within previous trail item
                for (var s = 0; s < prev.sections.length; s++) {

                    // Defined a shortcut for sibling
                    var sibling = prev.sections[s];

                    // If current nested section of previous trail item is not the same as section of
                    // current trail item, we add a '<ul>' tags, containing '<a>' tag. We need to do
                    // this check because there will be visual duplicates otherwise
                    if (sibling.id != item.section.id) {
                        sd.push(
                            '<li>' +
                                '<a page-href="' + this._crumbHref(sibling.alias, prev) + '">' +
                                '&raquo; ' + sibling.title +
                                '</a>' +
                                '</li>'
                        );
                    }
                }

                // Append closing '<div>' and '<ul>' tags
                sd.push('</ul>');
                sd.push('</div>');
            }

            // We append a section name (with link) as a crumb item, prepending it with html of builded
            // siblings div
            crumbA.push(sd.join('') +
                '<a page-href="'+this._crumbHref(item.section.alias, prev)+'" class="i-trail-item-section">' +
                item.section.title +
                '</a>'
            );

            // If current trail item has a row
            if (item.row) {

                // If that row has an id
                if (parseInt(item.row.id)) {

                    // Check 'form' action availability
                    var formActionIsAllowed = false;
                    for (var a = 0; a < item.actions.length; a++)
                        if (item.actions[a].alias == 'form') formActionIsAllowed = true;

                    // If current trail item is not a last item
                    if (this.store[i+1]) {

                        // If 'form' action is allowed, we append an 'a' tag, pointing to 'form' action for
                        // current trail item row
                        if (formActionIsAllowed) {
                            crumbA.push(
                                '<a page-href="' + this._crumbHref(item.section.alias, item) + '" ' +
                                    'class="i-trail-item-row-title">' +
                                    this.breadCrumbsRowTitle(item) +
                                    '</a>'
                            );

                            // Else 'form' action is not allowed, we just append current trail item row title,
                            // within 'span' tag, instead of 'a' tag
                        } else {
                            crumbA.push(
                                '<span class="i-trail-item-row-title">' +
                                    this.breadCrumbsRowTitle(item) +
                                    '</span>'
                            );
                        }

                        // Else if current trail item - is the last item
                    } else {

                        // We apend current trail item row title within 'span' tag, and append current trail
                        // item action title, by the same way
                        crumbA.push(
                            '<span class="i-trail-item-row-title">' +
                                this.breadCrumbsRowTitle(item) +
                                '</span>'
                        );
                        crumbA.push('<span>' + item.action.title + '</span>');
                    }

                    // Else if current trail item row does not have and id, and current action alias is 'form'
                } else if (item.action.alias == 'form') {

                    // We append 'form' action title, but it' version for case then new row is going to be
                    // created, hovewer, got from localization object, instead of actual action title
                    crumbA.push('<span>' + Indi.lang.I_CREATE + '</span>');
                }
            }
        }

        // If this.options.crumbs.pop is a positive integer - we pop N items from the crumbA array
        if (this.options.crumbs.pop)
            for (var i = 0; i < this.options.crumbs.pop; i++)
                crumbA.pop();

        // Reset this.options.crumbs.pop to '0'
        this.options.crumbs.pop = 0;

        // Replace the current contents of #i-center-north-trail DOM node with imploded crumbA array
        Ext.get('i-center-north-trail').setHTML(crumbA.join('<span> &raquo; </span>'));

        // Bind a click event listener to all 'a' items within imploded crumbs
        top.window.$('#i-center-north-trail a').click(function(){
            if ($(this).attr('page-href')) {
                top.window.Indi.load($(this).attr('page-href'));
                return false;
            }
        });

        // Provide and ability for .i-trail-item-section nodes to be shown and hidden then they need to be
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
                parseInt(e.pageX) < parseInt($(this).offset().left) ||
                parseInt(e.pageX) >= parseInt($(this).offset().left) + $(this).width())
                top.window.$('.i-trail-item-sections').hide();
        });
        top.window.$('.i-trail-item-sections').mouseleave(function(){
            top.window.$(this).hide();
        });
    },

    /**
     * Apply the store
     *
     * @param store
     */
    apply: function(store){

        // Reset store
        this.store = [];

        // Update store
        for (var i = 0; i < store.length; i++)
            this.store.push(Ext.create('Indi.trail.Item', Ext.merge({level: i}, store[i])));

        // Run
        try {
            var controller = Indi.app.getController(Indi.trail().section.alias);
            try {
                controller.dispatch(Indi.trail().action.alias, Indi.story[Indi.story.length-1]);
            } catch (e) {
                console.log(e.stack);
            }
        } catch (e) {
            Ext.define('Indi.controller.' + Indi.trail().section.alias, {extend: 'Indi.Controller'});
            Indi.app.getController(Indi.trail().section.alias).dispatch(Indi.trail().action.alias, Indi.story[Indi.story.length-1]);
        }
    },

    /**
     * Get the trail item
     *
     * @param stepsUp
     * @return {*}
     */
    item: function(stepsUp) {
        if (typeof stepsUp == 'undefined') stepsUp = 0;
        return this.store[this.store.length - 1 - stepsUp];
    }
});