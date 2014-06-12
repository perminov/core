<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?=Indi::ini('general')->title ? Indi::ini('general')->title : 'Indi Engine'?></title>
    <?
    Indi::implode(array(
        '/js/jquery-1.9.1.min.js',
        '/js/jquery.scrollTo-min.js',
        '/library/extjs4/ext-all.js',
        '/library/extjs4/ext-lang-' . Indi::ini()->lang->admin . '.js',
        '/js/admin/ext.override.js',
        '/js/admin/indi.js',
        '/js/admin/indi.lang.' . Indi::ini()->lang->admin . '.js',
        '/js/admin/indi.login.js'
    ) ,'login');
    Indi::implode(array(
        '/library/extjs4/resources/css/ext-all.css',
        '/css/admin/indi.layout.css'
    ), 'login');
    ?>
    <!-- Imploded and gzipped scripts and styles -->
    <script type="text/javascript" src="/js/admin/indi.all.login.gz.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/admin/indi.all.login.gz.css"/>
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
