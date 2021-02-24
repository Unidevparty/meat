<?php
/*
 * Product Title:		Awards Management System
 * Product Version:		3.0.23
 * Author:				InvisionHQ
 * Website:				bbcode.it
 * Website URL:			http://bbcode.it/
 * Email:				reficul@lamoneta.it
 * Copyright©:			InvisionHQ - Gabriele Venturini - bbcode.it - 2012/2013
 */
class auto_award_byPosts
{
	function __construct( $reg )
	{
		$this->registry =  $reg;
		$this->settings =& $this->registry->fetchSettings();
		$this->DB       =  $this->registry->DB();
		if( ! $this->registry->getClass( 'jawards_core' ) )
		{
			require_once( IPSLib::getAppDir('jawards') ."/app_class_jawards.php" );
			new app_class_jawards( ipsRegistry::instance() );
		}
	}

	public function config()
	{
		$cfg['name_human']	= "Post Count";				// Display name for this plugin
		$cfg['name_cpu']	= substr( __CLASS__, 11 );
//		$cfg['name_cpu']	= "byPosts";				// The Class name, less the 'auto_award_' prefix
		$cfg['author']		= "InvisionHQ";			// Your name
		$cfg['web']			= "http://bbcode.it/";	// Your Site
		$cfg['hook_key']	= 'jaabp';					// Hook Key
		$cfg['sequence']	= 'key';					// How the awards are sequenced, options;
														//		o key	-- Use the key variable defined below
														//		o drag	-- Allow use of drag'n'drop
/*  ***********************************************************************************
	 Field Explanation

	 	label
	 	-------------------------------------------------------------------------------
	 	A helpful label (title) for the field, displayed to the user

	 	description
	 	-------------------------------------------------------------------------------
	 	User information displayed uner the 'label'

	 	type
	 	-------------------------------------------------------------------------------
	 	Field type, one of;
	 		text	-- Normal text input line

	 	name
	 	-------------------------------------------------------------------------------
	 	The form name to use for this value

	 	required
	 	-------------------------------------------------------------------------------
	 	1/0 whether the field must be input

	 	options
	 	-------------------------------------------------------------------------------
	 	Form fields options based on the 'type'
	 		dropdown 	-- the options allowed as;
	 						ccccc

	 	key
	 	-------------------------------------------------------------------------------
	 	1/0 Whether this fields is the key for the plugin, only one field can be a key.

	 	This is used to sort the auto-awards both for user display and internally
	***********************************************************************************
 */

		$cfg['fields'][] = array(	'label'			=> 'Number of Posts Required',								// Title of field
									'description'	=> 'How many posts must the user have to get this award?',	// Helpful information
									'type'			=> 'text',													// Form type
									'name'			=> 'num_posts',												// Form name of field
									'required'		=> 1,														// Must be imput 0/1
									'options'		=> '',														// If drop down
									'key'			=> 1,														// The main key for this plugin 0/1 only one field can be a key
								);
		$cfg['hook'] = $this->registry->getClass('jawards_core')->HookInfo( $cfg['hook_key'] );
		if( ! $cfg['hook']['settings']['enabled'] )
		{
			IPSDebug::fireBug( 'info', array( 'Auto-Award Disabled', "byPosts::config()" ) ) ;
			return( false );
		}
		IPSDebug::fireBug( 'info', array( $cfg, "byPosts::config()" ) ) ;
		return $cfg;

	}

	public function run( $member_id, &$process, &$awarded, &$data )
	{
		$cfg	= $this->config();
		if( $cfg === false )
		{
			return;
		}
		if( $cfg['hook']['settings']['enabled'] )
		{
			$posts  = $data['posts'] + 1;
			IPSDebug::fireBug( 'info', array( $posts, "Post Count" ) ) ;
			$aaid = array();
			$list = array();
			foreach( $awarded AS $a )
			{
				$aaid[] = $a['auto_award_id'];
				$list[] = $a['row_id'];
			}
			IPSDebug::fireBug( 'info', array( $aaid, "AutoAward ID Array" ) ) ;
			IPSDebug::fireBug( 'info', array( $list, "Awarded Row ID Array" ) ) ;
			if( $cfg['hook']['settings']['highonly'] )	// Only one By Post Award (ie the highest one)
			{
				// Find out which is the highest they are entitled to have, if they have it forget it
				// otherwise we delete all old ones and add the new one
				$max = array();
				foreach( $process AS $i )
				{
					if( $posts > $i['data']['num_posts']  )
					{
						if( empty( $max ) or $i['data']['num_posts'] > $max['data']['num_posts'])
						{
							$max = $i;
						}
					}
				}
				if( ! empty( $max ) and ! in_array( $max['auto_award_id'], $aaid ) )
				{
					$this->registry->getClass('class_jawards')->deleteAwardList( $list );
					$this->registry->getClass('class_jawards')->doAutoAward( $max, $member_id, $cfg['hook']['settings'], 0 );
				}
			}
			else
			{
				// They get them all up to what they have posted....
				foreach( $process AS $i )
				{
					IPSDebug::fireBug( 'info', array( $i, "Processing" ) ) ;
					if( $posts > $i['data']['num_posts'] and ( ! in_array( $i['auto_award_id'], $aaid ) ) )
					{
						$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
					}
				}
			}
		}
	}
}
