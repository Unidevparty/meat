<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Promotion - Advertisements
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		7th February 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_promotion_advertisements
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
function manage( $active, $pending, $inactive ) {

if ( $this->request['pending'] )
{
	$defaultTab = ", defaultTab: 'tab_2'";
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['advertisements']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=add1'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['ad_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['advertisements']}</h3>
	<div id='tabstrip_ads' class='ipsTabBar with_left with_right'>
		<span class='tab_left'>&laquo;</span>
		<span class='tab_right'>&raquo;</span>
		<ul>
			<li id='tab_1'>{$this->lang->words['ad_active']}</li>
			<li id='tab_2'>{$this->lang->words['ad_pending']}</li>
			<li id='tab_3'>{$this->lang->words['ad_inactive']}</li>
		</ul>
	</div>
	<div id='tabstrip_ads_content' class='ipsTabBar_content'>
		<div id='tab_1_content'>
			{$this->_getAds( $active )}
		</div>
		<div id='tab_2_content'>
			{$this->_getAds( $pending )}
		</div>
		<div id='tab_3_content'>
			{$this->_getAds( $inactive )}
		</div>
	</div>
</div>

<script type='text/javascript'>
	jQ("#tabstrip_ads").ipsTabBar({ tabWrap: "#tabstrip_ads_content" {$defaultTab} });
</script>

HTML;

//--endhtml--//
return $IPBHTML;

}

