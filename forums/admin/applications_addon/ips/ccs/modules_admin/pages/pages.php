<?php

/**
 * <pre>
 * Invision Power Services
 * IP.CCS pages
 * Last Updated: $Date: 2012-01-17 21:56:35 -0500 (Tue, 17 Jan 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Content
 * @link		http://www.invisionpower.com
 * @since		1st March 2009
 * @version		$Revision: 10146 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly.";
	exit();
}

class admin_ccs_pages_pages extends ipsCommand
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
		
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_pages' );
		
		//-----------------------------------------
		// Set up stuff
		//-----------------------------------------
		
		$this->form_code	= $this->html->form_code	= 'module=pages&amp;section=pages';
		$this->form_code_js	= $this->html->form_code_js	= 'module=pages&section=pages';

		//-----------------------------------------
		// Load Language
		//-----------------------------------------
		
		ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'admin_lang' ) );

		//-----------------------------------------
		// Grab extra CSS
		//-----------------------------------------
		
		$this->registry->output->addToDocumentHead( 'importcss', $this->settings['skin_app_url'] . 'css/ccs.css' );
		
		$_global = $this->registry->output->loadTemplate( 'cp_skin_ccsglobal' );
		
		$this->registry->output->addToDocumentHead( 'inlinecss', $_global->getCss() );
		
		//-----------------------------------------
		// What to do?
		//-----------------------------------------
		
		switch( $this->request['do'] )
		{
			case 'delete':
				$this->_deletePage();
			break;
			
			case 'recache':
				$this->recachePage();
			break;

			default:
				$this->registry->output->silentRedirect( $this->settings['base_url'] . '&module=pages&section=list' );
			break;
		}
		
		//-----------------------------------------
		// Pass to CP output hander
		//-----------------------------------------
		
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	/**
	 * Delete a page
	 *
	 * @access	protected
	 * @return	@e void
	 */
	protected function _deletePage()
	{
		if( $this->request['type'] == 'wizard' )
		{
			$id	= IPSText::md5Clean( $this->request['page'] );
			
			$this->DB->delete( 'ccs_page_wizard', "wizard_id='{$id}'" );
			
			$this->registry->output->setMessage( $this->lang->words['wsession_pdeleted'] );
		}
		else
		{
			$id	= intval($this->request['page']);
			
			$page	= $this->DB->buildAndFetch( array( 'select' => 'page_name', 'from' => 'ccs_pages', 'where' => 'page_id=' . $id ) );

			$this->DB->delete( 'ccs_pages', 'page_id=' . $id );
			$this->DB->delete( 'ccs_revisions', "revision_type='page' AND revision_type_id=" . $id );
			$this->DB->delete( 'ccs_slug_memory', "memory_type='page' AND memory_type_id=" . $id );
			
			$this->registry->adminFunctions->saveAdminLog( sprintf( $this->lang->words['ccs_adminlog_pagedeleted'], $page['page_name'] ) );

			$this->registry->output->setMessage( $this->lang->words['page_deleted'] );
		}

		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . '&module=pages&section=list' );
	}
	
	/**
	 * Recache a page
	 *
	 * @access	public
	 * @param	array		[$page]		Override page data
	 * @param	bool		[$return]	Return content instead of storing it
	 * @return	@e void
	 */
	public function recachePage( $page=array(), $return=false )
	{
		if( !count($page) )
		{
			$id		= intval($this->request['page']);
			$page	= $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'ccs_pages', 'where' => 'page_id=' . $id ) );
		}

		//-----------------------------------------
		// Get page builder
		//-----------------------------------------
		
		$classToLoad	= IPSLib::loadLibrary( IPSLib::getAppDir('ccs') . '/sources/pages.php', 'pageBuilder', 'ccs' );
		$pageBuilder	= new $classToLoad( $this->registry );
		$content		= $pageBuilder->recachePage( $page );
		
		//-----------------------------------------
		// Return or update
		//-----------------------------------------
		
		if( !$return AND $page['page_id'] )
		{
			$this->DB->update( 'ccs_pages', array( 'page_cache' => $content, 'page_cache_last' => time() ), 'page_id=' . intval($page['page_id']) );
		}
		else
		{
			return $content;
		}
		
		$this->registry->output->setMessage( $this->lang->words['page_recached'] );
		
		$this->registry->output->silentRedirectWithMessage( $this->settings['base_url'] . '&module=pages&section=list' );
	}
}
