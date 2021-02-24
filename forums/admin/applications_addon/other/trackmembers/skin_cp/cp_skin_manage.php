<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */

class cp_skin_manage extends output
{

public function __destruct()
{
}

//===========================================================================
// Overview Index
//===========================================================================
function overviewIndex( $members, $total, $links, $cnt ) {

$IPBHTML = "";

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['stats_visible']}</h2>
</div>
<table class='ipsTable'>
	<tr>
	    <td width='50%' valign='top'>
	        <div class="acp-box">
			    <h3>{$cnt} {$this->lang->words['stats_visible']}</h3>
				<table class='ipsTable double_pad'>
					<tr>
						<th width='20%'>{$this->lang->words['manage_name']}</th>
						<th width='15%'>{$this->lang->words['manage_group']}</th>
						<th width='15%'>{$this->lang->words['manage_entries']}</th>
						<th width='23%'>{$this->lang->words['manage_managedeadline']}</th>
						<th width='17%'>{$this->lang->words['manage_joined']}</th>
					</tr>
HTML;
					if( is_array( $members ) AND count( $members ) )
					{
						foreach( $members as $r )
						{
							$cnt = $total[ $r['member_id'] ] > 0 ? $total[ $r['member_id'] ] : 0;
							//$total[ $r['member_id'] ] > 0 ? $total[ $r['member_id'] ] : 0;
							$date = $r['member_tracked_deadline'] > 0 ? $this->registry->class_localization->getDate( $r['member_tracked_deadline'], 'SHORT' ) : $this->lang->words['indefinitely'];

$IPBHTML .= <<<HTML
							<tr>
								<td width='20%'><a href='{$this->settings['base_url']}{$this->form_code}&amp;do=view&amp;mid={$r['member_id']}'>{$r['members_display_name']}</a></td>
								<td width='15%'>{$this->caches['group_cache'][ $r['member_group_id'] ]['prefix']}{$this->caches['group_cache'][ $r['member_group_id'] ]['g_title']}{$this->caches['group_cache'][ $r['member_group_id'] ]['suffix']}</td>
								<td width='15%' align='center'><a href='{$this->settings['base_url']}{$this->form_code}&amp;do=view&amp;mid={$r['member_id']}'>{$cnt}</a></td>
								<td width='23%'>{$date}</td>
								<td width='17%'>{$this->registry->class_localization->getDate( $r['joined'], 'JOINED' )}</td>
							</tr>
HTML;
						}
					}
					else
					{
$IPBHTML .= <<<HTML
						<tr>
							<td colspan="5" align="center"><em>{$this->lang->words['manage_notracked']}</em></center>
						</td>
HTML;
					}
$IPBHTML .= <<<HTML
				</table>
			</div>
		</td>
	</tr>
</table>
{$links}
HTML;


return $IPBHTML;
}

function viewLogs( $logs, $nr_logs, $links ) {
$IPBHTML  = "";

$text	= '' ;
$member = IPSMember::load( $this->request['mid'], 'core' );

if ( $member['member_tracked'] AND $member['member_tracked_deadline'] )
{
	$text = " (tracked until {$this->registry->class_localization->getDate( $member['member_tracked_deadline'], 'LONG')})";
}

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$member['members_display_name']}: {$nr_logs} {$this->lang->words['manage_logsof']}{$text}</h2>
	<ul class='context_menu'>
		<li>
			<a href='{$this->settings['base_url']}{$this->form_code}&amp;do=prune&amp;mid={$this->request['mid']}' onclick="return confirm('{$this->lang->words['manage_prune_confirm']}');">
				<img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' />
				{$this->lang->words['prune_logs']}
			</a>
		</li>
		<li>
			<a href='{$this->settings['base_url']}{$this->form_code}&amp;do=stop&amp;mid={$this->request['mid']}' onclick="return confirm('{$this->lang->words['manage_untrack_confirm']}');">
				<img src='{$this->settings['skin_acp_url']}/images/icons/exclamation.png' alt='' />
				{$this->lang->words['manage_stoptracking']}
			</a>
		</li>
	</ul>
</div>
<table class='ipsTable'>
	<tr>
	    <td width='50%' valign='top'>
	        <div class="acp-box">
			    <h3>{$this->lang->words['manage_logentries']}</h3>
				<table class='ipsTable double_pad'>
					<tr>
						<th width='25%'>{$this->lang->words['manage_name']}</th>
						<th width='40%'>{$this->lang->words['manage_description']}</th>
						<th width='25%'>{$this->lang->words['manage_action']}</th>
						<th width='10%'>{$this->lang->words['ip_address']}</th>
					</tr>
HTML;
					if( is_array( $logs ) AND count( $logs ) )
					{
						foreach( $logs as $i => $log )
						{
						 	$log_ip = $log['ip_address'] ? $log['ip_address'] : '---';
$IPBHTML .= <<<HTML
							<tr>
								<td>{$this->registry->class_localization->getDate( $log['date'], 'LONG')}</td>
								<td>{$log['description']}</td>
								<td>{$log['app']}</td>
								<td align='center'>{$log_ip}</td>
							</tr>
HTML;
						}
					}
					else
					{
$IPBHTML .= <<<HTML
						<tr>
							<td colspan='3'>{$this->lang->words['manage_nologentry']}</td>
						</tr>
HTML;
					}
$IPBHTML .= <<<HTML
				</table>
			</div>
		</td>
	</tr>
</table>
{$links}
HTML;
return $IPBHTML;
}

}
?>