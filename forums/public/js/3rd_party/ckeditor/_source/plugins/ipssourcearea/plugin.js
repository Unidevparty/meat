var IPS_BBCODE_POPUP = false;

/**
 * Matt's IPS Source Area
 */
CKEDITOR.plugins.add( 'ipssourcearea',
{
	requires : [ 'editingblock' ],

	init : function( editor )
	{
		var ipssourcearea = CKEDITOR.plugins.ipssourcearea,
			win = CKEDITOR.document.getWindow();
		
		editor.on( 'editingBlockReady', function()
			{
				var textarea,
					onResize;

				editor.addMode( 'ipssource',
					{
						load : function( holderElement, data )
						{
							if ( CKEDITOR.env.ie && CKEDITOR.env.version < 8 )
								holderElement.setStyle( 'position', 'relative' );

							// Create the source area <textarea>.
							editor.textarea = textarea = new CKEDITOR.dom.element( 'textarea' );
							textarea.setAttributes(
								{
									dir : 'ltr',
									tabIndex : CKEDITOR.env.webkit ? -1 : editor.tabIndex,
									'role' : 'textbox',
									'aria-label' : editor.lang.editorTitle.replace( '%1', editor.name )
								});
							textarea.addClass( 'cke_source' );
							textarea.addClass( 'cke_enable_context_menu' );

							editor.readOnly && textarea.setAttribute( 'readOnly', 'readonly' );

							var styles =
							{
								// IE7 has overflow the <textarea> from wrapping table cell.
								width	: CKEDITOR.env.ie7Compat ?  '99%' : '100%',
								height	: '100%',
								resize	: 'none',
								outline	: 'none',
								'text-align' : 'left'
							};

							// Having to make <textarea> fixed sized to conque the following bugs:
							// 1. The textarea height/width='100%' doesn't constraint to the 'td' in IE6/7.
							// 2. Unexpected vertical-scrolling behavior happens whenever focus is moving out of editor
							// if text content within it has overflowed. (#4762)
							if ( CKEDITOR.env.ie )
							{
								onResize = function()
								{
									// Holder rectange size is stretched by textarea,
									// so hide it just for a moment.
									textarea.hide();
									textarea.setStyle( 'height', holderElement.$.clientHeight + 'px' );
									textarea.setStyle( 'width', holderElement.$.clientWidth + 'px' );
									// When we have proper holder size, show textarea again.
									textarea.show();
								};

								editor.on( 'resize', onResize );
								win.on( 'resize', onResize );
								setTimeout( onResize, 0 );
							}

							// Reset the holder element and append the
							// <textarea> to it.
							holderElement.setHtml( '' );
							holderElement.append( textarea );
							textarea.setStyles( styles );

							editor.fire( 'ariaWidget', textarea );

							textarea.on( 'blur', function()
								{
									editor.focusManager.blur();
								});

							textarea.on( 'focus', function()
								{
									editor.focusManager.focus();
								});

							// The editor data "may be dirty" after this point.
							editor.mayBeDirty = true;

							// Set the <textarea> value.
							this.loadData( data );

							var keystrokeHandler = editor.keystrokeHandler;
							if ( keystrokeHandler )
								keystrokeHandler.attach( textarea );

							setTimeout( function()
							{
								editor.mode = 'ipssource';
								editor.fire( 'mode', { previousMode : editor._.previousMode } );
							},
							( CKEDITOR.env.gecko || CKEDITOR.env.webkit ) ? 100 : 0 );
						},

						loadData : function( data )
						{
							data = data.replace( /<\!-- isHtml:(0|1) -->/g, '' );
							
							textarea.setValue( ( editor.ipsOptions.isHtml ) ? data : myParser.toBBCode( data ) );
						
							editor.fire( 'dataReady' );
						},

						getData : function()
						{ 
							if ( parseInt( editor.ipsOptions.isHtml ) == 1 )
							{
								return ( inACP ) ? '<!-- isHtml:1 -->' + textarea.getValue() : textarea.getValue();
							}
							else
							{
								/* We need to run this via CKEditors HTML processor to tidy up the HTML and make it consistent with the HTML editor */
								return editor.dataProcessor.toHtml( myParser.toHTML( textarea.getValue() ) );
							}
						},

						getSnapshotData : function()
						{
							return textarea.getValue();
						},

						unload : function( holderElement )
						{
							textarea.clearCustomData();
							editor.textarea = textarea = null;

							if ( onResize )
							{
								editor.removeListener( 'resize', onResize );
								win.removeListener( 'resize', onResize );
							}

							if ( CKEDITOR.env.ie && CKEDITOR.env.version < 8 )
								holderElement.removeStyle( 'position' );
						},

						focus : function()
						{
							textarea.focus();
						}
					});
			});

		editor.on( 'readOnly', function()
			{
				if ( editor.mode == 'ipssource' )
				{
					if ( editor.readOnly )
						editor.textarea.setAttribute( 'readOnly', 'readonly' );
					else
						editor.textarea.removeAttribute( 'readOnly' );
				}
			});

		editor.addCommand( 'ipssource', ipssourcearea.commands.ipssource );

		if ( editor.ui.addButton )
		{
			editor.ui.addButton( 'Ipssource',
				{
					label : editor.lang.source,
					command : 'ipssource',
					icon :this.path + 'images/switch.png',
				});
		}

		editor.on( 'mode', function()
		{
			editor.getCommand( 'ipssource' ).setState(
				editor.mode == 'ipssource' ?
					CKEDITOR.TRISTATE_ON :
					CKEDITOR.TRISTATE_OFF );
		});
		
		if ( CKEDITOR.config.CmdVAsPlainText )
		{
			// Intercept the default pasting process.
			editor.on( 'afterPaste', function ( evt )
			{
				if ( parseInt( CKEDITOR.config._ohThisIsATerribleTerribleHack_yPos ) )
				{
					window.scrollTo( 0, CKEDITOR.config._ohThisIsATerribleTerribleHack_yPos );
					
					CKEDITOR.config._ohThisIsATerribleTerribleHack_yPos = 0;
				}
			}, null, null, 0 );

			editor.on( 'beforePaste', function( evt )
			{
				CKEDITOR.config._ohThisIsATerribleTerribleHack_yPos = parseInt( (document.all ? document.scrollTop : window.pageYOffset) );
			});
		}
			
		/* Switch to source mode after init - otherwise the source
		   can get badly formatted when initialising in source mode */
		editor.on( 'dataReady', function()
		{
			if ( editor.ipsOptions.startIsRte == 'source' )
			{
				/* wipe as we're done and don't want an infinite loop */
				editor.ipsOptions.startIsRte = false;
				
				editor.setMode( 'ipssource' );
				
				/* Make sure newlines and such are kept and not processed out by
				   editor registering HTML via CKE then switched to source */
				if ( editor.ipsOptions.isHtml && editor._defaultContent )
				{
					editor.setData( editor._defaultContent );
				}
			}
		});
	}
});

