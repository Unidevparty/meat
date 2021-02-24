<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Purchases
 * Last Updated: $Date: 2013-10-18 09:46:18 -0400 (Fri, 18 Oct 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		6th January 2010
 * @version		$Revision: 12386 $
 */
 
class cp_skin_payments_purchases
{

private $menuKey = 0;

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
// View Purchases
//===========================================================================
function viewPurchase( $purchase, $customfields, $children, $invoices, $extraHTML ) {

$status = $this->registry->getClass('class_localization')->words['purchase_active'];
if ( $purchase['ps_cancelled'] )
{
	$status = $this->registry->getClass('class_localization')->words['purchase_cancelled'];
}
elseif ( !$purchase['ps_active'] )
{
	$status = $this->registry->getClass('class_localization')->words['purchase_expired'];
}

$purchase['ps_start'] = $this->registry->getClass('class_localization')->getDate( $purchase['ps_start'], 'LONG' );
$purchase['ps_expire'] = $purchase['ps_expire'] ? $this->registry->getClass('class_localization')->getDate( $purchase['ps_expire'], 'JOINED', true ) : $this->registry->getClass('class_localization')->words['purchase_never_expire'];

$renewals = ipsRegistry::getAppClass( 'nexus' )->formatRenewalTerms( array( 'unit' => $purchase['ps_renewal_unit'], 'term' => $purchase['ps_renewals'], 'price' => $purchase['ps_renewal_price'], 'gr' => $purchase['ps_grouped_renewals'] ) );

$type = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $purchase['ps_app'], $purchase['ps_type'] ) . ( isset( $this->lang->words[ $purchase['ps_type'] ] ) ? $this->lang->words[ $purchase['ps_type'] ] : ucwords( $purchase['ps_type'] ) );

$member = customer::load( $purchase['ps_member'] )->data;

$parent = $purchase['ps_parent'] ? $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'nexus_purchases', 'where' => "ps_id={$purchase['ps_parent']}" ) ) : NULL;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$purchase['ps_name']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=edit&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_acp_url']}/images/icons/pencil.png' alt='' /> {$this->registry->getClass('class_localization')->words['edit_purchase']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=change&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/change.png' alt='' /> {$this->registry->getClass('class_localization')->words['purchases_change']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=transfer&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/transfer.png' alt='' /> {$this->registry->getClass('class_localization')->words['purchases_transfer']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=associate&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/associate.png' alt='' /> {$this->registry->getClass('class_localization')->words['purchases_associate']}</a></li>
HTML;
		if ( $purchase['ps_renewals'] )
		{
			if ( !$purchase['ps_invoice_pending'] )
			{
				$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=renew&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/renew.png' alt='' /> {$this->registry->getClass('class_localization')->words['generate_renewal']}</a></li>
HTML;
			}
			else
			{
				$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$purchase['ps_invoice_pending']}'><img src='{$this->settings['skin_app_url']}/images/purchases/renew.png' alt='' /> {$this->registry->getClass('class_localization')->words['view_renewal']}</a></li>
HTML;
			}
		}

		if ( $purchase['ps_parent'] and $purchase['ps_renewals'] and !$purchase['ps_grouped_renewals'] )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=groupon&amp;id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/arrow-merge.png' alt='' /> Group Renewals</a></li>
HTML;
		}
		elseif ( $purchase['ps_grouped_renewals'] )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=groupoff&amp;id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_app_url']}/images/purchases/arrow-branch.png' alt='' /> Ungroup Renewals</a></li>
HTML;
		}
		
		$IPBHTML .= <<<HTML
		{$extraHTML['buttons']}
HTML;


		if ( $purchase['ps_cancelled'] )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=uncancel&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_acp_url']}/images/icons/accept.png' alt='' /> {$this->registry->getClass('class_localization')->words['purchases_reactivate']}</a></li>
HTML;
		}
		
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&module=payments&section=purchases&do=cancel&id={$purchase['ps_id']}&v=1'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /> {$this->registry->getClass('class_localization')->words['purchases_cancel']}</a></li>
		</ul>
	</div>
</div>

