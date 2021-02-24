<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Payments - Packages
 * Last Updated: $Date: 2013-10-17 18:31:35 -0400 (Thu, 17 Oct 2013) $
 * </pre>
 *
 * @author 		$Author: AndyMillne $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		8th January 2010
 * @version		$Revision: 12385 $
 */
 
class cp_skin_stock_packages
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
// Manage Packages
//===========================================================================
function managePackages( $groups, $isSelector=0, $selected=array() ) {

$menuKey = 0;
$cat = intval( $this->request['cat'] );
$isSelector = intval( $isSelector );
$this->request['isSelector'] = $isSelector;
$selected = json_encode( $selected );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<script type='text/javascript'>
	var root = {$cat};
	var isSelector = {$isSelector};
	var defaultSelected = {$selected};
</script>
<script type='text/javascript' src='{$this->settings['js_app_url']}acp.packagemanager.js'></script>
HTML;

if ( !$isSelector )
{

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$this->lang->words['packages']}</h2>
</div>

HTML;

}

$IPBHTML .= <<<HTML
<div id='package_search_form' class='acp-box ipsTreeTable'>
HTML;
	if ( !$isSelector )
	{
		$IPBHTML .= <<<HTML
 	<h3>
		<div id='package_search' class='ipsInlineTreeSearch'>
			<input type='text' value='{$this->lang->words['packages_filter']}' id='package_search_box' spellcheck='false' size='16' /> <a href='#' class='cancel' id='cancel_filter'><img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' alt='{$this->lang->words['cancel']}' /></a>
		</div>
		{$this->lang->words['packages']}
	</h3>
	<div class='row root ipsControlRow' style='position: relative'>
		<span id='package_subtitle'>
HTML;
		if ( !$isSelector )
		{
			$IPBHTML .= <<<HTML
			<ul class='ipsControlStrip'>
				<li class='i_add'>
					<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=add_group&amp;group={$this->request['cat']}' title='{$this->lang->words['pgroup_add']}'>{$this->lang->words['pgroup_add']}</a>
				</li>
			</ul>
HTML;
		}
		$IPBHTML .= <<<HTML
			<img src='{$this->settings['skin_app_url']}/images/nexus_icons/package.png' /> &nbsp;
HTML;
	if ( $this->request['cat'] )
	{
		$IPBHTML .= "<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages'>{$this->lang->words['packages']}</a>";
	}
	else
	{
		$IPBHTML .= "{$this->lang->words['packages']}";
	}
$IPBHTML .= <<<HTML
		</span>
	</div>
HTML;
	}
	$IPBHTML .= <<<HTML
	<div>
HTML;

if ( empty( $groups ) )
{
	$add = $this->request['cat'] ? 'add_package' : 'add_group';
	$IPBHTML .= <<<HTML
			<table>
				<tr>
					<td colspan='8'>
						<p class='no_messages'><em>{$this->lang->words['packages_empty']}</em></p>
					</td>
				</tr>
			</table>
HTML;
}
else
{
	$IPBHTML .= <<<HTML
	<ul id='filter_results'></ul>
HTML;
	if ( !$isSelector )
	{
		$IPBHTML .= <<<HTML
	<div class='sortable_handle' id='sort_groups'>
HTML;
	}

	foreach ( $groups as $groupID => $data )
	{
		$IPBHTML .= $this->packageGroupRow( $groupID, $data['pg_name'], $data['group_count'] + $data['package_count'] );
	}
	
	$IPBHTML .= <<<HTML
	</div>
HTML;
	
	if ( !$isSelector )
	{
		$IPBHTML .= <<<HTML
	<script type='text/javascript'>
		jQ("#sort_groups").ipsSortable( 'multidimensional', {
			url: "{$this->settings['base_url']}&app=nexus&module=stock&section=packages&do=reorder_groups&md5check={$this->registry->adminFunctions->getSecurityKey()}&in={$cat}".replace( /&amp;/g, '&' ),
			serializeOptions: { key: 'package_groups[]' }
		});
	</script>
HTML;
	}
		
}

$IPBHTML .= <<<HTML
	</div>
</div>

<script type='text/javascript'>
	ipb.templates['nexus_group_wrap'] = new Template("<div id='g_wrap_#{id}' class='group_wrap parent_wrap' style='display: none'><ul id='gg_#{id}'>#{groups}</ul><ul id='gp_#{id}'>#{packages}</ul></div>");
</script>
HTML;

//--endhtml--//
return $IPBHTML;
}

//-------------------------------------------------------
// Row for package groups
//-------------------------------------------------------
function packageGroupRow( $groupID, $name, $hasChildren=TRUE ) {
	
$menuKey = md5( 'g' . $groupID );

if ( $this->request['isSelector'] )
{
	$isSelector = "style='display:none'";
}

switch ( $this->request['isSelector'] )
{
	case 1:
		$link = $name;
		break;
	
	case 2:
		$link = "<a id='toggle_group_{$groupID}' class='npm_select clickable'>{$name}</a>";
		break;
		
	default:
		$link =  "<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=manage&amp;cat={$groupID}'>{$name}</a>";
		break;
}

$nochildren = ( $hasChildren ) ? '' : 'nochildren';

$IPBHTML .= <<<HTML
<div class='group parent {$nochildren} row ipsControlRow isDraggable' id='group_{$groupID}'>
	<div class='draghandle' title='{$this->lang->words['package_reorder']}' {$isSelector}></div>
HTML;
	if ( $groupID != -1 )
	{
		$IPBHTML .= <<<HTML
		<img src='{$this->settings['skin_app_url']}/images/packages/group.png' class='icon' />
HTML;
	}
	$IPBHTML .= <<<HTML
	{$link}
	<ul class='ipsControlStrip' {$isSelector}>
		<li class='i_add'>
			<a class='add' href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=add_package&amp;group={$groupID}' title='{$this->lang->words['package_add']}'>{$this->lang->words['package_add']}...</a>
		</li>
		<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
			<a href='#'>...</a>
		</li>
	</ul>
	<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
		<li class='icon edit'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=edit_group&amp;id={$groupID}' title='{$this->lang->words['edit']}'>{$this->lang->words['edit']}</a></li>
		<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=delete_group&amp;id={$groupID}' title='{$this->lang->words['pgroup_delete']}'>{$this->lang->words['delete']}...</a></li>
	</ul>
	<!--<img class='tick_img' src='{$this->settings['skin_acp_url']}/images/done_tick.png' />-->
	</div>
HTML;
//--endhtml--//
return $IPBHTML;
}

