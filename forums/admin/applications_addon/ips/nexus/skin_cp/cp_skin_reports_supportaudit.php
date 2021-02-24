<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Nexus ACP Skin - Support Staff Audit
 * Last Updated: $Date: 2011-05-25 10:30:28 -0400 (Wed, 25 May 2011) $
 * </pre>
 *
 * @author 		$Author: ips_terabyte $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		11th April 2010
 * @version		$Revision: 8887 $
 */
 
class cp_skin_reports_supportaudit
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
// Splash
//===========================================================================
function splash() {

if( !$this->registry->getClass('class_permissions')->checkPermission( 'report_supportstaffself' ) )
{
	$form['staff'] = ipsRegistry::getClass('output')->formDropDown( 'staff', array( array( $this->memberData['member_id'], $this->memberData['members_display_name'] ) ), '' );
}
else
{
	$form['staff'] = ipsRegistry::getClass('output')->formDropDown( 'staff', supportRequest::getStaffList(), '' );
}
$form['days'] = ipsRegistry::getClass('output')->formSimpleInput( 'days', '5' );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words['supportaudit_title']}</h2>
</div>

<div class='acp-box'>
	<h3>{$this->registry->getClass('class_localization')->words['supportaudit_title']}</h3>
	<form action='{$this->settings['base_url']}&amp;module=reports&amp;section=supportaudit&amp;do=report' method='post'>
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['supportaudit_staff']}</strong></td>
			<td class='field_field'>
				{$form['staff']}
			</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>{$this->registry->getClass('class_localization')->words['supportaudit_days']}</strong></td>
			<td class='field_field'>
				{$form['days']}
			</td>
		</tr>
	</table>
	<div class="acp-actionbar">
		<input type='submit' value='{$this->registry->getClass('class_localization')->words['supportaudit_go']}' class='realbutton'>
	</div>
	</form>
</div>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// Report
//===========================================================================
function report( $staff, $rows, $columns, $results ) {

$title = sprintf( $this->registry->getClass('class_localization')->words['supportaudit_title_staff'], $staff['members_display_name'] );

$totals = array();

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
 	<h3>{$title}</h3>
	<table class='ipsTable'>
		<tr>
			<th width='20%'>&nbsp;</th>
HTML;
foreach ( $columns as $k => $v )
{
	$IPBHTML .= <<<HTML
			<th>{$v}</th>
HTML;
}

$IPBHTML .= <<<HTML
			<th>{$this->registry->getClass('class_localization')->words['supportaudit_total']}</th>
HTML;
		if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings', 'nexus', 'tickets' ) )
		{
			$IPBHTML .= <<<HTML
			<th>{$this->registry->getClass('class_localization')->words['supportaudit_rating']}</th>
HTML;
		}
		$IPBHTML .= <<<HTML
		</tr>
HTML;

foreach ( $rows as $dateSystem => $dateHuman )
{
	$totals[ $dateSystem ] = 0;
	$ratings[ $dateSystem ] = array();

	$IPBHTML .= <<<HTML
		<tr>
			<td>{$dateHuman}</td>
HTML;

foreach ( $columns as $hourSystem => $hourHuman )
{
	if ( isset( $results[ $dateSystem ][ $hourSystem ] ) )
	{
		$totals[ $dateSystem ] += $results[ $dateSystem ][ $hourSystem ]['count'];
		
		if ( $results[ $dateSystem ][ $hourSystem ]['rating'] )
		{
			$ratings[ $dateSystem ][] = $results[ $dateSystem ][ $hourSystem ]['rating'];
		}
		
		$IPBHTML .= <<<HTML
			<td><a href='{$this->settings['base_url']}&amp;module=reports&amp;section=supportaudit&amp;do=locate&amp;staff={$staff['member_id']}&amp;date={$dateSystem}&amp;time={$hourSystem}'>{$results[ $dateSystem ][ $hourSystem ]['count']}</a></td>
HTML;
	}
	else
	{
		$IPBHTML .= <<<HTML
			<td>&nbsp;</td>
HTML;
	}
}

	if ( $totals[ $dateSystem ] > 0 )
	{
		$IPBHTML .= <<<HTML
			<td><span class='larger_text'><a href='{$this->settings['base_url']}&amp;module=reports&amp;section=supportaudit&amp;do=locate&amp;staff={$staff['member_id']}&amp;date={$dateSystem}'>{$totals[ $dateSystem ]}</a></span></td>
HTML;
	}
	else
	{
		$IPBHTML .= <<<HTML
			<td><span class='larger_text'>0</span></td>
HTML;
	}
	
	if ( $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings', 'nexus', 'tickets' ) )
	{
		$avgRating = empty( $ratings[ $dateSystem ] ) ? 0 : ( array_sum( $ratings[ $dateSystem ] ) / count( $ratings[ $dateSystem ] ) );
		$IPBHTML .= <<<HTML
			<td>{$avgRating}</td>
HTML;
	}
	
	$IPBHTML .= <<<HTML
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

//===========================================================================
// View Replies
//===========================================================================
function view( $staff, $replies ) {

$title = sprintf( $this->registry->getClass('class_localization')->words['supportaudit_title_staff'], $staff['members_display_name'] );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
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
<div class='section_title'>
	<h2>{$title}</h2>
</div>

<div class='acp-box'>
	<h3>{$title}</h3>
	<div id='ticket_replies'>
	
HTML;

	foreach ( $replies as $message )
	{
		$request = sprintf( $this->registry->getClass('class_localization')->words['supportaudit_from'], "<a href='{$this->settings['base_url']}&amp;module=tickets&amp;section=view&amp;id={$message['reply_request']}'>{$message['r_title']}</a>" );
	
		$IPBHTML .= <<<HTML
		
		<div id='reply_{$message['reply_id']}' class='post_block clear type_{$message['reply_type']}'>
		<div class='post_wrap'>
			<label for='act_{$message['reply_id']}'>
				<div class='bar'>
					<span style='font-weight:bold'>{$request}</span></span>
				</div>
			</label>
			<div class='author_info'>
				<img src='{$staff['pp_small_photo']}' width="50" height="50" class='photo' />
			</div>
			<div class='post_body clearfix'>
				<p class='posted_info desctext'>{$message['reply_date']}</p>
				<div class='post'>
					{$message['reply_post']}
				</div>
HTML;
				if ( $this->settings['nexus_support_satisfaction'] and $message['reply_type'] == 's' and $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings', 'nexus', 'tickets' ) )
				{	
					$IPBHTML .= <<<EOF
						<div class='post desctext right'>
EOF;
					if ( $message['rating_rating'] )
					{
						if ( $message['rating_note'] and $this->registry->getClass('class_permissions')->checkPermission( 'sr_ratings_feedback', 'nexus', 'tickets' ) )
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
				$IPBHTML .= <<<HTML
			</div>
		</div>
	</div>
HTML;

	}
	
	$IPBHTML .= <<<HTML
	</div>
</div>


HTML;

//--endhtml--//
return $IPBHTML;
}

}