{$extraHTML['header']}

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchase_information']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchases_id']}</strong></td>
			<td class='field_field'>{$purchase['ps_id']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_type']}</strong></td>
			<td class='field_field'>{$type}</td>
		</tr>
HTML;

	if ( $purchase['ps_item_uri'] or $purchase['ps_admin_uri'] )
	{
		$url = $purchase['ps_admin_uri'] ? "<a href='{$this->settings['base_url']}{$purchase['ps_admin_uri']}'>{$purchase['ps_name']}</a>" : "<a href='{$this->settings['board_url']}/index.php?{$purchase['ps_item_uri']}' target='_blank'>{$purchase['ps_name']}</a>";
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_item']}</strong></td>
			<td class='field_field'>{$url}</td>
		</tr>
HTML;
	}

$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_member']}</strong></td>
			<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$member['member_id']}'>{$member['_name']}</a></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_status']}</strong></td>
			<td class='field_field'>{$status}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchases_purchased']}</strong></td>
			<td class='field_field'>{$purchase['ps_start']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchases_expires']}</strong></td>
			<td class='field_field'>{$purchase['ps_expire']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchases_renewal_terms']}</strong></td>
			<td class='field_field'>{$renewals}</td>
		</tr>
HTML;

		if ( $parent )
		{
				$IPBHTML .= <<<HTML
				<tr>
					<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_parent']}</strong></td>
					<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$parent['ps_id']}'>{$parent['ps_name']}</a></td>
				</tr>
HTML;
		}
		
		if ( $purchase['ps_pay_to'] )
		{
			$payTo = customer::load( $purchase['ps_pay_to'] )->data;
			$commission = sprintf( $this->registry->getClass('class_localization')->words['purchase_commission_'], 100 - $purchase['ps_commission'], "<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$payTo['member_id']}'>{$payTo['_name']}</a>" );
			$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_commission']}</strong></td>
				<td class='field_field'>{$commission}</td>
			</tr>
HTML;
		}

		if ( $purchase['ps_extra'] )
		{
			$extra = array();
			if ( $unserialized = unserialize( $purchase['ps_extra'] ) and is_array( $unserialized ) )
			{
				foreach ( $unserialized as $k => $e )
				{
					$file = IPSLib::getAppDir( $k ) . '/extensions/nexus/items.php';
					if ( is_file( $file ) )
					{
						require( $file );/*noLibHook*/
						$className = 'items_' . $k;
						$class = new $className;
						if ( method_exists( $class, 'formatExtra' ) )
						{
							$extra = array_merge( $extra, $class->formatExtra( $purchase, $e ) );
						}
					}
				}
				if ( !empty( $extra ) )
				{
					$extra = implode( '<br />', $extra );
					$IPBHTML .= <<<HTML
					<tr>
						<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_extra']}</strong></td>
						<td class='field_field'>{$extra}</td>
					</tr>
HTML;
				}
			}
		}
		
		$IPBHTML .= <<<HTML
	</table>
</div>
<br />

HTML;


if ( !empty( $customfields ) )
{
	$IPBHTML .= <<<HTML
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['product_options']}</h3>
		<table class='ipsTable'>
HTML;
	
	foreach ( $customfields as $field )
	{
		$ts = (string) $field;
	
		$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$field->name}</strong></td>
				<td class='field_field'>{$ts}</td>
			</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		</table>
	</div>
	<br />
HTML;
}

if ( $extraHTML['mainBox'] )
{
	$IPBHTML .= <<<HTML
	{$extraHTML['mainBox']}
	<br />
HTML;
}

if ( !empty( $children ) )
{
	$IPBHTML .= <<<HTML
	<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchase_children']}</h3>
	<table class='form_table'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th width='7%'>{$this->registry->getClass('class_localization')->words['purchases_id']}</th>
			<th width='29%'>{$this->registry->getClass('class_localization')->words['purchases_item']}</th>
			<th width='15%'>{$this->registry->getClass('class_localization')->words['purchases_purchased']}</th>
			<th width='15%'>{$this->registry->getClass('class_localization')->words['purchases_expires']}</th>
			<th width='24%'>{$this->registry->getClass('class_localization')->words['purchases_renewal_terms']}</th>
			<th width='5%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;
		foreach ( $children as $item )
		{
			$this->menuKey++;
	
			$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] );
						
			$cancel_word = $item['ps_cancelled'] ? $this->registry->getClass('class_localization')->words['purchases_reactivate'] : $this->registry->getClass('class_localization')->words['purchases_cancel'];
			$cancel_link = $item['ps_cancelled'] ? 'uncancel' : 'cancel';
			
			$class = '';
			$note = '';
			$this->class = ( $this->class == 'row1' ) ? 'row2' : 'row1';
			if ( $item['ps_cancelled'] )
			{
				$class = '_red';
				$note = $this->registry->getClass('class_localization')->words['purchase_cancelled'];
			}
			elseif ( !$item['ps_active'] )
			{
				$class = '_amber';
				$note = $this->registry->getClass('class_localization')->words['purchase_expired'];
			}
			$class = $class ? $class : $this->class;
			
			$cf_sticky = '';
			$cf_hidden = '';
			if ( is_array( $item['customfields'] ) )
			{
				foreach ( $item['customfields'] as $f )
				{
					if ( $f->sticky )
					{
						$cf_sticky .= "<br />{$f->name}: {$f}";
					}
					else
					{
						$cf_hidden .= "<strong>{$f->name}:</strong> {$f}<br />";
					}
				}
			}
			$extra = '';
			if ( $cf_hidden )
			{
				$extra = "onclick=\"showFields('{$item['ps_id']}')\" style='cursor:pointer'";
			}
		
			$IPBHTML .= <<<HTML
			<tr class='{$class} ipsControlRow'{$extra}>
				<td>{$appIcon}</td>
				<td>{$item['ps_id']}</td>
				<td><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$item['ps_id']}'>{$item['ps_name']}</a><span class='desctext'>{$cf_sticky}</span><br /><em>{$note}</em></td>
				<td>{$item['ps_start']}</td>
				<td>{$item['ps_expire']}</td>
				<td>{$item['renewal']}</td>
				<td>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a title='{$this->registry->getClass('class_localization')->words['edit']}'  href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=edit&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['edit']}...</a>
						</li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$this->menuKey}'>
							<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
						</li>
					</ul>
					<ul class='acp-menu' id='menu{$this->menuKey}_menucontent'>
						<li class='icon assign'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=transfer&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_transfer']}...</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=associate&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_associate']}...</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do={$cancel_link}&amp;id={$item['ps_id']}'>{$cancel_word}...</a></li>
						<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=delete&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
					</ul>
				</td>
			</tr>
			<tr class='{$class}' style='display:none' id='cf-{$item['ps_id']}'>
				<td colspan='1'>
					&nbsp;
				</td>
				<td colspan='5'>
					<div class='information-box'>
						{$cf_hidden}
					</div>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>

