<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?=Indi::ini('general')->title ?: 'Indi Engine'?></title>
    <?$this->other('gz')?>
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