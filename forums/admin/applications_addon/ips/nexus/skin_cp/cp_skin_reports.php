<?php
/**
 * Invision Power Services
 * IP.Nexus ACP Skin - Reports
 * Last Updated: $Date: 2012-02-13 05:07:16 -0500 (Mon, 13 Feb 2012) $
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		2nd December 2010
 * @version		$Revision: 10290 $
 */
 
class cp_skin_reports
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
// Render Report
//===========================================================================
function report() {

$addSeries = report::getOptions( $this->request['type'] );

$this->request['chart'] = $this->request['chart'] ? $this->request['chart'] : 'line';
$this->request['series'] = urldecode( http_build_query( array( 'series' => $this->request['series'] ) ) );

if ( !$this->request['timestamp'] )
{
	$time = mktime( 0, 0, 0, date( 'n', time() ), 1, date( 'Y', time() ) );
	$month = date( 'n', $time );
	ipsRegistry::getClass('output')->silentRedirect( "{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;view=m&amp;timestamp={$time}&amp;week=0&amp;month={$month}" );
}

if( $this->request['view'] == 'y' && ( ( $this->request['week'] && $this->request['week'] != 'null' ) || ( $this->request['month'] && $this->request['month'] != 'null' ) ) )
{
	$time = mktime( 0, 0, 0, 1, 1, date( 'Y', $this->request['timestamp'] ) );
	ipsRegistry::getClass('output')->silentRedirect( "{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;view=y&amp;timestamp={$time}&amp;week=0&amp;month=0" );
}

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML

<div class='section_title'>
	<h2>{$this->registry->getClass('class_localization')->words[ $this->request['type'] . '_report' ]}</h2>
	<div class='ipsActionBar clearfix'>
		<ul>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='date'><img src='{$this->settings['skin_app_url']}images/reports/calendar.png' /> {$this->lang->words['report_menu_date']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='view'><img src='{$this->settings['skin_app_url']}images/reports/calendar-{$this->request['view']}.png' /> {$this->lang->words['report_menu_view']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='type'><img src='{$this->settings['skin_app_url']}images/reports/chart-{$this->request['chart']}.png' /> {$this->lang->words['report_menu_chart']}<img src='{$this->settings['skin_acp_url']}/images/useropts_arrow.png' /></a></li>
			<li class='ipsActionButton'><a href='#' class='ipbmenu' id='editData'><img src='{$this->settings['skin_app_url']}images/reports/data.png' /> {$this->lang->words['report_menu_data']}</a></li>
		</ul>
	</div>
</div>

<ul class='ipbmenu_content' id='type_menucontent' style='display: none'>
	<li><img src='{$this->settings['skin_app_url']}images/reports/chart-bar.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart=bar&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_chart_bar']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/chart-line.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart=line&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_chart_line']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/chart-pie.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart=pie&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_chart_pie']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/chart-list.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart=list&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_chart_list']}</a></li>
</ul>

<ul class='ipbmenu_content' id='view_menucontent' style='display: none'>
	<li><img src='{$this->settings['skin_app_url']}images/reports/calendar-d.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view=d&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_date_day']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/calendar-w.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view=w&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_date_week']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/calendar-m.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view=m&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_date_month']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/calendar-y.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view=y&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_date_year']}</a></li>
	<li><img src='{$this->settings['skin_app_url']}images/reports/calendar-a.png' /> <a href='{$this->settings['base_url']}&amp;module=reports&amp;section={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view=a&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' style='text-decoration: none' >{$this->lang->words['report_date_all']}</a></li>
</ul>

