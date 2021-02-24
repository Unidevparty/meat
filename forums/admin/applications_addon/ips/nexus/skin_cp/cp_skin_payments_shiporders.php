<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - Shipping Orders
 * Last Updated: $Date: 2013-05-02 11:25:11 -0400 (Thu, 02 May 2013) $
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		14th May 2010
 * @version		$Revision: 12214 $
 */
 
class cp_skin_payments_shiporders
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
function orders( $orders, $pagination, $pendingOnly ) {

$menuKey = 0;
$filter  = array( 'pending' => '', 'all' => '' );

$alt = '';
if( $pendingOnly )
{
	$alt = ' alt';
	$filter['pending'] = 'active';
}
else
{
	$filter['all'] = 'active';
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['shiporders']}</h2>
</div>

<p class='section_filter'>
	{$this->registry->getClass('class_localization')->words['show']} <a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders' class='{$filter['all']}'>{$this->registry->getClass('class_localization')->words['shiporders_all']}</a> <a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;pend=1' class='{$filter['pending']}'>{$this->registry->getClass('class_localization')->words['shiporders_pend']}</a>
</p>
<div class='acp-box{$alt}'>
 	<h3>{$this->registry->getClass('class_localization')->words['shiporders']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='5%'>&nbsp;</th>
				
				<th>&nbsp;</th>
				<th>{$this->registry->getClass('class_localization')->words['shiporders_method']}</th>
				<th>{$this->registry->getClass('class_localization')->words['shiporders_member']}</th>
				<th>{$this->registry->getClass('class_localization')->words['shiporders_invoice']}</th>
				<th>{$this->registry->getClass('class_localization')->words['shiporders_date']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
EOF;

if ( !empty( $orders ) )
{
	foreach ( $orders as $id => $data )
	{
		$row = ($row == 'acp-row-off') ? 'acp-row-on' : 'acp-row-off';
		
		$menuKey++;
		switch ( $data['status'] ) { case 'done': $badgeColor = 'green'; break; case 'pend': $badgeColor = 'grey'; break; case 'canc': $badgeColor = 'red'; }	
			
		$IPBHTML .= <<<EOF
			<tr class='ipsControlRow'>
				<td>
					<span class='ipsBadge badge_{$badgeColor}' style='width: 60px; text-align: center'>
						{$this->registry->getClass('class_localization')->words[ 'shstatus_' . $data['status'] ]}
					</span>
				</td>
				<td>
					<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=view&amp;id={$id}'>
						<span class='desctext'>{$this->registry->getClass('class_localization')->words['shiporder_view']}</span>
					</a>
				</td>
				<td>{$data['method']}</td>
				<td><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$data['member']['member_id']}'>{$data['member']['_name']}</a></td>
				<td><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$data['invoiceID']}'>{$data['invoiceTitle']}</a></td>
				<td>{$data['date']}</td>
				
				<td>
					<ul class='ipsControlStrip'>
						<li class='i_delete'>
							<a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=set&amp;id={$id}&amp;status=canc' title='{$this->registry->getClass('class_localization')->words['cancel']}'>{$this->registry->getClass('class_localization')->words['cancel']}</a>
						</li>
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
						{$this->registry->getClass('class_localization')->words['shiporder_empty']}
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

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Order
//===========================================================================
function view( $order, $invoice, $customer, $items, $methods, $canApiShip, $proofOfDelivery, $trackingInfo ) {
	
if ( isset( $order['o_data'] ) )
{
	// Normalize o_data due to inconsistency in storage between versions.
	foreach( $order['o_data'] AS $k => $v )
	{
		if ( in_array( $k, array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'zip', 'country' ) ) )
		{
			$order['o_data']['cm_'.$k] = $v;
		}
	}
}

$address = $order['o_data']['cm_address_2'] ? $order['o_data']['cm_address_1'] . '<br />' . $order['o_data']['cm_address_2'] : $order['o_data']['cm_address_1'];
$countries = customer::getCountryList();

$methodName = $this->lang->words['no_shipping_selected']; 
if ( $order['o_api'] )
{
	if ( isset( $this->lang->words[ $order['o_api_service'] ] ) )
	{
		$methodName = $this->lang->words[ $order['o_api_service'] ];
	}
	elseif ( isset( $this->lang->words[ 'shipping_api_' . $order['o_api'] ] ) )
	{
		$methodName = $this->lang->words[ 'shipping_api_' . $order['o_api'] ];
	}
}
elseif ( isset( $methods[ $order['o_method'] ] ) )
{
	$methodName = $methods[ $order['o_method'] ];
}

$trackUrl = NULL;
if ( isset( $trackingInfo['url'] ) )
{
	$trackUrl = $trackingInfo['url'];
}
elseif ( $order['o_service'] and $order['o_tracknumber'] )
{
	$url = '#';
	foreach ( explode( "\n", $this->settings['nexus_track_urls'] ) as $v )
	{
		$exploded = explode( '~', $v );
		if ( $exploded[0] == $order['o_service'] )
		{
			$url = str_replace( '{NUMBER}', $order['o_tracknumber'], $exploded[1] );
		}
	}
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['shiporder_prefix']}{$methodName}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
HTML;
		if ( $order['o_status'] != 'done' )
		{
			if ( $canApiShip )
			{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=api_ship&amp;id={$order['o_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/cog.png' alt='' /> {$this->lang->words['shiporder_arrange']}</a></li>
HTML;
			}
			else
			{
				$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='#' id='shipbutton'><img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' /> {$this->lang->words['shiporder_mark']}</a></li>
HTML;
			}
			
		}
		
		if ( $order['o_status'] != 'canc' )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=set&amp;id={$order['o_id']}&amp;status=canc&amp;v=1'><img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' alt='' /> {$this->lang->words['cancel']}</a></li>
HTML;
		}

		
		if ( $trackUrl )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$trackUrl}' target='_blank'><img src='{$this->settings['skin_app_url']}/images/shipping/track.png' alt='' /> {$this->lang->words['track_link']}</a></li>
HTML;
		}
		
		if ( $trackUrl )
		{
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=proof_of_delivery&amp;id={$order['o_id']}' target='_blank'><img src='{$this->settings['skin_app_url']}/images/shipping/sig.png' alt='' /> {$this->lang->words['shiporder_proof']}</a></li>
HTML;
		}
		
			$IPBHTML .= <<<HTML
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=label&amp;id={$order['o_id']}' target='_blank'><img src='{$this->settings['skin_app_url']}/images/shipping/label.png' alt='' /> {$this->lang->words['shiporder_label']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=packing_note&amp;id={$order['o_id']}' target='_blank'><img src='{$this->settings['skin_app_url']}/images/shipping/pack.png' alt='' /> {$this->lang->words['shiporder_print']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['shiporder_information']}</h3>
	<table class='ipsTable'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_status']}</strong></td>
			<td class='field_field'>{$this->lang->words[ 'shipstatus_' . $order['o_status'] ]}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporders_member']}</strong></td>
			<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$customer->data['member_id']}'>{$customer->data['_name']}</a></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporders_invoice']}</strong></td>
			<td class='field_field'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$invoice->id}'>{$invoice->title}</a></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporders_date']}</strong></td>
			<td class='field_field'>{$this->lang->getDate( $order['o_date'], 'JOINED' )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporders_shipped_date']}</strong></td>
			<td class='field_field'>{$this->lang->getDate( $order['o_shipped_date'], 'JOINED' )}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_address']}</strong></td>
			<td class='field_field'>
				{$order['o_data']['cm_first_name']} {$order['o_data']['cm_last_name']}<br />
				{$address}<br />
				{$order['o_data']['cm_city']}<br />
				{$order['o_data']['cm_state']}<br />
				{$order['o_data']['cm_zip']}<br />
				{$countries[ $order['o_data']['cm_country'] ]}<br />
			</td>
		</tr>
HTML;

		if ( $order['o_service'] )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_service']}</strong></td>
			<td class='field_field'>{$order['o_service']}</td>
		</tr>
HTML;
		}
		
		if ( $order['o_tracknumber'] )
		{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_tracknumber']}</strong></td>
			<td class='field_field'>{$order['o_tracknumber']}</td>
		</tr>
HTML;
		}
		
		if ( $order['o_extra'] )
		{
			foreach ( unserialize( $order['o_extra'] ) as $k => $v )
			{
			$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_extra_' . $k]}</strong></td>
			<td class='field_field'>{$v}</td>
		</tr>
HTML;
			}
		}
		
		$IPBHTML .= <<<HTML
	</table>
