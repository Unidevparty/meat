<?php
/**
 * @brief		Upgrader: License
 * @author		<a href='http://ipscommunity.ru'>IPScommunity, Ltd.</a>
 * @copyright	(c) 2014 - SVN_YYYY IPScommunity, Ltd.
 * @license		http://ipscommunity.ru/license.html
 * @package		IPS Social Suite
 * @since		6 April 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\Content\ShareServices;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

class _Odnoklassniki
{
    /**
     * @brief    URL to the content item
     */
    protected $url        = NULL;
    
    /**
     * @brief    Title of the content item
     */
    protected $title    = NULL;

    /**
     * Constructor
     *
     * @param    \IPS\Http\Url    $url    URL to the content [optional - if omitted, some services will figure out on their own]
     * @param    string            $title    Default text for the content, usually the title [optional - if omitted, some services will figure out on their own]
     * @return    void
     */
    public function __construct( \IPS\Http\Url $url=NULL, $title=NULL )
    {
        $this->url        = $url;
        $this->title    = $title;
    }

	/**
	 * Determine whether the logged in user has the ability to autoshare
	 *
	 * @return	boolean
	 */
	public static function canAutoshare()
	{
		return false;
	}
    /**
     * Add any additional form elements to the configuration form. These must be setting keys that the service configuration form can save as a setting.
     *
     * @param    \IPS\Helpers\Form    $form    Configuration form for this service
	 * @param	\IPS\core\ShareLinks\Service	$service	The service
     * @return    void
     */
	public static function modifyForm( \IPS\Helpers\Form &$form, $service )
    {
    }



    /**
     * Return the HTML code to show the share link
     *
     * @return    string
     */
    public function __toString()
    {
        return '<a href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st.s=1&amp;st._surl='.urlencode( $this->url ).'" class="cShareLink cShareLink_ok" style="background-color:#F4811F;" target="_blank" data-controller="core.front.core.sharelink" title="Одноклассники" data-ipsTooltip>
               	<i class="fa fa-odnoklassniki"></i>
                </a>';
    }
}