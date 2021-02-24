<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Promotion - Commission Rules
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $ (Orginal: Mark)
 * @copyright	© 2012 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		2nd May 2012
 * @version		$Revision: 10721 $
 */
 
class cp_skin_promotion_refrules
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
function manage( $rules ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['refrules']}</h2>
	<ul class='context_menu'>
		<li><a href='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['refrule_add']}</a></li>
	</ul>
</div>

<div class='acp-box'>
 	<h3>{$this->lang->words['refrules']}</h3>
	<table class='ipsTable' id='referralBanners_list'>
		<tr>
			<th width='3%'>&nbsp;</th>
			<th width='69%'>{$this->lang->words['refrules_name']}</th>
			<th width='20%'>{$this->lang->words['refrules_commission']}</th>
			<th width='8%'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $rules ) )
{
	foreach ( $rules as $data )
	{
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='banners_{$data['rrule_id']}'>
			<td>&nbsp;</td>
			<td><a href='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=edit&amp;id={$data['rrule_id']}'><span class='larger_text'>{$data['rrule_name']}</span></a></td>
			<td>{$data['rrule_commission']}%</td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=edit&amp;id={$data['rrule_id']}'>{$this->lang->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=delete&amp;id={$data['rrule_id']}'>{$this->lang->words['delete']}</a>
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
			<td colspan='6' class='no_messages'>
				{$this->lang->words['refrules_empty']} <a href='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=add' class='mini_button'>{$this->lang->words['click2create']}</a>
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
// Add / Edit
//===========================================================================
function form( $current, $groups ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ) ? '' : $current['rrule_name'] );
$form['by_purchases_type'] = ipsRegistry::getClass('output')->formDropdown( 'by_purchases_type', array(
	array( 'n', $this->lang->words['refrules_ptype_amount'] ),
	array( 'v', sprintf( $this->lang->words['refrules_ptype_spent'], $this->settings['nexus_currency'] ) )
	), ( empty( $current ) ) ? '' : $current['rrule_by_purchases_type'] );
$form['by_purchases_op'] = $this->registry->output->formDropdown( 'by_purchases_op', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['rrule_by_purchases_op'] ) );
$form['by_purchases_unit'] = ipsRegistry::getClass('output')->formSimpleInput( 'by_purchases_unit', ( empty( $current ) ) ? '' : $current['rrule_by_purchases_unit'] );
$form['by_group'] = $this->registry->output->formMultiDropdown( 'by_group[]', $groups, empty( $current ) ? NULL : explode( ',', $current['rrule_by_group'] ) );
$form['for_purchases_type'] = ipsRegistry::getClass('output')->formDropdown( 'for_purchases_type', array(
	array( 'n', $this->lang->words['refrules_ptype_amount'] ),
	array( 'v', sprintf( $this->lang->words['refrules_ptype_spent'], $this->settings['nexus_currency'] ) )
	), ( empty( $current ) ) ? '' : $current['rrule_for_purchases_type'] );
$form['for_purchases_op'] = $this->registry->output->formDropdown( 'for_purchases_op', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['rrule_for_purchases_op'] ) );
$form['for_purchases_unit'] = ipsRegistry::getClass('output')->formSimpleInput( 'for_purchases_unit', ( empty( $current ) ) ? '' : $current['rrule_for_purchases_unit'] );
$form['for_group'] = $this->registry->output->formMultiDropdown( 'for_group[]', $groups, empty( $current ) ? NULL : explode( ',', $current['rrule_for_group'] ) );
$form['purchase_package'] = '<select name="purchase_package[]" multiple="multiple">' . package::getPackageSelector( NULL, TRUE, array( 'custom' ), empty( $current ) ? NULL : explode( ',', $current['rrule_purchase_packages'] ) ) . '</select>';
$form['purchase_any'] = $this->registry->output->formYesNo( 'purchase_any', empty( $current ) ? 1 : $current['rrule_purchase_any'] );
$form['purchase_package_limit'] = $this->registry->output->formYesNo( 'purchase_package_limit', empty( $current ) ? 0 : $current['rrule_purchase_package_limit'] );
$form['purchase_renewals'] = $this->registry->output->formYesNo( 'purchase_renewals', empty( $current ) ? 1 : $current['rrule_purchase_renewal'] );
$form['purchase_amount_op'] = $this->registry->output->formDropdown( 'purchase_amount_op', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['rrule_purchase_amount_op'] ) );
$form['purchase_amount_unit'] = ipsRegistry::getClass('output')->formSimpleInput( 'purchase_amount_unit', ( empty( $current ) ) ? '' : $current['rrule_purchase_amount_unit'] );
$form['commission'] = ipsRegistry::getClass('output')->formSimpleInput( 'commission', ( empty( $current ) ) ? '' : $current['rrule_commission'] );
$form['commission_limit'] = ipsRegistry::getClass('output')->formSimpleInput( 'commission_limit', ( empty( $current ) ) ? '' : $current['rrule_commission_limit'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['refrule_add']}</h2>
</div>

<form action='{$this->settings['base_url']}module=promotion&amp;section=refrules&amp;do=save' method='post' enctype='multipart/form-data'>
<input type='hidden' name='id' value='{$current['rrule_id']}' />
<div class='acp-box'>
	<h3>{$this->lang->words['refrule_add']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_name']}</td></label>
			<td class='field_field'>
				{$form['name']}<br />
				<span class='desctext'>{$this->lang->words['refrules_name_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['refrules_header_by']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchases']}</td></label>
			<td class='field_field'>
				{$form['by_purchases_type']} {$form['by_purchases_op']} {$form['by_purchases_unit']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchases_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_group']}</td></label>
			<td class='field_field'>
				{$form['by_group']}<br />
				<span class='desctext'>{$this->lang->words['refrules_group_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['refrules_header_for']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchases']}</td></label>
			<td class='field_field'>
				{$form['for_purchases_type']} {$form['for_purchases_op']} {$form['for_purchases_unit']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchases_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_group']}</td></label>
			<td class='field_field'>
				{$form['for_group']}<br />
				<span class='desctext'>{$this->lang->words['refrules_group_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['refrules_header_purchase']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchase_packages']}</td></label>
			<td class='field_field'>
				{$form['purchase_package']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchase_packages_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchase_renewals']}</td></label>
			<td class='field_field'>
				{$form['purchase_renewals']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchase_renewals']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchase_any']}</td></label>
			<td class='field_field'>
				{$form['purchase_any']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchase_any_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchase_packages_limit']}</td></label>
			<td class='field_field'>
				{$form['purchase_package_limit']}<br />
				<span class='desctext'>{$this->lang->words['refrules_purchase_packages_limit_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_purchase_amount']}</td></label>
			<td class='field_field'>{$form['purchase_amount_op']} {$form['purchase_amount_unit']}</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['refrules_header_commission']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_commission']}</td></label>
			<td class='field_field'>{$form['commission']} %</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['refrules_commission_limit']}</td></label>
			<td class='field_field'>
				{$form['commission_limit']} {$this->settings['nexus_currency']}<br />
				<span class='desctext'>{$this->lang->words['refrules_commission_limit_desc']}</span>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['save']}' class='realbutton'>
	</div>
</div>
</form>


HTML;

//--endhtml--//
return $IPBHTML;

}


}