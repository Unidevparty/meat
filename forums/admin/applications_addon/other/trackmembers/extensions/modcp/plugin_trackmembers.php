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
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

/**
 *
 * @class		plugin_members_notes
 * @brief		Moderator control panel plugin: show latest member notes
 * 
 */
class plugin_trackmembers_trackmembers
{
	/**
	 * Registry Object Shortcuts
	 *
	 * @var		$registry
	 * @var		$DB
	 * @var		$settings
	 * @var		$request
	 * @var		$lang
	 * @var		$member
	 * @var		$memberData
	 * @var		$cache
	 * @var		$caches
	 */
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;

	/**
	 * Main function executed automatically by the controller
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Make shortcuts
		//-----------------------------------------
		
		$this->registry		= $registry;
		$this->DB			= $this->registry->DB();
		$this->settings		=& $this->registry->fetchSettings();
		$this->request		=& $this->registry->fetchRequest();
		$this->member		= $this->registry->member();
		$this->memberData	=& $this->registry->member()->fetchMemberData();
		$this->cache		= $this->registry->cache();
		$this->caches		=& $this->registry->cache()->fetchCaches();
		$this->lang			= $this->registry->class_localization;
	}
	
	/**
	 * Returns the primary tab key for the navigation bar
	 * 
	 * @return	@e string
	 */
	public function getPrimaryTab()
	{
		return 'manage_members';
	}
	
	/**
	 * Returns the secondary tab key for the navigation bar
	 * 
	 * @return	@e string
	 */
	public function getSecondaryTab()
	{
		return 'trackmembers';
	}

	/**
	 * Determine if we can view tab
	 *
	 * @param	array 	$permissions	Moderator permissions
	 * @return	@e bool
	 */
	public function canView( $permissions )
	{
		if ( $this->settings['trackmembers_onoff'] AND IPSMember::isInGroup( $this->memberData, explode( ',', $this->settings['trackmembers_cantrackgroups'] ) ) )
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Execute plugin
	 *
	 * @param	array 	$permissions	Moderator permissions
	 * @return	@e string
	 */
	public function executePlugin( $permissions )
	{
		//-----------------------------------------
		// Check permissions
		//-----------------------------------------

		if( !$this->canView( $permissions ) )
		{
			return '';
		}

		//-----------------------------------------
		// Get last 10 notes
		//-----------------------------------------

		$st			= intval( $this->request['st'] );
		$perpage	= 10;
		$total		= $this->DB->buildAndFetch( array( 'select' => 'count(*) as total', 'from' => 'members', 'where' => 'member_tracked = 1' ) );
		$members	= array();

		$this->DB->build( array(
								'select'	=> 't.member_id, count(t.member_id) as cnt',
								'from'		=> array( 'members_tracker' => 't' ),
								'add_join'	=> array(
													array(
														'select'	=> 'm.*',
														'from'		=> array( 'members' => 'm' ),
														'where'		=> 'm.member_id=t.member_id',
														'type'		=> 'left',
														),
													array(
														'select'	=> 'pp.*',
														'from'		=> array( 'profile_portal' => 'pp' ),
														'where'		=> 'm.member_id=pp.pp_member_id',
														'type'		=> 'left',
														),
													),
								'where'		=> 'm.member_tracked = 1',
								'order'		=> 't.member_id',
								'group'		=> 't.member_id',
								'order'		=> 't.member_id',
								'limit'		=> array( $st, $perpage ),
		) );

		$outer	= $this->DB->execute();

		while( $r = $this->DB->fetch( $outer ) )
		{
			$members[] = IPSMember::buildDisplayData( $r, array( 'reputation' => 0, 'warn' => 0 ) );	
		}

		//-----------------------------------------
		// Page links
		//-----------------------------------------

		$pages	= $this->registry->output->generatePagination( array(	'totalItems'		=> $total['notes'],
																		'itemsPerPage'		=> $perpage,
																		'currentStartValue'	=> $st,
																		'baseUrl'			=> "app=core&amp;module=modcp&amp;fromapp=members&amp;tab=trackmembers",
															)		);
		
		return $this->registry->output->getTemplate( 'modcp' )->trackedMembers( $members, $pages );
	}
}