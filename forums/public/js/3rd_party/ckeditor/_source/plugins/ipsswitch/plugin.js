/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*
* This plug in based on code from http://cksource.com/forums/viewtopic.php?f=11&t=17017&start=10
*/

(function()
{
   var pluginName = 'ipsswitch';

   CKEDITOR.plugins.add( pluginName,
   {
	  /**
	   * @param editor The editor instance to which the plugin bind.
	   */
	  init : function( editor )
	  {
		 var commandDefinition =
		 {
			// Check for ipsfull toolbar
			modes : { wysiwyg: 1, source : 1 },

			// This command will not auto focus editor before execution.
			editorFocus : false,

			// This command requires no undo snapshot.
			canUndo : false,

			exec : function( editor )
			{
				/* Fire off a switch */
				ipb.textEditor.switchEditor( false, editor.name );
			}
		 };

		 var commandName = pluginName,
			command = editor.addCommand( commandName, commandDefinition );

		 editor.ui.addButton( 'Ipsswitch',
		 {
			label : ipb.lang['ckeditor__togglelabel'],
			command : commandName,
			icon:this.path + 'images/switch.png'
		 } );
	  }
   } );
})();