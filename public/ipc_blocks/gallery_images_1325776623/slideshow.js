//----------------------------------------------
// ips.gallerySlideshow
//
// Slideshow widget for IP.Content Template Blocks
//
// (c)2011 Invision Power Services, Inc.
// Author: Rikki Tissier
//----------------------------------------------
(function($, undefined){
	$.ccs.register('ips.gallerySlideshow', {

		// Default options; can be overwritten when instantiated
		options: {
			autoPlay: true,
			speed: 4
		},

		_paused: false,
		_boxSize: {},

		//----------------------------------------------
		// Init function, called when this widget is instantiated
		//----------------------------------------------
		_init: function()
		{
			var self = this;

			if( !this.option('imageData') || this.option('imageData') == '' ){
				return;
			}

			this._getBoxSize();

			$( this.element ).append( 
				$('<div/>')
					.attr('id', this.option('baseID') + '_preloader')
					.addClass('ccsSlideshow_preloader')
					.css('position', 'absolute')
					.css('left', '-2000px')
					//.css('width', '1px')
					//.css('height', '1px')
					.css('overflow', 'hidden')
			);

			this._preloadImage( this.option('imageData').slice(0,1)[0] );
			
			$('.ccsSlideshow', this.element)
				.mouseenter( function(e){
					self.pause();
				})
				.mouseleave( function(e){
					self.unpause();
				});

			$('.ccsSlideshow_moveLeft', this.element).click( function(e){
				e.preventDefault();
				var prevID = self.getPrevID();
				self._update( prevID );
				return false;
			} );

			$('.ccsSlideshow_moveRight', this.element).click( function(e){
				e.preventDefault();
				var nextID = self.getNextID();
				self._update( nextID );
				return false;
			} );

			// GO!
			this.play();
		},

		//----------------------------------------------
		// Play the slideshow
		//----------------------------------------------
		play: function()
		{
			var self = this;

			var _mainLoop = function(){
				if( self._paused )
				{
					self._mainLoop = window.setTimeout( _mainLoop, ( self.option('speed') * 1000 ) );
					return;
				}

				var next = self.getNextID();
				self._update( next );

				// Preload next
				self._preloadImage( self.getNextID( next[0] ) );

				// Set up the loop
				self._mainLoop = window.setTimeout( _mainLoop, ( self.option('speed') * 1000 ) );
			}

			_mainLoop();
		},

		//----------------------------------------------
		// Pauses the slideshow
		//----------------------------------------------
		pause: function()
		{
			//clearTimeout( this._mainLoop );
			this._paused = true;
		},

		//----------------------------------------------
		// Unpause the slideshow
		//----------------------------------------------
		unpause: function()
		{
			//this.play();
			this._paused = false;
		},

		//----------------------------------------------
		// Change the currently-shown image
		//----------------------------------------------
		_update: function( image )
		{
			if( this.element.data('curID') == image[0] ){
				return;
			}

			this._preloadImage( image );

			var self = this,
				newImage = this._getImgObj( image[0] );

			if( (!newImage[0].complete) || !newImage.width() )
			{
				window.setTimeout( function(){
					Debug.write('(' + image[0] + ') Waiting for load...');
					return self._update.call( self, image ) 
				}, 50 );
				return;
			}

			// Position the image
			this._getBoxSize();
			this._resizeAndPositionImage( image );
			this._animateImageInfo( image );

			$('.ccsSlideshow_images').append( newImage.hide() );

			if( this.element.data('curID') == undefined ){
				newImage.fadeIn();
			} else {
				var current = this._getImgObj( this._findFromID( this.element.data('curID') )[0] );

				current.fadeOut();
				newImage.fadeIn();
			}

			// Update the current image
			this.element.data('curID', image[0]);
		},

		//----------------------------------------------
		// Builds the image info box
		//----------------------------------------------
		_animateImageInfo: function( image )
		{
			var infoDiv = $('.ccsSlideShow_imageinfo > div[data-id="' + image[0] + '"]', this.element);

			if( !infoDiv.length ){
				var content = this.option('infoTemplate')
								.replace('[title]', image[1]['caption'])
								.replace('[desc]', image[1]['description'])
								.replace('[album]', image[1]['album_name']);

				// Create div
				infoDiv = $('<div/>').attr('data-id', image[0]).html( content ).hide();

				$('.ccsSlideshow_imageinfo', this.element).append( infoDiv );
			} 

			$('.ccsSlideshow_imageinfo > div:not(div[data-id="' + image[0] + '"])').fadeOut('slow');
			infoDiv.fadeIn('slow');			
		},

		//----------------------------------------------
		// Resizes an image to optimum size
		//----------------------------------------------
		_resizeAndPositionImage: function( image )
		{
			var img = this._getImgObj( image[0] );

			/* @link http://community.invisionpower.com/resources/bugs.html/_/ip-content/improper-resizing-in-the-recent-images-hook-r40592 */
			if( img.height() < this._boxSize.height )
			{
				img.css('left', (this._boxSize.width - img.width())/2);
				return;
			}

			img.css('height', this._boxSize.height);
			img.css('left', (this._boxSize.width - img.width())/2);
			return;


			if( !img.data('origWidth') || !img.data('origHeight') ){
				img.data('origWidth', img.width()).data('origHeight', img.height());
			}

			var aspect = img.data('origWidth') / img.data('origHeight');
			var newSize = {};

			if( img.data('origWidth') > this._boxSize.width ){
				newSize.width = this._boxSize.width;
				newSize.height = ( newSize.width / aspect );
			} else {
				newSize.width = img.data('origWidth');
				newSize.height = img.data('origHeight');
			}

			img.css('width', newSize.width + 'px').css('height', newSize.height + 'px');

			var posLeft = Math.ceil( (this._boxSize.width - newSize.width) / 2 );
			var posTop = Math.ceil( (this._boxSize.height - newSize.height) / 2 );

			img.css('left', posLeft + 'px').css('top', posTop + 'px');
		},

		//----------------------------------------------
		// Shortcut for getting an image object
		//----------------------------------------------
		_getImgObj: function( id )
		{
			return $('img[data-id="' + id + '"]', this.element);
		},

		//----------------------------------------------
		// Preload function
		//----------------------------------------------
		_preloadImage: function( image )
		{
			var self = this;
			if( !this._getImgObj( image[0] ).length ){
				$('.ccsSlideshow_preloader', this.element).append( 
					// Can't use $.data here because we need to select on it later
					$('<img/>').attr('src', this._getFilename( image[1] )).attr('data-id', image[0]).bind('click', function(e){
						return self._clickImage(e);
					})
				);
			}
		},

		//----------------------------------------------
		// Event handler for clicking an image
		//----------------------------------------------
		_clickImage: function(e)
		{
			var imageData = this._findFromID( $(e.target).data('id') );
			window.top.location = imageData[1]['url'].replace( /&amp;/, '&' );
		},

		//----------------------------------------------
		// Get the next image ID
		//----------------------------------------------
		getNextID: function( curID )
		{
			var curID = curID || this.element.data('curID'),
				imageData = this.option('imageData');

			if( !curID ){
				return imageData.slice(0,1)[0];
			}

			for( i=0; i < imageData.length; i++ ){
				if( imageData[i][0] == curID ){
					var pos = i;
					break;
				}
			}
		
			if( pos == ( imageData.length - 1 ) ){
				return imageData[0];
			} else {
				return imageData[ pos + 1 ];
			}
		},

		//----------------------------------------------
		// Get the previous image ID
		//----------------------------------------------
		getPrevID: function( curID )
		{
			var curID = curID || this.element.data('curID'),
				imageData = this.option('imageData');

			if( !curID ){
				return imageData.slice(0,1)[0];
			}

			for( i=0; i < imageData.length; i++ ){
				if( imageData[i][0] == curID ){
					var pos = i;
					break;
				}
			}

			if( pos === 0 ){
				return imageData[ imageData.length - 1 ];
			} else {
				return imageData[ pos - 1 ];
			}
		},

		//----------------------------------------------
		// Returns image data array from given ID
		//----------------------------------------------
		_findFromID: function( id )
		{
			var imageData = this.option('imageData');

			for( i=0; i < imageData.length; i++ ){
				if( imageData[i][0] == id ){
					return imageData[i];
				}
			}

			return false;
		},

		//----------------------------------------------
		// Gets the size of the viewing area
		//----------------------------------------------
		_getBoxSize: function()
		{
			var elem = $('.ccsSlideshow_images', this.element);
			this._boxSize = { width: elem.outerWidth(), height: elem.outerHeight() };
		},

		_getFilename: function( image )
		{
			return this.option('rootImageURL') + image['fileName'];
		}

	}, 'gallerySlideshow' );
})(_ccsjQ);