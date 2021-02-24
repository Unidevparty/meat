<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support - Stock Actions
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
 
class cp_skin_support_stockactions
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
function manage( $actions ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['stockactions']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['stockaction_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['stockactions']}</h3>
	<table class='ipsTable' id='stockActions_list'>
		<tr>
			<th width='4%'>&nbsp;</th>
			<th width='88%'>{$this->registry->getClass('class_localization')->words['stockaction_name']}</th>
			<th width='8%'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $actions ) )
{
	
	foreach ( $actions as $actionID => $data )
	{
		$menuKey++;
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='actions_{$actionID}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td width='88%'><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=edit&amp;id={$actionID}'>{$data['action_name']}</a></span></td>
			<td width='8%'>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=edit&amp;id={$actionID}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=delete&amp;id={$actionID}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
					</li>
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
				{$this->registry->getClass('class_localization')->words['stockaction_empty']} <a href='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
			</td>
		</td>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#stockActions_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=support&section=stockactions&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $departments, $statuses, $staff ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['stockaction_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['stockaction_edit'];
	$id = $current['action_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['action_name'] ) );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( empty( $current ) ? '' : $current['action_message'] );
$form['message'] = $editor->show('message');

$form['department'] = ipsRegistry::getClass('output')->formDropdown( 'department', array_merge( array( array( -1, $this->registry->getClass('class_localization')->words['stockaction_nochange'] ) ), $departments ), ( empty( $current ) ? '' : $current['action_department'] ) );
$form['status'] = ipsRegistry::getClass('output')->formDropdown( 'status', array_merge( array( array( -1, $this->registry->getClass('class_localization')->words['stockaction_nochange'] ) ), $statuses ), ( empty( $current ) ? '' : $current['action_status'] ) );
$form['staff'] = ipsRegistry::getClass('output')->formDropdown( 'staff', array_merge( array( array( -1, $this->registry->getClass('class_localization')->words['stockaction_nochange'] ), array( 0, $this->registry->getClass('class_localization')->words['stockaction_unassign'] ) ), $staff ), ( empty( $current ) ? '' : $current['action_staff'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=stockactions&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['stockaction_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['stockaction_message']}</strong></td>
			<td class='field_field'>{$form['message']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['stockaction_department']}</strong></td>
			<td class='field_field'>{$form['department']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['stockaction_status']}</strong></td>
			<td class='field_field'>{$form['status']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['stockaction_owner']}</strong></td>
			<td class='field_field'>{$form['staff']}</td>
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