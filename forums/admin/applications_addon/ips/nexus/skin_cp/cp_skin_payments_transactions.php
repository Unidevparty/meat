<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - Transactions
 * Last Updated: $Date: 2013-10-18 09:46:18 -0400 (Fri, 18 Oct 2013) $
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		14th January 2010
 * @version		$Revision: 12386 $
 */
 
class cp_skin_payments_transactions
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
// Manage Transactions
//===========================================================================
function transactions( $transactions, $pagination, $methods, $fromSearch, $currentSearch ) {

$search['status'] = ipsRegistry::getClass('output')->formMultiDropdown(
	'status[]', array(
		array( 'okay', $this->lang->words['tstatus_okay'] ),
		array( 'hold', $this->lang->words['tstatus_hold'] ),
		array( 'revw', $this->lang->words['tstatus_revw'] ),
		array( 'fail', $this->lang->words['tstatus_fail'] ),
		array( 'rfnd', $this->lang->words['tstatus_rfnd'] ),
		array( 'pend', $this->lang->words['tstatus_pend'] )
	), $currentSearch['status'], 6 );

$search['member'] = ipsRegistry::getClass('output')->formInput( 'member', $currentSearch['member'], 'member' );
$search['amount1'] = ipsRegistry::getClass('output')->formDropdown( 'amount1', array( array( 'eq', $this->lang->words['equals'] ), array( 'gt', $this->lang->words['gt'] ), array( 'lt', $this->lang->words['lt'] ) ), $currentSearch['amount1'] );
$search['amount2'] = ipsRegistry::getClass('output')->formSimpleInput( 'amount2', $currentSearch['amount2'] );
$search['method'] = ipsRegistry::getClass('output')->formMultiDropdown( 'method[]', $methods, $currentSearch['method'] );
$search['date1'] = ipsRegistry::getClass('output')->formSimpleInput( 'date1', $currentSearch['date1'], 11 );
$search['date2'] = ipsRegistry::getClass('output')->formSimpleInput( 'date2', $currentSearch['date2'], 11 );
$search['sort1'] = ipsRegistry::getClass('output')->formDropdown( 'sort1', array( array( 't_invoice', $this->lang->words['transaction_invoiceid'] ), array( 't_amount', $this->lang->words['transaction_amount'] ), array( 't_date', $this->lang->words['transaction_date'] ) ), $currentSearch['sort1'] ? $currentSearch['sort1'] : 't_date' );
$search['sort2'] = ipsRegistry::getClass('output')->formDropdown( 'sort2', array( array( 'asc', $this->lang->words['asc'] ), array( 'desc', $this->lang->words['desc'] ) ), $currentSearch['sort2'] ? $currentSearch['sort2'] : 'desc' );

$menuKey = 0;

$searchBoxStyle = $fromSearch ? '' : 'display:none; ';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$this->lang->words['transactions']}</h2>
</div>

EOF;

