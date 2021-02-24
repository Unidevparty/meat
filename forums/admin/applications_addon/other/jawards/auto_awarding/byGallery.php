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
class auto_award_byGallery
{
	function __construct( $reg )
	{
		$this->registry   =  $reg;
		$this->settings   =& $this->registry->fetchSettings();
		$this->DB         =  $this->registry->DB();
		if( ! $this->registry->getClass( 'jawards_core' ) )
		{
			require_once( IPSLib::getAppDir('jawards') ."/app_class_jawards.php" );
			new app_class_jawards( ipsRegistry::instance() );
		}
	}

	public function config()
	{
		$cfg['name_human']	= "Gallery Image";			// Display name for this plugin
		$cfg['name_cpu']	= substr( __CLASS__, 11 );
		$cfg['author']		= "InvisionHQ";			// Your name
		$cfg['web']			= "http://bbcode.it/";	// Your Site
		$cfg['hook_key']	= 'jaabg';					// Hook Key
		$cfg['sequence']	= 'key';					// How the awards are sequenced, options;
														//		o key	-- Use the key variable defined below
														//		o drag	-- Allow use of drag'n'drop

		$cfg['hook'] = $this->registry->getClass('jawards_core')->HookInfo( $cfg['hook_key'] );
		if( ! $cfg['hook']['settings']['enabled'] )
		{
			IPSDebug::fireBug( 'info', array( 'Auto-Award Disabled', "byGallery::config()" ) ) ;
			return( false );
		}
		if( ! $res = $this->DB->checkForTable( 'gallery_albums' ) )
		{
			IPSDebug::fireBug( 'error', array( 'table gallery_albums missing', "byGallery::config()" ) ) ;
			return( false );
		}
		$cfg['fields'][] = array(	'label'			=> 'Any Image',
									'description'	=> 'If on any image entry will trigger the award',
									'type'			=> 'yesno',
									'name'			=> 'all_images',
									'required'		=> 0,
									'options'		=> '',
									'key'			=> 0,
								);

		$this->DB->build( array( 'select' => 'album_id, album_name',
							 	 'from'   => 'gallery_albums',
							));

		$res = $this->DB->execute();
		$subs	= array();
		$list	= array();
		while( $f = $this->DB->fetch( $res ) )
		{
			$list[]		= array( $f['album_id'], $f['album_name'] );
		}
		if( count( $list ) )
		{
			$cfg['fields'][] = array(	'label'			=> 'Gallery',
										'description'	=> 'Select albums that will trigger the award',
										'type'			=> 'multi',
										'name'			=> 'albums',
										'required'		=> 0,
										'options'		=> $list,
										'key'			=> 0,
									);
		}

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
			$aaid = array();
			$list = array();
			foreach( $awarded AS $a )
			{
				$aaid[] = $a['auto_award_id'];
				$list[] = $a['row_id'];
			}
			IPSDebug::fireBug( 'info', array( $aaid, "AutoAward ID Array" ) ) ;
			IPSDebug::fireBug( 'info', array( $list, "Awarded Row ID Array" ) ) ;
			foreach( $process AS $i )
			{
				if( in_array( $i['auto_award_id'], $aaid ) )
				{
					continue; // Only one per customer please
				}
				if( $i['data']['all_images'] )
				{
					// Add Award for any Subscription payment
					$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
				}
				else
				{
					IPSDebug::fireBug( 'info', array( $i, "Processing" ) ) ;
					foreach( $i['data']['albums'] AS $cat )
					{
						if( $cat == $data['img_album_id'] )
						{
							$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
						}
					}
				}
			}
		}
	}
}
