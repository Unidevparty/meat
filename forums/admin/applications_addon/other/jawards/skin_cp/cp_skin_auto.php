<?php
class cp_skin_auto
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

	public function Header()
	{
		$IPBHTML = "";
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class="section_title">
	<h2>{$this->lang->words['ja_auto_awards']}</h2>
</div>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}

	public function main( $data )
	{
		$IPBHTML = $this->Header();
		$COREHTML = '';
		$jsAwardCat = '';
		$visibilityArray = '';
		$enabledImage	= "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' title='{$this->lang->words['ja_disable']}' />";
		$disabledImage	= "<img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' title='{$this->lang->words['ja_enable']}' />";
		if( ! is_readable( IPSLib::getAppDir('jawards') . '/auto_awarding' ) )
		{
			$IPBHTML .= $this->errorText( $this->lang->words['ja_dirwrite'] );
		}
		if( count( $data ) )
		{
			$type = '';
			foreach( $data AS $d )
			{
				if( $d['type'] != $type )
				{
					// Header break
					$type = $d['type'];
					$COREHTML .= <<<HTML
		<tr>
			<td align='left' colspan=0>
				<b>{$this->lang->words['ja_autotype']}: {$type}</b>
			</td>
		</tr>
		<tr>
			<th width='25%'>Function Title</th>
			<th width='5%'></th>
			<th width='*'>Award</th>
			<th width='5%' align='center'>{$this->lang->words['ja_enabledisable']}</th>
			<th width='10%'></th>
		</tr>
HTML;
				}
				if( $d['enabled'] )
				{
					$status = "<a href='javascript: changeStatus_funcs({$d['inst_id']});' id='funcs_enabler_{$d['inst_id']}'>" . $enabledImage . "</a>";
				}
				else
				{
					$status = "<a href='javascript: changeStatus_funcs({$d['inst_id']});' id='funcs_enabler_{$d['inst_id']}'>" . $disabledImage . "</a>";
				}
				$visibilityArray .= "funcsVisibility[{$d['inst_id']}] = {$d['enabled']};\n";
					$COREHTML .= <<<HTML
		<tr class='item ipsControlRow'>
			<td>{$d['title']}</td>
			<td align='center'><img src='{$this->settings['upload_url']}/jawards/{$d['award']['icon']}' /></td>
			<td>{$d['award']['name']}</td>
			<td align='center'>{$status}</td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}module=awards&section=auto&do=edit&id={$d['inst_id']}'>{$this->lang->words['ja_edit']}</a>
					</li>
					<li class='i_delete'>
						<a href='{$this->settings['base_url']}module=awards&section=auto&do=delete&id={$d['inst_id']}'>{$this->lang->words['ja_delete']}</a>
					</li>
				</ul>
			</td>
		</tr>
HTML;
			}
		}
		else
		{
			$COREHTML .= <<<HTML
		<tr>
			<td align='center' colspan=0>
				<em>{$this->lang->words['ja_noaafun']}</em>
			</td>
		</tr>
HTML;
		}

		$IPBHTML .= <<<HTML
<ul class='context_menu'>
	<li>
		<a href='{$this->settings['base_url']}module=awards&section=auto&do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' />{$this->lang->words['ja_addauto']}</a>
	</li>
</ul>
<script type='text/javascript'>
	var stylesURL = '{$this->settings['skin_acp_url']}';
	var enabledIMG  = "{$enabledImage}";
	var disabledIMG = "{$disabledImage}";
	{$jsAwardCat}
	var funcsVisibility = [];
	{$visibilityArray}
</script>
<script type='text/javascript' src='applications_addon/other/jawards/js/autoawarding.js'></script>
<div class='acp-box'>
	<h3>Auto-Award Functions</h3>
	<table class='ipsTable'>
		{$COREHTML}
	</table>
</div>
HTML;
		//--endhtml--//
		return $IPBHTML;
	}

	public function step1( $data )
	{
		$IPBHTML = $this->Header();
		if( count( $data ) )
		{
			$options = '';
			foreach( $data AS $d )
			{
				$options .= "<option value='{$d['name_cpu']}'>{$d['name_human']}</option>";
			}
			$IPBHTML .= <<<HTML
<form name='typeOfAutoAward' method='post' action='{$this->settings['base_url']}module=awards&section=auto&do=add&continue'>
	<div class='acp-box'>
		<h3>{$this->lang->words['ja_addaafun']}</h3>
		<table class='ipsTable'>
			<tr>
				<td width='35%'>
					<strong>{$this->lang->words['ja_addaatype']}</strong>
				</td>
				<td>
					<select name='type'>
						<option value='' disabled selected>{$this->lang->words['ja_pleasechooseone']}</option>
						{$options}
					</select>
				</td>
			</tr>
		</table>
		<div class='acp-actionbar' align='center'>
			<input type='submit' value=' {$this->lang->words['ja_continue']} ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}module=awards&section=auto'>{$this->lang->words['ja_cancel']}</a></strong>
		</div>
	</div>
</form>
HTML;
		}
		else
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td align='center' colspan=0>
				<em>{$this->lang->words['ja_noaafun']}</em>
			</td>
		</tr>
HTML;
		}

		//--endhtml--//
		return $IPBHTML;
	}

}
