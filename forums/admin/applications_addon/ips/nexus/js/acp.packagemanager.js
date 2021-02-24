/************************************************/
/* IPB3 Javascript								*/
/* -------------------------------------------- */
/* acp.packagemanager.js - fancy package manager*/
/* (c) IPS, Inc 2010							*/
/* -------------------------------------------- */
/* Author: Rikki Tissier 						*/
/************************************************/

var _packagemanager = window.IPBACP;
_packagemanager.prototype.packagemanager = {
	
	popups: [],
	sortables_packages: [],
	sortables_groups: [],
	selected: [],
	
	/*
	 * Init function
	 */
	init: function()
	{
		Debug.write("Initializing acp.packagemanager.js");
		
		document.observe("dom:loaded", function(){
			ipb.delegate.register('.npm_select', acp.packagemanager.selectGroup);
			ipb.delegate.register('.npmp_select', acp.packagemanager.selectPackage);
			ipb.delegate.register('.parent', acp.packagemanager.groupToggle);

			acp.packagemanager._doToggle( root );
			
			// Set up live filter
			acp.packagemanager.filter.init();
			Debug.write("Here 1");
			
			// Toggle selected
			if ( isSelector )
			{
				for ( i in defaultSelected )
				{
					if ( isSelector == 1 )
					{
						acp.packagemanager._selectPackage( i );
					}
					else
					{
						acp.packagemanager._selectGroup( i );
					}
				}
			}
		});
	},
	
	filter: {
		
		defaultText: '',
		lastText: '',
		timer: null,
		cache: [],
		pageSubtitle: '',
		
		init: function()
		{
			acp.packagemanager.filter.defaultText = $F('package_search_box');
			acp.packagemanager.filter.pageSubtitle = $('package_subtitle').innerHTML;
			
			$( 'package_search_box' ).writeAttribute('autocomplete', 'off');
			
			// Set up events for live filter
			$('package_search_box').observe('focus', acp.packagemanager.filter.timerEventFocus );
			$('package_search_box').observe('blur', acp.packagemanager.filter.timerEventBlur );
			$('cancel_filter').observe('click', acp.packagemanager.filter.endFiltering );
			
			Debug.write("Here");
		},
		
		timerEventFocus: function( e )
		{
			acp.packagemanager.filter.timer = acp.packagemanager.filter.eventFocus.delay( 0.5, e );
			
			$('package_search').addClassName('active');
			$('package_search_box').value = '';
		},
		
		eventFocus: function( e )
		{	
			// Keep the loop going
			acp.packagemanager.filter.timer = acp.packagemanager.filter.eventFocus.delay( 0.5, e );
			
			var text = acp.packagemanager.filter.getCurrentText();
			if( text == acp.packagemanager.filter.lastText ){ return; }
			if( text == '' ){ acp.packagemanager.filter.cancel(); return; }
			
			acp.packagemanager.filter.lastText = text;
			
			json = acp.packagemanager.filter.cacheRead( text );
			
			if( !json )
			{
				// Get it with ajax
				var url = ipb.vars['base_url'].replace( /&amp;/g, '&' ) + "app=nexus&module=ajax&section=packages&do=search";
				
				new Ajax.Request(	url,
									{
										method: 'post',
										parameters: {
											secure_key: ipb.vars['md5_hash'],
											'name': text
										},
										evalJSON: 'force',
										onSuccess: function(t)
										{
											Debug.write( t.responseJSON );
											
											if( Object.isUndefined( t.responseJSON ) )
											{
												alert( ipb.lang['js__nosearchperm'] );
												return;
											}
											
											if( t.responseJSON['error'] )
											{
												Debug.write( t.responseJSON['error'] );
												return;
											}
											
											// Seems to be OK!
											acp.packagemanager.filter.cacheWrite( text, t.responseJSON );
											acp.packagemanager.filter.updateAndShow( t.responseJSON );
										
										}
									});
			}
			else
			{
				acp.packagemanager.filter.updateAndShow( json );
			}
		},
		
		updateAndShow: function( json )
		{
			var output = '';
			
			if( !json['packages'].length )
			{
				//output = ipb.templates['ccs_filter_none'].evaluate();
				output = "none";
			}
			else
			{
				/*for( i=0;i<json.length;i++ )
				{
					if( json[i]['type'] == 'folder' ){ continue; }
					output += ipb.templates['ccs_filter_row'].evaluate( { id: json[i]['page_id'], type: ( json[i]['page_content_type'] != '' ) ? json[i]['page_content_type'] : 'file', name: json[i]['page_seo_name_hl'], path: json[i]['page_folder'] + '/' } );					
				}*/
				output = json['packages'];
			}
			
			//Debug.write( output );
			
			if( !acp.packagemanager.filter.filtering )
			{
				$('sort_groups').hide();
				$('filter_results').show().update( output );
				$('package_subtitle').update("Filter Matches");
				
				acp.packagemanager.filter.filtering = true;
			}
			else
			{
				$('filter_results').show().update( output );
			}
			
			acp.packagemanager._fixMenus();
		},
		
		cancel: function()
		{
			$('filter_results').hide();
			$('sort_groups').show();
			
			acp.packagemanager.filter.filtering = false;			
		},
		
		endFiltering: function( e )
		{
			acp.packagemanager.filter.cancel();
			$('package_search').removeClassName('active');
			$('package_search_box').value = acp.packagemanager.filter.defaultText;
			$('filter_results').update();
			$('page_subtitle').update( acp.packagemanager.filter.pageSubtitle );
		},
			
		cacheWrite: function( text, json )
		{
			acp.packagemanager.filter.cache[ text ] = json;
		},
		
		cacheRead: function( text )
		{
			if( !Object.isUndefined( acp.packagemanager.filter.cache[ text ] ) ){
				Debug.write("Results from cache");
				return acp.packagemanager.filter.cache[ text ];
			}
			
			return false;
		},
		
		timerEventBlur: function( e )
		{
			if( $F('package_search_box').strip() == '' )
			{
				acp.packagemanager.filter.cancel();
				$('package_search').removeClassName('active');
				$('package_search_box').value = acp.packagemanager.filter.defaultText;
				//$('package_subtitle').update( acp.packagemanager.filter.pageSubtitle );
			}
			
			clearTimeout( acp.packagemanager.filter.timer );
		},
		
		getCurrentText: function()
		{
			return $F('package_search_box').strip();
		}	
	},
	
	selectGroup: function( e, elem )
	{
		var groupID = $( elem ).id.replace('toggle_group_', '');
		acp.packagemanager._selectGroup( groupID );
	},
	
	_selectGroup: function( groupID )
	{	
		if( $('group_' + groupID).hasClassName('selected') )
		{
			$('group_' + groupID).removeClassName('selected');
			acp.packagemanager.selected[ groupID ] = false;
		}
		else
		{
			$('group_' + groupID).addClassName('selected');
			acp.packagemanager.selected[ groupID ] = true;
		}
	},
	
	selectPackage: function( e, elem )
	{
		var packageID = $( elem ).id.replace('toggle_package_', '');
		acp.packagemanager._selectPackage( packageID );
	},
	
	_selectPackage: function( packageID )
	{	
		if( $('package_' + packageID).hasClassName('selected') )
		{
			$('package_' + packageID).removeClassName('selected');
			acp.packagemanager.selected[ packageID ] = false;
		}
		else
		{
			$('package_' + packageID).addClassName('selected');
			acp.packagemanager.selected[ packageID ] = true;
		}
	},
	
	groupToggle: function( e, elem )
	{
		Debug.write( e.element() );
		
		if( !e.element().hasClassName('parent') ){ return; }
		
		var groupID = $( elem ).id.replace('group_', '');
		
		acp.packagemanager._doToggle( groupID );
		
	},
	
	_doToggle: function( groupID )
	{
		Debug.write( "ID is " + groupID );
		
		// Does this group exist?
		if( $('g_wrap_' + groupID) && $('g_wrap_' + groupID).readAttribute('needsUpdate') != 'yes' )
		{
			Effect.toggle( $('g_wrap_' + groupID), 'slide', { duration: 0.3 } );
			if ( !$('group_' + groupID).hasClassName('nochildren') )
			{
				if( $('group_' + groupID).hasClassName('open') ){
					$('group_' + groupID).removeClassName('open');
				} else {
					$('group_' + groupID).addClassName('open');
				}
			}
		}
		else if ( groupID != 0 )
		{
			// Get it with ajax
			var url = ipb.vars['base_url'].replace( /&amp;/g, '&' ) + "app=nexus&module=ajax&section=packages&do=children&id=" + groupID;

			new Ajax.Request(	url,
								{
									method: 'post',
									parameters: {
										secure_key: ipb.vars['md5_hash'],
										isSelector: isSelector
									},
									//evalJS: 'force',
									evalJSON: 'force',
									onSuccess: function(t)
									{
										if ( t.responseText.match( "__session__expired__log__out__" ) )
										{
											alert( ipb.lang['session_timed_out'] );
											
											return false;
										}
										
										if( Object.isUndefined( t.responseJSON ) )
										{
											alert( 'error' );
											return false;
										}
										else
										{
											if( !$('g_wrap_' + groupID) ){
												acp.packagemanager._buildGroupWrap( groupID, t.responseJSON['groups'], t.responseJSON['packages'] );
											} else {
												$('g_wrap_' + groupID).update( t.responseJSON['groups'] + t.responseJSON['packages'] );
											}
											
											$('g_wrap_' + groupID).writeAttribute('needsUpdate', 'no');
										}
									}
								}
							);
		}
	},
		
	_buildGroupWrap: function( groupID, groups, packages )
	{
		var html = ipb.templates['nexus_group_wrap'].evaluate( { id: groupID, groups: groups, packages: packages } );
		
		// Insert AFTER the folder row
		if( !$('group_' + groupID) )
		{
			return;
		}

		$('group_' + groupID).insert( { after: html } );
		
		acp.packagemanager._fixMenus( groupID );
		Effect.toggle( $('g_wrap_' + groupID), 'slide', { duration: 0.3 } );
		acp.packagemanager._buildSortable( groupID );
		
		if ( !$('group_' + groupID).hasClassName('nochildren') )
		{
			$('group_' + groupID).addClassName('open');
		}
	},
	
	_buildSortable: function( groupID )
	{
		acp.packagemanager['sortables_packages'][ groupID ] = function( draggableObject, mouseObject )
		{
			var options = {
							method : 'post',
							parameters : Sortable.serialize( 'gp_' + groupID, { tag: 'li', name: 'packages' } )
						};

			new Ajax.Request( ipb.vars['base_url'].replace( /&amp;/g, '&' ) + "&app=nexus&module=stock&section=packages&do=reorder_packages&md5check=" + ipb.vars['md5_hash'].replace( /&amp;/g, '&' ), options );
			
			return false;
		};

		Sortable.create( 'gp_' + groupID, { only: 'isDraggable', revert: true, format: 'package_([0-9]+)', onUpdate: acp.packagemanager['sortables_packages'][ groupID ], handle: 'draghandle' } );
		
		acp.packagemanager['sortables_groups'][ groupID ] = function( draggableObject, mouseObject )
		{
			var options = {
							method : 'post',
							parameters : Sortable.serialize( 'gg_' + groupID, { tag: 'li', name: 'package_groups' } )
						};
			
			new Ajax.Request( ipb.vars['base_url'].replace( /&amp;/g, '&' ) + "&app=nexus&module=stock&section=packages&do=reorder_groups&md5check=" + ipb.vars['md5_hash'].replace( /&amp;/g, '&' ), options );
			
			return false;
		};

		Sortable.create( 'gg_' + groupID, { only: 'isDraggable', revert: true, format: 'group_([0-9]+)', onUpdate: acp.packagemanager['sortables_groups'][ groupID ], handle: 'draghandle' } );
	},
	
	_fixMenus: function( groupID )
	{
		// We need to build menus
		if( !Object.isUndefined( groupID ) ){
			var menus = $('g_wrap_' + groupID).select('.ipbmenu');
		} else {
			var menus = $('filter_results').select('.ipbmenu');
		}
		
		if( menus.length )
		{
			for( i=0;i<menus.length;i++ )
			{
				var menuid = $( menus[i] ).id;
				var menucontent = $( menuid + '_menucontent' );
				
				if( $( menucontent ) ){
					new ipb.Menu( $(menuid), $(menucontent) );
				}
			}
		}
	}
};

acp.packagemanager.init();