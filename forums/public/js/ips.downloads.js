/************************************************/
/* IPB3 Javascript								*/
/* -------------------------------------------- */
/* ips.board.js - Board index code				*/
/* (c) IPS, Inc 2008							*/
/* -------------------------------------------- */
/* Author: Rikki Tissier						*/
/************************************************/

var _idx = window.IPBoard;

_idx.prototype.downloads = {
	totalChecked:		0,
	lastFileId:			0,
	lastScreenshotId:	0,
	downloaderPopups:	$H(),
	popup: 				{},
	editClicked: 		false,
	waitPeriod:			0,
	currentWait:		0,
	currentElem:		null,
	
	/*------------------------------*/
	/* Constructor 					*/
	init: function()
	{
		Debug.write("Initializing ips.downloads.js");
		
		document.observe("dom:loaded", function(){
			ipb.downloads.setUpToggle();
			ipb.downloads.initEvents();
			
			ipb.delegate.register("[data-switch]", function(e, elem){
				ipb.downloads.switchUploader( elem.readAttribute('data-switch') );
			});
		});
	},
	
	/* Switch the uploader type */
	switchUploader: function( mode )
	{
		var url  = ipb.vars['base_url'] + "app=core&module=ajax&section=attach&do=setPref&pref=" + mode + '&secure_key=' + ipb.vars['secure_hash'];
		var wLoc = window.location.toString();

		new Ajax.Request( url,
						{
							method: 'post',
							evalJSON: 'force',
							hideLoader: true,
							onSuccess: function(t)
							{
								if( Object.isUndefined( t.responseJSON ) ){ alert( ipb.lang['action_failed'] ); return; }
								
								if ( t.responseJSON['status'] == 'ok' )
								{
									$('postingform').action += '&preview=1&uploadPref=' + mode;
									$('postingform').submit();
									//window.location.reload();
								}
								else
								{
									alert( ipb.lang['action_failed'] );
									return;
								}
							}
						});
	},
	
	/**
	 * Edit a file - used to show a loading icon
	 */
 	editLink: function( target )
 	{
		// Since this can sometimes take a while, show a laoding graphic and stop people clicking the link more than once
		if ( ipb.downloads.editClicked == false )
		{
			ipb.downloads.editClicked = true;
			$('ipboard_body').insert( ipb.templates['ajax_loading'] );
			window.location = target;
			
			return false;
		}
 	},
	
	/**
	 * Init events for cat listing
	 */
	initEvents: function()
	{
		if( $('show_filters') )
		{
			$('show_filters').observe('click', ipb.downloads.toggleFilters );
			$('filter_form').hide();
		}
		
		/* Set up mod checkboxes for cats */
		if( $('files_all') )
		{
			ipb.downloads.preCheckFiles();
			$('files_all').observe( 'click', ipb.downloads.checkAllFiles );
		}
		
		ipb.delegate.register(".topic_mod", ipb.downloads.checkFile );
		ipb.delegate.register(".check_all", ipb.downloads.checkAllInForm );
		ipb.delegate.register(".delete_link", ipb.downloads.checkConfirm );
		ipb.delegate.register(".topic_moderation", ipb.downloads.checkModfile );

		if( $('change_author') )
		{
			$('change_author').observe( 'click', ipb.downloads.changeAuthorForm );
		}
		
		ipb.delegate.register("#full-changelog", ipb.downloads.showFullChangeLog );
		ipb.delegate.register('.view-downloads' , ipb.downloads.showDownloaderPopup );
		
		if( $('random-files') )
		{
			Event.observe( window, 'resize', ipb.downloads.resizeRandomFiles );
		}
	},
	
	/**
	 * Resize random files if window is resized
	 */
	resizeRandomFiles: function( e )
	{
		var w = $('random-files_wrap').getWidth();

		if( w < 690 )
		{
			$('random-files_wrap').removeClassName('three_column').removeClassName('four_column').addClassName('two_column');
		}
		else if( w < 1024 )
		{
			$('random-files_wrap').removeClassName('four_column').removeClassName('two_column').addClassName('three_column');
		}
		else
		{
			$('random-files_wrap').addClassName('four_column').removeClassName('three_column').removeClassName('two_column');
		}
		
		$('random-files').setStyle('width: ' + w + 'px');
	},
	
	/**
	 * Show full changelog in modal box
	 */
	showFullChangeLog: function( e, elem )
	{
		Event.stop(e);
		
		//var elem	= Event.findElement(e, 'a');
		var id		= $(elem).href.replace( /.+?file=(\d+)/, '$1' );
		
		if( ipb.downloads.popup['changelog'] )
		{
			ipb.downloads.popup['changelog'].show();
		}
		else
		{
			ipb.downloads.popup['changelog'] = new ipb.Popup( 'view_changelog', {
												type: 'balloon',
												ajaxURL: ipb.vars['base_url'] + '&app=downloads&module=ajax&secure_key=' + ipb.vars['secure_hash'] + '&section=changelog&file=' + id,
												stem: true,
												hideAtStart: false,
												attach: { target: elem, position: 'auto' },
												w: '500px'
										});
		}
		
		return false;
	},
	
	/**
	 * Show form to change author
	 */
	changeAuthorForm: function(e)
	{
		Event.stop(e);
		
		$('submitter_info').hide();
		$('change_author_box').show();
		var nameLookup = new ipb.Autocomplete( $('change_author_input'), { multibox: false, url: ipb.vars['base_url'] + 'app=core&module=ajax&section=findnames&do=get-member-names&secure_key=' + ipb.vars['secure_hash'] + '&name=', templates: { wrap: ipb.templates['autocomplete_wrap'], item: ipb.templates['autocomplete_item'] } } );
		$('change_author_input').focus();
		
		return false;
	},

	/**
	 * Initialize submit form.  Handles the upload/link/path stuff.
	 */
	initSubmitForm: function()
	{
		ipb.delegate.register(".remove_external", ipb.downloads.removeExternal);
		ipb.delegate.register(".screenshot_row", ipb.downloads.setPrimarySS);
		ipb.delegate.register(".primary_radio", ipb.downloads.setPrimarySS);
		ipb.delegate.register("#file_allowed_types", ipb.downloads.showFileTypePopup);
		ipb.delegate.register("#ss_allowed_types", ipb.downloads.showSSTypePopup);
	
		if( $('add_file_link') ){
			$('add_file_link').observe('click', ipb.downloads.addFileLink);
		}

		if( $('add_file_path') ){
			$('add_file_path').observe('click', ipb.downloads.addFilePath);
		}
		
		if( $('add_ss_link') ){
			$('add_ss_link').observe('click', ipb.downloads.addScreenshotLink);
		}
		
		if( $('add_ss_path') ){
			$('add_ss_path').observe('click', ipb.downloads.addScreenshotPath);
		}
		
		$('postingform').observe( 'submit', function( e ) {
			if( ipb.uploader.getUploadedFilesCount('files') < 1 )
			{
				Event.stop(e);
				
				$('postingform').scrollTo();
				
				ipb.global.errorDialogue( ipb.lang['nofiles_uploaded'] );
			}
		});
	},
	
	showFileTypePopup: function(e)
	{
		Event.stop(e);
		var elem = Event.findElement(e, 'a');
		Debug.dir( ipb.downloads.popup );
		
		if( ipb.downloads.popup['ftypes'] )
		{
			ipb.downloads.popup['ftypes'].show();
		}
		else
		{
			ipb.downloads.popup['ftypes']	= new ipb.Popup( 'view_filetypes_ooo', {
													type: 'balloon',
													initial: $('view_filetypes').innerHTML,
													stem: true,
													hideClose: true,
													hideAtStart: false,
													attach: { target: elem, position: 'auto' },
													w: '500px'
											});
		}
													
		return false;
		
	},
	
	showSSTypePopup: function(e)
	{
		Event.stop(e);
		var elem = Event.findElement(e, 'a');
		if( ipb.downloads.popup['sstypes'] )
		{
			ipb.downloads.popup['sstypes'].show();
		}
		else
		{
			ipb.downloads.popup['sstypes']	= new ipb.Popup( 'view_sstypes', {
													type: 'balloon',
													initial: $('view_sstypes').innerHTML,
													stem: true,
													hideClose: true,
													hideAtStart: false,
													attach: { target: elem, position: 'auto' },
													w: '500px'
											});
		}
		
		return false;
		
	},
	
	setPrimarySS: function(e, elem)
	{
		$$('.primary_radio').each( function(el){
			$(el).checked = false;
			try {
				$(el).up('.screenshot_row').removeClassName('active');
			} catch(er){ }
		});
		
		if( $('ss_linked') && $( elem ).descendantOf('ss_linked') )
		{
			$( elem ).checked = true;
		}
		else
		{
			$( elem ).addClassName('active').down('.primary_radio').checked = true;
		}
	},
	
	addFileLink: function(e)
	{
		ipb.downloads.lastFileId	= ipb.downloads.lastFileId + 1;
		var html = ipb.templates['new_file_link'].evaluate( { id: ipb.downloads.lastFileId } );
		
		$( 'files_linked' ).insert( { bottom: html } );
		
		Event.stop(e);
		return false;
	},
	
	addFilePath: function(e)
	{
		if( $('file_path') )
		{
			Debug.write("Already showing the path option");
		}
		else
		{
			ipb.downloads.lastFileId	= ipb.downloads.lastFileId + 1;
			var html = ipb.templates['new_file_path'].evaluate( { id: ipb.downloads.lastFileId } );
		
			$( 'files_linked' ).insert( { bottom: html } );
		}
		
		$('add_file_path').hide();
		
		Event.stop(e);
		return false;
	},
	
	addScreenshotLink: function(e)
	{
		ipb.downloads.lastScreenshotId	= ipb.downloads.lastScreenshotId + 1;
		var html = ipb.templates['new_ss_link'].evaluate( { id: ipb.downloads.lastScreenshotId } );
		
		$( 'ss_linked' ).insert( { bottom: html } );
		
		Event.stop(e);
		return false;
	},
	
	addScreenshotPath: function(e)
	{
		if( $('ss_path') )
		{
			Debug.write("Already showing the ss path option");
		}
		else
		{
			ipb.downloads.lastScreenshotId	= ipb.downloads.lastScreenshotId + 1;
			var html = ipb.templates['new_ss_path'].evaluate( { id: ipb.downloads.lastScreenshotId } );
		
			$( 'ss_linked' ).insert( { bottom: html } );
		}
		
		$('add_ss_path').hide();
		
		Event.stop(e);
		return false;
	},
	
	removeExternal: function(e, elem)
	{
		Event.stop(e);
		var row = $(elem).up('li');
		
		if( $( row ).id == 'ss_path' )
		{
			$('add_ss_path').show();
		}
		
		if( $( row ).id == 'file_path' )
		{
			$('add_file_path').show();
		}
		
		new Effect.Fade( $( row ), { duration: 0.3, afterFinish: function(){
			$( row ).remove();
		}});
	},

	showDownloaderPopup: function(e)
	{
		Event.stop(e);
		
		var elem	= Event.findElement(e, 'a');
		var thisId = parseInt( $(elem).id.replace( /^view\-downloads\-/g, '' ) );
		
		if ( ipb.downloads.downloaderPopups.get(thisId) )
		{
			ipb.downloads.downloaderPopups.get(thisId).show();
		}
		else
		{
			ipb.downloads.downloaderPopups.set( thisId, new ipb.Popup( 'view_downloads_' + thisId, {
															type: 'balloon',
															ajaxURL: ipb.vars['base_url'] + '&app=downloads&module=ajax&secure_key=' + ipb.vars['secure_hash'] + '&section=downloaders&id=' + thisId,
															stem: true,
															hideAtStart: false,
															/*hideClose: true,*/
															attach: { target: elem, position: 'auto' },
															h: 200,
															w: '250px'
														})	);
		}
		
		return false;
	},

	/**
	 * Confirmation for all delete links
	 */
	checkConfirm: function(e)
	{
		if( !confirm( ipb.lang['delete_confirm'] ) )
		{
			Event.stop(e);
		}
	},
	
	/**
	 * Moderator submitting the mod form
	 */
	submitModForm: function(e)
	{
		var action = $( Event.findElement(e, 'form') ).down('select');
		
		// Check for delete action
		if( $F(action) == 'del' ){
			if( !confirm( ipb.lang['delete_confirm'] ) ){
				Event.stop(e);
			}
		}
	},
	
	/* ------------------------------ */
	/**
	 * Inits the forum tables ready for collapsing
	*/
	setUpToggle: function()
	{
		$$('.ipb_table').each( function(tab){
			$( tab ).wrap( 'div', { 'class': 'table_wrap' } );
		});
		
		$$('.category_block').each( function(cat){
			if( $(cat).select('.toggle')[0] )
			{
				$(cat).select('.toggle')[0].observe( 'click', ipb.downloads.toggleCat );
			}
		});
		
		var cookie = ipb.Cookie.get('toggleIdmCats');
		
		if( cookie )
		{
			var cookies = cookie.split( ',' );
			
			//-------------------------
			// Little fun for you...
			//-------------------------
			for( var abcdefg=0; abcdefg < cookies.length; abcdefg++ )
			{
				if( cookies[ abcdefg ] )
				{
					if( $( cookies[ abcdefg ] ) )
					{
						var wrapper	= $( cookies[ abcdefg ] ).up('.category_block').down('.table_wrap');
						
						if( wrapper )
						{
							wrapper.hide();
							$( cookies[ abcdefg ] ).addClassName('collapsed');
						}
					}
				}
			}
		}
	},
	
	/* ------------------------------ */
	/**
	 * Show/hide a category
	 * 
	 * @var		{event}		e	The event
	*/
	toggleCat: function(e)
	{
		var click = Event.element(e);
		var remove = $A();
		var wrapper = $( click ).up('.category_block').down('.table_wrap');
		catname = $( click ).up('h3');
		var catid = catname.id;
		
		// Get cookie
		cookie = ipb.Cookie.get('toggleIdmCats');
		if( cookie == null ){
			cookie = $A();
		} else {
			cookie = cookie.split(',');
		}
		
		Effect.toggle( wrapper, 'blind', {duration: 0.4} );
		
		if( catname.hasClassName('collapsed') )
		{
			catname.removeClassName('collapsed');
			remove.push( catid );
		}
		else
		{
			new Effect.Morph( $(catname), {style: 'collapsed', duration: 0.4, afterFinish: function(){
				$( catname ).addClassName('collapsed');
			} });
			cookie.push( catid );
		}
		
		cookie = "," + cookie.uniq().without( remove ).join(',') + ",";
		ipb.Cookie.set('toggleIdmCats', cookie, 1);
		
		Event.stop( e );
	},
	
	/**
	 * Toggling the filters
	 */
	toggleFilters: function(e)
	{
		if( $('filter_form') )
		{
			Effect.toggle( $('filter_form'), 'blind', {duration: 0.2} );
			Effect.toggle( $('show_filters'), 'blind', {duration: 0.2} );
		}
	},
	
	/**
	 * Check the files we've selected
	 */
	preCheckFiles: function()
	{
		topics = $F('selectedfileids').split(',');
		
		var checkboxesOnPage	= 0;
		var checkedOnPage		= 0;

		if( topics )
		{
			topics.each( function(check){
				if( check != '' )
				{
					if( $('file_' + check ) )
					{
						checkedOnPage++;
						$('file_' + check ).checked = true;
						
						if ( !$('category_mod').visible() )
						{
							$('moderator_toggle').up('div').hide();
							$('category_mod').show();
						}
					}
					
					ipb.downloads.totalChecked++;
				}
			});
		}

		$$('.topic_mod').each( function(check){
			checkboxesOnPage++;
		} );
		
		if( checkedOnPage == checkboxesOnPage && checkboxesOnPage > 0 )
		{
			$('files_all').checked = true;
		}
		
		ipb.downloads.updateFileModButton();
	},	
	
	/**
	 * Check all the files in this form
	 */			
	checkAllInForm: function(e, elem)
	{
		checked	= 0;
		check	= elem; /*Event.findElement(e, 'input');*/
		toCheck	= $F(check);
		form	= check.up('form');
		selectedTopics	= new Array;
		
		form.select('.selectedfileids').each( function(field){
			selectedTopics	= field.value.split(',').compact();
		});
		
		toRemove		= new Array();

		form.select('.topic_moderation').each( function(check){
			if( toCheck != null )
			{
				check.checked = true;
				selectedTopics.push( check.id.replace('file_', '') );
				checked++;
			}
			else
			{
				check.checked = false;
				toRemove.push( check.id.replace('file_', '') );
			}
		});
		
		selectedTopics = selectedTopics.uniq().without( toRemove ).join(',');

		form.select('.submit_button').each( function(button)
		{
			if( checked == 0 ){
				button.disabled = true;
			} else {
				button.disabled = false;
			}
		
			button.value = ipb.lang['with_selected'].replace('{num}', checked);
		});
		
		form.select('.selectedfileids').each( function(hidden)
		{
			hidden.value = selectedTopics;
		});
	},
	
	/**
	 * Check a file on the moderation form
	 */			
	checkModfile: function(e, elem)
	{
		check			= elem; /*Event.findElement(e, 'input');*/
		toCheck			= $(check);
		form			= check.up('form');
		selectedTopics	= new Array;
		
		var checkboxesOnPage	= 0;
		var checkedOnPage		= 0;
		
		form.select('.selectedfileids').each( function(field){
			selectedTopics	= field.value.split(',').compact();
		});
		remove			= new Array();

		form.select('.topic_moderation').each( function(check){
			checkboxesOnPage++;
			
			if( check.checked == true )
			{
				checkedOnPage++;
				selectedTopics.push( check.id.replace('file_', '') );
			}
			else
			{
				remove.push( check.id.replace('file_', '') );

				if( form.select('.check_all')[0] )
				{
					form.select('.check_all')[0].checked = false;
				}
			}
		} );
		
		if( checkedOnPage == checkboxesOnPage && form.select('.check_all')[0] )
		{
			form.select('.check_all')[0].checked = true;
		}

		selectedTopics = selectedTopics.uniq().without( remove ).join(',');

		form.select('.submit_button').each( function(button)
		{
			if( checkedOnPage == 0 ){
				button.disabled = true;
			} else {
				button.disabled = false;
			}
		
			button.value = ipb.lang['with_selected'].replace('{num}', checkedOnPage);
		});
		
		form.select('.selectedfileids').each( function(hidden)
		{
			hidden.value = selectedTopics;
		});
	},
	
	/**
	 * Check all the files
	 */			
	checkAllFiles: function(e)
	{
		check = Event.findElement(e, 'input');
		toCheck = $F(check);
		ipb.downloads.totalChecked = 0;
		toRemove = new Array();
		selectedTopics = $F('selectedfileids').split(',').compact();

		$$('.topic_mod').each( function(check){
			if( toCheck != null )
			{
				check.checked = true;
				selectedTopics.push( check.id.replace('file_', '') );
				ipb.downloads.totalChecked++;
			}
			else
			{
				toRemove.push( check.id.replace('file_', '') );
				check.checked = false;
			}
		});

		selectedTopics = selectedTopics.uniq().without( toRemove ).join(',');
		ipb.Cookie.set('modfileids', selectedTopics, 0);

		$('selectedfileids').value = selectedTopics;
		
		ipb.downloads.updateFileModButton();
	},
	
	/**
	 * Check a single file
	 */	
	checkFile: function(e, elem)
	{
		remove = new Array();
		check = elem; /*Event.findElement( e, 'input' );*/
		selectedTopics = $F('selectedfileids').split(',').compact();
		
		var checkboxesOnPage	= 0;
		var checkedOnPage		= 0;
		
		if( check.checked == true )
		{
			selectedTopics.push( check.id.replace('file_', '') );
			ipb.downloads.totalChecked++;
		}
		else
		{
			remove.push( check.id.replace('file_', '') );
			ipb.downloads.totalChecked--;
		}
		
		$$('.topic_mod').each( function(check){
			checkboxesOnPage++;
			
			if( $(check).checked == true )
			{
				checkedOnPage++;
			}
		} );
		
		if ( checkedOnPage && !$('category_mod').visible() )
		{
			$('moderator_toggle').up('div').hide();
			$('category_mod').show();
		}
		
		if( $('files_all') )
		{
			if( checkedOnPage == checkboxesOnPage )
			{
				$('files_all' ).checked = true;
			}
			else
			{
				$('files_all' ).checked = false;
			}
		}
		
		selectedTopics = selectedTopics.uniq().without( remove ).join(',');		
		ipb.Cookie.set('modfileids', selectedTopics, 0);
		
		$('selectedfileids').value = selectedTopics;

		ipb.downloads.updateFileModButton();		
	},
	
	/**
	 * Update the moderation button
	 */	
	updateFileModButton: function( )
	{
		if( $('mod_submit') )
		{
			if( ipb.downloads.totalChecked == 0 ){
				$('mod_submit').disabled = true;
			} else {
				$('mod_submit').disabled = false;
			}
		
			$('mod_submit').value = ipb.lang['with_selected'].replace('{num}', ipb.downloads.totalChecked);
		}
	}, 
	
	/**
	 * Initialize download page
	 */
	initDownloadPage: function()
	{
		if( $('disclaimer_wrap') )
		{
			$('agree_disclaimer').observe('click', function(e){
				Event.stop(e);
				$('disclaimer_wrap').hide();
				$('files_wrap').show();
			});
		}
		
		/* Wait period? */
		if( ipb.downloads.waitPeriod )
		{
			ipb.delegate.register('.download_button', ipb.downloads.startWaitPeriod );
		}		
	},

	/**
	 * Handle wait period
	 */
	startWaitPeriod: function(e, elem)
	{
		if( ipb.downloads.waitPeriod )
		{
			Event.stop(e);

			/* Get current timestamp */
			var timestamp	= Math.round( +new Date()/1000 );

			new Ajax.Request( ipb.vars['base_url'] + "app=downloads&module=ajax&section=timestamp&secure_key=" + ipb.vars['secure_hash'],
							{
								method: 'get',
								asynchronous: false,	// We don't want timer to start until this returns
								onSuccess: function(t)
								{
									timestamp	= t.responseText;
								}
							});

			/* Has wait period already started? */
			if( ipb.Cookie.get('idm_wait_period') && ipb.Cookie.get('idm_wait_period') > 0 && ( timestamp - ipb.Cookie.get('idm_wait_period') < ipb.downloads.waitPeriod ) )
			{
				return false;
			}
			else
			{
				/* Set the cookie */
				ipb.Cookie.set( 'idm_wait_period', timestamp, true );
				
				/* Start on-page counter */
				ipb.downloads.currentWait	= ipb.downloads.waitPeriod;
				ipb.downloads.currentElem	= elem;
				
				ipb.downloads.countdown();
			}
		}
	}, 
	
	/**
	 * Timer callback
	 */
	countdown: function()
	{
		if( ipb.downloads.currentWait > 0 )
		{
			$(ipb.downloads.currentElem).innerHTML = ipb.lang['idmjs_wait_str'].replace( '%s', ipb.downloads.currentWait );
			
			ipb.downloads.currentWait	= ipb.downloads.currentWait - 1;
			
			setTimeout( "ipb.downloads.countdown();", 1000 );
		}
		else
		{
			_elem	= ipb.downloads.currentElem;
			ipb.downloads.currentElem	= null;
			ipb.downloads.currentWait	= 0;

			$(_elem).innerHTML = ipb.lang['idmjs_nowait_str'];
			
			window.location	= $(_elem).href;
		}
	}
};

ipb.downloads.init();