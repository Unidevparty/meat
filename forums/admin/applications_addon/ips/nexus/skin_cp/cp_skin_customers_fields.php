<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Payments - Custom Customer Fields
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		31st January 2011
 * @version		$Revision: 9800 $
 */
 
class cp_skin_customers_fields
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
	<h2>{$this->registry->getClass('class_localization')->words['customer_fields']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=fields&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['customer_fields_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['customer_fields']}</h3>
	<div>
		<table class='ipsTable' id='customerFields_list'>
			<tr>
				<th width='3%'>&nbsp;</th>
				<th width='3%'>&nbsp;</th>
				<th width='91%'>{$this->registry->getClass('class_localization')->words['customer_fields_name']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;
	
foreach ( $fields as $fieldID => $data )
{
	$class = ( $data['f_locked'] ) ? 'single' : 'no_extra';

	$IPBHTML .= <<<HTML
			<tr class='ipsControlRow isDraggable' id='fields_{$fieldID}'>
				<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
				<td>
					<img src='{$this->settings['skin_app_url']}/images/fields/{$data['f_type']}.png' alt='{$data['f_type']}' />
				</td>
				<td>
					<span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=fields&amp;do=edit&amp;id={$fieldID}'>{$data['f_name']}</a></span>
				</td>
				<td>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=fields&amp;do=edit&amp;id={$fieldID}'>{$this->registry->getClass('class_localization')->words['edit']}...</a>
						</li>
HTML;
					if ( !$data['f_locked'] )
					{	
						$IPBHTML .= <<<HTML
						<li class='i_delete'>
							<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=customers&amp;section=fields&amp;do=delete&amp;id={$fieldID}'>{$this->registry->getClass('class_localization')->words['delete']}...</a>
						</li>
HTML;
					}
					$IPBHTML .= <<<HTML
					</ul>
				</td>
			</tr>
HTML;

}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>

<script type='text/javascript'>
	jQ("#customerFields_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=customers&section=fields&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
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
	$title = $this->registry->getClass('class_localization')->words['customer_fields_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['customer_fields_edit'];
	$id = $current['f_id'];
}

$typeOptions = array(
	array( 'text', $this->registry->getClass('class_localization')->words['cfields_textbox'] ),
	array( 'dropdown', $this->registry->getClass('class_localization')->words['cfields_dropdown'] ),
	);
if ( $current['f_type'] == 'special' )
{
	$typeOptions[] = array( 'special', $this->registry->getClass('class_localization')->words['cfields_special'] );
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['f_name'] ) );
$form['type'] = ipsRegistry::getClass('output')->formDropdown( 'type', $typeOptions, ( empty( $current ) ? '' : $current['f_type'] ), '', ( $current['f_locked'] ? "disabled='disabled'" : '' ) );
$form['extra'] = ipsRegistry::getClass('output')->formTextArea( 'extra', ( empty( $current ) ? '' : "\n" . $current['f_extra'] ) );
$form['reg_show'] = ipsRegistry::getClass('output')->formYesNo( 'reg_show', ( empty( $current ) ? 0 : $current['f_reg_show'] ) );
$form['reg_require'] = ipsRegistry::getClass('output')->formYesNo( 'reg_require', ( empty( $current ) ? 0 : $current['f_reg_require'] ) );
$form['purchase_show'] = ipsRegistry::getClass('output')->formYesNo( 'purchase_show', ( empty( $current ) ? 1 : $current['f_purchase_show'] ) );
$form['purchase_require'] = ipsRegistry::getClass('output')->formYesNo( 'purchase_require', ( empty( $current ) ? 0 : $current['f_purchase_require'] ) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=fields&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<input type='hidden' name='column' value='{$current['f_column']}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_name']}</strong>
			</td>
			<td class='field_field'>
				{$form['name']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_type']}</strong>
			</td>
			<td class='field_field'>
				{$form['type']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['cfield_options']}</strong>
			</td>
			<td class='field_field'>
				{$form['extra']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['cfield_options_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['customer_fields_reg_show']}</strong>
			</td>
			<td class='field_field'>
				{$form['reg_show']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['customer_fields_reg_req']}</strong>
			</td>
			<td class='field_field'>
				{$form['reg_require']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['customer_fields_purchase_show']}</strong>
			</td>
			<td class='field_field'>
				{$form['purchase_show']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['customer_fields_purchase_require']}</strong>
			</td>
			<td class='field_field'>
				{$form['purchase_require']}
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