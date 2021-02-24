<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Payments - Custom Package Fields
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
 
class cp_skin_stock_customfields
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
	<h2>{$this->registry->getClass('class_localization')->words['cfields']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['cfield_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['cfields']}</h3>
	<div>
		<table class='ipsTable' id='customFields_list'>
			<tr>
				<th width='3%'>&nbsp;</th>
				<th width='3%'>&nbsp;</th>
				<th width='91%'>{$this->registry->getClass('class_localization')->words['cfield_name']}</th>
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
					<img src='{$this->settings['skin_app_url']}/images/fields/{$data['cf_type']}.png' alt='{$data['cf_type']}' />
				</td>
				<td width='93%'>
					<span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=edit&amp;id={$fieldID}'>{$data['cf_name']}</a></span>
HTML;
		if ( $data['cf_type'] == 'dropdown' )
		{
			$options = str_replace( '<br />', ', ', $data['cf_extra'] );
			$IPBHTML .= <<<HTML
				<br /><span class='desctext'>{$options}</span>
HTML;
		}
		$IPBHTML .= <<<HTML
				</td>
				<td width='3%'>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=edit&amp;id={$fieldID}'>{$this->registry->getClass('class_localization')->words['edit']}...</a>
						</li>
						<li class='i_delete'>
							<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=delete&amp;id={$fieldID}'>{$this->registry->getClass('class_localization')->words['delete']}...</a>
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
					{$this->registry->getClass('class_localization')->words['cfield_empty']} <a href='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a></p>
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
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=customfields&do=reorder&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $selector ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['cfield_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['cfield_edit'];
	$id = $current['cf_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['cf_name'] ) );
$form['type'] = ipsRegistry::getClass('output')->formDropdown( 'type', array(
	array( 'text', $this->registry->getClass('class_localization')->words['cfields_textbox'] ),
	array( 'dropdown', $this->registry->getClass('class_localization')->words['cfields_dropdown'] ),
	array( 'user', $this->registry->getClass('class_localization')->words['cfields_userpass'] ),
	array( 'ftp', $this->registry->getClass('class_localization')->words['cfields_ftp'] ),
	), ( empty( $current ) ? '' : $current['cf_type'] ) );
$form['extra'] = ipsRegistry::getClass('output')->formTextArea( 'extra', ( empty( $current ) ? '' : $current['cf_extra'] ) );
$form['sticky'] = ipsRegistry::getClass('output')->formYesNo( 'sticky', ( empty( $current ) ? 0 : $current['cf_sticky'] ) );
$form['purchase'] = ipsRegistry::getClass('output')->formYesNo( 'purchase', ( empty( $current ) ? 1 : $current['cf_purchase'] ) );
$form['required'] = ipsRegistry::getClass('output')->formYesNo( 'required', ( empty( $current ) ? 0 : $current['cf_required'] ) );
$form['editable'] = ipsRegistry::getClass('output')->formYesNo( 'editable', ( empty( $current ) ? 1 : $current['cf_editable'] ) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=customfields&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_type']}</strong></td>
			<td class='field_field'>{$form['type']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_options']}</strong></td>
			<td class='field_field'>
				{$form['extra']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['cfield_options_desc']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_packages']}</strong></td>
			<td class='field_field'>
				<select name='packages[]' multiple='multiple'>
					{$selector}
				</select>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_sticky']}</strong></td>
			<td class='field_field'>
				{$form['sticky']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['cfield_sticky_desc']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_purchase']}</strong></td>
			<td class='field_field'>{$form['purchase']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_required']}</strong></td>
			<td class='field_field'>{$form['required']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_editable']}</strong></td>
			<td class='field_field'>{$form['editable']}</td>
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