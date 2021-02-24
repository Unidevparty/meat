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


class admin_trackmembers_overview_overview extends ipsCommand
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
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_overview' );

		/* Set up stuff */
		$this->form_code	= $this->html->form_code	= 'module=overview&amp;section=overview';
		$this->form_code_js	= $this->html->form_code_js	= 'module=overview&section=overview';

		//-----------------------------------------
		// What to do?
		//-----------------------------------------
		
		switch($this->request['do'])
		{
			case 'settings':
				$this->registry->class_permissions->checkPermissionAutoMsg( 'manage_settings' );
				$this->_manageSettings();
			break;
							
			case 'overview':
			default:
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
		$this->DB->build( array( 'select' => 'upgrade_version_id, upgrade_version_human, upgrade_date',
								 'from'   => 'upgrade_history',
								 'where'  => "upgrade_app='trackmembers'",
								 'order'  => 'upgrade_version_id DESC',
								 'limit'  => array( 0, 2 )
		) );
   		
		$this->DB->execute();
		
   		while ( $row = $this->DB->fetch() )
   		{
   			$row['_date'] = $this->registry->getClass('class_localization')->formatTime( $row['upgrade_date'], 'SHORT' );
   			$data['upgrade'][] = $row;
   		}
   		
		$members 	= $this->DB->buildAndFetch( array( 'select' => 'count(member_id) as total',
								 				   	   'from'   => 'members',
								 				   	   'where'	=> "member_tracked = 1"
		) );

		$tracks 	= $this->DB->buildAndFetch( array( 'select' => 'count(id) as total',
								 				   	   'from'   => 'members_tracker'
		) );

		$members	= intval( $members['total'] );
		$tracks 	= intval( $tracks['total'] );

		$this->registry->output->html .= $this->html->overviewIndex( $data, $members, $tracks );
	}

	public function _manageSettings()
	{
		$classToLoad = IPSLib::loadActionOverloader( IPS_ROOT_PATH . 'applications/core/modules_admin/settings/settings.php', 'admin_core_settings_settings' );
		$settings    = new $classToLoad();
		$settings->makeRegistryShortcuts( $this->registry );
		
		$this->lang->loadLanguageFile( array( 'admin_tools' ), 'core' );
		
		$settings->html			= $this->registry->output->loadTemplate( 'cp_skin_settings', 'core' );	
				
		$settings->form_code	= $settings->html->form_code    = 'module=tools&amp;section=settings';
		$settings->form_code_js	= $settings->html->form_code_js = 'module=tools&section=settings';

		$this->request['conf_title_keyword'] = 'trackmembers';
		$settings->return_after_save         = $this->settings['base_url'].$this->form_code.'&do=settings';
		$settings->_viewSettings();	
	}	
}