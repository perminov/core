<?php if ($this->trail->getItem()->action->alias == 'index' && $this->trail->getRequestParam('json') == 1) { ?>
	<?php echo $this->jsonData?>
<?php } else { ?>
<?php $p = '/i/admin' ?>
<?php 
$english = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$russian = array('Воскесенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html><head><title><?php echo $this->titleAdmin ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script language="javascript" src="/js/admin/index.js"></script>
<script language="javascript" src="/js/admin/calendar.js"></script>
<?php if($this->trail->getItem()->action->alias == 'index') {?>
<link href="/library/ExtJS/css/ext-all.css" media="screen" rel="stylesheet" type="text/css">
<?php } ?>
<link rel="stylesheet" href="/css/admin/index.css">
<script type="text/javascript" src="/library/ExtJS/js/jquery.js"></script>
<script type="text/javascript" src="/library/ExtJS/js/ext-jquery-adapter.js"></script>
<script type="text/javascript" src="/library/ExtJS/js/ext-all.js"></script>
<script type="text/javascript" src="/library/ExtJS/js/searchField.js"></script>
<?if($GLOBALS['cmsOnlyMode']){?><script>var cmsOnlyMode=true;</script><?}?>
<script type="text/javascript" language="javascript" src="/js/admin/autocomplete.js"></script>
<script type="text/javascript" language="javascript" src="/js/jquery.scrollTo-min.js"></script>
<script type="text/javascript" language="javascript" src="/js/admin/dselect.js"></script>
</head>
<body text="#000000" bottommargin="0" vlink="#000000" alink="#000000" link="#000000" bgcolor="#ffffff" leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table class="main" cellspacing="0" cellpadding="0" style="width:100%;" border="0" height="100%" style="display: block; margin-bottom: -7px; padding-bottom: 0px;">
<tbody>
    <tr class="white" bgcolor="#66ccff">
        <td height="53" rowspan="2"></td>
        <td colspan="3" height="34" nowrap width="375">&nbsp;<b>Welcome</b>, <?php echo $this->escape($this->admin) ?></td>
        <td align="right" height="34"><?php echo str_replace($english, $english, $this->date) ?>&nbsp;<br><a href="/admin/logout/"><font color="#ffffff">Logout</a>&nbsp;</td>
        <td height="53" rowspan="2"></td>
    </tr>
    <tr bgcolor="#66ccff"><td colspan="4" height="19"></td></tr>
    <tr id="centerTr">
        <td valign=top width="173">
            <!-- navi_menu -->
            <?php echo $this->menu()?>
            <!-- /navi_menu-->
        </td>
        <td bgcolor=#cccccc width="1"></td>
        <td bgcolor=#999999 width="1"></td>
        <td class="content" valign="top" colspan="3">
            <!-- trail-->
            <?php echo $this->trail()?>
            <!-- /trail-->
            <!-- content-->
			<br>
           	<?php $this->renderContent()?>
            <!-- /content-->
         </td>
    </tr>
    <tr bgcolor="#cccccc" height="2">
        <td><img height="2" src="<?php echo $p?>/spacer.gif" width="1"></td>
        <td></td>
        <td bgcolor="#999999"></td>
        <td colspan="3"></td>
    </tr>
    <tr bgcolor="#66ccff" height="40">
        <td colspan="6"></td>
    </tr>
    <tr height="1"><td></td><td></td><td></td><td width="375"></td><td></td><td></td></tr>
</tbody>
</table>
</basefont>
</body>
</html>
<?php } ?>