$IPBHTML .= <<<EOF
<div class='acp-box'>
 	<h3>{$this->lang->words['transactions']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th>{$this->lang->words['transaction_']}</th>
			<th>{$this->lang->words['transaction_method']}</th>
			<th>{$this->lang->words['transaction_member']}</th>
			<th style='text-align: center'>{$this->lang->words['transaction_amount']}</th>
			<th>{$this->lang->words['transaction_date']}</th>
			<th class='col_buttons'>&nbsp;</th>
		</tr>
EOF;

if ( !empty( $transactions ) )
{
foreach ( $transactions as $id => $data )
{
	$row = ($row == 'acp-row-off') ? 'acp-row-on' : 'acp-row-off';
	
	$menuKey++;
	switch ( $data['status'] ) { case 'okay': $badgeColor = 'green'; break; case 'hold': case 'pend': case 'wait': $badgeColor = 'grey'; break; case 'fail': case 'rfnd': $badgeColor = 'red'; break; case 'revw': $badgeColor = 'purple'; }
		
	$IPBHTML .= <<<EOF
		<tr class='ipsControlRow'>
			<td>
				<span class='ipsBadge badge_{$badgeColor}' style='width: 100%; text-align: center'>
					{$this->lang->words[ 'tstatus_'. $data['status'] ]}
				</span>
			</td>
			<td>
				<span class='larger_text'>{$id}</span> <a title='{$this->lang->words['view_details']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=view&amp;id={$id}'><span class='desctext'>{$this->lang->words['transaction_view']}</span></a>
			</td>
			<td>{$data['method']}</td>
			<td><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$data['member']['member_id']}'>{$data['member']['_name']}</a></td>
			<td style='text-align: center'>
				<strong>{$data['amount']}</strong>
EOF;
				if ( $data['invoiceID'] )
				{
					$IPBHTML .= <<<EOF
				<span class='desctext' style='font-size: 11px'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$data['invoiceID']}'>{$this->lang->words['invoice_view']}</a></span>
EOF;
				}
				else
				{
					$IPBHTML .= <<<EOF
				<span class='desctext' style='font-size: 11px'>({$this->lang->words['invoice_deleted']})</span>
EOF;
				}
				
			$IPBHTML .= <<<EOF
			</td>
			<td>{$data['date']}</td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_accept'>
						<a title='{$this->lang->words['transaction_approve']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$id}&amp;status=okay'>{$this->lang->words['transaction_approve']}</a>
					</li>
					<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
						<a href='#'>{$this->lang->words['options']}</a>
					</li>
				</ul>
				<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
					<li class='icon cancel'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$id}&amp;status=fail'>{$this->lang->words['transaction_refuse']}</a></li>
					<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=delete&amp;id={$id}'>{$this->lang->words['delete']}</a></li>
				</ul>
			</td>
		</tr>
EOF;
}

}
else
{
	$IPBHTML .= <<<HTML
			<tr>
				<td colspan='7' class='no_messages'>
					{$this->lang->words['transactions_empty']}
				</td>
			</tr>
HTML;
}

$IPBHTML .= <<<EOF
		
	</table>
</div>
<br />
{$pagination}

<br /><br /><br />