HTML;
		}
		
		$IPBHTML .= <<<HTML
	</table>
	</div>
	<br /><br />
HTML;
	}
	
$IPBHTML .= <<<HTML
<div class='acp-box'>
<h3>{$this->registry->getClass('class_localization')->words['purchase_invoices']}</h3>
<table class='ipsTable'>
	<tr>
		<th width='5%'>&nbsp;</th>
		<th width='7%'>{$this->registry->getClass('class_localization')->words['invoice_id']}</th>
		<th width='25%'>{$this->registry->getClass('class_localization')->words['invoice_title']}</th>
		<th width='10%'>{$this->registry->getClass('class_localization')->words['invoice_amount']}</th>
		<th width='21%'>{$this->registry->getClass('class_localization')->words['invoice_date']}</th>
		<th width='10%' class='col_buttons'>&nbsp;</th>
	</tr>
HTML;
	foreach ( $invoices as $invoiceID => $invoice )
	{
		$this->menuKey++;
		
		switch ( $invoice['status'] ) { case 'paid': $badgeColor = 'green'; break; case 'pend': $badgeColor = 'grey'; break; case 'expd': $badgeColor = 'purple'; break; case 'canc': $badgeColor = 'red'; break; }
			
		$IPBHTML .= <<<HTML
			<tr class='ipsControlRow'>
				<td>
					<span class='ipsBadge badge_{$badgeColor}' style='width: 100%; text-align: center'>
						{$this->registry->getClass('class_localization')->words[ 'istatus_' . $invoice['status'] ]}
					</span>
				</td>
				<td>{$invoiceID}</td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$invoiceID}'>{$invoice['title']}</a></span></td>
				<td>{$invoice['amount']}</td>
				<td>{$invoice['date']}</td>
				<td>
HTML;
				if( $invoice['status'] != 'paid' )
				{
					$IPBHTML .= <<<EOF
						<ul class='ipsControlStrip'>
							<li class='i_accept'><a title='{$this->registry->getClass('class_localization')->words['invoice_mark_paid']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=paid&amp;l=1'>{$this->registry->getClass('class_localization')->words['invoice_mark_paid']}</a></li>
							<li class='ipsControlStrip_more ipbmenu' id='menu{$this->menuKey}'>
								<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
							</li>
						</ul>
						<ul class='acp-menu' id='menu{$this->menuKey}_menucontent'>
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=pend&amp;l=1'>{$this->registry->getClass('class_localization')->words['invoice_mark_pend']}</a></li>
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=expd&amp;l=1'>{$this->registry->getClass('class_localization')->words['invoice_mark_expd']}</a></li>
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=canc&amp;l=1'>{$this->registry->getClass('class_localization')->words['invoice_mark_canc']}</a></li>
							<li class='icon refresh'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=resend_invoice&amp;id={$invoiceID}&amp;l=1'>{$this->registry->getClass('class_localization')->words['invoice_resend']}...</a></li>
							<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}&amp;l=1'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
						</ul>

EOF;
				}
				else
				{
					$IPBHTML .= <<<EOF
					<ul class='ipsControlStrip'>
						<li class='i_delete'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$this->menuKey}'>
							<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
						</li>
					</ul>
					<ul class='acp-menu' id='menu{$this->menuKey}_menucontent'>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=pend'>{$this->registry->getClass('class_localization')->words['invoice_mark_pend']}</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=expd'>{$this->registry->getClass('class_localization')->words['invoice_mark_expd']}</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=canc'>{$this->registry->getClass('class_localization')->words['invoice_mark_canc']}</a></li>
					</ul>
EOF;
				}
							
			$IPBHTML .= <<<HTML
				</td>
			</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
</table>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Purchases
//===========================================================================
function cancel( $purchase ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$purchase['ps_name']}</h2>
</div>

HTML;

