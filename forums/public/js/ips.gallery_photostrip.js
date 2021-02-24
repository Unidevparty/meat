/**************************************************/
/* IPB3 Javascript								  */
/* --------------------------------------------   */
/* ips.gallery_photostrip.js - Gallery javascript */
/* (c) IPS, Inc 2008							  */
/* --------------------------------------------   */
/* Author: Matt Mecham							  */
/**************************************************/

var _photostrip = window.IPBoard;

_photostrip.prototype.photostrip = {
	
	_photoData: {},
	_total: 0, 
	_boxes: 0,
	_current: { 'left' : false, 'right': false, 'rightHit': false, 'leftHit': false },
	_seen: new Hash(),
	_stripless: false,
	_stopLeft: false,
	_stopRight: false,
	_clicked: false,
	
	/**
	 * Constructor
	 */
	init: function()
	{
		Debug.write("Initializing ips.gallery_photostrip.js");
		
		document.observe("dom:loaded", function()
		{
			ipb.photostrip._stripless  = ipb.vars['img_url'] + '/gallery/slider/stripless.png';
			ipb.photostrip._stopLeft   = ipb.vars['img_url'] + '/gallery/slider/stopLeft.png';
			ipb.photostrip._stopRight  = ipb.vars['img_url'] + '/gallery/slider/stopRight.png';
		});
	},
	
	/**
	 * Manually set photo data
	 */
	setPhotoData: function( json )
	{
		// { total: xx, photos { } }
		ipb.photostrip._photoData = json;
	},
	
	/**
	 * Main display function
	 */
	display: function()
	{
		if ( $H( ipb.photostrip._photoData ) )
		{
			// Set total
			ipb.photostrip._total = ipb.photostrip._photoData['total'];
			
			// Create boxex
			for( i = -2 ; i <= 2 ; i++ )
			{
				// insert blanks
				$('strip').insert( new Element( 'li', { 'id': 'strip_li_' + i } ).insert( new Element( 'img', { 'src': ipb.photostrip._stripless, 'class': 'galattach emptyBox' } ) ) );
				$('strip_li_' + i ).imageId = 0;
			}
			
			// Set box count
			ipb.photostrip._boxes = 5;

			$H( ipb.photostrip._photoData['photos'] ).each( function(data)
			{
				var key   = data.key;
				var image = data.value;
				
				if ( ! Object.isFunction( image ) )
				{
					Debug.write( 'here' + key );

					$('strip_li_' + key ).update( image['thumb'] );
					$('strip_li_' + key ).imageId = image['image_id'];

					if( image['_ourImage'] == 1 )
					{
						$('strip_li_' + key ).down('img').addClassName( 'photostripActive' ).removeClassName('emptyBox');
					}
				}
			} );
			
			// Set up seen
			ipb.photostrip._seen['l-2,r2'] = true;
			
			// Check edges
			ipb.photostrip._current['left']  = -2;
			ipb.photostrip._current['right'] = 2;
			
			ipb.photostrip._checkEdges('left');
			ipb.photostrip._checkEdges('right');
			
			ipb.photostrip._showSliders();
			
			// When window is resized, replace sliders in correct location
			Event.observe( window, 'resize', ipb.photostrip._showSliders );
		}
	},
	
	/**
	 * Show the sliders
	 */
	_showSliders: function( e )
	{
		$('slide_left').observe( 'click', ipb.photostrip.slide.bindAsEventListener( this, 'left' ) );
		$('slide_right').observe( 'click', ipb.photostrip.slide.bindAsEventListener( this, 'right' ) );

		if ( ipb.photostrip._current['leftHit'] !== false )
		{
			$('slide_left').hide();
		}
		
		if ( ipb.photostrip._current['rightHit'] !== false )
		{
			$('slide_right').hide();
		}
	},
	
	/**
	 * Hide the sliders
	 */
	_hideSliders: function( e )
	{
		elem = Event.findElement(e);
		
		// only hide if we're outside the hot zone
		if ( elem.hasClassName('photoStipNav') )
		{
			return true;
		}
		 
		$('slide_left').hide();
		$('slide_right').hide();
	},
	
	/**
	 * Fetch current visible left image
	 * UL.margin-left + LI.left = 0
	 */
	fetchLeft: function()
	{
		if ( ipb.photostrip._current['left'] === false )
		{
			// Go through all ell eyes
			$$('.__li').each( function( i )
			{ 
				id = i.id.replace( /strip_li_/, '' );
				
				if ( ipb.photostrip._current['left'] === false && $(i.id) && $(i.id).visible() )
				{ 
					ipb.photostrip._current['left'] = id;
					throw $break;
				}
			} );
		}
		
		return ipb.photostrip._current['left'];
	},
	
	/**
	 * Fetch current visible left right
	 * current.left + 4
	 */
	fetchRight: function()
	{
		if ( ipb.photostrip._current['right'] === false )
		{
			ipb.photostrip._current['right'] = parseInt( ipb.photostrip.fetchLeft() ) + 4;
		}
		
		return ipb.photostrip._current['right'];
	},
	
	/**
	 * Clear current cached data
	 */
	clearCurrent: function()
	{
		ipb.photostrip._current['left']  = false;
		ipb.photostrip._current['right'] = false;
	},
	
	/**
	 * Fetch element of left or right
	 */
	fetchElement: function( id )
	{
		if ( $('strip_li_' + id ) )
		{
			return $('strip_li_' + id );
		}
		else
		{
			return false;
		}
	},
	
	/**
	 * Generic album select pop-up
	 */
	slide: function( e, direction )
	{
		Event.stop(e);
		direction = ( direction == 'left' ) ? 'left' : 'right';
		
		// Stop multiple clicks
		if ( ipb.photostrip._clicked === true )
		{
			return;
		}
		
		ipb.photostrip._clicked = true;
		
		Debug.write( "A jump to the " + direction );
		
		// Current
		left       = ipb.photostrip.fetchLeft();
		right      = ipb.photostrip.fetchRight();
		
		// After
		if ( direction == 'left' )
		{
			afterRight = left;
			afterLeft  = parseInt( afterRight ) - 4;
		}
		else
		{
			afterLeft  = ipb.photostrip._current['right'];
			afterRight = parseInt( afterLeft ) + 4;
		}
		
		Debug.write( "Before: left: " + left + ', right: ' + right );
		Debug.write( "After: left: " + afterLeft + ', right: ' + afterRight );
		
		// Already hit our limits?
		if ( ipb.photostrip._current[ direction + 'Hit' ] === true )
		{
			return false;
		}
		
		// Reset
		if ( direction == 'left' )
		{
			// Reset right
			ipb.photostrip._current['rightHit'] = false;
			$('slide_right').show();
		}
		else
		{
			// Reset left
			ipb.photostrip._current['leftHit'] = false;
			$('slide_left').show();
		}
		
		// Been here before?
		if ( ! Object.isUndefined( ipb.photostrip._seen[ 'l' + afterLeft.toString() + ',r' + afterRight.toString() ] ) )
		{
			// Slide the slider
			ipb.photostrip._slideIt( direction, afterLeft, afterRight );
			
			Debug.write( "Cached, just sliding" );
			
			// Update edges
			ipb.photostrip._current['left']  = afterLeft;
			ipb.photostrip._current['right'] = afterRight;
			
			// Update hash
			ipb.photostrip._seen[ 'l' + afterLeft.toString() + ',r' + afterRight.toString() ] = true;
			
			// Check edges
			ipb.photostrip._checkEdges( direction );
			
			setTimeout( "ipb.photostrip._resetClicked()", 700 );
					
			// DONE
			return;
		}

		// Which way to move?
		if ( direction == 'right' )
		{
			// Add in more boxes to the right
			for( var c = 0 ; c <= 3 ; c++ )
			{
				i = right + c + 1;
				
				// insert blanks
				if ( ! $('strip_li_' + i ) )
				{
					$('strip').insert( new Element( 'li', { 'id': 'strip_li_' + i, 'style': 'display:none;' } ).insert( new Element( 'img', { 'src': ipb.photostrip._stripless, 'class': 'galattach emptyBox' } ) ) );
					$('strip_li_' + i ).imageId = 0;
				}
			}
			
			// Set pos
			dirPos = right;

			setTimeout( "ipb.photostrip._resetClicked()", 700 );
		}
		else if ( direction == 'left' )
		{
			// Add in more boxes to the left
			for( var i = left - 1 ; i >= left - 5 ; i-- )
			{
				// insert blanks
				if ( ! $('strip_li_' + i ) )
				{
					$('strip').insert( { top: new Element( 'li', { 'id': 'strip_li_' + i, 'style': 'display:none;' } ).insert( new Element( 'img', { 'src': ipb.photostrip._stripless, 'class': 'galattach emptyBox' } ) ) } );
					$('strip_li_' + i ).imageId = 0;
				}
			}
			
			// Set pos
			dirPos = left;

			setTimeout( "ipb.photostrip._resetClicked()", 700 );
		}

		// Slide the strip
		ipb.photostrip._slideIt( direction, afterLeft, afterRight );
		
		// Set box count
		ipb.photostrip._boxes += 4;

		// Fetch JASON
		new Ajax.Request( ipb.vars['base_url']+'app=gallery&module=ajax&section=photostrip&do=slide&secure_key=' + ipb.vars['secure_hash'] + '&directionPos=' + dirPos + '&direction=' + direction + '&left=' + ipb.photostrip.fetchElement( left ).imageId + '&right=' + ipb.photostrip.fetchElement( right ).imageId,
		{
			method: 'post',
			evalJSON: 'force',
			onSuccess: function(t)
			{
				if( Object.isUndefined( t.responseJSON ) ){ alert( ipb.lang['action_failed'] ); return; }
				
				ipb.photostrip._photoData['photos'] = t.responseJSON['photos'];
				
				if ( $H( ipb.photostrip._photoData['photos'] ) )
				{
					$H( ipb.photostrip._photoData['photos'] ).each( function(data)
					{
						var key   = data.key;
						var image = data.value;
						
						try
						{
							if ( ! Object.isFunction( image ) && $('strip_li_' + key ) )
							{
								$('strip_li_' + key ).update( image['thumb'] );
								$('strip_li_' + key ).imageId = image['image_id'];

								$('strip_li_' + key ).down('img').removeClassName('emptyBox');
							}
						}
						catch(e) { Debug.dir( e ); }
					} );
					
					// Update edges
					ipb.photostrip._current['left']  = afterLeft;
					ipb.photostrip._current['right'] = afterRight;
					
					// Update hash
					ipb.photostrip._seen[ 'l' + afterLeft.toString() + ',r' + afterRight.toString() ] = true;
					
					// Check edges
					ipb.photostrip._checkEdges( direction );
				}
			}
		});
	},
	
	/**
	 * Reset clicked value
	 */
	_resetClicked: function()
	{
		ipb.photostrip._clicked = false;
	},
	
	/**
	 * Slide the slider
	 */
	_slideIt: function( direction, afterLeft, afterRight )
	{
		if( direction == 'right' )
		{
			for( var i=afterLeft + 1, j=afterRight; i <= j; i++ )
			{
				new Effect.Parallel([
							new Effect.Fade( $('strip_li_' + parseInt( i - 5 ) ), { sync: true } ),
							new Effect.Appear( $('strip_li_' + i ), { sync: true } )
							], { duration: 0.3 } );
			}
		}
		else
		{
			for( var i=afterLeft, j=afterRight; i < j; i++ )
			{
				new Effect.Parallel([
							new Effect.Fade( $('strip_li_' + parseInt( i + 5 ) ), { sync: true } ),
							new Effect.Appear( $('strip_li_' + i ), { sync: true } )
							], { duration: 0.3 } );
			}
		}
	},
	
	/**
	 * Check edges
	 */
	_checkEdges: function( direction )
	{
		// Reset edges
		if ( direction == 'left' )
		{
			// Test for limits
			try
			{
				if ( ipb.photostrip.fetchElement( ipb.photostrip._current['left'] ).imageId == 0 )
				{
					ipb.photostrip._current['leftHit'] = true;
					$('slide_left').hide();
					
					// Start from the right and find the first non image
					for( i = ipb.photostrip._current['right'] ; i >= (ipb.photostrip._current['right'] - 5 ) ; i-- )
					{
						obj = ipb.photostrip.fetchElement( i );
						
						if ( obj !== false && obj.imageId == 0 )
						{
							obj.down('.galattach').src    = ipb.photostrip._stopLeft;
							break;
						}
					}
					
					Debug.write( "Left hit" );
				}
			} catch( e ) { }
			
		}
		else
		{
			// Test for limits
			try
			{
				if ( ipb.photostrip.fetchElement( ipb.photostrip._current['right'] ).imageId == 0 )
				{
					ipb.photostrip._current['rightHit'] = true;
					$('slide_right').hide();
					
					// Start from the left and find the first non image
					for( i = ipb.photostrip._current['left'] ; i <= (ipb.photostrip._current['left'] + 5 ) ; i++ )
					{
						obj = ipb.photostrip.fetchElement( i );
						
						if ( obj !== false && obj.imageId == 0 )
						{
							obj.down('.galattach').src    = ipb.photostrip._stopRight;
							break;
						}
					}
					
					Debug.write( "Right hit" );
				}
			} catch( e ) { }
		}
	}
};

ipb.photostrip.init();