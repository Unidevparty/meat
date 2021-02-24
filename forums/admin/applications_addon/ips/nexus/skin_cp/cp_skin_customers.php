<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - Customers
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		8th February 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_customers
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
// Search
//===========================================================================
function view( $customer, $notes, $purchases, $invoices, $support, $dots, $referrals, $referral_commission, $parentMap, $alternates, $parents, $pagination, $value ) {

$IPBHTML = "";

$IPBHTML .= <<<EOF
	<script type='text/javascript'>
		var _countriesWithStates = [];
	</script>
EOF;

$form = array();
foreach ( customer::fields() as $f )
{
	switch ( $f['f_type'] )
	{
		case 'text':
			$form[ $f['f_column'] ] = ipsRegistry::getClass('output')->formInput( $f['f_column'], $customer->data[ $f['f_column'] ] );
			break;
		
		case 'special':
			$form['cm_state'] = ipsRegistry::getClass('output')->formInput( '_state', $customer->data['cm_state'], 'text-states', 30, 'text', "style='display:none'" );
			foreach ( customer::generateStateDropdown( TRUE ) as $country => $states )
			{
				$form['cm_state'] .= ipsRegistry::getClass('output')->formDropdown( '_state', $states, $customer->data['cm_state'], $country.'-states', "style='display:none'" );
				$IPBHTML .= <<<EOF
				<script type='text/javascript'>
					_countriesWithStates["{$country}"] = 1;
				</script>
EOF;
			}
			break;
			
		case 'dropdown':
			$dropdown = array();
			$options = explode( "\n", $f['f_extra'] );
			if ( $f['f_column'] == 'cm_country' )
			{
				ipsRegistry::getClass('class_localization')->loadLanguageFile( array( 'public_countries' ), 'nexus' );
				$_options = array();
				foreach ( $options as $o )
				{
					$_options[ $o ] = ipsRegistry::getClass('class_localization')->words[ 'nc_' . $o ];
				}
				$options = $_options;
				$_countries = $options;
			}
			foreach ( $options as $k => $v )
			{
				$dropdown[] = array( $k, $v );
			}
			$form[ $f['f_column'] ] = ipsRegistry::getClass('output')->formDropdown( $f['f_column'], $dropdown, $customer->data[ $f['f_column'] ], ( $f['f_column'] == 'cm_country' ? 'cm_country' : '' ), ( $f['f_column'] == 'cm_country' ? 'onchange="states();"' : '' ) );
	}
}

$form['cm_credits'] = ipsRegistry::getClass('output')->formInput( 'cm_credits', $customer->data['cm_credits'] );

/* What methods support saving cards? */
$gateways = $this->cache->getCache('pay_gateways');
$methods = $this->cache->getCache('pay_methods');
$cardMethods = array();
$haveStripe = false;
$showFieldNames = false;
$stripeData = '';
$cardSubmit = '';
foreach ( $methods as $m )
{
	if ( $m['m_active'] and $m['stores_card'] )
	{
		// Is it Stripe?
		if ( $gateways[ $m['m_gateway'] ]['g_key'] == 'stripe' )
		{
			if ( !$haveStripe )
			{
				$IPBHTML .= <<<EOF

				<script type="text/javascript" src="https://js.stripe.com/v1/" charset="ISO-8859-1"></script>
				<script type='text/javascript' src='{$this->settings['js_base_url']}js/ips.nexus.js'></script>
				<script type='text/javascript' src='{$this->settings['js_base_url']}js/ips.nexus.checkout.js'></script>
EOF;
				
				$stripeData = json_encode( array(
					'name'				=> $customer->data['_name'],
					'address_line1' 	=> $customer->data['cm_address_1'],
					'address_line2'		=> $customer->data['cm_address_2'],
					'state'				=> $customer->data['cm_state'],
					'zip'				=> $customer->data['cm_zip'],
					'country'			=> $customer->data['cm_country'],
					) );
			}
			
			$settings = unserialize( $m['m_settings'] );
			$cardMethods[] = array( $m['m_id'], $m['m_name'], $settings['public_key'], "card_method_{$m['m_id']}" );
		
			$haveStripe = true;
		}
		else
		{
			$cardMethods[] = array( $m['m_id'], $m['m_name'], '', '' );
			$showFieldNames = true;
		}
	}
}
if ( $stripeData )
{
	$cardSubmit = "onsubmit='return nexusCheckout.stripeButtonClick( {$stripeData}, 0, true );'";
}
else
{
	$cardSubmit = "onsubmit='return nexusCheckout.submitCardForm();'";
}
		
$card = (string) $customer->creditCard();
$months = array( 0 => '' );
foreach ( range( 1, 12 ) as $m )
{
	$m = str_pad( $m, 2, '0', STR_PAD_LEFT );
	$months[] = array( $m, $m );
}
$years = array( 0 => '' );
foreach ( range( date('Y'), date('Y')+10 ) as $y )
{
	$years[] = array( $y, $y );
}
$form['card_number'] = ipsRegistry::getClass('output')->formInput( ( $showFieldNames ? 'card_number' : '' ), $card, 'card_number' );
$form['card_expire_month'] = ipsRegistry::getClass('output')->formDropdown( ( $showFieldNames ? 'exp_month' : '' ), $months, 0, 'exp_month' );
$form['card_expire_year'] = ipsRegistry::getClass('output')->formDropdown( ( $showFieldNames ? 'exp_year' : '' ), $years, 0, 'exp_year' );
$form['card_code'] = ipsRegistry::getClass('output')->formInput( ( $showFieldNames ? 'card_code' : '' ), '', 'code', 5 );

$menuKey = 0;

//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>
		{$customer->data['_name']}
		<span style='font-size:10px'>({$customer->data['email']} - {$customer->data['_group_formatted']})
EOF;
		if ( $customer->data['referred_by'] )
		{
			$ref = IPSMember::load( $customer->data['referred_by'] );
			$IPBHTML .= <<<EOF
			&nbsp;{$this->lang->words['customer_referred_by']}<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$ref['member_id']}'>{$ref['members_display_name']}</a>
EOF;
		}
	
	$IPBHTML .= <<<EOF
	</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}module=customers&amp;section=history&amp;id={$customer->data['member_id']}'><img src='{$this->settings['skin_app_url']}/images/customers/history.png' alt='' /> {$this->lang->words['customer_view_history']}</a></li>