if ( !$purchase['ps_cancelled'] and $purchase['ps_renewals'] )
{
$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=expire' method='post'>
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['purchcanc_expire']}</h3>
		<table class='ipsTable'>
			<tr>
				<td colspan='2'>{$this->registry->getClass('class_localization')->words['purchcanc_expire_blurb']}</td>
			</tr>
HTML;
			if ( $purchase['ps_app'] == 'nexus' and array_key_exists( $purchase['ps_type'], package::$types ) )
			{
				$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['puchcanc_can_reactivate']}</strong></td>
				<td class='field_field'>{$this->registry->output->formYesNo( 'customer_reactivate', 0 )}</td>
			</tr>
HTML;
			}
			$IPBHTML .= <<<HTML
		</table>
		<div class="acp-actionbar">
			<input type='submit' value='{$this->registry->getClass('class_localization')->words['purchcanc_expire']}' class='realbutton'>
		</div>
	</div>
</form>
<br /><br />
HTML;
}

if ( !$purchase['ps_cancelled'] )
{
$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=do_cancel' method='post'>
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['puchcanc_cancel']}</h3>
		<table class='ipsTable'>
			<tr>
				<td colspan='2'>{$this->registry->getClass('class_localization')->words['puchcanc_cancel_blurb']}</td>
			</tr>
HTML;
			if ( $purchase['ps_app'] == 'nexus' and array_key_exists( $purchase['ps_type'], package::$types ) )
			{
				$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['puchcanc_can_reactivate']}</strong></td>
				<td class='field_field'>{$this->registry->output->formYesNo( 'customer_reactivate', 0, 'rec2' )}</td>
			</tr>
HTML;
			}
			$IPBHTML .= <<<HTML
		</table>
		<div class="acp-actionbar">
			<input type='submit' value='{$this->registry->getClass('class_localization')->words['puchcanc_cancel']}' class='realbutton'>
		</div>
	</div>
</form>
<br /><br />
HTML;
}

$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=delete' method='post'>
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['puchcanc_delete']}</h3>
		<table class='ipsTable'>
			<tr>
				<td>{$this->registry->getClass('class_localization')->words['puchcanc_delete_blurb']}</td>
			</tr>
		</table>
		<div class="acp-actionbar">
			<input type='submit' value='{$this->registry->getClass('class_localization')->words['puchcanc_delete']}' class='realbutton redbutton'>
		</div>
	</div>
</form>
<br /><br />


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Reactivate
//===========================================================================
function reactivate( $purchase ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$purchase['ps_name']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=uncancel' method='post'>
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<input type='hidden' name='grr' value='c' />
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['puchreac_charge']}</h3>
		<table class='ipsTable'>
			<tr>
				<td>{$this->registry->getClass('class_localization')->words['puchreac_charge_blurb']}</td>
			</tr>
		</table>
		<div class="acp-actionbar">
			<input type='submit' value='{$this->registry->getClass('class_localization')->words['puchreac_charge']}' class='realbutton'>
		</div>
	</div>
</form>
<br /><br />

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=uncancel' method='post'>
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<input type='hidden' name='grr' value='f' />
	<div class='acp-box'>
		<h3>{$this->registry->getClass('class_localization')->words['puchreac_free']}</h3>
		<table class='ipsTable'>
			<tr>
				<td>{$this->registry->getClass('class_localization')->words['puchreac_free_blurb']}</td>
			</tr>
		</table>
		<div class="acp-actionbar">
			<input type='submit' value='{$this->registry->getClass('class_localization')->words['puchreac_free']}' class='realbutton'>
		</div>
	</div>
</form>
<br /><br />


HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Purchase Form
//===========================================================================
function purchaseForm( $purchase, $customFields, $extra, $customPackage, $memberGroups, $permissionSets, $supportDepartments, $severities, $tax, $attachments, $postKey, $parent=array(), $children=array() ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', $purchase['ps_name'] );
$form['expire_date'] = ipsRegistry::getClass('output')->formInput( 'expire_date', $purchase['ps_expire'], 'expire_date', 11 );

$hasGroupedChildren = FALSE;
foreach ( $children as $c )
{
	if ( $c['ps_grouped_renewals'] )
	{
		$hasGroupedChildren = TRUE;
		break;
	}
}

if ( $purchase['ps_grouped_renewals'] )
{
	$groupedRenewals = unserialize( $purchase['ps_grouped_renewals'] );
	$form['renewal'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal', $groupedRenewals['term'] );
	$form['renewal_unit'] = ipsRegistry::getClass('output')->formDropdown( 'renewal_unit', array( array( 'd', $this->registry->getClass('class_localization')->words['renew_term_days'] ), array( 'w', $this->registry->getClass('class_localization')->words['renew_term_weeks'] ), array( 'm', $this->registry->getClass('class_localization')->words['renew_term_months'] ), array( 'y', $this->registry->getClass('class_localization')->words['renew_term_years'] ) ), $groupedRenewals['unit'] );
	$form['renewal_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_price', $groupedRenewals['price'] );
}
else
{
	if ( $hasGroupedChildren )
	{
		require_once IPSLib::getAppDir( 'nexus' ) . '/sources/invoiceModel.php';
		$costPerDay = invoice::_costPerDay( array( 'term' => $purchase['ps_renewals'], 'unit' => $purchase['ps_renewal_unit'], 'price' => $purchase['ps_renewal_price'] ) );
		foreach ( $children as $c )
		{
			if ( !$c['ps_cancelled'] and $c['ps_grouped_renewals'] )
			{
				$costPerDay -= invoice::_costPerDay( unserialize( $c['ps_grouped_renewals'] ) );
			}
		}
		
		$purchase['ps_renewal_price'] = $costPerDay * ipsRegistry::getAppClass('nexus')->renewalTermInDays( $purchase['ps_renewals'], $purchase['ps_renewal_unit'] );

	}

	$form['renewal'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal', $purchase['ps_renewals'] );
	$form['renewal_unit'] = ipsRegistry::getClass('output')->formDropdown( 'renewal_unit', array( array( 'd', $this->registry->getClass('class_localization')->words['renew_term_days'] ), array( 'w', $this->registry->getClass('class_localization')->words['renew_term_weeks'] ), array( 'm', $this->registry->getClass('class_localization')->words['renew_term_months'] ), array( 'y', $this->registry->getClass('class_localization')->words['renew_term_years'] ) ), $purchase['ps_renewal_unit'] );
	$form['renewal_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_price', $purchase['ps_renewal_price'] );
}

$extraTab = '';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['purchase_edit']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchase_edit']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=save' method='post' onsubmit="return checkExpireDate( '{$purchase['ps_active']}' );">
	<input type='hidden' name='id' value='{$purchase['ps_id']}' />
	<input type='hidden' name='v' value='{$this->request['v']}' />
	<input type='hidden' name='rid' value='{$this->request['rid']}' />
	<input type='hidden' name='post_key' value='{$postKey}' />
	<table class="ipsTable double_pad">
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['package_tab_1']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['purchase_payment']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_expire']}</strong></td>
			<td class='field_field'>
HTML;
			if ( $purchase['ps_grouped_renewals'] )
			{
				$IPBHTML .= $this->lang->getDate( $parent['ps_expire'], 'JOINED' );
			}
			else
			{
				$IPBHTML .= <<<HTML
				{$form['expire_date']} {$this->registry->getClass('class_localization')->words['date_format']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['purchase_expire_desc']}</span>
HTML;
			}
			$IPBHTML .= <<<HTML
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_renewals']}</strong></td>
			<td class='field_field'>
				{$this->registry->getClass('class_localization')->words['every']} {$form['renewal']} {$form['renewal_unit']}
HTML;
				if ( $hasGroupedChildren )
				{
					$IPBHTML .= <<<HTML
					<br /><span class='desctext'>{$this->registry->getClass('class_localization')->words['purchase_renewals_parent_desc']}</span>
HTML;
				}
				elseif ( !$purchase['ps_grouped_renewals'] )
				{
					$IPBHTML .= <<<HTML
					<br /><span class='desctext'>{$this->registry->getClass('class_localization')->words['purchase_renewals_desc']}</span>
HTML;
				}
				
				$IPBHTML .= <<<HTML
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_renew_price']}</strong></td>
			<td class='field_field'>
				{$form['renewal_price']}
HTML;
				if ( $hasGroupedChildren )
				{
					$IPBHTML .= <<<HTML
					<br /><span class='desctext'>{$this->registry->getClass('class_localization')->words['purchase_renewals_thisonly_desc']}</span>
HTML;
				}
				elseif ( $purchase['ps_grouped_renewals'] )
				{
					$IPBHTML .= <<<HTML
					<br /><span class='desctext'>{$this->registry->getClass('class_localization')->words['purchase_renewals_grouped_desc']}</span>
HTML;
				}
				
				$IPBHTML .= <<<HTML
			</td>
		</tr>
HTML;

	if ( !empty( $customFields ) )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['purchase_cfields']}</th>
		</tr>
HTML;
		foreach ( $customFields as $field )
		{
			$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$field->name}</strong></td>
				<td class='field_field'>{$field->edit( TRUE, '' )}</td>
			</tr>
HTML;
		}
	}
	
	if ( $extra )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words[ $purchase['ps_type'] . '_settings' ]}</th>
		</tr>
		{$extra}
