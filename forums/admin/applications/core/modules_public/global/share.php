<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.8
 * Captcha
 * Last Updated: $Date: 2014-03-03 12:40:37 -0500 (Mon, 03 Mar 2014) $
 * </pre>
 *
 * @author 		$Author $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @subpackage	Core
 * @link		http://www.invisionpower.com
 * @since		20th February 2002
 * @version		$Rev: 12447 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class public_core_global_share extends ipsCommand
{
	/**
	 * Class entry point
	 *
	 * @param	object		Registry reference
	 * @return	@e void		[Outputs to screen/redirects]
	 */
	public function doExecute( ipsRegistry $registry ) 
	{
		/* Disabled? */
		if ( ! $this->settings['sl_enable'] )
		{
			$this->registry->output->showError( 'no_permission', 100234.1, false, null, 403 );
		}
				
		$url   = IPSText::base64_decode_urlSafe( $this->request['url'] );
		$title = IPSText::base64_decode_urlSafe( $this->request['title'] );
		$key   = trim( $this->request['key'] );

		if ( ! empty( $this->settings['ccs_root_url'] ) )
		{
			$_urls	 = explode( "\n", str_replace( "\r", '', trim( $this->settings['ccs_root_url'] ) ) );
			$_urls[] = $this->settings['_original_base_url'];
			$ok      = FALSE;
			
			foreach( $_urls as $_url )
			{
				if ( ! empty($_url) AND substr( $url, 0, strlen( $_url ) ) === $_url )
				{
					$ok = TRUE;
					break;
				}
			}
			
			if ( $ok !== TRUE ) 
			{
				$this->registry->output->showError( 'no_permission', 100234.21, false, null, 403 );
			}
		}
		else
		{
			if ( substr( $url, 0, strlen( $this->settings['_original_base_url'] ) ) !== $this->settings['_original_base_url'] ) 
			{
				$this->registry->output->showError( 'no_permission', 100234.22, false, null, 403 );
			}
		}
		
		/* Get the lib */
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/share/links.php', 'share_links' );
		$share = new $classToLoad( $registry, $key );
		
		/* Share! */
		$share->share( $title, $url );
	}
}