<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - Support Request Listing
 * Last Updated: $Date: 2013-05-16 17:37:13 -0400 (Thu, 16 May 2013) $
 *
 * @author 		$Author: bfarber $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		9th February 2010
 * @version		$Revision: 12256 $
 */
 
class cp_skin_tickets
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
// Listing
//===========================================================================
function listing( $requests, $pagination, $departments, $allDepartments, $statuses, $staff, $selected, $assigned, $dots, $tracked, $sortby, $sortorder, $groupdpts, $searchedFor, $total ) {

$check = 0;
$groupdptscheck = ( $groupdpts ) ? " checked='checked'" : '';

$search = ipsRegistry::getClass('output')->formInput( 'searchterm', $searchedFor['term'] );
$searchIn = ipsRegistry::getClass('output')->formDropdown( 'searchin', array(
	array( 'title', $this->lang->words['sr_search_title'] ),
	array( 'body', $this->lang->words['sr_search_body'] ),
	array( 'email', $this->lang->words['sr_search_email'] )
	), $searchedFor['in'] ? $searchedFor['in'] : 'body' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$this->lang->words['support_requests']}</h2>
	<ul class='context_menu'>
		<li class='ipsActionButton'><a href='#' id='log-request'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['support_log']}</a></li>
		<li><a href='{$this->settings['base_url']}module=tickets&amp;section=list&amp;do=recent'><img src='{$this->settings['skin_app_url']}/images/support/recent.png' alt='' />{$this->lang->words['sr_my_recent']}</a></li>

EOF;
	if ( $assigned )
	{
		$IPBHTML .= <<<EOF
		<li><a href='{$this->settings['base_url']}module=tickets&amp;section=list&amp;do=assigned'><img src='{$this->settings['skin_app_url']}/images/support/mine.png' alt='' />{$this->lang->words['sr_mine']} ({$assigned})</a></li>
EOF;
	}
	if ( !empty( $tracked ) )
	{
		$tracking = count( $tracked );
		$IPBHTML .= <<<EOF
		<li><a href='{$this->settings['base_url']}module=tickets&amp;section=list&amp;&do=tracked'><img src='{$this->settings['skin_app_url']}/images/support/track.png' alt='' /> {$this->lang->words['sr_tracking']} ({$tracking})</a></li>
EOF;
	}

$IPBHTML .= <<<EOF
	</ul>
</div>

<div class='acp-box'>
	<form action='{$this->settings['base_url']}module=tickets&amp;section=list&amp;do={$this->request['do']}' method='post' id='mm_form'>
	<input type='hidden' name='multimod' value='0' id='multimod' />
 	<h3>{$this->lang->words['support_requests']} ({$total})</h3>
	<div>
		<table class='form_table'>
			<tr>
				<th width='4%'>&nbsp;</th>
				<th width='25%'>{$this->lang->words['sr_subject']}</th>
				<th width='17%'>{$this->lang->words['sr_customer']}</th>
				<th width='12%'>{$this->lang->words['sr_created']}</th>
				<th width='20%'>{$this->lang->words['sr_replies']}</th>
				<th width='15%'>{$this->lang->words['sr_status']}</th>
				<th width='3%'><input type='checkbox' id='checkall' onchange='checkAll()' /></th>
			</tr>
EOF;

	foreach ( $requests as $did => $_requests )
	{
		if ( $groupdpts )
		{
			$IPBHTML .= <<<EOF
				<tr>
					<th colspan='7' class='subhead'><strong>{$allDepartments[$did]['dpt_name']}</strong></th>
				</tr>
EOF;
		}
		
		if ( $groupdpts and !isset( $departments[ $did ] ) )
		{
			$button = '';
			switch ( $this->request['do'] )
			{
				case 'tracked':
					$button = "<a href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=mass_untrack&amp;department={$did}' class='mini_button'>{$this->lang->words['req_track_off']}</a>";
					break;
					
				case 'assigned':
					$button = "<a href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=mass_unassign&amp;department={$did}' class='mini_button'>{$this->lang->words['support_list_unassign']}</a>";
					break;
			}
		
			$IPBHTML .= <<<EOF
				<tr class='_red'>
					<td colspan='7'>{$this->lang->words['support_list_noperm']} {$button}</th>
				</tr>
EOF;
		}
		else
		{
			$skipped = array();
			foreach ( $_requests as $r )
			{
				if ( !isset( $departments[ $r['r_department'] ] ) )
				{
					if ( !in_array( $r['r_department'], $skipped ) )
					{
						$message = sprintf( $this->lang->words['support_list_noperm_ungrouped'], $allDepartments[ $r['r_department'] ]['dpt_name'] );
				
						$button = '';
						switch ( $this->request['do'] )
						{
							case 'tracked':
								$button = "<a href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=mass_untrack&amp;department={$r['r_department']}' class='mini_button'>{$this->lang->words['req_track_off']}</a>";
								break;
								
							case 'assigned':
								$button = "<a href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=mass_unassign&amp;department={$r['r_department']}' class='mini_button'>{$this->lang->words['support_list_unassign']}</a>";
								break;
						}
					
						$IPBHTML .= <<<EOF
							<tr class='_red'>
								<td colspan='7'>{$message} {$button}</th>
							</tr>
EOF;
						
						$skipped[] = $r['r_department'];
					}
				}
				else
				{
					$_staff = '';
					if ( $r['r_staff'] )
					{
						$owner = supportRequest::checkSupportStaff( $r['r_staff'] );
						$_staff = "<br /><span class='desctext'>{$this->lang->words['support_list_owner']}{$owner['name']}</span>";
					}
					
					$extra = '';
					if ( $r['r_staff'] == $this->memberData['member_id'] )
					{
						$extra .= "<img src='{$this->settings['skin_app_url']}/images/support/mine.png' title='{$this->lang->words['sr_mine_']}' /> ";
					}
					if ( in_array( $r['r_id'], $tracked ) )
					{
						$extra .= "<img src='{$this->settings['skin_app_url']}/images/support/track.png' title='{$this->lang->words['sr_tracking_']}' /> ";
					}
					
					$icon = 'icon';
					if ( !$r['read'] )
					{
						$icon .= '_unread';
					}
					if ( in_array( $r['r_id'], $dots ) )
					{
						$icon .= '_dot';
					}
					
					if ( $r['sev_icon'] )
					{
						$extra .= "<img src='{$this->settings['skin_app_url']}/images/severities/{$r['sev_icon']}.png' />";
					}
								
					if ( $r['r_member'] )
					{
						$customer = "<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$r['r_member']['member_id']}'>{$r['r_member']['_name']}</a>";
					}
					else
					{
						$customer = "<em>{$r['r_email']}</em>";
					}
					
					$class = $r['highlight'] ? " class='_amber'" : ( $class == 'row1' ? 'row2' : 'row1' );
					
					$department = ( $groupdpts ) ? '' : "<br /><span class='desctext'>{$allDepartments[ $r['r_department'] ]['dpt_name']}</span>";
				
					$style = ( $r['sev_color'] and $r['sev_color'] != '000000' ) ? "style='color:#{$r['sev_color']}'" : '';
										
					$r['r_title'] = ( (bool) trim( $r['r_title'] ) ) ? $r['r_title'] : $this->lang->words['no_subject'];
				
					$IPBHTML .= <<<EOF
					<tr{$class}>
						<td><img src='{$this->settings['skin_app_url']}/images/support/{$icon}.png' /></td>
						<td>{$extra} <span class='larger_text'><a href='{$this->settings['base_url']}module=tickets&amp;section=view&amp;id={$r['r_id']}' {$style}>{$r['r_title']}</a></span>{$department}</td>
						<td>{$customer}</td>
						<td>{$this->lang->getDate($r['r_started'], 'JOINED', FALSE, TRUE)}</td>
						<td>{$r['r_replies']} {$this->lang->words['sr__replies']}<br /><span class='desctext'>{$this->lang->words['support_list_last_reply']}{$this->lang->getDate($r['r_last_reply'], 'JOINED', FALSE, TRUE)}</span></td>
						<td>
							<span style='color:#{$statuses['statuses'][$r['r_status']]['status_color']}'>{$statuses['statuses'][$r['r_status']]['status_name']}</span>
							{$_staff}
						</td>
						<td><input type='checkbox' name='check[{$r['r_id']}]' id='check{$check}' /></td>
					</tr>
EOF;
					$check++;
				}
			}
		}
	}
	
	$IPBHTML .= <<<EOF
		</table>
	</div>
	<div class='acp-actionbar' style='text-align:right'>
		<select onchange="$('multimod').value = this.value; if ( this.value == 'del' && !confirm('{$this->lang->words['delete_confirm']}' ) ) { this.value = 0; return false; } $('mm_form').submit()">
			<option value='0'>{$this->lang->words['sr_set_status']}</option>
EOF;
		foreach ( $statuses['statuses'] as $s )
		{
			$IPBHTML .= <<<EOF
				<option value='{$s['status_id']}'>{$s['status_name']}</option>
EOF;
		}
		
		$IPBHTML .= <<<EOF
				<option value='0'>{$this->lang->words['sr_actions']}</option>
EOF;
		
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_delete' ) )
		{
			$IPBHTML .= <<<EOF
				<option value='del'>{$this->lang->words['sr_delete']}</option>
EOF;
		}
			$IPBHTML .= <<<EOF
				<option value='track'>{$this->lang->words['req_track_on']}</option>
				<option value='untrack'>{$this->lang->words['req_track_off']}</option>
		</select>
	</div>
	</form>
</div><br />
{$pagination}
<br />

<form action='{$this->settings['base_url']}module=tickets&amp;section=list' method='post'>
	<input type='hidden' name='resort' value='1' />
	<div class='acp-box' style='width: 48%; float: left'>
		<h3>{$this->lang->words['sr_filters']}</h3>
		<table class='ipsTable'>
			<tr>
				<td><strong class='title'>{$this->lang->words['sr_departments']}</span></td>
				<td><strong class='title'>{$this->lang->words['sr_statuses']}</span></td>
				<td><strong class='title'>{$this->lang->words['sort']}</span></td>
			</tr>
			<tr>
				<td>
					<select name='departments[]' multiple='multiple' size='8'>