HTML;
	}
		
	if ( $customPackage !== NULL )
	{
		if ( $customPackage->data['p_type'] == 'product' )
		{
			$extraData = $customPackage->packageForm( $customPackage->data, 'product' );
			$form['lkey'] = ipsRegistry::getClass('output')->formDropdown( 'lkey', $extraData['lkeyOptions'], $customPackage->data['p_lkey'] );
			$form['lkey_identifier'] = ipsRegistry::getClass('output')->formDropdown( 'lkey_identifier', $extraData['identifierOptions'], $customPackage->data['p_lkey_identifier'] );
			$form['lkey_uses'] = ipsRegistry::getClass('output')->formSimpleInput( 'lkey_uses', $customPackage->data['p_lkey_uses'], 4 );
			$IPBHTML .= <<<HTML
			<tr>
				<th colspan='2'>{$this->registry->getClass('class_localization')->words['lkey']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['lkey']}</strong></td>
				<td class='field_field'>{$form['lkey']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['lkey_identifier']}</strong></td>
				<td class='field_field'>
					{$form['lkey_identifier']}<br />
					<span class='desctext'>{$this->registry->getClass('class_localization')->words['lkey_identifier_blurb']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['lkey_uses']}</strong></td>
				<td class='field_field'>
					{$form['lkey_uses']}<br />
					<span class='desctext'>{$this->registry->getClass('class_localization')->words['lkey_uses_blurb']}</span>
				</td>
			</tr>
HTML;
		}
		
		$form['primary_promote'] = ipsRegistry::getClass('output')->formDropdown( 'primary_promote', array_merge( array( array( 0, $this->registry->getClass('class_localization')->words['package_nogroupchange'] ) ), $memberGroups ), $customPackage->data['p_primary_group'] );
		$form['secondary_promote'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'secondary_promote[]', NULL, TRUE );
		$form['perms_promote'] = ipsRegistry::getClass('output')->formMultiDropdown( 'perms_promote[]', $permissionSets, NULL );
		$form['return_primary'] = ipsRegistry::getClass('output')->formYesNo( 'return_primary', $customPackage->data['p_return_primary'] );
		$form['return_secondary'] = ipsRegistry::getClass('output')->formYesNo( 'return_secondary', $customPackage->data['p_return_secondary'] );
		$form['return_perm'] = ipsRegistry::getClass('output')->formYesNo( 'return_perm', $customPackage->data['p_return_perm'] );
		$form['custom_module'] = ipsRegistry::getClass('output')->formSimpleInput( 'custom_module', $customPackage->data['p_module'], 20 );
		
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
		$editor = new $classToLoad();
		$editor->setContent( $customPackage->data['p_page'] );
		$form['page'] = $editor->show('page');
		
		$form['support'] = ipsRegistry::getClass('output')->formYesNo( 'support', $customPackage->data['p_support'] );
		$form['support_department'] = ipsRegistry::getClass('output')->formDropdown( 'support_department', $supportDepartments, $customPackage->data['p_support_department'] );
		$form['support_severity'] = ipsRegistry::getClass('output')->formDropdown( 'support_severity', $severities, $customPackage->data['p_support_severity'] );

		$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['package_member_changes']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_primary']}</strong></td>
			<td class='field_field'>
				{$form['primary_promote']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_primary_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_primary_return']}</strong></td>
			<td class='field_field'>{$form['return_primary']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_secondary']}</strong></td>
			<td class='field_field'>
				{$form['secondary_promote']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_secondary_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_secondary_return']}</strong></td>
			<td class='field_field'>{$form['return_secondary']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_perms']}</strong></td>
			<td class='field_field'>
				{$form['perms_promote']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_perms_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_perms_return']}</strong></td>
			<td class='field_field'>{$form['return_perm']}</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['package_custom_actions']}</th>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->registry->getClass('class_localization')->words['package_action']}</strong><br />
				<span class='desctext'><a href='http://www.invisionpower.com/support/guides/_/advanced-and-developers/ipnexus/ipnexus-custom-actions-r55' target='_blank'>{$this->registry->getClass('class_localization')->words['package_action_desc']}</a></span>
			</td>
			<td class='field_field'>
				<strong>admin/applications_addon/ips/nexus/sources/actions/{$form['custom_module']}.php</strong><br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_action_loc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->registry->getClass('class_localization')->words['package_tab_6']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_tab_6']}</strong></td>
			<td class='field_field'>
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_page_desc']}</span>
				{$form['page']}
				<span id='attachment_wrap'> <strong>{$this->registry->getClass('class_localization')->words['req_reply_attachments']}</strong></span>
				{$attachments}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_support']}</strong></td>
			<td class='field_field'>{$form['support']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_support_dpt']}</strong></td>
			<td class='field_field'>
				{$form['support_department']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_support_dpt_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_support_sev']}</strong></td>
			<td class='field_field'>
				{$form['support_severity']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['package_support_sev_desc']}</span>
			</td>
		</tr>
HTML;
	
	}

$IPBHTML .= <<<HTML
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
</div>

<script type='text/javascript'>
	function checkExpireDate( active )
	{
		exploded = $('expire_date').value.split("-");
		date = new Date( exploded[2], exploded[0] - 1, exploded[1], 0, 0, 0, 0 );
				
		today = new Date();
						
		if ( date > today && active == '0' )
		{
			if ( !confirm('You have changed the expiry date to a date in the future. The purchase will be reactivated.' ) ) { return false; }
		}
		else if ( date < today && active == '1' )
		{
			if ( !confirm('You have changed the expiry date to a date in the past. The purchase will be expired.' ) ) { return false; }
		}
	}
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Purchases
//===========================================================================
function view( $purchases, $parentMap, $pagination ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['customer_purchases']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['ac_purchases']}</h3>
	<table class='form_table'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th width='7%'>{$this->registry->getClass('class_localization')->words['purchases_id']}</th>
			<th width='17%'>{$this->registry->getClass('class_localization')->words['purchase_item']}</th>
			<th width='17%'>{$this->registry->getClass('class_localization')->words['purchase_member']}</th>
			<th width='14%'>{$this->registry->getClass('class_localization')->words['purchases_purchased']}</th>
			<th width='14%'>{$this->registry->getClass('class_localization')->words['purchases_expires']}</th>
			<th width='21%'>{$this->registry->getClass('class_localization')->words['purchases_renewal_terms']}</th>
			<th width='5%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	$this->menuKey = $menuKey;
	if( count($parentMap[0]) AND is_array($parentMap[0]) )
	{
		foreach ( $parentMap[0] as $item )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $item ], $parentMap, $purchases, FALSE, FALSE );
		}
	}
	else
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td colspan='6' class='no_messages'>{$this->lang->words['no_purchases_here']}</td>
		</tr>
HTML;
	}
	$menuKey = $this->menuKey;

$IPBHTML .= <<<HTML
	</table>
</div>
<br />
{$pagination}

<script type='text/javascript'>
	function showFields( id )
	{
		if ( $('cf-'+id).style.display == 'none' )
		{
			$('cf-'+id).style.display = '';
		}
		else
		{
			$('cf-'+id).style.display = 'none';
		}
	}
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

function _generatePurchaseRow( $item, $parentMap, $purchases, $assoc=FALSE, $tree=TRUE )
{
	$padding = '';
	if ( $tree )
	{
		$parent = $item['ps_parent'];
		while ( $parent != 0 )
		{
			$padding .= "<img src='{$this->settings['skin_app_url']}/images/tree.gif' /> ";
			$parent = $purchases[ $parent ]['ps_parent'];
		}
	}

	$this->menuKey++;
		
	$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] );
					
	$this->class = ( $this->class == 'row1' ) ? 'row2' : 'row1';
	if ( $item['ps_cancelled'] )
	{
		$this->class = '_red';
		$note = $this->registry->getClass('class_localization')->words['purchase_cancelled'];
	}
	elseif ( $assoc and !$item['associable'] )
	{
		$this->class = '_red';
		$note = $this->registry->getClass('class_localization')->words['purchase_no_assoc'];
	}
	elseif ( !$item['ps_active'] )
	{
		$class = '_amber';
		$note = $this->registry->getClass('class_localization')->words['purchase_expired'];
	}
		
	$cf_sticky = '';
	$cf_hidden = '';
	
	if ( $item['sticky'] !== NULL )
	{
		$cf_sticky .= "<br />{$item['sticky']}";
	}
	
	if ( is_array( $item['customfields'] ) )
	{
		foreach ( $item['customfields'] as $f )
		{
			if ( $f->sticky )
			{
				$cf_sticky .= "<br />{$f->name}: {$f}";
			}
			else
			{
				$cf_hidden .= "<strong>{$f->name}:</strong> {$f}<br />";
			}
		}
	}
	if ( is_array( $item['extrafields'] ) )
	{
		foreach ( $item['extrafields'] as $k => $v )
		{
			$cf_hidden .= "<strong>{$k}:</strong> {$v}<br />";
		}
	}
	$extra = '';
	if ( $cf_hidden )
	{
		$extra = "onclick=\"showFields('{$item['ps_id']}')\" style='cursor:pointer'";
	}
	
	$item['member'] = customer::load( $item['ps_member'] );
	
	$style = ( $assoc and $assoc['ps_parent'] == $item['ps_id'] ) ? " style='font-weight: bold'" : '';
	
	$link = $assoc ? "{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=do_associate&amp;id={$assoc['ps_id']}&amp;package={$item['ps_id']}" : "{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$item['ps_id']}";
	
	$IPBHTML = <<<EOF
	<tr class='{$this->class} ipsControlRow'{$extra}>
		<td{$style}>{$appIcon}</td>
		<td{$style}>{$item['ps_id']}</td>
		<td{$style}>{$padding}<a href='{$link}'>{$item['ps_name']}</a><span class='desctext'>{$cf_sticky}</span><br /><em>{$note}</em></td>
