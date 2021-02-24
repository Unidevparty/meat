<?php
/*
 * Product Title:		Awards :: Subscription Manager Add-on
 * Product Version:		3.0.20 Build 368
 * Author:				InvisionHQ
 * Website:				BBcode.it
 * Website URL:			http://bbcode.it/
 * Email:				reficul@lamoneta.it
 * Copyright©:			InvisionHQ - All rights Reserved 2012-2013
 */
 class auto_award_NexusPayment
{
	function __construct( $reg )
	{
		$this->registry	=  $reg;
		$this->settings	=& $this->registry->fetchSettings();
		$this->DB		=  $this->registry->DB();
		if( ! $this->registry->getClass( 'jawards_core' ) )
		{
			require_once( IPSLib::getAppDir('jawards') ."/app_class_jawards.php" );
			new app_class_jawards( ipsRegistry::instance() );
		}
	}

	public function config()
	{
		$cfg['name_human']	= "Nexus Payments";
		$cfg['name_cpu']	= substr( __CLASS__, 11 );
		$cfg['author']		= "InvisionHQ";
		$cfg['web']			= "http://bbcode.it/";
		$cfg['hook_key']	= 'jaanp';
		$cfg['sequence']	= 'key';
		$cfg['fields'][] = array(	'label'			=> 'Any Purchase',
									'description'	=> 'If on any Nexus purchase will trigger the award',
									'type'			=> 'yesno',
									'name'			=> 'all_items',
									'required'		=> 0,
									'options'		=> '',
									'key'			=> 0,
								);
		$cfg['hook'] = $this->registry->getClass('jawards_core')->HookInfo( $cfg['hook_key'] );
		if( ! $cfg['hook']['settings']['enabled'] )
		{
			IPSDebug::fireBug( 'info', array( 'Auto-Award Disabled', "NexusPayment::config()" ) ) ;
			return( false );
		}
		if( ! $res = $this->DB->checkForTable( 'nexus_packages' ) )
		{
			IPSDebug::fireBug( 'error', array( 'table nexus_packages missing', "NexusPayment::config()" ) ) ;
			return( false );
		}
		$this->DB->build( array( 'select' => 'p_id, p_name',
							 	 'from'   => 'nexus_packages',
							));

		$res = $this->DB->execute();
		$subs	= array();
		$list	= array();
		while( $f = $this->DB->fetch( $res ) )
		{
			$list[]		= array( $f['p_id'], $f['p_name'] );
		}
		if( count( $list ) )
		{
			$cfg['fields'][] = array(	'label'			=> 'Purchase',
										'description'	=> 'Select items that will trigger the award',
										'type'			=> 'multi',
										'name'			=> 'items',
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
			if( $data['act'] != 'new' )
			{
				IPSDebug::fireBug( $data['act'], array( $cfg, "Not an new purchase, ignoring" ) ) ;
			}
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
					IPSDebug::fireBug( 'info', array( $i, "Duplicate Award" ) ) ;
					continue; // Only one per customer please
				}
				if( $i['data']['all_items'] )
				{
					// Add Award for any Subscription payment
					IPSDebug::fireBug( 'info', array( $i, "Any purchase Award" ) ) ;
					$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
				}
				else
				{
					IPSDebug::fireBug( 'info', array( $i, "Processing" ) ) ;
					foreach( $i['data']['items'] AS $sub )
					{
						if( $sub == $data['itemID'] )
						{
							IPSDebug::fireBug( 'info', array( $i, "Specific purchase Award" ) ) ;
							$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
						}
					}
				}
			}
		}
	}
}
