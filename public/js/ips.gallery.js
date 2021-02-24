/************************************************/
/* IPB3 Javascript								*/
/* -------------------------------------------- */
/* ips.gallery.js - Gallery javascript			*/
/* (c) IPS, Inc 2008							*/
/* -------------------------------------------- */
/* Author(s): Rikki, Matt, bfarber				*/
/************************************************/

/**
 * Hack to get lastDescendant
 * Thanks: http://proto-scripty.wikidot.com/prototype:tip-getting-last-descendant-of-an-element
 */
Element.addMethods({
    lastDescendant: function(element) {
        element = $(element).lastChild;
        while (element && element.nodeType != 1) 
            element = element.previousSibling;
        return $(element);
    }
});

/**
* Returns the value of the selected radio button in the radio group, null if
* none are selected, and false if the button group doesn't exist
* @link  http://xavisys.com/using-prototype-javascript-to-get-the-value-of-a-radio-group/
*
* @param {radio Object} or {radio id} el
* OR
* @param {form Object} or {form id} el
* @param {radio group name} radioGroup
*/
function $RF(el, radioGroup) {
    if($(el).type && $(el).type.toLowerCase() == 'radio') {
        var radioGroup = $(el).name;
        var el = $(el).form;
    } else if ($(el).tagName.toLowerCase() != 'form') {
        return false;
    }

    var checked = $(el).getInputs('radio', radioGroup).find(
        function(re) {return re.checked;}
    );
    return (checked) ? $F(checked) : null;
}


var _gallery = window.IPBoard;