<div class='ipbmenu_content' id='date_menucontent' style='display: none'>
HTML;
	
	/* Day Select */
	if ( in_array( $this->request['view'], array( 'd' ) ) )
	{
	
		$IPBHTML .= <<<HTML
		<select id='day'>
HTML;
	
		for ( $i=1; $i<=31; $i++ )
		{
			$selected = ( date( 'j', $this->request['timestamp'] ) == $i ) ? "selected='selected'" : '';
			$IPBHTML .= "<option value='{$i}' {$selected}>{$i}</option>";
		}
		
		$IPBHTML .= <<<HTML
		</select><br />
HTML;
	}
	else
	{
		$IPBHTML .= "<input type='hidden' id='day' value='0' />";
	}
	
	/* Week Select */
	if ( in_array( $this->request['view'], array( 'w' ) ) )
	{
	
		$IPBHTML .= <<<HTML
		<select id='week'>
HTML;

		for ( $i=1; $i<=6; $i++ )
		{
			$selected = ( $this->request['week'] == $i ) ? "selected='selected'" : '';
			$IPBHTML .= "<option value='{$i}' {$selected}>Week {$i}</option>";
		}
		
		$IPBHTML .= <<<HTML
		</select><br />
HTML;
	}
	else
	{
		$IPBHTML .= "<input type='hidden' id='week' value='0' />";
	}

	/* Month Select */
	if ( in_array( $this->request['view'], array( 'd', 'w', 'm' ) ) )
	{
	
		$IPBHTML .= <<<HTML
		<select id='month'>
HTML;
	
		for ( $i=1; $i<=12; $i++ )
		{
			$v = date( 'F', mktime( 0, 0, 0, $i, 1 ) );
						
			$selected = ( $this->request['month'] == $i ) ? "selected='selected'" : '';
			$IPBHTML .= "<option value='{$i}' {$selected}>{$v}</option>";
		}
		
		$IPBHTML .= <<<HTML
		</select><br />
HTML;
	}
	else
	{
		$IPBHTML .= "<input type='hidden' id='month' value='0' />";
	}
	
	/* Year Select */
	if ( in_array( $this->request['view'], array( 'd', 'w', 'm', 'y' ) ) )
	{
		$IPBHTML .= <<<HTML
			
		<select id='year'>
HTML;
	
		for ( $i = ( isset( $this->settings['nexus_reports_start_year'] ) ? $this->settings['nexus_reports_start_year'] : ( date( 'Y', ipsRegistry::$applications['nexus']['app_added'] ) ) ) ; $i<=date( 'Y' ); $i++ )
		{
			$selected = ( date( 'Y', ( is_int( $this->request['timestamp'] ) ? $this->request['timestamp'] : time() ) ) == $i ) ? "selected='selected'" : '';
			$IPBHTML .= "<option value='{$i}' {$selected}>{$i}</option>";
		}
		
		$IPBHTML .= <<<HTML

	</select><br />
	
	<input type='button' onclick='changeDate()' class='realbutton' value='View Report' />
HTML;
	
	}
	else
	{
		$IPBHTML .= <<<HTML
		<span class='desctext'>{$this->lang->words['report_nodatechange']}</span>
HTML;
	}
	
	$IPBHTML .= <<<HTML
</div>

HTML;

if ( $this->request['chart'] == 'list' )
{
	$url = str_replace( '&amp;', '&', $this->settings['base_url'] ) . "&module=reports&section=render&type={$this->request['type']}&{$this->request['series']}&chart={$this->request['chart']}&view={$this->request['view']}&timestamp={$this->request['timestamp']}&week={$this->request['week']}&month={$this->request['month']}";

	$IPBHTML .= <<<HTML
	<div id='display_list'></div>
	<script type='text/javascript'>
		new Ajax.Request( "{$url}&secure_key=" + ipb.vars['md5_hash'],
		{
			onSuccess: function( t )
			{
				$('display_list').innerHTML = t.responseText;
			}
		});
	</script>
HTML;
}
else
{
	$IPBHTML .= <<<HTML
	<img src='{$this->settings['base_url']}&amp;module=reports&amp;section=render&amp;type={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}' />
HTML;
}

$IPBHTML .= <<<HTML

