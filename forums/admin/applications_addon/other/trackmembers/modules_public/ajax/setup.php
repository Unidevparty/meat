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

class public_trackmembers_ajax_setup extends ipsAjaxCommand
{
	
	/**
	 * Main class entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void
	 */	
	public function doExecute( ipsRegistry $registry )
	{		
		$this->registry->class_localization->loadLanguageFile( array( 'public_trackmembers' ) );
		//-----------------------------------------
		// What now?
		//-----------------------------------------

		switch( $this->request['do'] )
		{
			case 'save':
				$this->_saveTrackingSettings();
			break;
			case 'logs':
				$this->_showLatestLogs();
			break;

			case 'showPopup';
			default:
				$this->_showSetupPopup();
			break;
		}
    }

	protected function _showLatestLogs()
	{
		/* Load it */
		$mid 	= intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->returnJsonError("NO_MEMBER_ID");
		}

		$this->DB->build( array( 
								'select' 	=> '*',
								'from' 		=> 'members_tracker',
								'where' 	=> "member_id = " . $mid,
								'order'		=> 'date desc',
								'limit'		=> array( 0, 10 )
		) );

		$outer	= $this->DB->execute();

		$this->lang->loadLanguageFile( array( 'public_modcp' ), 'core' );

		while( $r = $this->DB->fetch( $outer ) )
		{
			$logs[] = $r;
		}

		$member = IPSMember::load( $mid, 'core' );
						
		$this->returnHtml( $this->registry->output->getTemplate( 'modcp' )->trackedMembersLogs( $logs, $member['members_display_name'] ) );
	}

    protected function _showSetupPopup()
    {
    	$formData 	= $formElements = array();
    	$member_id 	= (int) $this->request['mid'];

    	$member = IPSMember::load( $member_id, 'core' );
    	
    	$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'trackmembers' ) . '/extensions/coreExtensions.php', 'trackMemberMapping' );
    	$mapping = new $classToLoad;
    	
    	$formElements 	= $mapping->functionRemapToPrettyList();
    	$formData		= $mapping->getDefaultSettings();
    	
    	$curSettings	= IPSMember::getFromMemberCache( $member, 'trackmembers' );

    	if ( is_array( $curSettings ) )
    	{
    		$formData = array_merge( $formData, $curSettings );
    	}
    	
    	if ( $member['member_tracked_deadline'] == 0 OR ! $member['member_tracked'] )
    	{
    		$trackForDays = '';
    	}
    	else if ( $member['member_tracked_deadline'] > time() )
    	{
    		$trackForDays = ceil( ( $member['member_tracked_deadline'] - time() ) / 86400 );
    	}
    	else
    	{
    		$trackForDays = 0;
    	}

    	$this->returnHtml( $this->registry->output->getTemplate( 'trackmembers' )->showSetupPopup( $member, $formElements, $formData, $trackForDays ) );
    }
    
    protected function _saveTrackingSettings()
    {
    	$member_id 	= (int) $this->request['mid'];
    	$member 	= IPSMember::load( $member_id, 'core' );
    	
    	$toSave		= $memberUpdate = array();
    	
    	$classToLoad = IPSLib::loadLibrary( IPSLib::getAppDir( 'trackmembers' ) . '/extensions/coreExtensions.php', 'trackMemberMapping' );
    	$mapping = new $classToLoad;
    	
    	$defaults = $mapping->getDefaultSettings();
    	
    	foreach( array_keys( $defaults ) as $key )
    	{
    		$toSave['trackmembers'][ $key ] = isset( $this->request[ $key ] ) ? $this->request[ $key ] : 0;
    	}
    	

    	IPSMember::setToMemberCache( $member, $toSave );
    	
    	$trackForDays =  intval( $this->request['trackForDays'] );
    	if ( $this->request['trackForDays'] != '' AND $trackForDays > -1 )
    	{
    		$memberUpdate['core']['member_tracked_deadline'] = strtotime( "+{$trackForDays} days" );
    	}
    	else
    	{
    		$memberUpdate['core']['member_tracked_deadline'] = 0;
    	}
    	
    	if ( ! $member['member_tracked'] )
    	{
    		$memberUpdate['core']['member_tracked'] = 1;
    		
    		/* Hacky way to show the inline message when member isn't tracked already */
    		$this->DB->insert( 'core_inline_messages', array( 'inline_msg_date'    => IPS_UNIX_TIME_NOW,
														 	 'inline_msg_content' => 'Tracking settings successfully saved...' ) );
		
			$inline_msg_id = $this->DB->getInsertId();
	
			$this->DB->update( 'sessions', array( 'session_msg_id' => $inline_msg_id ), "id='{$this->member->session_id}'" );
    	}
    	
    	if ( count( $memberUpdate ) )
    	{
    		IPSMember::save( $member_id, $memberUpdate );
    	}
    	
    	$this->returnString( 'OK' );
    }
}