_gallery.prototype.gallery = {
	
	inSection:			'',
	maps:				[],
	latLon:				null,
	popups:				[],
	templates:			{},
	contextMenu:		false,
	isMedia:			0,
	mediaUpload:		null,
	albumId:			0,
	categoryId:			0,
	cropPhoto:			{},

	totalChecked:		0,
	
	/**
	 * Constructor
	 */
	init: function()
	{
		Debug.write("Initializing ips.gallery.js");
		
		document.observe("dom:loaded", function(){
			if ( $('map') )
			{
				ipb.gallery.initMaps();
			}
			
			// Watch the "set as profile photo" button and enable the cropper
			if ( $('profileTrigger') )
			{
				ipb.gallery.popups['photoTrigger'] = new ipb.Popup( 'photoTrigger',   { type: 'modal',
																			            initial: $('template_photo').innerHTML,
																			            stem: false,
																			            hideAtStart: true,
																			            warning: false,
																			            w: '700px' } );
				// Remove template to prevent js confusion
				$('template_photo').remove();
				
				cropper = new Cropper.Img(  'photo_view_' + ipb.gallery.imageID,  { ratioDim: { x: 100, y: 100 }, 
																					minWidth: 100,
																					minHeight: 100,
																					displayOnInit: true, 
																					autoIncludeCSS: false,
																					onEndCrop: ipb.gallery.photoOnEndCrop  } );

				$('profileTrigger').observe('click', ipb.gallery.photoCropStart );
				
				// Set up buttons
				$('setAsPhoto_accept').observe('click', ipb.gallery.photoCropAccept );
				$('setAsPhoto_cancel').observe('click', ipb.gallery.photoCropCancel );
			}
			
			// Delete album
			ipb.delegate.register('._albumDelete', ipb.gallery.albumDeleteDialogue );
			
			// Add labels after a short timeout
			setTimeout( ipb.gallery.setUpLabels, 750 );

			// Extra options if we are viewing an image
			if ( ipb.gallery.inSection == 'image' )
			{
				if ( ! ipb.gallery.isMedia )
				{
					if( $('rotate_left') && $('rotate_right') )
					{
						$('rotate_left').observe('click', ipb.gallery.rotateImage.bindAsEventListener( this, 'left') );
						$('rotate_right').observe('click', ipb.gallery.rotateImage.bindAsEventListener( this, 'right') );
					}
					
					try
					{
						$('theImage').down('img').writeAttribute( 'alt', '' );
						$('theImage').down('img').writeAttribute( 'title', '' );
					}
					catch(e){}
					
					$('theImage').down('img').observe( 'contextmenu', ipb.gallery.imageContextMenu );
					$('theImage').down('img').observe( 'click', ipb.gallery.click );
				}
			}
			
			// Set up album moderation
			if( $$('.albumModBox').size() )
			{
				ipb.gallery.preCheckImages();
			}

			ipb.delegate.register('input.albumModBox', ipb.gallery.albumModerate );
		});
	},

	/**
	 * Set up new/hidden labels
	 */
	setUpLabels: function()
	{
		$$('.hello_i_am_new').each( ipb.gallery.addNewSticker );
		$$('.hello_i_am_hidden').each( ipb.gallery.addHiddenSticker );
		$$('.hello_i_am_unapproved').each( ipb.gallery.addHiddenSticker );
	},
	
	/**
	 * Launch move dialogue
	 */
	imageMoveDialogue: function(elem, e)
	{
		Event.stop(e);
		
		ipb.menus.closeAll(e);
		
		if ( ! Object.isUndefined( ipb.gallery.popups['move'] ) )
		{
			ipb.gallery.popups['move'].show();
		}
		else
		{
			ipb.gallery.popups['move'] = new ipb.Popup( 'menuMove', { type: 'pane',
																		ajaxURL: ipb.vars['base_url'] + 'app=gallery&module=ajax&section=image&do=moveDialogue&secure_key=' + ipb.vars['secure_hash'] + '&imageid=' + ipb.gallery.imageID,
																		stem: true,
																		hideAtStart: false,
																		w: '400px' });
		}
	},
	
	/**
	 * Set image as a cover image
	 */
	imageSetAsCover: function(elem, e)
	{
		Event.stop(e);

		if ( ! Object.isUndefined( ipb.gallery.popups['sac'] ) )
		{
			ipb.gallery.popups['sac'].show();
		}
		else
		{
			ipb.gallery.popups['sac'] = new ipb.Popup( 'setAsCover', { type: 'pane',
																		ajaxURL: ipb.vars['base_url'] + 'app=gallery&module=ajax&section=image&do=setAsCoverOptions&secure_key=' + ipb.vars['secure_hash'] + '&imageId=' + ipb.gallery.imageID,
																		stem: true,
																		hideAtStart: false,
																		w: '400px' });
		}
	},
	
	/**
	 * Start the "set as profile photo" cropper
	 */
	photoCropStart: function(e)
	{
		Event.stop(e);
		ipb.gallery.popups['photoTrigger'].show();
	},

	/**
	 * Accept the cropped dimensions and set as profile photo
	 */
	photoCropAccept: function(e)
	{
		new Ajax.Request( ipb.vars['base_url'] + '&app=gallery&module=ajax&section=image&do=setAsPhoto&secure_key=' + ipb.vars['secure_hash'] + '&imageId=' + ipb.gallery.imageID,
							{
								method: 'post',
								evalJSON: 'force',
								parameters: { x1: ipb.gallery.cropPhoto['coords'].x1,
											  x2: ipb.gallery.cropPhoto['coords'].x2,
											  y1: ipb.gallery.cropPhoto['coords'].y1,
											  y2: ipb.gallery.cropPhoto['coords'].y2 },
								onSuccess: function(t)
								{
									// Did it save ok?
									if ( t.responseJSON && t.responseJSON['status'] == 'ok' )
									{
										// Close the cropper dialog
										ipb.gallery.photoCropCancel();
										
										// Attempt to dynamically update our profile photos on this page
										$$('.photo', '.ipsUserPhoto').each( function( elem )
										{
											try
											{
												src  = elem.readAttribute('src');
												rand = Math.round( Math.random() * 100000000 );
												
												if ( src == t.responseJSON['oldPhoto'] )
												{
													elem.src = t.responseJSON['thumb'] + '?t=' + rand;
												}
											} catch (err){}
										} );
									}
								}
							}
						);
	},

	/**
	 * Cancel the "set as profile photo" dialog
	 */
	photoCropCancel: function(e)
	{
		ipb.gallery.popups['photoTrigger'].hide();
	},

	/**
	 * When we stop cropping, store the dimensions for the AJAX request
	 */
	photoOnEndCrop: function( coords, dimensions )
	{
		ipb.gallery.cropPhoto['coords'] = coords;
		ipb.gallery.cropPhoto['dims']   = dimensions;
		
		Debug.dir( coords );
		Debug.dir( dimensions );
	},

	/**
	 * Any images checked from other pages?
	 */
	preCheckImages: function()
	{	
		var cookie = ipb.Cookie.get('modiids');
		
		if( !cookie ){ return; }

		var images	= cookie.split(',');
		
		if( images )
		{
			images.each( function(check){
				if( check != '' )
				{
					if( $('modBox_' + check ) )
					{
						$('modBox_' + check ).checked = true;
					}
					
					ipb.gallery.totalChecked++;
				}
			});
		}
		
		ipb.gallery.albumModerate();
	},

	/**
	 * Show or hide the moderation box when images are checked
	 */
	albumModerate: function(e, elem)
	{
		// Count checked boxes that are visible
		var cookie	= ipb.Cookie.get('modiids');

		if( !cookie )
		{
			var cookie	= '';
		}

		// Just checked an image?
		if( $(elem) )
		{
			var _thisid	= $(elem).id.replace( /modBox_/, '' );

			if( _thisid )
			{
				if( $(elem).checked )
				{
					cookie	= cookie ? cookie.split(',') : new Array();
					cookie.push( _thisid );
					cookie	= cookie.uniq().join(',');
				}
				else
				{
					cookie	= cookie.split(',').uniq().without( _thisid ).join(',');
				}
			}

			ipb.Cookie.set( 'modiids', cookie );
		}

		// Count?
		if( cookie )
		{
			var count	= cookie.split(',').size();
		}
		else
		{
			var count	= 0;
		}

		if( count )
		{
			if( !$('album_moderate_box') )
			{
				$$('body')[0].insert( {'bottom': ipb.templates['album_moderation'].evaluate( { count: count } ) } );
				$('albumModAction').on('change', ipb.gallery.albumModerateChangeOption);
				$('submitModAction').on('click', ipb.gallery.albumModerateSubmit);
				$('albumModSelectAll').on('click', ipb.gallery.albumModerateSelectAllImages);
			}
			else
			{
				$('images_modcount').update( count );
			}
			
			if( !$('album_moderate_box').visible() )
			{
				new Effect.Appear( $('album_moderate_box'), { duration: 0.3 } );
			}
		}
		else
		{
			if( $('album_moderate_box') )
			{
				new Effect.Fade( $('album_moderate_box'), { duration: 0.3 } );
			}
		}
	},

	/**
	 * Someone changed the moderation drop down box
	 */
	albumModerateChangeOption: function(e)
	{
		elem	= Event.findElement(e);
		option	= $F(elem);

		if ( option == 'move' )
		{
			// Show the moveto options
			if ( $('albumModBox_moveTo') )
			{
				Effect.BlindDown( $('albumModBox_move'), { 'duration': 0.4 } );
				return;
			}
			
			$('albumModBox_move').update( ipb.templates['album_img_moveto'].evaluate( { 'album_id': ipb.gallery.albumId } ) );
			
			Effect.BlindDown( $('albumModBox_move'), { 'duration': 0.4 } );
		}
		else
		{
			// Hide the moveto options
			if ( $('albumModBox_move').visible() )
			{
				Effect.BlindUp( $('albumModBox_move'), { 'duration': 0.4 } );
			}
		}
	},
	
	/**
	 * Someone submitted the moderation button
	 */
	albumModerateSubmit: function(e)
	{
		// Don't jump to the top of the page
		Event.stop(e);

		// Are we moving images to somewhere else?
		toAlbumId		= 0;
		toCategoryId	= 0;
		
		if ( $('albumModBox_move').visible() )
		{
			if( $RF('theForm', 'movetoAlbum') == 1 )
			{
				toAlbumId		= $F('albumModBox_moveTo');
			}
			else
			{
				toCategoryId	= $F('movetoCategory_id');
			}
		}

		// Fire AJAX request
		new Ajax.Request( ipb.vars['base_url'] + 'app=gallery&module=ajax&section=album&do=moderate',
							{
								method: 'post',
								evalJSON: 'force',
								encoding: ipb.vars['charset'],
								parameters: {
									md5check: 		ipb.vars['secure_hash'],
									'secure_key':	ipb.vars['secure_hash'],
									'toAlbumId':	toAlbumId,
									'toCategoryId':	toCategoryId,
									'modact':		$( 'albumModAction').value,
									'albumId':		ipb.gallery.albumId,
									'categoryId':	ipb.gallery.categoryId
									},
								onSuccess: function(t)
								{
									// Got an error?  Show it and return.
									if ( t.responseJSON['error'] )
									{
										alert( t.responseJSON['error'] );
										
										return false;
									}
									else
									{
										// Uncheck all the checkboxes
										$$('.albumModBox:checked').each( function(i){
											i.checked = false;
										});
										
										// Hide the moderation box
										if( $('album_moderate_box') ){
											new Effect.Fade( $('album_moderate_box'), { duration: 0.3 } );
										}

										ipb.Cookie.set( 'modiids', '' );

										// And make this easy...just reload the page
										window.location.href	= window.location.href.replace( /\#.*?/, '' );
									}
								}
							}
						);
	},
	
	/**
	 * Select all images on the page
	 */
	albumModerateSelectAllImages: function(e)
	{
		elem = Event.findElement(e);
		
		$$('.albumModBox').each( function(c){
			c.checked = elem.checked;
			ipb.gallery.albumModerate( e, c );
		});
		
		if( $('album_moderate_box').visible() && !elem.checked ){
			new Effect.Fade( $('album_moderate_box'), { duration: 0.3 } );
		}
	},
	
	/**
	 * Launch album delete dialogue
	 */
	albumDeleteDialogue: function(e, elem)
	{
		Event.stop(e);
		
		albumId = elem.readAttribute('album-id');
		
		ipb.menus.closeAll(e);
		
		if ( ! Object.isUndefined( ipb.gallery.popups['deleteAlbum'] ) )
		{
			ipb.gallery.popups['deleteAlbum'].kill();
		}

		ipb.gallery.popups['deleteAlbum'] = new ipb.Popup( 'deleteAlbum', { type: 'modal',
																            ajaxURL: ipb.vars['base_url'] + 'app=gallery&module=ajax&section=album&do=deleteDialogue&secure_key=' + ipb.vars['secure_hash'] + '&albumId=' + albumId,
																            stem: false,
																			w: '400px',
																            hideAtStart: false,
																            warning: true } );
	},
	
	/**
	 * Adds the 'hidden' sticker
	 */
	addHiddenSticker: function( elem, e )
	{
		try
		{
			var thisSticker = $(elem).hasClassName('hello_i_am_unapproved') ? ipb.lang['unapproved'] : ipb.lang['hidden'];

			if ( elem.getStyle('textAlign') == 'center' )
			{
				return;
			}
			
			if ( elem.className.match( /cover_img_/ ) )
			{
				return;
			}
			
			// Is the image padded?
			dims     = elem.getDimensions();
			width    = dims.width;
			height   = dims.height;
			
			if ( width <= 40 || height <= 40 )
			{
				return;
			}
			
			_div = new Element('div', { id: 'image_is_hidden_box_' + elem.id.replace( /tn_image_view_/, '' ), className: 'image_is_hidden_box' } ).update( thisSticker );
			elem.insert( { before: _div } );
			
			if ( elem.hasClassName('galmedium') )
			{
				_div.setStyle( 'margin-left:' + ( 10 ) + 'px !important' );
				width  += 6;
				height += 5;
			}
			
			if ( height )
			{
				_div.setStyle( 'height:16px !important' );
				_div.setStyle( 'margin-top:' + ( height - 19 ) + 'px !important' );
			}
			
			if ( width )
			{
				_div.setStyle( 'width:' + ( width - 10) + 'px !important' );
				
			}
		}
		catch(e){}
	},
	
	/**
	 * Adds the 'new' sticker
	 */
	addNewSticker: function( elem, e )
	{
		try
		{
			if ( elem.getStyle('textAlign') == 'center' )
			{
				return;
			}
			
			if ( elem.className.match( /cover_img_/ ) )
			{
				return;
			}
			
			try
			{
				if ( elem.width <= 30 )
				{
					return;
				}
				
			}
			catch (err) { }
			
			_div = new Element('div', { className: 'image_is_new_box' } ).update( ipb.lang['new_lowercase'] );
			elem.insert( { before: _div } );
			
			// Is the image padded?
			padLeft  = parseInt( elem.getStyle('paddingLeft') );
			padTop   = parseInt( elem.getStyle('paddingTop') );
			marLeft  = parseInt( elem.getStyle('marginLeft') );
			marTop   = parseInt( elem.getStyle('marginTop') );
			bckTop   = parseInt( elem.getStyle('backgroundPositionX') );
			bckLeft  = parseInt( elem.getStyle('backgroundPositionY') );
			
			if ( padLeft || marLeft || bckLeft )
			{
				_div.setStyle( 'margin-left:' + ( (padLeft + marLeft + bckLeft) - 3 ) + 'px !important' );
			}
			
			if ( padTop || marTop || bckTop )
			{
				_div.setStyle( 'margin-top:' + ( (padTop + marTop + bckTop) - 3 ) + 'px !important' );
			}
			
			elem.up('a').setStyle( { 'textDecoration': 'none' } );
		}
		catch(e){}
	},
	
	/**
	 * Init flash player
	 */
	flashPlayerInit: function( file, flowplayerUrl )
	{
		$f("player", flowplayerUrl, { clip: { autoPlay: false, url: file, scaling: 'fit' } } );
	},
	
	/**
	 * Launch the image delete dialogue
	 */
	imageDeleteDialogue: function(elem, e)
	{
		Event.stop(e);
		
		ipb.menus.closeAll(e);
		
		if ( ! Object.isUndefined( ipb.gallery.popups['delete'] ) )
		{
			ipb.gallery.popups['delete'].show();
		}
		else
		{
			ipb.gallery.popups['delete'] = new ipb.Popup( 'menuEdit', { type: 'modal',
															            initial: $('template_delete').innerHTML,
															            stem: false,
															            warning: true,
															            hideAtStart: false,
															            w: '300px' } );
		}
	},
	
	/**
	 * Launch the lightbox image view
	 */
	click: function(e)
	{
		if ( $('ips_lightbox') )
		{
			if ( $('ips_lightbox').readAttribute('available') == 'true' )
			{
				ipb.gallery_lightbox.launch();
			}
		}
	},
	
	/**
	 * Image Context Menu click
	 */
	imageContextMenu: function(e)
	{		
		if ( ! Event.isLeftClick(e) )
		{
			Event.stop(e);
			
			if ( ipb.gallery.contextMenu !== false )
			{
				ipb.gallery.contextMenu.kill();
			}
			
			ipb.gallery.contextMenu = new ipb.Popup( 'imcontextmenu', {  type: 'balloon',
																	     initial: $('template_sizes').innerHTML,
																	     stem: false,
																	     hideClose: true,
																	     hideAtStart: false,
																	     attach: { target: $('theImage'), position: 'auto' },
																	     w: '350px' });
			
			// Reposition
			x = Event.pointerX(e);
			y = Event.pointerY(e);
			
			if ( x && y )
			{
				$('imcontextmenu_popup').setStyle( { 'position': 'absolute', 'left': x + 'px', 'top': y + 'px'} );
			}
			
		}
	},
	
	/**
	 * Init map
	 */
	initMaps: function()
	{
		if ( $('map_0') && $('map_1') && ipb.gallery.latLon )
		{
			$('map').appear();
			
			ipb.gallery.maps[0] = $('map_0').src;
			ipb.gallery.maps[1] = $('map_1').src;
			
			$('map_0').observe( 'mouseover', function(e) { $('map_0').src = ipb.gallery.maps[1]; } );
			$('map_0').observe( 'mouseout' , function(e) { $('map_0').src = ipb.gallery.maps[0]; } );
		}
	},
	
	/**
	 * Remove map from image
	 */
	removeMap: function(elem, e)
	{
		Event.stop(e);

		new Ajax.Request( 
				ipb.vars['base_url']+'app=gallery&module=ajax&section=image&do=removeMap&secure_key=' + ipb.vars['secure_hash'] + '&imageid=' + ipb.gallery.imageID,
				{
					method: 'get',
					evalJSON: 'force',
					onSuccess: function(t)
					{
						// Got a valid response?
						if( Object.isUndefined( t.responseJSON ) )
						{
							alert( ipb.lang['action_failed'] );
							return;
						}
						// Is the response an error?
						else if ( t.responseJSON['error'] )
						{
							alert( ipb.lang['no_permission'] );
						}
						else
						{
							if ( t.responseJSON['done'] )
							{
								// Remove map stuff
								$$('.__mapon').each( function(elem)
								{
									elem.fade();
								} );
							}
						}
					}
				}
			);
	},
	
	/**
	 * Add map to image
	 */
	addMap: function(elem, e)
	{
		Event.stop(e);

		new Ajax.Request( 
				ipb.vars['base_url']+'app=gallery&module=ajax&section=image&do=addMap&secure_key=' + ipb.vars['secure_hash'] + '&imageid=' + ipb.gallery.imageID,
				{
					method: 'get',
					evalJSON: 'force',
					onSuccess: function(t)
					{
						// Got a valid response?
						if( Object.isUndefined( t.responseJSON ) )
						{
							alert( ipb.lang['action_failed'] );
							return;
						}
						// Was response an error?
						else if ( t.responseJSON['error'] )
						{
							alert( ipb.lang['no_permission'] );
						}
						else
						{
							if ( t.responseJSON['latLon'] )
							{
								// Set the lat and lon, and initialize map
								$$('.__mapoff').invoke('hide');
								ipb.gallery.latLon = t.responseJSON['latLon'];
								ipb.gallery.initMaps();
							}
						}
					}
				}
			);
	},
	
	/**
	 * Generic new album select dialogue
	 */
	newAlbumDialogue: function(e, elem)
	{
		Event.stop(e);
		
		if ( ! Object.isUndefined( ipb.gallery.popups['nad'] ) )
		{
			ipb.gallery.popups['nad'].kill();
		}
		
		// Set up submit button monitor
		ipb.delegate.register('._aSubmit', ipb.gallery.newAlbumSubmit );

		new Ajax.Request( 
				ipb.vars['base_url']+'app=gallery&module=ajax&section=album&do=newAlbumDialogue&secure_key=' + ipb.vars['secure_hash'],
				{
					method: 'get',
					onSuccess: function(t)
					{
						// Did we get an error?
						try
						{
							if ( ! Object.isUndefined( t.responseJSON ) && ! Object.isUndefined( t.responseJSON['error'] ) )
							{
								$$('._albumNew').invoke( 'hide' );

								ipb.global.errorDialogue( t.responseJSON['error'] );
								return false;
							}
						}
						catch(e){}
						
						// Get a nopermission error?
						if( t.responseText == 'nopermission' )
						{
							alert( ipb.lang['no_permission'] );
						}
						else
						{
							// Show the popup
							ipb.gallery.popups['nad'] = new ipb.Popup( 'newAlbumDialogue', {  type: 'pane',
																	   initial: t.responseText,
																	   stem: true,
																	   hideAtStart: false,
																	   h: 460 });

							$$('.cancel').each( function(elem) {
								$(elem).observe('click', ipb.gallery.popups['nad'].hide.bindAsEventListener( ipb.gallery.popups['nad'] ) );
							});

							$('album_category_id').observe( 'change', ipb.gallery.albumTypeOptions );
							ipb.gallery.albumTypeOptions();
						}
					}
				}
			);
	},

	/**
	 * Hide the friend-only and private album selections when appropriate
	 */
	albumTypeOptions: function( e )
	{
		if( $F('album_category_id') == ipb.vars['members_gallery'] )
		{
			if( $('private_album_option') )
			{
				$('private_album_option').show();
			}

			if( $('fo_album_option') )
			{
				$('fo_album_option').show();
			}
		}
		else
		{
			if( $('private_album_option') )
			{
				$('private_album_option').hide();
			}

			if( $('fo_album_option') )
			{
				$('fo_album_option').hide();
			}

			$('album_type_1').checked	= 'checked';
		}
	},

	/**
	 * Submit our new album
	 */
	newAlbumSubmit: function(e, elem)
	{
		Event.stop(e);
		var post = {};
		
		// Grab the post data
		post['album_name']				= $F('album_name');
		post['album_description']		= $F('album_description');
		post['album_category_id']		= $F('album_category_id');
		post['album_sort_options__key']	= $F('album_sort_options__key');
		post['album_sort_options__dir']	= $F('album_sort_options__dir');
		post['album_allow_comments']	= $F('album_allow_comments');
		post['album_allow_rating']		= $F('album_allow_rating');
		post['album_watermark']			= $F('album_watermark');
		post['album_type']				= $RF('theForm', 'album_type');
		
		// Hide the save button to prevent double clicks
		if ( $('fieldset_aSubmit') )
		{
			$('fieldset_aSubmit').hide();
		}

		new Ajax.Request( 
				ipb.vars['base_url']+'app=gallery&module=ajax&section=album&do=newAlbumSubmit&secure_key=' + ipb.vars['secure_hash'],
				{
					method: 'post',
					parameters: post,
					evalJSON: 'force',
					onSuccess: function(t)
					{
						// Not a valid response?
						if( Object.isUndefined( t.responseJSON ) )
						{
							// Show the save button
							if ( $('fieldset_aSubmit') )
							{
								$('fieldset_aSubmit').show();
							}
							
							alert( ipb.lang['action_failed'] );
						}
						// Get an error message?
						else if ( t.responseJSON['error'] )
						{
							// Show the save button
							if ( $('fieldset_aSubmit') )
							{
								$('fieldset_aSubmit').show();
							}
							
							alert( t.responseJSON['error'] );
						}
						else
						{
							// Close the popup
							ipb.gallery.popups['nad'].hide();
							
							if ( t.responseJSON['album'] )
							{
								// Update the uploader form
								ipb.uploader.buildContainerBox( t.responseJSON['album']['album_id'], 0, albumTemplate, 'albumWrap' );
							}
						}
					}
				}
			);
	},
	
	/**
	 * Call back for album selector for upload forms
	 */
	callBackForUploadFormForAlbumSelector: function( album )
	{
		ipb.uploader.buildContainerBox( album.album_id, 0, albumTemplate, 'albumWrap' );
	},
	
	/**
	 * Set up review page
	 */
	setUpReviewPage: function()
	{
		ipb.gallery.inUse = new Array();
		
		// Set up rotate links
		ipb.delegate.register('.rotate', ipb.gallery._rotate );
		
		// Media add thumb
		ipb.delegate.register( '.media_thumb_pop', ipb.gallery.mediaThumbPop );
		
		// Add CSS class
		$$('.galattach').invoke( 'addClassName', 'gallery_photo_wrap' );
		
		// Set up text editors and other stuff
		$$('._imgIds').each( function( id ) {
			_id = id.className.match( /_x(.+?)(\s|$)/ );
			
			if ( _id[1] )
			{
				Debug.write( "Set up editor for: " + _id[1] );
				
				try
				{
					if ( $('image_thumb_wrap_' + _id[1] ).down('.media_thumb_pop') )
					{
						if ( $('image_thumb_wrap_' + _id[1] ).down('.media_thumb_pop').readAttribute('media-has-thumb') == 'true' )
						{
							$('image_thumb_wrap_' + _id[1] ).down('.media_thumb_pop').value = ipb.lang['remove_img'];
						}
					}
				}
				catch(e){}
			}
		} );
	},

	/**
	 * Handle media thumbs
	 */
	mediaThumbPop: function(e)
	{
		var elem = Event.element(e);
		elem.identify();
		
		var mediaId  = elem.readAttribute('media-id');
		var hasThumb = elem.readAttribute('media-has-thumb');
		
		// If we have a thumb and we clicked, we want to remove
		if ( hasThumb == 'true' )
		{
			new Ajax.Request( 
					ipb.vars['base_url']+'app=gallery&module=ajax&section=image&do=thumbRemove&type=mediaThumb&secure_key=' + ipb.vars['secure_hash'] + '&id=' + mediaId,
					{
						method: 'post',
						onSuccess: function(t)
						{
							/* No Permission */
							if( Object.isUndefined( t.responseJSON ) )
							{
								alert( ipb.lang['action_failed'] );
								return;
							}
							else if ( t.responseJSON['error'] )
							{
								alert( t.responseJSON['error'] );
								return;
							}
							else
							{
								ipb.gallery._changeMediaThumb( t.responseJSON );
								$('image_thumb_wrap_' + t.responseJSON['image_id'] ).down('.media_thumb_pop').writeAttribute('media-has-thumb', 'false');
								$('image_thumb_wrap_' + t.responseJSON['image_id'] ).down('.media_thumb_pop').value = ipb.lang['add_thumb'];
							}
						}
					}
				);	
		}
		else
		{
			if ( ! Object.isUndefined( ipb.gallery.popups['mediaPop-' + mediaId ] ) )
			{
				ipb.gallery.popups['mediaPop-' + mediaId ].show();
			}
			else
			{
				ipb.gallery.popups['mediaPop-' + mediaId ] = new ipb.Popup( 'mediathumb', { type: 'pane',
																							modal: false,
																							w: '420px',
																							h: '300px',
																							initial: $('templates-mediaupload').innerHTML.replace( /\#\{id\}/g, mediaId ),
																							hideAtStart: false,
																							close: 'a[rel="close"]' } );
				
				ipb.gallery.mediaUpload = new ipb.mediaThumbUploader( mediaId, 'mediaUploader' );
			}
		}
	},

	/**
	 * Close media thumb popup
	 */
	mediaThumbClose: function( json )
	{
		if ( ! Object.isUndefined( ipb.gallery.popups['mediaPop-' + json['image_id'] ] ) )
		{
			ipb.gallery.popups['mediaPop-' + json['image_id'] ].hide();
		}
		
		if ( json && json['ok'] == 'done' && json['tag'] )
		{
			ipb.gallery._changeMediaThumb( json );
			$('image_thumb_wrap_' + json['image_id'] ).down('.media_thumb_pop').writeAttribute('media-has-thumb', 'true');
			$('image_thumb_wrap_' + json['image_id'] ).down('.media_thumb_pop').value = ipb.lang['remove_img'];
		}
	},

	/**
	 * Change the media thumb
	 */
	_changeMediaThumb: function( json )
	{
		// If we have our temp div already, remove it
		if ( $('_tmp_xx_x') )
		{
			$('_tmp_xx_x').remove();
		}
		
		// Create our temp div and insert the data
		div = new Element( 'div', { id: '_tmp_xx_x', style: 'display:none' } );
		$('postingform').insert( div );
		
		$('_tmp_xx_x').update( json['tag'] ).hide();
		
		$('image_thumb_wrap_' + json['image_id'] ).down('img').writeAttribute( 'src', $('_tmp_xx_x').down('img').readAttribute('src') );
	},
	
	/**
	 * Pre rotate from delegate
	 */
	_rotate: function(e)
	{
		var elem = Event.element(e);
		var cn   = elem.className;
		
		if ( ! cn.match( /rotate/ ) )
		{
			cn = elem.up('.rotate').className;
		}
		var _id = cn.match( /_r(.+?)(\s|$)/ );
		
		if ( _id[1] )
		{
			ipb.gallery.imageID = _id[1];
			ipb.gallery.rotateImage(e, 'right' );
		}
	},

	/**
	 * Rotate an image
	 */
	rotateImage: function( e, dir )
	{
		Event.stop(e);
		if( ( dir != 'left' && dir != 'right' ) )
		{
			return;
		}

		new Ajax.Request( 
							ipb.vars['base_url']+'app=gallery&module=ajax&section=image&do=rotate-' + dir + '&secure_key=' + ipb.vars['secure_hash'] + '&img=' + ipb.gallery.imageID,
							{
								method: 'post',
								onSuccess: function(t)
								{
									// Get an error message?
									if( t.responseText == 'nopermission' )
									{
										alert( ipb.lang['no_permission'] );
									}
									else if( t.responseText == 'rotate_failed' )
									{
										alert( ipb.lang['gallery_rotate_failed'] );
									}
									else
									{
										var rand = Math.round( Math.random() * 100000000 );
										var img = $('image_view_' + ipb.gallery.imageID) ? $('image_view_' + ipb.gallery.imageID) : $('tn_image_view_' + ipb.gallery.imageID);
										var tmpSrc = img.src;
										var w      = parseInt( $( img ).width );
										var h      = parseInt( $( img ).height );
										
										tmpSrc = tmpSrc.replace(/t=[0-9]+/, '');
										
										$( img ).removeAttribute( 'width' );
										$( img ).removeAttribute( 'height' );
										$( img ).src = tmpSrc + "?t=" + rand;
										
										try
										{
											var div = $(img).up('.image_view_wrap');
											div.setStyle( 'width: auto; height: auto' );
										}
										catch( e ) { }
									}
								}
							}
						);
		return false;
		
	},
	
	/**
	 * Show the meta information popup
	 */
	showMeta: function(elem, e)
	{
		Event.stop(e);
		
		ipb.menus.closeAll(e);
		
		if( ipb.gallery.popups['meta'] )
		{
			ipb.gallery.popups['meta'].show();
		}
		else
		{
			ipb.gallery.popups['meta'] = new ipb.Popup( 'showmeta', { type: 'pane', modal: false, w: '600px', h: '500', initial: $('metacontent').innerHTML, hideAtStart: false, close: 'a[rel="close"]' } );
		}
		
		return false;
	},
	
	/**
	 * Show the share links popup
	 */
	showShareLinks: function(elem, e)
	{
		Event.stop(e);
		
		ipb.menus.closeAll(e);
		
		if( ipb.gallery.popups['sharelinks'] )
		{
			ipb.gallery.popups['sharelinks'].show();
		}
		else
		{
			ipb.gallery.popups['sharelinks'] = new ipb.Popup( 'showsharelinks', { type: 'pane', modal: false, w: '580px', h: '300px', initial: $('share_links_content').innerHTML, hideAtStart: false, close: 'a[rel="close"]' } );
		}
		
		return false;
	}
};

ipb.gallery.init();

var _mtuploader = window.IPBoard;

_mtuploader.prototype.mediaThumbUploader = Class.create({
	options:	[],
	boxes:		[],
	json:		{},

	/**
	 * Initialize the class
	 */
	initialize: function( id )
	{
		this.id = id;
		this.wrapper = 'mt_attachments_' + this.id;

		if( $('media_thumb_iframe_' + this.id ) )
		{
			$('media_thumb_iframe_' + this.id ).remove();
		}

		// Build our iframe
		this.iframe = new Element('iframe', { 	id: 'media_thumb_iframe_' + this.id,
		 										name: 'media_thumb_iframe_' + this.id,
												scrolling: 'no',
												frameBorder: 'no',
												border: '0',
												className: '',
												allowTransparency: true,
												src: ipb.vars['base_url'] + 'app=gallery&module=ajax&section=post&do=upload&type=mediathumb&id=' + this.id + '&secure_key=' + ipb.vars['secure_hash'],
												tabindex: '1'
											}).setStyle({
												width: '400px',
												height: '50px',
												overflow: 'hidden',
												backgroundColor: 'transparent'
											});
											
		$( this.wrapper ).insert( this.iframe ).addClassName('traditional');
		
		$('mt_add_files_' + this.id ).observe('click', this.processUpload.bindAsEventListener( this ) );
	},

	/**
	 * Processes upload
	 */
	processUpload: function( e )
	{
		Debug.write( "Process upload for " + this.id );
		var iFrameBox  = window.frames[ 'media_thumb_iframe_' + this.id ].document.getElementById('mtiframeUploadBox');
		var iFrameForm = window.frames[ 'media_thumb_iframe_' + this.id ].document.getElementById('mtiframeUploadForm');
		
		iFrameForm.action = ipb.vars['base_url'] + 'app=gallery&module=ajax&section=post&do=uploadSave&type=mediaThumb&id=' + this.id + '&secure_key=' + ipb.vars['secure_hash'];

		$(iFrameForm).submit();
	},
	
	/**
	 * Store the JSON data
	 */
	_setJSON: function( id, json )
	{
		Debug.dir( json );
		Debug.write( "ipb.uploader.js: Got JSON from the iFrame" );
		
		if ( json['error'] )
		{
			$('mtErrorBox_' + id).update( ipb.lang[ json['error'] ] );
			new Effect.Appear( $('mtErrorBox_' + id) );
		}
		else if ( json['ok'] && json['ok'] == 'done' )
		{
			ipb.gallery.mediaThumbClose( json );
		}
	}
});
