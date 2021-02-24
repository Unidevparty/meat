<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */
 
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class public_trackmembers_core_trackmembers extends ipsCommand
{
	public function doExecute( ipsRegistry $registry )
	{	
		$this->registry->class_localization->loadLanguageFile( array( 'public_trackmembers' ), 'trackmembers' );
		$this->registry->class_localization->loadLanguageFile( array( 'public_profile' ), 'members' );		
		
		if ( !IPSMember::isInGroup( $this->memberData, explode( ',', $this->settings['trackmembers_cantrackgroups'] ) ) )
		{
			$this->registry->output->showError( 'no_permission' );
		}
		
        switch( $this->request['do'] )
        {
			case 'track':
				$this->_trackMember( $mid );
			break;
			case 'untrack':
				$this->_unTrackMember( $mid );
			break;
			case 'export':
				$this->_exportToPDF( $mid );
			break;

        	default:
        		$this->_viewTrackLogs( $mid );
        	break;
        }

		$this->registry->output->addContent( $this->output );
		$this->registry->getClass('output')->sendOutput();
	}

    public function _trackMember( $mid )
    {
		$mid = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		$member = IPSMember::load( $mid, 'core' );

		if ( !IPSMember::isInGroup( $this->memberData, explode( ',', $this->settings['trackmembers_cantrackgroups'] ) ) OR !IPSMember::isInGroup( $member, explode( ',', $this->settings['trackmembers_groups'] ) ) )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		$referrer = my_getenv( 'HTTP_REFERER' );

		IPSMember::save( $mid, array( 'core' => array( 'member_tracked' => 1, 'member_tracked_deadline' => 0 ) ) );

		$this->registry->getClass('output')->redirectScreen( $this->lang->words['tracking_member'], $referrer );
	}

    public function _unTrackMember( $mid )
    {
		$mid = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		$member = IPSMember::load( $mid, 'core' );

		if ( !IPSMember::isInGroup( $this->memberData, explode( ',', $this->settings['trackmembers_cantrackgroups'] ) ) OR !IPSMember::isInGroup( $member, explode( ',', $this->settings['trackmembers_groups'] ) ) )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		$referrer = my_getenv( 'HTTP_REFERER' );

		IPSMember::save( $mid, array( 'core' => array( 'member_tracked' => 0, 'member_tracked_deadline' => 0 ) ) );

		$this->registry->getClass('output')->redirectScreen( $this->lang->words['untracked_member'], $referrer );
	}

    public function _viewTrackLogs( $mid )
    {
		$mid = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		//-----------------------------------------
		// Figure out sort order, day cut off, etc
		//-----------------------------------------

		$sort_keys		=  array( 'date'	=> 'sort_by_date',
							   	  'app'		=> 'sort_by_app',
		);
		
		$sort_by_keys = array( 'Z-A'  => 'descending_order',
						 	   'A-Z'  => 'ascending_order'
		);
		
		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'trackmembers' ) . '/extensions/coreExtensions.php', 'trackMemberMapping' );
    	$mapping = new $classToLoad;
						     
		$filter_keys  = $mapping->functionToLangStrings();
		$filter_keys['all'] = 'logfilter_all';
		
	    //-----------------------------------------
	    // Additional queries?
	    //-----------------------------------------
	    
	    $add_query_array = array();
	    $add_query       = "";

	    $sort_key		= ! empty( $this->request['sort_key'] ) ? $this->request['sort_key'] : 'date';

		$sort_by		= ! empty( $this->request['sort_by'] ) ? $this->request['sort_by'] : 'Z-A';
												
		$logfilter		= ! empty( $this->request['logfilter'] ) ? $this->request['logfilter'] : 'all';
		
		/* HACKER */
		if( ( ! isset( $filter_keys[ $logfilter ] ) ) OR ( ! isset( $sort_keys[ $sort_key ] ) ) OR ( ! isset( $sort_by_keys[ $sort_by ] ) ) )
		{
			$this->registry->output->showError( 'no_permission', 1 );
		}
		
		if ( $logfilter != 'all' )
		{
			$_SQL_EXTRA	= " AND function = '{$logfilter}'";
		}
		
		$r_sort_by = $sort_by == 'A-Z' ? 'ASC' : 'DESC';		

		/* pagination */
		$count	= $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as total', 'from' => 'members_tracker', 'where' => 'member_id = ' . $mid . $_SQL_EXTRA ) );
		$rows	= array();

		$each	= intval( $this->settings['trackmembers_nrpv'] ) ? $this->settings['trackmembers_nrpv'] : 10;

		$st		= intval($this->request['st']);
		$pages  = $this->registry->output->generatePagination( array( 
			'totalItems'		=> $count['total'],
			'itemsPerPage'		=> $each,
			'currentStartValue'	=> $st,
			'baseUrl'			=> "app=trackmembers&module=core&section=trackmembers&mid={$mid}&amp;sort_by={$sort_by}&amp;sort_key={$sort_key}&amp;logfilter={$logfilter}"	
		) );

		$member = IPSMember::load( $mid, 'all' );
		$member = IPSMember::buildDisplayData( $member );
		$logs = array();

		$this->DB->build( array( 
			'select' 	=> '*', 
			'from' 		=> 'members_tracker', 
			'where' 	=> 'member_id = ' . $mid . $_SQL_EXTRA, 
			'order' 	=> "{$sort_key} {$r_sort_by}", 
			'limit' 	=> array( $st, $each ) 
		) );

		$this->DB->execute();

		while( $r = $this->DB->fetch() )
		{
			$logs[] = $r;
		}

		$member['_last_active'] = $this->registry->getClass( 'class_localization')->getDate( $member['last_activity'], 'SHORT' );

		//-----------------------------------------
		// Online?
		//-----------------------------------------

		$time_limit			= time() - ( $this->settings['au_cutoff'] * 60 );
		$member['_online']	= 0;
		$bypass_anon		= $this->memberData['g_access_cp'] ? 1 : 0;

		list( $be_anon, $loggedin )	= explode( '&', empty($member['login_anonymous']) ? '0&0' : $member['login_anonymous'] );

		/* Is not anon but the group might be forced to? */
		if ( empty( $be_anon ) && IPSMember::isLoggedInAnon( $member ) )
		{
			$be_anon = 1;
		}

		/* Finally set the online flag */
		if ( ( ( $member['last_visit'] > $time_limit OR $member['last_activity'] > $time_limit ) AND ( $be_anon != 1 OR $bypass_anon == 1 ) ) AND $loggedin == 1 )
		{
			$member['_online'] = 1;
		}

		/* Load status class */
		if ( ! $this->registry->isClassLoaded( 'memberStatus' ) )
		{
			$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/member/status.php', 'memberStatus' );
			$this->registry->setClass( 'memberStatus', new $classToLoad( ipsRegistry::instance() ) );
		}
			
		/* Fetch */
		$status = $this->registry->getClass('memberStatus')->fetchMemberLatest( $member['member_id'] );

		//-----------------------------------------
		// Warnings?
		//-----------------------------------------
		
		$warns = array();
		if ( $member['show_warn'] )
		{
			if ( $member['member_banned'] )
			{
				$warns['ban'] = 0;
				$_warn = ipsRegistry::DB()->buildAndFetch( array( 'select' => 'wl_id', 'from' => 'members_warn_logs', 'where' => "wl_member={$member['member_id']} AND wl_suspend<>0", 'order' => 'wl_date DESC', 'limit' => 1 ) );
				if ( $_warn['wl_id'] )
				{
					$warns['ban'] = $_warn['wl_id'];
				}
			}
			if ( $member['temp_ban'] )
			{
				$warns['suspend'] = 0;
				$_warn = ipsRegistry::DB()->buildAndFetch( array( 'select' => 'wl_id', 'from' => 'members_warn_logs', 'where' => "wl_member={$member['member_id']} AND wl_suspend<>0", 'order' => 'wl_date DESC', 'limit' => 1 ) );
				if ( $_warn['wl_id'] )
				{
					$warns['suspend'] = $_warn['wl_id'];
				}
			}
			if ( $member['restrict_post'] )
			{
				$warns['rpa'] = 0;
				$_warn = ipsRegistry::DB()->buildAndFetch( array( 'select' => 'wl_id', 'from' => 'members_warn_logs', 'where' => "wl_member={$member['member_id']} AND wl_rpa<>0", 'order' => 'wl_date DESC', 'limit' => 1 ) );
				if ( $_warn['wl_id'] )
				{
					$warns['rpa'] = $_warn['wl_id'];
				}
			}
			if ( $member['mod_posts'] )
			{
				$warns['mq'] = 0;
				$_warn = ipsRegistry::DB()->buildAndFetch( array( 'select' => 'wl_id', 'from' => 'members_warn_logs', 'where' => "wl_member={$member['member_id']} AND wl_mq<>0", 'order' => 'wl_date DESC', 'limit' => 1 ) );
				if ( $_warn['wl_id'] )
				{
					$warns['mq'] = $_warn['wl_id'];
				}
			}
		}

		//-----------------------------------------
		// Finish off the rest of the page  $filter_keys[$topicfilter]))
		//-----------------------------------------
		
		$sort_by_html	= "";
		$sort_key_html	= "";
		$filter_html	= "";
		
		foreach( $sort_by_keys as $k => $v )
		{
			$sort_by_html   .= $k == $sort_by      ? "<option value='$k' selected='selected'>{$this->lang->words[ $sort_by_keys[ $k ] ]}</option>\n"
											       : "<option value='$k'>{$this->lang->words[ $sort_by_keys[ $k ] ]}</option>\n";
		}
		
		foreach( $sort_keys as  $k => $v )
		{
			$sort_key_html  .= $k == $sort_key 	   ? "<option value='$k' selected='selected'>{$this->lang->words[ $sort_keys[ $k ] ]}</option>\n"
											       : "<option value='$k'>{$this->lang->words[ $sort_keys[ $k ] ]}</option>\n";
		}
		
		/*foreach( $filter_keys as  $k => $v )
		{
			$filter_html    .= $k == $logfilter    ? "<option value='$k' selected='selected'>{$this->lang->words[ $v ]}</option>\n"
												   : "<option value='$k'>{$this->lang->words[ $v ]}</option>\n";
		}*/

		$filter_html .= "<option value='all' selected='selected'>Show All</option>";

		foreach( $mapping->functionRemapToPrettyList() as $a => $b )
        {
        	$filter_html .= "<optgroup label='{$this->lang->words[ $a ]}'>";
        	
        	foreach( $b as $k => $v )
        	{
            	$filter_html    .= $k == $logfilter    ? "<option value='$k' selected='selected'>{$this->lang->words[ $v ]}</option>\n"
                	                                   : "<option value='$k'>{$this->lang->words[ $v ]}</option>\n";
        	}
        	$filter_html .= "</optgroup>";
        }

		$footer_filter['sort_by']		= $sort_key_html;
		$footer_filter['sort_order']	= $sort_by_html;
		$footer_filter['topic_filter']	= $filter_html;

		$template = $this->registry->output->getTemplate( 'trackmembers' )->trackMemberLogs( $logs, $member, $status, $pages, $footer_filter );

		$this->registry->output->setTitle( $this->settings['board_name'] );

		$this->registry->output->addContent( $template );

		$this->nav[] = array( $member['members_display_name'], "showuser={$member['member_id']}", $member['members_seo_name'], 'showuser' );
		$this->nav[] = array(  $this->lang->words['viewing_tracking_logs'] );

		foreach( $this->nav as $nav )
		{
			$this->registry->output->addNavigation( $nav[0], $nav[1] );	
		}

		$this->registry->getClass('output')->setTitle( $this->lang->words['tracking_logs_from'] . ' ' . $member['members_display_name'] . ' - ' . ipsRegistry::$settings['board_name'] );
	}

	/**
	 * Given an array of possible variables, the first one found is returned
	 *
	 * @param	array 	Mixed variables
	 * @return	mixed 	First variable from the array
	 * @since	2.0
	 */
    public static function selectVariable($array)
    {
    	if ( !is_array($array) ) return -1;

    	ksort($array);

    	$chosen = -1;

    	foreach ($array as $v)
    	{
    		if ( isset($v) )
    		{
    			$chosen = $v;
    			break;
    		}
    	}

    	return $chosen;
    }

	public function _exportToPDF( $mid )
	{
		$mid = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( 'no_permission' );
		}

		$member = IPSMember::load( $mid, 'core' );

		$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir('trackmembers') . '/sources/mpdf/mpdf.php', 'mPDF', 'trackmembers' );

		$mpdf=new mPDF('win-1252','A4','','',20,15,48,25,10,10);
		$mpdf->allow_charset_conversion = true;
		$mpdf->charset_in = $this->settings['gb_char_set'];
		$mpdf->useOnlyCoreFonts = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle( $this->lang->words['tracking_logs_report'] );
		$mpdf->SetAuthor( $this->settings['board_name'] );
		$mpdf->SetWatermarkText( $this->lang->words['tracking_logs'] );
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');

		$html = '
		<html>
		<head>
		<style>
		body {font-family: arial;
		    font-size: 10pt;
		}
		p {    margin: 0pt;
		}
		td { vertical-align: top; }
		tr.cor1 {background-color:#EEEEEE;}
		tr.cor2 {background-color:#FFFFFF;}
		.items td {
		    border-left: 0.1mm solid #000000;
		    border-right: 0.1mm solid #000000;
		    border-bottom: 0.1mm solid #000000;
		}
		table thead td { background-color: #A8A8A8;
		    text-align: center;
		    font-weight: bold;
		    border: 0.1mm solid #000000;
		}
		</style>
		</head>
		<body>
		<!--mpdf
		<htmlpageheader name="myheader">
		<table width="100%">
			<tr>
				<td width="100%" align="center">
					<span style="font-weight: bold; font-size: 18pt;">'.$this->lang->words['tracking_logs_report'].'</span>
					<br />
					<span style="font-weight: bold; font-size: 14pt;">'.$this->settings['board_name'].'</span>
				</td>
			</tr>
		</table>
		<br /><br />
		<table width="100%">
			<tr>
				<td width="17%">
					<span style="font-weight: bold; font-size: 10pt;">'.$this->lang->words['tracking_logs_report_member_name'].':</span>
				</td>
				<td width="83%">
					<span style="font-size: 10pt;">'.$member['members_display_name'].' ('.$this->lang->words['tracking_logs_report_member_idid'].': '.$member['member_id'].')</span>
				</td>
			</tr>
			<tr>
				<td width="17%">
					<span style="font-weight: bold; font-size: 10pt;">'.$this->lang->words['tracking_logs_report_member_group'].':</span>
				</td>
				<td width="83%">
					<span style="font-size: 10pt;">'.$this->caches['group_cache'][$member['member_group_id']]['g_title'].'</span>
				</td>
			</tr>
			<tr>
				<td width="17%">
					<span style="font-weight: bold; font-size: 10pt;">'.$this->lang->words['tracking_logs_report_member_joined'].':</span>
				</td>
				<td width="83%">
					<span style="font-size: 10pt;">'.$this->registry->getClass( 'class_localization')->getDate( $member['joined'], 'JOINED' ).'</span>
				</td>
			</tr>
		</table>
		</htmlpageheader>
		
		<htmlpagefooter name="myfooter">
		<table width="100%" style="border-top: 1px solid #000000; font-size: 9pt; padding-top: 3mm; ">
			<tr>
				<td width="50%" align="left">'.$this->lang->words['tracking_logs_report_page'].' {PAGENO} '.$this->lang->words['tracking_logs_report_pageof'].' {nb}</td>
				<td width="50%" align="right">'.date('jS F Y').'</td>
			</tr>
		</table>
		</htmlpagefooter>
		
		<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
		<sethtmlpagefooter name="myfooter" value="on" />
		mpdf-->
		
		<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="8">
		<thead>
		<tr>
		<td width="20%">'.$this->lang->words['filter_date'].'</td>
		<td width="40%">'.$this->lang->words['filter_description'].'</td>
		<td width="25%">'.$this->lang->words['filter_action'].'</td>
		<td width="15%">'.$this->lang->words['ip_address'].'</td>
		</tr>
		</thead>
		<tbody>
		';

		/* Logs */
		$this->DB->build( array( 'select' => '*', 'from' => 'members_tracker', 'where' => 'member_id = ' . $mid, 'order' => "date DESC"	) );
	
		$this->DB->execute();
	
		while( $r = $this->DB->fetch() )
		{
			$logs[] = $r;
		}
	
	    foreach( $logs as $row )
	    {
			if ( $row['id'] %2 == 0 )
			{
				$class = 'cor2';
			}
			else
			{
				$class = 'cor1';
			}

		$html .= '
			<tr class="'.$class.'">
				<td align="center">'.$this->registry->getClass( 'class_localization')->getDate($row['date'], 'long', 1).'</td>
				<td>'.IPSText::stripTags( $row['description'] ).'</td>
				<td align="center">'.$row['app'].'</td>
				<td align="center">'.$row['ip_address'].'</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		</body>
		</html>
		';

		$mpdf->WriteHTML($html);

		$file_name = 'Tracking Logs ' . $member['members_display_name'] . '.pdf';
		
		$mpdf->Output( $file_name,'D' ); exit;
		
		exit;
	}
}