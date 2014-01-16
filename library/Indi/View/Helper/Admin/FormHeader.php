<?php
class Indi_View_Helper_Admin_FormHeader extends Indi_View_Helper_Abstract
{
    public function formHeader($title = null)
    {
        $title = $title ? $title : $this->view->entity->title;
		ob_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Indi Engine</title>
    <!-- jQuery -->
    <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-migrate-1.1.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.js"></script>
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
    <script type="text/javascript" src="/js/admin/indi.action.form.js?<?=rand(0, 10000)?>"></script>
    <!-- CK editor and finder scripts -->
    <script type="text/javascript" src="/library/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/library/ckfinder/ckfinder.js"></script>
    <!-- STD dependent styles -->
    <?=$this->view->styleStd()?>
</head>
<body class="i-action-form">
<script>window.cmsOnlyMode='<?=COM?>';</script>
<script>Ext.require(['*']);</script>
<script>
Indi.std = '<?=STD?>';
Indi.com = '<?=COM ? '' : '/admin'?>';
Indi.pre = Indi.std + Indi.com;
Indi.lang = top.window.Indi.lang;
//top.window.Indi.shareWith(window);
Indi.trail = <?=json_encode($this->view->trail->toArray())?>;
Indi.scope = <?=json_encode($this->view->getScope())?>;
top.Indi.scope = Indi.scope;
</script>
<script>top.window.$('#trail').html('<?=str_replace("'", "\'", $this->view->trail())?>')</script>
<script>
var STD = '<?=STD?>';
var COM = '<?=COM ? '' : '/admin'?>';
var PRE = STD+COM;
    Ext.onReady(function(){
        top.window.$('.trail-item-section').hover(function(){
            top.window.$('.trail-siblings').hide();
            var itemIndex = $(this).attr('item-index');
            var width = (parseInt($(this).width()) + 27);
            if (top.window.$('#trail-item-' + itemIndex + '-sections ul li').length) {
                top.window.$('#trail-item-' + itemIndex + '-sections').css('min-width', width + 'px');
                top.window.$('#trail-item-' + itemIndex + '-sections').css('display', 'inline-block');
            }
        }, function(){
            if (parseInt(top.window.event.pageY) < parseInt($(this).offset().top) || parseInt(top.window.event.pageX) < parseInt($(this).offset().left)) top.window.$('.trail-siblings').hide();
        });
        top.window.$('.trail-siblings').mouseleave(function(){
            $(this).hide();
        });
    })
</script>
<?=$this->view->siblingCombo()?>
<form class="form row-form" action="<?=PRE?>/<?=$this->view->section->alias?>/save<?=$this->view->row->id ? '/id/' . $this->view->row->id : ''?>/" name="<?=$this->view->entity->table?>" method="post" enctype="multipart/form-data" row-id="<?=$this->view->row->id?>">
    <table celpadding="2" cellspacing="1" border="0" width="100%">
        <tr class="table_topics"><td colspan="2" align="center" class="table_topics"><?=$title?></td></tr>
        <col width="50%"/><col width="50%"/>
        <? return ob_get_clean();
    }
}