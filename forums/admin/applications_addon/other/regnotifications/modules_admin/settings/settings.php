<?php

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

class admin_regnotifications_settings_settings extends ipsCommand
{
	/**
	 * Shortcut for url
	 *
	 * @access	protected
	 * @var		string			URL shortcut
	 */
	protected $form_code;
	
	/**
	 * Shortcut for url (javascript)
	 *
	 * @access	protected
	 * @var		string			JS URL shortcut
	 */
	protected $form_code_js;
	
	/**
	 * Skin object
	 *
	 * @access	public
	 * @var		object
	 */
	public $html;

	/**
	 * Main class entry point
	 *
	 * @access	public
	 * @param	object		ipsRegistry reference
	 * @return	@e void		[Outputs to screen]
	 */
	public function doExecute( ipsRegistry $registry )
	{
		//-----------------------------------------
		// Load HTML
		//-----------------------------------------
		$this->html         = $this->registry->output->loadTemplate( 'cp_skin_regnotifications' );
		
		//-----------------------------------------
		// Set up stuff
		//-----------------------------------------
		$this->form_code    = $this->html->form_code	=  'module=settings&amp;section=settings';
		$this->form_code_js = $this->html->form_code_js	=  'module=settings&section=settings';

		//-----------------------------------------
		// Load Language
		//-----------------------------------------
		$this->lang->loadLanguageFile( array( 'admin_lang' ), "regnotifications" );

		//-----------------------------------------
		// What to do?
		//-----------------------------------------
		switch( $this->request['do'] )
		{
			case 'save':
				$this->_save();
				break;
			
			default:
				$this->_overview();
				break;
		}

		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	private function _save()
	{

		$data = array();

    	$data['mdmx_regnotifications_on'] = intval($_POST['active']);

		$members  = $_POST['members'];

		if( is_array( $members ) )
		{
			$members = IPSMember::load( $members, 'all', 'displayname' );
			$to_save = array();
			if( !empty( $members ) )
			{
				foreach ($members as $k => $v) {
					$to_save[] = $v['member_id'];
				}
			}
			$data['mdmx_regnotifications_members'] = implode(',', $to_save);
		}

    	$data['mdmx_regnotifications_groups'] = is_array($_POST['groups']) ? implode(',', $_POST['groups']) : ',';

    	$template = $_POST['template'];

    	$data['mdmx_regnotifications_template'] = $template;

    	IPSLib::updateSettings( $data, TRUE );

    	$this->registry->output->setMessage( "Настройки успешно сохранены" );
    	$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . $this->form_code);
    	

	}

	private function _overview()
	{
		$data = array(
						'active'   => $this->settings['mdmx_regnotifications_on'],
						'groups'   => explode(',', $this->settings['mdmx_regnotifications_groups']),
						'members_id'  => explode(',', $this->settings['mdmx_regnotifications_members']),
						'template' => $this->settings['mdmx_regnotifications_template'],
						'pf'       => array( 
											array( 'id' => '<#BOARD_ADDRESS#>', 'name' => "Адрес форума" ),
											array( 'id' => '<#BOARD_NAME#>', 'name' => "Имя форума" ),
											array(),
											array( 'id' => '<#NAME#>', 'name' => "Имя пользователя" ),
											array( 'id' => '<#EMAIL#>', 'name' => "Адрес электронной почты" ),
											array( 'id' => '<#DISPLAYNAME#>', 'name' => "Отображаемое имя" ),
											array( 'id' => '<#DATE#>', 'name' => "Дата регистрации" ),
											array( 'id' => '<#IP#>', 'name' => "IP адрес" ),
											array( 'id' => '<#PROFILE_LINK#>', 'name' => "Ссылка на профиль" ),
											));
		$this->DB->build(array( 'select' => 'pf_id, pf_title', 'from' => 'pfields_data', 'order' => "pf_group_id ASC" ));
		$this->DB->execute();
		if( $this->DB->getTotalRows() > 0 ) {
			while ( $r = $this->DB->fetch() ) {
				$data['pf'][] = array( 'id' => "<#FIELD_" . strtoupper($r['pf_id']) . "#>", 'name' => $r['pf_title'] );
			}
		}

		$members = IPSMember::load( $data['members_id'] );
		$data['members'] = $this->html->members( $members );

		$this->registry->output->html .= $this->html->overview( $data );
	}
}