</div>
<br />

<div class='acp-box'>
 	<h3>{$this->lang->words['invoice_items']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th style='width:20px'>&nbsp;</th>
				<th>{$this->lang->words['item_name']}</th>
				<th>{$this->lang->words['item_qty']}</th>
				<th>{$this->lang->words['item_cfields']}</th>
			</tr>
HTML;
	
	foreach( $items as $item )
	{
		$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['app'], $item['type'], $item['cost'], $item['renewal'] );

		if ( isset( $item['adminURI'] ) )
		{
			$item['itemName'] = "<a href='{$this->settings['base_url']}{$item['adminURI']}'>{$item['itemName']}</a>";
		}
		elseif ( $item['itemURI'] )
		{
			$item['itemName'] = "<a href='{$this->settings['board_url']}/index.php?{$item['itemURI']}'>{$item['itemName']}</a>";
		}
		
		$IPBHTML .= <<<HTML
			<tr>
				<td>{$appIcon}</td>
				<td><span class='larger_text'>{$item['itemName']}</span></td>
				<td>{$item['quantity']}</td>
				<td>{$item['cfields']}</td>
			</tr>
HTML;
	}
	
$IPBHTML .= <<<HTML
		</table>
	</div>
</div>
<br />

HTML;

if ( !empty( $trackingInfo['data'] ) )
{
	$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->lang->words['tracking_info']}</h3>
	<table class='ipsTable'>
		<tr>
			<th>{$this->lang->words['track_time']}</th>
			<th>{$this->lang->words['track_event']}</th>
			<th>{$this->lang->words['track_location']}</th>
			<th>{$this->lang->words['track_notes']}</th>
		</tr>
