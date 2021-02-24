<?php
/*
 * Product Title:		Awards Management System
 * Product Version:		3.0.23
 * Author:				InvisionHQ
 * Website:				bbcode.it
 * Website URL:			http://bbcode.it/
 * Email:				reficul@lamoneta.it
 * Copyright©:			InvisionHQ - Gabriele Venturini - bbcode.it - 2012/2013
 */
class cp_skin_manage
{
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;

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

	private function hookCodes()
	{
		$locs = array(	'0' => $this->lang->words['ja_type_profile'],
						'1' => $this->lang->words['ja_type_signature'],
						'2' => $this->lang->words['ja_type_badge'],
						'5' => $this->lang->words['ja_type_achievement'],
						'6' => $this->lang->words['ja_type_naked'],
					);
		return( $locs );
	}

	private function categoryField( $value="", $noShow="" )
	{
		if( ! $value )
		{
			$value = 1;
		}
		$this->DB->build( array(	'select' => '*',
							   		'from'   => 'jlogica_awards_cats',
							   		'order'  => 'placement ASC',
				));
		$this->catList = array();
		$cats = $this->DB->execute();
		while( $c = $this->DB->fetch( $cats ) )
		{
			$this->catList[$c['cat_id']] = $c;
			$sel = '';
			if( $value == $c['cat_id'] )
			{
				$sel = " selected";

			}
			$default = '';
			if( $c['cat_id'] == 1 )
			{
				$default = " (Default)";
			}
			if( $noShow != $c['cat_id'] )
			{
			    $options .= "<option value='{$c['cat_id']}'{$sel}>{$c['title']}{$default}</option>";
			}
		}
		return( $options );
	}

	private function groupsField( $value="" )
	{
		$groups = explode( ',', $value );
		$this->DB->build( array(	'select' => '*',
							   		'from'   => 'groups',
							   		'where'  => 'g_jlogica_awards_can_give = 1',
						));
		$this->DB->execute();
		while( $g = $this->DB->fetch() )
		{
			$sel = '';
			if( in_array( $g['g_id'], $groups ) )
			{
				$sel = " selected";
			}
			$options .= "<option value='{$g['g_id']}'{$sel}>{$g['g_title']}</option>";
		}
		return "<select multiple='multiple' size='5' name='public_perms[]'>{$options}</select>";
	}

	private function groupsBadge( $value="" )
	{
		$groups = explode( ',', $value );
		$this->DB->build( array(	'select' => '*',
							   		'from'   => 'groups',
						));
		$this->DB->execute();
		while( $g = $this->DB->fetch() )
		{
			$sel = '';
			if( in_array( $g['g_id'], $groups ) )
			{
				$sel = " selected";
			}
			$options .= "<option value='{$g['g_id']}'{$sel}>{$g['g_title']}</option>";
		}
		return "<select multiple='multiple' size='5' name='badge_perms[]'>{$options}</select>";
	}


	public function deleteAward( $data )
	{
		$IPBHTML = $this->Header();
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class='acp-box'>
	<h3>Deleting Award: {$a['name']}</h3>
	<div class='tablerow1' style='padding:10px;'>
		{$this->lang->words['ja_delaward']}
	</div>
	<div class='acp-actionbar'>
		<form name='deleteAward' method='post' action='{$this->settings['base_url']}module=awards&section=manage&do=delete&what=award&id={$this->request['id']}&continue=1'>
			<input type='hidden' name='deleteAward' value='{$data['id']}' />
			<input type='submit' value=' {$this->lang->words['ja_yesdelete']} ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}'>{$this->lang->words['ja_cancel']}</a></strong>
		</form>
	</div>
</div>
HTML;
		//--endhtml--//
		return $IPBHTML;
	}