function packagePackageRow( $packageID, $package )
{
	
	$menuKey = md5( 'p' . $packageID );
	
	$package['renewal'] = $package['renewal'] ? $package['renewal'] : $this->lang->words['renew_term_none'];
	
	$cost = "<strong>" . $this->registry->getClass('class_localization')->formatMoney( $package['p_base_price'] ) . "</strong>";
	
	$package['stock'] = ( $package['stock'] < 0 ) ? '&#8734;' : $package['stock'];
		
	$package['img'] = "<img src='{$package['thumbnail']}' " . IPSLib::getTemplateDimensions( $package['thumbnail'], 30, 30 ) . "class='icon' />";
		
	$sold = ( $package['renewal'] != $this->lang->words['renew_term_none'] ) ? "{$package['active']} {$this->lang->words['package_stats_active']} &nbsp;&nbsp;&middot;&nbsp;&nbsp; {$package['expired']} {$this->lang->words['package_stats_expired']}" : "{$package['active']} {$this->lang->words['package_stats_sold']}";
	
	if ( $this->request['isSelector'] )
	{
		$isSelector = "style='display:none'";
	}
	
	$link = ( $this->request['isSelector'] ) ? "<a id='toggle_package_{$packageID}' class='npmp_select clickable'>{$package['p_name']}</a>" : "<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=edit_package&amp;id={$packageID}'>{$package['p_name']}</a>";
	
	$IPBHTML .= <<<HTML
	<li id='package_{$packageID}' class='child row ipsControlRow isDraggable'>
		<div>
			<ul class='ipsControlStrip' {$isSelector}>
				<li class='i_edit'>
					<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=edit_package&amp;id={$packageID}' title='{$this->lang->words['edit']}'>{$this->lang->words['edit']}</a>
				</li>
				<li class='i_stock'>
					<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=product_options&amp;id={$packageID}' title='{$this->lang->words['product_options']}...'>{$this->lang->words['product_options']}...</a>
				</li>
				<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
					<a href='#'>...</a>
				</li>
			</ul>
			<ul class='acp-menu' id='menu{$menuKey}_menucontent' style='display: none'>
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=add_package&amp;duplicate=1&amp;id={$packageID}'>{$this->lang->words['package_duplicate']}...</a></li>
				<li class='icon view'><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=purchases&amp;do=view&amp;id={$packageID}'>{$this->lang->words['package_find_purchases']}...</a></li>
				<li class='icon info'><a href='{$this->settings['base_url']}&amp;module=reports&amp;section=purchases&amp;series[][]={$packageID}'>{$this->lang->words['purchases_report_link']}...</a></li>
				<li class='icon manage'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=remove_purchases&amp;id={$packageID}'>{$this->lang->words['package_remove']}...</a></li>
				<li class='icon delete'><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=delete_package&amp;id={$packageID}'>{$this->lang->words['delete']}...</a></li>
			</ul>
			<!--<img class='tick_img' src='{$this->settings['skin_acp_url']}/images/done_tick.png' />-->
			<span class='pricing infotext' {$isSelector}>
				<span class='price'>
					{$cost}
				</span>
				<br /><span class='desctext'>{$package['renewal']}</span>
			</span>
			<div class='draghandle' {$isSelector}></div>
			{$package['img']}
			<div class='package_info'>
				{$link}
				<br />
				<span class='stock desctext'>
					{$package['stock']} {$this->lang->words['package_stats_stock']} &nbsp;&nbsp;&middot;&nbsp;&nbsp; {$sold}
				</span>
			</div>
		</div>
	</li>
HTML;
//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Product options
//===========================================================================
function productOptions( $package, $options ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$package['p_name']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&do=manage&cat={$package['p_group']}'><img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' alt='' /> {$this->lang->words['save']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['product_options']}</h3>
	<table id='rules' class="ipsTable">
		<tr>
			<th width='10'>&nbsp;</th>
			<th>{$this->lang->words['prodoptions_option']}</th>
			<th>{$this->lang->words['prodoptions_stock']}</th>
			<th>{$this->lang->words['prodoptions_base']}</th>
			<th>{$this->lang->words['prodoptions_renew']}</th>
			<th class='col_buttons'></th>
		</tr>

HTML;

foreach( $options as $option )
{
	if ( $option['opt_stock'] == -1 )
	{
		$option['opt_stock'] = '&#8734;';
	}

	$IPBHTML .= <<<HTML
	<tr id='rule_{$option['opt_id']}' class='ipsControlRow'>
		<td></td>
		<td><span class='larger_text'>{$option['opt_values']}</span></td>
		<td>{$option['opt_stock']}</td>
		<td>{$option['opt_base_price']}</td>
		<td>{$option['opt_renew_price']}</td>
		<td>
			<ul class='ipsControlStrip'>
				<li class='i_edit'>
					<a onclick='editRule({$option['opt_id']})' style='cursor:pointer'>{$this->lang->words['edit']}</a>
				</li>
				<li class='i_delete'>
					<a onclick='removeRule({$option['opt_id']})' style='cursor:pointer'>{$this->lang->words['delete']}</a>
				</li>
			</ul>
		</td>
	</tr>
HTML;
}

	$IPBHTML .= <<<HTML
		</table>
		<div class="acp-actionbar">
			<input type='button' id='addRule' value='{$this->lang->words['prodoptions_add']}' class='realbutton'>
		</div>
	</div>

<script type='text/javascript'>

var options = Array();
var row_class = "acp-row-off";
var _popup = 0;

function doPopUp( e )
{
	Event.stop(e);
	_popup = _popup + 1;
	new ipb.Popup('addrule'+_popup, { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=add_option_rule&package={$package['p_id']}&popup="+ _popup +"&secure_key=" + ipb.vars['md5_hash'], modal: false } );
}

$('addRule').observe('click', doPopUp.bindAsEventListener( this ) );

function editRule( id )
{
	_popup = _popup + 1;
	new ipb.Popup('addrule'+_popup, { type: 'pane', stem: true, hideAtStart: false, w: '600px', h: '600px', ajaxURL: ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=edit_option_rule&id="+ id + "&popup="+ _popup +"&secure_key=" + ipb.vars['md5_hash'], modal: false } );
}

function saveRule( e )
{
	var saveOptions = Array();
	for ( var foo = 0; foo <= options.length; foo++ )
	{
		if ( options[foo] != undefined )
		{
			saveOptions[ options[foo] ] = options[foo] + ':' + $('options-'+ _popup +'['+ options[foo] +']').value;
		}
	}
	
	new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=save_option&secure_key=" + ipb.vars['md5_hash'],
	{
		evalJSON: 'force',
		parameters:
    	{
    		package: {$package['p_id']},
    		options: saveOptions.join(','),
    		stock: $('stock-'+ _popup).value,
    		base_price: $('base_price-'+ _popup).value,
    		renew_price: $('renew_price-'+ _popup).value
    	},
		onSuccess: function( t )
		{
			if ( t.responseJSON['error'] )
			{
				alert( t.responseJSON['error'] );
			}
			else
			{
				var row = $('rules').insertRow( $('rules').rows.length );
				row.id = 'rule_' + t.responseJSON['opt_id'];
				row.className = 'ipsControlRow';
				row.style.display = 'none';
										
				var cell_blank = row.insertCell(0);
				
				var cell_options = row.insertCell(1);
				cell_options.innerHTML = t.responseJSON['opt_values'];
				
				var cell_stock = row.insertCell(2);
				cell_stock.innerHTML = t.responseJSON['opt_stock'];
				
				var cell_base_price = row.insertCell(3);
				cell_base_price.innerHTML = t.responseJSON['opt_base_price'];
				
				var cell_renew_price = row.insertCell(4);
				cell_renew_price.innerHTML = t.responseJSON['opt_renew_price'];
				
				var cell_delete = row.insertCell(5);
				cell_delete.innerHTML = "<ul class='ipsControlStrip'><li class='i_edit'><a onclick='editRule("+t.responseJSON['opt_id']+")' style='cursor:pointer'>{$this->lang->words['edit']}</a></li><li class='i_delete'><a onclick='removeRule("+t.responseJSON['opt_id']+")' style='cursor:pointer'>{$this->lang->words['delete']}</a></li></ul>";
				
				new Effect.Appear( row, {duration:0.5} );
				new Effect.Fade( $('addrule'+_popup+'_popup'), {duration:0.5} );
			}
			
		}
	});
}

function doEditRule( id )
{
	new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=do_edit_option&secure_key=" + ipb.vars['md5_hash'],
	{
		evalJSON: 'force',
		parameters:
    	{
    		id: id,
    		stock: $('stock-'+ _popup).value,
    		base_price: $('base_price-'+ _popup).value,
    		renew_price: $('renew_price-'+ _popup).value
    	},
		onSuccess: function( t )
		{
			if ( t.responseJSON['error'] )
			{
				alert( t.responseJSON['error'] );
			}
			else
			{
				var row = $('rule_'+id);
				row['cells'][2].innerHTML = t.responseJSON['opt_stock'];
				row['cells'][3].innerHTML = t.responseJSON['opt_base_price'];
				row['cells'][4].innerHTML = t.responseJSON['opt_renew_price'];
				
				if ( row.className == 'acp-row-off' )
				{
					var endcolor = '#F1F4F7';
				}
				else
				{
					var endcolor = '#FAFBFC';
				}
				var _className = row.className;
				row.className = '';
				new Effect.Highlight( row, { duration: 1.5, endcolor: endcolor, afterFinish: function() { row.className = _className; } } );
			
				new Effect.Fade( $('addrule'+_popup+'_popup'), {duration:0.5} );
			}
			
		}
	});
}

function removeRule( id )
{
	if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
	
	new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=remove_option&secure_key=" + ipb.vars['md5_hash'], { parameters: { id: id } } );
	new Effect.Fade( $('rule_'+id), {duration:0.5} );
}

</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Product options: Add Rule
//===========================================================================
function addOptionRule( $package, $customfields, $popup ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$this->lang->words['prodoptions_add']}</h3>
	<table class="ipsTable double_pad" style='width:100%'>
HTML;
	foreach ( $customfields as $f )
	{
		if ( $f['cf_type'] == 'dropdown' )
		{
			$IPBHTML .= <<<HTML
			<script type='text/javascript'>options[ options.length + 1 ] = {$f['cf_id']};</script>
			<tr>
				<td class='field_title'><strong class='title'>{$f['cf_name']}</strong></td>
				<td class='field_field'>
					<select id='options-{$popup}[{$f['cf_id']}]'>
						<option value='*'>{$this->lang->words['prodoptions_any']}</option>
HTML;
			foreach ( $f['options'] as $k => $v )
			{
				$IPBHTML .= <<<HTML
						<option value='{$v}'>{$v}</option>
HTML;
			}
			
			$IPBHTML .= <<<HTML
					</select>
				<td>
			</tr>
HTML;
		}
		
	}
		
$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_stock']}</strong></td>
			<td class='field_field'><input id='stock-{$popup}' value='{$package['p_stock']}' /></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_base']}</strong></td>
			<td class='field_field'>
				<input id='base_price-{$popup}' value='0' /><br />
				<span class='desctext'>{$this->lang->words['prodoptions_base_blurb']}.</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_renew']}</strong></td>
			<td class='field_field'>
				<input id='renew_price-{$popup}' value='0' /><br />
				<span class='desctext'>{$this->lang->words['prodoptions_renew_blurb']}</span>
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='button' id='popup-save' onclick='saveRule()' value='{$this->lang->words['save']}' class='realbutton' />
	</div>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Product options: Edit Rule
//===========================================================================
function editOptionRule( $package, $customfields, $current, $popup ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$this->lang->words['prodoptions_edit']}</h3>
	<table class="ipsTable double_pad">
HTML;
	foreach ( $customfields as $f )
	{
		if ( $f['cf_type'] == 'dropdown' )
		{
			$IPBHTML .= <<<HTML
			<script type='text/javascript'>options[ options.length + 1 ] = {$f['cf_id']};</script>
			<tr>
				<td class='field_title'><strong class='title'>{$f['cf_name']}</strong></td>
				<td class='field_field'>
HTML;
				if ( $current['opt_values'][ $f['cf_id'] ] == '*' )
				{
					$IPBHTML .= <<<HTML
						<i>{$this->lang->words['prodoptions_any']}</i>
HTML;
				}
				else
				{
					$IPBHTML .= $customfields[ $f['cf_id'] ]['options'][ $current['opt_values'][ $f['cf_id'] ] ];
				}
				
				$IPBHTML .= <<<HTML
				</td>
			</tr>
HTML;
		}
		
	}
		
$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_stock']}</strong></td>
			<td class='field_field'><input id='stock-{$popup}' value='{$current['opt_stock']}' /></td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_base']}</strong></td>
			<td class='field_field'>
				<input id='base_price-{$popup}' value='{$current['opt_base_price']}' /><br />
				<span class='desctext'>{$this->lang->words['prodoptions_base_blurb']}.</span>
			</tD>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['prodoptions_renew']}</strong></td>
			<td class='field_field'>
				<input id='renew_price-{$popup}' value='{$current['opt_renew_price']}' /><br />
				<span class='desctext'>{$this->lang->words['prodoptions_renew_blurb']}</span>
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='button' id='popup-save' onclick='doEditRule({$current['opt_id']})' value='{$this->lang->words['save']}' class='realbutton' />
	</div>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Package Group Form
//===========================================================================
function packageGroupForm( $current ) {

if ( empty( $current ) )
{
	$title = $this->lang->words['pgroup_add'];
	$id = 0;
}
else
{
	$title = $this->lang->words['pgroup_edit'];
	$id = $current['pg_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name',	( empty( $current ) ? '' : $current['pg_name'] ) );
$form['parent'] = "<select name='parent'>" . package::getPackageSelector( $this->lang->words['pgroup_top'], FALSE, ( empty( $current ) ? array( 'custom' ) : array( $current['pg_id'], 'custom' ) ), ( empty( $current ) ? $this->request['group'] : $current['pg_parent'] ) ) . '</select>';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=save_group' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['pgroup_name']}</strong></td>
			<td class='field_field'>{$form['name']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['pgroup_parent']}</strong></td>
			<td class='field_field'>{$form['parent']}</td>
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
// Add Package
//===========================================================================
function addPackage( $options ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['package_add']}</h2>
</div>

<div class='information-box'>{$this->lang->words['package_choose_type']}</div>
<br /><br />

<div class='redirector'>
	<div class='info'><img src='{$this->settings['skin_app_url']}/images/packages/group.png' /> <a href='{$this->settings['base_url']}&amp;module=stock&section=packages&do=add_group&group={$this->request['group']}'>{$this->lang->words['package_group']}</a></div>
</div>
<br /><br />
HTML;

	foreach ( $options as $id => $table )
	{
		$IPBHTML .= <<<HTML
		<div class='redirector'>
			<div class='info'><img src='{$this->settings['skin_app_url']}/images/nexus_icons/{$id}.png' /> <a href='{$this->settings['base_url']}&amp;module=stock&section=packages&do=add_package&type={$id}&group={$this->request['group']}'>{$this->lang->words[ $id ]}</a></div>
		</div>
		<br /><br />
HTML;

	}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Package Form
//===========================================================================
function packageForm( $memberGroups, $permissionSets, $current, $supportDepartments, $severities, $groupOptions, $tax, $extraData, $postKey, $attachments, $images, $methods, $imageTemp ) {

//-----------------------------------------
// Init
//-----------------------------------------

if ( empty( $current ) or $this->request['duplicate'] )
{
	$title = $this->lang->words['package_add'];
	$id = 0;
}
else
{
	$title = $this->lang->words['package_edit'] . ': ' . $current['p_name'];
	$id = $current['p_id'];
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['p_name'] ) );
$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( $current['p_desc'] );
$form['description'] = $editor->show('description');
$form['group'] = "<select name='group'>" . package::getPackageSelector( NULL, FALSE, array( 'custom' ), ( empty( $current ) ? ( isset( $this->request['group'] ) ? $this->request['group'] : '' ) : $current['p_group'] ) ) . '</select>';
$form['stock'] = ipsRegistry::getClass('output')->formSimpleInput( 'stock', ( empty( $current ) ? '-1' : $current['p_stock'] ), 8 );
$form['show_on_reg'] = ipsRegistry::getClass('output')->formYesNo( 'show_on_reg', ( empty( $current ) ? 0 : $current['p_reg'] ) );
$form['show_in_store'] = ipsRegistry::getClass('output')->formYesNo( 'show_in_store', ( empty( $current ) ? 1 : $current['p_store'] ) );
$form['featured'] = ipsRegistry::getClass('output')->formYesNo( 'featured', ( empty( $current ) ? 0 : $current['p_featured'] ) );
$form['member_groups'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'member_groups[]', ( empty( $current ) or $current['p_member_groups'] == '*' ) ? NULL : explode( ',', $current['p_member_groups'] ), TRUE );
$form['allow_upgrading'] = ipsRegistry::getClass('output')->formYesNo( 'allow_upgrading', ( empty( $current ) ? 0 : $current['p_allow_upgrading'] ) );
$form['upgrade_charge'] = ipsRegistry::getClass('output')->formYesNo( 'upgrade_charge', ( empty( $current ) ? 0 : $current['p_upgrade_charge'] ) );
$form['allow_downgrading'] = ipsRegistry::getClass('output')->formYesNo( 'allow_downgrading', ( empty( $current ) ? 0 : $current['p_allow_downgrading'] ) );
$form['downgrade_refund'] = ipsRegistry::getClass('output')->formYesNo( 'downgrade_refund', ( empty( $current ) ? 0 : $current['p_downgrade_refund'] ) );
$form['base_price'] = ipsRegistry::getClass('output')->formSimpleInput( 'base_price', ( empty( $current ) ? '' : $current['p_base_price'] ), 8 );
$form['tax'] = ipsRegistry::getClass('output')->formDropdown( 'tax', array_merge( array( array( 0, $this->lang->words['package_notax'] ) ), $tax ), ( empty( $current ) ? '' : $current['p_tax'] ) );
$form['methods'] = ipsRegistry::getClass('output')->formMultiDropdown( 'methods[]', $methods, ( empty( $current ) ? NULL : explode( ',', $current['p_methods'] ) ) );
$form['renewal_days'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_days', empty( $current ) ? '' : $current['p_renewal_days'] );
$form['renewal_days_advance'] = ipsRegistry::getClass('output')->formSimpleInput( 'renewal_days_advance', empty( $current ) ? '' : $current['p_renewal_days_advance'] );
$form['primary_promote'] = ipsRegistry::getClass('output')->formDropdown( 'primary_promote', array_merge( array( array( 0, $this->lang->words['package_nogroupchange'] ) ), $memberGroups ), ( empty( $current ) ? '' : $current['p_primary_group'] ) );
$form['secondary_promote'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'secondary_promote[]', empty( $current ) ? NULL : explode( ',', $current['p_secondary_group'] ), TRUE );
$form['perms_promote'] = ipsRegistry::getClass('output')->formMultiDropdown( 'perms_promote[]', $permissionSets, empty( $current ) ? NULL : explode( ',', $current['p_perm_set'] ) );
$form['return_primary'] = ipsRegistry::getClass('output')->formYesNo( 'return_primary', ( empty( $current ) ? 1 : $current['p_return_primary'] ) );
$form['return_secondary'] = ipsRegistry::getClass('output')->formYesNo( 'return_secondary', ( empty( $current ) ? 1 : $current['p_return_secondary'] ) );
$form['return_perm'] = ipsRegistry::getClass('output')->formYesNo( 'return_perm', ( empty( $current ) ? 1 : $current['p_return_perm'] ) );
$form['custom_module'] = ipsRegistry::getClass('output')->formSimpleInput( 'custom_module', ( empty( $current ) ? '' : $current['p_module'] ), 20 );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( $current['p_page'] );
$form['page'] = $editor->show('page');

$form['support'] = ipsRegistry::getClass('output')->formYesNo( 'support', ( empty( $current ) ? 0 : $current['p_support'] ) );
$form['support_department'] = ipsRegistry::getClass('output')->formDropdown( 'support_department', $supportDepartments, ( empty( $current ) ? 0 : $current['p_support_department'] ) );
$form['support_severity'] = ipsRegistry::getClass('output')->formDropdown( 'support_severity', $severities, ( empty( $current ) ? 0 : $current['p_support_severity'] ) );
$form['associable'] = "<select name='associable[]' multiple='multiple'>" . package::getPackageSelector( NULL, TRUE, array( 'custom' ), empty( $current ) ? NULL : explode( ',', $current['p_associable'] ) ) . '</select>';
$form['force_assoc'] = ipsRegistry::getClass('output')->formYesNo( 'force_assoc', ( empty( $current ) ? 0 : $current['p_force_assoc'] ) );
$form['group_renewals'] = ipsRegistry::getClass('output')->formYesNo( 'group_renewals', ( empty( $current ) ? 0 : $current['p_group_renewals'] ) );
$form['assoc_error'] = ipsRegistry::getClass('output')->formTextarea( 'assoc_error', ( empty( $current ) ? '' : $current['p_assoc_error'] )	);
$form['upsell'] = ipsRegistry::getClass('output')->formYesNo( 'upsell', ( empty( $current ) ? 0 : $current['p_upsell'] ) );
$form['notify'] = ipsRegistry::getClass('output')->formInput( 'notify', ( empty( $current ) ? '' : $current['p_notify'] ) );

$form['reviewable'] =  ipsRegistry::getClass('output')->formYesNo( 'reviewable', ( empty( $current ) ? 1 : $current['p_reviewable'] ) );
$form['review_moderate'] =  ipsRegistry::getClass('output')->formYesNo( 'review_moderate', ( empty( $current ) ? 0 : $current['p_review_moderate'] ) );

$packageSelector = package::getPackageSelector();

$type = ( empty( $current ) ) ? $this->request['type'] : $current['p_type'];

$dupe = ( $this->request['duplicate'] ) ? $this->request['id'] : 0;

$IPBHTML = "";
//--starthtml--//

//-----------------------------------------
// Tab 1
//-----------------------------------------

$tab1 = <<<HTML
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_1']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_name']}</strong></td>
				<td class='field_field'>{$form['name']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_desc']}</strong></td>
				<td class='field_field'>
					{$form['description']}
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_group']}</strong></td>
				<td class='field_field'>{$form['group']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_stock']}</strong></td>
				<td class='field_field'>
					{$form['stock']}<br />
					<span class='desctext'>{$this->lang->words['package_stock_desc']}<br />{$this->lang->words['options_explaination']}</span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_permissions']}</th>
			</tr>
HTML;
			if ( $type == 'product' and ( empty( $current ) or package::load( $current['p_id'] )->canShowOnReg() === TRUE ) )
			{
				$tab1 .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_reg']}</strong></td>
				<td class='field_field'>{$form['show_on_reg']}</td>
			</tr>
HTML;
			}
			else
			{
				$tab1 .= <<<HTML
				<input type='hidden' name='show_on_reg' value='0' />
HTML;
			}
			$tab1 .= <<<HTML
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_store']}</strong></td>
				<td class='field_field'>{$form['show_in_store']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_feature']}</strong></td>
				<td class='field_field'>
					{$form['featured']}<br />
					<span class='desctext'>{$this->lang->words['package_feature_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_groups']}</strong></td>
				<td class='field_field'>
					{$form['member_groups']}<br />
					<span class='desctext'>{$this->lang->words['package_groups_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_reviewable']}</strong></td>
				<td class='field_field'>{$form['reviewable']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_review_moderate']}</strong></td>
				<td class='field_field'>{$form['review_moderate']}</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_associations']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_associable']}</strong></td>
				<td class='field_field'>{$form['associable']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_assoc_req']}</strong></td>
				<td class='field_field'>{$form['force_assoc']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_group_renewals']}</strong></td>
				<td class='field_field'>
					{$form['group_renewals']}<br />
					<span class='desctext'>{$this->lang->words['package_group_renewals_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_assoc_error']}</strong></td>
				<td class='field_field'>
					{$form['assoc_error']}<br />
					<span class='desctext'>{$this->lang->words['package_assoc_error_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_upsell']}</strong></td>
				<td class='field_field'>
					{$form['upsell']}<br />
					<span class='desctext'>{$this->lang->words['package_upsell_desc']}</span>
				</td>
			</tr>
		</table>
HTML;

//-----------------------------------------
// Tab 2
//-----------------------------------------

$tab2 = "<table class='ipsTable double_pad'>";

switch ( $type )
{
	case 'product':
		$form['physical'] = ipsRegistry::getClass('output')->formYesNo( 'physical', ( empty( $current ) ? 0 : $current['p_physical'] ) );
		$form['subscription'] = ipsRegistry::getClass('output')->formYesNo( 'subscription', ( empty( $current ) ? 0 : $current['p_subscription'] ) );
		$form['shipping'] = ipsRegistry::getClass('output')->formMultiDropdown( 'shipping[]', $extraData['shipping'], ( empty( $current ) ? array() : explode( ',', $current['p_shipping'] ) ) );
		$form['weight'] = ipsRegistry::getClass('output')->formSimpleInput( 'weight', ( empty( $current ) ? '0' : $current['p_weight'] ), 8 );
		$form['lkey'] = ipsRegistry::getClass('output')->formDropdown( 'lkey', $extraData['lkeyOptions'], ( empty( $current ) ? '0' : $current['p_lkey'] ) );
		$form['lkey_identifier'] = ipsRegistry::getClass('output')->formDropdown( 'lkey_identifier', $extraData['identifierOptions'], ( empty( $current ) ? '0' : $current['p_lkey_identifier'] ) );
		$form['lkey_uses'] = ipsRegistry::getClass('output')->formSimpleInput( 'lkey_uses', ( empty( $current ) ? '1' : $current['p_lkey_uses'] ), 4 );
		$tab2 .= <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['package_shipping']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_physical']}</strong></td>
			<td class='field_field'>
				{$form['physical']}<br />
				<span class='desctext'>{$this->lang->words['package_physical_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_subscription']}</strong></td>
			<td class='field_field'>
				{$form['subscription']}<br />
				<span class='desctext'>{$this->lang->words['package_subscription_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_shipping']}</strong></td>
			<td class='field_field'>
				{$form['shipping']}<br />
				<span class='desctext'>{$this->lang->words['package_shipping_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['package_weight']}</strong></td>
			<td class='field_field'>
				{$form['weight']} {$this->settings['nexus_weight_units']}<br />
				<span class='desctext'>{$this->lang->words['package_weight_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['lkey']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['lkey']}</strong></td>
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
		$form['location'] = ipsRegistry::getClass('output')->formMultiDropdown( 'location[]', $extraData['locations'], ( empty( $current ) ) ? array() : explode( ',', $current['p_locations'] ) );
		$form['location_custom'] = ipsRegistry::getClass('output')->formInput( 'location_custom', ( empty( $current ) ) ? '' : $extraData['custom_locations'] );
		$form['exempt'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'exempt[]', ( empty( $current ) ) ? array() : explode( ',', $current['p_exempt'] ), TRUE );
		$form['expire'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire', ( empty( $current ) ) ? 0 : $current['p_expire'] );
		$form['expire_unit'] = ipsRegistry::getClass('output')->formDropDown( 'expire_unit', array(
			array( 'i', $this->lang->words['ad__impressions'] ),
			array( 'c', $this->lang->words['ad__clicks'] ),
			), ( empty( $current ) ) ? 'i' : $current['p_expire_unit'] );
		$form['max_height'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_height', ( empty( $current ) ) ? 0 : $current['p_max_height'] );
		$form['max_width'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_width', ( empty( $current ) ) ? 0 : $current['p_max_width'] );
		$tab2 .= <<<HTML
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
		$form['queue'] = ipsRegistry::getClass('output')->formDropDown( 'queue', $extraData, ( empty( $current ) ) ? 0 : $current['p_queue'] );
		$form['quota'] = ipsRegistry::getClass('output')->formInput( 'quota', ( empty( $current ) ) ? '-1' : $current['p_quota'], '', 6 );
		$form['bwlimit'] = ipsRegistry::getClass('output')->formInput( 'bwlimit', ( empty( $current ) ) ? '-1' : $current['p_bwlimit'], '', 6 );
		$form['ip'] = ipsRegistry::getClass('output')->formYesNo( 'ip', ( empty( $current ) ) ? 0 : $current['p_ip'] );
		$form['cgi'] = ipsRegistry::getClass('output')->formYesNo( 'cgi', ( empty( $current ) ) ? 0 : $current['p_cgi'] );
		$form['frontpage'] = ipsRegistry::getClass('output')->formYesNo( 'frontpage', ( empty( $current ) ) ? 0 : $current['p_frontpage'] );
		$form['hasshell'] = ipsRegistry::getClass('output')->formYesNo( 'hasshell', ( empty( $current ) ) ? 0 : $current['p_hasshell'] );
		$form['maxftp'] = ipsRegistry::getClass('output')->formInput( 'maxftp', ( empty( $current ) ) ? '-1' : $current['p_maxftp'], '', 6 );
		$form['maxsql'] = ipsRegistry::getClass('output')->formInput( 'maxsql', ( empty( $current ) ) ? '-1' : $current['p_maxsql'], '', 6 );
		$form['maxpop'] = ipsRegistry::getClass('output')->formInput( 'maxpop', ( empty( $current ) ) ? '-1' : $current['p_maxpop'], '', 6 );
		$form['maxlst'] = ipsRegistry::getClass('output')->formInput( 'maxlst', ( empty( $current ) ) ? '-1' : $current['p_maxlst'], '', 6 );
		$form['maxsub'] = ipsRegistry::getClass('output')->formInput( 'maxsub', ( empty( $current ) ) ? '-1' : $current['p_maxsub'], '', 6 );
		$form['maxpark'] = ipsRegistry::getClass('output')->formInput( 'maxpark', ( empty( $current ) ) ? '-1' : $current['p_maxpark'], '', 6 );
		$form['maxaddon'] = ipsRegistry::getClass('output')->formInput( 'maxaddon', ( empty( $current ) ) ? '-1' : $current['p_maxaddon'], '', 6 );
		$tab2 .= <<<HTML
		<tr>
			<th colspan='2'>{$this->lang->words['hosting_basic_settings']}</th>
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
			<th colspan='2'>{$this->lang->words['hosting_features']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_ip']}</strong></td>
			<td class='field_field'>
				{$form['ip']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_cgi']}</strong></td>
			<td class='field_field'>
				{$form['cgi']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_frontpage']}</strong></td>
			<td class='field_field'>
				{$form['frontpage']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['hosting_hasshell']}</strong></td>
			<td class='field_field'>
				{$form['hasshell']}
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['hosting_allowances']}</th>
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

$tab2 .= "</table>";

//-----------------------------------------
// Tab 3
//-----------------------------------------

$tab3 = <<<HTML
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_2']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_base_price']}</strong></td>
				</td>
				<td class='field_field'>
					{$form['base_price']}<br />
					<span class='desctext'>{$this->lang->words['options_explaination']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_tax']}</strong></td>
				<td class='field_field'>{$form['tax']}</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_renewals']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewals']}</strong></td>
				<td class='field_field'>
					<table class='ipsTable' id='renewal-options'>
					</table>
					<br />
					<img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> <a onclick='addRenewalOption( "", "", "", true )' style='cursor:pointer'>{$this->lang->words['package_renewals_add']}</a>
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
				<td class='field_title'><strong class='title'>{$this->lang->words['package_renewal_days_advance']}</strong></td>
				<td class='field_field'>
					{$form['renewal_days_advance']}<br />
					<span class='desctext'>{$this->lang->words['package_renewal_advance_desc']}</span>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_discounts']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_usergroup_discounts']}</strong></td>
				<td class='field_field'>
					<table id='usergroup-discounts'></table>
					<img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> <a onclick='addUsergroup( "", "" )' style='cursor:pointer'>{$this->lang->words['package_usergroup_discounts_add']}</a><br />
					<span class='desctext'>{$this->lang->words['package_usergroup_discounts_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_loyalty_discounts']}</strong></td>
				<td class='field_field'>
					<table id='loyalty-discounts'></table>
					<img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> <a onclick='addLoyalty( "", "", "" )' style='cursor:pointer'>{$this->lang->words['package_loyalty_discounts_add']}</a>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_bulk_discounts']}</strong></td>
				<td class='field_field'>
					<table id='bulk-discounts'></table>
					<img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> <a onclick='addBulk( "", "" )' style='cursor:pointer'>{$this->lang->words['package_bulk_discounts_add']}</a>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_bundle_discounts']}</strong></td>
				<td class='field_field'>
					<table id='bundle-discounts'></table>
					<img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> <a onclick='addBundle( "", "" )' style='cursor:pointer'>{$this->lang->words['package_bundle_discounts_add']}</a>
				</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_pay_options']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_methods']}</strong></td>
				<td class='field_field'>
					{$form['methods']}<br />
					<span class='desctext'>{$this->lang->words['package_methods_desc']}</span>
				</td>
			</tr>
		</table>
HTML;


//-----------------------------------------
// Tab 4
//-----------------------------------------

$tab4 = <<<HTML
		<table class='ipsTable double_pad'>
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
				<td class='field_field'>{$form['return_primary']}</td>
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
				<td class='field_field'>{$form['return_secondary']}</td>
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
				<td class='field_field'>{$form['return_perm']}</td>
			</tr>
			<tr>
				<th colspan='2'>{$this->lang->words['package_custom_actions']}</th>
			</tr>
			<tr>
				<td class='field_title'>
					<strong class='title'>{$this->lang->words['package_action']}</strong><br />
					<span class='desctext'><a href='http://www.invisionpower.com/support/guides/_/advanced-and-developers/ipnexus/ipnexus-custom-actions-r55' target='_blank'>{$this->lang->words['package_action_desc']}</a>
				</td>
				<td>
					<strong>admin/applications_addon/ips/nexus/sources/actions/{$form['custom_module']}.php</strong><br />
					<span class='desctext'>{$this->lang->words['package_action_loc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_notify']}</strong></td>
				<td class='field_field'>
					{$form['notify']}<br />
					<span class='desctext'>{$this->lang->words['package_notify_desc']}</span>
				</td>
			</tr>
		</table>
HTML;

//-----------------------------------------
// Tab 5
//-----------------------------------------

$tab5 = <<<HTML
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_5']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_upgrades']}</strong></td>
				<td class='field_field'>
					{$form['allow_upgrading']}<br />
					<span class='desctext'>{$this->lang->words['package_upgrades_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_upgcharge']}</strong></td>
				<td class='field_field'>
					{$form['upgrade_charge']}<br />
					<span class='desctext'>{$this->lang->words['package_upgcharge_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_downgrades']}</strong></td>
				<td class='field_field'>
					{$form['allow_downgrading']}<br />
					<span class='desctext'>{$this->lang->words['package_downgrades_desc']}</span>
				</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_dwncharge']}</strong></td>
				<td class='field_field'>
					{$form['downgrade_refund']}<br />
					<span class='desctext'>{$this->lang->words['package_dwncharge_desc']}</span>
				</td>
			</tr>
		</table>
HTML;

//-----------------------------------------
// Tab 6
//-----------------------------------------

$tab6 = <<<HTML
		<table class='ipsTable double_pad'>
			<tr>
				<th colspan='2'>{$this->lang->words['package_tab_6']}</th>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['package_tab_6']}</strong></td>
				<td class='field_field'>
					<span class='desctext'>{$this->lang->words['package_page_desc']}</span><br /><br />
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
HTML;

//-----------------------------------------
// Tab 7 (which is actually tab 2)
//-----------------------------------------

$tab7 = <<<HTML
		<script type='text/javascript'>
		
			var _currentPopup = 0;
			
			function deleteImage( id )
			{
				if ( confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) )
				{
					$( 'image-location-' + id ).value = '';
					new Effect.Fade( $( 'package-image-' + id ), {duration:0.5} );
				}
			}
			
			function addImage()
			{
				_currentPopup++;
			
				new ipb.Popup( 'addimagepopup' + _currentPopup, { type: 'pane', stem: true, hideAtStart: false, w: '600px', h: '200px', ajaxURL: "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=packages&do=add_image&image_temp={$imageTemp}&secure_key=" + ipb.vars['md5_hash'], modal: false } );
			}
			
			function saveImage()
			{
				var ret = frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML;
				var data = eval("("+ret+")");
				
				if( data.error != undefined )
				{
					alert( "{$this->lang->words['package_image_error']} (" + data.error + ")" );
				}
				else
				{
					var row = $('images-table').insertRow( $('images-table').rows.length );
					row.addClassName('ipsControlRow');
					row.id = 'package-image-' + data.id;
					
					var cell_1 =  row.insertCell(0);
					cell_1.innerHTML = "<input type='radio' name='primary' value='"+data.id+"' />";
					
					var cell_2 =  row.insertCell(1);
					cell_2.innerHTML = "<input type='hidden' name='images["+data.id+"]' value='"+data.file+"' id='image-location-"+data.id+"' /><img src='{$this->settings['upload_url']}/"+data.file+"' "+data.dims+" style='padding:5px;' />";
					
					var cell_3 =  row.insertCell(2);
					cell_3.innerHTML = "<ul class='ipsControlStrip'><li class='i_delete'><a href='#' onclick='deleteImage("+data.id+")'>{$this->lang->words['delete']}...</a></li></ul>";
				
					new Effect.Fade( $( 'addimagepopup'+ _currentPopup +'_popup' ), {duration:0.5} );
				}
			}
						
		</script>
		
		<input type='hidden' name='image_temp' value='{$imageTemp}' />

		<table class='ipsTable double_pad' id='images-table'>
			<tr>
				<th>Main Image</th>
				<th>Image</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

	foreach ( $images as $image )
	{
		$dims = IPSLib::getTemplateDimensions( "{$this->settings['upload_url']}/{$image['image_location']}", 100, 100 );
		$imageSelected = $image['image_primary'] ? "checked='checked'" : '';
		
		$tab7 .= <<<HTML
			<tr class='ipsControlRow' id='package-image-{$image['image_id']}'>
				<td><input type='radio' name='primary' value='{$image['image_id']}' {$imageSelected} />
				<td>
					<input type='hidden' name='images[{$image['image_id']}]' value='{$image['image_location']}' id='image-location-{$image['image_id']}' />
					<img src='{$this->settings['upload_url']}/{$image['image_location']}' {$dims} style='padding:5px;' />
				</td>
				<td>
					<ul class='ipsControlStrip'>
						<li class='i_delete'>
							<a href='#' onclick='deleteImage({$image['image_id']})'>{$this->lang->words['delete']}...</a>
						</li>
					</ul>
				</td>
			</tr>
HTML;
	}

$tab7 .= <<<HTML
		</table>
		<div class="acp-actionbar">
			<input type='button' value='{$this->lang->words['package_image_add']}' class='realbutton' onclick='addImage()'>
		</div>
HTML;


//-----------------------------------------
// Put it together
//-----------------------------------------

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=save_package' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<input type='hidden' name='reload' id='reload' value='0' />
	<input type='hidden' name='post_key' value='{$postKey}' />
	<input type='hidden' name='type' value='{$type}' />
	<input type='hidden' name='duplicate' value='{$dupe}' />
	
HTML;

	if ( empty( $current ) )
	{
		$_one	= sprintf( $this->lang->words['step'], 1 );
		$_two	= sprintf( $this->lang->words['step'], 2 );
		$_thr	= sprintf( $this->lang->words['step'], 3 );
		$_fou	= sprintf( $this->lang->words['step'], 4 );
		$_fiv	= sprintf( $this->lang->words['step'], 5 );
		$_six	= sprintf( $this->lang->words['step'], 6 );
		$_svn	= sprintf( $this->lang->words['step'], 7 );
		
		$IPBHTML .= <<<HTML
	<div class='ipsSteps_wrap'>
		<div class='ipsSteps clearfix' id='steps_bar'>
			<ul>
				<li class='steps_active' id='step_1'>
					<strong class='steps_title'>{$_one}</strong>
					<span class='steps_desc'>{$this->lang->words['package_tab_1']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_2'>
					<strong class='steps_title'>{$_two}</strong>
					<span class='steps_desc'>{$this->lang->words['package_images']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_3'>
					<strong class='steps_title'>{$_thr}</strong>
					<span class='steps_desc'>{$this->lang->words[ $type . '_settings' ]}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_4'>
					<strong class='steps_title'>{$_fou}</strong>
					<span class='steps_desc'>{$this->lang->words['package_tab_2']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_5'>
					<strong class='steps_title'>{$_fiv}</strong>
					<span class='steps_desc'>{$this->lang->words['package_tab_4']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_6'>
					<strong class='steps_title'>{$_six}</strong>
					<span class='steps_desc'>{$this->lang->words['package_tab_5']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
				<li id='step_7'>
					<strong class='steps_title'>{$_svn}</strong>
					<span class='steps_desc'>{$this->lang->words['package_tab_6']}</span>
					<span class='steps_arrow'>&nbsp;</span>
				</li>
			</ul>
		</div>
		<div class='ipsSteps_wrapper' id='ipsSteps_wrapper'>
			<div id='step_1_content' class='steps_content'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_tab_1']}</h3>
					{$tab1}
				</div>
			</div>
			<div id='step_2_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_images']}</h3>
					{$tab7}
				</div>
			</div>
			<div id='step_3_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words[ $type . '_settings' ]}</h3>
					{$tab2}
				</div>
			</div>
			<div id='step_4_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_tab_3']}</h3>
					{$tab3}
				</div>
			</div>
			<div id='step_5_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_tab_4']}</h3>
					{$tab4}
				</div>
			</div>
			<div id='step_6_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_tab_5']}</h3>
					{$tab5}
				</div>
			</div>
			<div id='step_7_content' class='steps_content' style='display:none'>
				<div class='acp-box'>
					<h3>{$this->lang->words['package_tab_6']}</h3>
					{$tab6}
				</div>
			</div>
			<div id='steps_navigation' class='clearfix' style='margin-top: 10px;'>
				<input type='button' class='realbutton left' value='{$this->lang->words['wiz_prev']}' id='prev' />
				<input type='button' class='realbutton right' value='{$this->lang->words['wiz_next']}' id='next' />
				<p class='right' id='finish' style='display: none'>
					<input type='submit' class='realbutton' value='{$this->lang->words['save']}' />
				</p>
			</div>
			<script type='text/javascript'>
				jQ("#steps_bar").ipsWizard( { allowJumping: true, allowGoBack: false } );
			</script>
		</div>
	</div>
HTML;
	}
	else
	{
		$IPBHTML .= <<<HTML
	<div class='acp-box'>
		<h3>{$title}</h3>
		<div id='tabstrip_package' class='ipsTabBar with_left with_right'>
			<span class='tab_left'>&laquo;</span>
			<span class='tab_right'>&raquo;</span>
			<ul>
				<li id='tab_1'>{$this->lang->words['package_tab_1']}</li>
				<li id='tab_2'>{$this->lang->words['package_images']}</li>
				<li id='tab_3'>{$this->lang->words[ $type . '_settings' ]}</li>
				<li id='tab_4'>{$this->lang->words['package_tab_2']}</li>
				<li id='tab_5'>{$this->lang->words['package_tab_4']}</li>
				<li id='tab_6'>{$this->lang->words['package_tab_5']}</li>
				<li id='tab_7'>{$this->lang->words['package_tab_6']}</li>
			</ul>
		</div>
		<div id='tabstrip_package_content' class='ipsTabBar_content'>
			<div id='tab_1_content'>
				{$tab1}
			</div>
			<div id='tab_2_content'>
				{$tab7}
			</div>
			<div id='tab_3_content'>
				{$tab2}
			</div>
			<div id='tab_4_content'>
				{$tab3}
			</div>
			<div id='tab_5_content'>
				{$tab4}
			</div>
			<div id='tab_6_content'>
				{$tab5}
			</div>
			<div id='tab_7_content'>
				{$tab6}
			</div>
		</div>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['save']}' class='button'>
			<input type='submit' value='{$this->lang->words['sar']}' class='button' onclick="$('reload').value = 1;">
		</div>
	</div>
	<script type='text/javascript'>
		jQ("#tabstrip_package").ipsTabBar({ tabWrap: "#tabstrip_package_content" });
	</script>
HTML;
	}

	$IPBHTML .= <<<HTML
</form>

<script type='text/javascript'>
	var renew_options = 0;
	var loyalty = 0;
	var bundle = 0;
	var usergroup = 0;
	var bulk = 0;
	
	function addRenewalOption( unit, term, price, add )
	{
		renew_options++;
		var row = $('renewal-options').insertRow( $('renewal-options').rows.length );
		row.id = 'renewoption-' + renew_options
		row.class = 'ipsControlRow';
		
		var selected = new Array();
		selected['d'] = '';
		selected['w'] = '';
		selected['m'] = '';
		selected['y'] = '';
		selected[ unit ] = "selected='selected'";
		
		var cell = row.insertCell(0);
		cell.innerHTML = "{$this->lang->words['every']} <input name='renewal_term[" + renew_options + "]' size='3' class='input_text' value='" + term + "' /> <select name='renewal_unit[" + renew_options + "]' id='renewal_unit[" + renew_options + "]'><option value='d' " + selected['d'] + ">{$this->lang->words['renew_term_days']}</option><option value='w' " + selected['w'] + ">{$this->lang->words['renew_term_weeks']}</option><option value='m' " + selected['m'] + ">{$this->lang->words['renew_term_months']}</option><option value='y' " + selected['y'] + ">{$this->lang->words['renew_term_years']}</option></select>";
		
		var cell = row.insertCell(1);
		cell.innerHTML = "{$this->lang->words['renewal_price']}<input name='renewal_price[" + renew_options + "]' value='" + price + "' size='5' class='input_text' /> {$this->settings['nexus_currency']}";
		
		var cell = row.insertCell(2);
		var checked = '';
		if ( add )
		{
			checked = "checked='checked'";
		}
		cell.innerHTML = "<label for='renewal_add[" + renew_options + "]'><input type='checkbox' name='renewal_add[" + renew_options + "]' id='renewal_add[" + renew_options + "]' " + checked + " /> {$this->lang->words['renewal_price_add']}</label>";
				
		var cell = row.insertCell(3);
		cell.innerHTML = "<ul class='ipsControlStrip'><li class='i_delete'><a onclick='removeRenewalOption(" + renew_options + ")' style='cursor:pointer'>{$this->lang->words['delete']}</a></li></ul>";
	}
	
	function removeRenewalOption( id )
	{
		if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
		$( 'renewoption-' + id ).innerHTML = "";
	}
		
	function addLoyalty( owns, price, active )
	{
		loyalty++;
		var row = $('loyalty-discounts').insertRow( $('loyalty-discounts').rows.length );
		var cell = row.insertCell(0);
		cell.innerHTML = "<span id='loyalty-" + loyalty + "'><a onclick='removeLoyalty(" + loyalty + ")' style='cursor:pointer'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></a> {$this->lang->words['package_loyalty_1']}  <input name='loyalty_owns[" + loyalty + "]' size='3' value='" + owns + "' /> {$this->lang->words['package_loyalty_2']} <select name='loyalty_package[" + loyalty + "]' id='loyalty_package[" + loyalty + "]'><option value='0'>{$this->lang->words['this_package']}</option>{$packageSelector}</select>, {$this->lang->words['package_loyalty_3']} <input name='loyalty_price[" + loyalty + "]' size='7' value='" + price + "' /><br /><input type='checkbox' name='loyalty_active[" + loyalty + "]' " + active + " /> {$this->lang->words['package_loyalty_m']}<br /><br /></span>";
	}
	
	function removeLoyalty( id )
	{
		if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
		$( 'loyalty-' + id ).innerHTML = "";
	}
	
	function addBundle( discount, combine )
	{
		bundle++;
		var row = $('bundle-discounts').insertRow( $('bundle-discounts').rows.length );
		var cell = row.insertCell(0);
		cell.innerHTML += "<span id='bundle-" + bundle + "'><a onclick='removeBundle(" + bundle + ")' style='cursor:pointer'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></a> {$this->lang->words['package_bundle_1']} <select name='bundle_package[" + bundle + "]' id='bundle_package[" + bundle + "]'>{$packageSelector}</select>, {$this->lang->words['package_bundle_2']} <input name='bundle_discount[" + bundle + "]' size='3' value='" + discount + "' />{$this->lang->words['package_bundle_3']}<br /><input type='checkbox' name='bundle_combine[" + bundle + "]' " + combine + " /> {$this->lang->words['package_bundle_m']}<br /><br /></span>";
	}
	
	function removeBundle( id )
	{
		if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
		$( 'bundle-' + id ).innerHTML = "";
	}
	
	function addUsergroup( price, secondary )
	{
		usergroup++;
		var row = $('usergroup-discounts').insertRow( $('usergroup-discounts').rows.length );
		var cell = row.insertCell(0);
		cell.innerHTML = "<span id='usergroup-" + usergroup + "'><a onclick='removeUsergroup(" + usergroup + ")' style='cursor:pointer'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></a> {$this->lang->words['package_usergroup_1']}  <select name='usergroup_group[" + usergroup + "]' id='usergroup_group[" + usergroup + "]'>{$groupOptions}</select> {$this->lang->words['package_usergroup_2']} <input name='usergroup_price[" + usergroup + "]' size='7' value='" + price + "' /><br /><input type='checkbox' name='usergroup_secondary[" + usergroup + "]' " + secondary + " /> {$this->lang->words['package_usergroup_m']}<br /><br /></span>";
	}
	
	function removeUsergroup( id )
	{
		if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
		$( 'usergroup-' + id ).innerHTML = "";
	}
	
	function addBulk( buying, price )
	{
		if ( !price )
		{
			price = 0;
		}
		bulk++;
		var row = $('bulk-discounts').insertRow( $('bulk-discounts').rows.length );
		var cell = row.insertCell(0);
		cell.innerHTML = "<span id='bulk-" + bulk + "'><a onclick='removeBulk(" + bulk + ")' style='cursor:pointer'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></a> {$this->lang->words['package_bulk_1']}  <input name='bulk_buying[" + bulk + "]' size='3' value='" + buying + "' /> {$this->lang->words['package_bulk_2']} <select name='bulk_package[" +  bulk + "]' id='bulk_package[" +  bulk + "]'><option value='0'>{$this->lang->words['this_package']}</option>{$packageSelector}</select>, {$this->lang->words['package_bulk_3']} <input name='bulk_price[" + bulk + "]' size='7' value='" + price + "' /><br /></span>";
	}
	
	function removeBulk( id )
	{
		if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }
		$( 'bulk-' + id ).innerHTML = "";
	}
</script>

HTML;

// Populate renewal options
if ( $current['p_renew_options'] )
{
	$renewalOptions = unserialize( $current['p_renew_options'] );
	if ( !empty( $renewalOptions ) )
	{
		foreach ( $renewalOptions as $o )
		{
			$IPBHTML .= "<script type='text/javascript'>
				addRenewalOption('{$o['unit']}', '{$o['term']}', '{$o['price']}', '{$o['add']}');
				</script>";
		}
	}
}

// Populate discounts
if ( !empty( $current ) )
{
	$discounts = unserialize( $current['p_discounts'] );
	
	if ( !empty( $discounts['loyalty'] ) )
	{
		foreach ( $discounts['loyalty'] as $d )
		{
			$checked = '';
			if ( $d['active'] )
			{
				$checked = 'checked="checked"';
			}
			
			$IPBHTML .= "<script type='text/javascript'>
				addLoyalty('{$d['owns']}', '{$d['price']}', '{$checked}');
				$('loyalty_package['+loyalty+']').value = '{$d['package']}';
				</script>";
		}
	}
	
	if ( !empty( $discounts['bundle'] ) )
	{
		foreach ( $discounts['bundle'] as $d )
		{
			$checked = '';
			if ( $d['combine'] )
			{
				$checked = 'checked="checked"';
			}
			
			$IPBHTML .= "<script type='text/javascript'>
				addBundle( '{$d['discount']}', '{$checked}');
				$('bundle_package['+bundle+']').value = '{$d['package']}';
			</script>";
		}
	}
		
	if ( !empty( $discounts['usergroup'] ) )
	{
		foreach ( $discounts['usergroup'] as $d )
		{
			$checked = '';
			if ( $d['secondary'] )
			{
				$checked = 'checked="checked"';
			}
			
			$IPBHTML .= "<script type='text/javascript'>
				addUsergroup( '{$d['price']}', '{$checked}');
				$('usergroup_group['+usergroup+']').value = '{$d['group']}';
			</script>";
		}
	}
	
	if ( !empty( $discounts['bulk'] ) )
	{
		foreach ( $discounts['bulk'] as $d )
		{
			$IPBHTML .= "<script type='text/javascript'>
				addBulk( '{$d['buying']}', '{$d['price']}');
				$('bulk_package['+bulk+']').value = '{$d['package']}';
			</script>";
		}
	}
}

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Product options: Add Image
//===========================================================================
function addImage( $imageTemp ) {
$IPBHTML = "";
//--starthtml--//

$uploadBox = ipsRegistry::getClass('output')->formUpload( 'PRODUCT_IMAGE' );

$rand = md5( uniqid() );

$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&amp;module=ajax&amp;section=packages&amp;do=do_image_upload&amp;secure_key={$this->member->form_hash}' method='post' enctype='multipart/form-data' id='file_upload_form-{$rand}'>
	<input type='hidden' name='image_temp' value='{$imageTemp}' />
	<div class='acp-box'>
		<h3>{$this->lang->words['package_image_add']}</h3>
		<table class="ipsTable double_pad" style='width:100%'>
			<tr>
				<td class='field_field' colspan='2'>{$uploadBox}</td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['package_image_add']}' class='realbutton' />
		</div>
	</div>
	<iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #fff;">
</form>
<script type='text/javascript'>
	document.getElementById('file_upload_form-{$rand}').onsubmit=function() {
		document.getElementById('file_upload_form-{$rand}').target = 'upload_target';
		document.getElementById("upload_target").onload = saveImage;
	}
</script>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Remove Purchases
//===========================================================================
function removePurchases( $package, $otherPackages ) {

$newPackage = ipsRegistry::getClass('output')->formDropdown( 'swap', $otherPackages );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['package_remove_purchases']}</h2>
</div>

<br />

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=do_remove_purchases' method='post'>
<input type='hidden' name='id' value='{$package['p_id']}' />
<input type='hidden' name='action' value='swap' />
<div class='acp-box'>
	<h3>{$this->lang->words['package_swap']}</h3>
	<table class='ipsTable'>
		<tr>
			<td>
				<p>{$this->lang->words['package_swap_desc']}</p>
				<p>{$this->lang->words['package_swap_select']}{$newPackage}</p>
				<p><input type='checkbox' name='reset_renewals' /> {$this->lang->words['package_swap_m']}
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['package_swap']}' class='realbutton'>
	</div>
</div>
</form>

<br /><br />

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=do_remove_purchases' method='post'>
<input type='hidden' name='id' value='{$package['p_id']}' />
<input type='hidden' name='action' value='cancel' />
<div class='acp-box'>
	<h3>{$this->lang->words['package_cancel']}</h3>
	<table class='ipsTable'>
		<tr>
			<td>
				{$this->lang->words['package_cancel_desc']}
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['package_cancel']}' class='redbutton realbutton'>
	</div>
