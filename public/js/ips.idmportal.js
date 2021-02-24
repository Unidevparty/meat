/************************************************/
/* IPB3 Javascript								*/
/* -------------------------------------------- */
/* ips.idmportal.js - Portal & Categories		*/
/* (c) IPS, Inc 2011							*/
/* -------------------------------------------- */
/* Author: Rikki Tissier						*/
/************************************************/

var _idmportal = window.IPBoard;

_idmportal.prototype.idmportal = {
	
	/*
	 * Generic initialization function
	 */
	init: function()
	{
		Debug.write( "Initializing ips.idmportal.js" );
		
		document.observe("dom:loaded", function(){
			if( $('featured_pane') )
			{
				new idmportal_slider.carousel( $('featured_pane') );
			}
		});
		
		// Menu toggle
		ipb.delegate.register('.cat_toggle', ipb.idmportal.toggleCategory);
	},
	
	toggleCategory: function(e, elem)
	{
		Event.stop(e);
		
		var group = $( elem ).up('li');
		var subgroup = $( group ).down('.subforums');
		
		if( !$( group ) || !$( subgroup ) )
		{
			Debug.write("Can't find parent or subforums");
			return;
		}
		
		if( $( group ).hasClassName('closed') )
		{
			new Effect.BlindDown( $( subgroup ), { duration: 0.2 } );
			$( group ).removeClassName('closed').addClassName('open');
		}
		else
		{
			new Effect.BlindUp( $( subgroup ), { duration: 0.2 } );
			$( group ).removeClassName('open').addClassName('closed');
		}
		
	},
	
	/* INIT menu items*/
	setUpAjMenu: function( type )
	{
		Debug.write('setting up menu' );
		switch( type )
		{
		case 'all':
			ipb.delegate.register(".__allmenu", ipb.idmportal.ajMenu);		
		  break;
		case 'free':
			ipb.delegate.register(".__freemenu", ipb.idmportal.ajMenu);		
		  break;
		case 'paid':
			ipb.delegate.register(".__paidmenu", ipb.idmportal.ajMenu);
		  break;
		case 'author':
			ipb.delegate.register(".__authormenu", ipb.idmportal.ajMenu);
		  break;
		}	
	},
	
	ajMenu: function( e, elem )
	{
		Event.stop(e);

		var type = $( elem ).className.match('__t([a-z]+)');
		var period = $( elem ).className.match('__x([a-z]+)');
		
		if( period == null || Object.isUndefined( period[1] ) || type == null || Object.isUndefined( type[1] ) ){ Debug.error("Error showing popup"); return; }
		
		var newKey = $( elem ).innerHTML;
		var url = ipb.vars['base_url'] + "app=downloads&module=ajax&section=sidebar&type=" + type[1]  + "&period=" + period[1] + '&md5check=' + ipb.vars['secure_hash'];
		
		new Ajax.Request( url.replace(/&amp;/g, '&'),
						{
							method: 'post',
							evalJSON: 'force',
							onSuccess: function(t)
							{		
								switch( t.responseJSON['type'] )
								{
								case 'all':
									$('allajaxcontent').update( t.responseJSON['data'] );
									$('alltitle').update( newKey );
									$('allajax_menucontent').hide();			
								  break;
								case 'free':
									$('freeajaxcontent').update( t.responseJSON['data'] );
									$('freetitle').update( newKey );
									$('freeajax_menucontent').hide();			
								  break;
								case 'paid':
									$('paidajaxcontent').update( t.responseJSON['data'] );
									$('paidtitle').update( newKey );
									$('paidajax_menucontent').hide();	
								  break;
								case 'author':
									$('authorajaxcontent').update( t.responseJSON['data'] );
									$('authortitle').update( newKey );
									$('authorajax_menucontent').hide();	
								  break;
								}	
							}
						});
		
	},
	
	scroller: Class.create({
		
		initialize: function( id, total )
		{
			this.id				= id;
			this.total			= total;
			this.effect			= Effect.Transitions.sinoidal;
			this.current		= 1;
			this.isScrolling	= false;
			
			// Check for required elements
			if( !$( this.id + '_wrap' ) || !$( this.id + '_l' ) || !$( this.id + '_r' ) )
			{
				Debug.error("Required element missing");
				return false;
			}
			
			// Set panel height
			$( this.id + '_wrap' ).setStyle( 'height: ' + $( this.id + '_wrap' ).getHeight() + 'px' );
			
			// Set columns
			var w = this.getPaneWidth();
			$( this.id + '_' + this.current ).setStyle('position: absolute; width: ' + w + 'px;');

			// Check resize
			this.windowResize();
			
			// Set events
			$( this.id + '_l' ).observe( 'click', this.scrollLeft.bindAsEventListener(this) );
			$( this.id + '_r' ).observe( 'click', this.scrollRight.bindAsEventListener(this) );
			Event.observe( window, 'resize', this.windowResize.bindAsEventListener(this) );
		},
		
		scrollLeft: function(e)
		{
			if( this.isScrolling )
			{
				Event.stop(e);
				return false;
			}

			if( this.current != 1 )
			{
				this.isScrolling	= true;

				var prev = $( this.id + '_' + (this.current - 1) );
				var w = this.getPaneWidth();
				var _t = this.current;
				
				$( prev ).setStyle('position: absolute; width: ' + w + 'px; left: ' + ( w * -1 ) + 'px;').show();
				$( this.id + '_' + this.current ).setStyle('position: absolute; width: ' + w + 'px');
				
				new Effect.Parallel([
					new Effect.Move( $( prev ), { x: 0, y: 0, mode: 'absolute' } ),
					new Effect.Move( $( this.id + '_' + this.current ), { x: w, y: 0, mode: 'absolute' } )
				], { duration: 1, transition: this.effect, afterFinish: function(){
					$( this.id + '_' + _t ).hide();
					this.isScrolling	= false;
				}.bind(this) } );
				
				this.current = this.current - 1;
			}
			
			this.checkButtonDisable();			
			Event.stop(e);
		},
		
		scrollRight: function(e)
		{
			if( this.isScrolling )
			{
				Event.stop(e);
				return false;
			}

			if( this.current != this.total )
			{
				this.isScrolling	= true;

				var next = $( this.id + '_' + (this.current + 1) );
				var w = this.getPaneWidth();
				var _t = this.current;
				
				$( next ).setStyle('position: absolute; width: ' + w + 'px; left: ' + w + 'px').show();
				$( this.id + '_' + this.current ).setStyle('position: absolute; width: ' + w + 'px;');

				new Effect.Parallel([
					new Effect.Move( $( next ), { x: 0, y: 0, mode: 'absolute' } ),
					new Effect.Move( $( this.id + '_' + this.current ), { x: (w * -1), y: 0, mode: 'absolute' } )
				], { duration: 1, transition: this.effect, afterFinish: function(){
					$( this.id + '_' + _t ).hide();
					this.isScrolling	= false;
				}.bind(this) } );
				
				this.current = this.current + 1;
			}
			
			this.checkButtonDisable();	
			Event.stop(e);
		},
		
		windowResize: function(e)
		{
			var w = this.getPaneWidth();
			
			if( w < 420 ){
				$( this.id + '_wrap' ).removeClassName('three_column').removeClassName('two_column').addClassName('one_column');
			} else if( w < 690 ){
				$( this.id + '_wrap' ).removeClassName('three_column').removeClassName('one_column').addClassName('two_column');
			} else {
				$( this.id + '_wrap' ).removeClassName('one_column').removeClassName('two_column').addClassName('three_column');
			}
			
			$( this.id + '_' + this.current ).setStyle('width: ' + w + 'px');

			this.rebuildPanes();

			// Turn on the buttons
			this.checkButtonDisable();
		},

		rebuildPanes: function()
		{
			// The default is 3 columns which has 6 items
			if( $( this.id + '_1' ) && $( this.id + '_wrap' ).hasClassName('three_column') && ( $( this.id + '_1' ).select('li').length == 6 || !$( this.id + '_2' ) ) )
			{
				return;
			}

			// If we have 2 columns and 4 children, that's fine too
			if( $( this.id + '_1' ) && $( this.id + '_wrap' ).hasClassName('two_column') && ( $( this.id + '_1' ).select('li').length == 4 || !$( this.id + '_2' ) ) )
			{
				return;
			}

			// Or 1 column with 2 children..
			if( $( this.id + '_1' ) && $( this.id + '_wrap' ).hasClassName('one_column') && ( $( this.id + '_1' ).select('li').length == 2 || !$( this.id + '_2' ) ) )
			{
				return;
			}

			// If we're still here we need to rebuild...
			// First get all of the items in the entire panel
			var portalItems = $( this.id + '_wrap' ).select('li');

			// Now figure out how many per pane
			if( $( this.id + '_wrap' ).hasClassName('three_column') )
			{
				var perpane	= 6;
			}
			else if( $( this.id + '_wrap' ).hasClassName('two_column') )
			{
				var perpane	= 4;
			}
			else
			{
				var perpane	= 2;
			}

			// Remove old panes
			var i = 18, j = 1;
			while( j < i )
			{
				if( $( this.id + '_' + j ) )
				{
					$( this.id + '_' + j ).remove();
				}

				j++;
			}

			// How many panes do we have?
			var panes	= Math.ceil( portalItems.length / perpane );

			this.total	= panes;

			// Reset us to first pane
			this.current	= 1;

			// Loop over the panes - start at 1 and do post-iteration increment
			for( var i=1; i < ( panes + 1 ); i++ )
			{
				var newPane = new Element( 'ul' );
				var slice	= portalItems.slice( ( i - 1 ) * perpane, ( i * perpane ) + 1 );

				slice.each( function(elem){
					newPane.insert( { bottom: elem } );
				});

				if( $( this.id + '_' + i ) )
				{
					$( this.id + '_' + i ).remove();
				}

				newPane.writeAttribute( 'id', this.id + '_' + i );

				if( i != this.current )
				{
					newPane.setStyle( { display: 'none' } );
				}

				$( this.id + '_wrap' ).insert( { bottom: newPane } );
			}
		},
		
		getPaneWidth: function(e)
		{
			return $( this.id + '_wrap' ).getWidth();
		},
		
		checkButtonDisable: function()
		{
			if( this.current == 1 ){
				$( this.id + '_l' ).addClassName('disabled');
				
				if( this.total > 1 ){
					$( this.id + '_r').removeClassName('disabled');
				}
			}
			else if( this.current == this.total ){
				$( this.id + '_r' ).addClassName('disabled');
				
				if( this.total > 1 ){
					$( this.id + '_l').removeClassName('disabled');
				}
			}
			else {
				$( this.id + '_r' ).removeClassName('disabled');
				$( this.id + '_l' ).removeClassName('disabled');
			}	
			
		}		
	})	
};

