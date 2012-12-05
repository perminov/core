Ext.app.SearchField = Ext.extend(Ext.form.TriggerField, {
  initComponent : function() {
    Ext.app.SearchField.superclass.initComponent.call(this);
    this.on('specialkey', function(f, e) {
      if(e.getKey() == e.ENTER){
        this.onTriggerClick();
      }
    }, this);
  },

  validationEvent: false,
  validateOnBlur: false,
  triggerClass: 'x-form-search-trigger',
  hasSearch: false,
  paramName: 'search',

  onTriggerClick : function() {
    var v = this.getRawValue();
    if(v.length > 0){
      var o = {start: 0, limit: 25};
      this.store.baseParams = this.store.baseParams || {};
      this.store.baseParams[this.paramName] = v;
      this.store.reload({params:o});
    } else {
      this.store.baseParams[this.paramName] = '';
      this.store.reload({params:o});
    }
  }
});