	public function deleteCategory( $data )
	{
		$IPBHTML = $this->Header();
		//--starthtml--//

		$IPBHTML .= $this->errorText( $data['error'] );
		# Show the "this cat has awards" bar
		if( $data['numAwards'] )
		{
			$cato = $this->categoryField( $data['cat'], $data['cat'] );
			$awardsOptions = <<<HTML
<div style='margin-top:50px; font-weight:bold;' align='center'>
{$this->lang->words['ja_delhasawards']}{$cato}
</div>
HTML;
		}

		$IPBHTML .= <<<HTML
{$error}
<form name='deleteAward' method='post' action='{$this->settings['base_url']}module=awards&section=manage&do=delete&what=category&id={$data['cat']}&continue=1'>
	<div class='acp-box'>
		<h3>{$this->lang->words['ja_delcat']}: {$data['title']}</h3>
			<table class='ipsTable'>
				<tr>
					<td>
						{$this->lang->words['ja_delareyousure']}
						<select name='awards_option'>
			  				<option></option>
			  				<option value='delete'>{$this->lang->words['ja_delete']}</option>
			  				<optgroup label='{$this->lang->words['ja_ormoveto']}'>
								{$awardsOptions}
							</optgroup>
						</select>
					</td>
				</tr>
			</table>
			<div class='acp-actionbar'>
				<input type='submit' value='{$this->lang->words['ja_continue']}' class='realbutton' /> {$this->lang->words['ja_edit_saveor']} <strong><a href='{$this->settings['base_url']}'>{$this->lang->words['ja_cancel']}</a></strong>
			</div>
		</div>
	<input type='hidden' name='deleteCategory' value='{$data['cat']}' />
</form>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}



	public function errorText( $text )
	{
		if( ! $text )
		{
			return( '' );
		}
		$IPBHTML = '';
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class='warning' style='margin-bottom:15px;'>
	<h4><img src={$this->settings['skin_acp_url']}/images/icons/bullet_error.png' /> Error!</h4>
	{$text}
</div>
HTML;
		//--endhtml--//
		return $IPBHTML;
	}






	public function helpText( $text )
	{
		$IPBHTML = '';
		//--starthtml--//

		$IPBHTML .= <<<HTML
<div class='information-box' style='margin-bottom:15px;'>
	<h4 style='margin-bottom:0; padding-bottom:0;'>{$this->lang->words['ja_helppage']} <span style='font-size:10px; font-weight:100;'>(<a href='javascript: showHideInfo();'>{$this->lang->words['ja_helpshowhide']}</a>)</span></h4>
	<div id='helpDiv' style='display:none; margin-top:15px;'>
		{$text}
	</div>
</div>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}

