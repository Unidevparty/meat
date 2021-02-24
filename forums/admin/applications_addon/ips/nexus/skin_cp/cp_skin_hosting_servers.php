<?php
/**
 * @file		cp_skin_hosting_servers.php		Hosting Servers Management View
 *
 * $Copyright: $
 * $License: $
 * $Author: ips_terabyte $
 * $LastChangedDate: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * $Revision: 9800 $
 * @since 		18th January 2011
 */

/**
 *
 * @class	cp_skin_hosting_servers
 * @brief	Hosting Servers Management View
 *
 */
class cp_skin_hosting_servers
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
// Manage
//===========================================================================
function manage( $servers, $queues, $counts, $types ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['hservs']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='add_server'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['hserv_add']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=audit'><img src='{$this->settings['skin_app_url']}/images/hosting/audit.png' alt='' /> {$this->lang->words['hosting_audit_all']}</a></li>
		</ul>
	</div>
</div>

<ul class='ipbmenu_content' id='add_server_menucontent' style='display: none'>
HTML;

foreach ( $types as $type => $name )
{
	$IPBHTML .= <<<HTML
	<li><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=add&amp;type={$type}' style='text-decoration: none' >{$name}</a></li>
HTML;
}

$IPBHTML .= <<<HTML
</ul>


<div class='acp-box'>
	<h3>{$this->lang->words['hservs']}</h3>
	<div class='ipsTabBar with_left with_right' id='tabstrip_servers'>
		<span class='tab_left'>&laquo;</span>
		<span class='tab_right'>&raquo;</span>
		<ul>
HTML;
			foreach ( $servers as $qid => $_servers )
			{
				$name = ( !$qid ) ? $this->lang->words['hserv_all_servers'] : $queues[ $qid ]['queue_name'];
			
				$IPBHTML .= <<<HTML
				<li id='tab_{$qid}'>{$name}</li>
HTML;
			}
			
			$IPBHTML .= <<<HTML
		</ul>
	</div>
	<div class='ipsTabBar_content' id='tabstrip_servers_content'>
HTML;
	
	if ( !empty( $servers ) )
	{
		foreach ( $servers as $qid => $_servers )
		{
			$IPBHTML .= <<<HTML
		<div id='tab_{$qid}_content'>
			<table class='ipsTable'>
				<tr>
					<th width='4%'>&nbsp;</th>
					<th width='58%'>{$this->lang->words['hserv_name']}</th>
					<th width='15%'>{$this->lang->words['hserv_accounts']}</th>
					<th width='10%'>{$this->lang->words['hserv_cost']}</th>
					<th width='15%'>{$this->lang->words['hserv_profit']} <span class='desctext'>*</span></th>
					<th width='8%' class='col_buttons'>&nbsp;</th>
				</tr>
HTML;
			if ( !empty( $_servers ) )
			{
				foreach ( $_servers as $data )
				{
					$icon = '';
					if ( $this->settings['monitoring_script'] )
					{
						if ( !$data['server_monitor'] )
						{
							$icon = "<img src='{$this->settings['skin_app_url']}/images/hosting/monitor-unknown.png' title='{$this->lang->words['monitoring_off']}' />";
						}
						elseif ( $data['server_monitor_version'] and $data['server_monitor_version'] != 1000 )
						{
							$icon = "<img src='{$this->settings['skin_app_url']}/images/hosting/monitor-info.png' title='{$this->lang->words['monitoring_version']}' />";
						}
						elseif ( $data['server_monitor_fails'] )
						{
							$error = sprintf( $this->lang->words['monitoring_error'], $data['server_monitor_fails'] );
							$icon = "<img src='{$this->settings['skin_app_url']}/images/hosting/monitor-warning.png' title='{$error}' />";
						}
						else
						{
							$icon = "<img src='{$this->settings['skin_app_url']}/images/hosting/monitor-ok.png' title='{$this->lang->words['monitoring_ok']}' />";
						}
					}
				
					if ( $data['server_dedicated'] )
					{
						$accounts = "<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$data['server_dedicated']}'>{$this->lang->words['dedicated_server']}</a>";
					}
					else
					{
						$accounts = intval( $counts['accounts'][ $data['server_id'] ] ) . ' / ' . $data['server_max_accounts'];
					}
					$cost = $this->lang->formatMoney( $data['server_cost'] );
					$profitloss = $this->lang->formatMoney( $counts['income'][ $data['server_id'] ] - $data['server_cost'] );
					
					$name = ( $data['server_type'] == 'none' ) ? $data['server_hostname'] : "<a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=view&amp;id={$data['server_id']}'>{$data['server_hostname']}</a>";
					
					$IPBHTML .= <<<HTML
						<tr class='ipsControlRow'>
							<td style='text-align: center'>{$icon}</td>
							<td><span class='larger_text'>{$name}</span></td>
							<td>{$accounts}</td>
							<td>{$cost}</td>
							<td>{$profitloss}</td>
							<td>
								<ul class='ipsControlStrip'>
									<li class='i_edit'>
										<a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=edit&amp;id={$data['server_id']}'>{$this->lang->words['edit']}</a>
									</li>
									<li class='ipsControlStrip_more ipbmenu' id='menu{$data['server_id']}'>
										<a href='#'>{$this->lang->words['options']}</a>
									</li>
									<ul class='acp-menu' id='menu{$data['server_id']}_menucontent'>
										<li class='icon info'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=view&amp;id={$data['server_id']}'>{$this->lang->words['hserv_list_accounts']}...</a></li>
										<li class='icon refresh'><a onclick="if ( !confirm('{$this->lang->words['hserv_reboot_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=reboot&amp;id={$data['server_id']}'>{$this->lang->words['hserv_reboot']}...</a></li>
										<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=delete&amp;id={$data['server_id']}'>{$this->lang->words['delete']}...</a></li>
									</ul>
								</ul>
							</td>
						</tr>
HTML;
			
				}
			}
			else
			{
				$IPBHTML .= <<<HTML
					<tr>
						<td colspan='4' class='no_messages'>
							{$this->lang->words['hserv_empty']}
						</td>
					</tr>
HTML;
			}
			
		$IPBHTML .= <<<HTML
			</table>
		</div>
HTML;
		
		}
			
	}
	
	$IPBHTML .= <<<HTML
	</div>
</div>

<br />
<span class='desctext'>* {$this->lang->words['hosting_profitloss_desc']}</span>

<script type='text/javascript'>
     jQ("#tabstrip_servers").ipsTabBar({ tabWrap: "#tabstrip_servers_content" });
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $type, $access, $queues ) {

if ( empty( $current ) )
{
	$title = $this->lang->words['hserv_add'];
	$id = 0;
	$icon = 'add';
}
else
{
	$title = $this->lang->words['hserv_edit'];
	$id = $current['server_id'];
	$icon = 'edit';
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['server_hostname'] ) );
$form['username'] = ipsRegistry::getClass('output')->formInput( 'username', ( empty( $current ) ? 'root' : $current['server_username'] ) );
$form['ip'] = ipsRegistry::getClass('output')->formInput( 'ip', ( empty( $current ) ? '' : $current['server_ip'] ) );
$form['queues'] = ipsRegistry::getClass('output')->formMultiDropdown( 'queues[]', $queues, empty( $current ) ? NULL : explode( ',', $current['server_queues'] ) );
$form['max_accounts'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_accounts', ( empty( $current ) ? 0 : $current['server_max_accounts'] ) );
$form['nameservers'] = ipsRegistry::getClass('output')->formTextArea( 'nameservers', ( empty( $current ) ? '' : str_replace( '<br />', "\n", $current['server_nameservers'] ) ) );
$form['cost'] = ipsRegistry::getClass('output')->formInput( 'cost', ( empty( $current ) ? '' : $current['server_cost'] ) );
$form['monitor'] = ipsRegistry::getClass('output')->formInput( 'monitor', ( empty( $current ) ? '' : $current['server_monitor'] ) );

$defaultNameServers = nl2br( $this->settings['nexus_hosting_nameservers'] );

$costDesc = sprintf( $this->lang->words['hserv_cost_desc'], $this->settings['nexus_currency'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<input type='hidden' name='type' value='{$type}' />
	<input type='hidden' name='original_name' value='{$current['server_hostname']}' />
	<input type='hidden' name='original_ip' value='{$current['server_ip']}' />
	<input type='hidden' name='original_username' value='{$current['server_username']}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_ip']}</strong></td>
			<td class='field_field'>{$form['ip']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_username']}</strong></td>
			<td class='field_field'>{$form['username']}</td>
		</tr>
		{$access}
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_queues']}</strong></td>
			<td class='field_field'>{$form['queues']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_max_accounts']}</strong></td>
			<td class='field_field'>{$form['max_accounts']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_nameservers']}</strong></td>
			<td class='field_field'>
				{$form['nameservers']}<br />
				<span class='desctext'>{$this->lang->words['hserv_nameservers_desc']}<br />{$defaultNameServers}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_cost']}</strong></td>
			<td class='field_field'>
				{$form['cost']}<br />
				<span class='desctext'>{$costDesc}</span>
			</td>
		</tr>
HTML;
	if ( $this->settings['monitoring_script'] )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_monitor_script']}</strong></td>
			<td class='field_field'>{$form['monitor']}</td>
		</tr>
HTML;
	}
	$IPBHTML .= <<<HTML
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['save']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Delete
//===========================================================================
function delete( $current, $servers ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['hserv_delete']}</h2>
</div>

<div class='warning'>
	{$this->lang->words['hserv_delete_explain']}
</div>
<br /><br />

<div class='acp-box'>
 	<h3>{$this->lang->words['hserv_delete_desc']}</h3>
	<div>
		<table class='ipsTable'>
HTML;

foreach ( $servers as $serverID => $data )
{
	$IPBHTML .= <<<HTML
		<tr>
			<td width='3%'><img src='{$this->settings['skin_app_url']}/images/nexus_icons/hosting.png' /></td>
			<td><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=servers&amp;do=do_delete&amp;id={$current['server_id']}&amp;new={$serverID}'>{$data['server_hostname']}</a></td>
		</tr>
HTML;

}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Audit
//===========================================================================
function view( $serverData, $notPresentOnServer, $notPresentInDb, $domainsDontMatch, $diskSpaceAllocated, $diskSpaceInUse, $expiredButnotSuspended, $suspendedButNotExpired, $accounts ) {
$IPBHTML = "";
//--starthtml--//

$warnings = array();

if ( $diskSpaceAllocated == -1 )
{
	$space = sprintf( $this->lang->words['hserv_diskspace_audit_unlimited'], $diskSpaceInUse );
}
else
{
	$space = sprintf( $this->lang->words['hserv_diskspace_audit'], $diskSpaceAllocated, $diskSpaceInUse );
}

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$serverData['server_hostname']}</h2>
</div>

<div class='information-box'>{$space}</div>
<br />
HTML;

if ( empty( $notPresentOnServer ) and empty( $notPresentInDb ) and empty( $domainsDontMatch ) and empty( $expiredButnotSuspended ) and empty( $suspendedButNotExpired ) )
{
$IPBHTML .= <<<HTML
<div class='information-box'>{$this->lang->words['hserv_audit_ok']}</div>
<br />
HTML;
}
else
{
	if ( !empty( $notPresentOnServer ) )
	{
$IPBHTML .= <<<HTML
<div class='warning'>
	{$this->lang->words['hserv_audit_notonserver']}<br />
	<ul>
HTML;
		foreach ( $notPresentOnServer as $a )
		{
			$warnings[] = $a['account_username'];
			$IPBHTML .= <<<HTML
		<li><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$a['ps_id']}'>{$a['account_username']}</a></li>
HTML;
		}
	$IPBHTML .= <<<HTML
	</ul>
</div>
<br /><br />
HTML;
	}
	
	if ( !empty( $notPresentInDb ) )
	{
$IPBHTML .= <<<HTML
<div class='warning'>
	{$this->lang->words['hserv_audit_notindb']}<br />
	<ul>
HTML;
		foreach ( $notPresentInDb as $username => $a )
		{
			$active = $a['active'] ? 'ACTIVE' : 'SUSPENDED';
			$warnings[] = $username;
			
			$match = ( gethostbyname( $a['domain'] ) == $serverData['server_ip'] ) ? '' : " <strong>{$this->lang->words['err_hosting_noresolve']}</strong>";
			
			$IPBHTML .= <<<HTML
		<li>{$username} ({$a['domain']}{$match}) - {$active}</li>
HTML;
		}
	$IPBHTML .= <<<HTML
	</ul>
</div>
<br /><br />
HTML;
	}
	
	if ( !empty( $domainsDontMatch ) )
	{
$IPBHTML .= <<<HTML
<div class='warning'>
	{$this->lang->words['hserv_audit_domain']}<br />
	<ul>
HTML;
		foreach ( $domainsDontMatch as $a )
		{
			$warnings[] = $a['account_username'];
			$IPBHTML .= <<<HTML
		<li><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$a['ps_id']}'>{$a['account_username']}</a> ({$this->lang->words['database']}: {$a['account_domain']}, {$this->lang->words['server']}: {$a['domain']})</li>
HTML;
		}
	$IPBHTML .= <<<HTML
	</ul>
</div>
<br /><br />
HTML;
	}
	
	if ( !empty( $expiredButnotSuspended ) )
	{
$IPBHTML .= <<<HTML
<div class='warning'>
	{$this->lang->words['hserv_audit_not_suspended']}<br />
	<ul>
HTML;
		foreach ( $expiredButnotSuspended as $a )
		{
			$warnings[] = $a['account_username'];
			$IPBHTML .= <<<HTML
		<li><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$a['ps_id']}'>{$a['account_username']}</a></li>
HTML;
		}
	$IPBHTML .= <<<HTML
	</ul>
</div>
<br /><br />
HTML;
	}
	
	if ( !empty( $suspendedButNotExpired ) )
	{
$IPBHTML .= <<<HTML
<div class='warning'>
	{$this->lang->words['hserv_audit_suspended']}<br />
	<ul>
HTML;
		foreach ( $suspendedButNotExpired as $a )
		{
			$warnings[] = $a['account_username'];
			$IPBHTML .= <<<HTML
		<li><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$a['ps_id']}'>{$a['account_username']}</a></li>
HTML;
		}
	$IPBHTML .= <<<HTML
	</ul>
</div>
<br /><br />
HTML;
	}
}

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->lang->words['hserv_accounts']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='2%'>&nbsp;</th>
				<th width='26%'>{$this->lang->words['haccount_username']}</th>
				<th width='38%'>{$this->lang->words['haccount_domain']}</th>
				<th width='15%'>{$this->lang->words['haccount_disklimit']}</th>
				<th width='15%'>{$this->lang->words['haccount_diskused']}</th>
				<th width='4%'>&nbsp;</th>
			</tr>