<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions' method='post'>
<input type='hidden' name='search' value='1' />
<div class='acp-box'>
	<h3>{$this->lang->words['transactions_search']}</h3>
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_status']}</strong></td>
			<td class='field_field'>{$search['status']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_member']}</strong></td>
			<td class='field_field'>{$search['member']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_amount']}</strong></td>
			<td class='field_field'>{$search['amount1']} {$search['amount2']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_method']}</strong></td>
			<td class='field_field'>{$search['method']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['date_between']}</strong></td>
			<td class='field_field'>{$search['date1']} {$this->lang->words['and']} {$search['date2']} {$this->lang->words['date_format']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sort']}</strong></td>
			<td class='field_field'>{$search['sort1']} {$search['sort2']}</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['search']}' class='button primary' />
	</div>
</div>
</form>

<script type='text/javascript'>
document.observe("dom:loaded", function(){
	var autoComplete = new ipb.Autocomplete( $('member'), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
});
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View on Hold
//===========================================================================
function pending( $transactions ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$this->lang->words['transactions']}</h2>
</div>

EOF;

foreach ( $transactions as $id => $transaction )
{
	$width = ( empty( $transaction['fraud'] ) ) ? '100' : '50';
		
	$ip = ( $transaction['ip'] ) ? $transaction['ip'] : "<em>{$this->lang->words['trans_no_ip']}</em>";
		
	$alt = ( $transaction['status'] == 'hold' ) ? 'alt' : '';

	$IPBHTML .= <<<EOF
<div class='acp-box {$alt}' id='trans-{$id}' style='margin-bottom: 15px'>
 	<h3>#{$id}</h3>
 	<div style='width: {$width}%; float: left'>
	 	<table class='ipsTable'>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_status']}</strong></td>
				<td class='field_field'>{$transaction['status_desc']}{$transaction['status_by']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_invoice']}</strong></td>
				<td class='field_field'>
					<div class='acp-box'>
						<h3><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$transaction['invoiceID']}' style='color:white'>{$transaction['invoiceTitle']}</a></h3>
						{$transaction['invoiceItems']}
					</div>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_member']}</strong></td>
				<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$transaction['member']['member_id']}'>{$transaction['member']['members_display_name']}</a></td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['billing_address']}</strong></td>
				<td class='field_field'>{$transaction['member']['_address']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['customer_email']}</strong></td>
				<td class='field_field'>{$transaction['member']['email']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_ip']}</strong></td>
				<td class='field_field'>{$ip}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_method']}</strong></td>
				<td class='field_field'>{$transaction['method']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_amount']}</strong></td>
				<td class='field_field'>{$transaction['amount']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_date']}</strong></td>
				<td class='field_field'>{$transaction['date']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_gwid']}</strong></td>
				<td class='field_field'>{$transaction['gwid']}</td>
			</tr>
EOF;
		if ( $transaction['gateway']['g_key'] == 'paypal' and isset( $transaction['extra']['verified'] ) )
		{
			$transaction['extra']['verified'] = htmlentities( $transaction['extra']['verified'] );
			$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_paypal_status']}</strong></td>
				<td class='field_field'>{$transaction['extra']['verified']}</td>
			</tr>
EOF;
		}

$IPBHTML .= <<<EOF
		</table>
EOF;
if ( $transaction['note'] )
{
	$IPBHTML .= <<<HTML
	<div class='information-box' style='margin: 15px'>{$transaction['note']}</div>
HTML;
}
	$IPBHTML .= <<<EOF
	</div>
EOF;

	if ( !empty( $transaction['fraud'] ) )
	{
		$IPBHTML .= <<<EOF
	<table class='ipsTable' style='width: 50%; float: right; border-left: 1px solid #d7e6f0'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_score']}</strong></td>
			<td class='field_field'>{$this->lessIsGood( $transaction['fraud']['riskScore'], 100 )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_address_match']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['countryMatch'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_address_from_ip']}</strong></td>
			<td class='field_field'>{$transaction['fraud']['ip_city']}, {$transaction['fraud']['ip_region']}, {$transaction['fraud']['countryCode']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_proxy']}</strong></td>
			<td class='field_field'>{$this->lessIsGood( $transaction['fraud']['proxyScore'], 10 )}</td>
		</tr>
EOF;
		if ( !empty( $transaction['fraud']['binMatch'] ) and $transaction['fraud']['binMatch'] != 'NotFound' and $transaction['fraud']['binMatch'] != 'NA' )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_bin']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['binMatch'] )} ({$transaction['fraud']['binCountry']})</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_bin_name']}</strong></td>
			<td class='field_field'>{$transaction['fraud']['binName']}</td>
		</tr>
HTML;
		}
		if ( !empty( $transaction['fraud']['cityPostalMatch'] ) )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_zip_match']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['cityPostalMatch'] )}</td>
		</tr>
HTML;
		}
		if ( !empty( $transaction['fraud']['custPhoneInBillingLoc'] ) and $transaction['fraud']['custPhoneInBillingLoc'] != 'NotFound' and $transaction['fraud']['custPhoneInBillingLoc'] != 'NA' )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_phone']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['custPhoneInBillingLoc'] )}</td>
		</tr>
HTML;
		}
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_email_free']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['freeMail'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_email_risk']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['carderEmail'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_username_risk']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['highRiskUsername'] )}</td>
		</tr>
	</table>
HTML;
	}
	
	$IPBHTML .= <<<HTML
	<br style='clear: both' />
	<div class="acp-actionbar">
		<div style='float:left'>
			<input type='button' value='{$this->lang->words['transaction_approve']}' class='button' onclick="process( {$id}, 'approve' )">
HTML;
			if ( $transaction['status'] == 'hold' )
			{
				$IPBHTML .= <<<HTML
				<input id='rev-{$id}' type='button' value='{$this->lang->words['transaction_review']}' class='button' onclick="review( {$id} )">
HTML;
			}
			$IPBHTML .= <<<HTML
		</div>
		<div style='float:right'>
			<label for='refuse-ban-{$id}'><input id='refuse-ban-{$id}' type='checkbox' /> {$this->lang->words['transaction_refuse_ban']}</label> &nbsp;
			<input type='button' value='{$this->lang->words['transaction_refuse']}' class='button redbutton' onclick="process( {$id}, 'refuse' )">
HTML;

			if ( class_exists( 'gateway_' . $transaction['gateway']['g_key'] ) and method_exists( 'gateway_' . $transaction['gateway']['g_key'], 'refund' ) )
			{
				$IPBHTML .= <<<HTML
				<input type='button' value='{$this->lang->words['transaction_refund']}' class='button redbutton' onclick="process( {$id}, 'refund' )">
HTML;
			}
		$IPBHTML .= <<<HTML
		</div>
		<br style='clear: both' />
	</div>
</div>
HTML;
}

$IPBHTML .= <<<HTML

<script type='text/javascript'>

	function process( id, action )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=transactions&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
	    	{
	    		id: id,
	    		action: action,
	    		ban: $( 'refuse-ban-' + id ).checked
	    	},
			onSuccess: function( t )
			{
				if ( t.responseJSON['error'] )
				{
					alert( t.responseJSON['error'] );
				}
				else
				{
					new Effect.BlindUp( $( 'trans-' + id ) );
				}
			}
		});
	}
	
	function review( id )
	{
HTML;
		if ( $this->settings['nexus_revw_sa'] != -1 )
		{
			$IPBHTML .= <<<HTML
		window.location = "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=payments&section=transactions&do=set&id=" + id + "&status=revw&v=0";
HTML;
		}
		else
		{
			$IPBHTML .= <<<HTML
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=transactions&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
	    	{
	    		id: id,
	    		action: 'review',
	    	},
			onSuccess: function( t )
			{
				if ( t.responseJSON['error'] )
				{
					alert( t.responseJSON['error'] );
				}
				else
				{
					$( 'trans-' + id ).removeClassName('alt');
					$( 'rev-' + id ).hide();
				}
			}
		});