EOF;
	if ( !$assoc )
	{
		$IPBHTML .= <<<EOF
		<td><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$item['member']->data['member_id']}'>{$item['member']->data['_name']}</a></td>
EOF;
	}
		$IPBHTML .= <<<EOF
		<td{$style}>{$item['ps_start']}</td>
		<td{$style}>{$item['ps_expire']}</td>
		<td{$style}>{$item['renewal']}</td>
		<td>
EOF;
		if ( !$assoc )
		{
			$IPBHTML .= <<<EOF
			<ul class='ipsControlStrip'>
				<li class='i_edit'>
					<a title='{$this->registry->getClass('class_localization')->words['edit']}'  href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=edit&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['edit']}...</a>
				</li>
				<li class='ipsControlStrip_more ipbmenu' id='menu{$this->menuKey}'>
					<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
				</li>
			</ul>
			<ul class='acp-menu' id='menu{$this->menuKey}_menucontent'>
				<li class='icon assign'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=transfer&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_transfer']}...</a></li>
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=change&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_change']}...</a></li>
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=associate&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_associate']}...</a></li>
				<li class='icon delete'><a  href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=cancel&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_cancel']}...</a></li>
EOF;
				if ( $item['ps_cancelled'] )
				{
					$IPBHTML .= <<<HTML
					<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=uncancel&amp;id={$item['ps_id']}'>{$this->registry->getClass('class_localization')->words['purchases_reactivate']}...</a></li>
HTML;
				}
				
				$IPBHTML .= <<<EOF
			</ul>
