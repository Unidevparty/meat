<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */

class cp_skin_tools extends output
{

public function __destruct()
{
}

//===========================================================================
// Overview Index
//===========================================================================
function overviewIndex( $select ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}{$this->form_code}' method='post' onsubmit="return confirm('{$this->lang->words['confirm_form']}');">
	<input type='hidden' name='do' value='prune' />
	<input type='hidden' name='_admin_auth_key' value='{$this->registry->adminFunctions->getSecurityKey()}' />
	
	<div class='acp-box'>
		<h3>{$this->lang->words['prune_logs']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['prune_logs']}</strong></td>
				<td class='field_field'><span class='desctext'>{$this->lang->words['prune_logs_desc']}</span></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['prune_logs']}' class='button primary' accesskey='s'>
		</div>
	</div>
</form>
<br />
<form action='{$this->settings['base_url']}{$this->form_code}' method='post' onsubmit="return confirm('{$this->lang->words['confirm_form']}');">
	<input type='hidden' name='do' value='orphaned' />
	<input type='hidden' name='_admin_auth_key' value='{$this->registry->adminFunctions->getSecurityKey()}' />
	
	<div class='acp-box'>
		<h3>{$this->lang->words['prune_orphaned_logs']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['prune_orphaned_logs']}</strong></td>
				<td class='field_field'><span class='desctext'>{$this->lang->words['prune_orphaned_logs_desc']}</span></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['prune_orphaned_logs']}' class='button primary' accesskey='s'>
		</div>
	</div>
</form>
<br />
<form action='{$this->settings['base_url']}{$this->form_code}' method='post' onsubmit="return confirm('{$this->lang->words['confirm_form']}');">
	<input type='hidden' name='do' value='untrack' />
	<input type='hidden' name='_admin_auth_key' value='{$this->registry->adminFunctions->getSecurityKey()}' />
	
	<div class='acp-box'>
		<h3>{$this->lang->words['stop_track_all_members']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['stop_track_all_members']}</strong></td>
				<td class='field_field'><span class='desctext'>{$this->lang->words['stop_track_all_members_desc']}</span></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['stop_track_all_members']}' class='button primary' accesskey='s'>
		</div>
	</div>
</form>
<br />
<form action='{$this->settings['base_url']}{$this->form_code}' method='post' onsubmit="return confirm('{$this->lang->words['confirm_form']}');">
	<input type='hidden' name='do' value='track' />
	<input type='hidden' name='_admin_auth_key' value='{$this->registry->adminFunctions->getSecurityKey()}' />
	
	<div class='acp-box'>
		<h3>{$this->lang->words['track_all_members_title']}</h3>


		<table class='ipsTable double_pad'>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['track_all_members']}</strong></td>
				<td class='field_field'>{$select}&nbsp;&nbsp;{$this->lang->words['track_all_members_for']}&nbsp;&nbsp;<input type='text' name='days' id='days' value='7' size='1' maxlength="2" class='input_text' />&nbsp;&nbsp;{$this->lang->words['track_all_members_days']}<br /><span class='desctext'>{$this->lang->words['track_all_members_desc']}</span></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['track_all_members_title']}' class='button primary' accesskey='s'>
		</div>
	</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

}
?>