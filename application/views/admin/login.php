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
	<style>.x-panel-header-text-container{text-align: center !important;}.x-panel-header-text-default{font-weight: normal !important;}</style>
</head>
<body style="background-color: #dfe8f6;">
<script>Ext.require(['*']);</script>
<script>var cmsOnlyMode = '<?=$GLOBALS['cmsOnlyMode']?>';</script>
<script>
	Ext.onReady(function() {
		Ext.create('Ext.Panel', {
			title: 'Indi Engine',
			renderTo: 'login-box',
			height: 125,
			width: 300,
			bodyPadding: 10,
			items: [
				{
					xtype: 'textfield',
					name: 'email',
					fieldLabel: 'Пользователь',
					labelWidth: 90,
					value: '',
					width: 275
				},{
					xtype: 'textfield',
					name: 'password',
					inputType: 'password',
					fieldLabel: 'Пароль',
					labelWidth: 90,
					width: 275
				},{
					xtype: 'button',
					inputType: 'submit',
					name: 'submit',
					cls: 'asd',
					text: 'Вход',
					margin: '4 0 0 20',
					width: 113,
					handler: function(){
						var data = {email: $('input[name=email]').val(), password: $('input[name=password]').val(), enter: true}
						$.post(cmsOnlyMode?'/':'/admin/', data, function(response){
							if (response.error) {
								Ext.MessageBox.show({
									title: 'Ошибка',
									msg: response.error,
									buttons: Ext.MessageBox.OK,
									icon: Ext.MessageBox.ERROR
								});
							} else if (response.ok) {
								window.location.replace(cmsOnlyMode?'/':'/admin/');
							}
							console.log(response);
						}, 'json');
					}
				},{
					xtype: 'button',
					inputType: 'reset',
					name: 'reset',
					cls: 'asd',
					text: 'Сброс',
					margin: '4 0 0 10',
					width: 113,
					handler: function(){
						$('input[name=password]').val('');
						$('input[name=email]').val('');
					}
				}
			]
		});
	});
</script>
<div id="login-box" style="width: 300px;
height: 125px;
position: absolute;
top: 50%;
margin-top: -100px;
left: 50%;
margin-left: -150px;"></div>
</body>
</html>