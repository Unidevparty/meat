/************************************************/
/* IPB3 Javascript								*/
/* -------------------------------------------- */
/* ips.slideshow.js - Gallery slideshow code	*/
/* (c) IPS, Inc 2009							*/
/* -------------------------------------------- */
/* Author: Rikki Tissier						*/
/************************************************/

var _lb = window.IPBoard;

_lb.prototype.gallery_lightbox = {
	/* Cached responses */
	responses: new Hash(),

	/* Whether to initialize the automatic image linking */
	doAutoAdvanced: false,
	
	/**
	 * Constructor
	 */
	init: function()
	{
		Debug.write("Initializing ips.gallery_lightbox.js");

		// Check if we need to monitor image clicks
		document.observe("dom:loaded", function(){
			if( ipb.gallery_lightbox.doAutoAdvanced == true )
			//if( false )
			{
				ipb.delegate.register('.galimageview', ipb.gallery_lightbox.launchAdvanced );
			}
		});
	},
	
	/**
	 * L(a)unch
	 */
	launch: function()
	{
		// Lets get this party started
		$('ips_lightbox').show();
		
		if ( $('ips_lightbox').readAttribute('setup') == 'false' )
		{
			var dims  = $('ips_lightbox').readAttribute( 'dimensions' ).split('-');
			
			var img   = new Element('img').writeAttribute( 'src', $('ips_lightbox').readAttribute('fullimage') ).addClassName('lightbox_image');
			var div   = new Element('div').update( $('ips_lightbox').readAttribute( 'caption' ).escapeHTML() ).addClassName( 'lightbox_caption' );
			var close = new Element('div', { id: 'close_lightbox', style: 'cursor: pointer' } );
			
			if ( dims[0] && dims[1] )
			{
				//img.setStyle( 'max-width:' + parseInt( dims[0] ) + 'px; max-height: ' + parseInt( dims[1] ) + 'px' );
			}
			
			$('ips_lightbox').setStyle( { cursor: 'pointer' } );
			$('ips_lightbox').insert( close );
			$('ips_lightbox').insert( img );
			$('ips_lightbox').insert( div );
			$('ips_lightbox').observe( 'click', function(e) { $('ips_lightbox').hide(); } );
			
			$('ips_lightbox').writeAttribute('setup', 'true');
		}
	},

	/**
	 * Launch the advanced view
	 */
	launchAdvanced: function( e, elem )
	{
		Event.stop(e);

		// Show the lightbox view
		$('ips_lightbox').show();

		// Set up the lightbox if it isn't already
		if ( $('ips_lightbox').readAttribute('setup') == 'false' )
		{
			$$('body')[0].insert( $('ips_lightbox') );

			Event.observe( window, 'resize', ipb.gallery_lightbox.resizeWindow );

			$('ips_lightbox').writeAttribute('setup', 'true');

			ipb.delegate.register('.lightbox_load_next', ipb.gallery_lightbox.goNext );
			ipb.delegate.register('.lightbox_load_prev', ipb.gallery_lightbox.goPrev );
			ipb.delegate.register('#close_lightbox', ipb.gallery_lightbox.closeLightbox );

			Event.observe( document, 'keypress', ipb.gallery_lightbox.keyPressLightbox );
		}

		// What is the image id?
		var imageId	= $(elem).id.replace( /(tn|sml)_image_view_/, '' );

		if( imageId == 'undefined' || !imageId )
		{
			var imageId	= $(elem).down('img').id.replace( /(tn|sml)_image_view_/, '' );
		}

		// If we've fetched this image, pull from cache
		if( ipb.gallery_lightbox.responses.get( imageId ) )
		{
			return ipb.gallery_lightbox._doLaunchAdvanced( imageId, ipb.gallery_lightbox.responses.get( imageId ) );
		}

		// Set the spinner in action
		// Only show it if it's the first image, so that it's all more seamless to the user
		if( !$('ips_lightbox_content').visible() ){
			$('ips_lightbox_loading').show();
		}

		// Otherwise we need to fetch the HTML
		new Ajax.Request( ipb.vars['base_url'] + '&app=gallery&module=ajax&section=image&do=fetchAjaxView&secure_key=' + ipb.vars['secure_hash'] + '&imageId=' + imageId,
							{
								method: 'post',
								onSuccess: function(t)
								{
									ipb.gallery_lightbox.responses.set( imageId, t.responseText );

									return ipb.gallery_lightbox._doLaunchAdvanced( imageId, t.responseText );
								}
							}
						);
	},

	/**
	 * Check for keyboard shortcuts 
	 */
	keyPressLightbox: function(e){
		Debug.write("Pressed " + e.keyCode );

		if( e.keyCode == Event.KEY_ESC ){
			ipb.gallery_lightbox.closeLightbox(e);
		} else if( e.keyCode == Event.KEY_LEFT ){
			ipb.gallery_lightbox.goPrev(e);
		} else if( e.keyCode == Event.KEY_RIGHT ){
			ipb.gallery_lightbox.goNext(e);
		}
	},

	/**
	 * Close lightbox
	 */
	closeLightbox: function(e, elem){
		Event.stop(e);
		$(elem).hide();
		$('ips_lightbox').fade({ duration: 0.2 });
		return false;
	},

	/**
	 * Go to the next image
	 */
	goNext: function(e, elem)
	{
		try	{
			return ipb.gallery_lightbox.launchAdvanced( e, $('tn_image_view_' + ipb.gallery_lightbox.imageData['imageID'] ).up('li').next('li').down('.galimageview') );
		} catch( e ) {
			return false;
		}
	},

	/**
	 * Go to the previous image
	 */
	goPrev: function(e, elem)
	{
		try	{
			return ipb.gallery_lightbox.launchAdvanced( e, $('tn_image_view_' + ipb.gallery_lightbox.imageData['imageID'] ).up('li').previous('li').down('.galimageview') );
		} catch( e ) {
			return false;
		}
	},

	/**
	 * Resize the image
	 */
	resizeImage: function( img )
	{
		if( !img ){
			img = $$('#theImage img').first();
			ipb.gallery_lightbox.containerSize = $('imageHolder').getDimensions();
		}

		$(img).setStyle({ maxHeight: ipb.gallery_lightbox.containerSize['height'] + 'px', maxWidth: ipb.gallery_lightbox.containerSize['width'] + 'px' });
	},

	/**
	 * Fix lightbox if window is resized
	 */
	resizeWindow: function( e ){
		ipb.gallery_lightbox.positionCenter();
		ipb.gallery_lightbox.resizeImage();
	},

	/**
	 * Center the lightbox
	 */
	positionCenter: function()
	{
		// Horizontal align is done with CSS. Here, we set line-height that is the height
		// of the container, which centers vertically
		$('imageHolder').setStyle( { 'line-height': ( ipb.gallery_lightbox.containerSize['height'] - 2 ) +'px'} );
	},

	/**
	 * Actually launch the advanced view
	 */
	_doLaunchAdvanced: function( imageId, html )
	{
		$('ips_lightbox_content').update( html );
		$('ips_lightbox_loading').hide();
		$('ips_lightbox_content').show();
		$('close_lightbox').show();

		if( !$('theImage') )
		{
			return false;
		}

		ipb.gallery_lightbox.imageData = {
			imageID: $('theImage').readAttribute('data-imageid'),
			largeSrc: $('theImage').readAttribute('data-largesrc'),
			largeDims: $('theImage').readAttribute('data-largedims').split(':'),
			mediumSrc: $('theImage').readAttribute('data-mediumsrc'),
			mediumDims: $('theImage').readAttribute('data-mediumdims').split(':'),
			isImage: ( $('theImage').readAttribute('data-type') == 'image' ) ? true : false
		};

		ipb.gallery_lightbox.containerSize = $('imageHolder').getDimensions();

		var img = new Element('img');

		// Determine which image to show
		if( ipb.gallery_lightbox.imageData.mediumDims[0] >= ipb.gallery_lightbox.containerSize['width'] && 
			ipb.gallery_lightbox.imageData.mediumDims[1] >= ipb.gallery_lightbox.containerSize['height'] )
		{
			img.writeAttribute('src', ipb.gallery_lightbox.imageData['mediumSrc'] );
		}
		else
		{
			img.writeAttribute('src', ipb.gallery_lightbox.imageData['largeSrc'] );
			ipb.gallery_lightbox.resizeImage( img );
		}

		ipb.gallery_lightbox.positionCenter();

		if( $('theImageAnchor') )
		{
			$('theImageAnchor').insert( { bottom: img } ).hide().appear( { duration: 0.3 } );
		}
		else
		{
			$('theImage').insert( { bottom: img } ).hide().appear( { duration: 0.3 } );
		}

		try {
			if( $('tn_image_view_' + ipb.gallery_lightbox.imageData['imageID'] ).up('li').previous('li').down('.galimageview') ){
				$$('.lightbox_load_prev').first().show();
			} else {
				$$('.lightbox_load_prev').first().hide();
			}
		} catch(err) {
			$$('.lightbox_load_prev').first().hide();
		}

		try {
			if( $('tn_image_view_' + ipb.gallery_lightbox.imageData['imageID'] ).up('li').next('li').down('.galimageview') ){
				$$('.lightbox_load_next').first().show();	
			} else {
				$$('.lightbox_load_next').first().hide();
			}
		} catch(err) {
			$$('.lightbox_load_next').first().hide();
		}

		return false;
	}
};

ipb.gallery_lightbox.init();