EOF;
				foreach ( $departments as $d )
				{
					if ( array_key_exists( $d['dpt_id'], $allDepartments ) )
					{
						$_selected = '';
						if ( is_array( $selected['departments'] ) and in_array( $d['dpt_id'], $selected['departments'] ) )
						{
							$_selected = "selected='selected'";
						}
						$IPBHTML .= <<<EOF
							<option value='{$d['dpt_id']}' {$_selected}>{$d['dpt_name']}</option>
EOF;
					}
				}
				$IPBHTML .= <<<EOF
				</select>
				</td>
				<td>
					<select name='statuses[]' multiple='multiple' size='8'>
EOF;
					foreach ( $statuses['statuses'] as $s )
					{
						$_selected = '';
						if ( is_array( $selected['statuses'] ) and in_array( $s['status_id'], $selected['statuses'] ) )
						{
							$_selected = "selected='selected'";
						}
						$IPBHTML .= <<<EOF
							<option value='{$s['status_id']}' {$_selected}>{$s['status_name']}</option>
EOF;
					}
					
					$IPBHTML .= <<<EOF
					</select><br />
				</td>
				<td>
					<select name='sort'>
EOF;
					foreach ( array( 'r_started', 'r_last_new_reply', 'r_last_reply', 'r_last_staff_reply' ) as $k )
					{
						$checked = ( $sortby == $k ) ? " selected='selected'" : '';
						$IPBHTML .= <<<EOF
						<option value='{$k}'{$checked}>{$this->lang->words[ 'sr_sort_' . $k ]}</option>
EOF;
					}
					$IPBHTML .= <<<EOF
					</select>
					<select name='sortorder'>
EOF;
					foreach ( array( 'asc', 'desc' ) as $k )
					{
						$checked = ( $sortorder == $k ) ? " selected='selected'" : '';
						$IPBHTML .= <<<EOF
						<option value='{$k}'{$checked}>{$this->lang->words[$k]}</option>
EOF;
					}
					$IPBHTML .= <<<EOF
					</select><br /><br />
					<label for='group'><input id='group' type='checkbox' name='group'{$groupdptscheck} /> {$this->lang->words['sr_group']}</label>
				</td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['sr_update_filters']}' class='button' />
		</div>
	</div>
	
	<div class='acp-box' style='width: 48%; float:right'>
		<h3>{$this->lang->words['sr_search']}</h3>
		<table class='ipsTable'>
			<tr>
				<td style='text-align: center'>{$search} {$searchIn}</td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['sr_search']}' class='button' />
		</div>
	</div>
	
</form>
<br style='clear:both' />

<div id='log_request_popup' style='display:none'>
	<form action='{$this->settings['base_url']}&amp;module=tickets&amp;section=create' method='post'>
	<div class='acp-box'>
		<h3>{$this->lang->words['support_log']}</h3>
		<table class="ipsTable double_pad">
			<tr>
				<td class='field_title'><strong class='title'>{$this->lang->words['customer_name']}</strong></td>
				<td class='field_field'><input name='name' /></td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['support_log']}' class='realbutton' />
		</div>
	</div>
	</form>
</div>

