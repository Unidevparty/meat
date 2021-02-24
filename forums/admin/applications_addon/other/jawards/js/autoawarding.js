function changeStatus_funcs(id)
{
	var funcsEnablerImg       = document.getElementById("funcs_enabler_" + id);
	funcsEnablerImg.innerHTML = "<img src='" + stylesURL + "/images/loading.gif' title='Loading...' />";
	
	if(funcsVisibility[id])
	{
		newStatus = 0;
	}
	else
	{
		newStatus = 1;
	}
	
	new Ajax.Request(ipb.vars['base_url'].replace("&amp;", "&") + 'app=jawards&module=ajax&section=autoawarding&do=visibility&id=' + id + '&secure_key=' + ipb.vars['md5_hash'],
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
					funcsEnablerImg.innerHTML = enabledIMG;
				}
				else
				{
					funcsEnablerImg.innerHTML = disabledIMG;
				}
				
				funcsVisibility[id] = newStatus;
			}
		},
	});
}
