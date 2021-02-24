<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Promotion - Referral Banners
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		8th June 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_promotion_referrals
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
function manage( $referrals ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['referral_banners']}</h2>
	<ul class='context_menu'>
		<li><a href='{$this->settings['base_url']}module=promotion&amp;section=referrals&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['referral_add']}</a></li>
	</ul>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['referral_banners']}</h3>
	<table class='ipsTable' id='referralBanners_list'>
		<tr>
			<th width='3%'>&nbsp;</th>
			<th width='89%'>{$this->registry->getClass('class_localization')->words['referral_banner']}</th>
			<th width='8%'>&nbsp;</th>
		</tr>
HTML;

if ( !empty( $referrals ) )
{
	foreach ( $referrals as $data )
	{
		$IPBHTML .= <<<HTML
		<tr class='ipsControlRow isDraggable' id='banners_{$data['rb_id']}'>
			<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
			<td><img src='{$data['rb_url']}' /></td>
			<td>
				<ul class='ipsControlStrip'>
					<li class='i_edit'>
						<a href='{$this->settings['base_url']}module=promotion&amp;section=referrals&amp;do=edit&amp;id={$data['rb_id']}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
					</li>
					<li class='i_delete'>
						<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}module=promotion&amp;section=referrals&amp;do=delete&amp;id={$data['rb_id']}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
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
				{$this->registry->getClass('class_localization')->words['referral_empty']} <a href='{$this->settings['base_url']}module=promotion&amp;section=referrals&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
			</td>
		</tr>
HTML;
}

$IPBHTML .= <<<HTML
	</table>
</div>

<script type='text/javascript'>
	jQ("#referralBanners_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=promotion&section=referrals&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;

}

//===========================================================================
// Add / Edit
//===========================================================================
function form( $current ) {

$form['url'] = ipsRegistry::getClass('output')->formInput( 'url', ( empty( $current ) or $current['rb_upload'] ) ? '' : $current['rb_url'] );
$form['upload'] = ipsRegistry::getClass('output')->formUpload();

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['referral_add']}</h2>
</div>

<form action='{$this->settings['base_url']}module=promotion&amp;section=referrals&amp;do=save' method='post' enctype='multipart/form-data'>
<input type='hidden' name='id' value='{$current['rb_id']}' />
<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['referral_add']}</h3>
	<table class="ipsTable double_pad">
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['referral_upload']}</td></label>
			<td class='field_field'>{$form['upload']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['referral_url']}</td></label>
			<td class='field_field'>{$form['url']}</td>
		</tr>
	</table>
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