<?php

/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.5
 * Template Pluging: CCS menu highlighting
 * Last Updated: $Date: 2011-05-05 07:03:47 -0400 (Thu, 05 May 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Content
 * @link		http://www.invisionpower.com
 * @version		$Rev: 8644 $
 */

/**
* Main loader class
*/
class tp_menu_highlight extends output implements interfaceTemplatePlugins
{
	/**
	 * Prevent our main destructor being called by this class
	 *
	 * @access	public
	 * @return	@e void
	 */
	public function __destruct()
	{
	}
	
	/**
	 * Run the plug-in
	 *
	 * @access	public
	 * @author	Brandon Farber
	 * @param	string	The initial data from the tag
	 * @param	array	Array of options
	 * @return	string	Processed HTML
	 */
	public function runPlugin( $data, $options )
	{
		//-----------------------------------------
		// Process the tag and return the data
		//-----------------------------------------
		
		$return		= '';
		$onClass	= isset($options['onclass']) ? $options['onclass'] : "menu-on";
		$offClass	= isset($options['offclass']) ? $options['offclass'] : "menu-off";

		if( !$data )
		{
			return;	
		}
		
		//-----------------------------------------
		// Normalize URL - relative path
		//-----------------------------------------
		
		$data			= preg_replace( "/http:\/\/(.+?)\//i", "/", $data );
		
		$_ifCondition	= 'strpos( $_SERVER[\'REQUEST_URI\'], "' . $data . '" ) !== false';
		$_phpCode		= "\" . (( {$_ifCondition} ) ? '{$onClass}' : '{$offClass}' ) . \"";

		//-----------------------------------------
		// Process the tag and return the data
		//-----------------------------------------

		return $_phpCode ? $_phpCode : '';
	}
	
	/**
	 * Return information about this modifier
	 *
	 * It MUST contain an array  of available options in 'options'. If there are no allowed options, then use an empty array.
	 * Failure to keep this up to date will most likely break your template tag.
	 *
	 * @access	public
	 * @author	Brandon Farber
	 * @return	array
	 */
	public function getPluginInfo()
	{
		//-----------------------------------------
		// Return the data, it's that simple...
		//-----------------------------------------
		
		return array( 'name'    => 'menu_highlight',
					  'author'  => 'Invision Power Services, Inc.',
					  'usage'   => '{parse menu_highlight="http://example.com/folder/subfolder/file.html" onclass="menu-on" offclass="menu-off"}',
					  'options' => array( 'onclass', 'offclass' ) );
	}
}