/**
 * Test the BBCode to see if its valid (look for unmatched tags, etc)
 * @param editor
 * @returns array
 */
function validateBBCode( editor )
{
	/* Sure we're in the correct mode */
	if ( editor.mode != 'ipssource' )
	{
		/* Allow it */
		return true;
	}
	
	var testTags   = new Array();
	var brokenTags = new Array();
	var text       = $('cke_contents_' + editor.name).down('textarea').value;
	var forceCheck = ( forceCheck === true ) ? true : false;
	
	ipsBbcodeTags.each( function( i )
	{
		if ( myParser.options.tagsSingle.indexOf( i ) == -1 )
		{
			testTags.push( i );
		}
		else
		{
			Debug.write( 'Skipping: ' + i );
		}
	} );
	
	IPS_DEFAULT_TAGS.each( function( i )
	{
		/* [*] is a special case because although we prefer it to have a closing, historically it hasn't */
		if ( myParser.options.tagsSingle.indexOf( i ) == -1 && i != '*' )
		{
			testTags.push( i );
		}
		else
		{
			Debug.write( 'Skipping: ' + i );
		}
	} );
	
	Debug.dir( testTags );
	
	var testText = text.toLowerCase();
	
	testTags.each( function(i)
	{
		var oCount =  phpjs.substr_count( testText, '[' + i + ']' ) + phpjs.substr_count( testText, '[' + i + '=' );
		var cCount =  phpjs.substr_count( testText, '[/' + i + ']' );
		
		Debug.write( i + ' = opening: '  + oCount + ' closing: ' + cCount );
		
		if ( oCount > 0 && ( oCount != cCount ) )
		{
			brokenTags.push( i );
		}
	} );
	
	text = text.replace( /(\r\n|\r|\n)/g, '<br />' );
	
	if ( brokenTags.length > 0 )
	{
		brokenTags.each( function( tag )
		{
			text = text.replace( new RegExp( '\\\[' + tag + '(=[^\\\]]+?)?\\\]', 'gi' ), '<span class="bbcode_hilight">[' + tag  + '$1]</span>' );
			text = text.replace( new RegExp( '\\\[/' + tag + '\\\]', 'gi' ), '<span class="bbcode_hilight">[/' + tag  + ']</span>' );
		} );
		
		if ( IPS_BBCODE_POPUP !== false )
		{
			IPS_BBCODE_POPUP.kill();
		}
		
		/* easy one this... */
		IPS_BBCODE_POPUP = new ipb.Popup( 'bbcode_Error', { type: 'modal',
										                    initial: new Template( ipb.textEditor.IPS_BBCODE_ERROR ).evaluate( { content: text, tags: brokenTags.join(', ') } ),
										                    stem: false,
										                    warning: false,
										                    hideAtStart: false,
										                    modal: true,
										                    w: '600px' } );
		
		ipb.delegate.register('._bbcode_use_anyway', useAnywayAtYourOwnPeril.bind(editor) );
		ipb.delegate.register('._bbcode_close'     , doTheSensbibleThingAndEdit.bind(editor) );
		
		return false;
	}
	
	return true;
}