HTML;

	foreach ( $accounts as $data )
	{
		$icon = in_array( $data['account_username'], $warnings ) ? 'warning.png' : ( $data['active'] ? 'active.png' : 'suspended.png' );
		
		$IPBHTML .= <<<HTML
				<tr>
					<td><img src='{$this->settings['skin_app_url']}/images/hosting/{$icon}' /></td>
					<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$data['ps_id']}'>{$data['account_username']}</a></span></td>
					<td><a href='http://{$data['account_domain']}' target='_blank'>{$data['account_domain']}</a></td>
					<td>{$data['disklimit']}</td>
					<td>{$data['diskused']}</td>
					<td>&nbsp;</td>
				</tr>
HTML;
	}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Search
//===========================================================================
function search( $accounts, $pagination ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['search']}</h2>
</div>

<div class='acp-box'>
 	<h3>{$this->lang->words['hserv_accounts']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='2%'>&nbsp;</th>
				<th width='26%'>{$this->lang->words['haccount_username']}</th>
				<th width='38%'>{$this->lang->words['haccount_domain']}</th>
				<th width='4%'>&nbsp;</th>
			</tr>
HTML;

	foreach ( $accounts as $data )
	{
		$IPBHTML .= <<<HTML
				<tr>
					<td>&nbsp;</td>
					<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=payments&section=purchases&id={$data['ps_id']}'>{$data['account_username']}</a></span></td>
					<td><a href='http://{$data['account_domain']}' target='_blank'>{$data['account_domain']}</a></td>
					<td>&nbsp;</td>
				</tr>
HTML;
	}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>
{$pagination}
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Terminate Explaination
//===========================================================================
function terminate( $purchase ) {

$title = sprintf( $this->lang->words['hosting_terminate_title'], $purchase['ps_name'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$title}</h2>
</div>
<div class='information-box'>
	{$this->lang->words['hosting_terminate_blurb']}
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do={$this->request['do']}&amp;id={$purchase['ps_id']}&amp;v={$this->request['v']}&amp;terminate=1'>{$this->lang->words['hosting_terminate_yes']}</a></div>	
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do={$this->request['do']}&amp;id={$purchase['ps_id']}&amp;v={$this->request['v']}&amp;terminate=2'>{$this->lang->words['hosting_terminate_suspend']}</a></div>	
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do={$this->request['do']}&amp;id={$purchase['ps_id']}&amp;v={$this->request['v']}&amp;terminate=0'>{$this->lang->words['hosting_terminate_no']}</a></div>	
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}


}