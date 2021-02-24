$(function(){
	$('.rate:not(.rate-ui)').each(function(){
		var rate=parseFloat($(this).text()), markup='';
		for(var i=0;i<5;i++){
			if(i<parseInt(rate)){
				markup+='<i class="full"></i>';
			} else if(rate-parseInt(rate)>0 && parseInt(rate)==i){
				markup+='<i><span style="width:'+((rate-parseInt(rate))*100)+'%"></span></i>';
			} 
			else {

				markup+='<i></i>';
			}
		}

		$(this).append(markup);
	})

	$('.rate-ui a').click(function(e){
		e.preventDefault();
		$(this).addClass('selected').siblings().removeClass('selected').parents('.rate-ui').addClass('has-selected').find('input').val(($(this).index()+1));
	})
})

											// <i class="full"></i>
											// <i class="full"></i>
											// <i class="full"></i>
											// <i>
											// 	<span style="width:50%;"></span>
											// </i>
											// <i></i>
