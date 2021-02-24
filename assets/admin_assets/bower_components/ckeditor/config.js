/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */


CKEDITOR.plugins.add('nbsp',{
  icon: '/assets/admin_assets/img/nbsp.png',
  init : function( editor ) {
    editor.addCommand('clean_nbsp', {
        exec: function( editor ) {
            var data = editor.getData().replace(/&nbsp;/g, ' ');
            editor.setData(data);
        }
    });
    // do I have to add the button here again? Seems to have no effect
    editor.ui.addButton('Clean nbsp', {
        label: 'Удалить &amp;nbsp;',
        command: 'clean_nbsp',
        toolbar: 'basicstyles,0',
  		icon: '/assets/admin_assets/img/nbsp.png'
    });
    console.debug(editor);
  }
});


CKEDITOR.plugins.add('marked_list',{
  icon: '/assets/admin_assets/img/round.png',
  init : function( editor ) {
    editor.addCommand('marked_list', {
        exec: function( editor ) {
            editor.insertHtml('<ul class="marked-list"><li></li></ul>');
        }
    });
    // do I have to add the button here again? Seems to have no effect
    editor.ui.addButton('Ul', {
        label: 'Маркированный список',
        command: 'marked_list',
        toolbar: 'basicstyles,1',
  		icon: '/assets/admin_assets/img/round.png'
    });
    console.debug(editor);
  }
});

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	config.extraPlugins = 'image2,justify,youtube,nbsp,marked_list,lightbox';

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = '';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	config.contentsCss = '/assets/admin_assets/CKE.css';
};
CKEDITOR.on('dialogDefinition', function( ev ) {
    // Take the dialog name and its definition from the event
    // data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    // Check if the definition is from the dialog we're
    // interested on (the Link and Image dialog).
    if ( dialogName == 'link' || dialogName == 'image' || dialogName == 'image2' ) {
        // remove Upload tab
        dialogDefinition.removeContents( 'Upload' );
    }
});
CKEDITOR.config.language = 'ru';
CKEDITOR.config.extraPlugins = 'lightbox';
CKEDITOR.config.extraAllowedContent = 'a[data-fancybox,data-caption,data-fancybox-saved]';
