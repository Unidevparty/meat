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
 if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class customsubs
{

	function subs_paid( $sub_array, $member=array(), $trx_id="" )
	{
		$this->registry   =  ipsRegistry::instance();
		require_once( IPSLib::getAppDir('jawards') ."/app_class_jawards.php" );
		$this->awards = new app_class_jawards( ipsRegistry::instance() );

		IPSDebug::fireBug( 'info', array( "jaasp()", "Loaded Hook" ) );
		IPSDebug::fireBug( 'info', array( $sub_array, "Subscription Data" ) );
		IPSDebug::fireBug( 'info', array( $member, "Member Info" ) );
		IPSDebug::fireBug( 'info', array( $trx_id, "Transaction ID" ) );
		$this->registry->getClass('class_jawards')->autoAward( 'Subscriptions', $member['member_id'], $sub_array );
	}

	function subs_failed($sub_array, $member=array(), $trx_id="")
	{
	}
}
