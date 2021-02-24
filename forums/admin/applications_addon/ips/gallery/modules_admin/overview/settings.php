<?php
/**
 * @file		settings.php 	IP.Gallery settings
 *~TERABYTE_DOC_READY~
 * $Copyright: (c) 2001 - 2011 Invision Power Services, Inc.$
 * $License: http://www.invisionpower.com/company/standards.php#license$
 * $Author: bfarber $
 * @since		1st April 2004
 * $LastChangedDate: 2012-05-22 11:04:13 -0400 (Tue, 22 May 2012) $
 * @version		v5.0.5
 * $Revision: 10780 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

/**
 *
 * @class		admin_gallery_overview_settings
 * @brief		IP.Gallery settings
 */
class admin_gallery_overview_settings extends ipsCommand
{
	/**
	 * Skin object shortcut
	 *
	 * @var		object
	 */
	public $html;
	
	/**
	 * String for the screen url bit
	 *
	 * @var		string
	 */
	public $form_code    = '';
	
	/**
	 * String for the JS url bit
	 *
	 * @var		string
	 */
	public $form_code_js = '';
	
	/**
	 * Main function executed automatically by the controller
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		//-----------------------------------------
		// Set up stuff
		//-----------------------------------------
		
		$this->form_code	= 'module=overview&amp;section=settings&amp;';
		$this->form_code_js	= 'module=overview&section=settings&';
		
		//-------------------------------
		// Grab, init and load settings
		//-------------------------------
		
		$classToLoad	= IPSLib::loadActionOverloader( IPSLib::getAppDir('core') . '/modules_admin/settings/settings.php', 'admin_core_settings_settings' );
		$settings		= new $classToLoad( $this->registry );
		$settings->makeRegistryShortcuts( $this->registry );
		
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'admin_tools' ), 'core' );
		
		$settings->html			= $this->registry->output->loadTemplate( 'cp_skin_settings', 'core' );
		$settings->form_code	= $settings->html->form_code    = 'module=settings&amp;section=settings&amp;';
		$settings->form_code_js	= $settings->html->form_code_js = 'module=settings&section=settings&';

		$this->request['conf_title_keyword'] = 'invisiongallerysettings';
		$settings->return_after_save         = $this->settings['base_url'] . $this->form_code;
		$settings->_viewSettings();
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}
}