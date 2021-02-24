/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Paste as plain text plugin
 */

(function()
{
	// The pastetext command definition.
	var ipscodeCmd =
	{
		exec : function( editor )
		{
			var html	   = IPSCKTools.getSelectionHtml( editor );
			var newElement = new CKEDITOR.dom.element('span');
			
			/* @link http://community.invisionpower.com/resources/bugs.html/_/ip-board/ie8-and-code-tags-r41549 */
			if ( CKEDITOR.env.ie )
			{
				if( html.replace(/<p data-cke([^>]+?)>&nbsp;<\/p>/ig, '').trim() == '' )
				{
					html = '';
				}
			}

			if ( html != '' )
			{
				html = html.replace( /<\/div><div([^>]+?)?>/g, "\n" );
				html = html.replace( /(<br([^>]+?)?>)?<\/p><p([^>]+?)?>/g, "\n" );
				html = html.replace( /<br([^>]+?)?>/g, "\n" ).stripTags();
				
				html = IPSCKTools.cleanHtmlForTagWrap( html );
				
				editor.insertHtml( '<p></p><pre class="_prettyXprint">' + html + "</pre><p></p>" );
			}
			else
			{
				editor.openDialog( 'ipscode' );
			}
			
			return true;
		}
	};

	// Register the plugin.
	CKEDITOR.plugins.add( 'ipscode',
	{
		init : function( editor )
		{
			var commandName = 'ipscode',
				command = editor.addCommand( commandName, ipscodeCmd );

			editor.ui.addButton( 'Ipscode',
			{
					label : ipb.lang['ckeditor__codelabel'],
					command : commandName,
					icon: this.path+"images/code.png"
			});

			CKEDITOR.dialog.add( commandName, CKEDITOR.getUrl( this.path + 'dialogs/ipscode.js' ) );
			
			if ( editor.config.forcePasteAsPlainText )
			{
				// Intercept the default pasting process.
				editor.on( 'beforeCommandExec', function ( evt )
				{
					var mode = evt.data.commandData;
					// Do NOT overwrite if HTML format is explicitly requested.
					if ( evt.data.name == 'paste' && mode != 'html' )
					{
						editor.execCommand( 'pastetext' );
						evt.cancel();
					}
				}, null, null, 0 );
				
				editor.on( 'beforePaste', function( evt )
				{
					evt.data.mode = 'text';
				});
			}			
			editor.on( 'pasteState', function( evt )
			{
				editor.getCommand( 'pastetext' ).setState( evt.data );
			});
		},

		requires : [ 'clipboard' ]
	});

})();