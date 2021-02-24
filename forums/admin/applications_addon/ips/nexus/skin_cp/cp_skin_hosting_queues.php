<?php
/**
 * @file		cp_skin_hosting_queues.php		Hosting Server Queues Management View
 *
 * $Copyright: $
 * $License: $
 * $Author: ips_terabyte $
 * $LastChangedDate: 2011-11-09 14:10:24 -0500 (Wed, 09 Nov 2011) $
 * $Revision: 9800 $
 * @since 		18th January 2011
 */

/**
 *
 * @class	cp_skin_hosting_queues
 * @brief	Hosting Server Queues Management View
 *
 */
class cp_skin_hosting_queues
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
function manage( $queues ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['hques']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->registry->getClass('class_localization')->words['hque_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->registry->getClass('class_localization')->words['hques']}</h3>
	<div>
		<table class='form_table'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='63%'>{$this->registry->getClass('class_localization')->words['hque_name']}</th>
				<th width='30%'>{$this->registry->getClass('class_localization')->words['hque_server']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

if ( !empty( $queues ) )
{
	
	foreach ( $queues as $data )
	{
		$class = ( $class == 'row1' ) ? 'row2' : 'row1';
		$class = ( $data['warning'] ) ? ' _red' : $class;
	
		$IPBHTML .= <<<HTML
				<tr class='ipsControlRow {$class}'>
					<td>&nbsp;</td>
					<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=edit&amp;id={$data['queue_id']}'>{$data['queue_name']}</a></span></td>
					<td>{$data['active_server']}</td>
					<td>
						<ul class='ipsControlStrip'>
							<li class='i_edit'>
								<a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=edit&amp;id={$data['queue_id']}'>{$this->registry->getClass('class_localization')->words['edit']}</a>
							</li>
							<li class='i_delete' id='menu{$data['queue_id']}'>
								<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=delete&amp;id={$data['queue_id']}'>{$this->registry->getClass('class_localization')->words['delete']}...</a>
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
						{$this->registry->getClass('class_localization')->words['hque_empty']} <a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=add' class='mini_button'>{$this->registry->getClass('class_localization')->words['click2create']}</a></em></p>
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
function form( $current ) {

if ( empty( $current ) )
{
	$title = $this->registry->getClass('class_localization')->words['hque_add'];
	$id = 0;
	$icon = 'add';
}
else
{
	$title = $this->registry->getClass('class_localization')->words['hque_edit'];
	$id = $current['queue_id'];
	$icon = 'edit';
}

$form['name'] = ipsRegistry::getClass('output')->formInput( 'name', ( empty( $current ) ? '' : $current['queue_name'] ) );


$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=hosting&amp;section=queues&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['hque_name']}</strong></label></td>
			<td class='field_field'>{$form['name']}</td>
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