ipb.idmportal.init();


var idmportal_slider = { };

idmportal_slider.carousel = Class.create({
	
	hLeft			: false,
	hRight			: false,
	wrapper			: null,
	items			: null,
	active			: null,
	autoProgress	: null,
	
	/**
	 * Initialize the carousel
	 */
	initialize: function( wrapper, options )
	{
		this.wrapper = $(wrapper);
		this.options = Object.extend({
			handle: 'feature',
			duration: 5
		}, options || {});

		if( $( this.options.handle + '_left' ) ){
			this.hLeft	= $( this.options.handle + '_left' );
		}

		if( $( this.options.handle + '_right' ) ){
			this.hRight	= $( this.options.handle + '_right' );
		}

		try
		{
			this.items	= this.wrapper.select(".featured_file_panel");
		}
		catch( e )
		{
			return false;
		}

		this.items.each( function(elem, index){
			if( index > 0 )
			{
				$(elem).hide();
			}
		});

		this.active	= this.items.detect(function(n){ return $(n).visible(); });
		
		$(this.wrapper).on( 'mouseenter', this.mouseEnter.bindAsEventListener(this) );
		$(this.wrapper).on( 'mouseleave', this.mouseLeave.bindAsEventListener(this) );
		$(this.wrapper).on( 'click', 'a.carousel_nav', this.mouseClick.bind(this) );
		
		if( this.hLeft && this.hRight && this.items.length > 1 )
		{
			this.hLeft.show().setStyle('opacity: 0.12');
			this.hRight.show().setStyle('opacity: 0.12');
		}

		if ( this.items.length > 1 )
		{
			this.startAutoProgress();
		}
	},

	/**
	 * Callback for when a user mouses over carousel
	 */
	mouseEnter: function(e)
	{
		if( this.hLeft && this.hRight )
		{
			new Effect.Morph( this.hLeft, { 'style':'opacity: 1;', duration: 0.3 } );
			new Effect.Morph( this.hRight, { 'style':'opacity: 1;', duration: 0.3 } );
		}
		
		clearTimeout( this.autoProgress );
	},

	/**
	 * Callback for when a user mouses out of carousel
	 */
	mouseLeave: function(e)
	{
		if( this.hLeft && this.hRight )
		{
			new Effect.Morph( this.hLeft, { 'style':'opacity: 0.12;', duration: 0.3 } );
			new Effect.Morph( this.hRight, { 'style':'opacity: 0.12;', duration: 0.3 } );
		}
		
		if ( this.items.length > 1)
		{
			this.startAutoProgress();
		}
	},

	/**
	 * Callback for when a user clicks on the next/prev buttons
	 */
	mouseClick: function(e, element)
	{
		Event.stop(e);

		if( $(element).hasClassName('carousel_right') )
		{
			this.updatePane( this.getNext( this.active ) );
		}
		else if( $(element).hasClassName('carousel_left') )
		{
			this.updatePane( this.getPrev( this.active ) );
		}
	},

	/**
	 * Initialize carousel auto-progression
	 */
	startAutoProgress: function()
	{
		this.autoProgress = setTimeout( function(){ 
			this.updatePane( this.getNext( this.active ) );
			this.startAutoProgress();
		}.bind(this), this.options.duration * 1000 );		
	},

	/**
	 * Update the displayed image
	 */
	updatePane: function( newPane )
	{
		new Effect.Fade( $( this.active ), { duration: 0.5 } );
		new Effect.Appear( $( newPane ), { duration: 0.5 } );
		
		this.active = newPane;
	},

	/**
	 * Retrieve the next image
	 */
	getNext: function( cur )
	{
		// If no ID or currentImage is specified, return first image
		if( Object.isUndefined( cur ) ){
			return this.items.first();
		}
		
		var pos = this.items.indexOf( cur );
		
		// Last item?
		if( pos == ( this.items.length - 1 ) )
		{
			return this.items.first();
		}
		else
		{
			return this.items[ pos + 1 ];
		}
	},

	/**
	 * Retrieve the previous image
	 */
	getPrev: function( cur )
	{
		// If no ID or currentImage is specified, return first image
		if( Object.isUndefined( cur ) ){
			return this.items.first();
		}

		var pos = this.items.indexOf( cur );

		// Last item?
		if( pos === 0 )
		{
			return this.items.last();
		}
		else
		{
			return this.items[ pos - 1 ];
		}
	}
});