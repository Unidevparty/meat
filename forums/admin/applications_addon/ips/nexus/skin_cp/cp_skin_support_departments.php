<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support - Departments
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		4th February 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_support_departments
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
function manage( $departments ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sdpts']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['sdpt_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['sdpts']}</h3>
	<table class='ipsTable' id='supportDepartment_list'>
		<tr>
			<th width='3%'>&nbsp;</th>
			<th width='89%'>{$this->registry->getClass('class_localization')->words['sdpt_name']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $departments ) )
{
	
	foreach ( $departments as $departmentID => $data )
	{
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='departments_{$departmentID}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td width='93%'><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=edit&amp;id={$departmentID}'>{$data['dpt_name']}</a></span></td>
			<td width='3%'>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=edit&amp;id={$departmentID}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='ipsControlStrip_more ipbmenu' id='menu{$departmentID}'>
						<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
					</li>
					<ul class='acp-menu' id='menu{$departmentID}_menucontent'>
						<li class='icon info'><a href='{$this->settings['base_url']}&amp;module=reports&amp;section=supportrequests&amp;series[][]={$departmentID}'>{$this->registry->getClass('class_localization')->words['supportrequests_report_link']}...</a></li>
						<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=delete&amp;id={$departmentID}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
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
			<td colspan='3' class='no_messages'>
				{$this->registry->getClass('class_localization')->words['sdpt_empty']} <a href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=add'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
			</td>
		</tr>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#supportDepartment_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=support&section=departments&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $packageSelector ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['sdpt_add'];
	$id = 0;
	$icon = 'add';
}
else
{
	$title = $this->registry->getClass('class_localization')->words['sdpt_edit'];
	$id = $current['dpt_id'];
	$icon = 'edit';
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['dpt_name'] ) );
$form['open'] = ipsRegistry::getClass('output')->formYesNo( 'open', ( empty( $current ) ? 1 : $current['dpt_open'] ) );
$form['require_package'] = ipsRegistry::getClass('output')->formYesNo( 'require_package', ( empty( $current ) ? 0 : $current['dpt_require_package'] ) );
$form['email'] = ipsRegistry::getClass('output')->formInput( 'email', ( empty( $current ) ? '' : $current['dpt_email'] ) );
$form['notify'] = ipsRegistry::getClass('output')->formInput( 'notify', ( empty( $current ) ? '' : $current['dpt_notify'] ) );
$form['notify_reply'] = ipsRegistry::getClass('output')->formYesNo( 'notify_reply', ( empty( $current ) ? 0 : $current['dpt_notify_reply'] ) );
$form['ppi'] = ipsRegistry::getClass('output')->formSimpleInput( 'ppi', ( empty( $current ) ? '' : $current['dpt_ppi'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_name']}</strong></td>
			<td class='field_field'>
				{$form['name']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_open']}</strong></td>
			<td class='field_field'>
				{$form['open']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_assoc']}</strong></td>
			<td class='field_field'>
				{$form['require_package']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sdpt_assoc_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_packages']}</strong></td>
			<td class='field_field'>
				<select name='packages[]' multiple='multiple'>
					{$packageSelector}
				</select>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_ppi']}</strong></td>
			<td class='field_field'>
				{$form['ppi']} {$this->settings['nexus_currency']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sdpt_ppi_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_email']}</strong></td>
			<td class='field_field'>
				{$form['email']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sdpt_email_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_notify']}</strong></td>
			<td class='field_field'>
				{$form['notify']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sdpt_notify_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sdpt_notify_reply']}</strong></td>
			<td class='field_field'>
				{$form['notify_reply']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sdpt_notify_reply_desc']}</span>
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

//===========================================================================
// Delete
//===========================================================================
function delete( $current, $departments ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sdpt_delete']}</h2>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['sdpt_delete_desc']}</h3>
	<table class='ipsTable'>
HTML;

foreach ( $departments as $departmentID => $data )
{
	$IPBHTML .= <<<HTML
		<tr>
			<td width='3%'></td>
			<td><a href='{$this->settings['base_url']}&amp;module=support&amp;section=departments&amp;do=do_delete&amp;id={$current['dpt_id']}&amp;new={$departmentID}'>{$data['dpt_name']}</a></td>
		</tr>
HTML;

}

$IPBHTML .= <<<HTML
	</table>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}


}