HTML;
	foreach ( $trackingInfo['data'] as $event )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td>{$this->lang->getDate( $event['time'], 'LONG' )}</td>	
			<td class='field_field'>{$event['event']}</td>
			<td class='field_field'>{$event['location']}</td>
			<td class='field_field'>{$event['notes']}</td>
		</tr>
HTML;
	}
$IPBHTML .= <<<HTML
	</table>
</div>
HTML;
}

$IPBHTML .= <<<HTML
<script type='text/javascript'>
	$('shipbutton').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=shipping&do=ship&id={$order['o_id']}&secure_key=" + ipb.vars['md5_hash'],'shipbutton' ) );
	function doPopUp( e, url, popupid )
	{
		Event.stop(e);
		new ipb.Popup( popupid, { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: url, modal: true } );
	}
</script>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Packing Note
//===========================================================================
function packingNote( $order=array(), $invoice ) {

if ( isset( $order['data'] ) )
{
	// Normalize o_data due to inconsistency in storage between versions.
	foreach( $order['data'] AS $k => $v )
	{
		if ( in_array( $k, array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'zip', 'country' ) ) )
		{
			$order['data']['cm_'.$k] = $v;
		}
	}
}

$address = $order['data']['cm_address_2'] ? $order['data']['cm_address_1'] . '<br />' . $order['data']['cm_address_2'] : $order['data']['cm_address_1'];
$countries = customer::getCountryList();

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
		  width:800px;
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
				</div>
				
				<div style='float:right'>
					<br /><br /><br />
					{$this->registry->getClass('class_localization')->getDate( $invoice->date, 'LONG', TRUE )}
				</div>
				
				<br style='clear:both' />
				
				</div>
		
			<p>
				{$order['data']['cm_first_name']} {$order['data']['cm_last_name']}<br />
				{$address}<br />
				{$order['data']['cm_city']}<br />
				{$order['data']['cm_state']}<br />
				{$order['data']['cm_zip']}<br />
				{$countries[ $order['data']['cm_country'] ]}
			</p>
			
			<hr />
			
			<div id='content'>
			    <table>
					<tr>
						<td style='font-weight:bold'>{$this->registry->getClass('class_localization')->words['item_name']}</td>
						<td style='font-weight:bold'>{$this->registry->getClass('class_localization')->words['item_qty']}</td>
						<td style='font-weight:bold'>&nbsp;</td>
					</tr>