EOF;
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'customer_standing' ) )
		{
			$IPBHTML .= <<<EOF
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}module=customers&amp;section=standing&amp;id={$customer->data['member_id']}'> <img src='{$this->settings['skin_app_url']}/images/trophy.png' alt='' /> {$this->lang->words['customer_view_standing']}</a></li>
EOF;
		}
		
	$IPBHTML .= <<<EOF
			<li class='ipsActionButton'><a href='#' id='email'><img src='{$this->settings['skin_app_url']}/images/customers/mail.png' alt='' /> {$this->lang->words['customer_send_email']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}app=members&amp;module=members&amp;section=members&amp;do=viewmember&amp;member_id={$customer->data['member_id']}'><img src='{$this->settings['skin_app_url']}/images/customers/info.png' alt='' /> {$this->lang->words['customer_edit_link']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}app=nexus&amp;module=customers&amp;section=void&amp;member_id={$customer->data['member_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' alt='' /> {$this->lang->words['customer_void']}</a></li>
		</ul>
	</div>
</div>

<script type='text/javascript'>

	function doPopUp( e, url )
	{
		new ipb.Popup('addnote', { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: url } );
	}

	$('email').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=customers&do=email&member_id={$customer->data['member_id']}&secure_key=" + ipb.vars['md5_hash'] ) );
	
</script>

EOF;

if ( !empty( $parents ) )
{
	foreach ( $parents as $a )
	{
		$IPBHTML .= <<<EOF
		<div class='information-box'>{$this->lang->words['customer_alternate']}<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$a['member_id']}'>{$a['_name']}</a></div><br />
EOF;
	}
}

$flagName = 'flag_' . preg_replace('/[^a-z0-9_]/', '', strtolower( str_replace( ' ', '_', $_countries[ $customer->data['cm_country'] ] ) ) );
$flag = '';
if ( is_file( IPSLib::getAppDir('nexus') . "/skin_cp/images/customers/flags/{$flagName}.png" ) )
{
	$flag = "<img src='{$this->settings['skin_app_url']}images/customers/flags/{$flagName}.png' />";
}

$IPBHTML .= <<<EOF
<div class='acp-box' style='overflow: auto; float:left; width:49%'>
	<h3>{$this->lang->words['customer_details']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;do=edit&amp;id={$customer->data['member_id']}' method='post' {$cardSubmit} id='do_pay'>
		<input type='hidden' name='hidden_field' id='hidden_field' value='' />
		<div id='details'>
			<table class='ipsTable' id='show'>
EOF;
			foreach ( customer::fields() as $f )
			{
				if ( in_array( $f['f_column'], array( 'cm_first_name', 'cm_last_name', 'cm_address_2', 'cm_city', 'cm_state', 'cm_zip', 'cm_country' ) ) )
				{
					continue;
				}
				
				if ( $f['f_type'] == 'dropdown' )
				{
					$options = explode( "\n", $f['f_extra'] );
					$customer->data[ $f['f_column'] ] = $options[ $customer->data[ $f['f_column'] ] ];
				}
				
				if ( $f['f_column'] == 'cm_address_1' )
				{
					$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['customer_address']}</strong></td>
					<td class='field_field'>{$customer->address}<span style='float:right'>{$flag}</span></td>
				</tr>
EOF;
				}
				else
				{
					$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$f['f_name']}</strong></td>
					<td class='field_field'>{$customer->data[ $f['f_column'] ]}</td>
				</tr>
EOF;
				}
			}
			$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['customer_credit']}</strong></td>
				<td class='field_field'>{$customer->data['_cm_credits']}</td>
			</tr>
EOF;
			if ( !empty( $cardMethods ) )
			{
				$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['customer_cardonfile']}</strong></td>
					<td class='field_field'>
EOF;
					if ( $card )
					{
						$IPBHTML .= $card . " &nbsp; &nbsp; <a class='desctext' href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;do=edit&amp;id={$customer->data['member_id']}&amp;removecard=1'>{$this->lang->words['customer_removecard']}</a>";
					}
					else
					{
						$IPBHTML .= $this->lang->words['customer_nocard'];
					}
					$IPBHTML .= <<<EOF
					</td>
				</tr>
EOF;
			}
			$IPBHTML .= <<<EOF
			</table>
EOF;
	if ( $this->registry->getClass('class_permissions')->checkPermission( 'details_edit' ) )
	{
		$IPBHTML .= <<<EOF
			<div class='acp-actionbar'>
				<input type='button' class='realbutton' value='{$this->lang->words['customer_edit_details']}' onclick="$('details').style.display='none'; $('edit').style.display='inline';" />
			</div>
EOF;
	}



$IPBHTML .= <<<EOF
		</div>
		<div id='edit' style='display:none'>
			<table class='ipsTable double_pad'>
EOF;
			foreach ( customer::fields() as $f )
			{
				if ( $f['f_column'] == 'cm_address_1' )
				{
					$IPBHTML .= <<<EOF
					<tr>
						<td class='field_title'><strong class='title'>{$this->lang->words['cm_address']}</strong></td>
						<td class='field_field'>{$form['cm_address_1']}<br />{$form['cm_address_2']}</td>
					</tr>
EOF;
				}
				elseif ( $f['f_column'] == 'cm_address_2' )
				{
					continue;
				}
				else
				{
					$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$f['f_name']}</strong></td>
					<td class='field_field'>{$form[ $f['f_column'] ]}</td>
				</tr>
EOF;
				}
			}
							
			$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['customer_credit']}</strong></td>
					<td class='field_field'>{$form['cm_credits']}</td>
				</tr>
