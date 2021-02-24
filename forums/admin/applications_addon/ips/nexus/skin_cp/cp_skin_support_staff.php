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
 
class cp_skin_support_staff
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
function manage( $staff ) {

$menuKey = 0;

$staffCache = ipsRegistry::cache()->getCache( 'support_staff' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sstaff']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['sstaff_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['sstaff']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='4%'>&nbsp;</th>
			<th width='43%'>{$this->registry->getClass('class_localization')->words['sstaff_name']}</th>
			<th width='42%'>{$this->registry->getClass('class_localization')->words['sstaff_departments']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $staff ) )
{

foreach ( $staff as $data )
{
	$menuKey++;
	$IPBHTML .= <<<HTML
			<tr class='ipsControlRow'>
				<td><img src='{$this->settings['skin_app_url']}/images/staff/{$data['staff_type']}.png' /></td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=edit&amp;id={$data['staff_id']}&amp;type={$data['staff_type']}'>{$data['name']}</a></span></td>
				<td>
HTML;
			if ( is_array( $data['departments'] ) )
			{
				$IPBHTML .= "<ul>";
				foreach ( $data['departments'] as $d )
				{
					$IPBHTML .= "<li>{$d}</li>";
				}
				$IPBHTML .= "</ul>";
			}
			else
			{
				$IPBHTML .= <<<HTML
					<em>{$this->registry->getClass('class_localization')->words['sstaff_departments_all']}</em>
HTML;
			}
			
			if ( $data['staff_type'] == 'm' )
			{
				$reportLink = "{$this->settings['base_url']}&amp;module=reports&amp;section=supportstaff&amp;series[][]={$data['staff_id']}";
			}
			else
			{
				$reportLink = "{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=group_report&amp;id={$data['staff_id']}";
			}
			
	$IPBHTML .= <<<HTML
				</td>
				<td>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=edit&amp;id={$data['staff_id']}&amp;type={$data['staff_type']}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
						</li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
							<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
						</li>
						<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
							<li class='icon info'><a href='{$reportLink}'>{$this->registry->getClass('class_localization')->words['supportstaff_report_link']}...</a></li>
							<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=delete&amp;id={$data['staff_id']}&amp;type={$data['staff_type']}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
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
					{$this->registry->getClass('class_localization')->words['sstaff_empty']} <a href='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=add'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
				</td>
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

//===========================================================================
// Form
//===========================================================================
function addForm( $departments ) {

$form['member'] = ipsRegistry::getClass('output')->formInput( 'member', '', 'member' );
$form['group'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'group', 0 );
$form['departments'] = ipsRegistry::getClass('output')->formMultiDropdown( 'departments[]', $departments );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sstaff_add']}</h2>
</div>

<div class='acp-box'>
	<h3>Add Staff</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=save' method='post'>
	<input type='hidden' name='act' value='add' />
	
	<table class='ipsTable double_pad'>
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['sstaff_id']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstaff_member']}</strong></td>
			<td class='field_field'>
				{$form['member']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstaff_group']}</strong></td>
			<td class='field_field'>
				{$form['group']}
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['sstaff_perms']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstaff_departments']}</strong></td>
			<td class='field_field'>
				{$form['departments']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstaff_departments_desc']}</span>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
</div>

<script type='text/javascript'>
document.observe("dom:loaded", function(){
	var autoComplete = new ipb.Autocomplete( $('member'), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Edit Form
//===========================================================================
function editForm( $current, $departments ) {

$form['departments'] = ipsRegistry::getClass('output')->formMultiDropdown( 'departments[]', $departments, explode( ',', $current['staff_departments'] ) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['sstaff_edit']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['sstaff_edit']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=staff&amp;do=save' method='post'>
	<input type='hidden' name='act' value='edit' />
	<input type='hidden' name='id' value='{$current['staff_id']}' />
	<input type='hidden' name='type' value='{$current['staff_type']}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['sstaff_departments']}</strong></td>
			<td class='field_field'>
				{$form['departments']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['sstaff_departments_desc']}</span>
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