<script type='text/javascript'>

	var series = 0;

	function changeDate()
	{
		new Ajax.Request( "{$this->settings['base_url']}".replace(/&amp;/g, '&') + "app=nexus&module=ajax&section=reports&do=date&secure_key=" + ipb.vars['md5_hash'],
		{
			evalJSON: 'force',
			parameters:
	    	{
	    		view: '{$this->request['view']}',
HTML;

	if ( in_array( $this->request['view'], array( 'd' ) ) )
	{
		$IPBHTML .= <<<HTML
	    		day: $('day').value,
HTML;
	}
	if ( in_array( $this->request['view'], array( 'd', 'w' ) ) )
	{
		$IPBHTML .= <<<HTML
	    		week: $('week').value,
HTML;
	}
	if ( in_array( $this->request['view'], array( 'd', 'w', 'm' ) ) )
	{
		$IPBHTML .= <<<HTML
	    		month: $('month').value,
HTML;
	}
	if ( in_array( $this->request['view'], array( 'd', 'w', 'm', 'y' ) ) )
	{
		$IPBHTML .= <<<HTML
	    		year: $('year').value,
HTML;
	}
	
	$IPBHTML .= <<<HTML
	    	},
			onSuccess: function( t )
			{
				if ( t.responseJSON['error'] )
				{
					alert( t.responseJSON['error'] );
				}
				else
				{
					if ( t.responseJSON['timestamp'] != {$this->request['timestamp']} )
					{
						window.location = "{$this->settings['base_url']}&module=reports&section={$this->request['type']}&amp;{$this->request['series']}&chart={$this->request['chart']}&view={$this->request['view']}&timestamp=".replace(/&amp;/g, '&') + t.responseJSON['timestamp'] + "&week=" + t.responseJSON['week'] + "&month=" + t.responseJSON['month'];
					}
				}
				
			}
		});
	}
	
	function doPopUp( e )
	{
		new ipb.Popup('editData', { type: 'pane', stem: true, attach: { target: e, position: 'auto' }, hideAtStart: false, w: '600px', h: '600px', ajaxURL: "{$this->settings['base_url']}&module=ajax&section=reports&do=edit_data&type={$this->request['type']}&amp;{$this->request['series']}&amp;chart={$this->request['chart']}&amp;view={$this->request['view']}&amp;timestamp={$this->request['timestamp']}&amp;week={$this->request['week']}&amp;month={$this->request['month']}&secure_key=".replace(/&amp;/g, '&') + ipb.vars['md5_hash'], modal: false } );
	}

	
	// changeDate(); bug report 37925
	$('editData').observe('click', doPopUp.bindAsEventListener( this ) );
	
	function addSeries()
	{
		series = series + 1;

		var row = $('editSeriesTable').insertRow( $('editSeriesTable').rows.length );

		var cell_title = row.insertCell(0);
		cell_title.className = 'field_title';
		cell_title.innerHTML = "<strong class='title'>Series "+series+"</strong>";
		
		var cell_select = row.insertCell(1);
		cell_select.className = 'field_field';
		cell_select.innerHTML = "<select name='series["+ series +"][]' multiple='multiple'>{$addSeries}</select>";
	}
</script>

HTML;

return $IPBHTML;

}

//===========================================================================
// Edit Data
//===========================================================================
function editData( $series ) {
$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<form action='{$this->settings['base_url']}&module=reports&section={$this->request['type']}&type={$this->request['type']}&chart={$this->request['chart']}&view={$this->request['view']}&timestamp={$this->request['timestamp']}&week{$this->request['week']}&month={$this->request['month']}' method='post'>
	<div class='acp-box'>
		<h3>{$this->lang->words['report_edit_data']}</h3>
		<div style='max-height: 400px; overflow: auto'>
			<table class="ipsTable double_pad" id='editSeriesTable'>
HTML;
		
		foreach ( $series as $k => $box )
		{
			$IPBHTML .= <<<HTML
				<script type='text/javascript'>series = {$k};</script>
				<tr>
					<td class='field_title'><strong class='title'>{$this->lang->words['report_series']} {$k}</strong></td>
					<td class='field_field'>{$box}</td>
				</tr>
HTML;
		}
			
			$IPBHTML .= <<<HTML
			</table>
		</div>
		<div class='acp-actionbar'>
			<input type='button' onclick='addSeries()' class='button' value='{$this->lang->words['report_series_add']}' /> <input type='submit' value='{$this->registry->getClass('class_localization')->words['save']}' class='realbutton' />
		</div>
	</div>
</form>
HTML;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// View List
//===========================================================================
function viewList( $results ) {

arsort( $results );

$IPBHTML = "";
//--starthtml--//

$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>{$this->lang->words['report_results']}</h3>
	<table class='ipsTable'>
HTML;

	foreach ( $results as $label => $data )
	{
		$IPBHTML .= <<<HTML
		<tr>
			<td class='field_title'><strong class='title'>{$label}</strong></td>
			<td class='field_field'>{$data}</td>
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



}