Ext.define('Indi.controller.config', {
    extend: 'Indi.Controller',
    actionsConfig: {
        index: {
            rowset: {
                viewConfig: {
                    getRowClass: function(record) {
                        return 'i-grid-row-' + record.key('expiryType');
                    }
                }
            },
            gridColumn$Alias: {tdCls: 'i-grid-cell-color-lightgray'},
            gridColumn$ElementId: {tdCls: 'i-grid-cell-color-lightgray'},
            gridColumn$DefaultValue: {tdCls: 'i-grid-cell-color-lightgray'},
            gridColumn$ExpiryStart: {tdCls: 'i-grid-cell-color-lightgray'},
            gridColumn$ExpiryType: {tdCls: 'i-grid-cell-color-lightgray'},
            gridColumn$ExpiryDuration: {tdCls: 'i-grid-cell-color-lightgray', cls: 'i-grid-column-span-right'},
            gridColumn$ExpiryDurationStr: {tdCls: 'i-grid-cell-color-red'},
            gridColumn$ExpiryMeasure: {tdCls: 'i-grid-cell-color-lightgray'}
        },
        form: {
            formItemA: function() {
                var me = this, autoItemXA = me.ti().row.view('elementId').store.data.column('attrs').column('alias'),
                    itemA = me.callParent(), i, t, fieldI, autoTypeO = {default: [], current: []}, at, selected = {};

                // Detect current control
                if (me.ti().row.id) {
                    selected.index = me.ti().row.view('elementId').store.ids.indexOf(me.ti().row.elementId);
                    selected.control = me.ti().row.view('elementId').store.data[selected.index].attrs.alias;
                }

                // For each type (e.g. 'default' and 'current')
                for (t in autoTypeO) {

                    // Detect injection index
                    at = 0; itemA.forEach(function(v, i){ if (v.name == t + 'Value') at = i; });

                    // For each control element
                    for (i = 0; i < autoItemXA.length; i++) {

                        // Default field cfg
                        fieldI = {
                            alias: t + 'Value_' + autoItemXA[i],
                            title: itemA[at].fieldLabel,
                            satellite: '0',
                            _foreign: {
                                elementId: {alias: autoItemXA[i]}
                            }
                        };

                        // Number-specific field config
                        if (autoItemXA[i] == 'number') Ext.merge(fieldI, {
                            params: {
                                measure: '',
                                maxLength: 5
                            }
                        });

                        // Set up a new pseudo-property within me.ti().row, for it to be picked by formItemX<control> fn
                        if (autoItemXA[i] == selected.control) me.ti().row[t + 'Value_' + autoItemXA[i]] = me.ti().row[t + 'Value'];

                        // Push field
                        autoTypeO[t].push(new Indi.lib.dbtable.Row.prototype(fieldI));
                    }

                    // Convert fields array into their config array
                    autoTypeO[t] = me.callParent([autoTypeO[t]]);

                    // Inject into original array
                    for (i = 0; i < autoTypeO[t].length; i++) itemA.splice(i + at + 1, 0, autoTypeO[t][i]);
                }

                // Return
                return itemA;
            },
            formItemFly: function(control, kind) {
                var me = this, considerOn = [{
                    name: 'elementId'
                }];

                if (kind == 'current' && !me.ti().row.id) considerOn.push({
                    name: 'defaultValue_' + control
                });

                return {
                    considerOn: considerOn,
                    listeners: {
                        enablebysatellite: function(c, d) {
                            c.setVisible(d.elementId && c.sbl('elementId').prop('alias') == control);
                            if (kind == 'current' && !me.ti().row.id) c.setValue(d['defaultValue_' + control]);
                        }
                    }
                }
            },
            formItem$DefaultValue: {
                hidden: true,
                considerOn: [{
                    name: 'elementId'
                }, {
                    name: 'defaultValue_check',
                    required: false
                }, {
                    name: 'defaultValue_string',
                    required: false
                }, {
                    name: 'defaultValue_number',
                    required: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.val(d.elementId ? c.sbl('defaultValue_' + c.sbl('elementId').prop('alias')).getSubmitValue() + '' : '');
                    }
                }
            },
            formItem$CurrentValue: {
                hidden: true,
                considerOn: [{
                    name: 'elementId'
                }, {
                    name: 'currentValue_check',
                    required: false
                }, {
                    name: 'currentValue_string',
                    required: false
                }, {
                    name: 'currentValue_number',
                    required: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.val(d.elementId ? c.sbl('currentValue_' + c.sbl('elementId').prop('alias')).getSubmitValue() + '' : '');
                    }
                }
            },
            formItem$DefaultValue_string: function() {
                return this.formItemFly('string', 'default');
            },
            formItem$DefaultValue_number: function() {
                return this.formItemFly('number', 'default');
            },
            formItem$DefaultValue_check: function() {
                return this.formItemFly('check', 'default');
            },
            formItem$CurrentValue_string: function() {
                return this.formItemFly('string', 'current');
            },
            formItem$CurrentValue_number: function() {
                return this.formItemFly('number', 'current');
            },
            formItem$CurrentValue_check: function() {
                return this.formItemFly('check', 'current');
            },
            formItem$Value: {
                considerOn: [{
                    name: 'elementId'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.elementId);
                    }
                }
            },
            formItem$Expiry: {
                considerOn: [{
                    name: 'elementId'
                }, {
                    name: 'currentValue'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.elementId);
                    }
                }
            },
            formItem$ExpiryStart: {
                considerOn: [{
                    name: 'elementId'
                },{
                    name: 'expiryType'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.elementId && d.expiryType == 'temporary');
                        if (d.expiryType == 'temporary') c.val(new Date()); else c.reset();
                    }
                }
            },
            formItem$ExpiryType: {
                considerOn: [{
                    name: 'elementId'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        c.setVisible(d.elementId);
                    }
                }
            },
            formItem$ExpiryDuration: {
                considerOn: [{
                    name: 'elementId',
                    clear: false
                }, {
                    name: 'expiryType',
                    clear: false
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        if (!d.elementId) c.hide(); else c.setVisible(d.expiryType == 'temporary');
                    }
                }
            },
            formItem$ExpiryMeasure: {
                considerOn: [{
                    name: 'elementId'
                }, {
                    name: 'expiryType'
                }],
                listeners: {
                    enablebysatellite: function(c, d) {
                        if (!d.elementId) c.hide(); else c.setVisible(d.expiryType == 'temporary');
                    }
                }
            }
        }
    }
});