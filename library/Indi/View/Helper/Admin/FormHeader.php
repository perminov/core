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
    <link rel="stylesheet" type="text/css" href="/library/extjs4/resources/css/ext-all.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin/layout.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin/index.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin/form.css" />
    <link rel="stylesheet" type="text/css" href="/css/admin/combo.css" />
    <script type="text/javascript" src="/library/extjs4/ext-all.js"></script>
    <?$config = Indi_Registry::get('config');?>
    <script type="text/javascript" src="/library/extjs4/ext-lang-<?=$config['view']->lang?>.js"></script>
    <script type="text/javascript" src="/js/admin/index.js"></script>
    <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery-migrate-1.1.1.min.js"></script>
    <script type="text/javascript" src="/js/admin/dselect.js"></script>
    <script type="text/javascript" src="/js/admin/combo.js?<?=rand(0, 10000)?>"></script>
    <script type="text/javascript" src="/js/admin/autocomplete.js"></script>
    <script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script>
    <script type="text/javascript" src="/library/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/library/ckfinder/ckfinder.js"></script>
    <style>
        span.radio{
            background: url(<?=$_SERVER['STD']?>/i/admin/radio.png) no-repeat;
        }
        span.radio.checked{
            background: url(<?=$_SERVER['STD']?>/i/admin/radioChecked.png) no-repeat;
        }
        span.checkbox{
            background: url(<?=$_SERVER['STD']?>/i/admin/checkbox.png) no-repeat;
        }
        span.checkbox.checked{
            background: url(<?=$_SERVER['STD']?>/i/admin/checkboxChecked.png) no-repeat;
        }
        controls.upload{
            background: url(<?=$_SERVER['STD']?>/i/admin/transparentBg.png);
        }
    </style>
</head>
<body>
<script>window.cmsOnlyMode='<?=$GLOBALS['cmsOnlyMode']?>';</script>
<script>Ext.require(['*']);</script>
<script>top.window.$('#trail').html('<?=str_replace("'", "\'", $this->view->trail())?>')</script>
<script>
var STD = '<?=$_SERVER['STD']?>';
var COM = '<?=$GLOBALS['cmsOnlyMode'] ? '' : '/admin'?>';
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
<form class="form" action="../<?=$this->view->row->id ? '../../' : ''?>save/<?=$this->view->row->id ? 'id/' . $this->view->row->id . '/' : ''?>"	name="<?=$this->view->entity->table?>" method="post" enctype="multipart/form-data">
	<table celpadding="2" cellspacing="1" border="0" width="100%">
		<tr class="table_topics"><td colspan="2" align="center" class="table_topics"><?=$title?></td></tr>
		<col width="50%"/><col width="50%"/>
		<? $xhtml = ob_get_clean();
        return $xhtml;
    }
}