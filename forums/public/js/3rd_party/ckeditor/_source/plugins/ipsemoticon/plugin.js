/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file IPS smilie plug in
 */

(function()
{
	CKEDITOR.plugins.ipsemoticon = 
	{
		editor: {},
		emoPerPage: 20,
		emoTotalPages: 0,
		emoTotal: 0,
		emoPage: 1,
		emoAll: { 'count': false },
		
		/* Insert emoticon into editor */
		addEmoticon: function( e )
		{
			imgObj    = Event.findElement(e);
			editor    = CKEDITOR.plugins.ipsemoticon.editor;
			var src   = imgObj.readAttribute( 'src' );
			var title = imgObj.readAttribute( 'alt' );
			var img   = editor.document.createElement( 'img',
				{
					attributes :
					{
						src : src,
						title : title,
						alt : title,
						'class': 'bbc_emoticon'
					}
				});
			
			editor.insertElement( img );
			editor.insertText(' ');
			
			Event.stop(e);
		},
		
		/* Sets up and shows tray */
		createTray: function()
		{
			/* Create it */
			var editor = CKEDITOR.plugins.ipsemoticon.editor;
			var edObj  = $('cke_' + editor.name );
			
			var tray  = new Element( 'div', { 'id': 'cke_' + editor.name + '_stray', 'class': 'ipsSmileyTray' } ).hide();
			
			edObj.insert( { after: tray } );

			/* do some maths */
			this.emoPage = 1;
			this.emoTotalPages = 1;
			
			if ( IPS_smiles.total > this.emoPerPage )
			{
				this.emoTotalPages = Math.ceil( IPS_smiles.total / this.emoPerPage );
			}
			
			/* Populate the tray */
			this.populateTray( $H(IPS_smiles.emoticons) );
		
			/* Show tray */
			$('cke_' + editor.name + '_stray').blindDown( { duration: 0.4, afterFinish: CKEDITOR.plugins.ipsemoticon.addShowAllLink() } );
			
			/* Show left / right */
			this.setUpPrevNext();
			
			/* Shift screen if need be */
			try
			{
				var dims       = document.viewport.getDimensions();
				var editorDims = $('cke_' + editor.name ).getDimensions();
				var cOffset    = $('cke_' + editor.name ).cumulativeScrollOffset();
				var pOffset    = $('cke_' + editor.name ).cumulativeOffset();
				
				var bottomOfEditor = pOffset.top + editorDims.height;
				var bottomOfScreen = cOffset.top + dims.height;
				
				if ( bottomOfEditor > bottomOfScreen )
				{
					var diff = bottomOfEditor - bottomOfScreen;
					
					/* Scroll but with 100 extra pixels for comfort */
					window.scrollTo( 0, cOffset.top + diff + 100 );
				}
			}
			catch(e){}
			
			/* It's all about the delegation */
			ipb.delegate.register(".ipsSmileyTray_next", CKEDITOR.plugins.ipsemoticon.fireNext);
			ipb.delegate.register(".ipsSmileyTray_prev", CKEDITOR.plugins.ipsemoticon.firePrev);
			ipb.delegate.register(".ipsSmileyTray_all", CKEDITOR.plugins.ipsemoticon.fireAll);
			
			return true;
		},
		
		/* Shows the prev / next arrows */
		setUpPrevNext: function()
		{
			if ( this.emoPage < this.emoTotalPages )
			{
				/* Show right */
				$('cke_' + this.editor.name + '_stray').insert( new Element( 'div', { 'class': 'ipsSmileyTray_next' } ) );
			}
			
			if ( this.emoPage > 1 )
			{
				/* Show left */
				$('cke_' + this.editor.name + '_stray').insert( new Element( 'div', { 'class': 'ipsSmileyTray_prev' } ) );
			}
		},
		
		/* Fires prev */
		firePrev: function()
		{
			if ( CKEDITOR.plugins.ipsemoticon.emoAll.count === false )
			{
				CKEDITOR.plugins.ipsemoticon.getJson( CKEDITOR.plugins.ipsemoticon.firePrev );
			}
			else
			{
				/* Set up */
				var start = ( CKEDITOR.plugins.ipsemoticon.emoPerPage * ( CKEDITOR.plugins.ipsemoticon.emoPage - 1 ) ) - CKEDITOR.plugins.ipsemoticon.emoPerPage;
				
				/* Decrement */
				CKEDITOR.plugins.ipsemoticon.emoPage--;
				
				CKEDITOR.plugins.ipsemoticon.showPage( start );
			}
		},
		
		/* Fires next */
		fireNext: function()
		{
			if ( CKEDITOR.plugins.ipsemoticon.emoAll.count === false )
			{
				CKEDITOR.plugins.ipsemoticon.getJson( CKEDITOR.plugins.ipsemoticon.fireNext );
			}
			else
			{
				/* Set up */
				var start = CKEDITOR.plugins.ipsemoticon.emoPerPage * CKEDITOR.plugins.ipsemoticon.emoPage;
				
				/* Increment */
				CKEDITOR.plugins.ipsemoticon.emoPage++;
				
				CKEDITOR.plugins.ipsemoticon.showPage( start );
			}
		},
		
		/* Fires show all */
		fireAll: function(e, elem)
		{
			var url = ( ( ! inACP ) ? ipb.vars['base_url'] : ipb.vars['front_url'] ) + '&app=forums&module=extras&section=legends';
			
			window.open( url, 'Emo', 'status=0,toolbar=0,location=0,menubar=0,width=300,height=600,scrollbars=yes' );
			
			Event.stop(e);
			return false;
		},
		
		/* Show a page of emoticons */
		showPage: function( start )
		{
			var useThese = new Hash();
			var count    = 0;
			var seen     = 0;
			
			$H(CKEDITOR.plugins.ipsemoticon.emoAll.emoticons).each( function( smiley )
			{
				var key = smiley.key;
				var val = smiley.value;
				
				if ( typeof( val.src ) != 'undefined' )
				{
					seen++;
					
					if ( ( start == 0 ) || seen - 1 >= start )
					{
						count++;
						
						if ( count <= CKEDITOR.plugins.ipsemoticon.emoPerPage )
						{
							useThese.set( key, val );
						}
					}
				}
			} );
			
			/* Fade out current emos */
			$('cke_' + CKEDITOR.plugins.ipsemoticon.editor.name + '_stray').select('span').each( function( elem )
			{
				new Effect.Fade( elem, { duration: 0.2, afterFinish: function()
					{
						CKEDITOR.plugins.ipsemoticon.populateTray( useThese );
						CKEDITOR.plugins.ipsemoticon.setUpPrevNext();
					} } );
			} );
		},
		
		/* fetch data via json */
		getJson: function( callBack )
		{
			/* Prevent loading over and over */
			CKEDITOR.plugins.ipsemoticon.emoAll = IPS_smiles;
			
			callBack();
			
			/*
			var url			= ipb.vars['base_url'] + "app=core&module=ajax&section=editor&do=getEmoticons&secure_key=" + ipb.vars['secure_hash'];
			
			new Ajax.Request(	url.replace(/&amp;/g, '&'),
								{
									method:		'get',
									evalJSON:	'force',
									onSuccess:	function(t)
									{
										if ( t.responseJSON )
										{
											CKEDITOR.plugins.ipsemoticon.emoAll = t.responseJSON;
											
											
											callBack();
										}
									},
									onFailure: function( t, msg ) { Debug.error( msg ); },
									onException: function( t, msg ) { Debug.error( msg ); }
								} );*/
		},
		
		/* Add smilies */
		populateTray: function( emoticons )
		{
			var editor = CKEDITOR.plugins.ipsemoticon.editor;
			var added   = 0;
			
			/* Clear tray */
			$('cke_' + editor.name + '_stray').update('');
			
			emoticons.each( function( smiley )
			{
				var key = smiley.key;
				var val = smiley.value;
				
				added++;
				
				if ( added <= CKEDITOR.plugins.ipsemoticon.emoPerPage && typeof( val.src ) != 'undefined' )
				{
					var img = new Element( 'img', { 'src': CKEDITOR.tools.htmlEncode( IPS_smiley_path + val.src ),
													'alt': val.text,
													'title': val.text,
													'class': 'bbc_emoticon',
													'id' : 'ipsEmo__' + key } );
													
					var span = new Element( 'span' ).addClassName('cke_hand').insert( img ).hide();
					
					/* Insert image */
					$('cke_' + editor.name + '_stray').insert( span );
					
					/* Add listener */
					$('ipsEmo__' + key).on('click', CKEDITOR.plugins.ipsemoticon.addEmoticon );
				}
			} );
			
			/* Fade in current emos */
			$('cke_' + CKEDITOR.plugins.ipsemoticon.editor.name + '_stray').select('span').each( function( elem )
			{
				new Effect.Appear( elem, { duration: 0.2 } );
			} );
		},
		
		/* Add show all link */
		addShowAllLink: function()
		{
			var editor = CKEDITOR.plugins.ipsemoticon.editor;
			$('cke_' + editor.name + '_stray').insert( { after: new Element( 'div', { 'id': 'ips_x_smile_show_all', 'class': 'ipsSmileyTray_all' } ).addClassName('ipsText_smaller').update( ipb.lang['emo_show_all'] ) } );
		},
		
		/* Remove it */
		removeShowAllLink: function()
		{
			$('ips_x_smile_show_all').remove();
		}
	};
	
	// Do it
	var ipsemoticon =
	{
		exec : function( editor )
		{	
			CKEDITOR.plugins.ipsemoticon.editor = editor;
			
			/* Populate */
			CKEDITOR.plugins.ipsemoticon.emoCount = IPS_smiles.emoticons ? IPS_smiles.emoticons.total : 0;
			
			/* Tray open? */
			if ( $('cke_' + editor.name + '_stray') )
			{
				if ( $('cke_' + editor.name + '_stray').visible() )
				{
					$('cke_' + editor.name + '_stray').blindUp( { duration: 0.4, afterFinish: CKEDITOR.plugins.ipsemoticon.removeShowAllLink()  } );
					
				}
				else
				{
					$('cke_' + editor.name + '_stray').blindDown( { duration: 0.4, afterFinish: CKEDITOR.plugins.ipsemoticon.addShowAllLink()  } );
				}
			}
			else
			{
				/* Create it */
				CKEDITOR.plugins.ipsemoticon.createTray();
			}
		}
	};

	// Register the plugin.
	CKEDITOR.plugins.add( 'ipsemoticon',
	{
		init : function( editor )
		{
			var commandName = 'ipsemoticon',
				command = editor.addCommand( commandName, ipsemoticon );

			editor.ui.addButton( 'Ipsemoticon',
			{
				label : editor.lang.smiley.toolbar,
				command : commandName,
				icon: this.path+"images/ips_emoticon.png"
			});

		}
	});

})();