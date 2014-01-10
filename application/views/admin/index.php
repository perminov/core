<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Indi Engine</title>
    <!-- jQuery -->
    <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-migrate-1.1.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script>
    <!-- Ext -->
    <link type="text/css" rel="stylesheet" href="/library/extjs4/resources/css/ext-all.css"/>
    <script type="text/javascript" src="/library/extjs4/ext-all.js"></script><?$config = Indi::registry('config');?>
    <script type="text/javascript" src="/library/extjs4/ext-lang-<?=$config['view']->lang?>.js"></script>
    <!-- Indi styles -->
    <link type="text/css" rel="stylesheet" href="/css/admin/layout.css?1"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/index.css"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/form.css"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.css"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.combo.css?<?=rand(0, 10000)?>"/>
    <!-- Indi scripts -->
    <script type="text/javascript" src="/js/admin/indi.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.trail.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.form.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.filter.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.sibling.js?<?=rand(0, 10000)?>"></script>
    <!-- Std dependent styles -->
    <style>
        button span.add {
            background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/add.gif') !important;
        }
        button span.form{
            background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/form.gif') !important;
        }
        button span.delete{
            background-image: url('<?=STD?>/library/extjs4/resources/themes/images/default/shared/delete.gif') !important;
        }
        button span.back{
            background-image: url('<?=STD?>/i/admin/icon-action-back.gif') !important;
        }
        button span.save{
            background-image: url('<?=STD?>/i/admin/icon-action-save.png') !important;
        }
        button span.toggle{
            background-image: url('<?=STD?>/i/admin/icon-action-toggle.png') !important;
        }
        button span.up{
            background-image: url('<?=STD?>/i/admin/icon-action-upper.png') !important;
        }
        button span.down{
            background-image: url('<?=STD?>/i/admin/icon-action-lower.png') !important;
        }
        .i-combo .i-combo-multiple .i-combo-selected-item .i-combo-selected-item-delete{
            background-image: url(<?=STD?>/i/admin/combo-multiple-remove-item-from.png);
        }
    </style>
</head>
<body>
<script>
Ext.require(['*']); var viewport, menu, mainPanel, grid, form, loadContent, currentPanelId, locationHistory = [];
Indi.std = '<?=STD?>';
Indi.com = '<?=COM ? '' : '/admin'?>';
Indi.pre = Indi.std + Indi.com;
Indi.lang = <?=Indi::constants('user', true)?>
</script>
<?=$this->menu()?>
<?=$this->viewport()?>
</body>
</html>