EOF;
		if ( !empty( $cardMethods ) )
		{
			if ( count( $cardMethods ) == 1 )
			{
				$IPBHTML .= "<input type='hidden' name='card_method' value='{$cardMethods[0][0]}' />";
			}
			else
			{
				$form['card_method'] = "<select name='card_method' id='cc_method' onchange='nexusCheckout.changeCardMethod()'>";
				foreach ( $cardMethods as $data )
				{
					$selected = '';
					if ( $cystomer->data['cim_method'] == $data[0] )
					{
						$selected = " selected='selected'";
					}
					$form['card_method'] .= "<option value='{$data[0]}' id='{$data[3]}' data-extra='{$data[2]}' {$selected}>{$data[1]}</option>";
				}
				$form['card_method'] .= '</select>';
				
				
				ipsRegistry::getClass('output')->formDropdown( 'card_method', $cardMethods, $customer->data['cim_method'], 'cc_method' );
				$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['card_method']}</strong></td>
					<td class='field_field'>{$form['card_method']}</td>
				</tr>
EOF;
			}
			
			$IPBHTML .= <<<EOF
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['card_number']}</strong></td>
					<td class='field_field'>
						{$form['card_number']}<br />
						<span class='required' id='error_message_holder'></span>
					</td>
				</tr>
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['card_expire']}</strong></td>
					<td class='field_field'>{$form['card_expire_month']} {$form['card_expire_year']}</td>
				</tr>
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['card_code']}</strong></td>
					<td class='field_field'>{$form['card_code']}</td>
				</tr>
EOF;

		}
		
			$IPBHTML .= <<<EOF
			</table>
			<div class='acp-actionbar'>
				<input type='submit' class='realbutton' value='{$this->lang->words['save_details']}' id='submit_button' />
				<input type='button' class='redbutton realbutton' value='{$this->lang->words['cancel']}' onclick="$('edit').style.display='none'; $('details').style.display='inline';" />
			</div>
		</div>
	</form>
</div>

<script type='text/javascript'>
EOF;
if ( count( $cardMethods ) == 1 and $cardMethods[0][2] )
{
	$IPBHTML .= <<<EOF
	Stripe.setPublishableKey( '{$cardMethods[0][2]}' );
EOF;
}
else
{
	$IPBHTML .= <<<EOF
	nexusCheckout.changeCardMethod();
EOF;
}

$IPBHTML .= <<<EOF
	ipb.lang['info_card_newexpire'] = "{$this->lang->words['info_card_newexpire']}";
</script>

<div class='acp-box' style='float:right; width:49%'>
	<h3>{$this->lang->words['alternate_contacts']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th>{$this->lang->words['ac_name']}</th>
			<th>{$this->lang->words['ac_purchases']}</th>
			<th>{$this->lang->words['altcontacts__billing']}</th>
			<th>{$this->lang->words['altcontacts__support']}</th>
			<th class='col_buttons' width='3%'>&nbsp;</th>
		</tr>
EOF;

foreach ( $alternates as $a )
{
	$_purchases = array();
	foreach ( $a['purchases'] as $item )
	{
		if ( !empty( $item ) )
		{
			$_purchases[] = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] ) . " {$item['ps_name']} <span class='desctext'>{$item['ps_id']}</span>";
		}
	}
	$_purchases = empty( $_purchases ) ? "<em>{$this->lang->words['ac_no_purchases']}</em>" : implode( '<br />', $_purchases );
	
	$a['billing'] = ( $a['billing'] ) ? "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' />" : "<img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' alt='' />";
	$a['support'] = ( $a['support'] ) ? "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' />" : "<img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' alt='' />";
	
	if ( !$a['member_id'] )
	{
		$text = ucwords( $this->lang->words['deleted_member'] );
		$IPBHTML .= <<<EOF
		<tr class='ipsControlRow'>
			<td></td>
			<td colspan='4'><em>{$text}</em></td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=customers&amp;section=alternates&amp;do=delete&amp;member_id={$customer->data['member_id']}&amp;id={$a['alt_id']}'>{$this->lang->words['delete']}...</a>
					</li>
				</ul>
			</td>
		</tr>
EOF;
	}
	else
	{
		$menuKey++;
		$IPBHTML .= <<<EOF
		<tr class='ipsControlRow'>
			<td></td>
			<td><span class='larger_text'><a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$a['member_id']}'>{$a['_name']}</a></span></td>
			<td>{$_purchases}</td>
			<td>{$a['billing']}</td>
			<td>{$a['support']}</td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=alternates&amp;do=edit&amp;member_id={$customer->data['member_id']}&amp;id={$a['member_id']}'>{$this->lang->words['edit']}...</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=customers&amp;section=alternates&amp;do=delete&amp;member_id={$customer->data['member_id']}&amp;id={$a['member_id']}'>{$this->lang->words['delete']}...</a>
					</li>
				</ul>
			</td>
		</tr>
EOF;
	}
}

$IPBHTML .= <<<EOF
</table>
EOF;

if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_view' ) )
{
	$IPBHTML .= <<<EOF
<div class='acp-actionbar'>
	<a class='realbutton' href='{$this->settings['base_url']}&amp;module=customers&amp;section=alternates&amp;do=add&amp;member_id={$customer->data['member_id']}'>{$this->lang->words['ac_add']}</a>
</div>
EOF;
}
$IPBHTML .= <<<EOF
</div>

<script type='text/javascript'>

	function states()
	{
		var c = $('cm_country').value;
		if ( c in _countriesWithStates )
		{
			$( _display ).style.display = 'none';
			$( _display ).name = '_state';
			
			$( c + '-states' ).style.display = '';
			$( c + '-states' ).name = 'cm_state';
			
			_display = c + '-states';
		}
		else
		{
			$( _display ).style.display = 'none';
			$( _display ).name = '_state';
			
			$( 'text-states' ).style.display = '';
			$( 'text-states' ).name = 'cm_state';
			
			_display = 'text-states';
		}
	}
	
	var _display = 'text-states';
	states();

</script>

<br style='clear: both' /><br /><br />

EOF;

if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_view' ) )
{

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['customer_notes']}</h3>
	<table class='ipsTable'>
