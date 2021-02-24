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
 if(!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class class_jawards
{
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $member;

	public function __construct( ipsRegistry $registry )
	{
		$this->registry   = $registry;
		$this->DB         = $this->registry->DB();
		$this->settings   =& $this->registry->fetchSettings();
		$this->cache      =  $this->registry->cache();
		$this->caches     =& $this->registry->cache()->fetchCaches();
		$this->request    =& $this->registry->fetchRequest();
		$this->member     = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		IPSDebug::fireBug( 'info', array( $this->settings, "IP.Board Settings Cache" ) ) ;
	}

	public function canPublic_Award($memberData)
	{
		$currentMember = ipsRegistry::instance()->member()->fetchMemberData();

		if($this->settings['jawards_group_restricted'])
		{
			$mGroups     = $memberData['member_group_id'] . ",";
			$cMemberGrps = $currentMember['member_group_id'] . ",";

			if($this->settings['jawards_include_sub_groups'])
			{
				$mGroups     .=  $memberData['mgroup_others'];
				$cMemberGrps .= $currentMember['mgroup_others'];
			}

			$expldC   = explode(",", $cMemberGrps);
			$expldM   = explode(",", $mGroups);
			$continue = FALSE;

			foreach($expldC as $num => $id)
			{
				if(!$pos[$id] && $id)
				{
					$pos[$id] = 1;
				}
			}

			foreach($expldM as $num => $id)
			{
				if($pos[$id])
				{
					$continue = TRUE;
				}
			}
		}

		if($continue !== FALSE)
		{
			$continue = TRUE;

			if(!$this->settings['jawards_self_jawards_public'] && $currentMember['member_id'] == $memberData['member_id'])
			{
				$continue = FALSE;
			}

			if(!$currentMember['g_jlogica_awards_can_give'] || !$memberData['g_jlogica_awards_can_receive'])
			{
				$continue = FALSE;
			}

			if($this->memberData['g_access_cp'] &&!$continue)
			{
				$continue = TRUE;
			}

			return $continue;
		}
		return FALSE;
	}

	public function autoAward( $key, $member_id, &$data )
	{
		IPSDebug::fireBug( 'info', array( "Loaded", "class_jawards::autoAward()" ) ) ;
		IPSDebug::fireBug( 'info', array( $key, "Key" ) ) ;
		IPSDebug::fireBug( 'info', array( $member_id, "Member ID" ) ) ;
		IPSDebug::fireBug( 'info', array( $data, "Data" ) ) ;
		$hook = IPSLib::getAppDir('jawards') . '/auto_awarding/' . $key . ".php";
		if( file_exists( $hook ) )
		{
			require_once( $hook );
			$class  = "auto_award_" . $key;
			$call   = new $class( ipsRegistry::instance() );
			$this->DB->build( array( 'select' => '*',
								 	 'from'   => 'jlogica_awards_auto_awards',
									 'where'  => "type = '{$key}' AND enabled = 1",
									 'order'  => 'placement',
								));

			$res = $this->DB->execute();
			$process	= array();
			$awarded	= array();
			$list		= array();		// For second query
			while( $f = $this->DB->fetch( $res ) )
			{
				$f['data']	= json_decode( $f['data'], TRUE );
				$process[]	= $f;
				$list[]		= $f['inst_id'];
			}
			if( count( $list ) )
			{
				$list = implode( ',', $list );
				$this->DB->build( array( 'select' => '*',
									 	 'from'   => 'jlogica_awards_awarded',
										 'where'  => "user_id = {$member_id} AND auto_award_id IN ({$list})",
									));

				$res = $this->DB->execute();
				while( $f = $this->DB->fetch( $res ) )
				{
					$f['data']	= json_decode( $f['data'], TRUE );
					$awarded[]	= $f;
				}
			}
			IPSDebug::fireBug( 'info', array( "{$key}.php::run", "Loading" ) );
			IPSDebug::fireBug( 'info', array( $process, "Process Array" ) );
			IPSDebug::fireBug( 'info', array( $list, "List Array" ) );
			IPSDebug::fireBug( 'info', array( $awarded, "Awarded Array" ) );
			// $process is the list of auto-awards available for this type, eg byPost
			// $awarded are the auto-awards of this type that the user has received already
			$call->run( $member_id, $process, $awarded, $data );
		}
	}

	public function deleteAwardList( $list )
	{
		if( empty( $list ) )
		{
			return; // No global deletes huh!
		}
		if( is_array( $list ) )
		{
			$list = implode( ',', $list );
		}
		$this->DB->delete( 'jlogica_awards_awarded', "row_id IN({$list})" );
	}

	public function getAwardsList( $member_id, $list = '' )
	{
		if( ! empty( $list ) )
		{
			if( is_array( $list ) )
			{
				$list = implode( ',', $list );
			}
			$list = trim( $list );
			if( ! empty( $list ) )
			{
				$list = " and award_id IN ({$list})";
			}
		}
		else
		{
			$list = '';
		}
		$this->DB->build( array(	'select'	=> 'award_id, count(*) AS cnt',
									'from'		=> 'jlogica_awards_awarded',
									'where'		=> "user_id = '{$member_id}' and `is_active` = '1' and `approved` = '1' {$list}",
									'group'		=> 'award_id',
								));
		$res = $this->DB->execute();
		$awards = array();
		while( $a = $this->DB->fetch( $res ) )
		{
			$awards[$a['award_id']] = $a['cnt'];
		}
		return( $awards );
	}

	public function doAutoAward( $autoAward, $member_id, $hookSettings, $duplicate=0 )
	{
		IPSDebug::fireBug( 'info', array( "Loaded", "class_jawards::doAutoAward()" ) ) ;
		IPSDebug::fireBug( 'info', array( $autoAward, "AutoAward to Add" ) ) ;
		IPSDebug::fireBug( 'info', array( $member_id, "Member ID" ) ) ;
		IPSDebug::fireBug( 'info', array( $hookSettings, "Hook Settings" ) ) ;
		IPSDebug::fireBug( 'info', array( $duplicate, "Duplicates Allowed" ) ) ;
		if( ! $duplicate )
		{
			$res = $this->DB->buildAndFetch( array(	'select' => 'count(*) as cnt',
											   		'from'   => 'jlogica_awards_awarded',
											   		'where'  => "user_id = {$member_id} and `is_active` = 1 and `approved` = 1 AND auto_award_id = {$autoAward['inst_id']}",
											));
			if( $res['cnt'] )
			{
				IPSDebug::fireBug( 'info', array( $res, "Duplicates Not Allowed" ) ) ;
				return;
			}
		}
		$autouser = $this->settings['jacore_autouser'];
		if( isset( $hookSettings['autouser'] ) and ! empty( $hookSettings['autouser'] ) )
		{
			$autouser = $hookSettings['autouser'];
		}
		$autouser = IPSMember::load(  $autouser, 'none', 'username' );
		$auto_id = 1;
		if( isset( $autouser['member_id'] ) )
		{
			$auto_id = $autouser['member_id'];
		}
		$i = array(	'award_id'      => $autoAward['award_id'],
					'auto_award_id' => $autoAward['inst_id'],
					'user_id'       => $member_id,
					'awarded_by'    => $auto_id,
					'notes'         => $autoAward['notes'],
					'date'          => IPS_UNIX_TIME_NOW,
					'is_active'		=> 1,
					'approved'		=> 1,
					);
		$this->DB->insert('jlogica_awards_awarded', $i );
	}
}