<script type='text/javascript'>
	function checkAll()
	{
		if ( $('checkall').checked )
		{
			for ( i=0; i < {$check}; i++ )
			{
				\$('check'+i).checked = true ;
			}
		}
		else
		{
			for ( i=0; i < {$check}; i++ )
			{
				\$('check'+i).checked = false ;
			}
		}	
	}
	
	$('log-request').observe('click', function() { new ipb.Popup('log-request-popup', { type: 'pane', stem: true, attach: { target: this, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', initial: $('log_request_popup').innerHTML } ) } );
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View
//===========================================================================
function view( $request, $messages, $departments, $staff, $statuses, $severities, $pagination, $attachments, $postKey, $stockActions, $purchase, $parent, $children, $member, $tracking ) {

$messagesTotal = count( $messages );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( $this->memberData['signature'] ? "<br /><br /><br />" . $this->memberData['signature'] : '' );
$editor = $editor->show( 'reply', array( 'editorName' => 'replyEditor', 'autoSaveKey' => "nexus-admin-{$request['r_id']}" ) );

$_staff = "<span class='desctext'>{$this->lang->words['req_unassigned']}</span>";
if ( $request['r_staff'] )
{
	$owner = supportRequest::checkSupportStaff( $request['r_staff'] );
	$_staff = $owner['name'];
}

$track = !empty( $tracking ) ? $this->lang->words['req_track_off'] : $this->lang->words['req_track_on'];
$trackNotifyChecked = ( !empty( $tracking ) and $tracking['notify'] ) ? "checked='checked'" : '';

$form['track'] = ipsRegistry::getClass('output')->formCheckbox( 'track', !empty( $tracking ), 1, 'track_checkbox' );

$form['staff1'] = ipsRegistry::getClass('output')->formDropdown( '', array_merge( array( array( 0, $this->lang->words['support_unassigned'] ) ), supportRequest::getStaffList() ), $request['r_staff'], 'owner', 'onchange="updateRequest(\'owner\')" onclick="displaying = \'owner\'" style="display:none; width: 90%"' );

$form['staff2'] = ipsRegistry::getClass('output')->formDropdown( 'owner', array_merge( array( array( 0, $this->lang->words['support_unassigned'] ) ), supportRequest::getStaffList() ), ( $request['r_staff_lock'] ? $request['r_staff'] : 0 ), 'new_owner', " style='width: 100%'" );

$request['_r_started'] = $this->registry->class_localization->getDate( $request['r_started'], 'short', TRUE );
$request['_r_last_reply'] = $this->registry->class_localization->getDate( $request['r_last_reply'], 'short', FALSE, TRUE );
$request['_r_age'] = $this->registry->class_localization->getDate( $request['r_started'], 'short', FALSE, TRUE );
$request['_r_last_staff_reply'] = $this->registry->class_localization->getDate( $request['r_last_staff_reply'], 'short', FALSE, TRUE );

$member_sev_perm = ( $member['cm_no_sev'] ) ? 'user_lock' : 'user_unlock';

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<style type='text/css'>

strong.bbc				{	font-weight: bold !important; }
em.bbc 					{	font-style: italic !important; }
span.bbc_underline 		{ 	text-decoration: underline !important; }
acronym.bbc 			{ 	border-bottom: 1px dotted #000; }
span.bbc_center, div.bbc_center, p.bbc_center	{	text-align: center; display: block; }
span.bbc_left, div.bbc_left, p.bbc_left	{	text-align: left; display: block; }
span.bbc_right, div.bbc_right, p.bbc_right	{	text-align: right; display: block; }
div.bbc_indent 			{	margin-left: 50px; }
del.bbc 				{	text-decoration: line-through !important; }
.post.entry-content ul, ul.bbc 						{	list-style: disc outside; margin: 12px 0 12px 40px; }
	.post.entry-content ul,ul.bbc ul.bbc 			{	list-style-type: circle; }
		.post.entry-content ul,ul.bbc ul.bbc ul.bbc {	list-style-type: square; }
.post.entry-content ul.decimal,ul.bbcol.decimal 				{ margin: 12px 0 12px 40px; list-style-type: decimal; }
	.post.entry-content ul.lower-alpha,ul.bbcol.lower-alpha		{ margin-left: 40px; list-style-type: lower-alpha; }
	.post.entry-content ul.upper-alpha,ul.bbcol.upper-alpha		{ margin-left: 40px; list-style-type: upper-alpha; }
	.post.entry-content ul.lower-roman	,ul.bbcol.lower-roman		{ margin-left: 40px; list-style-type: lower-roman; }
	.post.entry-content ul.upper-roman,ul.bbcol.upper-roman		{ margin-left: 40px; list-style-type: upper-roman; }

hr.bbc 					{ 	display: block; border-top: 2px solid #777; }
div.bbc_spoiler 		{	 }
div.bbc_spoiler span.spoiler_title	{ 	font-weight: bold; }
div.bbc_spoiler_wrapper	{ 	border: 1px inset #777; padding: 4px; }
div.bbc_spoiler_content	{ 	 }
input.bbc_spoiler_show	{ 	width: 45px; font-size: .7em; margin: 0px; padding: 0px; }
pre.prettyprint 		{ padding: 5px; background: #f8f8f8; border: 1px solid #c9c9c9; overflow: auto; margin-left: 10px; font-size: 11px; line-height: 140%; }
img.bbc_img { cursor: pointer; }
.signature img.bbc_img { cursor: default; }
	.signature a img.bbc_img { cursor: pointer; }
	
/* LEGACY @todo remove in IPS4 */
div.blockquote {
	font-size: 12px;
	padding: 10px;
	border-left: 2px solid #989898;
	border-right: 2px solid #e5e5e5;
	border-bottom: 2px solid #e5e5e5;
	-moz-border-radius: 0 0 5px 5px;
	-webkit-border-radius: 0 0 5px 5px;
	border-radius: 0 0 5px 5px;
	background: #f7f7f7;
}

div.blockquote div.blockquote {
	margin: 0 10px 0 0;
}

div.blockquote p.citation {
	margin: 6px 10px 0 0;
}


/* Quote boxes */

p.citation {
	font-size: 12px;
	padding: 8px 10px;
	border-left: 2px solid #989898;
	/*background: #f3f3f3 */
	background: #f6f6f6;
	background: -moz-linear-gradient(top, #f6f6f6 0%, #e5e5e5 100%); /* firefox */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6f6f6), color-stop(100%,#e5e5e5)); /* webkit */
	border-top: 2px solid #e5e5e5;
	border-right: 2px solid #e5e5e5;
	-moz-border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
	font-weight: bold;
	overflow-x: auto;
}

blockquote.ipsBlockquote {
	font-size: 12px;
	padding: 10px;
	border: 2px solid #e5e5e5;
	border-left: 2px solid #989898;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	background: #f7f7f7;
	margin: 0 0;
	overflow-x: auto;
}

blockquote.ipsBlockquote blockquote.ipsBlockquote {
	margin: 0 10px 0 0;
}

blockquote.ipsBlockquote p.citation {
	margin: 6px 10px 0 0;
}

blockquote.ipsBlockquote.built {
	border-top: none;
	-moz-border-top-right-radius: 0px;
	-webkit-border-top-left-radius: 0px;
	border-top-left-radius: 0px;
	border-top-right-radius: 0px;

}

._sharedMediaBbcode {
	width: 500px;
	background: #f6f6f6;
	background: -moz-linear-gradient(top, #f6f6f6 0%, #e5e5e5 100%); /* firefox */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6f6f6), color-stop(100%,#e5e5e5)); /* webkit */
	border: 1px solid #dbdbdb;
	-moz-box-shadow: 0px 1px 3px rgba(255,255,255,1) inset, 0px 1px 1px rgba(0,0,0,0.2);
	-webkit-box-shadow: 0px 1px 3px rgba(255,255,255,1) inset, 0px 1px 1px rgba(0,0,0,0.2);
	box-shadow: 0px 1px 3px rgba(255,255,255,1) inset, 0px 1px 2px rgba(0,0,0,0.2);
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	color: #616161;
	display: inline-block;
	margin-right: 15px;
	margin-bottom: 5px;
	padding: 15px;
}

.bbcode_mediaWrap .details {
	color: #616161;
	font-size: 12px;
	line-height: 1.5;
	margin-left: 95px;
}

.bbcode_mediaWrap .details a {
	color: #616161;
	text-decoration: none;
}

.bbcode_mediaWrap .details h5, .bbcode_mediaWrap .details h5 a {
	font: 400 20px/1.3 "Helvetica Neue", Helvetica, Arial, sans-serif;
	color: #2c2c2c;
	word-wrap: break-word;
	max-width: 420px;
}

.bbcode_mediaWrap img.sharedmedia_image {
	float: left;
	position: relative;
	/*top: 10px;
	left: 10px;*/
	max-width: 80px;
}

.bbcode_mediaWrap img.sharedmedia_screenshot {
	float: left;
	position: relative;
	/*top: 10px;
	left: 10px;*/
	max-width: 80px;
}

/* Show my media label */
.cke_button_ipsmedia span.cke_label {
	display: inline !important;
}
	.stock_actions {
		margin-bottom: 10px;
		text-align: center;
		display: block;
	    margin-left: auto;
	    margin-right: auto;
	}

	.message {
		margin-bottom: 15px;
		line-height: 140%;
		border-width: 1px;
		border-style: solid;
	}
	
	.text {
		padding-top: 6px;
		padding-left: 6px;
		padding-right: 6px;
	}
	
	.header {
		margin: 0px;
		font-size: 12px;
		font-weight: bold;
		padding: 5px;
		color: #ffffff;
	}
	
	.header a {
		color: #ffffff;
		text-decoration: none;
	}
	
	.dateline {
		text-align: right;
		font-style: italic;
		font-size: 11px;
		padding: 1px;
		margin-right: 5px;
	}

	.s {
		border-color: #bbc4ce;
		background-color: #E5EAF1;
	}
	
	.s .header {
		background-color: #4b5b6e;		
	}
	
	.s .dateline {
		color: #4b5b6e;
	}
	
	.m, .e, .a {
		border-color: #bbcab1;
		background-color: #EFF6EA;
	}
	
	.m .header, .e .header, .a .header {
		background-color: #4b6e4d;
		background-repeat: repeat-x;
	}
	
	.m .dateline, .e .dateline, .a .dateline {
		color: #4b6e4d;
	}
	
	.h {
		border-color: #333333;
		background-color: #eeeeee;
	}
	
	.h .header {
		background-color: #555555;	
	}
	
	.h .dateline {
		color: #444444;
	}
	
.faux_dropdown {
	display: inline-block;
	padding-right: 20px;
	background: url( {$this->settings['skin_app_url']}/images/edit_ticket_info.png ) no-repeat right 2px;
}

.toggle, .toggle_pane > div {
	padding: 10px 10px 10px 30px;
}

.toggle:hover {
	background-color: #f1f4f6;
}

.toggle, .toggle.closed, #attachment_wrap, #attachment_wrap.closed {
	background-image: url( {$this->settings['skin_acp_url']}/images/folder_closed.png );
	background-repeat: no-repeat;
	background-position: 10px 10px;
}

	.toggle.open, #attachment_wrap.open {
		background-image: url( {$this->settings['skin_acp_url']}/images/folder_open.png );
		background-repeat: no-repeat;
		background-position: 10px 10px;
	}

#attachment_wrap {
	padding: 10px 10px 10px 30px;
	cursor: pointer;
}

#ticket_options {
	margin-top: 10px;
	margin-bottom: 10px;
}

#ticket_options li {
	padding: 5px 10px;
}

#notify_list li {
	padding: 10px;
}

</style>

<script type='text/javascript'>

	var displaying	= '';
	var actionbuttons = false;
	var current_owner = {$request['r_staff']};
	
	function tickAction( id )
	{
		selected = ipb.Cookie.get('ticketselect');
		pids = selected.split(',');
		remove = [];
		
		if( $( 'act_' + id ).checked == true )
		{
			pids.push( id );
		}
		else
		{
			remove.push( id );
		}
		pids = pids.uniq().without( remove ).join(',');
		ipb.Cookie.set('ticketselect', pids, 0);
		
		if( pids == '' && actionbuttons == true )
		{
			actionbuttons = false;
			$('action_buttons').toggle();
		}
		else if ( pids != '' && actionbuttons == false )
		{
			actionbuttons = true;
			$('action_buttons').toggle();
		}
	}
	
	function updateRequest( action )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=update-request&secure_key=" + ipb.vars['md5_hash'],
			{
				evalJSON: 'force',
				parameters:
	        	{
	        		rid: {$request['r_id']},
	        		department: $('department').value,
	        		status: $('status').value,
	        		severity: $('severity').value,
	        		owner: $('owner').value,
	        		old_owner: current_owner,
	        	},
				onSuccess: function( t )
				{
					\$('department-display').innerHTML = t.responseJSON['department'];
					\$('status-display').innerHTML = t.responseJSON['status'];
					\$('severity-display').innerHTML = t.responseJSON['severity'];
					\$('owner-display').innerHTML = t.responseJSON['owner'];
					ownerLock( t.responseJSON['locked'] );
					
					\$('owner').value = t.responseJSON['owner_id'];
					\$('new_department').value = $('department').value;
					
					if ( t.responseJSON['package_warning'] != '' )
					{
						\$('department-warning').show();
						\$('department-warning').innerHTML = t.responseJSON['package_warning'];
					}
					else
					{
						\$('department-warning').hide();
						\$('department-warning').innerHTML = '';
					}
				
					\$( action ).blur();
					\$( action ).style.display = 'none';
					\$( action + '-display').style.display = 'inline';
					
					if ( t.responseJSON['owner_warn'] )
					{
						alert( "{$this->lang->words['err_owner_change']}" );
					}
					
					current_owner = t.responseJSON['owner_id'];
				}
			});
	}
	
	function toggleOwnerLock()
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=toggle-owner-lock&secure_key=" + ipb.vars['md5_hash'],
			{
				evalJSON: 'force',
				parameters:
	        	{
	        		rid: {$request['r_id']},
	        	},
				onSuccess: function( t )
				{
					ownerLock( t.responseJSON['locked'] )
				}
			});
	}
	
	function ownerLock( owner )
	{
		if ( owner == 0 )
		{
			\$('owner-lock').innerHTML = "<a href='#' onclick='toggleOwnerLock()'><img src='{$this->settings['skin_app_url']}/images/support/unlock.png' title='{$this->lang->words['stip_owner_unlock']}' /></a>";
			\$('new_owner').value = 0;
		}
		else if ( owner == -1 )
		{
			\$('owner-lock').innerHTML = "";
			\$('new_owner').value = 0;
		}
		else
		{
			\$('owner-lock').innerHTML = "<a href='#' onclick='toggleOwnerLock()'><img src='{$this->settings['skin_app_url']}/images/support/lock.png' title='{$this->lang->words['stip_owner_lock']}' /></a>";
			\$('new_owner').value = owner;
		}
	}
	
	function toggleMemberSeverityPermissions()
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=toggle-member-severity-permissions&secure_key=" + ipb.vars['md5_hash'],
			{
				evalJSON: 'force',
				parameters:
	        	{
	        		rid: {$request['r_id']},
	        	},
				onSuccess: function( t )
				{
					severityLocks( t.responseJSON )
				}
			});
	}
		
	function severityLocks( response )
	{
		var html = '';

		if ( response['member_lock'] == 1 )
		{
			html = "<a href='#' onclick='toggleMemberSeverityPermissions()'><img src='{$this->settings['skin_app_url']}/images/support/user_lock.png' title=\"{$this->lang->words['stip_mem_user_lock']}\" /></a> ";
		}
		else
		{
			html  = "<a href='#' onclick='toggleMemberSeverityPermissions()'><img src='{$this->settings['skin_app_url']}/images/support/user_unlock.png' title=\"{$this->lang->words['stip_mem_user_unlock']}\" /></a> ";
		}

		$('severity-options').innerHTML = html;
	}
	
	function track()
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=toggle-tracking&secure_key=" + ipb.vars['md5_hash'],
			{
				evalJSON: 'force',
				parameters:
	        	{
	        		rid: {$request['r_id']},
	        	},
				onSuccess: function( t )
				{
					if ( t.responseJSON['tracking'] == 1 )
					{
						$('track-text').innerHTML = "{$this->lang->words['req_track_off']}";
						$('track_checkbox').checked = true;
						showTrackPopup();
					}
					else
					{
						$('track-text').innerHTML = "{$this->lang->words['req_track_on']}";
						$('track_checkbox').checked = false;
					}
				}
			});
	}
	
	var mouseOver = false;
	function showTrackPopup()
	{
		if ( !mouseOver && $('track-text').innerHTML == "{$this->lang->words['req_track_off']}" )
		{
			mouseOver = true;
			new ipb.Popup('track_popup_box', { type: 'balloon', stem: true, attach: { target: $('track-text'), position: 'auto' }, hideAtStart: false, w: '400px', initial: $('track-popup-box').innerHTML }, { 'afterHide': function pop() { mouseOver = false; } } );
		}
	}
	
	function trackNotify( checked )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=toggle-tracking-notify&secure_key=" + ipb.vars['md5_hash'],
		{
			parameters:
        	{
        		rid: {$request['r_id']},
        		notify: checked,
        	}
        });
	}
	
	function hiddenReply()
	{
		if ( \$('nochange').checked )
		{ 
			\$('tabtab-3').hide();
		}
		else
		{
			\$('tabtab-3').show();
		}
	}
	
	function displayOptions( action )
	{
		\$( action + '-display').style.display = 'none';
		\$( action ).style.display = 'inline';
	}
	
	function hideOptions( action )
	{
		if ( displaying != action )
		{
			\$( action + '-display').style.display = 'inline';
			\$( action ).style.display = 'none';
			displaying = '';
		}
	}
		
	function stockAction( action )
	{
		if ( $('stock-action').value )
		{
			new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=stock-action&secure_key=" + ipb.vars['md5_hash'],
				{
					evalJSON: 'force',
					parameters:
		        	{
		        		action: $('stock-action').value,
		        		r_department: {$request['r_department']}
		        	},
					onSuccess: function( t )
					{
						if ( t.responseJSON['department'] )
						{
							\$('new_department').value = t.responseJSON['department'];
						}
						
						if ( t.responseJSON['status'] )
						{						
							\$('new_status').value = t.responseJSON['status'];
						}
						
						if ( t.responseJSON['owner'] )
						{
							\$('new_owner').value = t.responseJSON['owner'];
						}
						
						if ( t.responseJSON['message'] && t.responseJSON['message'] != '' )
						{
							CKEDITOR.instances.replyEditor.setData( t.responseJSON['message'] );
						}
					}
				});
		}
	}
	
	function submitReply()
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=reply-check&secure_key=" + ipb.vars['md5_hash'],
		{
			asynchronous: false,
			evalJSON: 'force',
			parameters:
        	{
        		rid: {$request['r_id']},
        	},
			onSuccess: function( t )
			{
				if ( t.responseJSON == undefined || t.responseJSON['error'] != undefined )
				{
					alert( "{$this->lang->words['err_support_ajax']}" );
				}
				else
				{
					if ( t.responseJSON['last'] != {$request['r_last_reply']} )
					{
						if ( confirm("{$this->lang->words['support_new_reply_warn']}" ) )
						{
							$('reply-form').submit();
						}
					}
					else
					{
						$('reply-form').submit();
					}
				}
			}
		});
	}
	
	function doPopUp( e, url, popupid )
	{
		Event.stop(e);
		new ipb.Popup( popupid, { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '750px', h: '600px', ajaxURL: url, modal: true } );
	}
	
	function addNotify( e )
	{
		Event.stop(e);
		new ipb.Popup('addnotify', { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=add_notify&request_id={$request['r_id']}&secure_key=" + ipb.vars['md5_hash'], modal: false } );
	}
	
	var editTitlePopup = null;
	function titlePopup( e )
	{
		Event.stop(e);
		
		if ( editTitlePopup === null )
		{
			editTitlePopup = new ipb.Popup('edittitle', { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=edit_title&request_id={$request['r_id']}&secure_key=" + ipb.vars['md5_hash'], modal: false } );
		}
		else
		{
			editTitlePopup.show();
		}
	}
	
	function saveNotify( rand )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=save_notify&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
        	{
        		id: {$request['r_id']},
        		email: $('email-' + rand ).value,
        	},
			onSuccess: function( t )
			{
				if( t.responseJSON['error'] )
				{
					alert( "{$this->lang->words['invalid_email']}" );
				}
				else
				{
					id = t.responseJSON['id'];
				
					var row = $('notify_list').insertRow( $('notify_list').rows.length - 1 );
					row.id = "notify-" + id;
					
					var cell1 = row.insertCell(0);
					cell1.innerHTML = "<input type='checkbox' name='notify"+ id + "' checked='checked' />";
					
					var cell2 = row.insertCell(1);
					cell2.innerHTML = "<img src='{$this->settings['skin_app_url']}/images/support/e.png' />";
					
					var cell3 = row.insertCell(2);
					cell3.innerHTML = $('email-' + rand ).value;
					
					var cell4 = row.insertCell(3);
					cell4.innerHTML = "<span class='clickable' onclick='deleteNotify("+ id + ")'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></span>";
					
					new Effect.Appear( row, {duration:0.5} );
					new Effect.Fade( $('addnotify_popup'), {duration:0.5} );
				}
			}
		});
	}
	
	function deleteNotify( id )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=delete_notify&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
        	{
        		rid: {$request['r_id']},
        		n: id
        	},
			onSuccess: function( t )
			{
				new Effect.Fade( $("notify-" + id), {duration:0.5} );
			}
		});
	}
	
	function toggleMessageFormat( id, format )
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=format_toggle&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
        	{
        		id: id,
        		format: format
        	},
        	onSuccess: function( t )
			{
				if ( t.responseJSON['message'] )
				{
					$( 'reply_content_' + id ).innerHTML = t.responseJSON['message'];
					
					if ( format == 'h' )
					{
						$( 'format_toggle_' + id + '_h' ).style['font-weight'] = 'bold';
						$( 'format_toggle_' + id + '_p' ).style['font-weight'] = 'normal';
					}
					else if ( format == 'p' )
					{
						$( 'format_toggle_' + id + '_p' ).style['font-weight'] = 'bold';
						$( 'format_toggle_' + id + '_h' ).style['font-weight'] = 'normal';
					}
				}
			}
		});
	}
	
	function editTitle()
	{	
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=edit_title_save&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
        	{
        		request_id: {$request['r_id']},
        		title: $('title_edit').value
        	},
        	onSuccess: function( t )
			{
				if( t.responseJSON['error'] )
				{
					alert( "{$this->lang->words['err_no_title']}" );
				}
				else
				{
					console.log( $('title_edit').value );
				
					$('main_r_title').innerHTML = $('title_edit').value;
					editTitlePopup.hide();
				}
			}
		});
	}
	
