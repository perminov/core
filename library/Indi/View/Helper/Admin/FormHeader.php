<?php
class Indi_View_Helper_Admin_FormHeader {
    public function formHeader($title = null)
    {
        $title = $title ? $title : Indi::trail()->section->foreign('entityId')->title;
		ob_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Indi Engine</title>
    <?
    Indi::implode(array(
        '/library/extjs4/resources/css/ext-all.css',
        '/css/admin/indi.layout.css',
        '/css/admin/indi.combo.css',
        '/css/admin/indi.action.form.css'
    ), 'form');
    Indi::implode(array(
        '/js/jquery-1.9.1.min.js',
        '/js/jquery-migrate-1.1.1.min.js',
        '/js/jquery.scrollTo-min.js',
        '/library/extjs4/ext-all.js',
        '/library/extjs4/ext-lang-' . Indi::ini()->lang->admin . '.js',
        '/js/admin/indi.js',
        '/js/admin/indi.trail.js',
        '/js/admin/indi.combo.form.js',
        '/js/admin/indi.action.form.js',
        '/js/admin/colorpicker/colorPicker_src.js'
    ), 'form');
    ?>
    <!-- Imploded and gzipped scripts and styles -->
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.form.gz.css"/>
    <script type="text/javascript" src="/js/admin/indi.all.form.gz.js"></script>
    <!-- CK editor and finder scripts -->
    <script type="text/javascript" src="/library/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/library/ckfinder/ckfinder.js"></script>
</head>
<body class="i-action-form">
<script>
Indi = $.extend(Indi, {
    std: '<?=STD?>',
    com: '<?=COM ? '' : '/admin'?>',
    pre: '<?=STD?><?=COM ? '' : '/admin'?>',
    lang: <?=Indi::constants('user', true)?>,
    trail: <?=json_encode(Indi::trail(true)->toArray())?>,
    scope: <?=json_encode(Indi::view()->getScope())?>
});
top.Indi.scope = Indi.scope;
</script>
<?=Indi::view()->siblingCombo()?>
<form class="i-form" action="<?=(COM ? '' : '/admin') . '/' . Indi::trail()->section->alias?>/save<?=Indi::view()->row->id ? '/id/' . Indi::view()->row->id : ''?><?=Indi::uri()->ph ? '/ph/' . Indi::uri()->ph : ''?>/" name="<?=Indi::trail()->model->table()?>" method="post" enctype="multipart/form-data" row-id="<?=Indi::view()->row->id?>">
    <table cellspacing="1" border="0" width="100%" class="i-form-table">
        <tr class="i-form-subheader"><td colspan="2"><?=$title?></td></tr>
        <col width="50%"/><col width="50%"/>
        <? return ob_get_clean();
    }
}