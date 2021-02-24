<?php
class cp_skin_root
{
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;

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

	public function errorText( $text )
	{
		if( ! $text )
		{
			return( '' );
		}
		$IPBHTML = '';
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class='warning' style='margin-bottom:15px;'>
	<h4><img src={$this->settings['skin_acp_url']}/images/icons/bullet_error.png' /> Error!</h4>
	{$text}
</div>
HTML;
		//--endhtml--//
		return $IPBHTML;
	}

	public function helpText( $text )
	{
		$IPBHTML = '';
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class='information-box' style='margin-bottom:15px;'>
	<h4 style='margin-bottom:0; padding-bottom:0;'>{$this->lang->words['ja_helppage']} <span style='font-size:10px; font-weight:100;'>(<a href='javascript: showHideInfo();'>{$this->lang->words['ja_helpshowhide']}</a>)</span></h4>
	<div id='helpDiv' style='display:none; margin-top:15px;'>
		{$text}
	</div>
</div>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}

	public function Header( $text )
	{
		$IPBHTML = "";
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class="section_title">
	<h2>{$text}</h2>
</div>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}

	public function cabinet( $data )
	{
		$IPBHTML = $this->Header( $this->lang->words['ja_cabinet_permissions'] );
		$COREHTML = '';
		//--endhtml--//
		return $IPBHTML;
	}

}
