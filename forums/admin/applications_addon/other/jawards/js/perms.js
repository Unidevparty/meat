var currentStatus;
var changeTo;

function updatePerm(type, gid)
{
	if(type == "give")
	{
		currentStatus = giveAwdPerms[gid];
	}
	else if(type == "remove")
	{
		currentStatus = removeAwdPerms[gid];
	}
	else
	{
		currentStatus = receiveAwdPerms[gid];
	}
	
	if(currentStatus)
	{
		changeTo = 0;
	}
	else
	{
		changeTo = 1;
	}
	
	var permImg       = document.getElementById(type + "_" + gid);
	permImg.innerHTML = "<img src='" + stylesURL + "/images/loading.gif' title='Loading...' />";
	
	new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=perms&which=' + type + '&gid=' + gid + '&secure_key=' + ipb.vars['md5_hash'],
	{
		method: 'post',
		parameters: 
		{
		  'newperm': changeTo,
		},
		onSuccess: function(t)
		{
			if(t.responseText == "OkGo")
			{
				if(changeTo)
				{
					permImg.innerHTML = OKimg;
				}
				else
				{
					permImg.innerHTML = NOimg;
				}
				
				if(type == "give")
				{
					giveAwdPerms[gid] = changeTo;
				}
				else if(type == "remove")
				{
					removeAwdPerms[gid] = changeTo;
				}
				else
				{
					receiveAwdPerms[gid] = changeTo;
				}
			}
			else
			{
				alert("An unknow error has occurred");
				
				if(currentStatus)
				{
					permImg.innerHTML = OKimg;
				}
				else
				{
					permImg.innerHTML = NOimg;
				}
			}
		},
	});
}