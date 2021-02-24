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


class admin_trackmembers_manage_manage extends ipsCommand
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
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_manage' );

		/* Set up stuff */
		$this->form_code	= $this->html->form_code	= 'module=manage&amp;section=manage';
		$this->form_code_js	= $this->html->form_code_js	= 'module=manage&section=manage';

		//-----------------------------------------
		// What to do?
		//-----------------------------------------
		
		switch( $this->request['do'] )
		{
			case 'listmember':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_management' );
				$this->_listMember();
			break;
			case 'view':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_management' );
				$this->_viewLogs();
			break;
			case 'prune':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_management' );
				$this->_pruneLogs();
			break;
			case 'stop':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_management' );
				$this->_stopTracking();
			break;

			default:
			case 'manage':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'mod_management' );
				$this->home();
			break;
		}
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	public function home()
	{
		$members = array();
		$total	 = array();
		$guys	 = array();

		$start		= intval( $this->request['st'] ) >= 0 ? intval( $this->request['st'] ) : 0;
		$perpage	= intval( $this->settings['trackmembers_nrav'] ) ? $this->settings['trackmembers_nrav'] : 10;

		$items = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as cnt',
												  'from'   => 'members',
												  'where'  => 'member_tracked = 1'

		) );

		$cnt = $items['cnt'];

		$links = $this->registry->output->generatePagination( 
										array(  'totalItems'		=> $cnt,
												'itemsPerPage'		=> $perpage,
												'currentStartValue'	=> $start,
												'baseUrl'			=> $this->settings['base_url'] . "{$this->form_code}" ) );

		$this->DB->build( array( 	'select'	=> 'member_id',
									'from'		=> 'members',
									'where'		=> 'member_tracked = 1',
									'limit' 	=> array( $start, $perpage )
		) );

		$mems = $this->DB->execute();

		while ( $r = $this->DB->fetch( $mems ) )
		{
			$guys[ $r['member_id'] ] = $r['member_id'];
		}

		if( is_array( $guys ) AND count( $guys ) )
		{
			foreach( $guys as $id )
			{
				$members[ $id ] = IPSMember::load( $id, 'core' );
			}

			$this->DB->build( array( 'select'	=> 'member_id, count( id ) as cnt',
									 'from'		=> 'members_tracker',
									 'where'	=> 'member_id IN (' . implode( ',', $guys ) . ')',
									 'group'	=> 'member_id'
			) );
	
			$count = $this->DB->execute();
		
			while ( $r = $this->DB->fetch( $count ) )
			{
				$total[ $r['member_id'] ] = $r['cnt'];
			}
		}

		$this->registry->output->html .= $this->html->overviewIndex( $members, $total, $links, $cnt );
	}

	public function _viewLogs()
	{
		$mid   = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( $this->lang->words['manage_nomid'] );
		}

		$start		= intval( $this->request['st'] ) >= 0 ? intval( $this->request['st'] ) : 0;
		$perpage	= intval( $this->settings['trackmembers_nrav'] ) ? $this->settings['trackmembers_nrav'] : 10;

		$items = $this->DB->buildAndFetch( array( 'select' => 'COUNT(*) as cnt',
												  'from'   => 'members_tracker',
												  'where'  => 'member_id = ' . $mid

		) );

		$nr_logs = $items['cnt'];

		$links = $this->registry->output->generatePagination( 
										array(  'totalItems'		=> $nr_logs,
												'itemsPerPage'		=> $perpage,
												'currentStartValue'	=> $start,
												'baseUrl'			=> $this->settings['base_url'] . "{$this->form_code}" . "&amp;do=view&amp;mid=". $mid ) );

		$this->DB->build( array( 'select'	=> '*',
								 'from'		=> 'members_tracker',
								 'where'  	=> 'member_id = ' . $mid,
								 'order'	=> 'id desc',
								 'limit' 	=> array( $start, $perpage )
		) );

		$q = $this->DB->execute();

		if ( $this->DB->getTotalRows( $q ) )
		{
			while ( $r = $this->DB->fetch( $q ) )
			{
				$logs[ $r['id'] ] = $r;
			}
		}

		$this->registry->output->html .= $this->html->viewLogs( $logs, $nr_logs, $links );
	}

	public function _pruneLogs()
	{
		$mid   = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( $this->lang->words['manage_nomid'] );
		}

		$this->DB->delete( 'members_tracker', 'member_id =' . $mid );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $this->lang->words['tools_logs_pruned'] );

		$this->registry->output->global_message	= $this->lang->words['tools_logs_pruned'];
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code . '&do=view&mid=' . $mid );
	}

	public function _stopTracking()
	{
		$mid   = intval( $this->request['mid'] );

		if ( !$mid )
		{
			$this->registry->output->showError( $this->lang->words['manage_nomid'] );
		}

		IPSMember::save( $mid, array( 'core' => array( 'member_tracked' => 0, 'member_tracked_deadline' => 0 ) ) );

		$this->registry->getClass('adminFunctions')->saveAdminLog( $this->lang->words['manage_stoptracking_done'] );

		$this->registry->output->global_message	= $this->lang->words['manage_stoptracking_done'];
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code . '&do=view&mid=' . $mid );
	}
}