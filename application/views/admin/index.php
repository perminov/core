<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=Indi::ini('general')->title ? Indi::ini('general')->title : 'Indi Engine'?></title>
    <?
    Indi::implode(array(
        '/js/jquery-1.9.1.min.js',
        '/library/extjs4/ext-all.js',
        '/library/extjs4/ext-lang-' . Indi::ini()->lang->admin . '.js',
        '/library/extjs4/examples/ux/BoxReorderer.js',
        '/library/extjs4/examples/ux/TabReorderer.js',
        '/library/extjs4/examples/ux/CheckColumn.js',

        '/js/admin/app/override/Ext.Base.js',
        '/js/admin/app/override/Ext.data.Connection.js',
        '/js/admin/app/override/Ext.dom.Element.js',
        '/js/admin/app/override/Ext.dom.CompositeElementLite.js',
        '/js/admin/app/override/Ext.button.Button.js',
        '/js/admin/app/override/Ext.tip.ToolTip.js',
        '/js/admin/app/override/Ext.Component.js',
        '/js/admin/app/override/Ext.form.action.Submit.js',
        '/js/admin/app/override/Ext.form.field.Base.js',
        '/js/admin/app/override/Ext.form.field.Number.js',
        '/js/admin/app/override/Ext.form.field.Date.js',
        '/js/admin/app/override/Ext.picker.Date.js',
        '/js/admin/app/override/Ext.form.field.Checkbox.js',
        '/js/admin/app/override/Ext.grid.feature.Summary.js',
        '/js/admin/app/override/Ext.grid.View.js',
        '/js/admin/app/override/Ext.data.Model.js',
        '/js/admin/app/override/Ext.tab.Bar.js',
        '/js/admin/app/override/Ext.panel.Panel.js',
        '/js/admin/app/override/Ext.toolbar.Toolbar.js',
        '/js/admin/app/override/Ext.form.Panel.js',
        '/js/admin/app/override/Ext.grid.header.Container.js',
        '/js/admin/app/override/Ext.grid.Panel.js',
        '/js/admin/app/override/Ext.tab.Panel.js',
        '/js/admin/app/override/Ext.grid.plugin.Editing.js',
        '/js/admin/app/override/Ext.grid.column.Column.js',

        '/js/admin/app/ux/Ext.ux.form.field.plugin.InputMask.js',

        '/js/admin/indi.js',
        '/application/lang/admin/' . Indi::ini()->lang->admin . '.php:Indi.lang',

        '/js/admin/app/util/Shrinkable.js',

        '/js/admin/app/view/LoginBox.js',
        '/js/admin/app/view/Menu.js',
        '/js/admin/app/view/Viewport.js',
        '/js/admin/app/view/desktop/Window.js',
        '/js/admin/app/view/desktop/WindowButton.js',
        '/js/admin/app/view/desktop/WindowBar.js',
        '/js/admin/app/view/desktop/TaskBar.js',

        '/js/admin/app/lib/chart/HighStock.js',
        '/js/admin/app/lib/chart/HighStockSerie.js',

        '/js/admin/app/lib/view/action/south/South.js',
        '/js/admin/app/lib/view/action/south/Row.js',
        '/js/admin/app/lib/view/action/south/Rowset.js',
        '/js/admin/app/lib/view/action/Panel.js',
        '/js/admin/app/lib/view/action/Rowset.js',
        '/js/admin/app/lib/view/action/Row.js',
        '/js/admin/app/lib/view/action/Tab.js',
        '/js/admin/app/lib/view/action/TabRowset.js',
        '/js/admin/app/lib/view/action/TabRow.js',

        '/js/admin/app/lib/trail/Trail.js',
        '/js/admin/app/lib/trail/Item.js',
        '/js/admin/app/lib/dbtable/Row.js',
        '/js/admin/app/lib/view/ShrinkList.js',
        '/js/admin/app/lib/form/field/Combo.js',
        '/js/admin/app/lib/toolbar/Info.js',
        '/js/admin/app/lib/toolbar/Filter.js',
        '/js/admin/app/lib/form/field/SiblingCombo.js',
        '/js/admin/app/lib/form/field/AutoCombo.js',
        '/js/admin/app/lib/form/field/FilterCombo.js',
        '/js/admin/app/lib/form/field/CkEditor.js',
        '/js/admin/app/lib/form/field/FilePanel.js',
        '/js/admin/app/lib/form/field/Radios.js',
        '/js/admin/app/lib/form/field/MultiCheck.js',
        '/js/admin/app/lib/form/field/Phone.js',
        '/js/admin/app/lib/form/field/TimeSpan.js',
        '/js/admin/app/lib/form/field/Color.js',

        '/js/admin/app/lib/form/field/Time.js',
        '/js/admin/app/lib/picker/DateTime.js',
        '/js/admin/app/lib/picker/Color.js',
        '/js/admin/app/lib/form/field/DateTime.js',

        '/js/admin/app/lib/controller/Controller.js',
        '/js/admin/app/lib/controller/action/Action.js',
        '/js/admin/app/lib/controller/action/Rowset.js',
        '/js/admin/app/lib/controller/action/Grid.js',
        '/js/admin/app/lib/controller/action/Chart.js',
        '/js/admin/app/lib/controller/action/ChangeLog.js',
        '/js/admin/app/lib/controller/action/Row.js',
        '/js/admin/app/lib/controller/action/Form.js',
        '/js/admin/app/lib/controller/action/Print.js'
    ));
    Indi::implode(array(
        '/library/extjs4/resources/css/ext-all.css',
        '/library/extjs4/examples/ux/css/CheckHeader.css',
        '/library/extjs4/resources/css/colorpicker.css',
        '/css/admin/indi.all.css',
        '/css/admin/indi.all.default.css',
        '/css/admin/indi.layout.css',
        '/css/admin/indi.action.form.css',
        '/css/admin/indi.trail.css',
        '/css/admin/indi.combo.css',
        '/css/admin/indi.combo.default.css'
    ));
    ?>
    <script type="text/javascript" src="/library/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/library/ckfinder/ckfinder.js"></script>
    <!-- Imploded and gzipped scripts and styles -->
    <script type="text/javascript" src="/js/admin/indi.all.gz.js"></script>
    <script type="text/javascript" src="/library/Highstock-2.1.9/js/highstock.src.js"></script>
    <script src="/library/Highstock-2.1.9/current-price-indicator.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.gz.css"/>
</head>
<body id="body">
<script>
Ext.require(['*']);
Ext.create('Indi', {
    statics: {
        std: '<?=STD?>',
        com: '<?=COM ? '' : '/admin'?>',
        pre: '<?=PRE?>',
        uri: <?=json_encode(Indi::uri()->toArray())?>,
        time: <?=time()?>,
        menu: <?=json_encode($this->menu)?>,
        user: {
            title: '<?=$this->admin?>',
            dashboard: <?=($d=Indi::admin()->foreign('profileId')->dashboard) ? '\'' . $d . '\'': 'false'?>
        }
    }
});
</script>
<div style="display: none;">
    <div id="i-section-index-action-index-content"><?=$this->render('index/index.php');?></div>
    <div id="i-response-html"></div>
</div>
</body>
</html>