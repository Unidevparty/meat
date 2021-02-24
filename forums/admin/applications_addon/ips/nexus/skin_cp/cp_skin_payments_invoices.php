<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Invoices
 * Last Updated: $Date: 2013-10-18 09:46:18 -0400 (Fri, 18 Oct 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		9th January 2010
 * @version		$Revision: 12386 $
 */
 
class cp_skin_payments_invoices
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
// Manage Invoices
//===========================================================================
function invoices( $invoices, $pagination, $fromSearch, $currentSearch ) {

$search['status'] = ipsRegistry::getClass('output')->formMultiDropdown( 'status[]', array( array( 'paid', $this->lang->words['istatus_paid'] ), array( 'pend', $this->lang->words['istatus_pend'] ), array( 'expd', $this->lang->words['istatus_expd'] ), array( 'canc', $this->lang->words['istatus_canc'] ) ), $currentSearch['status'] );
$search['member'] = ipsRegistry::getClass('output')->formInput( 'member', $currentSearch['member'], 'member' );
$search['amount1'] = ipsRegistry::getClass('output')->formDropdown( 'amount1', array( array( 'eq', $this->lang->words['equals'] ), array( 'gt', $this->lang->words['gt'] ), array( 'lt', $this->lang->words['lt'] ) ), $currentSearch['amount1'] );
$search['amount2'] = ipsRegistry::getClass('output')->formSimpleInput( 'amount2', $currentSearch['amount2'] );
$search['date1'] = ipsRegistry::getClass('output')->formSimpleInput( 'date1', $currentSearch['date1'], 11 );
$search['date2'] = ipsRegistry::getClass('output')->formSimpleInput( 'date2', $currentSearch['date2'], 11 );
$search['sort1'] = ipsRegistry::getClass('output')->formDropdown( 'sort1', array( array( 'i_id', $this->lang->words['invoice_id'] ), array( 'i_title', $this->lang->words['invoice_title'], ), array( 'i_total', $this->lang->words['invoice_amount'] ), array( 'i_date', $this->lang->words['invoice_date'] ) ), $currentSearch['sort1'] ? $currentSearch['sort1'] : 't_date' );
$search['sort2'] = ipsRegistry::getClass('output')->formDropdown( 'sort2', array( array( 'asc', $this->lang->words['asc'] ), $currentSearch['sort2'] ? $currentSearch['sort2'] : array( 'desc', $this->lang->words['desc'] ) ), $currentSearch['sort2'] ? $currentSearch['sort2'] : 'desc' );

$menuKey = 0;
$check = 0;

$searchBoxStyle = $fromSearch ? '' : 'display:none; ';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['customer_invoices']}</h2>
HTML;
	if ( $this->registry->getClass('class_permissions')->checkPermission( 'invoices_add' ) )
	{
		$IPBHTML .= <<<HTML
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' id='generate-invoice'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['invoice_generate']}</a></li>
		</ul>
	</div>
HTML;
	}
$IPBHTML .= <<<HTML
</div>

<div class='acp-box'>
 	<h3>{$this->lang->words['customer_invoices']}</h3>
	<form action='{$this->settings['base_url']}module=payments&amp;section=invoices&amp;do=multimod' method='post' id='mm_form'>
	<input type='hidden' name='multimod' value='0' id='multimod' />
	<input type='hidden' name='member' value='{$this->request['member_id']}' />
	<table class='ipsTable'>
		<tr>
			<th width='30px'>&nbsp;</th>
			<th>{$this->lang->words['invoice_id']}</th>
			<th>{$this->lang->words['invoice_title']}</th>
			<th>{$this->lang->words['invoice_member']}</th>
			<th>{$this->lang->words['invoice_amount']}</th>
			<th>{$this->lang->words['invoice_date']}</th>
			<th>{$this->lang->words['invoice_paid']}</th>
			<th class='col_buttons'>&nbsp;</th>
			<th width='5px'><input type='checkbox' id='checkall' onchange='checkAll()'  /></th>
		</tr>
HTML;

if ( !empty( $invoices ) )
{
	foreach ( $invoices as $invoiceID => $invoice )
	{
		$menuKey++;
		
		$memberLink = ( $invoice['member']['member_id'] ) ? "<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$invoice['member']['member_id']}'>{$invoice['member']['_name']}</a>" : "<span class='desctext' title='{$this->lang->words['invoice_guest_desc']}'>{$this->lang->words['invoice_guest']}</span>";
		
		switch ( $invoice['status'] ) { case 'paid': $badgeColor = 'green'; break; case 'pend': $badgeColor = 'grey'; break; case 'expd': $badgeColor = 'purple'; break; case 'canc': $badgeColor = 'red'; break; }
		
		$IPBHTML .= <<<HTML
			<tr class='ipsControlRow'>
				<td>
					<span class='ipsBadge badge_{$badgeColor}' style='width: 100%; text-align: center'>
						{$this->lang->words[ 'istatus_' . $invoice['status'] ]}
					</span>
				</td>
				<td>{$invoiceID}</td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$invoiceID}'>{$invoice['title']}</a></span></td>
				<td>{$memberLink}</td>
				<td>{$invoice['amount']}</td>
				<td>{$invoice['date']}</td>
				<td>{$invoice['paid']}</td>
				<td>
HTML;
				if( $invoice['status'] != 'paid' )
				{
					$IPBHTML .= <<<EOF
						<ul class='ipsControlStrip'>
EOF;
						if ( $invoice['temp'] or $invoice['status'] == 'pend' )
						{
							$IPBHTML .= <<<EOF
							<li class='i_edit'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=edit&amp;id={$invoiceID}'>{$this->lang->words['edit']}...</a></li>
							<li class='i_accept'><a title='{$this->lang->words['invoice_mark_paid']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=paid&amp;l=1'>{$this->lang->words['invoice_mark_paid']}</a></li>
EOF;
						}
						else
						{
							$IPBHTML .= <<<EOF
							<li class='i_accept'><a title='{$this->lang->words['invoice_mark_paid']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=paid&amp;l=1'>{$this->lang->words['invoice_mark_paid']}</a></li>
EOF;
						}
						$IPBHTML .= <<<EOF
							<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
								<a href='#'>{$this->lang->words['options']}</a>
							</li>
						</ul>
						<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
EOF;
						if ( $invoice['member']['cim_profile_id'] )
						{
							$IPBHTML .= <<<EOF
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=card&amp;id={$invoiceID}&amp;l=1'>{$this->lang->words['charge_to_card']}</a></li>
EOF;
						}
						
						if ( $invoice['member']['cm_credits'] )
						{
							$IPBHTML .= <<<EOF
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=credit&amp;id={$invoiceID}&amp;l=1'>{$this->lang->words['charge_to_credit']}</a></li>
EOF;
						}
						
						$IPBHTML .= <<<EOF
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=pend&amp;l=1'>{$this->lang->words['invoice_mark_pend']}</a></li>
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=expd&amp;l=1'>{$this->lang->words['invoice_mark_expd']}</a></li>
							<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=canc&amp;l=1'>{$this->lang->words['invoice_mark_canc']}</a></li>
							<li class='icon refresh'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=resend_invoice&amp;id={$invoiceID}&amp;l=1'>{$this->lang->words['invoice_resend']}...</a></li>
							<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}&amp;l=1'>{$this->lang->words['delete']}...</a></li>
						</ul>

EOF;
				}
				else
				{
					$IPBHTML .= <<<EOF
					<ul class='ipsControlStrip'>
						<li class='i_delete'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}'>{$this->lang->words['delete']}...</a></li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
							<a href='#'>{$this->lang->words['options']}</a>
						</li>
					</ul>
					<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=pend'>{$this->lang->words['invoice_mark_pend']}</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=expd'>{$this->lang->words['invoice_mark_expd']}</a></li>
						<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=canc'>{$this->lang->words['invoice_mark_canc']}</a></li>
					</ul>
EOF;
				}
							
			$IPBHTML .= <<<HTML
				</td>
				<td>
					<input type='checkbox' name='check[{$invoiceID}]' id='check{$check}' />
				</td>
			</tr>
