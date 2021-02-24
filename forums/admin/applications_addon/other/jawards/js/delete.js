var processingRow;
var toDo;
var aid;

var processingTxt = "Processing...";

function runThrough_awards()
{
	var confirmation = confirm("Are you certain you would like to continue?");
	
	if(!confirmation)
	{
		return;
	}
	
	if(totalAwards > 0)
	{
		for(abcde in awardsArray)
		{
			awdid         = awardsArray[abcde];
			var selIdx  = document.getElementById("awardOpt_" + awdid).selectedIndex;
			var act     = document.getElementById("awardOpt_" + awdid).options[selIdx].value;
			
			if(!act)
			{
				alert("You must choose something to do for all awards.");
				
				return;
			}
		}
		
		for(x in awardsArray)
		{
			aid           = awardsArray[x];
			processingRow = document.getElementById("award_options_" + aid);
		var selectedIndex = document.getElementById("awardOpt_" + aid).selectedIndex;
			toDo          = document.getElementById("awardOpt_" + aid).options[selectedIndex].value;
			
			if(!toDo)
			{
				alert("Don't change anything!")
				
				return;
			}
			
			processAward_row();
		}
	}
	
	
}

function processAward_row()
{
	processingRow.innerHTML = processingTxt;
	
	new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=deleteCats&do=award&action=' + toDo + '&id=' + aid + '&secure_key=' + ipb.vars['md5_hash'],
		{
			method: 'post',
			onSuccess: function(t)
			{
				if(t.responseText == 1)
				{
					var putIn = "Complete!";
				}
				else
				{
					var putIn = "Failed...";
				}
			},
		});
}