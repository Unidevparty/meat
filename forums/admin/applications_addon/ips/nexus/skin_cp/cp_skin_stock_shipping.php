<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Tax Classes
 * Last Updated: $Date: 2012-01-23 06:04:11 -0500 (Mon, 23 Jan 2012) $
 * </pre>
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		11th May 2010
 * @version		$Revision: 10166 $
 */
 
class cp_skin_stock_shipping
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
// Manage Shipping Methods
//===========================================================================
function manage( $methods ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['shipping']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['shipping_add']}</a></li>
		</ul>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=fedex'><img src='{$this->settings['skin_acp_url']}/images/icons/cog.png' alt='' /> {$this->registry->getClass('class_localization')->words['fedex_settings']}</a></li>
		</ul>
	</div>
</div>

HTML;

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['shipping']}</h3>
	<table class='ipsTable' id='shippingMethods_list'>
		<tr>
			<th width='3%'>&nbsp;</th>
			<th width='89%'>{$this->registry->getClass('class_localization')->words['shipping_name']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	if ( !empty( $methods ) )
	{
		foreach ( $methods as $id => $data )
		{
			$menuKey++;
			$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='methods_{$id}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td><span class='larger_text'>{$data['s_name']}</span></td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=edit&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=delete&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
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
				{$this->registry->getClass('class_localization')->words['shipping_empty']}
			</td>
		</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#shippingMethods_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=shipping&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current=array(), $countryList, $selectedStates, $tax, $allLocations, $rates ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['shipping_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['shipping_edit'];
	$id = $current['s_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['s_name'] ) );
$form['tax'] = ipsRegistry::getClass('output')->formDropdown( 'tax', array_merge( array( array( 0, $this->registry->getClass('class_localization')->words['package_notax'] ) ), $tax ), ( empty( $current ) ? '' : $current['s_tax'] ) );

$allLocations = $allLocations ? "checked='checked'" : '';

$base = array( 'w' => '', 't' => '', 'q' => '' );
if ( empty( $current ) )
{
	$base['w'] = "checked='checked'";
}
else
{
	$base[ $current['s_type'] ] = "checked='checked'";
}

$IPBHTML = "";

$IPBHTML .= <<<EOF
	<script type='text/javascript'>
		var _countriesWithStates = [];
	</script>
EOF;

$stateLists = array();
foreach ( customer::generateStateDropdown() as $country => $states )
{
	$stateLists[ $country ] .= ipsRegistry::getClass('output')->formMultiDropdown( "loc_states[{$country}][]", $states, $selectedStates[ $country ], 10, $country.'-states', "style='display:none'" );
	$IPBHTML .= <<<EOF
	<script type='text/javascript'>
		_countriesWithStates["{$country}"] = 1;
	</script>
EOF;
}

//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['shipping_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['shipping_applies']}</strong></td>
			<td class='field_field'>
				<input type='checkbox' name='loc_all' onchange='countryList();' id='loc_all' {$allLocations} /> {$this->registry->getClass('class_localization')->words['all_locations']}
				<span id='loc_countries' style='display:none'>
					<br /><br />
					<select id='loc_countries_sel' name='loc_countries[]' multiple='multiple' onchange='stateList();'>{$countryList}</select>
				</span>
				<span id='loc_states' style='display:none'>
					<br /><br />
HTML;
				foreach ( $stateLists as $selectBox )
				{
					$IPBHTML .= $selectBox;
				}
					$IPBHTML .= <<<HTML
				</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['shipping_price']}</strong></td>
			<td class='field_field'>
				<input type='radio' name='base' value='w' {$base['w']} /> {$this->registry->getClass('class_localization')->words['shipping_price_w']}<br />
				<input type='radio' name='base' value='t' {$base['t']} /> {$this->registry->getClass('class_localization')->words['shipping_price_t']} <span class='desctext'>({$this->lang->words['shipping_price_t_desc']})</span><br />
				<input type='radio' name='base' value='q' {$base['q']} /> {$this->registry->getClass('class_localization')->words['shipping_price_q']}<br />
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['shipping_rate']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['shipping_rate_desc']}</span></label>
			<td class='field_field'>
				<table id='rates'>
					<tr>
						<th class='subhead'>{$this->registry->getClass('class_localization')->words['shipping_rate_value']}</th>
						<th class='subhead'>{$this->registry->getClass('class_localization')->words['shipping_rate_cost']}</th>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['package_tax']}</strong></td>
			<td class='field_field'>{$form['tax']}</td>
		</tr>
	</table>	
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
</div>

<script type='text/javascript'>
	var rates = -1;
	
	function rateChange( id )
	{			
		if( id == rates && $( 'rate-to-' + id ).value )
		{
			rates++;
			addRate( rates, '', '' );
		}
	}
	
	function addRate( id, value, cost )
	{
		var row = $('rates').insertRow( $('rates').rows.length );
		
		var cell_to = row.insertCell(0);
		cell_to.innerHTML = "<input name='rate-to-"+ id +"' id='rate-to-"+ id +"' value='"+ value +"' onchange='rateChange("+ id +")' />";
		
		var cell_cost = row.insertCell(1);
		cell_cost.innerHTML = "<input name='rate-cost-"+ id +"' value='"+ cost +"' />";
	}
	
	function countryList( id )
	{	
		if( $( 'loc_all' ).checked )
		{
			$( 'loc_countries' ).style.display = 'none';
		}
		else
		{
			$( 'loc_countries' ).style.display = 'inline';
		}
	}
	
	function stateList( id )
	{
		var sel = $( 'loc_countries_sel' );
		var us = false;
		for ( var foo = 0; foo <= $( 'loc_countries_sel' ).length; foo++ )
		{
			if ( sel[foo] != undefined )
			{
				if ( sel[ foo ].value in _countriesWithStates )
				{
					if ( sel[ foo ].selected )
					{
						us = true;
						$( sel[ foo ].value + '-states' ).style.display = 'inline';
					}
					else
					{
						$( sel[ foo ].value + '-states' ).style.display = 'none';
					}
				}
			}
		}
		
		if ( us )
		{
			$( 'loc_states' ).style.display = 'inline';
		}
		else
		{
			$( 'loc_states' ).style.display = 'none';
		}
	}
	
	countryList();
	stateList();
	
HTML;

	foreach ( $rates as $id => $data )
	{
		$IPBHTML .= <<<HTML
		addRate( {$id}, '{$data['value']}', '{$data['cost']}' );
		rates++;
HTML;
	}

$IPBHTML .= <<<HTML
	
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function fedex( $fedexServices, $taxClasses ) {

$this->settings['fedex_owner'] = unserialize( $this->settings['fedex_owner'] );

$form['key'] = $this->registry->output->formInput( 'key', $this->settings['fedex_key'] );
$form['password'] = $this->registry->output->formInput( 'password', $this->settings['fedex_password'] );
$form['account_number'] = $this->registry->output->formSimpleInput( 'account_number', $this->settings['fedex_account_number'], 10 );
$form['meter_number'] = $this->registry->output->formSimpleInput( 'meter_number', $this->settings['fedex_meter_number'], 10 );
$form['name'] = $this->registry->output->formInput( 'name', $this->settings['fedex_owner']['Contact']['PersonName'] );
$form['title'] = $this->registry->output->formInput( 'title', $this->settings['fedex_owner']['Contact']['Title'] );
$form['company'] = $this->registry->output->formInput( 'company', $this->settings['fedex_owner']['Contact']['CompanyName'] );
$form['department'] = $this->registry->output->formInput( 'department', $this->settings['fedex_owner']['Contact']['Department'] );
$form['address_1'] = $this->registry->output->formInput( 'address_1', $this->settings['fedex_owner']['Address']['StreetLines'][0] );
$form['address_2'] = $this->registry->output->formInput( 'address_2', $this->settings['fedex_owner']['Address']['StreetLines'][1] );
$form['city'] = $this->registry->output->formInput( 'city', $this->settings['fedex_owner']['Address']['City'] );
$form['zip'] = $this->registry->output->formSimpleInput( 'zip', $this->settings['fedex_owner']['Address']['PostalCode'] );
$form['phone'] = $this->registry->output->formInput( 'phone', $this->settings['fedex_owner']['Contact']['PhoneNumber'] );
$form['pager'] = $this->registry->output->formInput( 'pager', $this->settings['fedex_owner']['Contact']['PagerNumber'] );
$form['fax'] = $this->registry->output->formInput( 'fax', $this->settings['fedex_owner']['Contact']['FaxNumber'] );
$form['email'] = $this->registry->output->formInput( 'email', $this->settings['fedex_owner']['Contact']['EmailAddress'] );
$form['services'] = $this->registry->output->formMultiDropDown( 'services[]', $fedexServices, $this->settings['fedex_services'] ? explode( ',', $this->settings['fedex_services'] ) : '', count( $fedexServices ) );
$form['price_adjust'] = $this->registry->output->formSimpleInput( 'price_adjust', $this->settings['fedex_price_adjust'] );
$form['shiptime_adjust'] = $this->registry->output->formSimpleInput( 'shiptime_adjust', $this->settings['fedex_shiptime_adjust'] );
$form['tax'] = $this->registry->output->formDropDown( 'tax', $taxClasses, $this->settings['fedex_tax'] );

$IPBHTML .= <<<EOF
	<script type='text/javascript'>
		var _countriesWithStates = [];
	</script>
EOF;

$form['state'] = ipsRegistry::getClass('output')->formInput( '_state', $this->settings['fedex_owner']['Address']['StateOrProvinceCode'], 'text-states', 30, 'text', "style='display:none'" );
foreach ( customer::generateStateDropdown( TRUE ) as $country => $states )
{
	$form['state'] .= ipsRegistry::getClass('output')->formDropdown( '_state', $states, $this->settings['fedex_owner']['Address']['StateOrProvinceCode'], $country.'-states', "style='display:none'" );
	$IPBHTML .= <<<EOF
	<script type='text/javascript'>
		_countriesWithStates["{$country}"] = 1;
	</script>
EOF;
}

$dropdown = array();
foreach ( customer::getCountryList() as $k => $v )
{
	$dropdown[] = array( $k, $v );
}
$form['country'] = ipsRegistry::getClass('output')->formDropdown( 'country', $dropdown, $this->settings['fedex_owner']['Address']['CountryCode'], 'cm_country', 'onchange="states();"' );

//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['fedex_settings']}</h2>
</div>

<div class='information-box'>
	{$this->lang->words['fedex_blurb']}
</div>
<br />

<div class='acp-box'>
	<h3>{$this->lang->words['fedex_settings']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=shipping&amp;do=fedex_save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<th colspan='2'>{$this->lang->words['fedex_account_details']}</th>
		</tr>
HTML;
		foreach ( array( 'key', 'password', 'account_number', 'meter_number' ) as $k )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_' . $k]}</strong></td>
			<td class='field_field'>{$form[ $k ]}</td>
		</tr>
HTML;
		}
		$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['fedex_shipper_details']}</th>
		</tr>
HTML;
		foreach ( array( 'name', 'title', 'company', 'department', 'address_1', 'address_2', 'city', 'state', 'zip', 'country', 'phone', 'pager', 'fax', 'email' ) as $k )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_' . $k]}</strong></td>
			<td class='field_field'>{$form[ $k ]}</td>
		</tr>
HTML;
		}
		$IPBHTML .= <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['fedex_other_details']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_services']}</strong></td>
			<td class='field_field'>
				{$form['services']}<br />
				<span class='desctext'>{$this->lang->words['fedex_services_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_price_adjust']}</strong></td>
			<td class='field_field'>
				{$form['price_adjust']} {$this->settings['nexus_currency']}<br />
				<span class='desctext'>{$this->lang->words['fedex_price_adjust_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_tax']}</strong></td>
			<td class='field_field'>
				{$form['tax']}<br />
				<span class='desctext'>{$this->lang->words['fedex_tax_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_shiptime_adjust']}</strong></td>
			<td class='field_field'>
				{$form['shiptime_adjust']} {$this->lang->words['renew_term_days']}<br />
				<span class='desctext'>{$this->lang->words['fedex_shiptime_adjust_desc']}</span>
			</td>
		</tr>
	</table>	
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
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
			$( c + '-states' ).name = 'state';
			
			_display = c + '-states';
		}
		else
		{
			$( _display ).style.display = 'none';
			$( _display ).name = '_state';
			
			$( 'text-states' ).style.display = '';
			$( 'text-states' ).name = 'state';
			
			_display = 'text-states';
		}
	}
	
	var _display = 'text-states';
	states();

</script>


HTML;

//--endhtml--//
return $IPBHTML;
}


}