HTML;

			$check++;
	}
}
else
{
	$IPBHTML .= <<<HTML
			<tr>
				<td colspan='7' class='no_messages'>
					{$this->lang->words['invoice_empty']}
				</td>
			</tr>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
	</form>
	<div class='acp-actionbar' style='text-align:right'>
		<select onchange="$('multimod').value = this.value; if ( this.value == 'del' && !confirm('{$this->lang->words['delete_confirm']}' ) ) { this.value = 0; return false; } $('mm_form').submit()">
			<option value='0'>{$this->lang->words['invoice_multimod']}</option>
			<option value='paid'>{$this->lang->words['invoice_mark_paid']}</option>
			<option value='pend'>{$this->lang->words['invoice_mark_pend']}</option>
			<option value='expd'>{$this->lang->words['invoice_mark_expd']}</option>
			<option value='canc'>{$this->lang->words['invoice_mark_canc']}</option>
			<option value='resend'>{$this->lang->words['invoice_resend']}</option>
			<option value='del'>{$this->lang->words['delete']}</option>
		</select>
	</div>
</div>
<br />
{$pagination}

<br /><br /><br />

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices' method='post'>
<input type='hidden' name='search' value='1' />
	<div class='acp-box'>
	 	<h3>{$this->lang->words['invoice_search']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<td class="field_title"><strong class='title'>{$this->lang->words['invoice_status']}</strong></td>
				<td class="field_field">{$search['status']}</td>
			</tr>
			<tr>
				<td class="field_title"><strong class='title'>{$this->lang->words['invoice_member']}</strong></td>
				<td class="field_field">{$search['member']}</td>
			</tr>
			<tr>
				<td class="field_title"><strong class='title'>{$this->lang->words['invoice_amount']}</strong></td>
				<td class="field_field">{$search['amount1']} {$search['amount2']}</td>
			</tr>
			<tr>
				<td class="field_title"><strong class='title'>{$this->lang->words['date_between']}</strong></td>
				<td class="field_field">{$search['date1']} {$this->lang->words['and']} {$search['date2']} {$this->lang->words['date_format']}</td>
			</tr>
			<tr>
				<td class="field_title"><strong class='title'>{$this->lang->words['sort']}</strong></td>
				<td class="field_field">{$search['sort1']} {$search['sort2']}</td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['search']}' class='button' />
		</div>
	</div>
</form>

<script type='text/javascript'>

	document.observe("dom:loaded", function(){
	var autoComplete = new ipb.Autocomplete( $('member'), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
});

	function doPopUp( e, url )
	{
		new ipb.Popup('addnote', { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: url } );
	}
	
	function checkAll()
	{
		if ( $('checkall').checked )
		{
			for ( i=0; i < {$check}; i++ )
			{
				\$('check'+i).checked = true ;
			}
		}
		else
		{
			for ( i=0; i < {$check}; i++ )
			{
				\$('check'+i).checked = false ;
			}
		}
	}

	$('generate-invoice').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=customers&do=invoice&secure_key=" + ipb.vars['md5_hash'] ) );
	
</script>


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Invoice
//===========================================================================
function view( $invoice, $transactions, $shiporders ) {

$invoice['note'] = $invoice['note'] ? '<br /><em>' . $invoice['note'] . '</em>' : '';

$memberLink = ( $invoice['member']['member_id'] ) ? "<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$invoice['member']['member_id']}'>{$invoice['member']['_name']}</a>" : "<span class='desctext'>{$this->lang->words['invoice_guest_desc']}</span>";

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$invoice['title']} [#{$invoice['id']}]</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_acp_url']}/images/icons/pencil.png' /> {$this->lang->words['invoice_set_status']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=resend_invoice&amp;id={$invoice['id']}&amp;v=1'><img src='{$this->settings['skin_acp_url']}/images/icons/arrow_refresh.png' alt='' /> {$this->lang->words['invoice_resend']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=print&amp;id={$invoice['id']}' target='_blank'><img src='{$this->settings['skin_acp_url']}/images/icons/printer.png' alt='' /> {$this->lang->words['invoice_print']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=editNotes&amp;id={$invoice['id']}'><img src='{$this->settings['skin_app_url']}/images/customers/note.png' alt='' /> {$this->lang->words['invoice_notes']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoice['id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /> {$this->lang->words['delete']}</a></li>
		</ul>
	</div>
</div>

<ul class='ipbmenu_content' id='mark_invoice_menucontent' style='display: none'>
	<li><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoice['id']}&amp;status=paid&amp;v=1' style='text-decoration: none' >{$this->lang->words['istatus_paid']}</a></li>
	<li><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoice['id']}&amp;status=pend&amp;v=1' style='text-decoration: none' >{$this->lang->words['istatus_pend']}</a></li>
	<li><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoice['id']}&amp;status=expd&amp;v=1' style='text-decoration: none' >{$this->lang->words['istatus_expd']}</a></li>
	<li><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoice['id']}&amp;status=canc&amp;v=1' style='text-decoration: none' >{$this->lang->words['istatus_canc']}</a></li>
</ul>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_info']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_id']}</strong></td>
			<td class='field_field'>{$invoice['id']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_status']}</strong></td>
			<td class='field_field'>{$invoice['status_desc']}{$invoice['note']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_member']}</strong></td>
			<td class='field_field'>{$memberLink}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_date']}</strong></td>
			<td class='field_field'>{$invoice['date']}</td>
		</tr>
HTML;
	if ( $invoice['po'] )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_po']}</strong></td>
			<td class='field_field'>{$invoice['po']}</td>
		</tr>
HTML;
	}
	if ( $invoice['notes'] )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_notes']}</strong></td>
			<td class='field_field'>{$invoice['notes']}</td>
		</tr>
HTML;
	}
	
	if ( isset( $invoice['extra']['commission'] ) and !empty( $invoice['extra']['commission'] ) )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['history_type_comission']}</strong></td>
			<td class='field_field'>
HTML;
		foreach ( $invoice['extra']['commission'] as $c )
		{
			$cstring = sprintf( $this->lang->words['nexus_invoice_commission'], $this->lang->formatMoney( $c['amount'], FALSE ), "<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$c['to_id']}'>{$c['to_name']}</a>", isset( $c['rule'] ) ? "(<a href='{$this->settings['base_url']}&amp;module=promotion&amp;section=refrules&amp;id={$c['rule']}'>{$c['rule_name']}</a>)" : '' );
			$IPBHTML .= <<<HTML
				{$cstring}<br />
HTML;
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

<div class='acp-box'>
 	<h3>{$this->lang->words['invoice_items']}</h3>
	{$this->invoiceTable( new invoice( $invoice['id'] ) )}
	<div class='acp-actionbar' style='text-align: right; padding:10px; height: auto; line-height: 20px;'>
HTML;
	if ( $invoice['discount'] or $invoice['tax'] )
	{
		$IPBHTML .= <<<HTML
			{$this->lang->words['invoice_subtotal']}{$invoice['subtotal']}<br />
HTML;
	}
	if ( $invoice['discount'] )
	{
		$IPBHTML .= <<<HTML
			{$invoice['discount']}{$this->lang->words['item_discount']}: {$invoice['discount_amount']}<br />
HTML;
	}
		
	if ( $invoice['tax'] )
	{
		$invoice['tax'] = ipsRegistry::getClass('class_localization')->formatMoney( $invoice['tax'], FALSE );
		$IPBHTML .= <<<HTML
			{$this->lang->words['invoice_tax']}{$invoice['tax']}<br />
HTML;
	}
		$IPBHTML .= <<<HTML
		<strong>{$this->lang->words['invoice_total']}{$invoice['total']}</strong>
	</div>
</div>
<br /><br />

HTML;

if ( !empty( $transactions ) )
{

$IPBHTML .= <<<HTML

<div class='acp-box'>
 	<h3>{$this->lang->words['invoice_transactions']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th>{$this->lang->words['transaction_id']}</th>
			<th>{$this->lang->words['transaction_method']}</th>
			<th>{$this->lang->words['transaction_amount']}</th>
			<th>{$this->lang->words['transaction_date']}</th>
			<th>{$this->lang->words['transaction_note']}</th>
			<th>&nbsp;</th>
		</tr>
HTML;

foreach ( $transactions as $id => $data )
{
	$row = ($row == 'acp-row-off') ? 'acp-row-on' : 'acp-row-off';
	
	switch ( $data['status'] ) { case 'okay': $badgeColor = 'green'; break; case 'hold': case 'pend': case 'wait': $badgeColor = 'grey'; break; case 'fail': case 'rfnd': $badgeColor = 'red'; break; case 'revw': $badgeColor = 'purple'; }
	
	$IPBHTML .= <<<EOF
		<tr class='{$row} ipsControlRow'>
			<td>
				<span class='ipsBadge badge_{$badgeColor}' style='width: 100%; text-align: center'>
					{$this->lang->words[ 'tstatus_'. $data['status'] ]}
				</span>
			</td>
			<td>
				<span class='larger_text'>{$id}</span>
				<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=view&amp;id={$id}'><span class='desctext'>({$this->lang->words['view_details']})</span></a>
			</td>
			<td>{$data['method']}</td>
			<td>{$data['amount']}</td>
			<td>{$data['date']}</td>
			<td>{$data['note']}</td>
			<td class='col_buttons'>
EOF;
			if( $data['status'] != 'okay' )
			{
				$IPBHTML .= <<<EOF
					<ul class='ipsControlStrip'>
						<li class='i_accept'>
							<a title='{$this->lang->words['transaction_approve']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$id}&amp;status=okay&amp;i=1'>{$this->lang->words['transaction_approve']}</a>
						</li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$id}'>
							<a href='#'>{$this->lang->words['options']}...</a>
						</li>
					</ul>
					<ul class='acp-menu' id='menu{$id}_menucontent'>
EOF;
					if ( $data['status'] != 'fail' )
					{
						$IPBHTML .= <<<EOF
						<li class='icon cancel'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$id}&amp;status=fail&amp;i=1'>{$this->lang->words['transaction_refuse']}</a></li>
EOF;
					}
					
					$IPBHTML .= <<<EOF
						<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=delete&amp;id={$id}&amp;i=1'>{$this->lang->words['delete']}</a></li>
					</ul>
EOF;
			}
								
				$IPBHTML .= <<<EOF
				</td>
		</tr>
EOF;
}
	
$IPBHTML .= <<<HTML
	</table>
</div>
<br /><br />

HTML;

}

