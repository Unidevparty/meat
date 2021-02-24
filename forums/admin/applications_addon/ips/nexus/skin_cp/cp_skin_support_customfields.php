<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support - Custom Support Fields
 * Last Updated: $Date: 2012-05-11 05:59:51 -0400 (Fri, 11 May 2012) $
 * </pre>
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	© 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/community/board/license.html
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		10th May 2012
 * @version		$Revision: 10723 $
 */
 
class cp_skin_support_customfields
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
function manage( $fields ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['scfields']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['cfield_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->lang->words['cfields']}</h3>
	<div>
		<table class='ipsTable' id='customFields_list'>
			<tr>
				<th width='3%'>&nbsp;</th>
				<th width='3%'>&nbsp;</th>
				<th width='91%'>{$this->lang->words['cfield_name']}</th>
				<th width='3%' class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

if ( !empty( $fields ) )
{
	
	foreach ( $fields as $fieldID => $data )
	{
		$IPBHTML .= <<<HTML
			<tr class='ipsControlRow isDraggable' id='fields_{$fieldID}'>
				<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
				<td style='width: 3%; text-align: center'>
					<img src='{$this->settings['skin_app_url']}/images/fields/{$data['sf_type']}.png' alt='{$data['sf_type']}' />
				</td>
				<td width='93%'>
					<span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=edit&amp;id={$fieldID}'>{$data['sf_name']}</a></span>
HTML;
		if ( $data['sf_type'] == 'dropdown' )
		{
			$options = str_replace( '<br />', ', ', $data['sf_extra'] );
			$IPBHTML .= <<<HTML
				<br /><span class='desctext'>{$options}</span>
HTML;
		}
		$IPBHTML .= <<<HTML
				</td>
				<td width='3%'>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=edit&amp;id={$fieldID}'>{$this->lang->words['edit']}…</a>
						</li>
						<li class='i_delete'>
							<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=delete&amp;id={$fieldID}'>{$this->lang->words['delete']}…</a>
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
				<td colspan='8' class='no_messages'>
					{$this->lang->words['cfield_empty']} <a href='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=add' class='mini_button'>{$this->lang->words['click2create']}</a></p>
				</td>
			</tr>
HTML;
}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>

<script type='text/javascript'>
	jQ("#customFields_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=support&section=customfields&do=reorder&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $departments ) {

if ( empty( $current ) )
{
	$title = $this->lang->words['cfield_add'];
	$id = 0;
}
else
{
	$title = $this->lang->words['cfield_edit'];
	$id = $current['sf_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['sf_name'] ) );
$form['type'] = ipsRegistry::getClass('output')->formDropdown( 'type', array(
	array( 'text', $this->lang->words['cfields_textbox'] ),
	array( 'dropdown', $this->lang->words['cfields_dropdown'] ),
	array( 'user', $this->lang->words['cfields_userpass'] ),
	array( 'ftp', $this->lang->words['cfields_ftp'] ),
	), ( empty( $current ) ? '' : $current['sf_type'] ) );
$form['extra'] = ipsRegistry::getClass('output')->formTextArea( 'extra', ( empty( $current ) ? '' : $current['sf_extra'] ) );
$form['departments'] = ipsRegistry::getClass('output')->formMultiDropdown( 'departments[]', $departments, empty( $current ) ? NULL : explode( ',', $current['sf_departments'] ) );
$form['required'] = ipsRegistry::getClass('output')->formYesNo( 'required', ( empty( $current ) ? 0 : $current['sf_required'] ) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=support&amp;section=customfields&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['cfield_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['cfield_type']}</strong></td>
			<td class='field_field'>{$form['type']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['cfield_options']}</strong></td>
			<td class='field_field'>
				{$form['extra']}<br />
				<span class='desctext'>{$this->lang->words['cfield_options_desc']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['scfield_departments']}</strong></td>
			<td class='field_field'>
				{$form['departments']}<br />
				<span class='desctext'>{$this->lang->words['scfield_departments_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['scfield_require']}</strong></td>
			<td class='field_field'>{$form['required']}</td>
		</tr>
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


}