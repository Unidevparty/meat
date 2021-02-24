/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*
* This plug in based on code from http://cksource.com/forums/viewtopic.php?f=11&t=17017&start=10
*/

(function()
{
   var pluginName = 'ipsautosave';

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
				if ( editor.config.ips_AutoSaveKey )
				{
					/* Fire off a save */
					ipb.textEditor.getEditor().save( editor, command );
				}
			}
		 };

		 var commandName = pluginName,
			command = editor.addCommand( commandName, commandDefinition );

		 editor.ui.addButton( 'Ipsautosave',
		 {
			label : editor.lang.ajaxAutoSaveButtonLabel,
			command : commandName,
			icon:this.path + 'images/autosave.gif'
		 } );


		 // Schedule auto ajax save only if content is changed.
		 var autoAjaxSave = setInterval( function()
		 {
			if( editor.checkDirty() )
			{
			   editor.execCommand( commandName );

			   // Indicate busy state on this command.
			   command.setState( CKEDITOR.TRISTATE_DISABLED );
			}
		 }, editor.config.autoAjaxSaveInterval || 120000 );

		 // Stop the job after editor is down.
		 editor.on( 'destroy', function()
		 { 
			clearInterval( autoAjaxSave );
		 } );
	  }
   } );
})();