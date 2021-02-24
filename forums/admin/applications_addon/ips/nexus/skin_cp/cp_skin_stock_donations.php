<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Donations
 * Last Updated: $Date: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		7th January 2010
 * @version		$Revision: 9800 $
 */
 
class cp_skin_stock_donations
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
// Manage Goals
//===========================================================================
function manage( $goals ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['donation_goals']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['goal_add']}</a></li>
		</ul>
	</div>
</div>

HTML;

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['donation_goals']}</h3>
	<div>
		<table class='ipsTable' id='donateGoals_list'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='29%'>{$this->registry->getClass('class_localization')->words['goal_name']}</th>
				<th width='29%'>{$this->registry->getClass('class_localization')->words['goal_target']}</th>
				<th width='30%'>{$this->registry->getClass('class_localization')->words['goal_sofar']}</th>
				<th width='8%' class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

	if ( !empty( $goals ) )
	{
		foreach ( $goals as $id => $data )
		{
			$menuKey++;
			$IPBHTML .= <<<HTML
			<tr class='ipsControlRow isDraggable' id='donate_goals_{$id}'>
				<td class='col_drag'><span class='draghandle'>&nbsp;</span></td>
				<td width='31%'><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=view&amp;id={$id}'>{$data['d_name']}</a></span></td>
				<td width='31%'>{$data['d_goal']}</td>
				<td width='31%'>{$data['d_current']}</td>
				<td width='3%'>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=edit&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
						</li>
						<li class='ipsControlStrip_more ipbmenu' id='menu{$menuKey}'>
							<a href='#'>{$this->registry->getClass('class_localization')->words['options']}</a>
						</li>
					</ul>
					<ul class='acp-menu' id='menu{$menuKey}_menucontent'>
						<li class='icon view'><a href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=view&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['goal_view']}...</a></li>
						<li class='icon delete'><a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=delete&amp;id={$id}'>{$this->registry->getClass('class_localization')->words['delete']}...</a></li>

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
					{$this->registry->getClass('class_localization')->words['goal_empty']} <a href='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a>
				</td>
			</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		</table>
	</div>
</div>

<script type='text/javascript'>
	jQ("#donateGoals_list").ipsSortable( 'table', { 
		url: "{$this->settings['base_url']}&app=nexus&module=stock&section=donations&do=reorder&md5check={$this->registry->adminFunctions->getSecurityKey()}".replace( /&amp;/g, '&' )
	});
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View Goal
//===========================================================================
function view( $goal, $logs ) {

$menuKey = 0;

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$goal['d_name']}</h2>
</div>

HTML;

$IPBHTML .= <<<HTML
<div class='acp-box'>
 	<h3>{$goal['d_name']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th>{$this->registry->getClass('class_localization')->words['purchase_member']}</th>
				<th>{$this->registry->getClass('class_localization')->words['invoice_title']}</th>
				<th>{$this->registry->getClass('class_localization')->words['invoice_amount']}</th>
				<th>{$this->registry->getClass('class_localization')->words['invoice_date']}</th>
			</tr>
HTML;

	if ( !empty( $logs ) )
	{
		foreach ( $logs as $data )
		{
			$customer = customer::load( $data['dl_member'] );
			$customer = $customer->data['member_id'] ?  "<a href='{$this->settings['base_url']}&amp;module=customers&amp;section=view&amp;id={$customer->data['member_id']}'>{$customer->data['_name']}</a>" : "<span class='desctext'>{$this->lang->words['invoice_guest_desc']}</span>";
			$invoice = new invoice( $data['dl_invoice'] );
			
			$IPBHTML .= <<<HTML
			<tr>
				<td>{$customer}</td>
				<td><a href='{$this->settings['base_url']}&amp;module=payments&amp;section=invoices&amp;do=view_invoice&amp;id={$invoice->id}'>{$invoice->title}</a></td>
				<td>{$this->registry->getClass('class_localization')->formatMoney( $data['dl_amount'], FALSE )}</td>
				<td>{$this->registry->getClass('class_localization')->getDate( $data['dl_date'], 'LONG' )}</td>
			</tr>
HTML;
		}
	}
	else
	{
		$IPBHTML .= <<<HTML
			<tr>
				<td colspan='6' class='no_messages'>
					{$this->registry->getClass('class_localization')->words['goal_nologs']}
				</td>
			</tr>
HTML;
	}
	
	$IPBHTML .= <<<HTML
		</table>
	</div>
</div>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Form
//===========================================================================
function form( $current=array() ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['goal_add'];
	$id = 0;
}
else
{
	$title = $this->registry->getClass('class_localization')->words['goal_edit'];
	$id = $current['d_id'];
}

$form['title'] = ipsRegistry::getClass('output')->formInput( 'title', ( empty( $current ) ? '' : $current['d_name'] ) );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( $current['d_desc'] );
$form['description'] = $editor->show('description');

$form['target'] = ipsRegistry::getClass('output')->formSimpleInput( 'target', ( empty( $current ) ? '' : $current['d_goal'] ), 8 );
$form['current'] = ipsRegistry::getClass('output')->formSimpleInput( 'current', ( empty( $current ) ? 0 : $current['d_current'] ), 8 );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=stock&amp;section=donations&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class="ipsTable double_pad">
		<tr>
			<td class="field_title"><strong class='title'>{$this->registry->getClass('class_localization')->words['goal_name']}</strong></td>
			<td class="field_field">{$form['title']}</td>
		</tr>
		<tr>
			<td class="field_title"><strong class='title'>{$this->registry->getClass('class_localization')->words['goal_description']}</strong></td>
			<td class="field_field">{$form['description']}</td>
		</tr>
		<tr>
			<td class="field_title"><strong class='title'>{$this->registry->getClass('class_localization')->words['goal_target']}</strong></td>
			<td class="field_field">
				{$form['target']}<br />
				<span class='desctext'>{$this->registry->getClass('class_localization')->words['goal_target_desc']}</span>
			</td>
		</tr>
		<tr>
			<td class="field_title"><strong class='title'>{$this->registry->getClass('class_localization')->words['goal_sofar']}</strong></td>
			<td class="field_field">{$form['current']}</td>
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


}