function _getAds( $ads )
{
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
	<table class='form_table'>
		<tr style='max-height:50px;  overflow: scroll'>
			<th>{$this->lang->words['ad_preview']}</th>
			<th>{$this->lang->words['ad_member']}</th>
			<th>{$this->lang->words['ad_locations']}</th>
			<th>{$this->lang->words['ad__impressions']}</th>
			<th>{$this->lang->words['ad__clicks']}</th>
			<th width='8%' class='col_buttons'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $ads ) )
{
	
	foreach ( $ads as $data )
	{
		$class = ( $class == 'row1' ) ? 'row2' : 'row1';
		if ( $data['ad_active'] == -1 )
		{
			$class = '_amber';
		}
		
		if ( $data['ad_html'] )
		{
			$data['ad_clicks'] = "<a class='html_popup clickable'>--</a>";
		}
		
		$IPBHTML .= <<<HTML
			<tr class='ipsControlRow {$class}'>
				<td><div style='max-height: 350px; max-width:450px; overflow:auto;'>{$data['preview']}</div></td>
				<td>{$data['member']}</td>
				<td>{$data['locations']}</td>
				<td>{$data['ad_impressions']}</td>
				<td>{$data['ad_clicks']}</td>
				<td width='3%'>
					<ul class='ipsControlStrip'>
HTML;
					if ( $data['ad_active'] == -1 )
					{
						$IPBHTML .= <<<HTML
						<li class='i_accept'>
							<a href='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=approve&amp;id={$data['ad_id']}'>{$this->lang->words['approve']}</a>
						</li>
HTML;
					}
					
					$IPBHTML .= <<<HTML
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=edit&amp;id={$data['ad_id']}'>{$this->lang->words['edit']}</a>
						</li>
						<li class='i_delete'>
							<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=delete&amp;id={$data['ad_id']}'>{$this->lang->words['delete']}</a>
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
				<td colspan='7' class='no_messages'>
					{$this->lang->words['ad_empty']} <a href='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=add1' class='mini_button'>{$this->lang->words['click2create']}</a>
				</td>
			</tr>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
	
<script type='text/javascript'>

	function doPopUp( e, elem )
	{
		new ipb.Popup('html_popup_box', { type: 'balloon', stem: true, attach: { target: elem, position: 'auto' }, hideAtStart: false, w: '400px', initial: $('html-popup-box').innerHTML } );
	}
	
	ipb.delegate.register('.html_popup', doPopUp );
	
</script>

<div id='html-popup-box' class='popupWrapper' style='display:none;'>
	<div class='popupInner'>
		{$this->lang->words['ad_html_clicks_explain']}
	</div>
</div>
	
HTML;

//--endhtml--//
return $IPBHTML;

}

//===========================================================================
// Add: Step 1
//===========================================================================
function step1() {

$form['type'] = ipsRegistry::getClass('output')->formDropdown( 'type', array(
	array( 'code', $this->lang->words['ad_html'] ),
	array( 'image', $this->lang->words['ad_image'] ),
	) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['ad_add']}</h2>
</div>

<form action='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=add2' method='post'>
<div class='acp-box'>
	<h3>{$this->lang->words['ad_add']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_type']}</strong></td>
			<td class='field_field'>
				{$form['type']}<br />
				<span class='desctext'>{$this->lang->words['ad_type_desc']}</span>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['continue']}' class='realbutton'>
	</div>
</div>
</form>

HTML;

//--endhtml--//
return $IPBHTML;

}


//===========================================================================
// Add: Step 2
//===========================================================================
function step2( $type, $locations, $current, $customLocations ) {

$unit = array( array( 'i', $this->lang->words['ad__impressions'] ) );
if ( $type == 'image' )
{
	$unit[] = array( 'c', $this->lang->words['ad__clicks'] );
}

$form['html'] = ipsRegistry::getClass('output')->formTextarea( 'html', ( empty( $current ) ) ? '' : htmlentities( $current['ad_html'] ) );
$form['image_upload'] = ipsRegistry::getClass('output')->formUpload();
$form['image_url'] = ipsRegistry::getClass('output')->formInput( 'image', ( empty( $current ) ) ? '' : $current['ad_image'] );
$form['link'] = ipsRegistry::getClass('output')->formInput( 'link', ( empty( $current ) ) ? '' : $current['ad_link'] );
$form['location'] = ipsRegistry::getClass('output')->formMultiDropdown( 'location[]', $locations, ( empty( $current ) ) ? array() : explode( ',', $current['ad_locations'] ) );
$form['location_custom'] = ipsRegistry::getClass('output')->formInput( 'location_custom', $customLocations );
$form['exempt'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'exempt[]', ( empty( $current ) ) ? array() : explode( ',', $current['ad_exempt'] ), TRUE );
$form['active'] = ipsRegistry::getClass('output')->formYesNo( 'active', ( empty( $current ) ) ? 1 : $current['ad_active'] );
$form['expire'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire', ( empty( $current ) ) ? 0 : $current['ad_expire'] );
$form['expire_unit'] = ipsRegistry::getClass('output')->formDropDown( 'expire_unit', $unit, ( empty( $current ) ) ? 'i' : $current['ad_expire_unit'] );
$form['start'] = ipsRegistry::getClass('output')->formSimpleInput( 'start', $current['ad_start'], 11 );
$form['end'] = ipsRegistry::getClass('output')->formSimpleInput( 'end', $current['ad_end'], 11 );
$form['clicks'] = ipsRegistry::getClass('output')->formSimpleInput( 'clicks', ( empty( $current ) ) ? 0 : $current['ad_clicks'] );
$form['impressions'] = ipsRegistry::getClass('output')->formSimpleInput( 'impressions', ( empty( $current ) ) ? 0 : $current['ad_impressions'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['ad_add']}</h2>
</div>

<form action='{$this->settings['base_url']}module=promotion&amp;section=advertisements&amp;do=save' method='post' enctype='multipart/form-data'>
<input type='hidden' name='id' value='{$current['ad_id']}' />
<div class='acp-box'>
	<h3>{$this->lang->words['ad_add']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<th colspan='2'>{$this->lang->words['ad_settings']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_locations']}</strong></td>
			<td class='field_field'>
				{$form['location']}<br />
				<span class='desctext'>{$this->lang->words['ad_locations_desc']}</span>
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
HTML;

if ( $type == 'code' )
{
	$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_html']}</strong></td>
			<td class='field_field'>{$form['html']}</td>
		</tr>
HTML;
}
else
{
	$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['referral_upload']}</strong></td>
			<td class='field_field'>{$form['image_upload']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['referral_url']}</strong></td>
			<td class='field_field'>{$form['image_url']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_link_url']}</strong></td>
			<td class='field_field'>
				{$form['link']}<br />
				<span class='desctext'>{$this->lang->words['ad_link_url_desc']}</span>
			</td>
		</tr>
HTML;
}
	$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_exempt']}</strong></td>
			<td class='field_field'>
				{$form['exempt']}<br />
				<span class='desctext'>{$this->lang->words['ad_exempt_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['ad_active']}</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_active']}</strong></td>
			<td class='field_field'>{$form['active']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_expire']}</strong></td>
			<td class='field_field'>
				{$form['expire']} {$form['expire_unit']}<br />
				<span class='desctext'>{$this->lang->words['ad_expire_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_start']}</strong></td>
			<td class='field_field'>
				{$form['start']} {$this->lang->words['date_format']}<br />
				<span class='desctext'>{$this->lang->words['ad_start_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad_end']}</strong></td>
			<td class='field_field'>
				{$form['end']} {$this->lang->words['date_format']}<br />
				<span class='desctext'>{$this->lang->words['ad_end_desc']}</span>
			</td>
		</tr>
		<tr>
			<th colspan='2'>{$this->lang->words['ad_counts']}</th>
		</tr>
HTML;

if ( $type == 'image' )
{
	$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad__clicks']}</strong></td>
			<td class='field_field'>{$form['clicks']}</td>
		</tr>
HTML;
}
	$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['ad__impressions']}</strong></td>
			<td class='field_field'>{$form['impressions']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['save']}' class='realbutton'>
	</div>
</div>
</form>


HTML;

//--endhtml--//
return $IPBHTML;

}


}