EOF;

	foreach ( $notes as $noteID => $data )
	{
		$menuKey++;
		$edit = $delete = $classname = '';
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_edit' ) )
		{
			$edit = "<li class='i_edit'><a href='javascript: void();' id='editnote{$noteID}'>{$this->lang->words['edit']}...</a></li>";
		}
		
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_delete' ) )
		{
			$delete = <<<EOF
<li class='i_delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=customers&amp;section=notes&amp;do=delete&amp;id={$noteID}' id='deletenote{$noteID}'>{$this->lang->words['delete']}...</a></li>
EOF;
		}
		
		$noteBy = sprintf( $this->lang->words['note_by'], "<a href='{$this->settings['base_url']}app=members&amp;module=members&amp;section=members&amp;do=viewmember&amp;member_id={$data['note_author']['member_id']}'>{$data['note_author']['members_display_name']}</a>", $data['note_date'] );
		
		$IPBHTML .= <<<EOF
		<tr class='ipsControlRow'>
			<td>
				{$data['note_text']}<br />
				<span class='desctext'>{$noteBy}</span>
			</td>
			<td width='100px'>
				<ul class='ipsControlStrip'>
					{$edit}
					{$delete}
				</ul>
			</td>
		</tr>
		
		<script type='text/javascript'>
			$('editnote{$noteID}').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=customers&do=edit_note&id={$noteID}&secure_key=" + ipb.vars['md5_hash'] ) );
		</script>

EOF;
	}
	
	$IPBHTML .= <<<EOF
	</table>
EOF;
	
	if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_add' ) )
	{
		$IPBHTML .= <<<EOF
	<div class='acp-actionbar'>
		<input type='button' class='realbutton' value='{$this->lang->words['note_add']}' id='addnote' />
	</div>
EOF;
	}
	
		
$IPBHTML .= <<<EOF
</div>
<script type='text/javascript'>

	$('addnote').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=customers&do=add_note&member_id={$customer->data['member_id']}&secure_key=" + ipb.vars['md5_hash'] ) );

</script>

<br /><br />

EOF;

}

if ( !empty( $purchases ) and $this->registry->getClass('class_permissions')->checkPermission( 'purchases_view', 'nexus', 'payments' ) )
{

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['customer_purchases']}</h3>
	<table class='form_table'>
		<tr>
			<th width='5%'>&nbsp;</th>
			<th width='7%'>{$this->lang->words['purchases_id']}</th>
			<th width='29%'>{$this->lang->words['purchase_item']}</th>
			<th width='15%'>{$this->lang->words['purchases_purchased']}</th>
			<th width='15%'>{$this->lang->words['purchases_expires']}</th>
			<th width='24%'>{$this->lang->words['purchases_renewal_terms']}</th>
			<th class='col_buttons'>&nbsp;</th>
		</tr>
EOF;

	$this->menuKey = $menuKey;
	if ( is_array( $parentMap[0] ) )
	{
		foreach ( $parentMap[0] as $item )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $item ], $parentMap, $purchases );
		}
	}
	$menuKey = $this->menuKey;
	
	$IPBHTML .= <<<EOF
	</table>
EOF;
	if ( isset( $pagination['purchases'] ) )
	{
		$va = sprintf( $this->lang->words['customer_view_all'], $pagination['purchases'] );
	$IPBHTML .= <<<EOF
	<div class='acp-actionbar' style='padding: 10px'>
		<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=view&amp;member=1&amp;id={$customer->data['member_id']}' class='realbutton'>{$va}</a>
	</div>
EOF;
	}
	$IPBHTML .= <<<EOF
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

<br /><br />

EOF;

}

if ( $this->registry->getClass('class_permissions')->checkPermission( 'invoices_manage', 'nexus', 'payments' ) )
{
	$IPBHTML .= <<<EOF
	<div class='acp-box'>
		<h3>{$this->lang->words['customer_invoices']}</h3>
		<table class='ipsTable'>
			<tr>
				<th width='5%'>&nbsp;</th>
				<th width='7%'>{$this->lang->words['invoice_id']}</th>
				<th width='31%'>{$this->lang->words['invoice_title']}</th>
				<th width='15%'>{$this->lang->words['invoice_amount']}</th>
				<th width='16%'>{$this->lang->words['invoice_date']}</th>
				<th width='16%'>{$this->lang->words['invoice_paid']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
EOF;
		foreach ( $invoices as $invoiceID => $data )
		{
			$menuKey++;
			
			switch ( $data['i_status'] ) { case 'paid': $badgeColor = 'green'; break; case 'pend': $badgeColor = 'grey'; break; case 'expd': $badgeColor = 'purple'; break; case 'canc': $badgeColor = 'red'; break; }
			
			$IPBHTML .= <<<EOF
			<tr class='ipsControlRow'>
			 	<td>
					<span class='ipsBadge badge_{$badgeColor}' style='width: 100%; text-align: center'>
						{$this->lang->words[ 'istatus_' . $data['i_status'] ]}
					</span>
				</td>
				<td>{$data['i_id']}</td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$invoiceID}'>{$data['i_title']}</a></span></td>
				<td>{$data['i_amount']}</td>
				<td>{$data['i_date']}</td>
				<td>{$data['i_paid']}</td>
				<td>
EOF;
					if( $data['i_status'] != 'paid' )
					{
						$IPBHTML .= <<<EOF
							<ul class='ipsControlStrip'>
EOF;
						if ( $data['i_status'] == 'pend' )
						{
							$IPBHTML .= <<<EOF
							<li class='i_edit'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=edit&amp;id={$invoiceID}'>{$this->lang->words['edit']}...</a></li>
EOF;
						}
						
						$IPBHTML .= <<<EOF
								<li class='i_accept'><a title='{$this->lang->words['invoice_mark_paid']}' href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=paid'>{$this->lang->words['invoice_mark_paid']}</a></li>
								<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
									<a href='#'>{$this->lang->words['options']}</a>
								</li>
							</ul>
							<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
EOF;
							if ( $customer->data['cim_profile_id'] )
							{
								$IPBHTML .= <<<EOF
								<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=card&amp;id={$invoiceID}'>{$this->lang->words['charge_to_card']}</a></li>
EOF;
							}
							
							if ( $customer->data['cm_credits'] )
							{
								$IPBHTML .= <<<EOF
								<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=credit&amp;id={$invoiceID}'>{$this->lang->words['charge_to_credit']}</a></li>
EOF;
							}
							
							$IPBHTML .= <<<EOF
								<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=pend'>{$this->lang->words['invoice_mark_pend']}</a></li>
								<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=expd'>{$this->lang->words['invoice_mark_expd']}</a></li>
								<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=set_invoice&amp;id={$invoiceID}&amp;status=canc'>{$this->lang->words['invoice_mark_canc']}</a></li>
								<li class='icon refresh'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=resend_invoice&amp;id={$invoiceID}'>{$this->lang->words['invoice_resend']}...</a></li>
								<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}'>{$this->lang->words['delete']}...</a></li>
							</ul>
	
EOF;
					}
					else
					{
						$IPBHTML .= <<<EOF
						<ul class='ipsControlStrip'>
							<li class='i_delete'><a class='delete' href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=delete&amp;id={$invoiceID}'>{$this->lang->words['delete']}...</a></li>
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
									
					$IPBHTML .= <<<EOF
				</td>
			</tr>
EOF;
		}
		
		$IPBHTML .= <<<EOF
		</table>
EOF;

		if ( $this->registry->getClass('class_permissions')->checkPermission( 'invoices_add', 'nexus', 'payments' ) )
		{
			$IPBHTML .= <<<EOF
			<form action='{$this->settings['base_url']}module=payments&amp;section=invoices&amp;do=add' method='post'>
				<input type='hidden' name='member_id' value='{$customer->data['member_id']}' />
				<div class='acp-actionbar'>
					<input type='submit' class='realbutton' value='{$this->lang->words['invoice_add']}' />
EOF;
						if ( isset( $pagination['invoices'] ) )
						{
							$va = sprintf( $this->lang->words['customer_view_all'], $pagination['invoices'] );
						$IPBHTML .= <<<EOF
							&nbsp;&nbsp;&nbsp;<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;member_id={$customer->data['member_id']}' class='realbutton'>{$va}</a>
EOF;
						}
				$IPBHTML .= <<<EOF
				</div>
			</form>
EOF;
		}
		
		$IPBHTML .= <<<EOF
	</div>
	
	<br /><br />

EOF;

}