</div>
</form>

<br /><br />

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=do_remove_purchases' method='post'>
<input type='hidden' name='id' value='{$package['p_id']}' />
<input type='hidden' name='action' value='delete' />
<div class='acp-box'>
	<h3>{$this->lang->words['package_delete']}</h3>
	<table class='ipsTable'>
		<tr>
			<td>
				{$this->lang->words['package_delete_desc']}
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['package_delete']}' class='redbutton realbutton'>
	</div>
</div>
</form>


HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Change Package Details
//===========================================================================
function packageDetailsChanged( $current, $changed, $newValues, $reload ) {

$serialized = array();
$messageinabottle = urlencode( $this->lang->words['package_saved'] );

$doNothing = ( $reload ) ? "module=stock&amp;section=packages&amp;do=edit_package&amp;id={$current->data['p_id']}" : "module=stock&amp;section=packages&amp;cat={$current->data['p_group']}&amp;messageinabottleacp={$messageinabottle}";

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$current->data['p_name']} {$this->lang->words['edited']}</h2>
</div>
<div class='information-box'>
	{$this->lang->words['package_details_changed']}
	<ul>
HTML;
	foreach ( $changed as $type => $fields )
	{
		foreach ( $fields as $f => $cv )
		{
			$l = $this->lang->words[ str_replace( 'p_', ( $type == 'core' ? 'package_' : $type . '_' ), $f ) ];
			$IPBHTML .= "<li>{$l}</li>";
		}
	}
