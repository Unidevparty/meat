<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Payments - Payout Requests
 * Last Updated: $Date: 2012-01-25 05:50:46 -0500 (Wed, 25 Jan 2012) $
 * </pre>
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		4th February 2010
 * @version		$Revision: 10186 $
 */
 
class cp_skin_payments_payouts
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
function manage( $requests, $pagination, $massPayments ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['payouts']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
HTML;

		if ( $massPayments )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=masspayments'><img src='{$this->settings['skin_app_url']}/images/mass-payments.png' alt='' /> {$this->registry->getClass('class_localization')->words['payouts_masspayments']}</a></li>
HTML;
		}
	
	$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=search&amp;do=credit'><img src='{$this->settings['skin_app_url']}/images/search.png' alt='' /> {$this->registry->getClass('class_localization')->words['payouts_credit_list']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box alt'>
 	<h3>{$this->registry->getClass('class_localization')->words['payouts']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='5%'>&nbsp;</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_member']}</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_amount']}</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_gateway']}</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_date']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

	if ( !empty( $requests ) )
	{
		foreach ( $requests as $requestID => $request )
		{
			$menuKey++;
			$IPBHTML .= <<<HTML
				<tr class='ipsControlRow'>
					<td>&nbsp;</td>
					<td><a href='{$this->settings['base_url']}app=members&amp;module=members&amp;section=members&amp;do=viewmember&amp;member_id={$request['member']['member_id']}'>{$request['member']['members_display_name']}</a></td>
					<td><span class='larger_text'>{$request['amount']}</span></td>
					<td>{$request['gatewayName']}</td>
					<td>{$request['date']}</td>
					<td>
						<ul class='controls ipsControlStrip'>
							<li class='i_accept'>
								<a title='{$this->registry->getClass('class_localization')->words['payouts_pay']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=pay&amp;id={$requestID}'>{$this->registry->getClass('class_localization')->words['payouts_pay']}...</a>
							</li>
							<li class='i_delete'>
								<a title='{$this->registry->getClass('class_localization')->words['payouts_dismiss']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=dismiss&amp;id={$requestID}'>{$this->registry->getClass('class_localization')->words['payouts_dismiss']}...</a>
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
						{$this->registry->getClass('class_localization')->words['payouts_empty']}
					</td>
				</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		</table>
	</div>
</div>
<br />
{$pagination}

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Mass Payment
//===========================================================================
function massPayment( $requests ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['payouts_masspayments']}</h2>
</div>

<form id='masspayform' action='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=do_masspayment' method='post'>
<input id='dismiss' type='hidden' name='dismiss' value='0' />
<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['payouts']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='5%'>&nbsp;</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_member']}</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_amount']}</th>
				<th>{$this->registry->getClass('class_localization')->words['payouts_date']}</th>
			</tr>
HTML;

	foreach ( $requests as $requestID => $request )
	{
		$menuKey++;
		$IPBHTML .= <<<HTML
			<tr class='control_row'>
				<td><input type='checkbox' name='request-{$requestID}' checked='checked' /></td>
				<td><a href='{$this->settings['base_url']}app=members&amp;module=members&amp;section=members&amp;do=viewmember&amp;member_id={$request['member']['member_id']}'>{$request['member']['members_display_name']}</a></td>
				<td><span class='larger_text'>{$request['amount']}</span></td>
				<td>{$request['date']}</td>
			</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		</table>
		<div class='acp-actionbar'>
			<input type='submit' class='realbutton' value='{$this->registry->getClass('class_localization')->words['masspayment_go']}' /> <input type='button' onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['masspayment_dismiss_confirm']}' ) ) { return false; } $('dismiss').value=1; $('masspayform').submit()" class='realbutton' value='{$this->registry->getClass('class_localization')->words['masspayment_dismiss']}' />
		</div>
	</div>
</div>
</form>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Manage
//===========================================================================
function pay( $html ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['payouts']}</h2>
</div>

{$html}

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Dismiss
//===========================================================================
function dismiss( $payout ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['payouts_dismiss']}</h2>
</div>

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=do_dismiss&amp;id={$payout['po_id']}&amp;refund=1'>{$this->lang->words['payout_dismiss_refund']}</a></div>	
</div>
<br /><br />
<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=payouts&amp;do=do_dismiss&amp;id={$payout['po_id']}&amp;refund=0'>{$this->lang->words['payout_dismiss_norefund']}</a></div>	
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}



}