if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_view', 'nexus', 'tickets' ) )
{
	$IPBHTML .= <<<EOF
	<div class='acp-box'>
		<h3>{$this->lang->words['customer_support']}</h3>
		<table class='ipsTable'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='7%'>{$this->lang->words['support_id']}</th>
				<th width='32%'>{$this->lang->words['support_title']}</th>
				<th width='19%'>{$this->lang->words['sr_created']}</th>
				<th width='19%'>{$this->lang->words['support_department']}</th>
				<th width='19%'>{$this->lang->words['support_status']}</th>
			</tr>
EOF;
	
		foreach ( $support as $supportID => $r )
		{
			$class = ( $r['r_staff'] and $r['r_staff']['member_id'] == $this->memberData['member_id'] ) ? '_amber' : '';
			$_staff = $r['r_staff'] ? "<br /><span class='desctext'>{$this->lang->words['support_list_owner']}{$r['r_staff']['members_display_name']}</span>" : '';
			
			$icon = 'icon';
			if ( !$r['read'] )
			{
				$icon .= '_unread';
			}
			if ( in_array( $r['r_id'], $dots ) )
			{
				$icon .= '_dot';
			}
			
			$date = ipsRegistry::getClass('class_localization')->getDate( $r['r_started'], 'JOINED' );
		
			$IPBHTML .= <<<EOF
			<tr class='{$class}'>
				<td><img src='{$this->settings['skin_app_url']}/images/support/{$icon}.png' /></td>
				<td>{$r['r_id']}</td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}module=tickets&amp;section=view&amp;id={$r['r_id']}'>{$r['r_title']}</a></span></td>
				<td>{$date}</td>
				<td>{$r['r_department']}</td>
				<td>{$r['r_status']}{$_staff}</td>
			</tr>
EOF;
		}
		
		$IPBHTML .= <<<EOF
		</table>
EOF;
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_add', 'nexus', 'tickets' ) )
		{
			$IPBHTML .= <<<EOF
			<div class='acp-actionbar'>
				<form action='{$this->settings['base_url']}module=tickets&amp;section=create' method='post'>
					<input type='hidden' name='member' value='{$customer->data['member_id']}' />
					<input type='submit' class='realbutton' value='{$this->lang->words['support_log']}' />
EOF;
						if ( isset( $pagination['support'] ) )
						{
							$va = sprintf( $this->lang->words['customer_view_all'], $pagination['support'] );
						$IPBHTML .= <<<EOF
							&nbsp;&nbsp;&nbsp;<a href='{$this->settings['base_url']}&amp;module=tickets&amp;section=list&amp;do=member&amp;id={$customer->data['member_id']}' class='realbutton'>{$va}</a>
EOF;
						}
				$IPBHTML .= <<<EOF
				</form>
			</div>
EOF;
		}
		
		$IPBHTML .= <<<EOF
	</div>
	
	<br /><br />
EOF;

}

if ( !empty( $referrals ) )
{

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['customer_referrals']}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='4%'>&nbsp;</th>
			<th><span class='larger_text'>{$this->lang->words['referral_name']}</span></th>
			<th>{$this->lang->words['referral_commission']}</th>
		</tr>
EOF;

	foreach ( $referrals as $data )
	{
		$IPBHTML .= <<<EOF
		<tr>
			<td>&nbsp;</td>
			<td><a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$data['member']['member_id']}'>{$data['member']['members_display_name']}</a></td>
			<td>{$data['amount']}</td>
		</tr>
EOF;
	}
	
	$IPBHTML .= <<<EOF
	</table>
	<div class='acp-actionbar'>
		<strong>{$this->lang->words['referral_total_comm']}{$referral_commission}</strong>
	</div>
</div>

<br /><br />
EOF;

}

//--endhtml--//
return $IPBHTML;
}