/**
 * Display a warning that the tags are broken
 * @param text
 * @param brokenTags
 */
function displayBrokenBBCodeWarning( text, brokenTags, editor )
{
	text = text.replace( /(\r\n|\r|\n)/g, '<br />' );
	
	brokenTags.each( function( tag )
	{
		text = text.replace( new RegExp( '\\\[' + tag + '(=[^\\\]]+?)?\\\]', 'gi' ), '<span class="bbcode_hilight">[' + tag  + '$1]</span>' );
		text = text.replace( new RegExp( '\\\[/' + tag + '\\\]', 'gi' ), '<span class="bbcode_hilight">[/' + tag  + ']</span>' );
	} );
	
	if ( IPS_BBCODE_POPUP !== false )
	{
		IPS_BBCODE_POPUP.kill();
	}
	
	/* easy one this... */
	IPS_BBCODE_POPUP = new ipb.Popup( 'bbcode_Error', { type: 'modal',
									                    initial: new Template( ipb.textEditor.IPS_BBCODE_ERROR ).evaluate( { content: text, tags: brokenTags.join(', ') } ),
									                    stem: false,
									                    warning: false,
									                    hideAtStart: false,
									                    modal: true,
									                    w: '600px' } );
	
	ipb.delegate.register('._bbcode_use_anyway', useAnywayAtYourOwnPeril.bind(editor) );
	ipb.delegate.register('._bbcode_close'     , doTheSensbibleThingAndEdit.bind(editor) );
}

/**
 * Close the pop up to allow one to fix
 */
function doTheSensbibleThingAndEdit(e, elem)
{
	var _editor = this;
	
	IPS_BBCODE_POPUP.hide();
}

/**
 * Use broken code anyway
 */
function useAnywayAtYourOwnPeril(e, elem)
{
	var _editor = this;
	
	setTimeout( function()
	{
		_editor.setMode( 'wysiwyg' );
	},
	( CKEDITOR.env.gecko || CKEDITOR.env.webkit ) ? 100 : 0 );
	
	IPS_BBCODE_POPUP.hide();
}
					
/**
 * Holds the definition of commands an UI elements included with the sourcearea
 * plugin.
 * @example
 */
CKEDITOR.plugins.ipssourcearea =
{
	commands :
	{
		ipssource :
		{
			modes : { wysiwyg:1, ipssource:1 },
			editorFocus : false,
			readOnly : 1,
			exec : function( editor )
			{
				editor._ipsToggled           = true;
				editor._ipsBypassBBCodeCheck = ( typeof( editor._ipsBypassBBCodeCheck ) === 'undefined' ) ? false : editor._ipsBypassBBCodeCheck;
				
 				if ( editor.mode == 'wysiwyg' )
				{
					editor.fire( 'saveSnapshot' );
					
					ipb.textEditor.getEditor( editor.name ).setIsRte( false );
					
					if ( $('cke_' + editor.name + '_stray') != null && $('cke_' + editor.name + '_stray').visible() )
					{
						$('cke_' + editor.name + '_stray').blindUp( { duration: 0.4, afterFinish: CKEDITOR.plugins.ipsemoticon.removeShowAllLink()  } );
					}
				}
				else
				{
					ipb.textEditor.getEditor( editor.name ).setIsRte( true );
				}
				
				editor.getCommand( 'ipssource' ).setState( CKEDITOR.TRISTATE_DISABLED );
				
				if ( editor.mode == 'ipssource' && editor._ipsBypassBBCodeCheck === false )
				{
					/* Source mode? Test BBCode... */
					//var doTest = validateBBCode( editor );
					var doTest	= true;
			
					if ( doTest === false )
					{
						/* Toggle it back as switchable */
						editor.getCommand( 'ipssource' ).setState( CKEDITOR.TRISTATE_ON );

						editor._ipsToggled = false;
						
						return false;
					}
				}
				
				/* Here? Run the command */
				editor.setMode( editor.mode == 'ipssource' ? 'wysiwyg' : 'ipssource' );
				
				editor._ipsToggled = false;
			},

			canUndo : false
		}
	}
};

