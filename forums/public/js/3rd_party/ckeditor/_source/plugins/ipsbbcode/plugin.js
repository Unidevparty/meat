/* This file allows one to quickly insert emoticons from the context menu
   and also sets up the special BBCode button */


CKEDITOR.plugins.add('ipsbbcode',
{
    init: function(editor)
    {
    	/* Add main button in */
    	var PATH       = this.path;
        var pluginName = 'ipsbbcode';
        CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/ipsbbcode.js');
        editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));
        
        editor.ui.addButton('Ipsbbcode',
            {
                label: ipb.lang['ckeditor__bbcode'],
                command: pluginName,
                icon: this.path+"images/ips_bbcode.png"
            });

       /* Add in custom buttons */
       $H(editor.config.IPS_BBCODE).each( function( tag )
		{
			var key = tag.key;
			var value = tag.value;
			
			/* Add a button? */
			if ( value.image && value.image != '' )
			{
				editor.ui.addButton( 'Ipsbbcode_' + value.tag,
				{
					label : value.title,
					command : 'ipsbbcode_' + value.tag,
					icon: editor.config.IPS_BBCODE_IMG_URL + '/' + value.image
				} );
				
				CKEDITOR.dialog.add('ipsbbcode_' + value.tag, PATH + 'dialogs/ipsbbcode.js');
				editor.addCommand('ipsbbcode_' + value.tag, new CKEDITOR.dialogCommand('ipsbbcode_' + value.tag));
				
				if ( editor.config.IPS_BBCODE_BUTTONS.indexOf( 'Ipsbbcode_' + value.tag ) == -1 )
				{
					editor.config.IPS_BBCODE_BUTTONS.push( 'Ipsbbcode_' + value.tag );
				}
			}
		} );
    }
});