	public function main( $data )
	{
		$IPBHTML = $this->Header();
		$IPBHTML .= <<<HTML
<ul class='context_menu'>
	<li>
		<a href='{$this->settings['base_url']}module=awards&section=manage&do=add&what=award'><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' />{$this->lang->words['ja_addnewaward']}</a>
	</li>
	<li>
		<a href='javascript: addCategory();' alt='' /><img src='{$this->settings['skin_acp_url']}/images/icons/add.png' alt='' />{$this->lang->words['ja_addnewcat']}</a>
	</li>
</ul>
HTML;
		$CoreHTML = '';
		//--starthtml--//

		# Set some stuff
		$enabledImage	= "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' title='{$this->lang->words['ja_disable']}' />";
		$disabledImage	= "<img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' title='{$this->lang->words['ja_enable']}' />";
		$showImage		= "<img src='{$this->settings['skin_acp_url']}/images/icons/eye.png' title='{$this->lang->words['ja_hide']}' />";
		$hideImage		= "<img src='{$this->settings['skin_acp_url']}/images/icons/eye_minus.png' title='{$this->lang->words['ja_show']}' />";

		$help = $this->lang->words['ja_mahelp'];
		$search  = array( '%enabledImage%',	'%disabledImage%',	'%showImage%',	'%hideImage%' );
		$replace = array( $enabledImage,	$disabledImage,		$showImage,		$hideImage );
		$help = str_replace( $search, $replace, $help );
		$IPBHTML .= $this->helpText( $help );
		$jsCatVisibilityArray 		= '';
		$jsCatAwards          		= '';
		$jsArray 			  		= '';
		$jsAwardsVisibilityArray	= '';
		$jsCatShowArray				= '';
		$jsAwardCat					= '';
		$locs = $this->hookCodes();

		foreach( $data as $cat )
		{
			$jsArray .= "catTitles[{$cat['cat_id']}] = '" . addslashes( $cat['title'] ) . "';\n";
			$jsCatShowArray .= "catShow[{$cat['cat_id']}] = {$cat['frontend']};\n";
			if( $cat['visible'] and count( $cat['Awards'] ) )
			{
				$visibility = "<a href='javascript: changeStatus_category({$cat['cat_id']}, " . count( $cat['Awards'] ) . ");' id='cat_enabler_{$cat['cat_id']}'>" . $enabledImage . "</a>";
				$jsCatVisibilityArray .= "catVisibility[{$cat['cat_id']}] = 1;\n";
			}
			else
			{
				$visibility = "<a href='javascript: changeStatus_category({$cat['cat_id']}, " . count( $cat['Awards'] ) . ");' id='cat_enabler_{$cat['cat_id']}'>" . $disabledImage . "</a>";
				$jsCatVisibilityArray .= "catVisibility[{$cat['cat_id']}] = 0;\n";
			}
//			$jsCatAwards          .= "catAwards[{$cat['cat_id']}] = \"". implode( ',', array_keys( $cat['Awards'] ) ) . "\";\n";
			$keys = '';
			foreach( $cat['Awards'] AS $a ) $keys .= $a['id'] . ',';
			$keys = substr( $keys, 0, -1 );
			$jsCatAwards          .= "catAwards[{$cat['cat_id']}] = \"{$keys}\";\n";
			if( $cat['frontend'] )
			{
				$fevisible = "<a href='javascript: changeShow({$cat['cat_id']});' id='show_enabler_{$cat['cat_id']}'>" . $showImage . "</a>";
			}
			else
			{
				$fevisible = "<a href='javascript: changeShow({$cat['cat_id']});' id='show_enabler_{$cat['cat_id']}'>" . $hideImage . "</a>";
			}
			$EditOption  = "<a href='{$this->settings['base_url']}module=awards&section=manage&do=edit&what=category&id={$cat['cat_id']}' title='{$this->lang->words['ja_editcat_title']}'>{$this->lang->words['ja_editcat']}</a>";
			if($cat['cat_id'] == 1)
			{
				$catTitleExtra = " <span style='font-wight:100; font-size:12px;'>(<em>Default</em>)</span>";
				$DelOption  = "<a href='#' title='{$this->lang->words['ja_deletecatno']}'>{$this->lang->words['ja_deletecat']}</a>";
			}
			else
			{
				$catTitleExtra = '';
				$DelOption  = "<a href='{$this->settings['base_url']}module=awards&section=manage&do=delete&what=category&id={$cat['cat_id']}' title='{$this->lang->words['ja_deletecat_title']}'>{$this->lang->words['ja_deletecat']}</a>";
			}
			$CoreHTML .= <<<HTML
<div class='isDraggable' id='cat_{$cat['cat_id']}'>
	<div class='root_item item category clearfix ipsControlRow'>
		<div>
			<table width='100%'>
				<tr onmouseover='javascript: revealCatDeleteBtn({$cat['cat_id']});' onmouseout='javascript: revealCatDeleteBtn({$cat['cat_id']});'>
					<td style='background:transparent; margin:0; padding:0;' width='2%'>
						<div class='draghandle'>&nbsp;</div>
					</td>
					<td style='background:transparent; margin:0; padding:0;' width='72%'>
						<strong class='larger_text'>
							<span style='cursor:pointer;' id='cat_{$cat['cat_id']}_name' onDblClick='javascript: editCatTitle({$cat['cat_id']});' title='{$this->lang->words['ja_doubleclick']}'>{$cat['title']}</span>
						</strong>
						{$catTitleExtra}
					</td>
					<td style='width:8%; text-align:center;'>
						{$locs[$cat['location']]}
					</td>
					<td style='width:5%; text-align:center;'>
						{$fevisible}
					</td>
					<td style='background:transparent; margin:0; padding:0; padding-left:10px; text-align:center;' width='5%;'>
						{$visibility}
					</td>
					<td style='background:transparent; margin:0; padding:0; padding-left:10px; text-align:center;' width='8%;'>
						{$Option}
						<ul class='ipsControlStrip'>
							<li class='i_edit'>
								{$EditOption}
							</li>
							<li class='i_delete'>
								{$DelOption}
							</li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div id='cat_jawards_{$cat['cat_id']}' class='item_wrap'>
HTML;
			if( count( $cat['Awards'] ) )
			{
				$URL = $this->settings['base_url'] . "module=ajax&section=manage&do=order&for=awds&cat={$cat['cat_id']}&secure_key=";
				$URL = str_replace( '&amp;', '&', $URL );
				foreach( $cat['Awards'] AS $a )
				{
					$jsAwardCat .= "awardCat[{$a['id']}] = {$cat['cat_id']};\n";
					$jsAwardsVisibilityArray .= "awardsVisibility[{$a['id']}] = {$a['visible']};\n";
					if( $a['visible'] && $cat['visible'] )
					{
						$visibility = "<a href='javascript: changeStatus_awards({$a['id']});' id='awards_enabler_{$a['id']}'>" . $enabledImage . "</a>";
					}
					else
					{
						$visibility = "<a href='javascript: changeStatus_awards({$a['id']});' id='awards_enabler_{$a['id']}'>" . $disabledImage . "</a>";
					}
					$CoreHTML .= <<<HTML
<div class='item ipsControlRow isDraggable' id='award_{$a['id']}'>
	<div>
		<table width='100%'>
			<tr>
				<td width='2%'><div class='draghandle'></div></td>
				<td width='5%' align='center'><img src='{$this->settings['upload_url']}/jawards/{$a['icon']}' /></td>
				<td width='2%'></td>
				<td width='71%'>
					<strong class='forum_name'>{$a['name']}</strong><br />
					<div class='desctext'>{$a['desc']}</div>
				</td>
				<td style='width:5%; text-align:center;'></td>
				<td style='width:5%; text-align:center;'>{$visibility}</td>
				<td style='width:8%; text-align:center;'>
					<ul class='ipsControlStrip'>
						<li class='i_edit'>
							<a href='{$this->settings['base_url']}module=awards&section=manage&do=edit&what=award&id={$a['id']}' title='{$this->lang->words['ja_editaward']}'>{$this->lang->words['ja_editaward']}</a>
						</li>
						<li class='i_delete'>
							<a href='{$this->settings['base_url']}module=awards&section=manage&do=delete&what=award&id={$a['id']}' title='{$this->lang->words['ja_delaward']}'>{$this->lang->words['ja_delaward']}</a>
						</li>
					</ul>
				</td>
			</tr>
		</table>
	</div>
</div>
HTML;
				}
				$CoreHTML .= <<<HTML
<script type='text/javascript'>
	dropItLikeItsHot_cat_{$cat['cat_id']} = function( draggableObject, mouseObject )
	{
		new Ajax.Request( '{$URL}' + ipb.vars['md5_hash'],
		{
			method: 'post',
			parameters : Sortable.serialize('cat_jawards_{$cat['cat_id']}', { tag: 'div', name: 'awds' }),
		});
		return false;
	};
	Sortable.create(	'cat_jawards_{$cat['cat_id']}',
						{	tag: 'div',
							only: 'isDraggable',
							revert: true,
							format: 'award_([0-9]+)',
							onUpdate: dropItLikeItsHot_cat_{$cat['cat_id']},
							handle: 'draghandle'
						}
					);
</script>
HTML;
			}
			$CoreHTML .= <<<HTML
	</div>
</div>
HTML;
		}
		$URL = $this->settings['base_url'] . 'module=ajax&section=manage&do=order&for=cats&secure_key=';
		$URL = str_replace( "&amp;", "&", $URL );
		$IPBHTML .= <<<HTML
<script>
	var stylesURL = '{$this->settings['skin_acp_url']}';	var catTitles = [];
	{$jsArray}

	var enabledIMG  = "{$enabledImage}";
	var disabledIMG = "{$disabledImage}";
	var showIMG  = "{$showImage}";
	var hideIMG = "{$hideImage}";

	var catVisibility = [];
	{$jsCatVisibilityArray}

	var catShow = [];
	{$jsCatShowArray}

	var awardsVisibility = [];
	{$jsAwardsVisibilityArray}

	var catAwards = [];
	{$jsCatAwards}

	var awardCat = [];
	{$jsAwardCat}

	window.onload = function()
	{
		Sortable.create(	'categories',
							{	tag: 'div',
								only: 'isDraggable',
								revert: true,
								format:
								'cat_([0-9]+)',
								onUpdate: dropItLikeItsHot,
								handle: 'draghandle'
							}
						);
	};
	dropItLikeItsHot = function( draggableObject, mouseObject )
	{
		new Ajax.Request( '{$URL}' + ipb.vars['md5_hash'],
							{	method: 'post',
								parameters : Sortable.serialize('categories', { tag: 'div', name: 'cats' } ),
							}
						);
		return false;
	};
</script>
<div class='acp-box'>
  <h3>Awards Management</h3>
	  <div class='ipsExpandable' id='categories'>
		{$CoreHTML}
	</div>
</div>
HTML;
		//--endhtml--//
		return $IPBHTML;

	}

