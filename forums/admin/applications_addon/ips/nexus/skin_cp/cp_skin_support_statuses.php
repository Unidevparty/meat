<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support - Statuses
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		7th February 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_support_statuses
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
function manage( $statuses ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sstatuses']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=statuses&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['sstatus_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['sstatuses']}</h3>
	<table class='ipsTable' id='status_list'>
		<tr>
			<th width='4%'>&nbsp;</th>
			<th width='30%'>{$this->registry->getClass('class_localization')->words['sstatus_internal']}</th>
			<th width='31%'>{$this->registry->getClass('class_localization')->words['sstatus_public']}</th>
			<th width='23%'>{$this->registry->getClass('class_localization')->words['sstatus_defaults']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	foreach ( $statuses as $statusID => $data )
	{
		$defaults = '';
		if ( $data['status_default_member'] )
		{
			$defaults .= "<img src='{$this->settings['skin_app_url']}/images/statuses/default_member.png' title='{$this->registry->getClass('class_localization')->words['sstatus_default_member']}' />";
		}
		if ( $data['status_default_staff'] )
		{
			$defaults .= "<img src='{$this->settings['skin_app_url']}/images/statuses/default_staff.png' title='{$this->registry->getClass('class_localization')->words['sstatus_default_staff']}' />";
		}
		
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='statuses_{$statusID}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td width='30%'><span style='color:#{$data['status_color']}' class='larger_text'>{$data['status_name']}</span></th>
			<td width='31%'>{$data['status_public_name']}</th>
			<td width='23%'>{$defaults}</th>
			<td width='8%'>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=support&amp;section=statuses&amp;do=edit&amp;id={$statusID}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=statuses&amp;do=delete&amp;id={$statusID}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
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
	jQ("#status_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=support&section=statuses&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['sstatus_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['sstatus_edit'];
	$id = $current['status_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['status_name'] ) );
$form['public_name'] = ipsRegistry::getClass('output')->formInput( 'public_name', ( empty( $current ) ? '' : $current['status_public_name'] ) );
$form['public_set'] = ipsRegistry::getClass('output')->formInput( 'public_set', ( empty( $current ) ? '' : $current['status_public_set'] ) );
$form['default_member'] = ipsRegistry::getClass('output')->formYesNo( 'default_member', ( empty( $current ) ? 0 : $current['status_default_member'] ) );
$form['default_staff'] = ipsRegistry::getClass('output')->formYesNo( 'default_staff', ( empty( $current ) ? 0 : $current['status_default_staff'] ) );
$form['is_locked'] = ipsRegistry::getClass('output')->formYesNo( 'is_locked', ( empty( $current ) ? 0 : $current['status_is_locked'] ) );
$form['assign'] = ipsRegistry::getClass('output')->formYesNo( 'assign', ( empty( $current ) ? 0 : $current['status_assign'] ) );
$form['open'] = ipsRegistry::getClass('output')->formYesNo( 'open', ( empty( $current ) ? 1 : $current['status_open'] ) );
$form['color'] = ipsRegistry::getClass('output')->formInput( 'color', ( empty( $current ) ? '000000' : $current['status_color'] ), '', 10, 'text', '', 'color' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<script type="text/javascript" src="{$this->settings['public_dir']}js/3rd_party/colorpicker/jscolor.js"></script>

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=statuses&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_internal']}</strong></td>
			<td class='field_field'>
				{$form['name']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_internal_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_public']}</strong></td>
			<td class='field_field'>
				{$form['public_name']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_public_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_set']}</strong></td>
			<td class='field_field'>
				{$form['public_set']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_set_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_default_member_set']}</strong></td>
			<td class='field_field'>
				{$form['default_member']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_default_member_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_default_staff_set']}</strong></td>
			<td class='field_field'>
				{$form['default_staff']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_default_staff_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_open']}</strong></td>
			<td class='field_field'>
				{$form['open']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_locked']}</strong></td>
			<td class='field_field'>
				{$form['is_locked']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_locked_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_assign']}</strong></td>
			<td class='field_field'>
				{$form['assign']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_assign_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstatus_color']}</strong></td>
			<td class='field_field'>
				{$form['color']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstatus_color_desc']}</span>
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