$IPBHTML .= <<<HTML
	</ul>
</div>
<br />

<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=edit_purchases' id='pdc_all' method='post'>
	<input type='hidden' name='id' value='{$current->data['p_id']}' />
	<div class='acp-box'>
		<h3>{$this->lang->words['pdc_all']}</h3>
HTML;
	
	if ( isset( $changed['core']['p_renew_options'] ) and $current->data['p_renew_options'] )
	{
		$options = array( array( '-', $this->lang->words['package_change_nochange'] ) );
		foreach ( unserialize( $newValues['p_renew_options'] ) as $k => $term )
		{
			$options[] = array( $k, ipsRegistry::getAppClass('nexus')->formatRenewalTerms( $term ) );
		}
		$options[] = array( 'z', $this->lang->words['package_change_nothing1'] );
		$options[] = array( 'y', $this->lang->words['package_change_nothing2'] );
		$options[] = array( 'x', $this->lang->words['package_change_nothing3'] );
	
		$IPBHTML .= <<<HTML
		<table class='ipsTable'>
			<tr>
				<td colspan='2'>{$this->lang->words['psc_all_blurb']}</td>
			</tr>
			<tr>
				<th>{$this->lang->words['package_change_renewals_1']}</th>
				<th>{$this->lang->words['package_change_renewals_2']}</th>
			</tr>
HTML;
			foreach ( unserialize( $current->data['p_renew_options'] ) as $k => $term )
			{
				$selectBox = $this->registry->output->formDropdown( "nrt[{$term['unit']}:{$term['term']}:{$term['price']}]", $options, $k );
				$term = ipsRegistry::getAppClass('nexus')->formatRenewalTerms( $term );
				$IPBHTML .= <<<HTML
			<tr>
				<td>{$term}</td>
				<td>{$selectBox}</td>
			</tr>
HTML;
			}
			
			$selectBox = $this->registry->output->formDropdown( "nrt[x]", $options );

$IPBHTML .= <<<HTML
			<tr>
				<td>
					{$this->lang->words['pdc_other']}
				</td>
				<td>{$selectBox}</td>
			</tr>
		</table>
HTML;
	}

