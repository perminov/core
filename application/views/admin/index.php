<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Indi Engine</title>
    <?
    Indi::implode(array(
        '/js/jquery-1.9.1.min.js',
        '/js/jquery-migrate-1.1.1.min.js',
        '/js/jquery.scrollTo-min.js',
        '/library/extjs4/ext-all.js',
        '/library/extjs4/ext-lang-' . Indi::ini('lang')->admin . '.js',
        '/js/admin/indi.js',
        '/js/admin/indi.layout.js',
        '/js/admin/indi.trail.js',
        '/js/admin/indi.combo.form.js',
        '/js/admin/indi.combo.filter.js',
        '/js/admin/indi.combo.sibling.js',
        '/js/admin/indi.action.index.js'
    ) ,'index');
    Indi::implode(array(
        '/library/extjs4/resources/css/ext-all.css',
        '/css/admin/indi.layout.css',
        '/css/admin/indi.trail.css',
        '/css/admin/indi.combo.css'
    ), 'index');
    ?>
    <!-- Imploded and gzipped scripts and styles -->
    <script type="text/javascript" src="/js/admin/indi.all.index.gz.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.index.gz.css"/>
</head>
<body>
<script>
Ext.require(['*']);
Indi = $.extend(Indi, {
    std: '<?=STD?>',
    com: '<?=COM ? '' : '/admin'?>',
    pre: '<?=STD?><?=COM ? '' : '/admin'?>',
    lang: <?=Indi::constants('user', true)?>,
    time: <?=time()?>
});
Indi.ready(function(){
    Indi.layout.menu.data = <?=json_encode($this->menu)?>;
    Indi.layout.adminInfo = '<?=$this->admin?>';
}, 'layout');
</script>
</body>
</html>