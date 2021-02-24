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

class app_class_jawards
{
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $member;
	protected $awardCache;
	protected $hookSettingsCache;

	public function __construct(ipsRegistry $registry)
	{
		$this->registry   = $registry;
		$this->DB         = $this->registry->DB();
		$this->settings   =& $this->registry->fetchSettings();
		$this->cache      =  $this->registry->cache();
		$this->caches     =& $this->registry->cache()->fetchCaches();
		$this->request    =& $this->registry->fetchRequest();
		$this->member     = $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->lang       =  $this->registry->class_localization;
		$this->lang->loadLanguageFile( array( 'public_awards' ), 'jawards' );
		if( ! $this->caches['jawards_cats'] )
		{
			$this->caches['jawards_cats'] = $this->cache->getCache('jawards_cats');
		}
		if( ! $this->caches['jawards_awards'] )
		{
			$this->caches['jawards_awards'] = $this->cache->getCache('jawards_awards');
		}
		if(IN_ACP)
		{
			require_once(IPSLib::getAppDir('jawards') . "/sources/classes/jawards/class_jawards.php");
			$classToLoad = IPSLib::loadLibrary(IPSLib::getAppDir('jawards') . "/sources/classes/jawards/admin_jawards_functions.php", "admin_jawards_functions", "jawards");
			$this->registry->setClass('class_jawards', new $classToLoad($registry));
		}
		else
		{
			$classToLoad = IPSLib::loadLibrary(IPSLib::getAppDir('jawards') . "/sources/classes/jawards/class_jawards.php", "class_jawards", "jawards");
			$this->registry->setClass( 'class_jawards', new $classToLoad($registry) );
		}
		$this->awardCache = array();
		$this->registry->setClass( 'jawards_core', $this );
		IPSDebug::fireBug( 'info', array( 'Loaded', "app_class_jawards.php" ) );
		IPSDebug::fireBug( 'info', array( $this->caches['jawards_awards'], "Awards Cache" ) );
		IPSDebug::fireBug( 'info', array( $this->caches['jawards_cats'], "Awards Categories Cache" ) );
	}

	public function rebuildAllCaches()
	{
		$this->rebuildAwardsCategoryCache();
		$this->rebuildAwardsCache();
	}
	public function rebuildAwardsCategoryCache()
	{
		$cache = array();
		$this->DB->build( array( 'select'   => '*',
								 'from'     => 'jlogica_awards_cats',
								 'order'    => 'placement',
						)	   );
		$outer = $this->DB->execute();

		while( $row = $this->DB->fetch( $outer ) )
		{
			$awards = array();
			$this->DB->build( array( 'select'   => '*',
									 'from'     => 'jlogica_awards',
									 'order'    => 'placement',
									 'where'	=> "parent='{$row['cat_id']}'",
							)	   );
			$inner = $this->DB->execute();
			while( $a = $this->DB->fetch( $inner ) )
			{
				$awards[] = $a;
			}
			$row['awards'] = $awards;
			$cache[] = $row;
		}

		$this->cache->setCache( 'jawards_cats', $cache, array( 'array' => 1, 'deletefirst' => 1 ) );
	}

	public function rebuildAwardsCache()
	{
		$cache = array();
		$this->DB->build( array( 'select'   => '*',
								 'from'     => 'jlogica_awards',
								 'order'    => 'id',
						)	   );
		$outer = $this->DB->execute();

		while ( $row = $this->DB->fetch( $outer ) )
		{
			$cache[ $row['id'] ] = $row;
		}

		$this->cache->setCache( 'jawards_awards', $cache, array( 'array' => 1, 'deletefirst' => 1 ) );
	}

