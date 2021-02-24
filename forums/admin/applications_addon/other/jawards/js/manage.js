var inEditMode = false;
var editing    = 0;
var titleSpan;
var hasFailed  = false;
var field;
var saving     = false;
var newStatus  = 0;
var deleteBtnDiv;

function revealCatDeleteBtn(cat_id)
{
	deleteBtnDiv = document.getElementById("catDeleteButton_" + cat_id);
	
	if(deleteBtnDiv)
	{
		if(deleteBtnDiv.style.display == "none")
		{
			deleteBtnDiv.style.display = "block";
		}
		else
		{
			deleteBtnDiv.style.display = "none";
		}
	}
}

function editCatTitle(cat)
{
	if(!inEditMode)
	{
		inEditMode = true;
		editing    = cat;
		
		titleSpan = document.getElementById("cat_" + cat + "_name");
		
		titleSpan.innerHTML = "<input type='text' id='title' value='" + catTitles[cat] + "' onBlur='javascript: saveEdits();' />";
		field = document.getElementById("title");
		
		field.onkeydown = function(e)
		{
			var e = window.event || e;
			if(e.keyCode == 13 && !saving)
			{
				saveEdits();
			}
		}
	}
}

function saveEdits()
{	
	saving = true;
	var value = field.value;
	
	if(value != catTitles[editing] && value != "")
	{
		titleSpan.innerHTML = "Saving...";
		
		new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=manage&do=alterCatName&cat_id=' + editing + '&secure_key=' + ipb.vars['md5_hash'],
		{
			method: 'post',
			parameters: 
			{
			  'cat': editing,
			  'catName': value.encodeParam()
			},
			onSuccess: function(t)
			{
				if(t.responseText == 1)
				{
					catTitles[editing]  = value;
					titleSpan.innerHTML = value;
					inEditMode = false;
					saving     = false;
				}
				else
				{
					putFailed();
				}
			},
		});
	}
	else if(value === "")
	{
		saving = false;
		alert('You must enter something');
		field.value = catTitles[editing];
	}
	else
	{
		saving = false;
		titleSpan.innerHTML = catTitles[editing];
		inEditMode = false;
	}
}

function putFailed()
{
	if(inEditMode === true && !hasFailed)
	{
		titleSpan.innerHTML = "<span style='color:red;'>FAILED</span>";
		hasFailed           = true;
		setTimeout("revertTitle();", 2000);
	}
}

function revertTitle()
{
	titleSpan.innerHTML = catTitles[editing];
	inEditMode = false;
	hasFailed  = false;
}

function showHideInfo()
{
	var helpDiv = document.getElementById("helpDiv");
	
	if(helpDiv.style.display == "none")
	{
		helpDiv.style.display = "block";
	}
	else
	{
		helpDiv.style.display = "none";
	}
}

function changeStatus_category(id, numAwards)
{
	Debug.write( "manage.changeStatus_category( id='" + id + "' numAwards='" + numAwards + "' )" );
	Debug.info( "manage.changeStatus_category( id='" + id + "' numAwards='" + numAwards + "' )" );
	Debug.error( "manage.changeStatus_category( id='" + id + "' numAwards='" + numAwards + "' )" );
	Debug.warn( "manage.changeStatus_category( id='" + id + "' numAwards='" + numAwards + "' )" );
	//Debug.dir( "id" );
	if(!numAwards)
	{
		alert("You cannot enable a category which does not have any awards in it.");
		
		return;
	}
	
	var catEnablerImg       = document.getElementById("cat_enabler_" + id);
	catEnablerImg.innerHTML = "<img src='" + stylesURL + "/images/loading.gif' title='Loading...' />";
	
	if(catVisibility[id])
	{
		newStatus = 0;
	}
	else
	{
		newStatus = 1;
	}
	
	new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=manage&do=visibility&for=cats&cat_id=' + id + '&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: 
		{
		  'status': newStatus,
		},
		onSuccess: function(t)
		{
			if(t.responseText)
			{
				if(newStatus)
				{
					catEnablerImg.innerHTML = enabledIMG;
				}
				else
				{
					catEnablerImg.innerHTML = disabledIMG;
				}
				
				changeCatAwardsStatuses(id, newStatus);
				
				catVisibility[id] = newStatus;
			}
		},
	});
}

function changeCatAwardsStatuses(id)
{
	if(catAwards[id])
	{
		var splitIT = catAwards[id].split(",");
		
		for(i in splitIT)
		{
			if(splitIT[i])
			{
				if(awardsVisibility[splitIT[i]] && !newStatus)
				{
					document.getElementById("awards_enabler_" + splitIT[i]).innerHTML = disabledIMG;
				}
				
				if(awardsVisibility[splitIT[i]] && newStatus)
				{
					document.getElementById("awards_enabler_" + splitIT[i]).innerHTML = enabledIMG;
				}
			}
		}
	}
}

function changeStatus_awards(id, newStatus)
{
	if(!catVisibility[awardCat[id]])
	{
		alert("You cannot change the visibility of this award because it will already be hidden since the category is disabled.");
		
		return;
	}
	
	var awardsEnablerImg       = document.getElementById("awards_enabler_" + id);
	awardsEnablerImg.innerHTML = "<img src='" + stylesURL + "/images/loading.gif' title='Loading...' />";
	
	if(awardsVisibility[id])
	{
		newStatus = 0;
	}
	else
	{
		newStatus = 1;
	}
	
	new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=manage&do=visibility&for=awards&id=' + id + '&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: 
		{
		  'status': newStatus,
		},
		onSuccess: function(t)
		{
			if(t.responseText)
			{
				if(newStatus)
				{
					awardsEnablerImg.innerHTML = enabledIMG;
				}
				else
				{
					awardsEnablerImg.innerHTML = disabledIMG;
				}
				
				awardsVisibility[id] = newStatus;
			}
		},
	});
}

function changeShow(id)
{
	var ShowImg       = document.getElementById( "show_enabler_" + id );
	ShowImg.innerHTML = "<img src='" + stylesURL + "/images/loading.gif' title='Loading...' />";
	
	if( catShow[id] )
	{
		newStatus = 0;
	}
	else
	{
		newStatus = 1;
	}
	
	new Ajax.Request( ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=manage&do=catshow&id=' + id + '&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: 
		{
		  'status': newStatus,
		},
		onSuccess: function(t)
		{
			if( t.responseText )
			{
				if( newStatus )
				{
					ShowImg.innerHTML = showIMG;
				}
				else
				{
					ShowImg.innerHTML = hideIMG;
				}
				
				catShow[id] = newStatus;
			}
		},
	});
}

function addCategory()
{
	var name = prompt("Enter name for new category", "");
	
	if( name.length )
	{
		addCategory_continue( name );
	}
	return;
}

function addCategory_continue( name )
{
	new Ajax.Request(ipb.vars['base_url'] . replace("&amp;", "&") + 'app=jawards&module=ajax&section=manage&do=addCategory&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: 
		{
		  'name': name,
		},
		onSuccess: function(t)
		{
			if(t.responseText == 1)
			{
				window.location = window.location;
			}
			else
			{
				prompt("Adding this category has failed");
				return;
			}
		},
	});
}