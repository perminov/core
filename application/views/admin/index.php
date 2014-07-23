<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=Indi::ini('general')->title ? Indi::ini('general')->title : 'Indi Engine'?></title>
    <?
    Indi::implode(array(
        '/js/jquery-1.9.1.min.js',
        '/library/extjs4/ext-all.js',
        //'/library/extjs4/ext-debug.js',
        '/library/extjs4/ext-lang-' . Indi::ini()->lang->admin . '.js',
        '/js/admin/ext.override.js',
        '/js/admin/indi.js',
        '/js/admin/indi.ux.js',
        '/js/admin/indi.lang.' . Indi::ini()->lang->admin . '.js',
        '/js/admin/indi.viewport.js',
        '/js/admin/indi.trail.js',
        '/js/admin/indi.controller.action.js',
        //'/js/admin/app/controller/entities.js',
        '/js/admin/indi.combo.form.js',
        '/js/admin/indi.combo.filter.js',
        /*'/js/admin/indi.combo.filter.js',
        '/js/admin/indi.combo.sibling.js',
        '/js/admin/indi.action.index.js'*/
    ) ,'index');
    Indi::implode(array(
        '/library/extjs4/resources/css/ext-all.css',
        '/css/admin/indi.layout.css',
        '/css/admin/indi.action.form.css',
        '/css/admin/indi.trail.css',
        '/css/admin/indi.combo.css'
    ), 'index');
    ?>
    <!-- Imploded and gzipped scripts and styles -->
    <script type="text/javascript" src="/js/admin/indi.all.index.gz.js"></script>
    <script type="text/javascript" src="/library/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/library/ckfinder/ckfinder.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.index.gz.css"/>
</head>
<body id="body">
<script>
Ext.require(['*']);
Ext.create('Indi', {
    statics: {
        std: '<?=STD?>',
        com: '<?=COM ? '' : '/admin'?>',
        pre: '<?=STD?><?=COM ? '' : '/admin'?>',
        uri: <?=json_encode(Indi::uri()->toArray())?>,
        time: <?=time()?>,
        menu: <?=json_encode($this->menu)?>,
        user: '<?=$this->admin?>',
        home: <?=Indi::admin()->foreign('profileId')->home ? 'true' : 'false'?>
    }
});
</script>
<div style="display: none;"><div id="i-section-index-action-index-content"><?=$this->render('index/index.php');?></div></div>
</body>
</html>