function _generatePurchaseRow( $item, $parentMap, $purchases )
{
	$padding = '';
	$parent = $item['ps_parent'];
	while ( $parent != 0 )
	{
		$padding .= "<img src='{$this->settings['skin_app_url']}/images/tree.gif' /> ";
		$parent = $purchases[ $parent ]['ps_parent'];
	}

	$this->menuKey++;
	
	$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] );
					
	$this->class = ( $this->class == 'row1' ) ? 'row2' : 'row1';
	if ( $item['ps_cancelled'] )
	{
		$this->class = '_red';
		$note = $this->lang->words['purchase_cancelled'];
	}
	elseif ( !$item['ps_active'] )
	{
		$this->class = '_amber';
		$note = $this->lang->words['purchase_expired'];
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
		
	$IPBHTML = <<<EOF
	<tr class='ipsControlRow {$this->class}'{$extra}>
		<td>{$appIcon}</td>
		<td>{$item['ps_id']}</td>
		<td>{$padding}<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;id={$item['ps_id']}'>{$item['ps_name']}</a><span class='desctext'>{$cf_sticky}</span><br /><em>{$note}</em></td>
		<td>{$item['ps_start']}</td>
		<td>{$item['ps_expire']}</td>
		<td>{$item['renewal']}</td>
		<td>
			<ul class='ipsControlStrip'>
				<li class='i_edit'>
					<a title='{$this->lang->words['edit']}'  href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=edit&amp;id={$item['ps_id']}'>{$this->lang->words['edit']}...</a>
				</li>
				<li class='ipsControlStrip_more ipbmenu' id='menu{$this->menuKey}'>
					<a href='#'>{$this->lang->words['options']}</a>
				</li>
			</ul>
			<ul class='acp-menu' id='menu{$this->menuKey}_menucontent'>
				<li class='icon assign'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=transfer&amp;id={$item['ps_id']}'>{$this->lang->words['purchases_transfer']}...</a></li>
EOF;
				if ( $item['ps_app'] == 'nexus' and array_key_exists( $item['ps_type'], package::$types ) )
				{
					$IPBHTML .= <<<EOF
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=change&amp;id={$item['ps_id']}'>{$this->lang->words['purchases_change']}...</a></li>
EOF;
				}
				
				$IPBHTML .= <<<EOF
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=associate&amp;id={$item['ps_id']}'>{$this->lang->words['purchases_associate']}...</a></li>
EOF;
				if ( $item['ps_renewals'] )
				{
					if ( !$item['ps_invoice_pending'] )
					{
						$IPBHTML .= <<<HTML
					<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=renew&amp;id={$item['ps_id']}'>{$this->lang->words['generate_renewal']}...</a></li>
HTML;
					}
					else
					{
						$IPBHTML .= <<<HTML
					<li class='icon view'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$item['ps_invoice_pending']}'>{$this->lang->words['view_renewal']}...</a></li>
HTML;
					}
				}
				
				if ( $item['ps_parent'] and $item['ps_renewals'] and !$item['ps_grouped_renewals'] )
				{
					$IPBHTML .= <<<HTML
					<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=groupon&amp;id={$item['ps_id']}'>Group Renewals...</a></li>
HTML;
				}
				elseif ( $item['ps_grouped_renewals'] )
				{
					$IPBHTML .= <<<HTML
					<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=groupoff&amp;id={$item['ps_id']}'>Ungroup Renewals...</a></li>
HTML;
				}
				
				$IPBHTML .= <<<EOF
				<li class='icon delete'><a  href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=cancel&amp;id={$item['ps_id']}'>{$this->lang->words['purchases_cancel']}...</a></li>
EOF;
				if ( $item['ps_cancelled'] )
				{
					$IPBHTML .= <<<HTML
					<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=uncancel&amp;id={$item['ps_id']}'>{$this->lang->words['purchases_reactivate']}...</a></li>
HTML;
				}
				
				$IPBHTML .= <<<EOF
			</ul>
		</td>
	</tr>
	<tr class='{$this->class}' style='display:none' id='cf-{$item['ps_id']}'>
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
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $child ], $parentMap, $purchases );
		}
	}

	return $IPBHTML;

}

