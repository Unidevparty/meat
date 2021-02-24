<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Gateways
 * Last Updated: $Date: 2013-10-17 18:31:35 -0400 (Thu, 17 Oct 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		13th January 2010
 * @version		$Revision: 12385 $
 */
 
class cp_skin_stock_gateways
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
// Manage Gateways
//===========================================================================
function manage( $methods, $gateways ) {

$menuKey = 0;

$httpsWarning = array();
$infoWarning = array();
$currencyWarnings = array();

$fields = array();
$_fields = customer::fields();
foreach ( $_fields as $f )
{
	$fields[ $f['f_column'] ] = $f;
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['gateways']}</h2>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['gateways']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='30%'>{$this->registry->getClass('class_localization')->words['gateway_gateway']}</th>
				<th width='18%' style='text-align: center'>{$this->registry->getClass('class_localization')->words['gateways_max_amount']}</th>
				<th width='12%' style='text-align: center'>{$this->registry->getClass('class_localization')->words['gateway_active']}</th>
				<th width='12%' style='text-align: center'>{$this->registry->getClass('class_localization')->words['gateway_complete']}</th>
				<th width='12%' style='text-align: center'>{$this->registry->getClass('class_localization')->words['gateway_failed']}</th>
				<th width='8%' class='col_buttons'>&nbsp;</th>
			</tr>
		</table>
		<div id='gateways_list'>
HTML;

	foreach ( $methods as $gatewayID => $_methods )
	{
		$menuKey++;
	
		$testMode = $gateways[$gatewayID]['g_testmode'] ? " &nbsp; &nbsp; &nbsp; <span class='badge_red' style='color: white'>&nbsp;{$this->registry->getClass('class_localization')->words['gateway_testmode']}&nbsp;</span>" : '';
		
		$promo = ( $gateways[ $gatewayID ]['g_key'] == '2checkout' ) ? "<table class='ipsTable'><tr><td width='4%'>&nbsp;</td><td><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=add_method&amp;id={$gatewayID}'<span class='badge_purple' style='color:white'>&nbsp;{$this->registry->getClass('class_localization')->words['nexus_2co_promo']}&nbsp;</span></a></td></tr></table>" : '';
				
		$IPBHTML .= <<<HTML
			<div class='isDraggable' id='gateways_{$gatewayID}'>
				<table class='ipsTable'>
					<tr class='ipsControlRow isDraggable'>
						<th width='4%' class='subhead col_drag'><span class='draghandle'>&nbsp;</span></th>
						<th width='88%' class='subhead'>
							<span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=edit_gateway&amp;id={$gatewayID}'><strong>{$gateways[$gatewayID]['g_name']}</strong></a></span>{$testMode}
						</th>
						<th width='8%' class='col_buttons subhead'>
							<ul class='ipsControlStrip'>
								<li class='i_add'>
									<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=add_method&amp;id={$gatewayID}'>{$this->registry->getClass('class_localization')->words['paymethod_add']}...</a>
								</li>
								<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
									<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
								</li>
							</ul>
							<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
								<li class='icon edit'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=edit_gateway&amp;id={$gatewayID}'>{$this->registry->getClass('class_localization')->words['gateway_edit']}...</a></li>
								<li class='icon view'><a href='#'>{$this->registry->getClass('class_localization')->words['gateway_find']}...</a></li>
							</ul>
						</th>
					</tr>
				</table>
				<table class='ipsTable' id='methods_list_{$gatewayID}'>
HTML;

		foreach ( $_methods as $methodID => $method )
		{
			if ( $method['m_active'] and $this->settings['nexus_https'] != 'https' and $gateways[ $gatewayID ]['req_https'] )
			{
				$httpsWarning[ $gatewayID ] = $gateways[ $gatewayID ]['g_name'];
			}
			if ( $method['m_active'] and !empty( $gateways[ $gatewayID ]['req_info'] ) )
			{
				foreach ( $gateways[ $gatewayID ]['req_info'] as $k )
				{
					if ( !$fields[ $k ]['f_purchase_require'] )
					{
						$infoWarning[ $gatewayID ][ $k ] = $fields[ $k ]['f_name'];
					}
				}
			}
			
			$maxAmount = 0;
			if ( array_key_exists( $this->settings['nexus_currency'], $method['max_amounts'] ) )
			{
				$maxAmount = $method['max_amounts'][ $this->settings['nexus_currency'] ];
			}
			elseif ( array_key_exists( '*', $method['max_amounts'] ) )
			{
				$maxAmount = $method['max_amounts']['*'];
			}
			else
			{
				$maxAmount = '--';
				if ( $method['m_active'] )
				{
					$currencyWarnings[ $gatewayID ] = $gateways[ $gatewayID ]['g_name'];
				}
			}
			$maxAmount = $maxAmount == '*' ? '&#8734;' : $maxAmount;
			$maxAmount = is_numeric( $maxAmount ) ? $this->registry->getClass('class_localization')->formatMoney( $maxAmount ) : $maxAmount;
		
			$menuKey++;
			$active = $method['m_active'] ? 'tick' : 'cross';
			$IPBHTML .= <<<HTML
					<tr class='ipsControlRow isDraggable' id='methods_{$methodID}'>
						<td width='4%' class='subhead col_drag'><span class='draghandle'>&nbsp;</span></td>
						<td witdh='30%'><span class='larger_text'><a style='margin-left: 25px' href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=edit_method&amp;id={$method['m_id']}'>{$method['m_name']}</a></span></td>
						<td width='18%' style='text-align: center'>{$maxAmount}</td>
						<td width='12%' style='text-align: center'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=toggle&amp;id={$method['m_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/{$active}.png' /></a></td>
						<td width='12%' style='text-align: center'>{$method['complete_trans']}</td>
						<td width='12%' style='text-align: center'>{$method['fail_trans']}</td>
						<td width='8%' class='col_buttons'>
							<ul class='ipsControlStrip'>
								<li class='i_edit'>
									<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=edit_method&amp;id={$method['m_id']}'>{$this->registry->getClass('class_localization')->words['paymethod_edit']}...</a>
								</li>
								<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
									<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
								</li>
							</ul>
							<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
								<li class='icon view'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=transactions&amp;search=1&amp;method={$method['m_id']}'>{$this->registry->getClass('class_localization')->words['gateway_find']}...</a></li>
								<li class='icon info'><a href='{$this->settings['base_url']}&amp;module=reports&amp;section=income&amp;series[][]={$method['m_id']}'>{$this->registry->getClass('class_localization')->words['income_report_link']}...</a></li>
								<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=delete_method&amp;id={$method['m_id']}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>
							</ul>
						</td>
					</tr>
HTML;
		}

			$IPBHTML .= <<<HTML
				</table>
				<script type='text/javascript'>
					jQ("#methods_list_{$gatewayID}").ipsSortable( 'table', {
						url: "{$this->settings['base_url']}&app=nexus&module=stock&section=gateways&do=reorder_methods&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
					});
				</script>
				{$promo}
			</div>
HTML;

	}
	
	$IPBHTML .= <<<HTML
		</div>
	</div>
</div>

<script type='text/javascript'>
	jQ("#gateways_list").ipsSortable( 'multidimensional', {
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=gateways&do=reorder_gateways&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

<br /><br />

HTML;

if ( !empty( $httpsWarning ) or !empty( $infoWarning ) or !empty( $currencyWarnings ) )
{
	$IPBHTML .= <<<HTML
	<div class='warning'>
HTML;
	if ( !empty( $httpsWarning ) )
	{
		$warning_text_https = sprintf( $this->registry->getClass('class_localization')->words['gateways_https_warning'],"{$this->settings['base_url']}&amp;module=settings" );
		$IPBHTML .= <<<HTML
		{$warning_text_https}
		<ul style='list-style-type: square; padding:3px'>
HTML;
		foreach ( $httpsWarning as $gw )
		{
			$IPBHTML .= "<li>{$gw}</li>";
		}
		$IPBHTML .= <<<HTML
		</ul>
HTML;
	}
	$IPBHTML .= "<br />";
	if ( !empty( $infoWarning ) )
	{
		$warning_text_info = sprintf( $this->registry->getClass('class_localization')->words['gateways_info_warning'],"{$this->settings['base_url']}&amp;module=customers&amp;section=fields" );
		$IPBHTML .= <<<HTML
		{$warning_text_info}
		<ul style='list-style-type: square; padding:3px'>
HTML;
		foreach ( $infoWarning as $gid => $fields )
		{
			$message = sprintf( $this->registry->getClass('class_localization')->words['_gateways_info_warning'], $gateways[ $gid ]['g_name'], implode( ', ', $fields ) );
			$IPBHTML .= "<li>{$message}</li>";
		}
		$IPBHTML .= <<<HTML
		</ul>
HTML;
	}
	$IPBHTML .= "<br />";
	if ( !empty( $currencyWarnings ) )
	{
		$warning_text_curr = sprintf( $this->registry->getClass('class_localization')->words['gateways_curr_warning'],"{$this->settings['base_url']}&amp;module=settings" );
		$IPBHTML .= <<<HTML
		{$warning_text_curr}
		<ul style='list-style-type: square; padding:3px'>
HTML;
		foreach ( $currencyWarnings as $gw )
		{
			$IPBHTML .= "<li>{$gw}</li>";
		}
		$IPBHTML .= <<<HTML
		</ul>
HTML;
	}
	$IPBHTML .= "<br />";
	$IPBHTML .= <<<HTML
	</div>
HTML;
}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Gateway Form
//===========================================================================
function gatewayForm( $current ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name',	$current['g_name'] );
$form['testmode'] = ipsRegistry::getClass('output')->formYesNo( 'testmode',	$current['g_testmode'] );
$form['payout'] = ipsRegistry::getClass('output')->formYesNo( 'payout',	$current['g_payout'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['gateway_edit']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['gateway_edit']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=save_gateway' method='post'>
	<input type='hidden' name='id' value='{$current['g_id']}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['gateway_name']}</td></label>
			<td class='field_field'>{$form['name']}</td>
		</tr>
HTML;

if ( ipsRegistry::$settings['nexus_payout'] )
{

	$IPBHTML .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['gateway_payouts']}</td></label>
				<td class='field_field'>{$form['payout']}</td>
			</tr>
HTML;
	}
	
$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['gateway_test']}</td></label>
			<td class='field_field'>{$form['testmode']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Method Form
//===========================================================================
function methodForm( $current, $gateway ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['paymethod_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['paymethod_edit'];
	$id = $current['m_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name',	( empty( $current ) ? '' : $current['m_name'] ) );
$form['active'] = ipsRegistry::getClass('output')->formYesNo( 'active',	( empty( $current ) ? 1 : $current['m_active'] ) );

$countries = array();
foreach ( customer::getCountryList() as $k => $v )
{
	if ( $k )
	{
		$countries[] = array( $k, $v );
	}
}
$form['countries'] = ipsRegistry::getClass('output')->formMultiDropdown( 'countries[]', $countries, ( empty( $current ) ? array() : explode( ',', $current['m_countries'] ) ) );

$settings = unserialize( $gateway['g_settings'] );
$currentSettings = ( empty($current) ) ? array() : unserialize($current['m_settings']);

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

HTML;

if ( $gateway['g_key'] == '2checkout' )
{
	$IPBHTML .= <<<HTML
	<div class='acp-box alt'>
		<h3>{$this->registry->getClass('class_localization')->words['nexus_2co_promo']}</h3>
		<table class='ipsTable'>
			<tr>
				<td class='no_messages'>
					{$this->registry->getClass('class_localization')->words['nexus_2co_promo_desc']}
				</td>
			</tr>
		</table>
	</div>
	<br />
HTML;

}

if ( isset( $this->registry->getClass('class_localization')->words[ 'method_form_instructions_' . $gateway['g_key'] ] ) )
{
	$message = str_replace( '{board_url}', $this->settings['public_url'], $this->registry->getClass('class_localization')->words[ 'method_form_instructions_' . $gateway['g_key'] ] );
	$IPBHTML .= <<<HTML
	<div class='information-box'>
		{$message}
	</div>
	<br />
HTML;
}

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=gateways&amp;do=save_method' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<input type='hidden' name='gateway' value='{$gateway['g_id']}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['paymethod_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['paymethod_active']}</strong></td>
			<td class='field_field'>{$form['active']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['paymethod_countries']}</strong></td>
			<td class='field_field'>
				{$form['countries']}<br />
				<span class='desctext'>{$this->lang->words['paymethod_countries_desc']}</span>
			</td>
		</tr>
HTML;

	foreach( $settings as $key => $data )
	{
		$title = $this->registry->getClass('class_localization')->words['gateway_title_'.$gateway['g_key'].'_'.$key];
		$description = $this->registry->getClass('class_localization')->words['gateway_desc_'.$gateway['g_key'].'_'.$key];
		
		if ( $data['type'] == 'formTextarea' )
		{
			$currentSettings[$key] = IPSText::getTextClass('bbcode')->preEditParse( $currentSettings[$key] );
		}
		
		if ( $data['type'] == 'formDropdown' )
		{
			$formField = ipsRegistry::getClass('output')->$data['type']( "settings[{$key}]", $data['options'], ( empty( $currentSettings ) ? $data['default'] : $currentSettings[$key] ) );
		}
		else
		{
			$formField = ipsRegistry::getClass('output')->$data['type']( "settings[{$key}]", ( empty( $currentSettings ) ? $data['default'] : $currentSettings[$key] ) );
		}
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$title}</strong></td>
			<td class='field_field'>
				{$formField}<br />
				<span class='desctext'>{$description}</span>
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
<br />
HTML;

//--endhtml--//
return $IPBHTML;
}



}