</script>

<div id='track-popup-box' class='popupWrapper' style='display:none;'>
	<div class='popupInner'>
		<label for='track-notify-checkbox'><input type='checkbox' onchange='trackNotify( this.checked )' id='track-notify-checkbox' {$trackNotifyChecked} /> {$this->lang->words['req_track_notify']}</label>
	</div>
</div>

<div class='section_title'>
	<h2><span id='main_r_title'>{$request['r_title']}</span> [#{$request['r_id']}]</h2>
	<ul class='context_menu'>
		<li><a href='#' id='addnote'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['req_add_note']}</a></li>
		<li><a href='#' id='edittitle'><img src='{$this->settings['skin_acp_url']}/images/icons/pencil.png' alt='' /> {$this->lang->words['req_edit_title']}</a></li>
		<li><a href='#' onclick='track()' onmouseover='showTrackPopup()'><img src='{$this->settings['skin_app_url']}/images/support/track.png' alt='' /> <span id='track-text'>{$track}</span></a></li>
		<li><a href='#' id='merge'><img src='{$this->settings['skin_app_url']}/images/support/merge.png' alt='' /> {$this->lang->words['req_merge']}</a></li>
EOF;

	if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_delete' ) )
	{
		$IPBHTML .= <<<EOF
		<li><a onclick="if ( !confirm('{$this->lang->words['delete_confirm']}' ) ) { return false; }" href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=delete&amp;id={$request['r_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /> {$this->lang->words['sr_delete']}</a></li>
EOF;
	}

	$IPBHTML .= <<<EOF
		<li><a href='#' id='showviews'><img src='{$this->settings['skin_app_url']}/images/search.png' alt='' /> {$this->lang->words['sr_viewed_by']}</a></li>
	</ul>
</div>

<script type='text/javascript'>
		$('addnote').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=add_note&request_id={$request['r_id']}&secure_key=" + ipb.vars['md5_hash'],'addnote' ) );
		$('edittitle').observe('click', titlePopup.bindAsEventListener( this ) );
		$('showviews').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=show_views&request_id={$request['r_id']}&secure_key=" + ipb.vars['md5_hash'], 'views' ) );
		$('merge').observe('click', doPopUp.bindAsEventListener( this, ipb.vars['base_url'].replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=merge&request_id={$request['r_id']}&secure_key=" + ipb.vars['md5_hash'], 'views' ) );
</script>

<div class='acp-box'>
	<h3>{$this->lang->words['req_info']}</h3>
	<table class='form_table'>
		<tr>
			<th colspan='2'>{$this->lang->words['req_info']}</th>
			<th colspan='2'>{$this->lang->words['req_timing']}</th>
		</tr>
		<tr>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_customer']}</strong></td>
			<td class='row1'>
EOF;
				if ( $request['r_member'] )
				{
					$IPBHTML .= <<<EOF
					<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$request['r_member']['member_id']}'>{$request['r_member']['_name']}</a> <span class='desctext'>({$request['r_member']['member_id']} - {$request['r_member']['email']})</span>
					<span style='float:right'>
						<a href='{$this->settings['base_url']}module=tickets&amp;section=list&amp;do=member&amp;id={$request['r_member']['member_id']}'><img src='{$this->settings['skin_app_url']}/images/search.png' title="{$this->lang->words['sr_view_customer']}" /></a>
					</span>
EOF;
				}
				else
				{
					$encoded = urlencode( $request['r_email'] );
					$IPBHTML .= <<<EOF
					<em>{$request['r_email']}</em>
					<span style='float:right'>
						<a href='{$this->settings['base_url']}module=tickets&amp;section=list&amp;do=email&amp;email={$encoded}'><img src='{$this->settings['skin_app_url']}/images/search.png' title="{$this->lang->words['sr_view_customer']}" /></a>
					</span>
EOF;
				}
				$IPBHTML .= <<<EOF
			</td>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_created']}</strong></td>
			<td class='row1'>{$request['_r_started']}</td>
		</tr>
		<tr style='height: 45px'>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_department']}</strong></td>
			<td class='row1' onmouseover="displayOptions('department')" onmouseout="hideOptions('department')">
				<span id='department-display' class='faux_dropdown'>{$departments[$request['r_department']]['dpt_name']}</span>
				<select id='department' onchange="updateRequest('department')" onclick="displaying = 'department'" style='display:none; width: 90%'>
EOF;
			foreach ( $departments as $d )
			{
				$_selected = ( $d['dpt_id'] == $request['r_department'] ) ? "selected='selected'" : '';
				$IPBHTML .= <<<EOF
					<option value='{$d['dpt_id']}' {$_selected}>{$d['dpt_name']}</option>
EOF;
			}
			$IPBHTML .= <<<EOF
				</select>
			</td>
			<td class='row2'><strong class='title'>{$this->lang->words['req_age']}</strong></td>
			<td class='row1'>{$request['_r_age']}</td>
		</tr>
		<tr style='height: 45px'>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_status']}</strong></td>
			<td class='row1' onmouseover="displayOptions('status')" onmouseout="hideOptions('status')" class='acp-row-on'>
				<span id='status-display' class='faux_dropdown'><span style='color:#{$statuses['statuses'][$request['r_status']]['status_color']}'>{$statuses['statuses'][$request['r_status']]['status_name']}</span></span>
				<select id='status' onchange="updateRequest('status')" onclick="displaying = 'status'" style='display:none; width: 90%'>
EOF;
			foreach ( $statuses['statuses'] as $id => $data )
			{
				$_selected = ( $id == $request['r_status'] ) ? "selected='selected'" : '';
				$IPBHTML .= <<<EOF
					<option value='{$id}' {$_selected}>{$data['status_name']}</option>
EOF;
			}
			
			$IPBHTML .= <<<EOF
				</select>
			</td>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_sort_r_last_reply']}</strong></td>
			<td class='row1'>{$request['_r_last_reply']}</td>
		</tr>
		<tr style='height: 45px'>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_severity']}</strong></td>
			<td class='row1' onmouseover="displayOptions('severity')" onmouseout="hideOptions('severity')" class='acp-row-on'>
				<span id='severity-display' class='faux_dropdown'><span style='color:#{$severities['list'][ $request['r_severity'] ]['sev_color']}'>{$severities['list'][ $request['r_severity'] ]['sev_name']}</span></span>
				<select id='severity' onchange="updateRequest('severity')" onclick="displaying = 'severity'" style='display:none; width: 90%'>
EOF;
			foreach ( $severities['list'] as $id => $data )
			{
				$_selected = ( $id == $request['r_severity'] ) ? "selected='selected'" : '';
				$IPBHTML .= <<<EOF
					<option value='{$id}' {$_selected}>{$data['sev_name']}</option>
EOF;
			}
			
			$IPBHTML .= <<<EOF
				</select>
EOF;
			if ( $request['r_member']['member_id'] and $this->registry->getClass('class_permissions')->checkPermission( 'block_sev' ) )
			{
				$IPBHTML .= <<<EOF
				<span id='severity-options' style='float:right; height: 22px'>
					<a href='#' onclick='toggleMemberSeverityPermissions()'><img src='{$this->settings['skin_app_url']}/images/support/{$member_sev_perm}.png' title="{$this->lang->words[ 'stip_mem_' . $member_sev_perm ]}" /></a>
				</span>
EOF;
			}
			
			$IPBHTML .= <<<EOF
			</td>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_sort_r_last_staff_reply']}</strong></td>
			<td class='row1'>{$request['_r_last_staff_reply']}</td>
		</tr>
		<tr style='height: 45px'>
			<td class='row2'><strong class='title'>{$this->lang->words['sr_owner']}</strong></td>
			<td class='row1' onmouseover="displayOptions('owner')" onmouseout="hideOptions('owner')" class='acp-row-on'>
				<span id='owner-display' class='faux_dropdown'>{$_staff}</span>
				{$form['staff1']}
				<span id='owner-lock' style='float:right'>
EOF;

			if ( $request['r_staff'] )
			{
				$lock_icon = ( $request['r_staff_lock'] ) ? 'lock' : 'unlock';
				$IPBHTML .= <<<EOF
				<a href='#' onclick='toggleOwnerLock()'><img src='{$this->settings['skin_app_url']}/images/support/{$lock_icon}.png' title="{$this->lang->words[ 'stip_owner_' . $lock_icon ]}" /></a>
EOF;
			}

			$IPBHTML .= <<<EOF
				</span>
			</td>
			<td class='row2'>&nbsp;</td>
			<td class='row1'>&nbsp;</td>
		</tr>
		<tr>
			<th colspan='4'>{$this->lang->words['req_package']}</th>
		</tr>
		<tr>
			<td colspan='4' style='padding: 0'>
EOF;

if ( !$purchase['ps_id'] )
{
	$package_warning = 'none';
	if ( $departments[ $request['r_department'] ]['dpt_require_package'] )
	{
		$package_warning = '';
	}

	$IPBHTML .= <<<EOF
		<div style='padding: 10px'>
			<strong>{$this->lang->words['req_no_package']}</strong> &nbsp;&nbsp;<span class='desctext'><a href='{$this->settings['base_url']}module=tickets&amp;section=associate&amp;id={$request['r_id']}'>{$this->lang->words['req_associate']}</a></span><br />
			<span id='department-warning' style='display:{$package_warning}'><br /><div class='warning'>{$this->lang->words['req_assoc_warning']}</div></span>	
		</div>
EOF;
}
else
{
	$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $purchase['ps_app'], $purchase['ps_type'] );
	
	$package_warning = 'none';
	$package_message = '';
	if ( $departments[ $request['r_department'] ]['dpt_require_package'] )
	{	
		if ( !( $purchase['ps_app'] == 'nexus' and array_key_exists( $purchase['ps_type'], package::$types ) and in_array( $purchase['ps_item_id'], explode( ',', $departments[ $request['r_department'] ]['dpt_packages'] ) ) ) and ( !isset( $purchase['package'] ) or $purchase['package']['p_support_department'] != $request['r_department'] ) )
		{
			$package_warning = '';
			$package_message = $this->lang->words['req_bad_assoc_warning'];
		}
		elseif ( $purchase['ps_cancelled'] )
		{
			$package_warning = '';
			$package_message = $this->lang->words['purchase_canceled'];
		}
		elseif ( !$purchase['ps_active'] )
		{
			$package_warning = '';
			$package_message = $this->lang->words['purchase_expired'];
		}
	}
	
	try
	{
		$srvf = package::load( $purchase['ps_item_id'] )->supportRequestView( $purchase );
	}
	catch ( Exception $e )
	{
		$srvf = array();
	}
	$class = ( is_array( $purchase['customfields'] ) or $parent or !empty( $srvf ) ) ? "class='toggle closed'" : "style='padding: 10px 10px 10px 30px;'";
			
	$IPBHTML .= <<<EOF
	<div id='toggle_parent' {$class} style='cursor:pointer'>
		{$appIcon} <strong>{$purchase['ps_name']}</strong> &nbsp;&nbsp; 
		<span class='desctext'>
			<a href='{$this->settings['base_url']}module=tickets&amp;section=associate&amp;id={$request['r_id']}'>{$this->lang->words['req_associate_change']}</a> &nbsp;&nbsp; 
			<a href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;id={$purchase['ps_id']}'>{$this->lang->words['parent_info_full_link']}</a> &nbsp;&nbsp; 
			<a href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;do=edit&amp;id={$purchase['ps_id']}&amp;rid={$request['r_id']}'>{$this->lang->words['purchase_edit']} {$this->lang->words['_rarr']}</a>
		</span><br />
EOF;
		if ( !empty( $children ) )
		{
			$IPBHTML .= <<<EOF
		<br />
EOF;
	
			$names = array();
			foreach ( $children as $c )
			{
				if ( $c['ps_cancelled'] )
				{
					$IPBHTML .= <<<EOF
		<img src='{$this->settings['skin_app_url']}/images/support/package-cancelled.png' alt='{$this->lang->words['purchase_cancelled']}' title='{$this->lang->words['purchase_cancelled']}' />
EOF;
				}
				elseif ( !$c['ps_active'] )
				{
					$IPBHTML .= <<<EOF
		<img src='{$this->settings['skin_app_url']}/images/support/package-expired.png' alt='{$this->lang->words['purchase_expired']}' title='{$this->lang->words['purchase_expired']}' />
EOF;
				}
				else
				{
					$IPBHTML .= <<<EOF
		<img src='{$this->settings['skin_app_url']}/images/support/package-active.png' alt='{$this->lang->words['purchase_active']}' title='{$this->lang->words['purchase_active']}' />
EOF;
				}
				
				$IPBHTML .= <<<EOF
		<a href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;id={$c['ps_id']}'>{$c['ps_name']}</a> &nbsp;&nbsp;&nbsp; 
EOF;
			}
		}
		
		$IPBHTML .= <<<EOF
		<div id='department-warning' class='warning' style='display:{$package_warning}'>{$package_message}</div>
	</div>
	<div id='toggle_parent_pane' class='toggle_pane' style='display: none'>
		<div>
			<table class='form_table'>
EOF;
		foreach ( $srvf as $k => $v )
		{
			$IPBHTML .= <<<EOF
			<tr>
				<td><strong class='title'>{$k}</strong></td>
				<td class='field_field'>{$v}</td>
			</tr>
EOF;
		}
		
		if ( $purchase['lkey_key'] )
		{
			$IPBHTML .= <<<EOF
			<tr>
				<td><strong class='title'>{$this->lang->words['lkey']}</strong></td>
				<td class='field_field'>{$purchase['lkey_key']}</td>
			</tr>
EOF;
		}
		
		if ( is_array( $purchase['customfields'] ) )
		{
			foreach ( $purchase['customfields'] as $f )
			{
				$_f = customField::factory( $f );
				$_f->currentValue = $purchase['ps_custom_fields'][ $_f->id ];
				
				$content = (string) $_f;
			
				$IPBHTML .= <<<EOF
				<tr>
					<td><strong class='title'>{$f['cf_name']}</strong></td>
					<td class='field_field'>{$content}</td>
				</tr>
EOF;
	
			}
		}
		
		if ( $parent )
		{
			$parent['ps_custom_fields'] = unserialize( $parent['ps_custom_fields'] );
			$parentLink = ( empty( $parent['ps_custom_fields'] ) ) ? "href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;id={$parent['ps_id']}'" : "onclick=\"$('parentInfo').toggle()\"";
		
			$IPBHTML .= <<<EOF
			<tr>
				<td><strong class='title'>{$this->lang->words['req_package_parent']}</strong></td>
				<td class='field_field'><a style='cursor:pointer' {$parentLink}>{$parent['ps_name']}</a></td>
			</tr>
		</table>
		<table class='form_table' id='parentInfo' style='margin-left: 25px; display:none'>
EOF;

			if ( is_array( $parent['customfields'] ) )
			{
				foreach ( $parent['customfields'] as $f )
				{
					$_f = customField::factory( $f );
					$_f->currentValue = $parent['ps_custom_fields'][ $_f->id ];
					
					$content = (string) $_f;
				
					$IPBHTML .= <<<EOF
				<tr>
					<td><strong class='title'>{$f['cf_name']}</strong></td>
					<td class='field_field'>{$content}</td>
				</tr>
EOF;
				}
			}

			$IPBHTML .= <<<EOF
			<tr>
				<td colspan='2'><a href='{$this->settings['base_url']}module=payments&amp;section=purchases&amp;id={$parent['ps_id']}'>{$this->lang->words['parent_info_full_link']}</td>
			</tr>
		</table>
EOF;
		}
		else
		{
$IPBHTML .= <<<EOF
		</table>
EOF;
		}
			
}
	
	$IPBHTML .= <<<EOF
	
			</td>
		</tr>
EOF;

	if ( !empty( $request['r_cfields'] ) )
	{
		$IPBHTML .= <<<EOF
		<tr>
			<th colspan='4'>
				<div class='left' style='margin-top: 5px'>
					{$this->lang->words['scfields']}
				</div>
				<a class='button right' style='font-weight: normal; text-shadow: none' href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=cfields&amp;id={$request['r_id']}'>Edit</a>
			</th>
		</tr>
EOF;
		$i = 0;
		foreach ( $request['r_cfields'] as $field )
		{
			$i++;
			if ( $i % 2 == 0 )
			{
				$IPBHTML .= <<<EOF
			<td class='row2'><strong class='title'>{$field->name}</strong></td>
			<td class='row1 field_field'>{$field}</td>
		</tr>		
EOF;
			}
			else
			{
				$IPBHTML .= <<<EOF
		<tr>
			<td class='row2'><strong class='title'>{$field->name}</strong></td>
			<td class='row1 field_field'>{$field}</td>
EOF;
			}
		
		}
		
		if ( count( $request['r_cfields'] ) % 2 !== 0 )
		{
				$IPBHTML .= <<<EOF
			<td class='row2'>&nbsp;</td>
			<td class='row1 field_field'>&nbsp;</td>
		</tr>		
EOF;
		}
	}

	$IPBHTML .= <<<EOF
	</table>
</div>
<br style='clear: both' />
<br />

<script type='text/javascript'>
	ipb.delegate.register('.toggle', function( e, elem ){
		var pane = $( $( elem ).id + '_pane' );
		if( !pane ){ return; }
		
		if( $( pane ).visible() )
		{
			$( elem ).removeClassName('open').addClassName('closed');
			new Effect.BlindUp( $( pane ), { duration: 0.4 } );
		}
		else
		{
			$( elem ).removeClassName('closed').addClassName('open');
			new Effect.BlindDown( $( pane ), { duration: 0.4 } );
		}
	});
</script>

{$pagination}
<div class='acp-box'>
	<h3>{$this->lang->words['req_responses']}</h3>
	<div id='ticket_replies' class='acp-row-off'>
EOF;

$k = 0;
$l = 0;
foreach ( $messages as $message )
{
	$msg = $this->lang->words[ 'req_response_type_' . $message['reply_type'] ];
	if ( $message['reply_cc'] )
	{
		$msg = '(' . str_replace( array( '(', ')' ), '', $msg ) . ' - CC: ' . $message['reply_cc'] . ')';
	}
		
	if ( $message['reply_type'] == 's' or $message['reply_type'] == 'h' )
	{
		$name = $message['reply_member']['members_display_name'];
	}
	elseif ( $message['reply_type'] == 'e' and !$message['reply_member']['member_id'] )
	{
		$name = $message['reply_email'];
	}
	else
	{
		$name = <<<EOF
		<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$message['reply_member']['member_id']}'>{$message['reply_member']['_name']}</a>
EOF;
	}

	$ipaddress	= '';

	if( $message['reply_ip_address'] )
	{
		$ipaddress	= "{$this->lang->words['ip_prefix_ticket']} <a href='{$this->settings['_base_url']}&amp;app=members&amp;module=members&amp;section=tools&amp;do=learn_ip&amp;ip={$message['reply_ip_address']}'>{$message['reply_ip_address']}</a>";
	}
	
	$toggle = '';
	if ( $message['reply_textformat'] == 'h' )
	{
		$toggle = "<span class='ipsType_smaller clickable' onclick='toggleMessageFormat( {$message['reply_id']}, \"h\" )' id='format_toggle_{$message['reply_id']}_h' style='font-weight:bold'>{$this->lang->words['support_format_html']}</span> &middot; <span class='ipsType_smaller clickable' onclick='toggleMessageFormat( {$message['reply_id']}, \"p\" )' id='format_toggle_{$message['reply_id']}_p'>{$this->lang->words['support_format_plain']}</span> &nbsp; ";
	}
	elseif ( $message['reply_textformat'] == 'p' )
	{
		$toggle = "<span class='ipsType_smaller clickable' onclick='toggleMessageFormat( {$message['reply_id']}, \"h\" )' id='format_toggle_{$message['reply_id']}_h'>{$this->lang->words['support_format_html']}</span> &middot; <span class='ipsType_smaller clickable' onclick='toggleMessageFormat( {$message['reply_id']}, \"p\" )' id='format_toggle_{$message['reply_id']}_p' style='font-weight:bold'>{$this->lang->words['support_format_plain']}</span> &nbsp; ";
	}
	
$IPBHTML .= <<<EOF
		<div id='reply_{$message['reply_id']}' class='post_block post entry-content clear type_{$message['reply_type']}'>
			<div class='post_wrap'>
				<div class='bar'>
					<span style='font-weight:bold'>{$name}</span> <span class='type'>{$msg}</span>
					<span style='float: right'>
						{$toggle}
EOF;

	if ( $l )
	{	
			
$IPBHTML .= <<<EOF
	<input type='checkbox' name='act_{$message['reply_id']}' id='act_{$message['reply_id']}' onclick='tickAction( {$message['reply_id']} )' />	
EOF;
		
	}

$IPBHTML .= <<<EOF

					</span>
				</div>
				<div class='author_info'>
					<img src='{$message['reply_member']['pp_small_photo']}' width="50" height="50" class='photo' />
				</div>
				<div class='post_body clearfix'>
					<p class='posted_info desctext'>
EOF;

if( $ipaddress )
{
	$IPBHTML .= "<span style='float:right;'>{$ipaddress}</span>";
}

$IPBHTML .= <<<EOF
{$message['reply_date']}</p>
					<div class='post' id='reply_content_{$message['reply_id']}'>
						{$message['reply_post']}
					</div>
EOF;
				if ( $this->settings['nexus_support_satisfaction'] and $message['reply_type'] == 's' and $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings' ) )
				{	
					$IPBHTML .= <<<EOF
						<div class='post desctext right'>
EOF;
					if ( $message['rating_rating'] )
					{
						if ( $message['rating_note'] and $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings_feedback' ) )
						{
						$IPBHTML .= <<<EOF
							<div id='rating_feedback_{$message['reply_id']}' class='popupWrapper' style='display:none'>
								<div class='popupInner'>
									{$message['rating_note']}
								</div>
							</div>
							<a class='clickable' onclick="new ipb.Popup('feedback_box_{$message['replyid']}', { type: 'balloon', stem: true, attach: { target: this, position: 'auto' }, hideAtStart: false, w: '650px', initial: $('rating_feedback_{$message['reply_id']}').innerHTML } );">{$this->lang->words['support_rating']}</a>
EOF;
						}
						else
						{
							$IPBHTML .= $this->lang->words['support_rating'];
						}
					
						for ( $i=1; $i < 6; $i++ )
						{
							if ( $message['rating_rating'] >= $i )
							{
								$IPBHTML .= <<<EOF
									<img src='{$this->settings['skin_app_url']}/images/support/star.png' alt='' />
EOF;
							}
							else
							{
								$IPBHTML .= <<<EOF
									<img src='{$this->settings['skin_app_url']}/images/support/star-empty.png' alt='' />
EOF;
							}
						}
					}
					else
					{
						$IPBHTML .= $this->lang->words['support_rating_none'];
					}
					
						$IPBHTML .= <<<EOF
						</div>
EOF;
				}
				$IPBHTML .= <<<EOF
				</div>
			</div>
		</div>
EOF;
				$l++;
}

$IPBHTML .= <<<EOF
	</div>
	<div class='acp-actionbar' id='action_buttons' style='text-align:right'>
		<a class='button' href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=split&amp;id={$request['r_id']}' target='_blank' onclick='setTimeout("window.location.reload()",1250);'><img src='{$this->settings['skin_app_url']}/images/support/split.png' alt='' /> {$this->lang->words['req_act_split']}</a>
		&nbsp;
		<a class='button redbutton' href='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=delete_replies&amp;id={$request['r_id']}'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /> {$this->lang->words['req_act_delete']}</a></li>
	</div>
</div>
<br />
{$pagination}
<br />

EOF;

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}module=tickets&amp;section=reply' method='post' id='reply-form'>
<input type='hidden' name='id' value='{$request['r_id']}' />
<input type='hidden' name='reload' id='reload' value='0' />
<input type='hidden' name='post_key' value='{$postKey}' />

<div class='acp-box'>
	<h3>{$this->lang->words['req_reply']}</h3>
	
	<table class='form_table'>
		<tr>
			<th style='width: 74%'>{$this->lang->words['req_reply_message']}</th>
			<th style='width: 26%'>{$this->lang->words['req_reply_options']}</th>
		</tr>
		<tr>
			<td rowspan='2'>
				{$editor}
				<span id='attachment_wrap'> <strong>{$this->lang->words['req_reply_attachments']}</strong></span>
				<div id='attachment_editor' style='display: none'>
					{$attachments}
				</div>
			</td>
			<td class='row2' style='vertical-align: top; padding: 0px'>
				<table width='100%'>
					<tr>
						<td style='vertical-align: top; padding: 0px'>
EOF;
					if ( !empty( $stockActions ) )
					{
							$IPBHTML .= <<<EOF
							<div style='padding: 10px; background: #dbe4ee;'>
								<select id='stock-action' style='width: 100%; font-weight: bold;' onchange='stockAction()'>
									<option value='0'>{$this->lang->words['req_stockactions']}</option>
EOF;
								foreach ( $stockActions as $id => $name )
								{
										$IPBHTML .= <<<EOF
									<option value='{$id}'>{$name}</option>
EOF;
								}
			
								$IPBHTML .= <<<EOF
								</select>
							</div>
EOF;
				
					}
			
							$IPBHTML .= <<<EOF
							<ul id='ticket_options'>
								<li>
									{$this->lang->words['sr_department']}<br />
										<select name='department' style='width: 100%' id='new_department'>
EOF;
									foreach ( $departments as $d )
									{
										$_selected = ( $d['dpt_id'] == $request['r_department'] ) ? "selected='selected'" : '';
										$IPBHTML .= <<<EOF
											<option value='{$d['dpt_id']}' {$_selected}>{$d['dpt_name']}</option>
EOF;
									}
									
										$IPBHTML .= <<<EOF
										</select>
								</li>
								<li>
									{$this->lang->words['sr_status']}<br />
										<select name='status' style='width: 100%' id='new_status'>
EOF;
									foreach ( $statuses['statuses'] as $id => $data )
									{
										$_selected = ( $id == $statuses['defaults']['staff'] ) ? "selected='selected'" : '';
										$IPBHTML .= <<<EOF
											<option value='{$id}' {$_selected}>{$data['status_name']}</option>
EOF;
									}

									$IPBHTML .= <<<EOF
										</select>
								</li>
								<li>
									{$this->lang->words['sr_owner']}<br />
									{$form['staff2']}
								</li>
								<li>
									{$form['track']} {$this->lang->words['req_track']}
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th>{$this->lang->words['req_notifications']}</th>
					</tr>
					<tr>
						<td style='vertical-align: top; padding: 0px'>
							<table id='notify_list' class='ipsTable'>
EOF;
		if( $request['r_notify'] )
		{
			$notifies = unserialize( $request['r_notify'] );
			if ( !empty( $notifies ) )
			{
				foreach ( $notifies as $k => $n )
				{
					$protected = false;
					if ( $n['type'] == 'm' )
					{
						$member = customer::load( $n['value'] )->data;

						$content = "<a href='{$this->settings['base_url']}module=customers&amp;section=view&amp;id={$member['member_id']}'>{$member['_name']}</a>";

						if ( $member['member_id'] == $request['r_member']['member_id'] )
						{
							$protected = true;
						}
					}
					else
					{
						$content = $n['value'];
					}

					$IPBHTML .= <<<EOF
								<tr id='notify-{$k}'>
									<td style='width: 10px'><input type='checkbox' name='notify{$k}' checked='checked' /></td>
									<td style='width: 16px'><img src='{$this->settings['skin_app_url']}/images/support/{$n['type']}.png' /></td>
									<td>{$content}</td>
									<td style='width: 16px'>
EOF;
						if ( !$protected )
						{
							$IPBHTML .= <<<EOF
									<span class='clickable' onclick='deleteNotify({$k})'><img src='{$this->settings['skin_acp_url']}/images/icons/delete.png' alt='' /></span>
EOF;
						}
						$IPBHTML .= <<<EOF
									</td>
								</tr>
EOF;
				}
			}
		}
		
		$IPBHTML .= <<<EOF
								<tr>
									<td colspan='4'>
										<a href='#' id='addnotify' class='button' style='font-size: 11px'><strong><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' /> {$this->lang->words['req_notify_add']}</strong></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<script type='text/javascript'>
					$('addnotify').observe('click', addNotify.bindAsEventListener( this ) );
				</script>
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='button' value='{$this->lang->words['req_submit_1']}' class='realbutton' onclick="submitReply();"> 
		<input type='button' value='{$this->lang->words['req_submit_2']}' class='realbutton' onclick="$('reload').value = 1; submitReply(); $('reload').value = 0;">
	</div>
</div>
</form>
<br />

<script type='text/javascript'>
	$('attachment_wrap').observe('click', function(e){
		if( $('attachment_editor').visible() )
		{
			$('attachment_wrap').addClassName('closed').removeClassName('open');
			new Effect.BlindUp( $('attachment_editor'), { duration: 0.3 } );
		}
		else
		{
			$('attachment_wrap').addClassName('open').removeClassName('closed');
			new Effect.BlindDown( $('attachment_editor'), { duration: 0.3 } );
		}
	});
	
	if ( ipb.Cookie.get('ticketselectid') != {$request['r_id']} )
	{
		ipb.Cookie.set('ticketselectid', '{$request['r_id']}');
		ipb.Cookie.set('ticketselect', '');
	}
	
	selected = ipb.Cookie.get('ticketselect');
	pids = selected.split(',');
	for ( i in pids )
	{
		if ( ( i.toString().search(/^-?[0-9]+$/) == 0 ) && $( 'act_' + pids[i] ) != null )
		{
			$( 'act_' + pids[i] ).checked = true;
		}
	}
	
	if( selected == '' )
	{
		actionbuttons = false;
		$('action_buttons').style.display = 'none';
	}
	else
	{
		actionbuttons = true;
	}
		
</script>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Note
//===========================================================================

function addNote( $requestID, $editorValue='', $postKey=NULL, $attachments=NULL ) {

$uniqid = md5( uniqid() );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$editor->setContent( $editorValue );
$editor = $editor->show( 'note', array( 'editorName' => 'noteEditor-' . $uniqid, 'autoSaveKey' => "nexus-admin-{$requestID}-note", 'type' => "mini" ) );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}module=tickets&amp;section=reply&amp;do=note&amp;id={$requestID}' method='post' id='addnote_form-{$uniqid}'>
<input type='hidden' name='fulloptions' id='fulloptions' value='0' />
<input type='hidden' name='post_key' value='{$postKey}' />
<div class='acp-box'>
	<h3>{$this->lang->words['req_add_note']}</h3>
	<table class="form_table double_pad">
		<tr>
			<td>
				{$editor}
EOF;
			if ( $attachments !== NULL )
			{
				$IPBHTML .= <<<EOF
				<div id='attachment_editor'>
					{$attachments}
				</div>
EOF;
			}
			
			$IPBHTML .= <<<EOF
			<br />
			<div style='text-align: center'><em>{$this->lang->words['req_note_desc']}</em></div>
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='button' value='{$this->lang->words['save']}' class='realbutton' onclick="$('fulloptions').value = 0; $('addnote_form-{$uniqid}').submit();" />
EOF;
	if ( $attachments === NULL )
	{
		$IPBHTML .= <<<EOF
		<input type='button' value='{$this->lang->words['req_note_attach']}' class='realbutton' onclick="$('fulloptions').value = 1; $('addnote_form-{$uniqid}').submit();" />
EOF;
	}
	$IPBHTML .= <<<EOF
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Add Note
//===========================================================================

function merge( $requestID ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<form action='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=merge' method='post' id='addnote_form'>
<input type='hidden' name='id' value='{$requestID}' />
<div class='acp-box'>
	<h3>{$this->lang->words['req_merge']}</h3>
	<table class="form_table double_pad">
		<tr>
			<td class='field_title'>
				<strong class='title'>{$this->lang->words['req_merge_id']}</strong>
			</td>
			<td class='field_field'>
				{$this->registry->output->formSimpleInput( 'id_2' )}
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' value='{$this->lang->words['req_merge']}' class='realbutton' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Edit Title
//===========================================================================

function editTitle( $title ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['req_edit_title']}</h3>
	<table class="ipsTable">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['support_title']}</strong></td>
			<td class='field_field'><input id='title_edit' size='30' class='input_text' value='{$title}' /></td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='button' value='{$this->lang->words['save']}' class='realbutton' onclick='editTitle()' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Add Notify
//===========================================================================

function addNotify( $requestID ) {

$rand = uniqid();

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<div class='acp-box'>
	<h3>{$this->lang->words['req_notify_add']}</h3>
	<table class="ipsTable">
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['email']}</strong></td>
			<td class='field_field'><input id='email-{$rand}' size='30' class='input_text'  /></td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='button' value='{$this->lang->words['save']}' class='realbutton' onclick='saveNotify("{$rand}")' />
	</div>
</div>
</form>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Log New Request
//===========================================================================

function create( $member, $departments, $statuses, $severities, $staff, $defaults, $stockActions, $postKey, $attachments ) {

$form['title'] = ipsRegistry::getClass('output')->formInput( 'title', ( $this->request['revw'] ? $this->lang->words['transaction_review_sr'] : '' ) );

$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
$editor = new $classToLoad();
$form['content'] = $editor->show( 'content', array( 'editorName' => 'replyEditor', 'autoSaveKey' => "nexus-admin-n{$member['member_id']}" ) );

$form['department'] = ipsRegistry::getClass('output')->formDropdown( 'department', $departments, 0, 'new_department' );
$form['status'] = ipsRegistry::getClass('output')->formDropdown( 'status', $statuses, $defaults['member'], 'new_status' );
$form['severity'] = ipsRegistry::getClass('output')->formDropdown( 'severity', $severities['list'], $severities['default'] );
$form['owner'] = ipsRegistry::getClass('output')->formDropdown( 'owner', $staff, 0, 'new_owner' );
$form['notify'] = ipsRegistry::getClass('output')->formYesNo( 'notify', 1 );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$this->lang->words['support_log']}</h2>
</div>

EOF;

if ( $this->request['revw'] )
{
	$message = urlencode( $this->lang->words['transaction_saved'] );
	
	$link = $this->request['v'] ? "{$this->settings['base_url']}module=payments&amp;section=transactions&amp;do=view&amp;id={$this->request['revw']}&amp;messageinabottleacp={$message}" : "{$this->settings['base_url']}module=payments&amp;section=transactions&amp;pend=1&amp;messageinabottleacp={$message}";

	$IPBHTML .= <<<EOF

<div class='redirector'>
	<div class='info'><a href='{$link}'>{$this->lang->words['transaction_review_skip']}</a></div>	
</div>
<br /><br />
EOF;

}

$IPBHTML .= <<<EOF

<form action='{$this->settings['base_url']}module=tickets&amp;section=create&amp;do=save' method='post'>
<input type='hidden' name='revw' value='{$this->request['revw']}' />
<input type='hidden' name='v' value='{$this->request['v']}' />
<input type='hidden' name='member' value='{$member['member_id']}' />
<input type='hidden' name='post_key' value='{$postKey}' />
<div class='acp-box'>
	<h3>{$this->lang->words['support_log']}</h3>
	<table class="ipsTable double_pad">
EOF;
		if ( !empty( $stockActions ) )
		{
			$IPBHTML .= <<<EOF
		<tr>
			<td class='field_title' style='background: #dbe4ee'><strong class='title'>{$this->lang->words['stockactions']}</strong></td>
			<td class='field_field'  style='background: #dbe4ee'>
				<select id='stock-action' onchange='stockAction()'>
					<option value='0'>{$this->lang->words['req_stockactions']}</option>
EOF;
				foreach ( $stockActions as $id => $name )
				{
					$selected = ( $this->request['revw'] and $this->settings['nexus_revw_sa'] == $id ) ? "selected='selected'" : '';
				
						$IPBHTML .= <<<EOF
					<option value='{$id}' {$selected}>{$name}</option>
EOF;
				}

				$IPBHTML .= <<<EOF
				</select>
			</td>
		</tr>
EOF;
	
		}
		
		$IPBHTML .= <<<EOF
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sr_subject']}</strong></td>
			<td class='field_field'>{$form['title']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['req_reply_message']}</strong></td>
			<td class='field_field'>
				{$form['content']}
				{$attachments}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sr_department']}</strong></td>
			<td class='field_field'>{$form['department']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sr_status']}</strong></td>
			<td class='field_field'>{$form['status']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sr_severity']}</strong></td>
			<td class='field_field'>{$form['severity']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['sr_owner']}</strong></td>
			<td class='field_field'>{$form['owner']}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->lang->words['req_log_notify']}</strong></td>
			<td class='field_field'>{$form['notify']}</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->lang->words['support_log']}' class='realbutton'>
	</div>
</div>
</form>

<script type='text/javascript'>
	function stockAction( action )
	{
		if ( $('stock-action').value )
		{
			new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=support&do=stock-action&secure_key=" + ipb.vars['md5_hash'],
				{
					evalJSON: 'force',
					parameters:
		        	{
		        		action: $('stock-action').value,
		        		r_department: \$F('new_department')
		        	},
					onSuccess: function( t )
					{
						if ( t.responseJSON['department'] )
						{
							\$('new_department').value = t.responseJSON['department'];
						}
						
						if ( t.responseJSON['status'] )
						{						
							\$('new_status').value = t.responseJSON['status'];
						}
						
						if ( t.responseJSON['owner'] )
						{
							\$('new_owner').value = t.responseJSON['owner'];
						}
						
						if ( t.responseJSON['message'] && t.responseJSON['message'] != '' )
						{
							CKEDITOR.instances.replyEditor.setData( t.responseJSON['message'] );
						}
					}
				});
		}
	}
	
EOF;

if ( $this->request['revw'] and $this->settings['nexus_revw_sa'] )
{
	$IPBHTML .= <<<EOF
	Event.observe(window, 'load', function() {
		stockAction( {$this->settings['nexus_revw_sa']} );
		});
EOF;
}

$IPBHTML .= <<<EOF
	
</script>

EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Associate
//===========================================================================
function associate( $request, $purchases, $parentMap ) {

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->lang->words['req_assoc']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['req_assoc_select']}</h3>
	<table class='form_table'>
		<tr>
			<th>&nbsp;</th>
			<th>{$this->lang->words['purchases_id']}</th>
			<th>{$this->lang->words['purchase_item']}</th>
			<th>{$this->lang->words['purchases_purchased']}</th>
			<th>{$this->lang->words['purchases_expires']}</th>
		</tr>
HTML;

	if ( is_array(  $parentMap[0]) )
	{
		foreach ( $parentMap[0] as $item )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $item ], $parentMap, $purchases, $request );
		}
		$IPBHTML .= <<<HTML
		<tr>
			<td colspan='4' style='text-align:center'><em><a href='{$this->settings['base_url']}module=tickets&amp;section=associate&amp;do=save&amp;id={$request['r_id']}&amp;package=0'>or click here to remove assocation</a></em></td>
		</tr>
