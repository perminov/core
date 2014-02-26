<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <script type="text/javascript" src="/library/extjs4/ext-all.js"></script>
    <script type="text/javascript" src="/library/extjs4/ext-lang-<?=Indi::registry('config')->view->lang?>.js"></script>
    <!-- Indi styles -->
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.layout.css?<?=rand(0, 10000)?>"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.trail.css?<?=rand(0, 10000)?>"/>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.combo.css?<?=rand(0, 10000)?>"/>
    <!-- Indi scripts -->
    <script type="text/javascript" src="/js/admin/indi.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.layout.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.trail.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.form.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.filter.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.combo.sibling.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/indi.action.index.js?<?=rand(0, 10000)?>"></script>
    <!-- STD dependent styles -->
    <?=$this->styleStd()?>
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
    Indi.layout.menu.data = <?=json_encode($this->menu->toArray())?>;
    Indi.layout.adminInfo = '<?=$this->admin?>';
}, 'layout');
</script>
</body>
</html>