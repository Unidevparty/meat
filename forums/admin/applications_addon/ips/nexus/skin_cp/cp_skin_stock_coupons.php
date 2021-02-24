<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Coupons
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		11th May 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_stock_coupons
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
// Manage Coupons
//===========================================================================
function manage( $coupons ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['coupons']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['coupon_add']}</a></li>
		</ul>
	</div>
</div>

HTML;

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['coupons']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th>{$this->registry->getClass('class_localization')->words['coupon_code']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

if ( !empty( $coupons ) )
{
	
	foreach ( $coupons as $data )
	{
		$menuKey++;
		$IPBHTML .= <<<HTML
				<tr class='ipsControlRow'>
					<td>&nbsp;</td>
					<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=edit&amp;id={$data['c_id']}'>{$data['c_code']}</a></span></td>
					<td>
						<ul class='ipsControlStrip'>
							<li class='i_edit'>
								<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=edit&amp;id={$data['c_id']}'>{$this->registry->getClass('class_localization')->words['edit']}...</a>
							</li>
							<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
								<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
							</li>
						</ul>
						<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
							<li class='icon view'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=uses&amp;id={$data['c_id']}'>{$this->registry->getClass('class_localization')->words['coupon_view_uses']}</a></li>
							<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=delete&amp;id={$data['c_id']}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>

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
						{$this->registry->getClass('class_localization')->words['coupon_empty']} <a href='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
					</td>
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
// Form
//===========================================================================
function form( $current=array() ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['coupon_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['coupon_edit'];
	$id = $current['c_id'];
}

$form['code'] = ipsRegistry::getClass('output')->formSimpleInput( 'code', ( empty( $current ) ? '' : $current['c_code'] ), 12 );
$form['base_discount'] = ipsRegistry::getClass('output')->formSimpleInput( 'base_discount', ( empty( $current ) ? '' : $current['c_discount'] ), 8 );
$form['base_unit'] = ipsRegistry::getClass('output')->formDropdown( 'base_unit', array( array( 'v', $this->settings['nexus_currency'] ), array( 'p', '%' ) ), ( empty( $current ) ? 'v' : $current['c_unit'] ) );
$form['products'] = "<select name='products[]' multiple='multiple'>" . package::getPackageSelector( NULL, TRUE, array( 'custom' ), ( empty( $current ) or $current['c_products'] == '*' ) ? NULL : explode( ',', $current['c_products'] ) ) . '</select>';
$form['limit_discount'] = ipsRegistry::getClass('output')->formYesNo( 'limit_discount', ( empty( $current ) ? 1 : $current['c_limit_discount'] ) );
$form['groups'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'groups[]', ( empty( $current ) or $current['c_groups'] == '*' ) ? NULL : explode( ',', $current['c_groups'] ), TRUE );
$form['uses'] = ipsRegistry::getClass('output')->formSimpleInput( 'uses', ( empty( $current ) ? '-1' : $current['c_uses'] ), 5 );
$form['member_uses'] = ipsRegistry::getClass('output')->formSimpleInput( 'member_uses', ( empty( $current ) ? '1' : $current['c_member_uses'] ), 5 );
$form['start'] = ipsRegistry::getClass('output')->formSimpleInput( 'start', $current['c_start'], 11 );
$form['end'] = ipsRegistry::getClass('output')->formSimpleInput( 'end', $current['c_end'], 11 );
$form['combine'] = ipsRegistry::getClass('output')->formYesNo( 'combine', ( empty( $current ) ? 1 : $current['c_combine'] ) );
$form['renewals'] = ipsRegistry::getClass('output')->formYesNo( 'renewals', ( empty( $current ) ? 0 : $current['c_renewals'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=coupons&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_code']}</strong></td>
			<td class='field_field'>
				{$form['code']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_code_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_discount']}</strong></td>
			<td class='field_field'>{$form['base_discount']} {$form['base_unit']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_limit']}</strong></td>
			<td class='field_field'>
				{$form['products']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_limit_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_renewals']}</strong></td>
			<td class='field_field'>
				{$form['renewals']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_renewals_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_dlimit']}</strong></td>
			<td class='field_field'>
				{$form['limit_discount']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_dlimit_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_glimit']}</strong></td>
			<td class='field_field'>
				{$form['groups']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_glimit_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_uses']}</strong></td>
			<td class='field_field'>
				{$form['uses']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_uses_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_muses']}</strong></td>
			<td class='field_field'>
				{$form['member_uses']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_muses_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_combine']}</strong></td>
			<td class='field_field'>
				{$form['combine']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['coupon_dates']}</strong></td>
			<td class='field_field'>
				{$form['start']} - {$form['end']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['coupon_dates_desc']}</span>
			</td>
		</tr>
	</table>	
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='button'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Uses
//===========================================================================
function uses( $uses ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['coupon_uses']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['coupon_uses']}</h3>
	<table class='ipsTable'>
		<tr>
			<th>{$this->registry->getClass('class_localization')->words['coupon_member']}</th>
			<th>{$this->registry->getClass('class_localization')->words['coupon_uses']}</th>
		</tr>
HTML;

	if ( !empty( $uses ) )
	{
		foreach ( $uses as $u )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$u['member']['member_id']}'>{$u['member']['members_display_name']}</a></span></td>
			<td>{$u['uses']}</td>
		</tr>		
HTML;
		}
	}
	else
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td colspan='4' class='no_messages'>{$this->registry->getClass('class_localization')->words['coupon_uses_empty']}</td>
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