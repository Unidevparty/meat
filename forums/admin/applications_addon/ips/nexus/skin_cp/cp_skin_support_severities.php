<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support - Severities
 * Last Updated: $Date: 2013-02-19 15:55:32 -0500 (Tue, 19 Feb 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		9th December 2010
 * @version		$Revision: 12002 $
 */
 
class cp_skin_support_severities
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
function manage( $severities ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sseverities']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=severities&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['sev_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['sseverities']}</h3>
	<table class='ipsTable' id='severity_list'>
		<tr>
			<th width='2%'>&nbsp;</th>
			<th width='2%'>&nbsp;</th>
			<th width='70%'>{$this->registry->getClass('class_localization')->words['ssev_name']}</th>
			<th width='20%'>{$this->registry->getClass('class_localization')->words['ssev_default_header']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	foreach ( $severities as $sevID => $data )
	{
		$default = '&nbsp;';
		if ( $data['sev_default'] )
		{
			$default = "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' />";
		}
		
		$icon = '&nbsp;';
		if ( $data['sev_icon'] )
		{
			$icon = "<img src='{$this->settings['skin_app_url']}/images/severities/{$data['sev_icon']}.png' />";
		}
		
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='severities_{$sevID}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td width='5%'>{$icon}</td>
			<td width='50%'><span style='color:#{$data['sev_color']}' class='larger_text'>{$data['sev_name']}</span></td>
			<td width='33%'>{$default}</th>
			<td width='8%'>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=support&amp;section=severities&amp;do=edit&amp;id={$sevID}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=severities&amp;do=delete&amp;id={$sevID}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
					</li>
				</ul>
			</td>
		</tr>
HTML;
	
	}

$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#severity_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=support&section=severities&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current=array(), $icons ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['sev_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['sev_edit'];
	$id = $current['sev_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['sev_name'] ) );
$form['color'] = ipsRegistry::getClass('output')->formInput( 'color', ( empty( $current ) ? '000000' : $current['sev_color'] ), '', 10, 'text', '', 'color' );
$form['default'] = ipsRegistry::getClass('output')->formYesNo( 'default', ( empty( $current ) ? 0 : $current['sev_default'] ) );
$form['public'] = ipsRegistry::getClass('output')->formYesNo( 'public', ( empty( $current ) ? 0 : $current['sev_public'] ) );
$form['action'] = ipsRegistry::getClass('output')->formInput( 'action', ( empty( $current ) ? '' : $current['sev_action'] ) );

$selected = ( empty( $current ) or $current['sev_icon'] == '' ) ? "checked='checked'" : '';
$more = sprintf( $this->registry->getClass('class_localization')->words['sev_icon_more'], IPSLib::getAppDir('nexus') . '/skin_cp/images/severities/' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<script type="text/javascript" src="{$this->settings['public_dir']}js/3rd_party/colorpicker/jscolor.js"></script>

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=severities&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['ssev_name']}</strong></td>
			<td class='field_field'>
				{$form['name']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sev_icon']}</strong></td>
			<td class='field_field'>
				<input type='radio' name='icon' value='' {$selected} /> {$this->registry->getClass('class_localization')->words['sev_icon_none']}
HTML;
			foreach ( $icons as $i )
			{
				$selected = ( !empty( $current ) and $current['sev_icon'] == $i ) ? "checked='checked'" : '';
				$IPBHTML .= "<br /><input type='radio' name='icon' value='{$i}' {$selected} /> <img src='{$this->settings['skin_app_url']}/images/severities/{$i}.png' />";
			}
			$IPBHTML .= <<<HTML
				<br /><span class='desctext'>{$this->registry->getClass('class_localization')->words['sev_icon_desc']}<br />{$more}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sev_color']}</strong></td>
			<td class='field_field'>
				{$form['color']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sev_color_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sev_default']}</strong></td>
			<td class='field_field'>
				{$form['default']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sev_public']}</strong></td>
			<td class='field_field'>
				{$form['public']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sev_action']}</strong><br />
				<span class='desctext'><a href='http://www.invisionpower.com/support/guides/_/advanced-and-developers/ipnexus/ipnexus-custom-actions-r55' target='_blank'>{$this->registry->getClass('class_localization')->words['package_action_desc']}</a></span>
			</td>
			<td class='field_field'>
				<strong>admin/applications_addon/ips/nexus/sources/actions/{$form['action']}.php</strong><br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_action_loc']}</span>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}


}