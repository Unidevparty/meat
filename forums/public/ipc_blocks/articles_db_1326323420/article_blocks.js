//----------------------------------------------
// ips.articleBlocks
//
// Article blocks JS for IP.Content block templates
//
// (c)2011 Invision Power Services, Inc.
// Author: Rikki Tissier
//----------------------------------------------
(function($, undefined){
	$.ccs.register('ips.articleBlocks', {

		options: { 
			minBlockWidth: 200,
			heightFactor: 0.9,
			imageHeightFactor: 0.6,
			margin: 10 
		},

		_wrapper: null,
		_width: 0,
		_total: 0,
		
		_init: function(){
			var self = this;

			self
				//.recalculateBlocks()
				.recalculateBlocks();

			$(window).resize( function(){
				self.recalculateBlocks();
			});
		},

		recalculateBlocks: function()
		{
			this._wrapper = $('.ccsArticles_wrap', this.element);
			this._width = this._wrapper.width();
			this._total = $('.ccsArticles_block', this.element).length;

			var margin = this.option('margin'),
				minimum = this.option('minBlockWidth'),
				heightFactor = this.option('heightFactor'),
				imageHeightFactor = this.option('imageHeightFactor'),
				k = this._width,
				self = this;

			// Calculate how many blocks we can fit in this space
			var getOptimum = function(){
				for( i = self._total; i > 1; i-- ){
					k = Math.floor( ( self._width - ( ( i - 1 ) * margin ) ) / i );

					if( k > minimum ){
						return [ i, k ];
					}
					/* @link http://community.invisionpower.com/resources/bugs.html/_/ip-content/article-feed-block-with-medium-image-r38120 */
					else
					{
						return [ i, minimum ];
					}
				}

				return k;
			};

			var optimumSize = getOptimum();

			// Now loop through each block to apply styles
			$('.ccsArticles_block', this.element).each( function(idx, item){

				$( this )
					.css( 'width', optimumSize[1] + 'px' )
					.css( 'height', (optimumSize[1] * heightFactor) + 'px' )
					.css( 'margin-right', ( (idx+1) % optimumSize[0] != 0 ) ? margin + 'px' : '0px' );

				// Sort out the image
				var img = $( this ).find('.ccsArticles_img > img'),
					imgWrap = $( this ).find('.ccsArticles_img'),
					imageSize = [],
					newSize = {};

				if( imgWrap.length ){
					
					// Reset the image wrapper size so we can recalculate it below
					imgWrap.css( { width: 'auto', height: 'auto' } );
						
					var w = imgWrap.width();						
					imageSize = [ w, ( w * imageHeightFactor )];
					

					var newSize = $.ccs.util.fitImage( 
						{ 'w': imgWrap.data('width'), 'h': imgWrap.data('height') },
						{ 'w': imageSize[0], 'h': imageSize[1] }
					);
					
					img
						.css('width', newSize['w'] + 'px')
						.css('height', newSize['h'] + 'px')
						.css('top', newSize['t'] + 'px')
						.css('left', newSize['l'] + 'px');

					imgWrap
						.css('width', imageSize[0] + 'px')
						.css('height', imageSize[1] + 'px')
						.hide()
						.show();
				}
			});

			return this;
		}

	}, 'articleBlocks');
})(_ccsjQ);