	private function buildAwardArray( $member_id, $hook )
	{
		$res = $this->getAwards( $member_id, $hook );
		$isShown = array();
		$awards = array();
		$count = 0;
		foreach( $res AS $a )
		{
			// multiawards 0 don't show
			if( $hook['settings']['multiawards'] == 0 )
			{
				if( ! isset( $isShown[$a['award_id']] ) )
				{
					if( $hook['settings']['count'] )
					{
						$count++;
					}
					if( $count > $hook['settings']['count'] )
					{
						break;
					}
					$isShown[$a['award_id']] = true;
					$awards[] = $a;
				}
			}
			else if( $hook['settings']['multiawards'] == 2 ) // multiawards 2 show count
			{
				if( ! isset( $isShown[$a['award_id']] ) )
				{
					if( $hook['settings']['count'] )
					{
						$count++;
					}
					if( $count > $hook['settings']['count'] )
					{
						continue;
					}
					$isShown[$a['award_id']] = true;
					$a['count'] = 0;
					$awards[] = $a;
				}
				foreach( $awards AS $k => $v )
				{
					if( $v['award_id'] == $a['award_id'] )
					{
						$awards[ $k ]['count']++;
						break;
					}
				}
			}
			else if( $hook['settings']['multiawards'] == 3 ) // show latest awards plus count
			{
				if( ! isset( $isShown[0] ) )
				{
					if( $hook['settings']['count'] )
					{
						$count++;
					}
					if( $count > $hook['settings']['count'] )
					{
						continue;
					}
					$isShown[0] = true;
					$a['count'] = 0;
					$awards[0] = $a;
				}
				foreach( $awards AS $k => $v )
				{
					$awards[0]['count']++;
				}
			}
			else	// multiawards 1 show all
			{
				if( $hook['settings']['count'] )
				{
					$count++;
				}
				if( $count > $hook['settings']['count'] )
				{
					break;
				}
				$awards[] = $a;
			}
		}
		return( $awards );
	}

