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
     * Prepare trail item row title for use at a part of bread crumbs
     *
     * @param item
     */
    breadCrumbsRowTitle: function(item){

        // At first, we strip newline characters, html '<br>' tags
        var title = item.row._system.title ?
            item.row._system.title.toString().replace(/[\n\r]/g, '').replace(/<br>/g, ' ') : (item.row._title || 'id#' + item.row.id);

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

        // Any href starts from project's root, concatenated with a current section alias
        var href = '/' + section + '/';

        // If source of a scope of additional info is provided
        if (hero) {

            // If `row` prop exists
            if (hero.row) {

                // Append action
                href += hero.section.alias == section ? 'form' : 'index';

                // Append id
                href += '/id/' + hero.row.id + '/';

            // Else if action is not 'index'
            } else if (hero.action.alias != 'index') href += hero.action.alias + '/';

            // Append ph and aix
            href += (hero.section.primaryHash ? 'ph/' + hero.section.primaryHash + '/' : '') +
                    (hero.section.rowIndex ? 'aix/' + hero.section.rowIndex + '/' : '');
        }

        // Return built href
        return href;
    },

    /**
     * Build the breadcrumbs
     */
    breadCrumbA: function(route){
        var me = this, crumbA = [], menuItems, i, item;

        // If no trail items exist yet
        if (route.length == 0) return crumbA;

        // Push the first item - section group
        crumbA.push({
            text: route[0].section.title,
            cls: 'i-trail-section-group'
        });

        // For each remaining trail items
        for (i = 1; i < route.length; i++) {

            // Define a shortcut for current trail item
            item = route[i];

            // Define a shortcut for previous trail item
            if (i > 1) var prev = route[i-1];

            // Define an array for menu-items, for representing current trail item siblings
            menuItems = [];

            // If this is at least second iteration within current 'for' loop, and item, that was current at
            // first iteration ( - previous item, actually) has > 1 nested sections, we start building
            // html for siblings.
            if (i > 1 && prev.sections.length > 1) {

                // Foreach nested sections, within previous trail item
                for (var s = 0; s < prev.sections.length; s++) {

                    // Defined a shortcut for sibling
                    var sibling = prev.sections[s];

                    // If current nested section of previous trail item is not the same as section of
                    // current trail item, we add a '<ul>' tags, containing '<a>' tag. We need to do
                    // this check because there will be visual duplicates otherwise
                    if (sibling.id != item.section.id) {
                        menuItems.push({
                            text: ' &raquo; ' + sibling.title,
                            load: me._crumbHref(sibling.alias, prev)
                        });
                    }
                }
            }

            // We append a section name (with link) as a crumb item, prepending it with html of builded
            // siblings div
            crumbA.push({
                text: item.section.title,
                cls: 'i-trail-item-section',
                menuItems: menuItems,
                load: me._crumbHref(item.section.alias, prev)
            });

            // If current trail item has a row
            if (item.row) {

                // If that row has an id
                if (parseInt(item.row.id)) {

                    // Check 'form' action availability
                    var formActionIsAllowed = false;
                    for (var a = 0; a < item.actions.length; a++)
                        if (item.actions[a].alias == 'form') formActionIsAllowed = true;

                    // If current trail item is not a last item
                    if (route[i+1]) {

                        // If 'form' action is allowed, we append an 'a' tag, pointing to 'form' action for
                        // current trail item row
                        if (formActionIsAllowed) {
                            crumbA.push({
                                text: me.breadCrumbsRowTitle(item),
                                cls: 'i-trail-item-row-title',
                                load: this._crumbHref(item.section.alias, item)
                            });

                        // Else 'form' action is not allowed, we just append current trail item row title,
                        // within 'span' tag, instead of 'a' tag
                        } else {
                            crumbA.push({
                                text: me.breadCrumbsRowTitle(item),
                                cls: 'i-trail-item-row-title'
                            });
                        }

                    // Else if current trail item - is the last item
                    } else {

                        // We apend current trail item row title within 'span' tag, and append current trail
                        // item action title, by the same way
                        crumbA.push({
                            text: me.breadCrumbsRowTitle(item),
                            cls: 'i-trail-item-row-title',
                            load: this._crumbHref(item.section.alias, item)
                        });
                        crumbA.push({
                            text: item.action.title,
                            cls: 'i-trail-item-action'
                        });
                    }

                // Else if current trail item row does not have and id, and current action alias is 'form'
                } else if (item.action.alias == 'form') {

                    // We append 'form' action title, but it' version for case then new row is going to be
                    // created, hovewer, got from localization object, instead of actual action title
                    crumbA.push({
                        text: Indi.lang.I_CREATE,
                        cls: 'i-trail-item-action'
                    });
                }

            // Else if action is not 'index'
            } else if (item.action.alias != 'index') {

                // Push action title into crumbs
                crumbA.push({
                    text: item.action.title,
                    cls: 'i-trail-item-action i-trail-item-action-active',
                    load: this._crumbHref(item.section.alias, item)
                });
            }
        }

        return crumbA;
    },

    /**
     * Apply the store
     *
     * @param route
     */
    apply: function(scope){
        var section = scope.route.last().section.alias, action = scope.route.last().action.alias, controller;

        // Fulfil global fields storage
        scope.route.forEach(function(r, i, a) {
            if (r.fields) r.fields.forEach(function(fr, fi, fa){
                Indi.fields[fr.id] = new Indi.lib.dbtable.Row.prototype(fr);
            });
        });

        // Show loader
        Ext.get('loader').css('opacity', 1).show();

        // Try to pick up loaded controller and dispatch it's certain action
        try {

            // Get controller
            controller = Indi.app.getController(section);

            // Try dispatch needed action
            try {controller.dispatch(scope);}

            // If dispatch failed - write the stack to the console
            catch (e) {console.log(e.stack);}

        // If failed
        } catch (e) {

            // Define needed controller on-the-fly
            Ext.define('Indi.controller.' + section, {extend: 'Indi.Controller'});

            // Instantiate it, and dispatch needed action
            Indi.app.getController(section).dispatch(scope);
        }
    }
});