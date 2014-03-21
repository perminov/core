<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
        '/library/extjs4/ext-lang-' . Indi::ini('view')->lang . '.js',
        '/js/admin/indi.js',
        '/js/admin/indi.layout.js'
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
    Indi = $.extend(Indi, {
        std: '<?=STD?>',
        com: '<?=COM ? '' : '/admin'?>',
        pre: '<?=STD?><?=COM ? '' : '/admin'?>',
        lang: <?=Indi::constants('user', true)?>,
        throwOutMsg: '<?=$this->throwOutMsg?>'
    });
    </script>
</head>
<body class="i-login"><div id="i-login-box"></div></body>
</html>