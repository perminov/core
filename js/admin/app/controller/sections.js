Ext.define('Indi.controller.sections', {
    extend: 'Indi.lib.controller.Controller',
    actionsConfig: {
        index: {
            gridColumn$RowsOnPage: {editor: true},
            gridColumn$Alias: {editor: true},
            gridColumn$Title: {editor: true, resizable: false, minWidth: 200},
            rowset: {
                multiSelect: true
            },
            panelDocked$Filter$RoleIds: {allowClear: false},
            gridColumn$Alias_Renderer: function(v, s, r) {
                var pc = r.system('php-class'), pe = r.system('php-error'), pt,
                    jc = r.system('js-class'),  je = r.system('js-error'),  jt;

                // If php-controller class error detected - add tooltip
                if (pe) pt = ' title="' + pe + '" data-state="error"';

                // If js-controller class error detected - add tooltip
                if (je) jt = ' title="' + je + '" data-state="error"';

                // If php-controller file exists - append badge
                if (pc) v += '<div class="i-grid-cell-badge" data-type="exists php"' + pt +'>PHP</div>';

                // If js-controller file exists - append badge
                if (jc) v += '<div class="i-grid-cell-badge" data-type="exists js"' + jt +'>JS</div>';

                // Return value
                return v;
            },
            gridColumn$ExtendsPhp_Renderer: function(v, s, r) {
                var e = r.system('php-error'), t = ' title="' + e + '"';

                // If php-controller class error detected - wrap value in red <span> and add tooltip
                if (e) v = '<span style="color: red;"' + t + '">' + v + '</div>';

                // Return wrapped
                return v;
            },
            gridColumn$ExtendsJs_Renderer: function(v, s, r) {
                var e = r.system('js-error'), t = ' title="' + e + '"';

                // If php-controller class error detected - wrap value in red <span> and add tooltip
                if (e) v = '<span style="color: red;"' + t + '">' + v + '</div>';

                // Return wrapped
                return v;
            }
        },
        form: {
            formItem$SectionId: {
                jump: '/sections/form/id/{id}/'
            },
            formItem$EntityId: {
                jump: '/entities/form/id/{id}/'
            },
            formItem$Expand: {
                considerOn: [{
                    name: 'sectionId'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(!d.sectionId);
                    }
                }
            },
            formItem$ExpandRoles: {
                considerOn: [{
                    name: 'expand'
                },{
                    name: 'sectionId'
                }],
                listeners: {
                    considerchange: function(c, d) {
                        c.setVisible(!d.sectionId && !d.expand.match(/^(all|none)$/));
                    }
                }
            }
        }
    }
});