HTML;
		}
		$IPBHTML .= <<<HTML
	}
	
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Transaction
//===========================================================================
function view( $transaction=array(), $gateway ) {

$transaction['status_by'] = $transaction['status_by'] ? "<br /><em>{$transaction['status_by']}</em>" : '';

$ip = ( $transaction['ip'] ) ? $transaction['ip'] : "<em>{$this->lang->words['trans_no_ip']}</em>";

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['transaction']} #{$transaction['id']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
HTML;
		if ( in_array( $transaction['status'], array( 'hold', 'pend', 'wait', 'revw', 'fail' ) ) )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$transaction['id']}&amp;status=okay&amp;v=1' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' /> {$this->lang->words['transaction_approve']}</a></li>
HTML;
		}
		if ( in_array( $transaction['status'], array( 'hold', 'pend', 'wait' ) ) )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$transaction['id']}&amp;status=revw&amp;v=1' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_app_url']}/images/review.png' /> {$this->lang->words['transaction_review']}</a></li>
HTML;
		}
		if ( in_array( $transaction['status'], array( 'okay', 'hold', 'pend', 'wait', 'revw' ) ) )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=set&amp;id={$transaction['id']}&amp;status=fail&amp;v=1' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' /> {$this->lang->words['transaction_refuse']}</a></li>
HTML;
		}
		
		if ( in_array( $transaction['status'], array( 'okay', 'hold', 'pend', 'revw', 'fail' ) ) and class_exists( 'gateway_' . $gateway['g_key'] ) and method_exists( 'gateway_' . $gateway['g_key'], 'refund' ) )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=refund&amp;id={$transaction['id']}' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_app_url']}/images/refund.png' /> {$this->lang->words['transaction_refund']}</a></li>
HTML;
		}
		
		if ( in_array( $transaction['status'], array( 'hold', 'pend', 'wait', 'revw', 'fail', 'rfnd' ) ) )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;do=delete&amp;id={$transaction['id']}' class='ipbmenu' id='mark_invoice'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' /> {$this->lang->words['delete']}</a></li>
