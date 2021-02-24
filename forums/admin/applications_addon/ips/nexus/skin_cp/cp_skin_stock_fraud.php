<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Stock - Fraud Rules
 * Last Updated: $Date: 2013-01-24 04:57:34 -0500 (Thu, 24 Jan 2013) $
 * </pre>
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	© 2012 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		26th April 2012
 * @version		$Revision: 11892 $
 */
 
class cp_skin_stock_fraud
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
	<h2>{$this->lang->words['fraudrules']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['fraud_add']}</a></li>
		</ul>
	</div>
</div>

<div class='information-box'>
	{$this->lang->words['fraudrules_desc']}
</div>
<br />

<div class='acp-box'>
 	<h3>{$this->lang->words['fraudrules']}</h3>
	<table class='ipsTable' id='rules_list'>
		<tr>
			<th width='4%'>&nbsp;</th>
			<th width='88%'>{$this->lang->words['fraud_name']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	if ( !empty( $rules ) )
	{
		foreach ( $rules as $ruleID => $data )
		{
			$IPBHTML .= <<<HTML
			<tr class='ipsControlRow isDraggable' id='rules_{$ruleID}'>
				<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
				<td width='88%'><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=edit&amp;id={$ruleID}'>{$data['f_name']}</a></span></th>
				<td width='8%'>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=edit&amp;id={$ruleID}'>{$this->lang->words['edit']}</a>
						</li>
						<li class='i_delete'>
							<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=delete&amp;id={$ruleID}'>{$this->lang->words['delete']}</a>
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
				{$this->lang->words['fraud_empty']} <a href='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=add' class='mini_button'>{$this->lang->words['click2create']}</a>
			</td>
		</tr>
HTML;
	}

$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#rules_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=fraud&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current, $methods, $groups, $countries ) {

if ( empty( $current ) )
{
	$title = $this->lang->words['fraud_add'];
	$id = 0;
}
else
{
	$title = $this->lang->words['fraud_edit'];
	$id = $current['f_id'];
}

$form['name'] = $this->registry->output->formInput( 'name', ( empty( $current ) ? '' : $current['f_name'] ) );
$form['amount'] = $this->registry->output->formDropdown( 'amount', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['f_amount'] ) );
$form['amount_unit'] = $this->registry->output->formSimpleInput( 'amount_unit', ( empty( $current ) ? '' : $current['f_amount_unit'] ) );
$form['methods'] = $this->registry->output->formMultiDropdown( 'methods[]', $methods, empty( $current ) ? NULL : explode( ',', $current['f_methods'] ) );
$form['groups'] = $this->registry->output->formMultiDropdown( 'groups[]',$groups, empty( $current ) ? NULL : explode( ',', $current['f_groups'] ) );
$form['email'] = $this->registry->output->formDropdown( 'email', array(
	array( '', '' ),
	array( 'c', $this->lang->words['contains'] ),
	array( 'e', $this->lang->words['equals'] ),
	), ( empty( $current ) ? '' : $current['f_email'] ) );
$form['email_unit'] = $this->registry->output->formInput( 'email_unit', ( empty( $current ) ? '' : $current['f_email_unit'] ) );
$form['country'] = $this->registry->output->formMultiDropdown( 'country[]', $countries, empty( $current ) ? NULL : explode( ',', $current['f_country'] ) );
$form['trans_okay'] = $this->registry->output->formDropdown( 'trans_okay', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['f_trans_okay'] ) );
$form['trans_okay_unit'] = $this->registry->output->formSimpleInput( 'trans_okay_unit', ( empty( $current ) ? '' : $current['f_trans_okay_unit'] ) );
$form['trans_fraud'] = $this->registry->output->formDropdown( 'trans_fraud', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['f_trans_fraud'] ) );
$form['trans_fraud_unit'] = $this->registry->output->formSimpleInput( 'trans_fraud_unit', ( empty( $current ) ? '' : $current['trans_fraud_unit'] ) );
$form['voucher'] = $this->registry->output->formDropdown( 'voucher', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_voucher'] ) );
$form['maxmind'] = $this->registry->output->formDropdown( 'maxmind', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['f_maxmind'] ) );
$form['maxmind_unit'] = $this->registry->output->formSimpleInput( 'maxmind_unit', ( empty( $current ) ? '' : $current['f_maxmind_unit'] ) );
$form['maxmind_address_valid'] = $this->registry->output->formDropdown( 'maxmind_address_valid', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_address_valid'] ) );
$form['maxmind_address_match'] = $this->registry->output->formDropdown( 'maxmind_address_match', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_address_match'] ) );
$form['maxmind_phone_match'] = $this->registry->output->formDropdown( 'maxmind_phone_match', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_phone_match'] ) );
$form['maxmind_proxy'] = $this->registry->output->formDropdown( 'maxmind_proxy', array(
	array( '', '' ),
	array( 'g', $this->lang->words['gte'] ),
	array( 'e', $this->lang->words['equals'] ),
	array( 'l', $this->lang->words['lte'] ),
	), ( empty( $current ) ? '' : $current['f_maxmind_proxy'] ) );
$form['maxmind_proxy_unit'] = $this->registry->output->formSimpleInput( 'maxmind_proxy_unit', ( empty( $current ) ? '' : $current['f_maxmind_proxy_unit'] ) );
$form['maxmind_freeemail'] = $this->registry->output->formDropdown( 'maxmind_freeemail', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_freeemail'] ) );
$form['maxmind_riskyemail'] = $this->registry->output->formDropdown( 'maxmind_riskyemail', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_riskyemail'] ) );
$form['maxmind_riskyusername'] = $this->registry->output->formDropdown( 'maxmind_riskyusername', array(
	array( '0', '' ),
	array( '1', $this->lang->words['yes'] ),
	array( '-1', $this->lang->words['no'] ),
	), ( empty( $current ) ? '0' : $current['f_maxmind_riskyusername'] ) );
$form['action'] = $this->registry->output->formDropdown( 'action', array(
	array( 'okay', ucwords( $this->lang->words['tcstatus_okay'] ) ),
	array( 'hold', ucwords( $this->lang->words['tcstatus_hold'] ) ),
	array( 'fail', ucwords( $this->lang->words['tcstatus_fail'] ) )
	), ( empty( $current ) ? 'hold' : $current['f_action'] ) );
$form['action_ban'] = $this->registry->output->formYesNo( 'action_ban', ( empty( $current ) ? '0' : $current['f_action_ban'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$title}</h2>
</div>

HTML;

if ( !$this->settings['maxmind_key'] )
{
	$IPBHTML .= <<<HTML
	<div class='information-box'>
		<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=settings' target='_blank'>{$this->lang->words['fraud_nomaxmind']}</a>
	</div>
	<br />
HTML;
}

$IPBHTML .= <<<HTML

<div class='information-box'>
	{$this->lang->words['fraud_emptydesc']}
</div>
<br />

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=fraud&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_name']}</strong></td>
			<td class='field_field'>
				{$form['name']}<br />
				<span class='desctext'>{$this->lang->words['fraud_name_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['fraud_header_1']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_amount']}</strong></td>
			<td class='field_field'>
				{$form['amount']} {$form['amount_unit']} {$this->settings['nexus_currency']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_methods']}</strong></td>
			<td class='field_field'>
				{$form['methods']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_groups']}</strong></td>
			<td class='field_field'>
				{$form['groups']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_email']}</strong></td>
			<td class='field_field'>
				{$form['email']} {$form['email_unit']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_country']}</strong></td>
			<td class='field_field'>
				{$form['country']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_trans_okay']}</strong></td>
			<td class='field_field'>
				{$form['trans_okay']} {$form['trans_okay_unit']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_trans_fraud']}</strong></td>
			<td class='field_field'>
				{$form['trans_fraud']} {$form['trans_fraud_unit']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_voucher']}</strong></td>
			<td class='field_field'>
				{$form['voucher']}
			</td>
		</tr>
HTML;
	if ( $this->settings['maxmind_key'] )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind']}</strong></td>
			<td class='field_field'>
				{$form['maxmind']} {$form['maxmind_unit']}<br />
				<span class='desctext'>{$this->lang->words['fraud_maxmind_score100_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_address_valid']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_address_valid']}<br />
				<span class='desctext'>{$this->lang->words['fraud_maxmind_address_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_address_match']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_address_match']}<br />
				<span class='desctext'>{$this->lang->words['fraud_maxmind_address_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_phone_match']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_phone_match']}<br />
				<span class='desctext'>{$this->lang->words['fraud_maxmind_phone_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_proxy']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_proxy']} {$form['maxmind_proxy_unit']}<br />
				<span class='desctext'>{$this->lang->words['fraud_maxmind_score10_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_freeemail']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_freeemail']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_riskyemail']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_riskyemail']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_maxmind_riskyusername']}</strong></td>
			<td class='field_field'>
				{$form['maxmind_riskyusername']}
			</td>
		</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['fraud_header_2']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_action']}</strong></td>
			<td class='field_field'>
				{$form['action']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fraud_ban']}</strong></td>
			<td class='field_field'>
				{$form['action_ban']}
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


}