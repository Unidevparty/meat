<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Promotion - Advertisement Packages
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		28th May 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_promotion_packages
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
function manage( $packages ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['adpacks']}</h2>
	<ul class='context_menu'>
		<li><a href='{$this->settings['base_url']}module=promotion&amp;section=packages&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['adpack_add']}</a></li>
	</ul>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['adpacks']}</h3>
	<div>
		<table class='alternate_rows'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='25%'>{$this->registry->getClass('class_localization')->words['adpack_name']}</th>
				<th width='25%'>{$this->registry->getClass('class_localization')->words['ad_locations']}</th>
				<th width='19%'>{$this->registry->getClass('class_localization')->words['adpack_terms']}</th>
				<th width='19%'>{$this->registry->getClass('class_localization')->words['adpack_cost']}</th>
				<th width='8%'>&nbsp;</th>
			</tr>
		</table>
		<ul class='sortable_handle alternate_rows' id='sortable_handle'>
HTML;

if ( !empty( $packages ) )
{
	foreach ( $packages as $data )
	{
		$IPBHTML .= <<<HTML
			<li id='adpack_{$data['ap_id']}' class='isDraggable'>
			<table class='adv_controls'>
				<tr class='control_row'>
					<td style='width: 4%; text-align: center'>
						<div class='draghandle'></div>
					</td>
					<td width='25%'>{$data['ap_name']}</td>
					<td width='25%'>{$data['locations']}</td>
					<td width='19%'>{$data['terms']}</td>
					<td width='19%'>{$data['ap_cost']}</td>
					<td width='8%'>
						<ul class='controls no_extra'>
							<li class='left'>
								<a class='edit' href='{$this->settings['base_url']}module=promotion&amp;section=packages&amp;do=edit&amp;id={$data['ap_id']}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
							</li>
							<li class='right'>
								<a class='delete' onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}module=promotion&amp;section=packages&amp;do=delete&amp;id={$data['ap_id']}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
							</li>
						</ul>
					</td>
				</tr>
			</table>
			</li>
HTML;
	
	}
}
else
{
	$IPBHTML .= <<<HTML
			<table>
				<tr>
					<td colspan='6'>
						<p style='text-align:center'><em>{$this->registry->getClass('class_localization')->words['adpack_empty']} <a href='{$this->settings['base_url']}module=promotion&amp;section=packages&amp;do=add'>{$this->registry->getClass('class_localization')->words['click2create']}</a>.</em></p>
					</td>
				</tr>
			</table>
HTML;
}

$IPBHTML .= <<<HTML
		</ul>
	</div>
</div>

<script type="text/javascript">
	dropItLikeItsHot = function( draggableObject, mouseObject )
	{
		var options = {
						method : 'post',
						parameters : Sortable.serialize( 'sortable_handle', { tag: 'li', name: 'packages' } )
					};

		new Ajax.Request( "{$this->settings['base_url']}&app=nexus&module=promotion&section=packages&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' ), options );

		return false;
	};

	Sortable.create( 'sortable_handle', { only: 'isDraggable', revert: true, format: 'adpack_([0-9]+)', onUpdate: dropItLikeItsHot, handle: 'draghandle' } );
</script>

HTML;

//--endhtml--//
return $IPBHTML;

}

//===========================================================================
// Add / Edit
//===========================================================================
function form( $locations, $current ) {

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ) ? '' : $current['ap_name'] );
$form['desc'] = ipsRegistry::getClass('output')->formTextarea( 'desc', ( empty( $current ) ? '' : $current['ap_desc'] )	);
$form['location'] = ipsRegistry::getClass('output')->formMultiDropdown( 'location[]', $locations, ( empty( $current ) ) ? array() : explode( ',', $current['ap_locations'] ) );
$form['exempt'] = ipsRegistry::getClass('output')->generateGroupDropdown( 'exempt[]', ( empty( $current ) ) ? array() : explode( ',', $current['ap_exempt'] ), TRUE );
$form['expire'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire', ( empty( $current ) ) ? 0 : $current['ap_expire'] );
$form['expire_unit'] = ipsRegistry::getClass('output')->formDropDown( 'expire_unit', array(
	array( 'i', $this->registry->getClass('class_localization')->words['ad__impressions'] ),
	array( 'c', $this->registry->getClass('class_localization')->words['ad__clicks'] ),
	array( 'd', ucwords( $this->registry->getClass('class_localization')->words['renew_term_days'] ) ),
	array( 'w', ucwords( $this->registry->getClass('class_localization')->words['renew_term_weeks'] ) ),
	array( 'm', ucwords( $this->registry->getClass('class_localization')->words['renew_term_months'] ) ),
	array( 'y', ucwords( $this->registry->getClass('class_localization')->words['renew_term_years'] ) )
	), ( empty( $current ) ) ? 'i' : $current['ap_expire_unit'] );
$form['expire'] = ipsRegistry::getClass('output')->formSimpleInput( 'expire', ( empty( $current ) ) ? 0 : $current['ap_expire'] );
$form['price'] = ipsRegistry::getClass('output')->formSimpleInput( 'price', ( empty( $current ) ) ? 0 : $current['ap_price'] );
$form['max_height'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_height', ( empty( $current ) ) ? 0 : $current['ap_max_height'] );
$form['max_width'] = ipsRegistry::getClass('output')->formSimpleInput( 'max_width', ( empty( $current ) ) ? 0 : $current['ap_max_width'] );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['adpack_add']}</h2>
</div>

<form action='{$this->settings['base_url']}module=promotion&amp;section=packages&amp;do=save' method='post'>
<input type='hidden' name='id' value='{$current['ap_id']}' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['adpack_add']}</h3>
	<ul class="acp-form alternate_rows">
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['adpack_name']}</strong></label>
			{$form['name']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['adpack_desc']}</strong></label>
			{$form['desc']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['ad_locations']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['adpack_locations_desc']}</span></label>
			{$form['location']}
		</li>
HTML;

	$IPBHTML .= <<<HTML
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['ad_exempt']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['adpack_exempt_desc']}</span></label>
			{$form['exempt']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['ad_expire']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['ad_expire_desc']}</span></label>
			{$form['expire']} {$form['expire_unit']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['adpack_cost']}</strong></label>
			{$form['price']} {$this->settings['nexus_currency']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['adpack_max_height']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['adpack_dims_desc']}</span></label>
			{$form['max_height']}
		</li>
		<li>
			<label><strong>{$this->registry->getClass('class_localization')->words['adpack_max_width']}</strong><span class='desctext'>{$this->registry->getClass('class_localization')->words['adpack_dims_desc']}</span></label>
			{$form['max_width']}
		</li>
	</ul>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton'>
	</div>
</div>
</form>


HTML;

//--endhtml--//
return $IPBHTML;

}


}