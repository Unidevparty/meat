/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Print Plugin
 */
 

CKEDITOR.plugins.add( 'ipsoptions',
{
	init : function( editor )
	{
		var pluginName = 'ipsoptions';

		// Register the command.
		var command = editor.addCommand( pluginName, CKEDITOR.plugins.ipsoptions );

		// Register the toolbar button.
		editor.ui.addButton( 'Ipsoptions',
			{
				label : ipb.lang['ckeditor__options'],
				command : pluginName,
				icon: this.path+"images/ips_options.png"
			});
		
		editor.on( 'beforePaste', function( evt )
		{ 	
			if ( editor.config.CmdVAsPlainText === true )
			{
				evt.data['html']	= evt.data['html'].replace( /[\r\n\xA0]/g, "\n\n");
				evt.data.mode = 'text';
			}
		});
		
		/*editor.on( 'paste', function( evt )
		{
			if ( editor.config.CmdVAsPlainText !== true && typeof( evt.data['html'] ) != 'undefined' )
			{
				// Fool CKE into thinking this is MS Word text
				evt.data['html'] = '<!--class="Mso"-->' +  evt.data['html'];
			}
		}, null, null, 9);*/

		/* Strip <pre> tags in pasted text */
		editor.on( 'paste', function( evt )
		{
			if ( editor.config.CmdVAsPlainText !== true && typeof( evt.data['html'] ) != 'undefined' )
			{
				var matches = evt.data['html'].match(/<pre([^>]+?)>((\n|\r|\t|.)+?)<\/pre>/g);
				
				if ( matches )
				{
					var len = matches.length;
					var i;
	
					for ( i = 0; i < len; i++ )
					{
						evt.data['html'] = evt.data['html'].replace( matches[i], matches[i].replace( /\n/g, "<br />" ).replace( /\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;" ) );
					}
					
					evt.data['html'] = evt.data['html'].replace( /<pre([^>]+?)>/g, '' ).replace( /<\/pre>/g, '' );
				}
			}
		}, null, null, 9);
	},
	requires : [ 'clipboard' ]
} );

CKEDITOR.plugins.ipsoptions =
{
	exec : function( editor )
	{
		if ( ! Object.isUndefined( CKEDITOR.plugins.ipsoptions.popup ) )
		{
			CKEDITOR.plugins.ipsoptions.popup.kill();
		}

		var url			= ipb.textEditor.ajaxUrl + "app=core&module=ajax&section=editor&do=showSettings&secure_key=" + ipb.vars['secure_hash'];

		new Ajax.Request(	url.replace(/&amp;/g, '&'),
							{
								method:		'get',
								evalJSON:	'force',
								onSuccess:	function(t)
								{
									CKEDITOR.plugins.ipsoptions.popup	= new ipb.Popup( 'options_popup', {		type: 'pane',
																											    initial: t.responseText,
																												hideAtStart: false,
																												hideClose: false,
																												defer: false,
																												modal: true,
																												w: '260px',
																												h: '170'
																											 } );
																											 
									$('ipsEditorOptionsSave').observe('click', CKEDITOR.plugins.ipsoptions.saveIt.bindAsEventListener( editor ) );
								}
							});
	},
	
	saveIt: function( e, editor )
	{
		Event.stop(e);
		
		var url			= ipb.textEditor.ajaxUrl + "app=core&module=ajax&section=editor&do=saveSettings&secure_key=" + ipb.vars['secure_hash'];

		new Ajax.Request(	url.replace(/&amp;/g, '&'),
							{
								method:		'post',
								evalJSON:	'force',
								parameters: { 'pastePlain': $F('pastePlain'),
											  'clearSavedContent': $F('clearSavedContent') },
								onSuccess:	function(t)
								{
									if( t.responseJSON['error'] == 'nopermission' || t.responseJSON['error'] == 'no_permission' )
									{
										ipb.global.errorDialogue( ipb.lang['no_permission'] );
									}
									else
									{
										CKEDITOR.plugins.ipsoptions.popup.hide();
										
										if ( $F('clearSavedContent') == 1)
										{
											try
											{
												var s = '__last_update_stamp_' + ipb.textEditor.getCurrentEditorId();
												
												var x = $$('._as_launch').first().up('.cke_path');
												
												/* remove old */
												$$('.' + s ).invoke('remove');
												x.remove();
											}
											catch(e){}
										}
										
										ipb.global.showInlineNotification( ipb.lang['editor_prefs_updated'] );
										
										$('ipsEditorOptionsSave').stopObserving();
									}
								},
								onFailure: function( t, msg ) {}
							});

	},
	
	modes : { wysiwyg : 1, source: 1 }
};