	public function Header()
	{
		$IPBHTML = "";
		//--starthtml--//

		$IPBHTML .= <<<HTML
<script type='text/javascript' src='{$this->settings['js_app_url']}manage.js'></script>
<div class="section_title">
	<h2>{$this->lang->words['ja_manage_awards']}</h2>
</div>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}


	public function editAward( $data, $add = false )
	{
		$cat   = $this->categoryField( $data['parent'] );
		$badges = $this->groupsBadge( $data['badge_perms'] );
		$public = $this->groupsField( $data['public_perms'] );
		if( $add )
		{
			$action = "{$this->settings['base_url']}module=awards&section=manage&do=add&continue=1";
			$verb = $this->lang->words['ja_addingaward'];
			$submit = $this->lang->words['ja_addaward'];
			$warn = '';
		}
		else
		{
			$action = "{$this->settings['base_url']}module=awards&section=manage&do=edit&what=award&id={$data['id']}&continue=1";
			$verb = $this->lang->words['ja_edit_award'];
			$submit = $this->lang->words['ja_edit_savechanges'];
			$warn = "<span style='color:red; font-weight:bold;'>{$this->lang->words['ja_edit_icon_warn']}</span><br />";
		}
		$IPBHTML = $this->Header();
		//--starthtml--//
//{$this->lang->words['ja_edit_']}
		$IPBHTML .= <<<HTML
<form name='editAward' method='post' action='{$action}' enctype='multipart/form-data'>
	<div class='acp-box'>
		<h3>{$verb} {$data['name']}</h3>
			<table width='100%' class='alternate_rows double_pad'>
				<tr>
					<td width='35%'>
						<strong>{$this->lang->words['ja_edit_awardname']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_awardname_desc']}</div>
					</td>
					<td>
						<input type='text' name='award_name' size='35' value='{$data['name']}' />
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_awarddescno']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_awarddesc_descno']}</div>
					</td>
					<td class='tablerow2'>
						<input type='text'  name='award_descno' size='35' value='{$data['descno']}' />
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_awarddesc']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_awarddesc_desc']}</div>
					</td>
					<td class='tablerow2'>
						<input type='text' name='award_desc' size='35' value='{$data['desc']}' />
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_awardlongdesc']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_awardlongdesc_desc']}</div>
					</td>
					<td>
						{$data['editor']}
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_awardcategory']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_awardcategory_desc']}</div>
					</td>
					<td>
						<select name='category'>
							{$cat}
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_badge']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_badge_desc']}</div>
					</td>
					<td class='tablerow2'>
						{$badges}
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_perm']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_perm_desc']}</div>
					</td>
					<td>
						{$public}
					</td>
				</tr>
				<tr>
					<td>
						<strong>{$this->lang->words['ja_edit_icon']}</strong>
						<div class='desctext'>{$this->lang->words['ja_edit_icon_desc']}</div>
					</td>
					<td class='tablerow2'>
						{$warn}
						<input type='file' name='award_icon' />
					</td>
			</tr>
		</table>
		<div class='acp-actionbar'>
			<input type='submit' value='{$submit}' class='realbutton' /> {$this->lang->words['ja_edit_saveor']} <strong><a href='{$this->settings['base_url']}'>{$this->lang->words['ja_cancel']}</a></strong>
		</div>
	</div>