//===========================================================================
// Search
//===========================================================================
function search( $members, $pagination, $currentSearch ) {

$searchField = $this->lang->words[ $currentSearch['option'] ];

$options = array();
$options[] = array( 'member_id', $this->lang->words['member_id'] );
$options[] = array( 'email', $this->lang->words['email'] );
foreach ( customer::fields() as $f )
{
	$options[] = array( $f['f_column'], $f['f_name'] );
	if ( $f['f_column'] == $currentSearch['option'] )
	{
		$searchField = $f['f_name'];
	}
}
$options[] = array( 'members_display_name', $this->lang->words['members_display_name'] );

$search['option'] = ipsRegistry::getClass('output')->formDropdown( 'option', $options, $currentSearch['option'] ? $currentSearch['option'] : 'contains' );
$search['criteria'] = ipsRegistry::getClass('output')->formDropdown( 'criteria', array(
	array( 'contains', $this->lang->words['contains'] ),
	array( 'equals', $this->lang->words['equals'] ),
	array( 'begins', $this->lang->words['begins'] ),
	array( 'ends', $this->lang->words['ends'] ),
	), $currentSearch['criteria'] ? $currentSearch['criteria'] : 'contains' );
	
$search['value'] = ipsRegistry::getClass('output')->formInput( 'value', $currentSearch['value'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$this->lang->words['customers']}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=search' method='post'>
<input type='hidden' name='search' value='1' />
<div class='acp-box'>
	<h3>{$this->lang->words['search_customers']}</h3>
	<table class='ipsTable double_pad'>
		<tr>
			<td style='text-align:center'>{$search['option']} {$search['criteria']} {$search['value']}</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['search']}' class='button primary' />
	</div>
</div>
</form>
<br />

EOF;

if ( $this->request['search'] or !empty( $members ) )
{

$IPBHTML .= <<<EOF
{$pagination}
<div class='acp-box'>
 	<h3>{$this->lang->words['customers']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th style='width: 30px'></th>
				<th>{$this->lang->words['ac_name']}</th>
				<th>{$this->lang->words['members_display_name']}</th>
				<th>{$this->lang->words['email']}</th>
EOF;
			if ( $this->request['do'] == 'credit' )
			{
				$IPBHTML .= <<<EOF
				<th>{$this->lang->words['customer_credit']}</th>
EOF;
			}
			if ( $this->request['search'] and !in_array( $currentSearch['option'], array( 'cm_first_name', 'cm_last_name', 'members_display_name', 'email' ) ) )
			{
				$IPBHTML .= <<<EOF
				<th>{$searchField}</th>
EOF;
			}
			$IPBHTML .= <<<EOF
			</tr>
EOF;

if ( !empty( $members ) )
{

	foreach ( $members as $id => $data )
	{
		$IPBHTML .= <<<EOF
			<tr>
				<td style='text-align: center'>
					<img src='{$data['pp_mini_photo']}' width='{$data['pp_mini_width']}' height='{$data['pp_mini_height']}' class='photo' />
				</td>
				<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$id}'>{$data['full_name']}</a></span></td>
				<td>{$data['members_display_name']}</td>
				<td>{$data['email']}</td>
EOF;
			if ( $this->request['do'] == 'credit' )
			{
				$data['cm_credits'] = $this->registry->getClass('class_localization')->formatMoney( $data['cm_credits'] );
				$IPBHTML .= <<<EOF
					<td>{$data['cm_credits']}</td>
EOF;
			}
			if ( $this->request['search'] and !in_array( $currentSearch['option'], array( 'cm_first_name', 'cm_last_name', 'members_display_name', 'email' ) ) )
			{
				$IPBHTML .= <<<EOF
				<td>{$data[ $currentSearch['option'] ]}</td>
EOF;
			}
			$IPBHTML .= <<<EOF
			</tr>
EOF;
	}
	
}
else
{
		$IPBHTML .= <<<HTML
				<tr>
					<td colspan='7'>
						<p style='text-align:center'><em>{$this->lang->words['customers_empty']}</em></p>
					</td>
				</tr>
HTML;
}
	
	$IPBHTML .= <<<EOF
			
		</table>
	</div>
</div>
<br />
{$pagination}

EOF;

}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Note
//===========================================================================

function addNote( $memberID ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=notes&amp;do=add&amp;member_id={$memberID}' method='post'>
<div class='acp-box'>
	<h3>{$this->lang->words['note_add']}</h3>
	<div style='text-align:center; padding: 15px'>
		<textarea name='note' cols='75' rows='10'></textarea>
	</div>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['save']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Edit Note
//===========================================================================

function editNote( $note ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=notes&amp;do=edit&amp;id={$note['note_id']}' method='post'>
<div class='acp-box'>
	<h3>{$this->lang->words['note_edit']}</h3>
	<div style='text-align:center;  padding: 15px'>
		<textarea name='note' cols='75' rows='10'>{$note['note_text']}</textarea>
	</div>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['save']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// History
//===========================================================================

function history( $member, $items, $deleted ) {

$id = array( 'info' => 0, 'purchase' => 0, 'invoice' => 0, 'transaction' => 0, 'support' => 0, 'note' => 0, 'mail' => 0, 'payout' => 0 );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
EOF;
if ( $deleted )
{
	$IPBHTML .= <<<EOF
	<h2>{$member['name']}</h2>
EOF;
}
else
{
	$IPBHTML .= <<<EOF
	<h2><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$member['member_id']}'>{$member['_name']}</a></h2>
EOF;
}

$IPBHTML .= <<<EOF
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}module=customers&amp;section=history&amp;id={$member['member_id']}&amp;sort=asc'><img src='{$this->settings['skin_app_url']}/images/up.png' /> {$this->lang->words['history_oldest']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}module=customers&amp;section=history&amp;id={$member['member_id']}&amp;sort=desc'><img src='{$this->settings['skin_app_url']}/images/down.png' /> {$this->lang->words['history_newest']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box' style='width:70%; float: left'>
 	<h3>{$this->lang->words['customer_history']}</h3>
	<div>
		<table class='ipsTable' id='results'>
			<tr>
				<th width='3%'>&nbsp;</th>
				<th width='15%'>{$this->lang->words['history_date']}</th>
				<th width='15%'>{$this->lang->words['history_ipaddress']}</th>
				<th>{$this->lang->words['history_action']}</th>
			</tr>
EOF;

		foreach ( $items as $i )
		{
			$id[ $i['type'] ]++;
			$i['date'] = ipsRegistry::getClass('class_localization')->getDate( $i['date'], '' );

			$ip_address	= '';

			if( $i['ip_address'] )
			{
				$ip_address	= "<a href='{$this->settings['_base_url']}&amp;app=members&amp;module=members&amp;section=tools&amp;do=learn_ip&amp;ip={$i['ip_address']}'>{$i['ip_address']}</a>";
			}
			else
			{
				$ip_address = "<span title='{$this->lang->words['ip_not_found_desc']}'><em>{$this->lang->words['ip_not_found']}</em></span>";
			}
			
			$IPBHTML .= <<<EOF
			<tr id='{$i['type']}{$id[ $i['type'] ]}'>
				<td><img src='{$this->settings['skin_app_url']}/images/customers/{$i['icon']}.png' alt='' /></td>
				<td>{$i['date']}</td>
				<td>{$ip_address}</td>
				<td><span class='larger_text'>{$i['action']}</span></td>
			</tr>
EOF;
			
		}
		
	$IPBHTML .= <<<EOF
		</table>
	</div>
</div>

<div class='acp-box' style='width:28%; float: right'>
	<h3>Filters</h3>
	<table class='ipsTable'>
EOF;

	foreach ( $id as $type => $count )
	{
		if ( !$count )
		{
			continue;
		}
		
		$IPBHTML .= <<<EOF
		<tr>
			<td width='18px'><img src='{$this->settings['skin_app_url']}/images/customers/{$type}.png' /></td>
			<td width='18px'><input type='checkbox' checked='checked' id='filter_{$type}' onchange='updateResults("{$type}")' /></td>
			<td><label for='filter_{$type}'>{$this->lang->words[ 'history_type_'. $type ]}</label></td>
		</tr>
EOF;
	}
	
	$IPBHTML .= <<<EOF
	</table>
</div>

<script type='text/javascript'>

function updateResults( type )
{
	var i = 1;
	while ( true == true )
	{
		if ( $( type + i ) == undefined )
		{
			break;
		}
				
		\$( type + i ).toggle();
		
		i++;
	}
}

</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Send Email
//===========================================================================

function email( $memberID ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=email&amp;member_id={$memberID}' method='post'>
<div class='acp-box'>
	<h3>{$this->lang->words['customer_send_email']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email_from']}</strong></td>
			<td class='field_field'><input name='from' value='{$this->settings['email_out']}' size='30' /></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email_subject']}</strong></td>
			<td class='field_field'><input name='subject' size='30' /></td>
		</tr>
		<tr>
			<td class='field_field' colspan='2' style='text-align: center'><textarea name='message' cols='75' rows='10'></textarea></td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['customer_send_email']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Email
//===========================================================================

function viewEmail( $data ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['customer_email']}</h3>
	<table class="ipsTable">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email_from']}</strong></td>
			<td class='field_field'>{$data['from']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email_subject']}</strong></td>
			<td class='field_field'>{$data['subject']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['customer_email_message']}</strong></td>
			<td class='field_field'><div style='max-height: 600px; overflow: auto;'>{$data['message']}</div></td>
		</tr>
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Alternate Contact Form
//===========================================================================
function altContact( $customer, $current, $purchases, $error=NULL ) {

$title = empty( $current ) ? "{$this->lang->words['ac_add']}: {$customer->data['_name']}" : $this->lang->words['ac_edit'];

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$title}</h2>
</div>

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

<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=alternates&amp;do=save' method='post'>
<input type='hidden' name='member_id' value='{$customer->data['member_id']}' />
<div class='acp-box'>

HTML;

if ( empty( $current ) )
{
	$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ipsRegistry::$request['name'], 'member' );
	$form['email'] = ipsRegistry::getClass('output')->formInput( 'email', ipsRegistry::$request['email'] );
	$form['target_id'] = ipsRegistry::getClass('output')->formSimpleInput( 'target_id', ipsRegistry::$request['target_id'] );
	$IPBHTML .= <<<HTML

	<h3>{$this->lang->words['purchase_transfer_h3']}</h3>
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchase_transfer_to_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchase_transfer_to_email']}</strong></td>
			<td class='field_field'>{$form['email']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['purchase_transfer_to_id']}</strong></td>
			<td class='field_field'>{$form['target_id']}</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['continue']}' class='real button' />
	</div>
		
HTML;
}
else
{
	$form['purchases'] = ipsRegistry::getClass('output')->formMultiDropdown( 'purchases[]', $purchases, $current['purchases'], 10, '', 'style="width: 250px"' );
	$form['billing'] = ipsRegistry::getClass('output')->formYesNo( 'billing', $current['billing'] );
	$form['support'] = ipsRegistry::getClass('output')->formYesNo( 'support', $current['support'] );

	$IPBHTML .= <<<EOF
	<input type='hidden' name='id' value='{$current['alt_id']['member_id']}' />
	<h3>{$this->lang->words['ac_edit']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ah']}</strong></td>
			<td class='field_field'>{$customer->data['_name']} <span class='desctext'>({$customer->data['member_id']} - {$customer->data['email']})</span></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ac']}</strong></td>
			<td class='field_field'>{$current['alt_id']['_name']} <span class='desctext'>({$current['alt_id']['member_id']} - {$current['alt_id']['email']})</span></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ac_purchases']}</strong></td>
			<td class='field_field'>
				{$form['purchases']}
			</td>
		</tr>
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->lang->words['altcontacts__billing']}</strong>
			</td>
			<td class='field_field'>
				{$form['billing']}<br />
				<span class='desctext'>{$this->lang->words['altcontacts_billing']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['altcontacts__support']}</strong></td>
			<td class='field_field'>
				{$form['support']}<br />
				<span class='desctext'>{$this->lang->words['altcontacts_support']}</span>
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['save']}' class='real button' />
	</div>
EOF;
}

$IPBHTML .= <<<HTML
</div>
</form>

HTML;

if ( empty( $current ) )
{
	$IPBHTML .= <<<HTML
<script type='text/javascript'>
document.observe("dom:loaded", function(){
	var autoComplete = new ipb.Autocomplete( $('member'), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
});
</script>

HTML;

}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Void Account
//===========================================================================
function void() {

foreach ( array( 'rfd', 'cp', 'ci', 'rs', 'b' ) as $k )
{
	$form[ $k ] = $this->registry->output->formYesNo( $k, 1 );
}


$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['customer_void']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;member_id={$this->request['member_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' /> {$this->lang->words['cancel']}</a></li>
		</ul>
	</div>
</div>
<div class="warning">
	{$this->lang->words['void_noundo']}
</div>
<br />

<div class='acp-box'>
	<form action='{$this->settings['base_url']}&amp;module=customers&amp;section=void&amp;do=go&amp;member_id={$this->request['member_id']}' method='post'>
	<h3>{$this->lang->words['customer_void']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['void_rfd']}</strong></td>
			<td class='field_field'>
				{$form['rfd']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['void_cp']}</strong></td>
			<td class='field_field'>
				{$form['cp']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['void_ci']}</strong></td>
			<td class='field_field'>
				{$form['ci']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['void_rs']}</strong></td>
			<td class='field_field'>
				{$form['rs']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['void_b']}</strong></td>
			<td class='field_field'>
				{$form['b']}
			</td>
		</tr>
HTML;
	
	if ( $this->registry->getClass('class_permissions')->checkPermission( 'notes_add' ) )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['note_add']}</strong></td>
			<td class='field_field'>
				<textarea name='note' cols='75' rows='10'></textarea>
			</td>
		</tr>
HTML;
	}
	
		
$IPBHTML .= <<<HTML
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['customer_void']}' class='real button' />
	</div>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Standing
//===========================================================================
function standing( $customer, $standings ) {

$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$customer->data['member_id']}'>{$customer->data['_name']}</a></h2>
</div>

<div class='information-box'>
	{$this->lang->words['standing_desc']}
</div>
<br />

<div class='acp-box'>
	<h3>{$this->lang->words['standing']}</h3>
	<table class='ipsTable'>
HTML;
		foreach ( $standings as $type => $data )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td style='width: 20%'><strong class='title'>{$this->lang->words['standing_' . $type]}</strong></td>
			<td style='width: 10%'>{$data['value']}</td>
			<td style='width: 70%'>{$data['graph']}</td>
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