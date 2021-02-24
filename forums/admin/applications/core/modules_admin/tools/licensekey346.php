<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.3.4
 * License Manager
 * Last Updated: $LastChangedDate: 2012-06-12 10:14:49 -0400 (Tue, 12 Jun 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @subpackage	Core
 * @link		http://www.invisionpower.com
 * @version		$Rev: 10914 $
 */

if ( ! defined( 'IN_ACP' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}


class admin_core_tools_licensekey extends ipsCommand
{
	/**
	 * Main entry point
	 *
	 * @param	object		ipsRegistry reference
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry )
	{
		/* Load lang and skin */
		$this->registry->class_localization->loadLanguageFile( array( 'admin_tools' ) );
		$this->html = $this->registry->output->loadTemplate( 'cp_skin_tools' );
		
		/* URLs */
		$this->form_code    = $this->html->form_code    = 'module=tools&amp;section=licensekey';
		$this->form_code_js = $this->html->form_code_js = 'module=tools&section=licensekey';
						
		/* What to do */
		switch( $this->request['do'] )
		{
			case 'activate':
				$this->activate();
				break;
				
			case 'remove':
				IPSLib::updateSettings( array( 'ipb_reg_number' => '' ) );
				$this->settings['ipb_reg_number'] = '';
				// Deliberately no break as we'll do onto recahce and then default action
				
			case 'refresh':
				$this->recache();
				// Deliberately no break as we'll go on to the default action
		
			default:
				if ( $this->settings['ipb_reg_number'] )
				{
					$this->overview();
				}
				else
				{
					$this->activateForm();
				}
		}
		
		/* Output */
		$this->registry->output->html_main .= $this->registry->output->global_template->global_frame_wrapper();
		$this->registry->output->sendOutput();
	}

	/**
	 * Show Activation Form
	 */
	protected function activateForm( $error='' )
	{
		$this->registry->output->html .= $this->html->activateForm( $error );
	}
	
	/**
	 * Activate
	 */
	protected function activate()
	{
		/* Fetch data */
		$classToLoad = IPSLib::loadLibrary( IPS_KERNEL_PATH . 'classFileManagement.php', 'classFileManagement' );
		$classFileManagement = new $classToLoad();
		$responseIBR = $classFileManagement->getFileContents( "http://clientarea.ibresource.ru/lifo.php?api=activateKey&lk={$this->request['license_key']}&domain=".urlencode($this->settings['board_url']) );
		$responseIPS = $classFileManagement->getFileContents( "http://license.invisionpower.com/?a=activate&key={$this->request['license_key']}&url={$this->settings['board_url']}" );
		
		/* IPS Check */
		/* Is the key invalid? */
		$returnvalue='';
		if ( !$responseIPS )
		{
			$returnvalue = 'IPS: '. $this->lang->words['license_key_server_error'] ;
		}
		elseif( $responseIPS == 'NO_KEY' )
		{
			$returnvalue = 'IPS: '. $this->lang->words['license_key_notfound'] ;
		}
		elseif ( $response == 'TEST_INSTALL_ALREADY_ACTIVE' )
		{
			$returnvalue = 'IPS: '.$this->lang->words['license_key_test_in_use'] ;
		}
		else{
			$responseIPS = @json_decode( $responseIPS, true );
			if( !isset( $responseIPS['result'] ) or $responseIPS['result'] != 'ok' )
			{
				$returnvalue ='IPS: '.$this->lang->words['license_key_bad'] ;
			}
		}
		/* IBR Check */
		if ($returnvalue)
		{
			if ( !$responseIBR )
			{
				$returnvalue =$returnvalue. '</br>IBR: '.$this->lang->words['license_key_server_error'] ;
			}
			elseif( $responseIBR == 'NO_KEY' )
			{
				$returnvalue =$returnvalue. '</br>IBR: '.$this->lang->words['license_key_notfound'] ;
			}
			else
			{
				$responseIBR = @json_decode( $responseIBR, true );
				if( !isset( $responseIBR['result'] ) or $responseIBR['result'] != 'ok' )
				{
					$returnvalue =$returnvalue. '</br>IBR: '.$this->lang->words['license_key_bad'] ;
				} 
				else
				{
					$returnvalue='';
				}
			}
		}
		
		if ($returnvalue)
		{
			return $this->activateForm($returnvalue);
		}
		IPSLib::updateSettings( array( 'ipb_reg_number' => $this->request['license_key'] ) );
		$this->settings['ipb_reg_number'] = $this->request['license_key'];
		$this->recache();
		
		$this->registry->output->silentRedirect( $this->settings['base_url'] . $this->form_code );		
	}
	
	/**
	 * Overview
	 */
	protected function overview()
	{
		$this->registry->output->html .= $this->html->licenseKeyStatusScreen( substr_replace( $this->settings['ipb_reg_number'], "**********", -10 ), $this->cache->getCache( 'licenseData' ) );
	}
	
	/**
	 * Recache License Data
	 */
	public function recache()
	{
		$response='';
		/* If we don't have a key, an empty array is fine */
		if( ! $this->settings['ipb_reg_number'] )
		{
			$this->cache->setCache( 'licenseData', array(), array( 'array' => 1 ) );
			return;
		}
		
		/* Query the api */
		$classToLoad = IPSLib::loadLibrary( IPS_KERNEL_PATH . 'classFileManagement.php', 'classFileManagement' );
		$classFileManagement = new $classToLoad();
		
		
		$responseIBR = $classFileManagement->getFileContents( "http://clientarea.ibresource.ru/lifo.php?lk={$this->settings['ipb_reg_number']}&url={$this->settings['board_url']}" );
		$responseIPS = $classFileManagement->getFileContents( "http://license.invisionpower.com/?a=info&key={$this->settings['ipb_reg_number']}&url={$this->settings['board_url']}" );

		
		
		/* Is it valid? */
			if ($responseIPS == 'NO_KEY' or $responseIPS=='')
			{
				$response=$responseIBR;
			}
			else
			{
				$response=$responseIPS;
			}
			if ( $response == 'NO_KEY' or $response=='')
			{			
				//IPSLib::updateSettings( array( 'ipb_reg_number' => '' ) );
				//$this->cache->setCache( 'licenseData', array(), array( 'array' => 1 ) );
			return;
			}
		
		$responseIPS = @json_decode( $responseIPS, true );
		$responseIBR = @json_decode( $responseIBR, true );		
		$response = @json_decode( $response, true );		
	
		if ( is_array($responseIBR) and is_array($responseIPS) and !isset( $responseIBR['error'] ) and !isset( $responseIPS['error'] ) )
		{
			$response=array_merge($responseIBR,$responseIPS);		
		}
		/* Save */
		$licenseData = $response;
		$licenseData['_cached_date']	= time();
		$licenseData['key']['_expires']	= $licenseData['key']['_expires'] ? $licenseData['key']['_expires'] : 9999999999;
		$licenseData['key']['expires']	= $licenseData['key']['expires'] ? $licenseData['key']['expires'] : 9999999999;
		$this->cache->setCache( 'licenseData', $licenseData, array( 'array' => 1 ) );
			
			/* Copyright Removal? */
			if ( $licenseData['cr'] )
			{
				IPSLib::updateSettings( array( 'ipb_copy_number' => $licenseData['cr'], 'ips_cp_purchase' => 1 ) );
			}
			elseif ( $this->settings['ips_cp_purchase'] )
			{
				IPSLib::updateSettings( array( 'ips_cp_purchase' => 0 ) );
			}
	}
}