HTML;
	
	foreach( $order['items'] as $item )
	{		
		$IPBHTML .= <<<HTML
			<tr>
				<td>{$item['itemName']}</td>
				<td>{$item['quantity']}</td>
				<td>{$item['cfields']}</td>
			</tr>
HTML;
	}
	
$IPBHTML .= <<<HTML
				</table>
				<hr />
		  </div>
		</div>
	</body>

</html>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Label
//===========================================================================
function label( $order=array() ) {
	
if ( isset( $order['data'] ) )
{
	// Normalize o_data due to inconsistency in storage between versions.
	foreach( $order['data'] AS $k => $v )
	{
		if ( in_array( $k, array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'zip', 'country' ) ) )
		{
			$order['data']['cm_'.$k] = $v;
		}
	}
}	

$address = $order['data']['cm_address_2'] ? $order['data']['cm_address_1'] . '<br />' . $order['data']['cm_address_2'] : $order['data']['cm_address_1'];
$countries = customer::getCountryList();

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset={$this->settings['gb_char_set']}" />
		<style type="text/css">
		body {
		  font-family:Tahoma;
		  font-size: 3em;
		}
		#page {
		  width:800px;
		  margin:0 auto;
		  padding:15px;
		
		}
		</style>
		<script type='text/javascript'>
			javascript:window.print()
		</script>
	</head>
	<body>
		<div id="page">
			<p>
				{$order['data']['cm_first_name']} {$order['data']['cm_last_name']}<br />
				{$address}<br />
				{$order['data']['cm_city']}<br />
				{$order['data']['cm_state']}<br />
				{$order['data']['cm_zip']}<br />
				{$countries[ $order['data']['cm_country'] ]}
			</p>
		  </div>
		</div>
	</body>
</html>
HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Ship Popup
//===========================================================================

