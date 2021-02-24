function deleteAward(aid)
{
	conf = confirm('Are you sure you want to PERMANENTLY DELETE this award?');
	
	if( conf )
	{
		window.location = ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=awards&section=award&do=delete&id=' + aid;
	}
}

function showEditBtn(id, status)
{
	if( ! InEditMode )
	{
		var btnSpan = document.getElementById( 'edit_' + id );
		
		if(status)
		{
			btnSpan.style.display = 'block';
		}
		else
		{
			btnSpan.style.display = 'none';
		}
	}
}

function editNotes(row, status, cancel)
{
	if( ! InEditMode || cancel || !status )
	{
		var editTD = document.getElementById( 'notes_td_' + row );
		InEditMode = 1;
		
		if(status)
		{
			editTD.innerHTML = '<textarea id=\"edit_notes_' + row + '\" rows=\"5\" cols=\"40\">' + notesArray[row] + '</textarea> <a href=\"javascript: saveNotes(' + row + ');\"><img src=\"' + CP_SKIN_URL + '/images/icons/disk.png\" alt=\"Save Notes\" title=\"Save Notes\" border=\"0\" /></a> <a href=\"javascript: editNotes(' + row + ', 0, 1);\"><img src=\"' + CP_SKIN_URL + '/images/icons/delete.png\" alt=\"Cancel\" title=\"Cancel\" border=\"0\" /></a>';
		}
		else
		{
			if(cancel)
			{
				conf = confirm('Are you sure that you would like to cancel, any changes will be lost.');
			}
			else
			{
				conf = 1;
			}
			
			if(conf)
			{
				editTD.innerHTML = notesArray[row] + " <span style='display:none;' id='edit_" + row + "'><a href='javascript: editNotes(" + row + ", 1, 0);'><img src='" + CP_SKIN_URL + "/images/icons/pencil.png' alt='Edit Notes' title='Edit Notes' border='0' /></a></span>";
				
				InEditMode = 0;
			}
		}
	}
}

function saveNotes(row)
{
	var notesValue = document.getElementById('edit_notes_' + row).value;
	var editTD     = document.getElementById('notes_td_' + row);
	editTD.innerHTML = "<img src='" + CP_SKIN_URL + "/images/loading.gif' alt='Loading...' title='Loading...' border='0' />";
	new Ajax.Request( ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=award&do=saveNotes&row_id=' + row + '&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: {
			         'row_id': row,
					 'notes': notesValue.encodeParam()
		},
		onSuccess: function(t)
		{
			if(t.responseText == 1)
			{
				notesArray[row] = notesValue;
				editTD.innerHTML = "<span style='color:green;'><strong>Notes Saved!</strong></span>";
				setTimeout('editNotes(' + row + ', 0, 0)', 1500);
			}
			else
			{
				alert('An error has occurred');
				editNotes(row, 0, 0);
			}
		},
	});
}