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
	<link rel="stylesheet" type="text/css" href="/css/admin/general.css" />
	<script type="text/javascript" src="/library/extjs4/ext-all.js"></script>
	<script type="text/javascript" src="/js/admin/index.js"></script>
	<script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="/js/admin/dselect.js"></script>
	<script type="text/javascript" src="/js/admin/autocomplete.js"></script>
	<script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script>
</head>
<body>
<script>window.cmsOnlyMode='<?=$GLOBALS['cmsOnlyMode']?>';</script>
<script>Ext.require(['*']);</script>
<script>window.parent.$('#trail').html('<?=str_replace("'", "\'", $this->view->trail())?>')</script>
<form class="form" action="../<?=$this->view->row->id ? '../../' : ''?>save/<?=$this->view->row->id ? 'id/' . $this->view->row->id . '/' : ''?>"	name="<?=$this->view->entity->table?>" method="post" enctype="multipart/form-data">
	<table celpadding="2" cellspacing="1" border="0" width="100%">
		<tr class="table_topics"><td colspan="2" align="center" class="table_topics"><?=$title?></td></tr>
		<col width="50%"/><col width="50%"/>
		<? $xhtml = ob_get_clean();
        return $xhtml;
    }
}