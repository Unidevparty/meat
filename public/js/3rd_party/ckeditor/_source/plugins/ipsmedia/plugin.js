/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file IPS Shared Media
 */

CKEDITOR.plugins.add( 'ipsmedia',
{
	init : function( editor )
	{
		var pluginName = 'ipsmedia';

		// Register the command.
		var command = editor.addCommand( pluginName, CKEDITOR.plugins.ipsmedia );
		
		if ( inACP )
		{
			ipb.vars['_base_url']    = ipb.vars['front_url'];
			ipb.vars['_secure_hash'] = ipb.vars['md5_hash'];
		}
		else
		{
			ipb.vars['_base_url']    = ipb.vars['base_url'];
			ipb.vars['_secure_hash'] = ipb.vars['secure_hash'];
		}
		
		if( ipb.vars['member_id'] )
		{
			// Register the toolbar button.
			editor.ui.addButton( 'Ipsmedia',
				{
					label : ipb.lang['ckeditor__mymedia'],
					command : pluginName,
					icon: this.path+"images/mymedia.png"
				});
		}
	}
} );

CKEDITOR.plugins.ipsmedia =
{
	loadTab: function( app, plugin )
	{
		$$("#mymedia_tabs li").each( function(elem) {
			$(elem).removeClassName('active');
		});

		$(app + '_' + plugin).addClassName('active');
		$('mymedia_toolbar').show();
		
		$('sharedmedia_search_app').value		= app;
		$('sharedmedia_search_plugin').value	= plugin;
		
		var searchstring	= $('sharedmedia_search').value;
		
		if( searchstring == ipb.vars['sm_init_value'] )
		{
			searchstring	= '';
		}

		var url				= ipb.vars['_base_url'] + "app=core&module=ajax&section=media&do=loadtab&tabapp=" + app + "&tabplugin=" + plugin;

		new Ajax.Request(	url.replace(/&amp;/g, '&'),
							{
								method:		'post',
								parameters: {
									md5check: 	ipb.vars['_secure_hash'],
									search:		searchstring
								},
								onSuccess:	function(t)
								{
									$('mymedia_content').update( t.responseText );
								}
							});

		return false;
	},
	
	exec : function( editor )
	{
		if ( ! Object.isUndefined( CKEDITOR.plugins.ipsmedia.popup ) )
		{
			CKEDITOR.plugins.ipsmedia.popup.kill();
		}
		
		CKEDITOR.plugins.ipsmedia.selectedText = IPSCKTools.getSelectionHtml( editor );
		
		var url = ipb.vars['_base_url'] + "app=core&module=ajax&section=media&secure_key=" + ipb.vars['_secure_hash'];
	
		CKEDITOR.plugins.ipsmedia.mediaEditor	= editor;

		new Ajax.Request(	url.replace(/&amp;/g, '&'),
							{
								method:		'get',
								evalJSON:	'force',
								onSuccess:	function(t)
								{
									CKEDITOR.plugins.ipsmedia.popup	= new ipb.Popup( 'my_media_inline', {	type: 'pane',
																											initial: t.responseJSON['html'],
																											hideAtStart: false,
																											hideClose: true,
																											defer: false,
																											modal: true,
																											w: '800px',
																											h: '410'
																										 } );
								}
							});
	},
	
	insert: function( insertCode )
	{
		var html	   = CKEDITOR.plugins.ipsmedia.selectedText;
		var newElement = new CKEDITOR.dom.element('div');
        
		if ( html != '' )
		{
	        newElement.setHtml( '[sharedmedia=' + insertCode + ']' + IPSCKTools.cleanHtmlForTagWrap( html ) );
        
    		CKEDITOR.plugins.ipsmedia.mediaEditor.insertElement(newElement);
		}
		else
		{
	        newElement.setHtml( '[sharedmedia=' + insertCode + ']' );
        
    		CKEDITOR.plugins.ipsmedia.mediaEditor.insertElement(newElement);
		}
				
		$('mymedia_inserted').show().fade({duration: 0.3, delay: 2});
		
		return false;
	},
	
	search: function()
	{
		var searchstring	= $('sharedmedia_search').value;
		
		var url				= ipb.vars['_base_url'] + "app=core&module=ajax&section=media&do=loadtab&tabapp=" + $('sharedmedia_search_app').value + "&tabplugin=" + $('sharedmedia_search_plugin').value;

		new Ajax.Request(	url.replace(/&amp;/g, '&'),
							{
								method:		'post',
								parameters: {
									md5check: 	ipb.vars['_secure_hash'],
									search:		searchstring
								},
								onSuccess:	function(t)
								{
									$('mymedia_content').update( t.responseText );
								}
							});

		return false;
	},
	
	searchinit: function()
	{
		$('sharedmedia_submit').observe( 'click', function(e) {
			Event.stop(e);
			
			CKEDITOR.plugins.ipsmedia.search();
			
			return false;
		});

		$('sharedmedia_reset').observe( 'click', function(e) {
			Event.stop(e);
			
			$('sharedmedia_search').value	= '';
			CKEDITOR.plugins.ipsmedia.search();
			
			$('sharedmedia_search').addClassName('inactive').value	= ipb.vars['sm_init_value'];
			
			return false;
		});
		
		$('sharedmedia_search').observe( 'focus', function(e) {
			if( $('sharedmedia_search').value == ipb.vars['sm_init_value'] )
			{
				$('sharedmedia_search').removeClassName('inactive').value	= '';
			}
		});
	},
	
	canUndo : true,
	modes : { wysiwyg : 1 },
	mediaEditor: null
};
