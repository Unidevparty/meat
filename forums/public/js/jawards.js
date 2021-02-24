var loadedAwards = [];
var row;
var currentRow;

function showIT(html, award)
{
	row.innerHTML = "<div align='left'><a href='javascript: showAwarded(" + award + ", 0);'>" + lang_hide_awarded + "</a><br /><br /><div style='font-weight:100;'>" + html + "</div></div>";
}

function showAwarded(award, display)
{
	if(row == undefined || currentRow != award)
	{
		row = document.getElementById("awardRow_" + award);
		currentRow = award;
	}
	
	if(display)
	{
		row.style.width = "45%";
		row.innerHTML   = "<img src='public/style_images/master/loading.gif' title='Loading...' />";
		if( loadedAwards[award] )
		{
			showIT( loadedAwards[award], award );
		}
		else
		{
			getAwarded( award );
		}
	}
	else
	{
		row.style.width = "15%";
		row.innerHTML   = "<a href='javascript: showAwarded(" + award + ", 1);'>" + lang_show_awarded + "</a>";
	}
}

function getAwarded( award )
{
	new Ajax.Request( ipb.vars['base_url'] + "app=jawards&module=ajax&section=awards&do=getAwarded",
						{
							method: 'post',
							evalJSON: 'force',
							parameters: 
							{
								'md5check': ipb.vars['secure_hash'],
								'id': award
							},
							onSuccess: function(t)
							{
								if(t.responseText)
								{
									loadedAwards[award] = t.responseText;
									showIT(t.responseText, award);
								}
								else
								{
									showAwarded(award, 0);
								}
							}
						});
}