function shipPopup( $order ) {

$services = array( array( 0, $this->lang->words['shiporder_notracking'] ) );
foreach ( explode( "\n", $this->settings['nexus_track_urls'] ) as $v )
{
	$exploded = explode( '~', $v );
	$services[] = array( $exploded[0], $exploded[0] );
}

$form['service'] = $this->registry->output->formDropdown( 'service', $services, $order['o_service'] );
$form['tracknumber'] = $this->registry->output->formInput( 'tracknumber', $order['o_tracknumber'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=set&amp;id={$order['o_id']}&amp;status=done&amp;v=1' method='post'>
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['shiporder_mark']}</h3>
	<table class="form_table double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_service']}</strong></td>
			<td class='field_field'>{$form['service']}</td>
		</tr>

		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_tracknumber']}</strong></td>
			<td class='field_field'>{$form['tracknumber']}</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['shiporder_mark']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// API Ship: Manual Ship
//===========================================================================
function manualShip( $order ) {
$methodName = $this->lang->words['shipping_api_fedex']; 
if ( isset( $this->lang->words[ $order['o_api_service'] ] ) )
{
	$methodName = $this->lang->words[ $order['o_api_service'] ];
}
$form['tracknumber'] = $this->registry->output->formInput( 'tracknumber', $order['o_tracknumber'] );
$IPBHTML = "";
//--starthtml--//
$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$this->lang->words['shiporder_prefix']}{$methodName}</h2>
</div>
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=set&amp;id={$order['o_id']}&amp;status=done&amp;v=1' method='post'>
<input type='hidden' name='service' value='API' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['shiporder_mark']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_tracknumber']}</strong></td>
			<td class='field_field'>{$form['tracknumber']}</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['shiporder_mark']}' class='realbutton' />
	</div>
</div>
</form>
EOF;
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// API Ship: Fedex
//===========================================================================
function apiShip_fedex( $order, $locations, $error ) {

$methodName = $this->lang->words['shipping_api_fedex']; 
if ( isset( $this->lang->words[ $order['o_api_service'] ] ) )
{
	$methodName = $this->lang->words[ $order['o_api_service'] ];
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$this->lang->words['shiporder_prefix']}{$methodName}</h2>
</div>

EOF;

if ( $error )
{
	$IPBHTML .= <<<EOF
	<div class='warning'>
		{$this->lang->words['fedex_ship_error']}<br />
		{$error}<br /><br />
		{$this->lang->words['fedex_ship_error_help']}
	</div>
	<br />
EOF;
}

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=api_ship&amp;go=1' method='post'>
<input type='hidden' name='id' value='{$order['o_id']}' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['shiporder_arrange']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_arrange_how']}</strong></td>
			<td class='field_field'>
EOF;
			foreach ( array( 'SPECIAL', 'REGULAR_PICKUP', 'REQUEST_COURIER', 'DROP_BOX', 'BUSINESS_SERVICE_CENTER', 'STATION', 'MANUAL' ) as $k )
			{
				$_locations = '';
				if ( isset( $locations[ $k ] ) and !empty( $locations[ $k ] ) )
				{
					$_locations = '<br /><div style="margin-left: 20px">' . $this->lang->words['shiporder_locations'] . '<br /><ul>';
					foreach ( $locations[ $k ] as $i => $loc )
					{
						$_locations .= <<<EOF
							<div id='loc_details_{$k}_{$i}' class='popupWrapper' style='display:none;'>
								<div class='popupInner'>
									{$loc['details']}
								</div>
							</div>
							<li style='padding: 3px'>
								<a href='#' onclick="new ipb.Popup('loc_popup_box_{$k}_{$i}', { type: 'balloon', stem: true, attach: { target: this, position: 'auto' }, hideAtStart: false, w: '400px', initial: $('loc_details_{$k}_{$i}').innerHTML } );">
									{$loc['address']}
								</a>
							</li>
EOF;
					}
					$_locations .= '</ul></div>';
				}
			
				$IPBHTML .= <<<EOF
					<input type='radio' name='extra[type]' value='{$k}' id='fedex_{$k}' /> {$this->lang->words['fedex_ship_' . $k]}<br />
					{$_locations}
					<br />
EOF;
			}
			$IPBHTML .= <<<EOF
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['shiporder_arrange_date']}</strong></td>
			<td class='field_field'>
				<select name='extra[date]'>
					<option value='0'>{$this->lang->words['shiporder_date_today']}</option>
					<option value='1'>{$this->lang->words['shiporder_date_tomorrow']}</option>
EOF;
					foreach ( range( 2, 6 ) as $d )
					{
						$date = date( 'd M', time() + ( 86400 * $d ) );
						$IPBHTML .= <<<EOF
							<option value='{$d}'>{$date}</option>
EOF;
					}
					
					$IPBHTML .= <<<EOF
				</select>
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['shiporder_arrange']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// API Ship: Fedex Pickup
//===========================================================================
function apiShip_fedex_pickup( $order, $error ) {

$methodName = $this->lang->words['shipping_api_fedex']; 
if ( isset( $this->lang->words[ $order['o_api_service'] ] ) )
{
	$methodName = $this->lang->words[ $order['o_api_service'] ];
}

$form['PackageLocation'] = $this->registry->output->formDropdown( 'extra[PackageLocation]', array(
	array( 'FRONT', $this->lang->words['fedex_pickup_PackageLocation_FRONT'] ),
	array( 'SIDE', $this->lang->words['fedex_pickup_PackageLocation_SIDE'] ),
	array( 'REAR', $this->lang->words['fedex_pickup_PackageLocation_REAR'] ),
	) );

$form['BuildingPartCode'] = $this->registry->output->formDropdown( 'extra[BuildingPartCode]', array(
	array( 'APARTMENT', $this->lang->words['fedex_pickup_BuildingPartCode_APARTMENT'] ),
	array( 'BUILDING', $this->lang->words['fedex_pickup_BuildingPartCode_BUILDING'] ),
	array( 'DEPARTMENT', $this->lang->words['fedex_pickup_BuildingPartCode_DEPARTMENT'] ),
	array( 'FLOOR', $this->lang->words['fedex_pickup_BuildingPartCode_FLOOR'] ),
	array( 'SUITE', $this->lang->words['fedex_pickup_BuildingPartCode_SUITE'] ),
	) );
	
$form['BuildingPartDescription'] = $this->registry->output->formInput( 'extra[BuildingPartDescription]' );
$form['CourierRemarks'] = $this->registry->output->formTextArea( 'extra[CourierRemarks]' );


$form['from_day'] = $this->registry->output->formDropdown( 'extra[from_day]', array(
	array( time(), $this->lang->words['shiporder_date_today'] ),
	array( time() + 86400, $this->lang->words['shiporder_date_tomorrow'] ),
	) );
$hours = array();
foreach ( range( 0, 23 ) as $i )
{
	$hours[] = array( $i, date( 'ga', mktime( $i ) ) );
}
$form['from_hour'] = $this->registry->output->formDropdown( 'extra[from_hour]', $hours );
$form['from_minute'] = $this->registry->output->formDropdown( 'extra[from_minute]', array(
	array( 0, '00' ),
	array( 15, '15' ),
	array( 30, '30' ),
	array( 45, '45' )
	) );

$form['to_hour'] = $this->registry->output->formDropdown( 'extra[to_hour]', $hours );
$form['to_minute'] = $this->registry->output->formDropdown( 'extra[to_minute]', array(
	array( 0, '00' ),
	array( 15, '15' ),
	array( 30, '30' ),
	array( 45, '45' )
	) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='section_title'>
	<h2>{$this->lang->words['shiporder_prefix']}{$methodName}</h2>
</div>

EOF;

if ( $error )
{
	$IPBHTML .= <<<EOF
	<div class='warning'>
		{$this->lang->words['fedex_ship_error']}<br />
		{$error}<br />
	</div>
	<br />
EOF;
}

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}&amp;module=payments&amp;section=shiporders&amp;do=api_ship&amp;go=2' method='post'>
<input type='hidden' name='id' value='{$order['o_id']}' />
<input type='hidden' name='extra[type]' value='SPECIAL' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['shiporder_arrange']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_from']}</strong></td>
			<td class='field_field'>
				{$form['from_day']} {$form['from_hour']} {$form['from_minute']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_to']}</strong></td>
			<td class='field_field'>
				{$form['to_hour']} {$form['to_minute']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_PackageLocation']}</strong></td>
			<td class='field_field'>
				{$form['PackageLocation']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_BuildingPartCode']}</strong></td>
			<td class='field_field'>
				{$form['BuildingPartCode']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_BuildingPartDescription']}</strong></td>
			<td class='field_field'>
				{$form['BuildingPartDescription']}<br />
				<span class='desctext'>{$this->lang->words['fedex_pickup_BuildingPartDescription_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['fedex_pickup_CourierRemarks']}</strong></td>
			<td class='field_field'>
				{$form['CourierRemarks']}
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['shiporder_arrange']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}


}