if ( !empty( $shiporders ) )
{
	
	$IPBHTML .= <<<EOF
<div class='acp-box'>
 	<h3>{$this->lang->words['shiporders']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='5%'>&nbsp;</th>
			
			<th>&nbsp;</th>
			<th>{$this->lang->words['shiporders_method']}</th>
			<th>{$this->lang->words['shiporders_date']}</th>
			<th>{$this->lang->words['shiporders_shipped_date']}</th>
		</tr>
EOF;

foreach ( $shiporders as $id => $data )
{
	$row = ($row == 'acp-row-off') ? 'acp-row-on' : 'acp-row-off';
	
	switch ( $data['status'] ) { case 'done': $badgeColor = 'green'; break; case 'pend': $badgeColor = 'grey';  break; case 'canc': $badgeColor = 'red'; }
	
	$IPBHTML .= <<<EOF
		<tr>
			<td>
				<span class='ipsBadge badge_{$badgeColor}' style='width: 60px; text-align: center'>
					{$this->lang->words[ 'shstatus_' . $data['status'] ]}
				</span>
			</td>
			<td>
				<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=view&amp;id={$id}'>
					<span class='desctext'>{$this->lang->words['shiporder_view']}</span>
				</a>
			</td>
			<td>{$data['method']}</td>
			<td>{$data['date']}</td>
			<td>{$data['shipped_date']}</td>
		</tr>
EOF;
}

$IPBHTML .= <<<EOF
		
	</table>
</div>
<br />

EOF;

}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Invoice Form
//===========================================================================
function add( $invoice, $items ) {

$form['title'] = ipsRegistry::getClass('output')->formInput( 'title', $invoice['title'], 'title', 30, 'text', '', '', 128 );
$form['status'] = ipsRegistry::getClass('output')->formDropDown( 'status', array(
	array( 'paid', $this->lang->words['istatus_paid'] ),
	array( 'pend', $this->lang->words['istatus_pend'] ),
	array( 'expd', $this->lang->words['istatus_expd'] ),
	array( 'canc', $this->lang->words['istatus_canc'] ),
	), $invoice['temp'], 'status' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_generate']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='add_item'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' /> {$this->lang->words['invoice_add_item']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add_renewal&amp;id={$invoice['id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['invoice_add_renewal']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add_item&amp;item_app=nexus&amp;item_type=charge&amp;invoice={$invoice['id']}'><img src='{$this->settings['skin_app_url']}/images/nexus_icons/charge.png' alt='' /> {$this->lang->words['invoice_add_charge']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save&amp;id={$invoice['id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' /> {$this->lang->words['save']}</a></li>
		</ul>
	</div>
</div>

<ul class='ipbmenu_content' id='add_item_menucontent' style='display: none'>
HTML;

foreach ( $items as $app => $icons )
{
	foreach ( $icons as $k => $v )
	{
		$icon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $app, $k );
		$IPBHTML .= <<<HTML
		<li>{$icon} <a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add_item&amp;item_app={$app}&amp;item_type={$k}&amp;invoice={$invoice['id']}' style='text-decoration: none' >{$v}</a></li>
HTML;
	}
}

$IPBHTML .= <<<HTML
</ul>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_info']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=do_edit' method='post'>
	<input type='hidden' name='id' value='{$invoice['id']}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_title_']}</strong></td>
			<td class='field_field'>
				{$form['title']}<br />
				<span class='desctext'>{$this->lang->words['optional']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_status']}</strong></td>
			<td class='field_field'>{$form['status']}</td>
		</tr>
	</table>
	</form>
</div>

<script type='text/javascript'>

	function editInfo( e )
	{
		new Ajax.Request( ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=invoices&do=edit_info&secure_key=" + ipb.vars['md5_hash'],
		{
			parameters:
			{
				invoice: '{$invoice['id']}',
				title: \$('title').value,
				status: \$('status').value
			},
			onSuccess: function( t ) { }
		});

	}
	
	function updateQuantity( e, id )
	{
		if ( parseInt( $('q'+id).value ) == $('q'+id).value && $('q'+id).value != 0 )
		{
			new Ajax.Request( ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=invoices&do=quantity&secure_key=" + ipb.vars['md5_hash'],
			{
				evalJSON: 'force',
				parameters:
				{
					invoice: '{$invoice['id']}',
					item: id,
					q: \$('q'+id).value
				},
				onSuccess: function( t )
				{
					\$( 'unit-'+id ).innerHTML = t.responseJSON['unit'];
					\$( 'line-'+id ).innerHTML = t.responseJSON['line'];
					\$('total').innerHTML = t.responseJSON['total'];
				}
			});
		}
	}

	$('title').observe('blur', editInfo.bindAsEventListener( this ) );
	$('status').observe('change', editInfo.bindAsEventListener( this ) );

</script>

<br /><br />

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_items']}</h3>
	<table class='form_table'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th width='25%'>{$this->lang->words['invoice_item']}</th>
			<th width='25%'>{$this->lang->words['item_cfields']}</th>
			<th width='15%'>{$this->lang->words['item_unit']}</th>
			<th width='10%'>{$this->lang->words['item_qty']}</th>
			<th width='15%'>{$this->lang->words['item_line']}</th>
			<th width='5%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

$totalBeforeDiscount = 0;
$customfields = $this->cache->getCache('package_fields');
require_once( IPSLib::getAppDir( 'nexus' ) . '/sources/customFields.php' );/*noLibHook*/
foreach( $invoice['items'] as $k => $item )
{
	$class = ( $class == 'row1' ) ? 'row2' : 'row1';
	if ( $item['app'] == 'nexus' and $item['type'] == 'special' )
	{
		$class = '_amber';
		$appIcon = '&nbsp;';
	}
	else
	{
		$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['app'], $item['type'], $item['cost'], $item['renewal'] );
	}
	
	$item['itemName'] = $item['itemURI'] ? "<a href='". $this->registry->output->buildUrl( $item['itemURI'], 'public' ) ."' target='_blank'>{$item['itemName']}</a>" : $item['itemName'];
	$totalBeforeDiscount += $linePrice;
	
	if ( is_array( $item['prices'] ) )
	{
		$linePrice = 0;
		foreach ( $item['prices'] as $price => $qty )
		{
			$linePrice += ( $price * $qty );
		}
		$item['cost'] = invoice::formatPrices( $item['prices'] );
	}
	else
	{
		$linePrice = $item['cost'] * $item['quantity'];
		$item['cost'] = ipsRegistry::getClass('class_localization')->formatMoney( $item['cost'] );
	}
	
	$item['tax'] = ipsRegistry::getClass('class_localization')->formatMoney( $item['tax'] );
	
	$linePrice = ipsRegistry::getClass('class_localization')->formatMoney( $linePrice );
	
	$cfields = array();
	if ( $item['app'] == 'nexus' and array_key_exists( $item['type'], package::$types ) and isset( $item['cfields'] ) )
	{
		foreach ( $item['cfields'] as $cfId => $cfValue )
		{
			$_f = customField::factory( $customfields[ $cfId ] );
			$_f->currentValue = $cfValue;
			$cfields[] = "{$customfields[ $cfId ]['cf_name']}: " . (string) $_f;
		}
	}
	$cfields = implode( '<br />', $cfields );

	$IPBHTML .= <<<HTML
		<tr class='ipsControlRow {$class}' style='height:35px'>
			<td>{$appIcon}</td>
			<td>{$item['itemName']}</td>
			<td>{$cfields}</td>
			<td><span id='unit-{$k}'>{$item['cost']}</span></td>
			<td><input value='{$item['quantity']}' size='3' id='q{$k}' /></td>
			<td><span id='line-{$k}'>{$linePrice}</span></td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=remove_item&amp;invoice={$invoice['id']}&amp;item={$k}'>{$this->lang->words['delete']}...</a>
					</li>
				</ul>
			</td>
		</tr>
		<script type='text/javascript'>
			$('q{$k}').observe('keyup', updateQuantity.bindAsEventListener( this, '{$k}' ) );
		</script>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
	<div class='acp-actionbar' style='height: auto; line-height: 20px;'>
HTML;
	if ( $invoice['discount'] or $invoice['tax'] )
	{
		$IPBHTML .= <<<HTML
			{$this->lang->words['invoice_subtotal']}{$invoice['subtotal']}<br />
HTML;
	}
		
	if ( $invoice['tax'] )
	{
		$invoice['tax'] = ipsRegistry::getClass('class_localization')->formatMoney( $invoice['tax'], FALSE );
		$IPBHTML .= <<<HTML
			{$this->lang->words['invoice_tax']}{$invoice['tax']}<br />
HTML;
	}
	if ( $invoice['discount'] )
	{
		$IPBHTML .= <<<HTML
			{$invoice['discount']}{$this->lang->words['item_discount']}: {$invoice['discount_amount']}<br />
HTML;
	}
		$IPBHTML .= <<<HTML
		<strong>{$this->lang->words['invoice_total']}<span id='total'>{$invoice['total']}</span></strong><br />
		<span class='desctext'>{$this->lang->words['invoice_tax_explain']}</span>
	</div>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Package
//===========================================================================
function addPackage( $packageGroups, $packages, $id ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['package_add']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add_item&amp;item_app=nexus&amp;item_type=package&amp;invoice={$id}' method='post'>
	<div class='acp-box ipsTreeTable' style='margin-right: 5px;'>
		<h3>Packages</h3>
		{$this->_packageSelector( 0, $packageGroups, $packages )}
		<div class="acp-actionbar">
			<input type='submit' value='{$this->lang->words['continue']}' class='realbutton'>
		</div>
	</div>
</form>

<br style='clear: both' />

<script type='text/javascript'>
	var qts = [];

	function selectGroup( groupID )
	{
		if( $('g_wrap_' + groupID) && $('g_wrap_' + groupID).readAttribute('needsUpdate') != 'yes' )
		{
			Effect.toggle( $('g_wrap_' + groupID), 'slide', { duration: 0.3 } );
			if ( !$('group_' + groupID).hasClassName('nochildren') )
			{
				if( $('group_' + groupID).hasClassName('open') ){
					$('group_' + groupID).removeClassName('open');
				} else {
					$('group_' + groupID).addClassName('open');
				}
			}
		}
	}
	
	function selectPackage( event, packageId )
	{
		if ( !( event.target instanceof HTMLInputElement ) )
		{
			$( 'qty_' + packageId ).value++;
		}
	}
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Package: Selector
//===========================================================================
function _packageSelector( $parent, $packageGroups, $packages ) {
$IPBHTML = "";
//--starthtml--//

if ( !isset( $packageGroups[ $parent ] ) )
{
	return '';
}

foreach ( $packageGroups[ $parent ] as $groupID => $groupName )
{
	$nochildren = ( ( !isset( $packages[ $groupID ] ) or empty( $packages[ $groupID ] ) ) and ( !isset( $packageGroups[ $groupID ] ) or empty( $packageGroups[ $groupID ] ) ) ) ? 'nochildren' : '';

	$IPBHTML .= <<<HTML
	<div class='group parent {$nochildren} row' id='group_{$groupID}' onclick='selectGroup({$groupID})'>
		<img src='{$this->settings['skin_app_url']}/images/packages/group.png' class='icon' /> <a  class='clickable'>{$groupName}</a>
	</div>
	<div id='g_wrap_{$groupID}' class='group_wrap parent_wrap' style='display: none'>
HTML;

	if ( isset( $packages[ $groupID ] ) )
	{
		foreach ( $packages[ $groupID ] as $packageId => $packageData )
		{
			$IPBHTML .= <<<HTML
		<div class='child row clickable' onclick='selectPackage( event, {$packageId} )'>
			<div class='package_info'>
				{$this->registry->output->formInput( "packages[{$packageId}]", '0', "qty_{$packageId}", 1 )} &nbsp; <a class='clickable'>{$packageData['p_name']}</a>
			</div>
		</div>
HTML;
		}
	}

	$IPBHTML .= <<<HTML
		{$this->_packageSelector( $groupID, $packageGroups, $packages )}
	</div>
HTML;
}


//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Purchase Form
//===========================================================================
function addPackage2( $id, $output, $extraIds ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['package_add']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['package_add']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=package&amp;invoice={$id}' method='post'>
HTML;
	foreach ( $extraIds as $p => $_extraIds )
	{
		foreach ( $_extraIds as $i )
		{
			$IPBHTML .= <<<HTML
		<input type='hidden' name='packages[{$p}][]' value='{$i}' />
HTML;
		}
	}
	$IPBHTML .= <<<HTML
	<table class="ipsTable double_pad">
		{$output}
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

//===========================================================================
// Purchase Form
//===========================================================================
function addPackage2_p( $id, $package, $customFields, $extra, $renewOptions, $associateOptions, $selectedAssociateOption, $itemId ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
	<input type='hidden' name='packages[{$package['p_id']}][]' value='{$itemId}' />
	<tr>
		<th colspan='2'>{$package['p_name']}</th>
	</tr>

HTML;
	if ( !empty( $associateOptions ) )
	{
		$form['assoc_options'] = $this->registry->output->formDropdown( "associate[{$itemId}]", $associateOptions, $selectedAssociateOption );
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_assoc']}</strong></td>
			<td class='field_field'>{$form['assoc_options']}</td>
		</tr>
HTML;
	}
	if ( count( $renewOptions ) > 1 )
	{
		$form['renew_options'] = $this->registry->output->formDropdown( "renewals[{$itemId}]", $renewOptions );
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchases_renewal_terms']}</strong></td>
			<td class='field_field'>{$form['renew_options']}</td>
		</tr>
HTML;
	}
	$IPBHTML .= $extra;
	foreach ( $customFields as $field )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$field->name}</strong></td>
			<td class='field_field'><fieldset style='border: 0'>{$field->edit( TRUE, '', "{$field->id}[$itemId]" )}</fieldset></td>
		</tr>
HTML;
	}

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Add Renewal - Select Item
//===========================================================================
function addRenewal1( $purchases, $parentMap, $id ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_renewal']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_select_renew']}</h3>
	<table class='ipsTable'>
		<tr>
			<th>&nbsp;</th>
			<th>{$this->lang->words['purchases_id']}</th>
			<th>{$this->lang->words['purchase_item']}</th>
			<th>{$this->lang->words['purchases_purchased']}</th>
			<th>{$this->lang->words['purchases_expires']}</th>
			<th>{$this->lang->words['purchases_renewal_terms']}</th>
		</tr>
HTML;

	if ( !empty( $parentMap[0] ) )
	{
		foreach ( $parentMap[0] as $item )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $item ], $parentMap, $purchases, $id );
		}
	}

$IPBHTML .= <<<HTML
	</table>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

function _generatePurchaseRow( $item, $parentMap, $purchases, $id )
{
	$padding = '';
	$parent = $item['ps_parent'];
	while ( $parent != 0 )
	{
		$padding .= "<img src='{$this->settings['skin_app_url']}/images/tree.gif' /> ";
		$parent = $purchases[ $parent ]['ps_parent'];
	}

	$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] );
			
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
			
	$IPBHTML .= <<<HTML
		<tr>
			<td>{$appIcon}</td>
			<td>{$item['ps_id']}</td>
			<td>
				{$padding}
				<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=process_renewal&amp;item={$item['ps_id']}&amp;invoice={$id}'>{$item['ps_name']}</a>
				<span class='desctext'>{$cf_sticky}</span>
			</td>
			<td>{$item['ps_start']}</td>
			<td>{$item['ps_expire']}</td>
			<td>{$item['renewal']}</td>
HTML;

	if ( is_array( $parentMap[ $item['ps_id'] ] ) )
	{
		foreach ( $parentMap[ $item['ps_id'] ] as $child )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $child ], $parentMap, $purchases, $id );
		}
	}

	return $IPBHTML;
}

