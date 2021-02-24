<?php
/**
 * @file		cp_skin_hosting_errors.php		Hosting Server Error Log View
 *
 * $Copyright: $
 * $License: $
 * $Author: mark $
 * $LastChangedDate: 2011-06-10 10:07:06 -0400 (Fri, 10 Jun 2011) $
 * $Revision: 9023 $
 * @since 		18th January 2011
 */

/**
 *
 * @class	cp_skin_hosting_errors
 * @brief	Hosting Server Error Log View
 *
 */
class cp_skin_hosting_errors
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
function manage( $errors, $servers ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['herrs']}</h2>
</div>

<div class='acp-box alt'>
 	<h3>{$this->registry->getClass('class_localization')->words['herrs']}</h3>
	<div>
		<table class='ipsTable'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='15%'>{$this->registry->getClass('class_localization')->words['herr_time']}</th>
				<th width='12%'>{$this->registry->getClass('class_localization')->words['herr_server']}</th>
				<th width='12%'>{$this->registry->getClass('class_localization')->words['herr_function']}</th>
				<th width='12%'>{$this->registry->getClass('class_localization')->words['herr_account']}</th>
				<th width='42%'>{$this->registry->getClass('class_localization')->words['herr_message']}</th>
				<th width='3%' class='col_buttons'>&nbsp;</th>
			</tr>
HTML;

if ( !empty( $errors ) )
{
	
	foreach ( $errors as $data )
	{
		$extra = unserialize( $data['e_extra'] );
		$user = $extra['params']['user'] ? $extra['params']['user'] : $extra['params']['username'];
	
		$IPBHTML .= <<<HTML
				<tr class='ipsControlRow'>
					<td>&nbsp;</td>
					<td>{$this->registry->getClass('class_localization')->getDate( $data['e_time'], 'LONG' )}</td>
					<td>{$servers[ $data['e_server'] ]['server_hostname']}</td>
					<td><span class='larger_text'><a href='#' onclick='viewInfo({$data['e_id']})'>{$extra['function']}</a></span></td>
					<td><a href='{$this->settings['base_url']}&amp;module=search&amp;section=search&amp;nexus_searchby=username&amp;nexus_search={$user}'>{$user}</a></td>
					<td>{$data['e_message']}</td>
					<td>
						<ul class='ipsControlStrip'>
							<li class='i_refresh'>
								<a href='{$this->settings['base_url']}&amp;module=hosting&amp;section=errors&amp;do=resend&amp;id={$data['e_id']}' title='{$this->registry->getClass('class_localization')->words['herr_resend']}'>{$this->registry->getClass('class_localization')->words['herr_resend']}</a>
							</li>
							<li class='i_delete'>
								<a onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['herr_delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=errors&amp;do=delete&amp;id={$data['e_id']}' title='{$this->registry->getClass('class_localization')->words['delete']}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
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
						{$this->registry->getClass('class_localization')->words['herr_empty']}
					</td>
				</tr>
HTML;
}

$IPBHTML .= <<<HTML
		</table>
	</div>
</div>

<script type='text/javascript'>
	function viewInfo( id )
	{
		var url = ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=hosting&do=errorlog&id=" + id + "&secure_key=" + ipb.vars['md5_hash'];
		new ipb.Popup('info_' + id, { type: 'pane', stem: true, attach: { target: this, position: 'auto' }, hideAtStart: false, w: '800px', h: '1000px', ajaxURL: url } );
	}
</script>

HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Popup
//===========================================================================

function logPopup( $log ) {

$log['e_extra'] = unserialize( $log['e_extra'] );

$params = '<table>';
foreach ( $log['e_extra']['params'] as $key => $value )
{
	$params .= "<tr><td><strong>{$key}</strong></td><td>{$value}</td></tr>";
}
$params .= '</table>';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>Error Log #{$log['e_id']}</h3>
	<div>
		<table class="ipsTable double_pad">
			<tr>
				<td class='field_title'><strong class='title'>Function</strong></td>
				<td class='field_field'>{$log['e_extra']['function']}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Params</strong></td>
				<td class='field_field'>{$params}</td>
			</tr>
			<tr>
				<td class='field_title'><strong class='title'>Response</strong></td>
				<td class='field_field'>{$log['e_message']}</td>
			</tr>
		</table>
	</div>
	<div class='acp-actionbar'>
		<a class='button' href='{$this->settings['base_url']}&amp;module=hosting&amp;section=errors&amp;do=resend&amp;id={$log['e_id']}'>{$this->registry->getClass('class_localization')->words['herr_resend']}</a> <a class='button redbutton' onclick="if ( !confirm('{$this->registry->getClass('class_localization')->words['herr_delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}&amp;module=hosting&amp;section=errors&amp;do=delete&amp;id={$log['e_id']}'>{$this->registry->getClass('class_localization')->words['delete']}</a>
	</div>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}


}