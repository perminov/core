/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.extraPlugins = 'oembed';
	config.allowedContent = true;
	config.oembed_maxWidth = '560';
	config.oembed_maxHeight = '315';
    config.baseFloatZIndex = 100001;
};
CKEDITOR.dtd.$removeEmpty['i'] = false;
CKEDITOR.dtd.$removeEmpty['a'] = false;

// Redefine DTD to allow block-elements inside inline-elements
CKEDITOR.dtd['a']['div'] = 1;
CKEDITOR.dtd['a']['p'] = 1;
CKEDITOR.dtd['a']['i'] = 1;
CKEDITOR.dtd['a']['span'] = 1;