	public function hook_japsd( $output, $key )
	{
		$hook = $this->HookInfo( 'japsd' );
		if( is_array( $this->registry->output->getTemplate('topic')->functionData['post'] )				AND
			count( $this->registry->output->getTemplate( 'topic' )->functionData['post'] )				AND
			$this->settings['jacore_enable']															AND
			$hook !== false																				AND
			$hook['settings']['show']																	AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			$cache	= array();

			foreach( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] as $inst => $data )
			{
				$pos = strpos( $output, $tag, $last );
				if( $pos !== FALSE )
				{
					$out = "";
					$member = IPSMember::load( $data['author']['member_id'] );
					if( $member['member_id'] )
					{
						if( ! isset( $this->awardCache[$hook['prefix']][$member['member_id']] ) )
						{
							$awards = $this->buildAwardArray( $member['member_id'], $hook );
							$this->awardCache[$hook['prefix']][$member['member_id']] = '';
							if( count( $awards ) )
							{
								$this->awardCache[$hook['prefix']][$member['member_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicSig_view( $awards, $member );
							}
						}
						$out = $this->awardCache[$hook['prefix']][$member['member_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}

	public function hook_japsdpm( $output, $key )
	{
		$hook = $this->HookInfo( 'japsd' );
		if( is_array( $this->registry->output->getTemplate('messaging')->functionData['showConversation'] )	AND
			count( $this->registry->output->getTemplate( 'messaging' )->functionData['showConversation'] )	AND
			$this->settings['jacore_enable']																AND
			$hook !== false																					AND
			$hook['settings']['show']																		AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			$cache	= array();

			foreach( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] as $inst => $data )
			{
				$pos = strpos( $output, $tag, $last );
				if( $pos !== FALSE )
				{
					$out = "";
					$member = IPSMember::load( $data['author']['member_id'] );
					if( $member['member_id'] )
					{
						if( ! isset( $this->awardCache[$hook['prefix']][$member['member_id']] ) )
						{
							$awards = $this->buildAwardArray( $member['member_id'], $hook );
							$this->awardCache[$hook['prefix']][$member['member_id']] = '';
							if( count( $awards ) )
							{
								$this->awardCache[$hook['prefix']][$member['member_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicSig_view( $awards, $member );
							}
						}
						$out = $this->awardCache[$hook['prefix']][$member['member_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}

	public function hook_jappn( $output, $key )
	{
		$hook = $this->HookInfo( 'jappn' );
		IPSDebug::fireBug( 'info', array( $hook, "Hook Info" ) );
		if( is_array( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )	AND
			count( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )		AND
			$this->settings['jacore_enable']															AND
			$hook !== false																				AND
			$hook['settings']['show']																	AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			$cache	= array();
			foreach( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] as $inst => $data )
			{
				$pos = strpos( $output, $tag, $last );
				if( $pos !== FALSE )
				{
					$out = "";
					$member = IPSMember::load( $data['author']['member_id'] );
					if( $member['member_id'] )
					{
						if( ! isset( $this->awardCache[$hook['prefix']][$member['member_id']] ) )
						{
							$awards = $this->buildAwardArray( $member['member_id'], $hook );
							$this->awardCache[$hook['prefix']][$member['member_id']] = '';
							if( count( $awards ) )
							{
								$this->awardCache[$hook['prefix']][$member['member_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicPost_jappn( $awards, $member );
							}
						}
						$out = $this->awardCache[$hook['prefix']][$member['member_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}

	public function hook_japab( $output, $key )
	{
		$hook = $this->HookInfo( 'japab' );
		IPSDebug::fireBug( 'info', array( $hook, "Hook Info" ) );
		if( is_array( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )	AND
			count( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )		AND
			$this->settings['jacore_enable']															AND
			$hook !== false																				AND
			$hook['settings']['show']																	AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			$cache	= array();

			foreach( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] as $inst => $data )
			{
				$pos = strpos( $output, $tag, $last );
				if( $pos !== FALSE )
				{
					$out = "";
					$member = IPSMember::load( $data['author']['member_id'] );
					if( $member['member_id'] )
					{
						if( ! isset( $this->awardCache[$hook['prefix']][$member['member_id']] ) )
						{
							$awarded = $this->getAwards( $member['member_id'], $hook );
							IPSDebug::fireBug( 'info', array( $awarded, "Awarded" ) );
							$this->DB->build( array(	'select'   => 'a.*',
														'from'     => array('jlogica_awards' => 'a'),
														'add_join' => array(	array(	'select' => 'ac.cat_id,ac.location',
																						'from'   => array('jlogica_awards_cats' => 'ac'),
																						'where'  => 'a.parent = ac.cat_id',
																						),
												   							),
														'where'    => "location = '{$hook['id']}'",
														'order'    => 'placement DESC'
													));
							IPSDebug::fireBug( 'info', array( $this->DB->fetchSqlString(), "SQL" ) );
							$res = $this->DB->execute();
							$awards = array();
							while( $a = $this->DB->fetch( $res ) )
							{
								$a['hook']		= $hook;
								$a['dim']		= $hook['settings']['dim'];
								$a['count'] 	= 0;
								$this->getToolTip( $a );
								$a['descno']		= trim( $a['descno'] );
								if( ! empty( $a['descno'] ) )
								{
									$a['toolTip'] = $a['descno'];
								}
								$this->getSize( $a );
								foreach( $awarded AS $aa )
								{
									if( $aa['award_id'] == $a['id'] )
									{
										if( $a['count'] == 0 )
										{
											$a['toolTip'] = $aa['toolTip'];
										}
										if( $hook['settings']['multiawards'] == 0 )
										{
											$a['count'] = 1;
										}
										else
										{
											$a['count']++;
										}
									}
								}
								$awards[] = $a;

							}
							IPSDebug::fireBug( 'info', array( $awards, "Achievement Awards" ) );
							$this->awardCache[$hook['prefix']][$member['member_id']] = '';
							if( count( $awards ) )
							{
								$this->awardCache[$hook['prefix']][$member['member_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicPost_japab( $awards, $member );
							}
						}
						$out = $this->awardCache[$hook['prefix']][$member['member_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}


	public function hook_japbd( $output, $key )
	{
		static $badgesList = NULL;
		$hook = $this->HookInfo( 'japbd' );
		IPSDebug::fireBug( 'info', array( $hook, "Hook Info" ) );
		if( is_array( $this->registry->output->getTemplate('topic')->functionData['post'] )				AND
			count( $this->registry->output->getTemplate('topic')->functionData['post'] )				AND
			$this->settings['jacore_enable']															AND
			$hook !== false																				AND
			$hook['settings']['show']																	AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			if( is_null( $badgesList ) )
			{
				$badgesList = array();
				$this->DB->build( array(	'select'   => 'a.icon, a.badge_perms, a.visible',
											'from'     => array('jlogica_awards' => 'a'),
											'add_join' => array(	array(	'select' => 'ac.cat_id, ac.location',
																			'from'   => array('jlogica_awards_cats' => 'ac'),
																			'where'  => 'a.parent = ac.cat_id',
																		),
									   							),
											'where'    => "ac.location = '{$hook['id']}' AND a.visible = '1'",
											'order'    => 'a.placement'
										));
				IPSDebug::fireBug( 'info', array( $this->DB->fetchSqlString(), "Badge List SQL" ) );
				$res = $this->DB->execute();
				while( $a = $this->DB->fetch( $res ) )
				{
					$inst = array();
					$inst['icon'] = $a['icon'];
					$inst['perm'] = explode( ',', $a['badge_perms'] );
					$badgeList[] = $inst;
				}
				IPSDebug::fireBug( 'info', array( $badgeList, "Badge Cache" ) );
			}
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			if( count( $badgeList ) )
			{
				foreach( $this->registry->output->getTemplate('topic')->functionData['post'] as $inst => $data )
				{
					$pos = strpos( $output, $tag, $last );
					if( $pos !== false )
					{
/*
echo "<pre>";
echo "Author\n";
print_r( $data['post']['author'] );
exit;
*/
						if( ! isset( $this->awardCache[$hook['prefix']][$data['post']['author']['author_id']] ) )
						{
							$this->awardCache[$hook['prefix']][$data['post']['author']['author_id']] = '';
							foreach( $badgeList AS $badge )
							{
								if( IPSMember::isInGroup( $data['post']['author'], $badge['perm'] ) )
								{
									$this->awardCache[$hook['prefix']][$data['post']['author']['author_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicPost_japbd( $badge['icon'] );
									break;
								}
							}
						}
						$out = $this->awardCache[$hook['prefix']][$data['post']['author']['author_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}

	public function hook_jappd( $output, $key )
	{
		$hook = $this->HookInfo( 'jappd' );
		if( is_array( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )	AND
			count( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] )		AND
			$this->settings['jacore_enable']															AND
			$hook !== false																				AND
			$hook['settings']['show']																	AND
			is_dir( $this->settings['upload_dir'] . '/jawards/' )
			)
		{
			$tag	= "<!--hook." . $key . "-->";
			$last	= 0;
			$cache	= array();

			foreach( $this->registry->output->getTemplate('global')->functionData['userInfoPane'] as $inst => $data )
			{
				$pos = strpos( $output, $tag, $last );
				if( $pos !== FALSE )
				{
					$out = "";
					$member = IPSMember::load( $data['author']['member_id'] );
					if( $member['member_id'] )
					{
						if( ! isset( $this->awardCache['jappd'][$member['member_id']] ) )
						{
							$awards = $this->buildAwardArray( $member['member_id'], $hook );
							$this->awardCache['jappd'][$member['member_id']] = '';
							if( count( $awards ) )
							{
								$this->awardCache['jappd'][$member['member_id']] = $this->registry->getClass('output')->getTemplate('jawards')->topicPost_view( $awards, $member );
							}
						}
						$out = $this->awardCache['jappd'][$member['member_id']];
					}
					$output	= substr_replace( $output, $out . $tag, $pos, strlen( $tag ) );
					$last	= $pos + strlen( $tag . $out );
				}
			}
		}
		return $output;
	}

	public function hook_japa()
	{
		$hook = $this->HookInfo( 'japa' );
		if( is_array( $this->registry->output->getTemplate( 'profile' )->functionData['profileModern'] )	AND
			count( $this->registry->output->getTemplate( 'profile' )->functionData['profileModern'] )		AND
			$this->settings['jacore_enable']																AND
			$hook !== false																					AND
			! $this->settings['jawards_disable_public_awding'] )
		{
			$memberData = $this->registry->output->getTemplate( 'profile' )->functionData['profileModern'][0]['member'];
			if( $this->registry->getClass( 'class_jawards' )->canPublic_award( $memberData ) )
			{
				return "<li><a href='{$this->settings['base_url']}app=jawards&do=award&mid={$memberData['member_id']}' class='ipsButton_secondary'><img src='{$this->settings['img_url']}/award_star_add.png' border='0'/>&nbsp;&nbsp; {$this->lang->words['profile_public_award']}</a></li>";
			}
		}
		return '';
	}
/*
	private function getAward( &$a )
	{
		$award = $this->caches['jawards_awards'][$a['award_id']];
		$a['id']		= $award['id'];
		$a['name']		= $award['name'];
		$a['desc']		= $award['desc'];
		$a['icon']		= $award['icon'];
		$a['parent']	= $award['parent'];
		IPSDebug::fireBug( 'info', array( $award, "app_class_jawards::getAward()" ) );
	}

	private function getAwardCat( &$a )
	{
		$cat = $this->caches['jawards_cats'][$a['parent']];
		IPSDebug::fireBug( 'info', array( $cat, "app_class_jawards::getAwardCat()" ) );
	}
*/

	private function getAwards( $member_id, $hook )
	{
		//  Stage 1 -- Grab the normally awarded items (either auto-awards or standard ones)
		$this->DB->build( array(	'select'   => 'aa.row_id,aa.award_id,aa.notes,aa.is_active,aa.date',
									'from'     => array('jlogica_awards_awarded' => 'aa'),
									'add_join' => array(	array(	'select' => 'a.id,a.name,a.desc,a.icon',
																	'from'   => array('jlogica_awards' => 'a'),
																	'where'  => 'a.id = aa.award_id',
																),
															array(	'select' => 'ac.cat_id,ac.location',
																	'from'   => array('jlogica_awards_cats' => 'ac'),
																	'where'  => 'a.parent = ac.cat_id',
																),
							   							),
									'where'    => "user_id = '{$member_id}' && `is_active` = '1' && `approved` = '1' && location = '{$hook['id']}'",
									'order'    => 'date DESC'
								));
//		IPSDebug::fireBug( 'info', array( $this->DB->fetchSqlString(), "getAwards SQL" ) );
		$res = $this->DB->execute();
		$awards = array();
		while( $a = $this->DB->fetch( $res ) )
		{
			$a['hook']		= $hook;
//			$this->getAward( $a );
//			$this->getAwardCat( $a );
			$this->getToolTip( $a );
			$this->getSize( $a );
			$awards[] = $a;

		}

		// Stage 2 -- Use the Collector system to return Real-Time Awards

		return( $awards );
	}

	private function HookSettings( $prefix )
	{
		if( ! $this->hookSettingsCache[$prefix] )
		{
			$xPrefix = $prefix . '_';
			$lPrefix = strlen( $xPrefix );
			$this->hookSettingsCache[$prefix] = array();
			foreach( $this->settings AS $k => $v )
			{
				if( substr( $k, 0, $lPrefix ) == $xPrefix )
				{
					$this->hookSettingsCache[$prefix][ substr( $k, $lPrefix ) ] = $v;
				}
			}
			IPSDebug::fireBug( 'info', array( $this->hookSettingsCache, "app_class_jawards::hookSettingsCache( {$prefix} ) - Loaded" ) );
		}
		else
		{
			IPSDebug::fireBug( 'info', array( $this->hookSettingsCache, "app_class_jawards::hookSettingsCache( {$prefix} ) - Cache" ) );
		}
		return( $this->hookSettingsCache[$prefix] );
	}

	private function HookID( $id )
	{
		$id - intval( $id );
		switch( $id )
		{
			case '0':
				return( $this->HookInfo( 'jappd' ) );
				break;
			case '1':
				return( $this->HookInfo( 'japsd' ) );
				break;
			case '2':
				return( $this->HookInfo( 'japbd' ) );
				break;
			case '3':
				return( $this->HookInfo( 'japa' ) );
				break;
			case '4':
				return( $this->HookInfo( 'jaabp' ) );
				break;
			case '5':
				return( $this->HookInfo( 'japab' ) );
				break;
			case '6':
				return( $this->HookInfo( 'jappn' ) );
				break;
			case '7':
				return( $this->HookInfo( 'jaasp' ) );
				break;
			case '8':
				return( $this->HookInfo( 'jaabb' ) );
				break;
			case '9':
				return( $this->HookInfo( 'jaabg' ) );
				break;
			case '10':
				return( $this->HookInfo( 'jaafu' ) );
				break;
			case '11':
				return( $this->HookInfo( 'jaanp' ) );
				break;
			case '12':
				return( $this->HookInfo( 'jacore' ) );
				break;
			default:
				return( false );
				break;
		}
		return( false );
	}

	public function HookInfo( $id )
	{
		$id = strtolower( $id );
		switch( $id )
		{
			case 'jappd':	// Profile Display
				return( array( 'id' => 0, 'prefix' => 'jappd', 'settings' => $this->HookSettings( 'jappd' ) ) );
				break;
			case 'japsd':	// Signature Display
				return( array( 'id' => 1, 'prefix' => 'japsd', 'settings' => $this->HookSettings( 'japsd' ) ) );
				break;
			case 'japbd':	// Badge Display
				return( array( 'id' => 2, 'prefix' => 'japbd', 'settings' => $this->HookSettings( 'japbd' ) ) );
				break;
			case 'japa':	// Public Awarding
				return( array( 'id' => 3, 'prefix' => 'japa',  'settings' => $this->HookSettings( 'japa' ) ) );
				break;
			case 'jaabp':	// AutoAward by Post
				return( array( 'id' => 4, 'prefix' => 'jaabp',  'settings' => $this->HookSettings( 'jaabp' ) ) );
				break;
			case 'japab':	// Achievement Display
				return( array( 'id' => 5, 'prefix' => 'japab',  'settings' => $this->HookSettings( 'japab' ) ) );
				break;
			case 'jappn':	// Naked Profile icons
				return( array( 'id' => 6, 'prefix' => 'jappn',  'settings' => $this->HookSettings( 'jappn' ) ) );
				break;
			case 'jaasp':	// Subscription Payment
				return( array( 'id' => 7, 'prefix' => 'jaasp',  'settings' => $this->HookSettings( 'jaasp' ) ) );
				break;
			case 'jaabb':	// Blog Post
				return( array( 'id' => 8, 'prefix' => 'jaabb',  'settings' => $this->HookSettings( 'jaabb' ) ) );
				break;
			case 'jaabg':	// Gallery Image
				return( array( 'id' => 9, 'prefix' => 'jaabg',  'settings' => $this->HookSettings( 'jaabg' ) ) );
				break;
			case 'jaafu':	// File Upload
				return( array( 'id' => 10, 'prefix' => 'jaafu',  'settings' => $this->HookSettings( 'jaafu' ) ) );
				break;
			case 'jaanp':	// Nexus Payment
				return( array( 'id' => 11, 'prefix' => 'jaanp',  'settings' => $this->HookSettings( 'jaanp' ) ) );
				break;
			case 'jacore':	// Core Settings
				return( array( 'id' => 12, 'prefix' => 'jacore',  'settings' => $this->HookSettings( 'jacore' ) ) );
				break;
			default:
				return( false );
				break;
		}
		return( false );
	}

	private function getSize( &$a )
	{
		$width = '';
		$height = '';
		if( $a['hook']['settings']['resize'] && function_exists( 'getimagesize' ) )
		{
			list( $width, $height ) = getimagesize( "{$this->settings['upload_dir']}/jawards/{$a['icon']}" );

			$split = explode( "x", $a['hook']['settings']['resize_size'] );
			$maxW  = $split[0];
			$maxH  = $split[1];

			if( $width > $maxW )
			{
				$lgr = $maxW / $width;

				$width  = round( $width * $lgr );
				$height = round( $height * $lgr );
			}

			if( $height > $maxH )
			{
				$lgr = $maxH / $height;

				$width  = round( $width * $lgr );
				$height = round( $height * $lgr );
			}
		}
		$a['width']  = $width;
		$a['height'] = $height;
	}

	private function getToolTip( &$a )
	{
		$tt = $a['hook']['settings']['tool_tip'];
		$date = ipsRegistry::getClass( 'class_localization' )->getDate( $a['date'], 'LONG' );
		$search  = array( '{awards_name}',	'{awards_desc}',	'{awards_notes}',	'{awards_date}' );		// Really old format
		$replace = array( $a['name'],		$a['desc'],			$a['notes'],		$date );
		$tt = str_replace( $search, $replace, $tt );
		$search  = array( '{jawards_name}',	'{jawards_desc}',	'{jawards_notes}',	'{jawards_date}' );		// JLogica format
		$replace = array( $a['name'],		$a['desc'],			$a['notes'],		$date );
		$tt = str_replace( $search, $replace, $tt );
		$search  = array( '{name}',			'{desc}',			'{notes}',			'{date}' );				// Sensible format
		$replace = array( $a['name'],		$a['desc'],			$a['notes'],		$date );
		$tt = str_replace( $search, $replace, $tt );
		$a['toolTip'] = $tt;
	}
}
