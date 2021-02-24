<?php
/*
 * Product Title:		Awards :: Subscription Manager Add-on
 * Product Version:		3.0.21 Build 375
 * Author:				InvisionHQ
 * Website:				BBcode.it
 * Website URL:			http://bbcode.it/
 * Email:				reficul@lamoneta.it
 * Copyright©:			InvisionHQ - All rights Reserved 2012-2013
 */
 class auto_award_Subscriptions
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
		$cfg['name_human']	= "Subscriptions";
		$cfg['name_cpu']	= substr( __CLASS__, 11 );
		$cfg['author']		= "InvisionHQ";
		$cfg['web']			= "http://bbcode.it/";
		$cfg['hook_key']	= 'jaasp';
		$cfg['sequence']	= 'key';
		$cfg['fields'][] = array(	'label'			=> 'Any Subscription',
									'description'	=> 'If on any subscription will trigger the award',
									'type'			=> 'yesno',
									'name'			=> 'all_subs',
									'required'		=> 0,
									'options'		=> '',
									'key'			=> 0,
								);
		$cfg['hook'] = $this->registry->getClass('jawards_core')->HookInfo( $cfg['hook_key'] );
		if( ! $cfg['hook']['settings']['enabled'] )
		{
			IPSDebug::fireBug( 'info', array( 'Auto-Award Disabled', "Subscriptions::config()" ) ) ;
			return( false );
		}
		if( ! $res = $this->DB->checkForTable( 'subscriptions' ) )
		{
			IPSDebug::fireBug( 'error', array( 'table subscriptions missing', "Subscriptions::config()" ) ) ;
			return( false );
		}
		$this->DB->build( array( 'select' => 'sub_id, sub_title',
							 	 'from'   => 'subscriptions',
							));

		$res = $this->DB->execute();
		$subs	= array();
		$list	= array();
		while( $f = $this->DB->fetch( $res ) )
		{
			$list[]		= array( $f['sub_id'], $f['sub_title'] );
		}
		if( count( $list ) )
		{
			$cfg['fields'][] = array(	'label'			=> 'Subscription',
										'description'	=> 'Select subscriptions that will trigger the award',
										'type'			=> 'multi',
										'name'			=> 'subs',
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
				if( $i['data']['all_subs'] )
				{
					// Add Award for any Subscription payment
					$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
				}
				else
				{
					IPSDebug::fireBug( 'info', array( $i, "Processing" ) ) ;
					foreach( $i['data']['subs'] AS $sub )
					{
						if( $sub == $data['subtrans_sub_id'] )
						{
							$this->registry->getClass('class_jawards')->doAutoAward( $i, $member_id, $cfg['hook']['settings'], 0 );
						}
					}
				}
			}
		}
	}
}