</form>
HTML;
IPSDebug::fireBug( 'info', array( $this->lang->words, "Loaded language strings" ) ) ;

		//--endhtml--//
		return $IPBHTML;
	}

	public function editCategory( $data )
	{
		$feChecked = $data['frontend'] ? 'CHECKED' : '';
		$locations = '';
		$locs = $this->hookCodes();
		foreach( $locs AS $i => $l )
		{
			$sel = "";
			if( $data['location'] == $i )
			{
				$sel = " selected";
			}

			$locations .= "<option value='{$i}'{$sel}>{$l}</option>";
		}
		$locations = "<select name='location'>{$locations}</select>";

		$IPBHTML = $this->Header();
		//--starthtml--//

		$IPBHTML .= <<<HTML
<form name='editCategory' method='post' action='{$this->settings['base_url']}module=awards&section=manage&do=edit&what=category&id={$data['cat_id']}&continue=1' enctype='multipart/form-data'>
	<div class='acp-box'>
 	  <h3>Editing Category: {$data['title']}</h3>
		  <table width='100%' class='alternate_rows double_pad'>
		    <tr>
			  <td width='35%'>
			    <strong>{$this->lang->words['ja_edit_cattitle']}</strong>
				<div class='desctext'>{$this->lang->words['ja_edit_cattitle_desc']}</div>
			  </td>
			  <td>
				<input type='text' name='title' size='35' value='{$data['title']}' />
			  </td>
			</tr>
		    <tr>
			  <td width='35%'>
			    <strong>{$this->lang->words['ja_show_on_frontend']}</strong>
				<div class='desctext'>{$this->lang->words['ja_show_on_frontend_desc']}</div>
			  </td>
			  <td>
			  	<input type='checkbox' name='frontend' {$feChecked} />
			  </td>
			<tr>
			  <td>
			    <strong>{$this->lang->words['ja_edit_location']}</strong>
				<div class='desctext'>{$this->lang->words['ja_edit_location_desc']}</div>
			  </td>
			  <td class='tablerow2'>
			    	{$locations}
			  </td>
			</tr>
			</tr>
		  </table>
		  <div class='acp-actionbar'>
			<input type='submit' value=' Save Changes ' class='realbutton' /> {$this->lang->words['ja_edit_saveor']} <strong><a href='{$this->settings['base_url']}'>{$this->lang->words['ja_cancel']}</a></strong>
	  	  </div>
		</div>
		</form>
HTML;

		//--endhtml--//
		return $IPBHTML;
	}

}
