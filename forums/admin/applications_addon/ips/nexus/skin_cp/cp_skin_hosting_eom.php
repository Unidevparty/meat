<?php
/**
 * @file		cp_skin_hosting_eom.php		Expected Output Monitoring View
 *
 * $Copyright: $
 * $License: $
 * $Author: mark $
 * $LastChangedDate: 2012-06-01 10:55:06 -0400 (Fri, 01 Jun 2012) $
 * $Revision: 10853 $
 * @since 		1st June 2012
 */

/**
 *
 * @class	cp_skin_hosting_eom
 * @brief	Expected Output Monitoring View
 *
 */
class cp_skin_hosting_eom
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
function manage( $rules ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['eoms']}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=add'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['eom_add']}</a></li>
		</ul>
	</div>
</div>

<div class='acp-box'>
 	<h3>{$this->lang->words['eoms']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th>{$this->lang->words['eom_url']}</th>
				<th>{$this->lang->words['eom_type']}</th>
				<th>{$this->lang->words['eom_value']}</th>
				<th class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

if ( !empty( $rules ) )
{
	
	foreach ( $rules as $data )
	{
		$data['eom_value'] = htmlspecialchars( $data['eom_value'] );
	
		$IPBHTML .= <<<HTML
				<tr class='ipsControlRow'>
					<td>&nbsp;</td>
					<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=edit&amp;id={$data['eom_id']}'>{$data['eom_url']}</a></span></td>
					<td>{$this->lang->words['eom_type_' . $data['eom_type']]}</td>
					<td>{$data['eom_value']}</td>
					<td>
						<ul class='ipsControlStrip'>
							<li class='i_edit'>
								<a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=edit&amp;id={$data['eom_id']}'>{$this->lang->words['edit']}</a>
							</li>
							<li class='i_delete' id='menu{$data['eom_id']}'>
								<a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=delete&amp;id={$data['eom_id']}'>{$this->lang->words['delete']}...</a>
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
					<td colspan='5' class='no_messages'>
						{$this->lang->words['eom_empty']} <a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=add' class='mini_button'>{$this->lang->words['click2create']}</a></em></p>
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
	$title = $this->lang->words['eom_add'];
	$id = 0;
	$icon = 'add';
}
else
{
	$title = $this->lang->words['eom_edit'];
	$id = $current['eom_id'];
	$icon = 'edit';
}

$form['url'] = ipsRegistry::getClass('output')->formInput( 'url', ( empty( $current ) ? 'http://' : $current['eom_url'] ) );
$form['type'] = ipsRegistry::getClass('output')->formDropdown( 'type', array( array( 'c', $this->lang->words['eom_type_c'] ), array( 'e', $this->lang->words['eom_type_e'] ), array( 'n', $this->lang->words['eom_type_n'] ) ), ( empty( $current ) ? 'c' : $current['eom_type'] ) );
$form['value'] = ipsRegistry::getClass('output')->formTextarea( 'value', ( empty( $current ) ? '' : $current['eom_value'] ) );
$form['notify'] = ipsRegistry::getClass('output')->formInput( 'notify', ( empty( $current ) ? '' : $current['eom_notify'] ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<form action='{$this->settings['base_url']}&amp;module=hosting&amp;section=eom&amp;do=save' method='post'>
	<input type='hidden' name='id' value='{$id}' />
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['eom_url']}</strong></label></td>
			<td class='field_field'>{$form['url']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['eom_type']}</strong></label></td>
			<td class='field_field'>{$form['type']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['eom_value']}</strong></label></td>
			<td class='field_field'>{$form['value']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['eom_notify']}</strong></label></td>
			<td class='field_field'>
				{$form['notify']}<br />
				<span class='desctext'>{$this->lang->words['eom_notify_desc']}</span>
			</td>
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

}