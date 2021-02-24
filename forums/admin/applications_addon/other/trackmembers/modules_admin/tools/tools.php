<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}


class admin_trackmembers_tools_tools extends ipsCommand
{
	
	/**
	 * Skin object
	 *
	 * @access	private
	 * @var		object			Skin templates
	 */
	private $html;
	
	/**
	 * Shortcut for url
	 *
	 * @access	private
	 * @var		string			URL shortcut
	 */
	private $form_code;
	
	/**
	 * Shortcut for url (javascript)
	 *
	 * @access	private
	 * @var		string			JS URL shortcut
	 */
	private $form_code_js;
	
	/**
	 * Main class entry point
	 *
	 * @access	public
	 * @param	object		ipsRegistry reference
	 * @return	void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Load HTML
		//-----------------------------------------
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_tools' );

		/* Set up stuff */
		$this->form_code	= $this->html->form_code	= 'module=tools&amp;section=tools';
		$this->form_code_js	= $this->html->form_code_js	= 'module=tools&section=tools';

		//-----------------------------------------
		// What to do?
		//-----------------------------------------
		
		switch( $this->request['do'] )
		{
			case 'prune':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_tools' );
				$this->_pruneLogs();
			break;
			case 'orphaned':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_tools' );
				$this->_pruneOrphanedLogs();
			break;
			case 'track':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_tools' );
				$this->_trackMembers();
			break;
			case 'untrack':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_tools' );
				$this->_untrackMembers();
			break;
							
			case 'overview':
			default:
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_tools' );
				$this->home();
			break;
		}
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	/*-------------------------------------------------------------------------*/
	// Home
	/*-------------------------------------------------------------------------*/

	public function home()
	{
		$select  = "<select name='groups'>";
		$select .= "<option value='0'>-- {$this->lang->words['all_groups']} --</option>";
		
		foreach( $this->cache->getCache('group_cache') as $k => $v )
		{
			if ( in_array( $k, explode( ',', $this->settings['trackmembers_groups'] ) ) AND $k != $this->settings['guest_group'] AND $k != $this->settings['banned_group'] )
			{
				$select .= "<option value='{$k}'>{$v['g_title']} {$this->lang->words['track_all_members_groups']}</option>";
			}
		}
		
		$select .= "</select>";

		$this->registry->output->html .= $this->html->overviewIndex( $select );
	}

	public function _pruneLogs()
	{
		$this->DB->delete( 'members_tracker' );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $this->lang->words['tools_logs_pruned'] );

		$this->registry->output->global_message	= $this->lang->words['tools_logs_pruned'];
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code );
	}

	public function _pruneOrphanedLogs()
	{
		$this->DB->build( array( 'select'	=> 'member_id',
								 'from'		=> 'members_tracker',
								 'group'	=> 'member_id'
		) );

		$t = $this->DB->execute();

		if ( $this->DB->getTotalRows( $t ) )
		{
			while ( $r = $this->DB->fetch( $t ) )
			{
				$ids[] = $r['member_id'];
			}
		}

		if ( !is_array( $ids ) OR !count( $ids ) )
		{
			$this->registry->output->showError( $this->lang->words['manage_notracked'] );
		}

		$this->DB->build( array(
								'select' 	=> 'member_id, member_tracked',
								'from' 		=> 'members',
								'where' 	=> 'member_id IN (' . implode( ',', $ids ) . ')'
		) );

		$q = $this->DB->execute();

		while ( $r = $this->DB->fetch( $q ) )
		{
			if ( $r['member_tracked'] == 0 )
			{
				$orphaned[] = $r['member_id'];
			}
		}

		if ( !is_array( $orphaned ) OR !count( $orphaned ) )
		{
			$this->registry->output->showError( $this->lang->words['manage_noorphaned'] );
		}

		$this->DB->delete( 'members_tracker', 'member_id IN (' . implode( ',', $orphaned ) . ')' );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $this->lang->words['prune_orphaned_logs_done'] );

		$this->registry->output->global_message	= $this->lang->words['prune_orphaned_logs_done'];
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code );
	}

	public function _untrackMembers()
	{
		$this->DB->update( 'members', array( 'member_tracked' => 0, 'member_tracked_deadline' => 0 ) );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $this->lang->words['tools_membres_untracked'] );

		$this->registry->output->global_message	= $this->lang->words['tools_membres_untracked'];
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code );
	}

	public function _trackMembers()
	{
		if ( !$this->settings['trackmembers_onoff'] )
		{
			$this->registry->output->showError( $this->lang->words['tools_membres_track_all_appdisabled'] );
		}

		$days 	= intval( $this->request['days'] );
		$group	= $this->request['groups'];

		if ( $group > 0 AND !in_array( $group, explode( ',', $this->settings['trackmembers_groups'] ) ) )
		{
			$text = sprintf( $this->lang->words['tools_membres_track_all_notallowed_group'], strtoupper( $this->caches['group_cache'][ $group ]['g_title'] ) );
			
			$this->registry->output->showError( $text );
		}

		if ( !$days )
		{
			$this->registry->output->showError( $this->lang->words['tools_membres_track_all_generic'] );
		}

		if ( $days > 30 )
		{
			$this->registry->output->showError( $this->lang->words['tools_membres_track_all_overamonth'] );
		}

		$limit = IPS_UNIX_TIME_NOW + ( 86400 * $days );

		if ( $group > 0 )
		{
			$this->DB->update( 'members', array( 'member_tracked' => 1, 'member_tracked_deadline' => $limit ), 'member_group_id = ' . $group );
		}
		else
		{
			$this->DB->update( 'members', array( 'member_tracked' => 1, 'member_tracked_deadline' => $limit ) );
		}

		$text  = $group > 0 ? strtoupper( $this->caches['group_cache'][ $group ]['g_title'] ) . ' ' . $this->lang->words['track_all_members_groups'] : $this->lang->words['all_groups'];

		$msg	= sprintf( $this->lang->words['tools_membres_track_all_done'], $text, $this->registry->getClass('class_localization')->getDate( $limit, 'LONG' ) );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $msg );

		$this->registry->output->global_message	= $msg;
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code );
	}
}