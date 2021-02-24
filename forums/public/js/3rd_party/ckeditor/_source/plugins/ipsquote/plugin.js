/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @file Print Plugin
 */
 

CKEDITOR.plugins.add( 'ipsquote',
{
	init : function( editor )
	{
		var pluginName = 'ipsquote';

		// Register the command.
		var command = editor.addCommand( pluginName, CKEDITOR.plugins.ipsquote );

		// Register the toolbar button.
		editor.ui.addButton( 'Ipsquote',
			{
				label : ipb.lang['ckeditor__quotelabel'],
				command : pluginName,
				icon: this.path+"images/quote.png"
			});

		// Remove cites
		editor.on( 'removeFormatCleanup', function( evt )
			{
				var element = evt.data;

				if( element.is('blockquote') )
				{
					var children = evt.data.getChildren();

					for( var i=0; i < children.count(); i++ )
					{
						var child = children.getItem(i);

						if( child.getName() == 'cite' )
						{
							child.remove();
						}
					}

					element.remove(1);
				}
			});
	}
} );

CKEDITOR.plugins.ipsquote =
{
	exec : function( editor )
	{	
		var sel = new CKEDITOR.dom.selection( editor.document );
		
		/* Get highlighted content */
		text = IPSCKTools.getSelectionHtml( editor );
		if ( text == '' )
		{
			//text = '&nbsp;';
		}

		/* Quoting lists can be tricky */
		if ( text.substr( 0, 4 ) == '<li>' )
		{
			var list = sel.getCommonAncestor();
			
			para = CKEDITOR.dom.element.createFromHtml( '<p>&nbsp;</p>' );
			list.insertBeforeMe( para );
			CKEDITOR.dom.element.createFromHtml( '<p>&nbsp;</p>' ).insertAfter( para );
			list.move(para);
			sel.selectElement( para );
									
			text = IPSCKTools.getSelectionHtml( editor );			
		}
						
		/* Create the actual blockquote with the content */
		defaulttext = IPSCKTools.cleanHtmlForTagWrap( text ) ? IPSCKTools.cleanHtmlForTagWrap( text ) : "<p>" + IPSCKTools.cleanHtmlForTagWrap( '' ) + "</p>";
		blockquote = CKEDITOR.dom.element.createFromHtml( '<blockquote class="ipsBlockquote">' + defaulttext + '</p></blockquote>' );
						
		/* Create a cite tag */
		cite = CKEDITOR.dom.element.createFromHtml( '<cite class="ipb" contenteditable="false">'+ipb.lang['ckeditor__quotelabel']+'</cite>' );
		
		/* Put it all together */
		blockquote.append( cite, true );
						
		/* Insert it */
		editor.insertElement( blockquote );
				
		/* Select it */
		range = new CKEDITOR.dom.range( editor.document );
		range.moveToElementEditEnd( blockquote.getLast() );
		ranges = [ range ];
		sel.selectRanges( ranges );
		
		/* Add an onclick handler to the cite tag which will remove focus so that FireFox users can't delete it */
		ipb.textEditor.makeQuoteEventHandlers( editor );

	},
	canUndo : true,
	modes : { wysiwyg : 1 }
	
};