//===========================================================================
// Add Renewal - Enter Details
//===========================================================================
function addRenewal2( $item, $id ) {

$form['renewal'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal', $item['ps_renewals'] );
$form['renewal_unit'] = ipsRegistry::getClass('output')->formDropdown( 'renewal_unit', array( array( 'd', 'Days' ), array( 'w', 'Weeks' ), array( 'm', 'Months' ), array( 'y', 'Years' ) ), $item['ps_renewal_unit'] );
$form['expire_date'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire_date', '', 11 );
$form['renewal_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_price', $item['ps_renewal_price'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_renewal']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_renewal']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_renewal' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<input type='hidden' name='item' value='{$item['ps_id']}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchase_item']}</strong></td>
			<td class='field_field'>{$item['ps_name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchases_renewal_terms']}</strong></td>
			<td class='field_field'>
				{$form['renewal']} {$form['renewal_unit']}<br />
				<span class='desctext'>{$this->lang->words['invoice_renew_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_renew_expire']}</strong></td>
			<td class='field_field'>
				{$form['expire_date']} (MM-DD-YYYY)<br />
				<span class='desctext'>{$this->lang->words['invoice_expire_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['item_renew_price']}</strong></td>
			<td class='field_field'>
				{$form['renewal_price']}<br />
				<span class='desctext'>{$this->lang->words['invoice_renew_price_desc']}</span>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_renewal']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Renewal - Has Existing Renewal
//===========================================================================
function addRenewalExisting( $item, $id ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_renewal']}</h2>
</div>

<div class='information-box'>
	{$this->lang->words['err_no_renew_existing']}<br />
	<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$item['ps_invoice_pending']}' target='_blank'>{$this->lang->words['view_renewal']} &rarr;</a>
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=process_renewal&amp;item={$item['ps_id']}&invoice={$id}&amp;overrideExisting=1'>{$this->lang->words['renew_existing_1']}</a></div>
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=resend_invoice&amp;id={$item['ps_invoice_pending']}'>{$this->lang->words['renew_existing_2']}</a></div>
</div>
<br />


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Charge
//===========================================================================
function addCharge( $id ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name' );
$form['cost'] = ipsRegistry::getClass('output')->formSimpleInput( 'cost' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_charge']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=charge' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['item_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['cost']}</strong></td>
			<td class='field_field'>{$form['cost']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Credit
//===========================================================================
function addCredit( $id ) {

	$form['name'] = ipsRegistry::getClass('output')->formInput( 'name' );
	$form['cost'] = ipsRegistry::getClass('output')->formSimpleInput( 'cost' );

	$IPBHTML = "";
	//--starthtml--//

	$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_credit']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=topup' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['item_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['cost']}</strong></td>
			<td class='field_field'>{$form['cost']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

	//--endhtml--//
	return $IPBHTML;
}

//===========================================================================
// Add Donation
//===========================================================================
function addDonation( $id, $goals ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name' );
$form['cost'] = ipsRegistry::getClass('output')->formSimpleInput( 'cost' );
$form['goal'] =  ipsRegistry::getClass('output')->formDropdown( 'goal', $goals );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_donation']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=donation' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_donate_amount']}</strong></td>
			<td class='field_field'>{$form['cost']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_donate_goal']}</strong></td>
			<td class='field_field'>{$form['goal']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Generate Invoice
//===========================================================================
function generateInvoicePopup() {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add' method='post'>
<div class='acp-box'>
	<h3>{$this->lang->words['invoice_generate']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_name']}</strong></td>
			<td class='field_field'><input name='name' id='altcontact' /></td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['invoice_generate']}' class='realbutton' />
	</div>
</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Mark Unpaid
//===========================================================================
function markUnpaid( $invoice, $info, $otherTransactionAmount ) {

if ( $this->request['status'] )
{
	$title = "{$this->lang->words['invoice_prefix']}{$invoice->title}";
	$blurb = $this->lang->words['invoice_unpaid_blurb'];
}
else
{
	$title = "{$this->lang->words['transaction']} #{$this->request['transaction']}";
	$blurb = $this->lang->words['invoice_unpaid_transblurb'];
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<style type='text/css'>
	.cons {
		list-style: square;
		list-style-position: inside;
		padding: 5px;
	}
</style>

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='warning'>
	{$blurb}
	<br />
	<br />
	<ul class='cons'>

HTML;

	if ( !empty( $info['purchases'] ) )
	{
		$IPBHTML .= <<<HTML
		<li>
			{$this->lang->words['invoice_unpaid_purchases']}
			<ul class='cons'>
HTML;
		foreach ( $info['purchases'] as $purchase )
		{
			$IPBHTML .= <<<HTML
				<li style='padding:5px'>
					<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$purchase['ps_id']}'>{$purchase['ps_name']}</a>
HTML;
				if ( $purchase['ps_member'] != $invoice->member )
				{
					$newMember	= customer::load( $purchase['ps_member'] )->data;
					$IPBHTML .= <<<HTML
					<div class='warning'>{$this->lang->words['invoice_unpaid_transferred']}<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$newMember['member_id']}'>{$newMember['_name']}</a></div>
HTML;
				}
				$IPBHTML .= <<<HTML
				</li>
HTML;
		}
		$IPBHTML .= <<<HTML
			</ul>
		</li>
HTML;
	}
	
	if ( isset( $info['credit'] ) )
	{
		$member	= customer::load( $invoice->member )->data;
		$IPBHTML .= <<<HTML
		<li>
			{$this->lang->words['invoice_unpaid_credit']}
			<ul class='cons'>
				<li style='padding:5px'>
					<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$member['member_id']}'>{$member['_name']}</a>{$this->lang->words['invoice_unpaid_gain']}{$this->registry->getClass('class_localization')->formatMoney( $info['credit'] )}
				</li>
			</ul>
		</li>
HTML;
	}

	if ( !empty( $info['renewals'] ) )
	{
		$IPBHTML .= <<<HTML
		<li>
			{$this->lang->words['invoice_unpaid_renewals']}
			<ul class='cons'>
HTML;
		foreach ( $info['renewals'] as $purchase )
		{
			$IPBHTML .= <<<HTML
				<li style='padding:5px'>
					<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$purchase['data']['ps_id']}'>{$purchase['data']['ps_name']}</a>{$this->lang->words['invoice_unpaid_newexpire']}{$this->registry->getClass('class_localization')->getDate( $purchase['new_expire_date'], 'JOINED' )}
HTML;
				if ( $purchase['data']['ps_member'] != $invoice->member )
				{
					$newMember	= customer::load( $purchase['data']['ps_member'] )->data;
					$IPBHTML .= <<<HTML
					<div class='warning'>{$this->lang->words['invoice_unpaid_transferred']}<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$newMember['member_id']}'>{$newMember['_name']}</a></div>
HTML;
				}
				$IPBHTML .= <<<HTML
				</li>
HTML;
		}
		$IPBHTML .= <<<HTML
			</ul>
		</li>
HTML;
	}

	if ( !empty( $info['commission'] ) )
	{
		$IPBHTML .= <<<HTML
		<li>
			{$this->lang->words['invoice_unpaid_commission']}
			<ul class='cons'>
HTML;
		foreach ( $info['commission'] as $data )
		{
			$IPBHTML .= <<<HTML
				<li style='padding:5px'>
					<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$data['member']['member_id']}'>{$data['member']['_name']}</a>{$this->lang->words['invoice_unpaid_lose']}{$this->registry->getClass('class_localization')->formatMoney( $data['amount'] )}
				</li>
HTML;
		}
		$IPBHTML .= <<<HTML
			</ul>
		</li>
HTML;
	}
	
	if ( !empty( $info['shiporders'] ) )
	{
		$IPBHTML .= <<<HTML
		<li>
			{$this->lang->words['invoice_unpaid_shipping']}
			<ul class='cons'>
HTML;
		foreach ( $info['shiporders'] as $data )
		{
			$IPBHTML .= <<<HTML
				<li style='padding:5px'>
					<a href='{$this->settings['base_url']}&amp;module=payments&section=shiporders&do=view&id={$data['o_id']}'>{$this->lang->words['shipping_order_no']}{$data['o_id']}</a>
HTML;
				if ( $data['o_status'] == 'done' )
				{
					$IPBHTML .= <<<HTML
					<div class='warning'>{$this->lang->words['invoice_unpaid_shipped']}</div>
HTML;
				}
				$IPBHTML .= <<<HTML
				</li>
HTML;
		}
		$IPBHTML .= <<<HTML
			</ul>
		</li>
HTML;
	}
	
$IPBHTML .= <<<HTML
	</ul>
</div>
<br />
HTML;

if ( !$otherTransactionAmount )
{
$IPBHTML .= <<<HTML
<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=unpaid&status={$this->request['status']}&transaction={$this->request['transaction']}&id={$invoice->id}&amp;go=1&amp;refund={$this->request['refund']}&amp;othertrans='>{$this->lang->words['continue']}</a></div>
</div>

HTML;
}
else
{
$IPBHTML .= <<<HTML
<br /><br />
<div class='information-box'>
HTML;
	$IPBHTML .= sprintf( ( $this->request['transaction'] ? $this->lang->words['invoice_unpaid_othertrans'] : $this->lang->words['invoice_unpaid_trans'] ), $this->lang->formatMoney( $otherTransactionAmount ), "{$this->settings['base_url']}&amp;module=payments&section=invoices&do=view_invoice&id={$invoice->id}" );
$IPBHTML .= <<<HTML
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=unpaid&status={$this->request['status']}&transaction={$this->request['transaction']}&id={$invoice->id}&amp;go=1&amp;refund={$this->request['refund']}&amp;othertrans=refuse'>{$this->lang->words['invoice_unpaid_refuse']}</a></div>
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=unpaid&status={$this->request['status']}&transaction={$this->request['transaction']}&id={$invoice->id}&amp;go=1&amp;refund={$this->request['refund']}&amp;othertrans=refund'>{$this->lang->words['invoice_unpaid_refund']}</a><br /><span class='desctext' style='font-size: 0.7em'>{$this->lang->words['invoice_unpaid_refund_desc']}</span></div>
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=unpaid&status={$this->request['status']}&transaction={$this->request['transaction']}&id={$invoice->id}&amp;go=1&amp;refund={$this->request['refund']}&amp;othertrans=credit'>{$this->lang->words['invoice_unpaid_credit']}</a></div>
</div>
<br />
HTML;

}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Delete Explaination
//===========================================================================
function delete( $invoice ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['invoice_prefix']}{$invoice->title}</h2>
</div>
<div class='information-box'>
	{$this->lang->words['invoice_delete_blurb']}
</div>
<br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=delete&id={$invoice->id}&amp;go=1'>{$this->lang->words['invoice_delete_yes']}</a></div>
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&section=invoices&do=view_invoice&id={$invoice->id}'>{$this->lang->words['invoice_delete_no']}</a></div>
</div>


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Print
//===========================================================================
function printInvoice( $invoice, $items, $member, $totals, $header, $footer ) {

$address = $member['cm_address_2'] ? $member['cm_address_1'] . '<br />' . $member['cm_address_2'] : $member['cm_address_1'];
$state = $member['cm_state'] ? $member['cm_state'] . '<br />' : '';

$status = '<strong>' . strtoupper( $this->lang->words['istatus_'.$invoice->status] ) . '</strong>';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset={$this->settings['gb_char_set']}" />
		<title>[#{$invoice->id}] {$invoice->title}</title>
		
		<style type="text/css">
		body {
		  font-family:Tahoma;
		}
		
		#page {
		  width:1000px;
		  margin:0 auto;
		  padding:15px;
		
		}
		
		table {
		  width:100%;
		}
		
		td {
		padding:5px;
		}
		</style>
		
		<script type='text/javascript'>
			javascript:window.print()
		</script>
		
	</head>
	
	<body>
		<div id="page">
		
			<div>
				<div style='float:left'>
					<h1>[#{$invoice->id}] {$invoice->title}</h1>
HTML;
				if ( $invoice->po )
				{
					$IPBHTML .= <<<HTML
					{$this->lang->words['invoice_po']}: {$invoice->po}<br /><br />
HTML;
				}
					
				$IPBHTML .= <<<HTML
				</div>
				
				<div style='float:right; text-align: right'>
					{$header}
					<br /><br /><br />
					{$this->registry->getClass('class_localization')->getDate( $invoice->date, 'LONG', TRUE )}<br />
					{$status}
				</div>
				
				<br style='clear:both' />
				
				</div>
		
			<p>
				{$member['cm_first_name']} {$member['cm_last_name']}<br />
				{$address}<br />
				{$member['cm_city']}<br />
				{$state}
				{$member['cm_zip']}<br />
				{$this->lang->words[ 'nc_' . $member['cm_country'] ]}
			</p>
			
			<hr />
			
			<div id='content'>
			    <table>
					<tr>
						<td style='font-weight:bold'>{$this->lang->words['item_name']}</td>
						<td style='font-weight:bold'>{$this->lang->words['item_cfields']}</td>
						<td style='font-weight:bold'>{$this->lang->words['item_unit']}</td>
						<td style='font-weight:bold'>{$this->lang->words['item_qty']}</td>
						<td style='font-weight:bold; text-align: right'>{$this->lang->words['item_line']}</td>
					</tr>
HTML;
	
	foreach( $items as $item )
	{
		$IPBHTML .= <<<HTML
			<tr>
				<td>{$item['itemName']}</td>
				<td>{$item['cfields']}</td>
				<td>{$item['cost']}
				<td>{$item['quantity']}</td>
				<td style='text-align: right'>{$item['line']}</td>
			</tr>
HTML;
	}
	
$IPBHTML .= <<<HTML
				</table>
				<hr />
		  </div>
		  <div style='text-align:right; padding:5px; line-height: 25px'>
HTML;
		if ( $invoice->discount or $totals['tax'] )
		{
			$IPBHTML .= <<<HTML
		  	<strong>{$this->lang->words['invoice_subtotal']}</strong>{$totals['subtotal']}<br />
HTML;
			if ( $invoice->discount )
			{
				$IPBHTML .= <<<HTML
			<strong>{$invoice->discount}{$this->lang->words['item_discount']}</strong>{$totals['discount']}<br />
HTML;
			}
			
			if ( $totals['tax'] )
			{
				$IPBHTML .= <<<HTML
		  	<strong>{$this->lang->words['invoice_tax']}</strong>{$totals['tax']}<br />
HTML;
			}
		}
		$IPBHTML .= <<<HTML
		  	<strong>{$this->lang->words['invoice_total']}</strong>{$totals['grandtotal']}<br />
		  </div>
HTML;
		if ( $invoice->notes )
		{
			$IPBHTML .= <<<HTML
			<hr />
			<p style='text-align: center'>{$invoice->notes}</p>
HTML;
		}
		if ( $footer )
		{
			$IPBHTML .= <<<HTML
			<hr />
			<p style='text-align: center'>{$footer}</p>
			<hr />
HTML;
		}
			
		$IPBHTML .= <<<HTML
		  </p>
		</div>
	</body>

</html>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Custom
//===========================================================================
function addCustom( $invoice, $options ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['package_add']}</h2>
</div>

<div class='information-box'>{$this->lang->words['invoice_select_custom']}</div>
<br /><br />

HTML;

	foreach ( $options as $id => $table )
	{
		$IPBHTML .= <<<HTML
		<div class='redirector'>
			<div class='info'><img src='{$this->settings['skin_app_url']}/images/nexus_icons/{$id}.png' /> <a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=add_item&amp;item_app=nexus&amp;item_type=custom&amp;invoice={$invoice}&amp;type={$id}'>{$this->lang->words[ $id ]}</a></div>
		</div>
		<br /><br />
HTML;

	}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Custom Package Form
//===========================================================================
function addCustom2( $invoice, $type, $memberGroups, $permissionSets, $supportDepartments, $severities, $tax, $attachments, $postKey, $extraData ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', '' );
$form['base_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'base_price', '', 8 );
$form['renewal'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal', '' );
$form['renewal_unit'] = ipsRegistry::getClass('output')->formDropdown( 'renewal_unit', array( array( 'd', $this->lang->words['renew_term_days'] ), array( 'w', $this->lang->words['renew_term_weeks'] ), array( 'm', $this->lang->words['renew_term_months'] ), array( 'y', $this->lang->words['renew_term_years'] ) ), 'm' );
$form['renewal_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_price', '', 8 );
$form['renewal_days'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_days', '' );
$form['primary_promote'] = ipsRegistry::getClass('output')->formDropdown( 'primary_promote', array_merge( array( array( 0, $this->lang->words['package_nogroupchange'] ) ), $memberGroups ), '' );
$form['secondary_promote'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'secondary_promote[]', NULL, TRUE );
$form['perms_promote'] = ipsRegistry::getClass('output')->formMultiDropdown( 'perms_promote[]', $permissionSets, NULL );
$form['return_primary'] = ipsRegistry::getClass('output')->formYesNo( 'return_primary', 1 );
$form['return_secondary'] = ipsRegistry::getClass('output')->formYesNo( 'return_secondary', 1 );
$form['return_perm'] = ipsRegistry::getClass('output')->formYesNo( 'return_perm', 1 );
$form['custom_module'] = ipsRegistry::getClass('output')->formSimpleInput( 'custom_module', '', 20 );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$form['page'] = $editor->show('page');

$form['support'] = ipsRegistry::getClass('output')->formYesNo( 'support', 0 );
$form['support_department'] = ipsRegistry::getClass('output')->formDropdown( 'support_department', $supportDepartments, 0 );
$form['support_severity'] = ipsRegistry::getClass('output')->formDropdown( 'support_severity', $severities, 0 );

switch ( $type )
{
	case 'product':
		$form['lkey'] = ipsRegistry::getClass('output')->formDropdown( 'lkey', $extraData['lkeyOptions'], '0' );
		$form['lkey_identifier'] = ipsRegistry::getClass('output')->formDropdown( 'lkey_identifier', $extraData['identifierOptions'], '0' );
		$form['lkey_uses'] = ipsRegistry::getClass('output')->formSimpleInput( 'lkey_uses', '1', 4 );
		$extraTab = <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['lkey']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['lkey']}</strong></td></td>
			<td class='field_field'>{$form['lkey']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['lkey_identifier']}</strong></td>
			<td class='field_field'>
				{$form['lkey_identifier']}<br />
				<span class='desctext'>{$this->lang->words['lkey_identifier_blurb']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['lkey_uses']}</strong></td>
			<td class='field_field'>
				{$form['lkey_uses']}<br />
				<span class='desctext'>{$this->lang->words['lkey_uses_blurb']}</span>
			</td>
		</tr>

HTML;
		break;
		
	case 'ad':
		$form['location'] = ipsRegistry::getClass('output')->formMultiDropdown( 'location[]', $extraData['locations'], array() );
		$form['location_custom'] = ipsRegistry::getClass('output')->formInput( 'location_custom', '' );
		$form['exempt'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'exempt[]', array(), TRUE );
		$form['expire'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire', 0 );
		$form['expire_unit'] = ipsRegistry::getClass('output')->formDropDown( 'expire_unit', array(
			array( 'i', $this->lang->words['ad__impressions'] ),
			array( 'c', $this->lang->words['ad__clicks'] ),
			), 'i' );
		$form['max_height'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_height', 0 );
		$form['max_width'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_width', 0 );
		$extraTab = <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['ad_settings']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_locations']}</strong></td>
			<td class='field_field'>
				{$form['location']}<br />
				<span class='desctext'>{$this->lang->words['adpack_locations_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_custom_locations']}</strong></td>
			<td class='field_field'>
				{$form['location_custom']}<br />
				<span class='desctext'>
					{$this->lang->words['ad_custom_locations_desc']}<br />
					<a href='http://external.ipslink.com/ipboard30/landing/?p=nexus-external-ads' target='_blank'>{$this->lang->words['ad_custom_locations_moreinfo']}</a>
				</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_exempt']}</strong></td>
			<td class='field_field'>
				{$form['exempt']}<br />
				<span class='desctext'>{$this->lang->words['ad_exempt_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_expire']}</strong></td>
			<td class='field_field'>
				{$form['expire']} {$form['expire_unit']}<br />
				<span class='desctext'>{$this->lang->words['ad_expire_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['adpack_max_height']}</strong></td>
			<td class='field_field'>
				{$form['max_height']}<br />
				<span class='desctext'>{$this->lang->words['adpack_dims_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['adpack_max_width']}</strong></td>
			<td class='field_field'>
				{$form['max_width']}<br />
				<span class='desctext'>{$this->lang->words['adpack_dims_desc']}</span>
			</td>
		</tr>
HTML;
		break;
		
	case 'hosting':
		$form['queue'] = ipsRegistry::getClass('output')->formDropDown( 'queue', $extraData, 0 );
		$form['quota'] = ipsRegistry::getClass('output')->formInput( 'quota', '-1' );
		$form['bwlimit'] = ipsRegistry::getClass('output')->formInput( 'bwlimit', '-1' );
		$form['ip'] = ipsRegistry::getClass('output')->formYesNo( 'ip', 0 );
		$form['cgi'] = ipsRegistry::getClass('output')->formYesNo( 'cgi', 0 );
		$form['frontpage'] = ipsRegistry::getClass('output')->formYesNo( 'frontpage', 0 );
		$form['hasshell'] = ipsRegistry::getClass('output')->formYesNo( 'hasshell', 0 );
		$form['maxftp'] = ipsRegistry::getClass('output')->formInput( 'maxftp', '-1' );
		$form['maxsql'] = ipsRegistry::getClass('output')->formInput( 'maxsql', '-1' );
		$form['maxpop'] = ipsRegistry::getClass('output')->formInput( 'maxpop', '-1' );
		$form['maxlst'] = ipsRegistry::getClass('output')->formInput( 'maxlst', '-1' );
		$form['maxsub'] = ipsRegistry::getClass('output')->formInput( 'maxsub', '-1' );
		$form['maxpark'] = ipsRegistry::getClass('output')->formInput( 'maxpark', '-1' );
		$form['maxaddon'] = ipsRegistry::getClass('output')->formInput( 'maxaddon', '-1' );
		$extraTab = <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['hosting_settings']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_queue']}</strong></td>
			<td class='field_field'>{$form['queue']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_quota']}</strong></td>
			<td class='field_field'>
				{$form['quota']}<br />
				<span class='desctext'>{$this->lang->words['hosting_quota_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_bwlimit']}</strong></td>
			<td class='field_field'>
				{$form['bwlimit']}<br />
				<span class='desctext'>{$this->lang->words['hosting_quota_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_ip']}</strong></td>
			<td class='field_field'>{$form['ip']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_cgi']}</strong></td>
			<td class='field_field'>{$form['cgi']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_frontpage']}</strong></td>
			<td class='field_field'>{$form['frontpage']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_hasshell']}</strong></td>
			<td class='field_field'>{$form['hasshell']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxftp']}</strong></td>
			<td class='field_field'>
				{$form['maxftp']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxsql']}</strong></td>
			<td class='field_field'>
				{$form['maxsql']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxpop']}</strong></td>
			<td class='field_field'>
				{$form['maxpop']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxlst']}</strong></td>
			<td class='field_field'>
				{$form['maxlst']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxsub']}</strong></td>
			<td class='field_field'>
				{$form['maxsub']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxpark']}</strong></td>
			<td class='field_field'>
				{$form['maxpark']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_maxaddon']}</strong></td>
			<td class='field_field'>
				{$form['maxaddon']}<br />
				<span class='desctext'>{$this->lang->words['hosting_allowances_desc']}</span>
			</td>
		</tr>
		
HTML;
		break;
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=custom&amp;invoice={$invoice}' method='post' enctype='multipart/form-data'>
<input type='hidden' name='post_key' value='{$postKey}' />
<input type='hidden' name='type' value='{$type}' />
<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_item']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_1']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_name']}</strong></td>
				<td class='field_field'>{$form['name']}</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_2']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_base_price']}</strong></td>
				<td class='field_field'>
					{$form['base_price']}<br />
					<span class='desctext'>{$this->lang->words['options_explaination']}</span>
				</td>
			</tr>
			<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_renewals']}</strong></td>
			<td class='field_field'>
				{$this->lang->words['every']} {$form['renewal']} {$form['renewal_unit']}
			</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewal_price']}</strong></td>
				<td class='field_field'>
					{$form['renewal_price']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewal_days']}</strong></td>
				<td class='field_field'>
					{$form['renewal_days']}<br />
					<span class='desctext'>{$this->lang->words['package_renewal_days_desc']}</span>
				</td>
			</tr>
			{$extraTab}
			<tr>
				<th colspan='2'>{$this->lang->words['package_member_changes']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_primary']}</strong></td>
				<td class='field_field'>
					{$form['primary_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_primary_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_primary_return']}</strong></td>
				<td class='field_field'>
					{$form['return_primary']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_secondary']}</strong></td>
				<td class='field_field'>
					{$form['secondary_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_secondary_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_secondary_return']}</strong></td>
				<td class='field_field'>
					{$form['return_secondary']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_perms']}</strong></td>
				<td class='field_field'>
					{$form['perms_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_perms_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_perms_return']}</strong></td>
				<td class='field_field'>
					{$form['return_perm']}
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_custom_actions']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_action']}</strong></td>
				<td class='field_field'>
					<strong>admin/applications_addon/ips/nexus/sources/actions/{$form['custom_module']}.php</strong> {$this->lang->words['package_action_loc']}<br />
					<span class='desctext'><a href='http://www.invisionpower.com/support/guides/_/advanced-and-developers/ipnexus/ipnexus-custom-actions-r55' target='_blank'>{$this->lang->words['package_action_desc']}</a></span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_6']}</th>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['package_tab_6']}</strong><br />
					<span class='desctext'>{$this->lang->words['package_page_desc']}</span>
				</td>
				<td class='field_field'>
					{$form['page']}
					<span id='attachment_wrap'> <strong>{$this->lang->words['req_reply_attachments']}</strong></span>
					{$attachments}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support']}</strong></td>
				<td class='field_field'>{$form['support']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support_dpt']}</strong></td>
				<td class='field_field'>
					{$form['support_department']}<br />
					<span class='desctext'>{$this->lang->words['package_support_dpt_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support_sev']}</strong></td>
				<td class='field_field'>
					{$form['support_severity']}<br />
					<span class='desctext'>{$this->lang->words['package_support_sev_desc']}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Edit Notes
//===========================================================================
function editNotes( $invoice ) {

$form['po'] = ipsRegistry::getClass('output')->formInput( 'po', $invoice->po );
$form['notes'] = ipsRegistry::getClass('output')->formTextArea( 'notes', str_replace( '<br />', "\n", $invoice->notes ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$invoice->title} [#{$invoice->id}]</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_notes']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=editNotes_do' method='post'>
	<input type='hidden' name='id' value='{$invoice->id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_po']}</strong></td>
			<td class='field_field'>{$form['po']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['invoice_notes']}</strong></td>
			<td class='field_field'>
				{$form['notes']}<br />
				<span class='desctext'>{$this->lang->words['invoice_notes_warning']}</span>
			</td>
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

//===========================================================================
// Dedicated Server Form
//===========================================================================
function addDedi( $invoice, $servers, $memberGroups, $permissionSets, $supportDepartments, $severities, $tax, $attachments, $postKey ) {

$form['server'] = ipsRegistry::getClass('output')->formDropDown( 'server', $servers );
$form['base_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'base_price', '', 8 );
$form['renewal'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal', '' );
$form['renewal_unit'] = ipsRegistry::getClass('output')->formDropdown( 'renewal_unit', array( array( 'd', $this->lang->words['renew_term_days'] ), array( 'w', $this->lang->words['renew_term_weeks'] ), array( 'm', $this->lang->words['renew_term_months'] ), array( 'y', $this->lang->words['renew_term_years'] ) ), 'm' );
$form['renewal_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_price', '', 8 );
$form['renewal_days'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_days', '' );
$form['primary_promote'] = ipsRegistry::getClass('output')->formDropdown( 'primary_promote', array_merge( array( array( 0, $this->lang->words['package_nogroupchange'] ) ), $memberGroups ), '' );
$form['secondary_promote'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'secondary_promote[]', NULL, TRUE );
$form['perms_promote'] = ipsRegistry::getClass('output')->formMultiDropdown( 'perms_promote[]', $permissionSets, NULL );
$form['return_primary'] = ipsRegistry::getClass('output')->formYesNo( 'return_primary', 1 );
$form['return_secondary'] = ipsRegistry::getClass('output')->formYesNo( 'return_secondary', 1 );
$form['return_perm'] = ipsRegistry::getClass('output')->formYesNo( 'return_perm', 1 );
$form['custom_module'] = ipsRegistry::getClass('output')->formSimpleInput( 'custom_module', '', 20 );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$form['page'] = $editor->show('page');

$form['support'] = ipsRegistry::getClass('output')->formYesNo( 'support', 0 );
$form['support_department'] = ipsRegistry::getClass('output')->formDropdown( 'support_department', $supportDepartments, 0 );
$form['support_severity'] = ipsRegistry::getClass('output')->formDropdown( 'support_severity', $severities, 0 );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=dedi&amp;invoice={$invoice}' method='post' enctype='multipart/form-data'>
<input type='hidden' name='post_key' value='{$postKey}' />
<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_item']}</h3>
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_1']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['server']}</strong></td>
				<td class='field_field'>{$form['server']}</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_2']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_base_price']}</strong></td>
				<td class='field_field'>
					{$form['base_price']}<br />
					<span class='desctext'>{$this->lang->words['options_explaination']}</span>
				</td>
			</tr>
			<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_renewals']}</strong></td>
			<td class='field_field'>
				{$this->lang->words['every']} {$form['renewal']} {$form['renewal_unit']}<br />
				<span class='desctext'>{$this->lang->words['package_renewals_desc']}<br />{$this->lang->words['options_explaination']}</span>
			</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewal_price']}</strong></td>
				<td class='field_field'>
					{$form['renewal_price']}<br />
					<span class='desctext'>{$this->lang->words['package_renewal_price_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewal_days']}</strong></td>
				<td class='field_field'>
					{$form['renewal_days']}<br />
					<span class='desctext'>{$this->lang->words['package_renewal_days_desc']}</span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_member_changes']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_primary']}</strong></td>
				<td class='field_field'>
					{$form['primary_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_primary_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_primary_return']}</strong></td>
				<td class='field_field'>
					{$form['return_primary']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_secondary']}</strong></td>
				<td class='field_field'>
					{$form['secondary_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_secondary_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_secondary_return']}</strong></td>
				<td class='field_field'>
					{$form['return_secondary']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_perms']}</strong></td>
				<td class='field_field'>
					{$form['perms_promote']}<br />
					<span class='desctext'>{$this->lang->words['package_perms_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_perms_return']}</strong></td>
				<td class='field_field'>
					{$form['return_perm']}
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_custom_actions']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_action']}</strong></td>
				<td class='field_field'>
					<strong>admin/applications_addon/ips/nexus/sources/actions/{$form['custom_module']}.php</strong> {$this->lang->words['package_action_loc']}<br />
					<span class='desctext'><a href='http://www.invisionpower.com/support/guides/_/advanced-and-developers/ipnexus/ipnexus-custom-actions-r55' target='_blank'>{$this->lang->words['package_action_desc']}</a></span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_6']}</th>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['package_tab_6']}</strong><br />
					<span class='desctext'>{$this->lang->words['package_page_desc']}</span>
				</td>
				<td class='field_field'>
					{$form['page']}
					<span id='attachment_wrap'> <strong>{$this->lang->words['req_reply_attachments']}</strong></span>
					{$attachments}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support']}</strong></td>
				<td class='field_field'>{$form['support']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support_dpt']}</strong></td>
				<td class='field_field'>
					{$form['support_department']}<br />
					<span class='desctext'>{$this->lang->words['package_support_dpt_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_support_sev']}</strong></td>
				<td class='field_field'>
					{$form['support_severity']}<br />
					<span class='desctext'>{$this->lang->words['package_support_sev_desc']}</span>
				</td>
			</tr>
		</table>
	</div>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Domain
//===========================================================================
function addDomain( $id, $tldOptions ) {

$form['sld'] = ipsRegistry::getClass('output')->formInput( 'sld' );
$form['tld'] = ipsRegistry::getClass('output')->formDropdown( 'tld', $tldOptions );
$form['nameservers'] = ipsRegistry::getClass('output')->formTextArea( 'nameservers', $this->settings['nexus_hosting_nameservers'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_domain']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=domain' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['domain']}</strong></td>
			<td class='field_field'>{$form['sld']} {$form['tld']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hserv_nameservers']}</strong></td>
			<td class='field_field'>{$form['nameservers']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Gift Voucher
//===========================================================================
function addGiftVoucher( $id ) {

$form['amount'] = ipsRegistry::getClass('output')->formSimpleInput( 'amount' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['invoice_add_item']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['invoice_add_giftvoucher']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=save_item&amp;item_app=nexus&amp;item_type=giftvoucher' method='post'>
	<input type='hidden' name='invoice' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['gv_amount']}</strong></td>
			<td class='field_field'>{$form['amount']} {$this->settings['nexus_currency']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['invoice_add_item']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Invoice Table
//===========================================================================
function invoiceTable( $invoice ) {
$IPBHTML = "";
//--starthtml--//

if ( !$invoice->id )
{
	return '';
}

$IPBHTML .= <<<HTML
<table class='ipsTable'>
		<tr>
			<th style='width:20px'>&nbsp;</th>
			<th>{$this->lang->words['invoice_item']}</th>
			<th>{$this->lang->words['item_cfields']}</th>
			<th>{$this->lang->words['item_unit']}</th>
			<th style='text-align: center'>{$this->lang->words['item_qty']}</th>
			<th style='text-align: right; padding-right: 10px;'>{$this->lang->words['item_line']}</th>
		</tr>
HTML;

$cfields = $this->cache->getCache('package_fields');
require_once( IPSLib::getAppDir( 'nexus' ) . '/sources/customFields.php' );/*noLibHook*/
foreach( $invoice->items as $k => $item )
{
	if ( is_array( $item['prices'] ) )
	{
		$item['cost'] = invoice::formatPrices( $item['prices'] );
	}
	else
	{
		$item['cost'] = ipsRegistry::getClass('class_localization')->formatMoney( $item['cost'], FALSE );
	}
	
	$fields = array();
	if ( is_array( $item['cfields'] ) )
	{
		foreach ( $item['cfields'] as $id => $value )
		{
			if ( isset( $cfields[ $id ] ) )
			{
				$_f = customField::factory( $cfields[ $id ] );
				$_f->currentValue = $value;
				$fields[ $item['itemID'] ] .= "{$cfields[ $id ]['cf_name']}: " . (string) $_f . '<br />';
			}
		}
	}
	$item['cfields'] = implode( '<br />', $fields );
	
	$line = $invoice->getLinePrice( $k );
	$item['line'] = ipsRegistry::getClass('class_localization')->formatMoney( $line, FALSE );

	$class='';
	if ( $item['app'] == 'nexus' and $item['type'] == 'credit' )
	{
		$class=" class='_amber'";
		$appIcon = '&nbsp;';
	}
	else
	{
		$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['app'], $item['type'], $item['cost'], $item['renewal'] );
	}
			
	if ( isset( $item['adminURI'] ) )
	{
		$item['itemName'] = "<a href='{$this->settings['base_url']}{$item['adminURI']}' target='_blank'>{$item['itemName']}</a>";
	}
	elseif ( $item['itemURI'] )
	{
		$item['itemName'] = "<a href='{$this->settings['board_url']}/index.php?{$item['itemURI']}' target='_blank'>{$item['itemName']}</a>";
	}
	elseif ( $item['act'] == 'renewal' )
	{
		$item['itemName'] .= " ({$item['itemID']})";
	}
	
	$extra = isset( $item['extra']['domain'] ) ? "<br /><span class='desctext'>{$item['extra']['domain']}</span>" : '';
					
	$IPBHTML .= <<<HTML
		<tr{$class}>
			<td>{$appIcon}</td>
			<td>
				<span class='larger_text'>{$item['itemName']}</span>
				{$extra}
			</td>
			<td>{$item['cfields']}</td>
			<td>{$item['cost']}</td>
			<td style='text-align: center'>{$item['quantity']}</td>
			<td style='text-align: right; padding-right: 10px;'><strong>{$item['line']}</strong></td>
		</tr>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
HTML;

//--endhtml--//
return $IPBHTML;
}


}