HTML;
	}
	else
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td colspan='4' style='text-align:center'><em>{$this->lang->words['packages_empty']}</em></td>
		</tr>
HTML;
	}

$IPBHTML .= <<<HTML
	</table>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

function _generatePurchaseRow( $item, $parentMap, $purchases, $request )
{
	$padding = '';
	$parent = $item['ps_parent'];
	while ( $parent != 0 )
	{
		$padding .= "<img src='{$this->settings['skin_app_url']}/images/tree.gif' /> ";
		$parent = $purchases[ $parent ]['ps_parent'];
	}

	$appIcon = ipsRegistry::getAppClass( 'nexus' )->getItemImage( $item['ps_app'], $item['ps_type'] );
		
	$this->class = ( $this->class == 'row1' ) ? 'row2' : 'row1';
	$note = '';
	if ( !$item['associable'] )
	{
		$note = $this->lang->words['req_bad_assoc_warning'];
		$this->class = '_red';
	}
	if ( $item['ps_cancelled'] )
	{
		$note = $this->lang->words['purchase_canceled'];
		$this->class = '_red';
	}
	elseif ( !$item['ps_active'] )
	{
		$note = $this->lang->words['purchase_expired'];
		$this->class = '_amber';
	}
	
	$style = ( $item['ps_id'] == $request['r_purchase'] ) ? "style='font-weight: bold'" : '';
	
	$IPBHTML .= <<<HTML
		<tr class='{$this->class}' {$style}>
			<td>{$appIcon}</td>
			<td>{$item['ps_id']}</td>
			<td>{$padding}<a href='{$this->settings['base_url']}module=tickets&amp;section=associate&amp;do=save&amp;id={$request['r_id']}&amp;package={$item['ps_id']}'>{$item['ps_name']}</a><br /><em>{$note}</em></td>
			<td>{$item['ps_start']}</td>
			<td>{$item['ps_expire']}</td>
HTML;

	if ( is_array( $parentMap[ $item['ps_id'] ] ) )
	{
		foreach ( $parentMap[ $item['ps_id'] ] as $child )
		{
			$IPBHTML .= $this->_generatePurchaseRow( $purchases[ $child ], $parentMap, $purchases, $request );
		}
	}

	return $IPBHTML;

}

