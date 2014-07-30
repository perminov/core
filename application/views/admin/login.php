<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=Indi::ini('general')->title ? Indi::ini('general')->title : 'Indi Engine'?></title>
    <!-- Imploded and gzipped scripts and styles -->
    <script type="text/javascript" src="/js/admin/indi.all.index.gz.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.index.gz.css"/>
    <script>
    Ext.create('Indi', {
        statics: {
            std: '<?=STD?>',
            com: '<?=COM ? '' : '/admin'?>',
            pre: '<?=STD?><?=COM ? '' : '/admin'?>',
            uri: <?=json_encode(Indi::uri()->toArray())?>,
            title: '<?=Indi::ini('general')->title ? Indi::ini('general')->title : 'Indi Engine'?>',
            throwOutMsg: '<?=$this->throwOutMsg?>'
        }
    });
    </script>
</head>
<body class="i-login"><div id="i-login-box"></div></body>
</html>
<?//=Indi::lang('js');?>