$IPBHTML .= <<<HTML
		<div class="acp-actionbar">
			<a class='realbutton' href='#' onclick="$('pdc_all').submit();">{$this->lang->words['pdc_all']}</a>
		</div>
	</div>
</form>
<br /><br />

<div class='acp-box'>
	<h3>{$this->lang->words['pdc_existing']}</h3>
	<table class='ipsTable'>
		<tr>
			<td colspan='2'>{$this->lang->words['pdc_existing_blurb']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<a class='realbutton' href='{$this->settings['base_url']}&amp;{$doNothing}'>{$this->lang->words['pdc_existing']}</a></a>
	</div>
</div>
<br /><br />

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Delete Package
//===========================================================================
function packageDelete( $package ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='section_title'>
	<h2>{$package['p_name']}</h2>
</div>
<div class='information-box'>
	{$this->lang->words['package_delete_blurb']}
</div>
<br />
<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=do_delete_package&amp;id={$package['p_id']}'>{$this->lang->words['package_delete_okay']}</a></div>
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;do=remove_purchases&amp;id={$package['p_id']}'>{$this->lang->words['package_delete_remove']}</a></div>
</div>

<br /><br />

<div class='redirector'>
	<div class='info'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=packages&amp;cat={$package['p_group']}'>{$this->lang->words['package_delete_back']}</a></div>
</div>

<br /><br />


HTML;

//--endhtml--//
return $IPBHTML;
}



}