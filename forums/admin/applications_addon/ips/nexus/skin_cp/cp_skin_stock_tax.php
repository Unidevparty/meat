<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Tax Classes
 * Last Updated: $Date: 2012-05-10 16:10:13 -0400 (Thu, 10 May 2012) $
 * </pre>
 *
 * @author 		$Author: bfarber $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		11th May 2010
 * @version		$Revision: 10721 $
 */
 
class cp_skin_stock_tax
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
// Manage Tax Classes
//===========================================================================
function manage( $classes ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['tax_classes']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=tax&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['tax_add']}</a></li>
		</ul>
	</div>
</div>

HTML;

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['tax_classes']}</h3>
	<table class='ipsTable' id='taxClasses_list'>
		<tr>
			<th width='3%'>&nbsp;</th>
			<th width='89%'>{$this->registry->getClass('class_localization')->words['tax_name']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

	if ( !empty( $classes ) )
	{
		foreach ( $classes as $id => $data )
		{
			$menuKey++;
			$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='classes_{$id}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td><span class='larger_text'>{$data['t_name']}</span></td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=tax&amp;do=edit&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}?' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=tax&amp;do=delete&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
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
				{$this->registry->getClass('class_localization')->words['tax_empty']}
			</td>
		</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#taxClasses_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=tax&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current=array(), $countryList, $currentRates ) {
if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['tax_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['tax_edit'];
	$id = $current['t_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['t_name'] ) );

$IPBHTML = "";

$IPBHTML .= <<<HTML
	<script type='text/javascript'>
		var _stateLists = [];
HTML;

foreach( customer::$states as $c => $s )
{
	$d = '';
	foreach ( $s as $k => $v )
	{
		$k = ucwords( strtolower( $k ) );
		$d .= "<option value='{$v}'>{$k}</option>";
	}
	$IPBHTML .= <<<HTML
		_stateLists["{$c}"] = "{$d}";
HTML;
}

$IPBHTML .= <<<HTML
	</script>
HTML;

//--starthtml--//
$IPBHTML .= <<<HTML

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=tax&amp;do=save' method='post' id='tax-class-form'>

<div class='section_title'>
	<h2>{$title}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' onclick="$('tax-class-form').submit();"><img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' /> {$this->registry->getClass('class_localization')->words['save']}</a></li>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=tax&amp;do=help' target='_blank'><img src='{$this->settings['skin_acp_url']}/images/icons/help.png' alt='' /> {$this->registry->getClass('class_localization')->words['help']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<input type='hidden' name='id' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['tax_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
	</table>		
</div>

<br /><br />

<div class='acp-box'>
	<h3>Rates</h3>
	<table class="ipsTable double_pad" id='rates'>
		<tr>
			<th width='20%'>{$this->registry->getClass('class_localization')->words['tax_rate']}</th>
			<th width='75%'>{$this->registry->getClass('class_localization')->words['tax_applies']}</th>
			<th width='5%' class='col_buttons'>&nbsp;</th>
	</table>
	<div class='acp-actionbar'>
HTML;
	$IPBHTML .= <<<HTML
		<input type='button' value='{$this->registry->getClass('class_localization')->words['tax_rate_add']}' class='button primary' onclick='defaultAddRate()' />
HTML;
	$IPBHTML .= <<<HTML
	</div>
</div>

</form>

<script type='text/javascript'>
	var rates = 0;
	var row_class = "acp-row-off";

	function addRate( _rate, _countryList, _allLocations, _countryStyle, _stateStyle )
	{
		rates++;
		var row = $('rates').insertRow( $('rates').rows.length );
		row.id = 'rate-' + rates;
		row.className = 'ipsControlRow';
				
		var cell_rate = row.insertCell(0);
		cell_rate.innerHTML = "<input name='rate_rate[" + rates + "]' size='5' value='" + _rate + "' /> %";
		
		var cell_locations = row.insertCell(1);		
		cell_locations.innerHTML = "<label for='rate_all[" + rates + "]'><input type='checkbox' name='rate_all-" + rates + "' id='rate_all[" + rates + "]' " + _allLocations + " onchange='countryList(" + rates + ");' /> {$this->registry->getClass('class_localization')->words['all_locations']}</label> &nbsp;<select id='rate_countries-" + rates + "' name='rate_countries-" + rates + "[]' multiple='multiple' style='"+_countryStyle+"' onchange='stateList(" + rates + ", new Array() );'>" + _countryList + "</select> &nbsp; <span id='state-lists-" + rates + "'></span>";
		
		var cell_delete = row.insertCell(2);
		cell_delete.innerHTML = "<ul class='ipsControlStrip'><li class='i_delete'><a onclick='removeRate(" + rates + ")' style='cursor:pointer'>{$this->registry->getClass('class_localization')->words['delete']}</a></li></ul>";
	}
	
	function defaultAddRate()
	{
		addRate( '', "{$countryList}", "checked='checked'", "display:none", "display:none" );
	}
	
	function removeRate( id )
	{
		if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }
		$( 'rate-' + id ).innerHTML = "";
	}
	
	function countryList( id )
	{	
		if( $( 'rate_all[' + id + ']' ).checked )
		{
			$( 'rate_countries-' + id ).style.display = 'none';
		}
		else
		{
			$( 'rate_countries-' + id ).style.display = 'inline';
		}
	}
	
	function stateList( id, selected )
	{
		var sel = $( 'rate_countries-' + id );
		for ( var foo = 0; foo <= sel.length; foo++ )
		{
			if ( sel[foo] != undefined && _stateLists[ sel[foo].value ] != undefined )
			{
				var _options = _stateLists[ sel[foo].value ];
				
				if ( selected[ sel[foo].value ] != undefined )
				{
					var split = selected[ sel[foo].value ];
					for ( var bar = 0; bar <= split.length; bar++ )
					{
						_options = _options.replace( "value='"+ split[bar] +"'", "value='"+ split[bar] +"' selected='selected'" );
					}
				}
							
				if ( sel[ foo ].selected )
				{
					if ( $( 'state-lists-' + id + '-' + sel[foo].value ) != undefined )
					{
						$( 'state-lists-' + id + '-' + sel[foo].value ).innerHTML = "<select id='rate_states-" + rates + "-" + sel[foo].value + "[]' name='rate_states-" + rates + "-" + sel[foo].value + "[]' multiple='multiple'>" + _options + "</select>";
					}
					else
					{
						$( 'state-lists-' + rates ).innerHTML += "<span id='state-lists-" + id + "-"+ sel[foo].value +"'><select id='rate_states-" + rates + "-" + sel[foo].value + "[]' name='rate_states-" + rates + "-" + sel[foo].value + "[]' multiple='multiple'>" + _options + "</select></span>";
					}
				}
				else if ( $( 'state-lists-' + id + '-' + sel[foo].value ) != undefined )
				{
					$( 'state-lists-' + id + '-' + sel[foo].value ).innerHTML = "";
				}
			}
		}
	}
HTML;
	
	if ( empty( $current ) )
	{
		$IPBHTML .= <<<HTML
		defaultAddRate();
HTML;
	}
	else
	{
		foreach ( $currentRates as $k => $r )
		{			
		$IPBHTML .= <<<HTML
		
		addRate( '{$r['rate']}', "{$r['countries']}", "{$r['all']}", "{$r['countryStyle']}", "{$r['stateStyle']}" );
HTML;
			if ( is_array( $r['applies'] ) )
			{
				$countryArray = array();
				foreach ( $r['applies'] as $country => $states )
				{
					if ( !empty( $states ) )
					{
						$_s = array();
						foreach ( $states as $s )
						{
							$_s[] = "'{$s}'";
						}
						$countryArray[ $country ] = implode( ', ', $_s );
					}
				}
				
				$IPBHTML .= <<<HTML
				
				var _SLA = [];
HTML;
				
				foreach ( $countryArray as $_c_ => $_s_ )
				{
					$IPBHTML .= <<<HTML
					
					_SLA["{$_c_}"] = [ {$_s_} ];
HTML;
				}
								
				$IPBHTML .= <<<HTML
				
				stateList( rates, _SLA );
HTML;

				continue;
			}
			
			$IPBHTML .= <<<HTML
			
			stateList( rates, new Array() );
HTML;

		}
	}
	
$IPBHTML .= <<<HTML
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Help
//===========================================================================
function help() {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['tax_help']}</h2>
</div>

{$this->registry->getClass('class_localization')->words['tax_help_1']}

<div style='margin-top: 35px'>
	<h3><strong>{$this->registry->getClass('class_localization')->words['tax_help_2']}</strong></h3><br />
	
	{$this->registry->getClass('class_localization')->words['tax_help_3']}<br /><br />
	<img src='{$this->settings['skin_app_url']}/images/tax_help/single.jpg' /><br />
	<em>{$this->registry->getClass('class_localization')->words['tax_help_4']}</em>
</div>

<div style='margin-top: 35px'>
	<h3><strong>{$this->registry->getClass('class_localization')->words['tax_help_5']}</strong></h3><br />
	
	{$this->registry->getClass('class_localization')->words['tax_help_6']}.<br /><br />
	<img src='{$this->settings['skin_app_url']}/images/tax_help/multiple.jpg' /><br />
	<em>{$this->registry->getClass('class_localization')->words['tax_help_7']}</em>
</div>

<div style='margin-top: 35px'>
	<h3><strong>{$this->registry->getClass('class_localization')->words['tax_help_8']}</strong></h3><br />
	
	{$this->registry->getClass('class_localization')->words['tax_help_9']}<br /><br />
	<img src='{$this->settings['skin_app_url']}/images/tax_help/fallback.jpg' /><br />
	<em>{$this->registry->getClass('class_localization')->words['tax_help_10']}</em>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

}