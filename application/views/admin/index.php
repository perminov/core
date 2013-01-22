<?if($this->trail->requestParams['section'] == 'index' && $this->trail->requestParams['action'] == 'index'){?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>Border Layout Example</title>
	<link rel="stylesheet" type="text/css" href="/library/extjs4/resources/css/ext-all.css" />
	<link rel="stylesheet" type="text/css" href="/css/admin/layout.css" />
	<link rel="stylesheet" type="text/css" href="/css/admin/index.css" />
	<link rel="stylesheet" type="text/css" href="/css/admin/form.css" />
	<link rel="stylesheet" type="text/css" href="/css/admin/general.css" />
	<script type="text/javascript" src="/library/extjs4/ext-all.js"></script>
	<script type="text/javascript" src="/js/admin/index.js"></script>
	<script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
</head>
<body>
<script>Ext.require(['*']); var viewport, menu, createGrid, grid, createForm, form, currentPanelId;</script>
<?=$this->menu()?>
<?=$this->viewport()?>
<?=$this->grid()?>
<?=$this->form()?>
<div id="form"></div>
</body>
</html>

<?} else if ($this->jsonData){
	echo $this->jsonData;
} else if ($this->trail->requestParams['action'] == 'index') {
	echo json_encode($this->gridColumns());
}?>
