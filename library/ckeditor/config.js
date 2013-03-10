/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/
CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	config.uiColor = '#dfe8f6';
	config.skin = 'kama';
	$.post(window.location.pathname.replace('form', 'save') + '?check', function(access){
		if (access != 'ok') {
			config.removeDialogTabs = 'link:upload;image:Upload;flash:Upload'
		}
	});
};
