//----------------------------------------------
// ips.ticker
//
// Ticker JS for IP.Content block templates
//
// (c)2011 Invision Power Services, Inc.
// Author: Rikki Tissier
//----------------------------------------------
(function($, undefined){
	$.ccs.register('ips.ticker', {

		options: { 
			speed: 3,
			select: 'li'
		},

		_items: [],
		_loop: null,

		//----------------------------------------------
		// Init the ticker
		//----------------------------------------------
		_init: function()
		{
			var self = this;

			// Get items
			this._items = $( this.option('select'), this.element );	

			// Hide all but first
			this._items.hide().first().show();

			// Do events
			$( this.element )
				.bind('mouseenter', function(e){
					self.pause();
				})
				.bind('mouseleave', function(e){
					self.play();
				})
				.find( '.ccsTicker_next' )
					.bind('click', function(e){
						return self.nextItem();
					})
				.end()
				.find( '.ccsTicker_prev' )
					.bind('click', function(e){
						return self.prevItem();
					});

			// Start timer
			this.play();
		},

		//----------------------------------------------
		// Shows the next item, and resets the loop
		//----------------------------------------------
		_update: function()
		{
			this.nextItem();
			this.play();
		},

		//----------------------------------------------
		// Pauses the ticker
		//----------------------------------------------
		pause: function()
		{
			clearTimeout( this._loop );
		},

		//----------------------------------------------
		// Plays (unpauses) the ticker
		//----------------------------------------------
		play: function()
		{
			var self = this;
			this._loop = window.setTimeout( function(e){
				self._update();
			}, ( this.option('speed') * 1000 ));
		},

		//----------------------------------------------
		// Show the previous item
		//----------------------------------------------
		prevItem: function()
		{
			var cur = $( this.option('select') + ':visible', this.element ),
				prev = ( cur.prev().length ) ? cur.prev() : this._items.last();

			cur.fadeOut('slow');
			prev.fadeIn('slow');				
		},

		//----------------------------------------------
		// Show the next item
		//----------------------------------------------
		nextItem: function()
		{
			var cur = $( this.option('select') + ':visible', this.element ),
				next = ( cur.next().length ) ? cur.next() : this._items.first();

			cur.fadeOut('slow');
			next.fadeIn('slow');				
		}
	}, 'ipsTicker');
})(_ccsjQ);