EOF;
		}
			
			$IPBHTML .= <<<EOF
		</td>
	</tr>
	<tr class='{$class}' style='display:none' id='cf-{$item['ps_id']}'>
		<td colspan='1'>
			&nbsp;
		</td>
		<td colspan='5'>
			<div class='information-box'>
				{$cf_hidden}
			</div>
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
EOF;

	if ( is_array( $parentMap[ $item['ps_id'] ] ) )
	{
		foreach ( $parentMap[ $item['ps_id'] ] as $child )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $child ], $parentMap, $purchases, $assoc );
		}
	}

	return $IPBHTML;

}

//===========================================================================
// Transfer
//===========================================================================
function transfer( $purchase, $error ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ipsRegistry::$request['name'], 'member' );
$form['email'] = ipsRegistry::getClass('output')->formInput( 'email', ipsRegistry::$request['email'] );
$form['target_id'] = ipsRegistry::getClass('output')->formSimpleInput( 'target_id', ipsRegistry::$request['target_id'] );


$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['purchase_transfer']}</h2>
</div>

<div class='information-box'>
	{$this->registry->getClass('class_localization')->words['purchase_transfer_desc']}
</div>
<br />

HTML;

if ( $error )
{
	$error = ( $error == 2 ) ? $this->lang->words['err_no_member'] : $this->lang->words['err_transfer_badmulti'];

	$IPBHTML .= <<<HTML
	<div class='warning'>
		{$error}
	</div>
	<br />
HTML;
}

$IPBHTML .= <<<HTML

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=do_transfer' method='post'>
<input type='hidden' name='id' value='{$purchase['ps_id']}' />
<input type='hidden' name='v' value='{$this->request['v']}' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchase_transfer_h3']}</h3>
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_transfer_to_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_transfer_to_email']}</strong></td>
			<td class='field_field'>{$form['email']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_transfer_to_id']}</strong></td>
			<td class='field_field'>{$form['target_id']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['purchase_transfer']}' class='realbutton'>
	</div>
</div>
</form>

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
// Associate
//===========================================================================
function associate( $ps, $purchases, $parentMap ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['purchase_associate']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchase_associate_select']}</h3>
	<table class='form_table'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th width='7%'>{$this->registry->getClass('class_localization')->words['purchases_id']}</th>
			<th width='34%'>{$this->registry->getClass('class_localization')->words['purchase_item']}</th>
			<th width='14%'>{$this->registry->getClass('class_localization')->words['purchases_purchased']}</th>
			<th width='14%'>{$this->registry->getClass('class_localization')->words['purchases_expires']}</th>
			<th width='21%'>{$this->registry->getClass('class_localization')->words['purchases_renewal_terms']}</th>
			<th width='5%'>&nbsp;</th>
		</tr>
HTML;

		foreach ( $parentMap[0] as $item )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $item ], $parentMap, $purchases, $ps );
		}
		
$IPBHTML .= <<<HTML
		<tr>
			<td>&nbsp;</td>
			<td colspan='8' style='text-align: center'><em><a href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;do=do_associate&amp;id={$ps['ps_id']}&amp;package=0&amp;v={$this->request['v']}'>or click here to remove assocation</a></em></td>
		</tr>
	</table>
</div>

<script type='text/javascript'>
	function showFields( id )
	{
		if ( $('cf-'+id).style.display == 'none' )
		{
			$('cf-'+id).style.display = '';
		}
		else
		{
			$('cf-'+id).style.display = 'none';
		}
	}
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Change
//===========================================================================
function changePackage( $purchase ) {

$selector = package::getPackageSelector( NULL, TRUE, array(), $purchase['ps_item_id'], array( $purchase['ps_type'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['purchases_change']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=do_change' method='post'>
<input type='hidden' name='id' value='{$purchase['ps_id']}' />
<input type='hidden' name='v' value='{$this->request['v']}' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['purchases_change']}</h3>
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['purchase_change_select']}</strong></td>
			<td class='field_field'>
				<select name='new_package'>{$selector}</select>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['purchases_change']}' class='realbutton'>
	</div>
</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

}