//===========================================================================
// Show views
//===========================================================================

function showViews( $views ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF
<style text='text/javascript'>
table
{
    border: 0;
}

table th
{
    background: #B1C2D4 url(../../public/style_images/master/gradient_bg.png) repeat-x;
    font-weight: bold;
    padding: 5px;
}

table td
{
    padding: 5px;
}
</style>
<div class='acp-box'>
	<h3>{$this->lang->words['sr_viewed_by']}</h3>
	<table class="ipsTable">
		<tr>
			<th>{$this->lang->words['srvb_member']}</th>
			<th>{$this->lang->words['srvb_first']}</th>
			<th>{$this->lang->words['srvb_last']}</th>
			<th>{$this->lang->words['srvb_reply']}</th>
		</tr>
EOF;
	foreach ( $views as $v )
	{
		$IPBHTML .= <<<EOF
		<tr>
			<td>{$v['view_member']['members_display_name']}</td>
			<td>{$this->lang->getDate( $v['view_first'], 'JOINED' )}</td>
			<td>{$this->lang->getDate( $v['view_last'], 'JOINED' )}</td>
			<td>{$this->lang->getDate( $v['view_reply'], 'JOINED' )}</td>
		</tr>
EOF;
	}
	
	$IPBHTML .= <<<EOF
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}


//===========================================================================
// Edit custom fields
//===========================================================================

function cfields( $request, $fields, $errors ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<EOF

<div class='section_title'>
	<h2>{$request->title} [#{$request->id}]</h2>
</div>

<div class='acp-box'>
	<h3>{$this->lang->words['scfields']}</h3>
	<form action='{$this->settings['base_url']}module=tickets&amp;section=act&amp;do=cfields_save&amp;id={$request->id}' method='post'>
		<table class="ipsTable">
EOF;
	foreach ( $fields as $f )
	{
		$error = in_array( $f->id, $errors ) ? "<br /><div class='warning'>{$this->lang->words['scfield_is_required']}</span>" : '';
	
		$IPBHTML .= <<<EOF
			<tr>
				<td class='field_title'><strong class='title'>{$f->name}</strong></td>
				<td class='field_field'>
					{$f->edit( TRUE )}
					{$error}
				</td>
			</tr>
EOF;
	}
$IPBHTML .= <<<EOF
		</table>
		<div class='acp-actionbar'>
			<input type='submit' class='button' value='{$this->lang->words['save']}' />
		</div>
	</form>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}



}