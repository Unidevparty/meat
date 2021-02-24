<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - IP.Subscriptions Conversion
 * Last Updated: $Date: 2011-05-05 07:03:47 -0400 (Thu, 05 May 2011) $
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		1st April 2010
 * @version		$Revision: 8644 $
 */
 
class cp_skin_convert
{

	/**
	 * Constructor
	 *
	 * @param	object		$registry		Registry object
	 * @return	@e void
	 */
	public function __construct( ipsRegistry $registry )
	{
		$this->registry 	= $registry;
		$this->DB	    	= $this->registry->DB();
		$this->settings		=& $this->registry->fetchSettings();
		$this->request		=& $this->registry->fetchRequest();
		$this->member   	= $this->registry->member();
		$this->memberData	=& $this->registry->member()->fetchMemberData();
		$this->cache		= $this->registry->cache();
		$this->caches		=& $this->registry->cache()->fetchCaches();
		$this->lang 		= $this->registry->class_localization;
	}

//===========================================================================
// Splash
//===========================================================================
function splash() {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['convert']}</h2>
</div>

HTML;

return $IPBHTML;

}

//===========================================================================
// Confirm
//===========================================================================
function confirm() {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['convert']}</h2>
</div>

<div class='information-box'>
	{$this->registry->getClass('class_localization')->words['convert_blurb']}
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}module=convert&section=convert'>{$this->registry->getClass('class_localization')->words['convert_yes']}</a></div>	
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}module=convert&section=splash&do=skip'>{$this->registry->getClass('class_localization')->words['convert_no']}</a></div>	
</div>


HTML;

return $IPBHTML;

}


}