HTML;
		}
		
		$IPBHTML .= <<<HTML
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['transaction_information']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_status']}</strong></td>
			<td class='field_field'>{$transaction['status_desc']}{$transaction['status_by']}{$transaction['support_link']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_invoice']}</strong></td>
			<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$transaction['invoiceID']}'>{$transaction['invoiceTitle']}</a></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_member']}</strong></td>
			<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$transaction['member']['member_id']}'>{$transaction['member']['_name']}</a></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['billing_address']}</strong></td>
			<td class='field_field'>{$transaction['member']['_address']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email']}</strong></td>
			<td class='field_field'>{$transaction['member']['email']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_ip']}</strong></td>
			<td class='field_field'>{$ip}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_method']}</strong></td>
			<td class='field_field'>{$transaction['method']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_amount']}</strong></td>
			<td class='field_field'>{$transaction['amount']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_date']}</strong></td>
			<td class='field_field'>{$transaction['date']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['transaction_gwid']}</strong></td>
			<td class='field_field'>{$transaction['gwid']}</td>
		</tr>
HTML;
		if ( $transaction['gatewayKey'] == 'paypal' and isset( $transaction['extra']['verified'] ) )
		{
			$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['transaction_paypal_status']}</strong></td>
				<td class='field_field'>{$transaction['extra']['verified']}</td>
			</tr>
EOF;
		}

$IPBHTML .= <<<HTML
	</table>
</div>
<br />

HTML;
if ( $transaction['note'] )
{
	$IPBHTML .= <<<HTML
	<br /><br />
	<div class='information-box'>{$transaction['note']}</div>
	<br /><br />
HTML;
}

if ( !empty( $transaction['fraud'] ) )
{
	if ( !$transaction['fraud']['riskScore'] and $transaction['fraud']['err'] )
	{
		$IPBHTML .= <<<HTML
	<div class='warning'>{$this->lang->words['fraud_error']}{$transaction['fraud']['err']}</div>
HTML;
	}
	else
	{
		$proxy = ( $transaction['fraud']['anonymousProxy'] == 'Yes' ) ? 10 : ( $transaction['fraud']['proxyScore'] );
		$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$this->lang->words['fraud_title']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_score']}</strong></td>
			<td class='field_field'>{$this->lessIsGood( $transaction['fraud']['riskScore'], 100 )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_address_match']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['countryMatch'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_address_from_ip']}</strong></td>
			<td class='field_field'>{$transaction['fraud']['ip_city']}, {$transaction['fraud']['ip_region']}, {$transaction['fraud']['countryCode']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_proxy']}</strong></td>
			<td class='field_field'>{$this->lessIsGood( $proxy, 10 )}</td>
		</tr>
HTML;
		if ( !empty( $transaction['fraud']['binMatch'] ) and $transaction['fraud']['binMatch'] != 'NotFound' and $transaction['fraud']['binMatch'] != 'NA' )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_bin']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['binMatch'] )} ({$transaction['fraud']['binCountry']})</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_bin_name']}</strong></td>
			<td class='field_field'>{$transaction['fraud']['binName']}</td>
		</tr>
HTML;
		}
		if ( !empty( $transaction['fraud']['cityPostalMatch'] ) )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_zip_match']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['cityPostalMatch'] )}</td>
		</tr>
HTML;
		}
		if ( !empty( $transaction['fraud']['custPhoneInBillingLoc'] ) and $transaction['fraud']['custPhoneInBillingLoc'] != 'NotFound' and $transaction['fraud']['custPhoneInBillingLoc'] != 'NA' )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_phone']}</strong></td>
			<td class='field_field'>{$this->yesIsGood( $transaction['fraud']['custPhoneInBillingLoc'] )}</td>
		</tr>
HTML;
		}
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_email_free']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['freeMail'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_email_risk']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['carderEmail'] )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_username_risk']}</strong></td>
			<td class='field_field'>{$this->noIsGood( $transaction['fraud']['highRiskUsername'] )}</td>
		</tr>
	</table>
</div>
HTML;
	}
}

//--endhtml--//
return $IPBHTML;
}

function yesIsGood( $value )
{
	if ( $value == 'Yes' )
	{
		return "<span style='color:green; font-weight:bold'>YES</span>";
	}
	else
	{
		return "<span style='color:red; font-weight:bold'>NO</span>";
	}
}

function noIsGood( $value )
{
	if ( $value == 'No' )
	{
		return "<span style='color:green; font-weight:bold'>NO</span>";
	}
	else
	{
		return "<span style='color:red; font-weight:bold'>YES</span>";
	}
}

function lessisGood( $value, $range )
{
	$percentage = ( 100 / $range ) * $value;
		
	$red = ( ( 205 / 100 ) * $percentage ) + 50;
	$green = ( 205 - $red ) + 50;
	
    $r = dechex( $red );
    $g = dechex( $green );
    $b = dechex( 0 );
    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
	
	return "<span style='color:#{$color}; font-weight:bold'>{$value} / {$range}</span>";
	
}

function rgb2html($r=0, $g=0, $b=0)
{
    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}



}