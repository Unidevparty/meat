<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.8
 * Installer: License Key
 * Last Updated: $LastChangedDate: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @link		http://www.invisionpower.com
 * @version		$Rev: 10721 $
 *
 */


class install_license extends ipsCommand
{	
	/**
	 * Execute selected method
	 *
	 * @access	public
	 * @param	object		Registry object
	 * @return	@e void
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		$lcheck = '';
		if ( $this->request['do'] == 'check' )
		{
			$lcheck = $this->check();
			if ( $lcheck === TRUE )
			{
				$this->registry->autoLoadNextAction( 'db' );
				return;
			}
		}
	
		$this->registry->output->setTitle( "Ключ лицензии" );
		$this->registry->output->setNextAction( "license&do=check" );
		$this->registry->output->addContent( $this->registry->output->template()->page_license( $lcheck ) );
		$this->registry->output->sendOutput();
	}
	
	/**
	 * Check License Key
	 *
	 * @access	public
	 * @return	bool
	 */
	private function check()
	{
		$this->request['lkey'] = trim( $this->request['lkey'] );
		
		// License key is optional
		if( ! $this->request['lkey'] )
		{
			return true;
		}
		
		$url = IPSSetup::getSavedData( 'install_url' );
		
		require_once( IPS_KERNEL_PATH . 'classFileManagement.php' );/*noLibHook*/
		$query = new classFileManagement();
		$response = $query->getFileContents( "http://license.invisionpower.com/?a=check&key={$this->request['lkey']}&url={$url}" );
		$response = json_decode( $response, true );
		
		if( $response['result'] != 'ok' )
		{
			return "Не удалось проверить ключ лицензии. Пожалуйста проверьте указанный ключ. Если проблема не решается указанием правильного ключа, пожалуйста свяжитесь со службой поддержки.";
		}
		else
		{
			IPSSetup::setSavedData( 'lkey', $this->request['lkey'] );
			return TRUE;
		}
						
	}
}