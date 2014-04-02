/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	$.post(window.location.pathname.replace('form', 'save') + '?check=1', function(access){
		if (access != 'ok') {
			config.removeDialogTabs = 'link:upload;image:Upload;flash:Upload'
		}
	});
	config.extraPlugins = 'oembed';
	config.allowedContent = true;
	config.oembed_maxWidth = '560';
	config.oembed_maxHeight = '315';

};
