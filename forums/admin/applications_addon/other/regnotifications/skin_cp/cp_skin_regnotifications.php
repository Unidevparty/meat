<?php

class cp_skin_regnotifications
{
	/**
	 * Registry Object Shortcuts
	 *
	 * @var		$registry
	 * @var		$DB
	 * @var		$settings
	 * @var		$request
	 * @var		$lang
	 * @var		$member
	 * @var		$memberData
	 * @var		$cache
	 * @var		$caches
	 */
	protected $registry;
	protected $DB;
	protected $settings;
	protected $request;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;
	
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

/**
 * overview
 * 
 * @access public
 * @return string HTML
 */
public function overview( $data )
{
$IPBHTML = "";
$active = $this->registry->output->formYesNo( 'active', $data['active'] );
$groups = $this->registry->output->formMultiDropdown( 'groups[]', '--groups--', $data['groups'] );
$template = $this->registry->output->formTextarea( 'template', $data['template'] );
//--starthtml--//
$IPBHTML .= <<<HTML
<style type='text/css'>
.link {
	color: #3287C9;
}
.link:hover {
	cursor: pointer;
}
.multitext {
	width: 100%;
}
</style>
<div class='section_title'>
	<h2>Настройки</h2>
</div>
<div class='acp-box'>
	<h3>Список настроек</h3>
	<form method="post" action="{$this->settings['base_url']}{$this->form_code}&do=save">
	<table class='ipsTable double_pad'>
		<tr>
			<td class='field_title'><strong class='title'>Включить уведомления</strong></td>
			<td class='field_field'>{$active}</td>
		</tr>
		<tr>
			<th colspan="2">Пользователи</th>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>Группы пользователей</strong></td>
			<td class='field_field'>{$groups}</td>
		</tr>
		<tr>
			<td class='field_title'><strong class='title'>Отдельные пользователи</strong></td>
			<td class='field_field'>
				{$data['members']}
			</td>
		</tr>
		<tr>
			<th colspan="2">Шаблон уведомления</th>
		</tr>
		<tr>
			<td class='field_field' colspan='2' style='padding-left: 2%; padding-right: 3%'>
				{$template}
				<br /><b>Доступные значения:</b><br />
HTML;

foreach ($data['pf'] as $field) {
	if( $field['name'] && $field['name'] )
	$IPBHTML .= <<<HTML
		<span class='link' onclick="jQ('#template').insertAtCaret('{$field['id']}')">{$field['id']}</span> - <b>{$field['name']}</b>
HTML;
	$IPBHTML .= "<br />";
}

$IPBHTML .= <<<HTML
			</td>
		</tr>
	</table>
	<div class='acp-actionbar'>
		<input type='submit' class='button' />
	</div>
	</form>
</div>
<script type="text/javascript">
jQ.fn.extend({
    insertAtCaret: function(myValue){
        return this.each(function(i) {
            if (document.selection) {
                // Для браузеров типа Internet Explorer
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else if (this.selectionStart || this.selectionStart == '0') {
                // Для браузеров типа Firefox и других Webkit-ов
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        })
    }
});
</script>
HTML;
//--endhtml--//
return $IPBHTML;
}

/**
 * members
 * 
 * @access public
 * @return string HTML
 */
public function members( $members )
{
$IPBHTML = "";
$counter = 1;
$input = $this->registry->output->formInput( "members[]", "", "member_{$counter}");
//--starthtml--//

$IPBHTML .= <<<HTML
<div id='members_container'>
HTML;

if( !empty( $members ) ) {
	foreach ( $members as $m ) {
		$IPBHTML .= <<<HTML
	<div id='member_{$counter}' class='clearfix' style='margin-bottom: 5px'>
		<input name='members[]' id='membername_{$counter}' value='{$m['members_display_name']}' size='30' class='input_text' style='margin-right: 5px; float: left;' type='text'> 
		<div class='button left' onclick='jQ(\"#member_" + last_id + "\").detach();'>Удалить</div>
	</div>
	<script type="text/javascript">
		document.observe("dom:loaded", function(){
			new ipb.Autocomplete( $('membername_{$counter}'), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
		});
	</script>
HTML;
		$counter++;
	}
}

$IPBHTML .= <<<HTML
</div>
<div id='members_add' class='button left'>Добавить</div>
<script type="text/javascript">
document.observe("dom:loaded", function(){
	last_id = {$counter};
	jQ('#members_add').click(function(){
		jQ('#members_container').append( "<div id='member_" + last_id + "' class='clearfix' style='margin-bottom: 5px'>{$this->registry->output->formInput( 'members[]', '', 'membername_" + last_id + "', 30, 'text', ' style=\"float: left; margin-right: 5px;\"' )} <div class='button left' onclick='jQ(\"#member_" + last_id + "\").detach();'>Удалить</div></div>"); 
		new ipb.Autocomplete( $('membername_' + last_id ), { multibox: false, url: acp.autocompleteUrl, templates: { wrap: acp.autocompleteWrap, item: acp.autocompleteItem } } );
		last_id++;
	});
});
</script>
HTML;

//--endhtml--//
return $IPBHTML;
}

}