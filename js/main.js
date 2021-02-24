/*!
 * @fileOverview TouchSwipe - jQuery Plugin
 * @version 1.6.18
 *
 * @author Matt Bryson http://www.github.com/mattbryson
 * @see https://github.com/mattbryson/TouchSwipe-Jquery-Plugin
 * @see http://labs.rampinteractive.co.uk/touchSwipe/
 * @see http://plugins.jquery.com/project/touchSwipe
 * @license
 * Copyright (c) 2010-2015 Matt Bryson
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 */
!function(factory){"function"==typeof define&&define.amd&&define.amd.jQuery?define(["jquery"],factory):factory("undefined"!=typeof module&&module.exports?require("jquery"):jQuery)}(function($){"use strict";function init(options){return!options||void 0!==options.allowPageScroll||void 0===options.swipe&&void 0===options.swipeStatus||(options.allowPageScroll=NONE),void 0!==options.click&&void 0===options.tap&&(options.tap=options.click),options||(options={}),options=$.extend({},$.fn.swipe.defaults,options),this.each(function(){var $this=$(this),plugin=$this.data(PLUGIN_NS);plugin||(plugin=new TouchSwipe(this,options),$this.data(PLUGIN_NS,plugin))})}function TouchSwipe(element,options){function touchStart(jqEvent){if(!(getTouchInProgress()||$(jqEvent.target).closest(options.excludedElements,$element).length>0)){var event=jqEvent.originalEvent?jqEvent.originalEvent:jqEvent;if(!event.pointerType||"mouse"!=event.pointerType||0!=options.fallbackToMouseEvents){var ret,touches=event.touches,evt=touches?touches[0]:event;return phase=PHASE_START,touches?fingerCount=touches.length:options.preventDefaultEvents!==!1&&jqEvent.preventDefault(),distance=0,direction=null,currentDirection=null,pinchDirection=null,duration=0,startTouchesDistance=0,endTouchesDistance=0,pinchZoom=1,pinchDistance=0,maximumsMap=createMaximumsData(),cancelMultiFingerRelease(),createFingerData(0,evt),!touches||fingerCount===options.fingers||options.fingers===ALL_FINGERS||hasPinches()?(startTime=getTimeStamp(),2==fingerCount&&(createFingerData(1,touches[1]),startTouchesDistance=endTouchesDistance=calculateTouchesDistance(fingerData[0].start,fingerData[1].start)),(options.swipeStatus||options.pinchStatus)&&(ret=triggerHandler(event,phase))):ret=!1,ret===!1?(phase=PHASE_CANCEL,triggerHandler(event,phase),ret):(options.hold&&(holdTimeout=setTimeout($.proxy(function(){$element.trigger("hold",[event.target]),options.hold&&(ret=options.hold.call($element,event,event.target))},this),options.longTapThreshold)),setTouchInProgress(!0),null)}}}function touchMove(jqEvent){var event=jqEvent.originalEvent?jqEvent.originalEvent:jqEvent;if(phase!==PHASE_END&&phase!==PHASE_CANCEL&&!inMultiFingerRelease()){var ret,touches=event.touches,evt=touches?touches[0]:event,currentFinger=updateFingerData(evt);if(endTime=getTimeStamp(),touches&&(fingerCount=touches.length),options.hold&&clearTimeout(holdTimeout),phase=PHASE_MOVE,2==fingerCount&&(0==startTouchesDistance?(createFingerData(1,touches[1]),startTouchesDistance=endTouchesDistance=calculateTouchesDistance(fingerData[0].start,fingerData[1].start)):(updateFingerData(touches[1]),endTouchesDistance=calculateTouchesDistance(fingerData[0].end,fingerData[1].end),pinchDirection=calculatePinchDirection(fingerData[0].end,fingerData[1].end)),pinchZoom=calculatePinchZoom(startTouchesDistance,endTouchesDistance),pinchDistance=Math.abs(startTouchesDistance-endTouchesDistance)),fingerCount===options.fingers||options.fingers===ALL_FINGERS||!touches||hasPinches()){if(direction=calculateDirection(currentFinger.start,currentFinger.end),currentDirection=calculateDirection(currentFinger.last,currentFinger.end),validateDefaultEvent(jqEvent,currentDirection),distance=calculateDistance(currentFinger.start,currentFinger.end),duration=calculateDuration(),setMaxDistance(direction,distance),ret=triggerHandler(event,phase),!options.triggerOnTouchEnd||options.triggerOnTouchLeave){var inBounds=!0;if(options.triggerOnTouchLeave){var bounds=getbounds(this);inBounds=isInBounds(currentFinger.end,bounds)}!options.triggerOnTouchEnd&&inBounds?phase=getNextPhase(PHASE_MOVE):options.triggerOnTouchLeave&&!inBounds&&(phase=getNextPhase(PHASE_END)),phase!=PHASE_CANCEL&&phase!=PHASE_END||triggerHandler(event,phase)}}else phase=PHASE_CANCEL,triggerHandler(event,phase);ret===!1&&(phase=PHASE_CANCEL,triggerHandler(event,phase))}}function touchEnd(jqEvent){var event=jqEvent.originalEvent?jqEvent.originalEvent:jqEvent,touches=event.touches;if(touches){if(touches.length&&!inMultiFingerRelease())return startMultiFingerRelease(event),!0;if(touches.length&&inMultiFingerRelease())return!0}return inMultiFingerRelease()&&(fingerCount=fingerCountAtRelease),endTime=getTimeStamp(),duration=calculateDuration(),didSwipeBackToCancel()||!validateSwipeDistance()?(phase=PHASE_CANCEL,triggerHandler(event,phase)):options.triggerOnTouchEnd||options.triggerOnTouchEnd===!1&&phase===PHASE_MOVE?(options.preventDefaultEvents!==!1&&jqEvent.cancelable!==!1&&jqEvent.preventDefault(),phase=PHASE_END,triggerHandler(event,phase)):!options.triggerOnTouchEnd&&hasTap()?(phase=PHASE_END,triggerHandlerForGesture(event,phase,TAP)):phase===PHASE_MOVE&&(phase=PHASE_CANCEL,triggerHandler(event,phase)),setTouchInProgress(!1),null}function touchCancel(){fingerCount=0,endTime=0,startTime=0,startTouchesDistance=0,endTouchesDistance=0,pinchZoom=1,cancelMultiFingerRelease(),setTouchInProgress(!1)}function touchLeave(jqEvent){var event=jqEvent.originalEvent?jqEvent.originalEvent:jqEvent;options.triggerOnTouchLeave&&(phase=getNextPhase(PHASE_END),triggerHandler(event,phase))}function removeListeners(){$element.off(START_EV,touchStart),$element.off(CANCEL_EV,touchCancel),$element.off(MOVE_EV,touchMove),$element.off(END_EV,touchEnd),LEAVE_EV&&$element.off(LEAVE_EV,touchLeave),setTouchInProgress(!1)}function getNextPhase(currentPhase){var nextPhase=currentPhase,validTime=validateSwipeTime(),validDistance=validateSwipeDistance(),didCancel=didSwipeBackToCancel();return!validTime||didCancel?nextPhase=PHASE_CANCEL:!validDistance||currentPhase!=PHASE_MOVE||options.triggerOnTouchEnd&&!options.triggerOnTouchLeave?!validDistance&&currentPhase==PHASE_END&&options.triggerOnTouchLeave&&(nextPhase=PHASE_CANCEL):nextPhase=PHASE_END,nextPhase}function triggerHandler(event,phase){var ret,touches=event.touches;return(didSwipe()||hasSwipes())&&(ret=triggerHandlerForGesture(event,phase,SWIPE)),(didPinch()||hasPinches())&&ret!==!1&&(ret=triggerHandlerForGesture(event,phase,PINCH)),didDoubleTap()&&ret!==!1?ret=triggerHandlerForGesture(event,phase,DOUBLE_TAP):didLongTap()&&ret!==!1?ret=triggerHandlerForGesture(event,phase,LONG_TAP):didTap()&&ret!==!1&&(ret=triggerHandlerForGesture(event,phase,TAP)),phase===PHASE_CANCEL&&touchCancel(event),phase===PHASE_END&&(touches?touches.length||touchCancel(event):touchCancel(event)),ret}function triggerHandlerForGesture(event,phase,gesture){var ret;if(gesture==SWIPE){if($element.trigger("swipeStatus",[phase,direction||null,distance||0,duration||0,fingerCount,fingerData,currentDirection]),options.swipeStatus&&(ret=options.swipeStatus.call($element,event,phase,direction||null,distance||0,duration||0,fingerCount,fingerData,currentDirection),ret===!1))return!1;if(phase==PHASE_END&&validateSwipe()){if(clearTimeout(singleTapTimeout),clearTimeout(holdTimeout),$element.trigger("swipe",[direction,distance,duration,fingerCount,fingerData,currentDirection]),options.swipe&&(ret=options.swipe.call($element,event,direction,distance,duration,fingerCount,fingerData,currentDirection),ret===!1))return!1;switch(direction){case LEFT:$element.trigger("swipeLeft",[direction,distance,duration,fingerCount,fingerData,currentDirection]),options.swipeLeft&&(ret=options.swipeLeft.call($element,event,direction,distance,duration,fingerCount,fingerData,currentDirection));break;case RIGHT:$element.trigger("swipeRight",[direction,distance,duration,fingerCount,fingerData,currentDirection]),options.swipeRight&&(ret=options.swipeRight.call($element,event,direction,distance,duration,fingerCount,fingerData,currentDirection));break;case UP:$element.trigger("swipeUp",[direction,distance,duration,fingerCount,fingerData,currentDirection]),options.swipeUp&&(ret=options.swipeUp.call($element,event,direction,distance,duration,fingerCount,fingerData,currentDirection));break;case DOWN:$element.trigger("swipeDown",[direction,distance,duration,fingerCount,fingerData,currentDirection]),options.swipeDown&&(ret=options.swipeDown.call($element,event,direction,distance,duration,fingerCount,fingerData,currentDirection))}}}if(gesture==PINCH){if($element.trigger("pinchStatus",[phase,pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData]),options.pinchStatus&&(ret=options.pinchStatus.call($element,event,phase,pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData),ret===!1))return!1;if(phase==PHASE_END&&validatePinch())switch(pinchDirection){case IN:$element.trigger("pinchIn",[pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData]),options.pinchIn&&(ret=options.pinchIn.call($element,event,pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData));break;case OUT:$element.trigger("pinchOut",[pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData]),options.pinchOut&&(ret=options.pinchOut.call($element,event,pinchDirection||null,pinchDistance||0,duration||0,fingerCount,pinchZoom,fingerData))}}return gesture==TAP?phase!==PHASE_CANCEL&&phase!==PHASE_END||(clearTimeout(singleTapTimeout),clearTimeout(holdTimeout),hasDoubleTap()&&!inDoubleTap()?(doubleTapStartTime=getTimeStamp(),singleTapTimeout=setTimeout($.proxy(function(){doubleTapStartTime=null,$element.trigger("tap",[event.target]),options.tap&&(ret=options.tap.call($element,event,event.target))},this),options.doubleTapThreshold)):(doubleTapStartTime=null,$element.trigger("tap",[event.target]),options.tap&&(ret=options.tap.call($element,event,event.target)))):gesture==DOUBLE_TAP?phase!==PHASE_CANCEL&&phase!==PHASE_END||(clearTimeout(singleTapTimeout),clearTimeout(holdTimeout),doubleTapStartTime=null,$element.trigger("doubletap",[event.target]),options.doubleTap&&(ret=options.doubleTap.call($element,event,event.target))):gesture==LONG_TAP&&(phase!==PHASE_CANCEL&&phase!==PHASE_END||(clearTimeout(singleTapTimeout),doubleTapStartTime=null,$element.trigger("longtap",[event.target]),options.longTap&&(ret=options.longTap.call($element,event,event.target)))),ret}function validateSwipeDistance(){var valid=!0;return null!==options.threshold&&(valid=distance>=options.threshold),valid}function didSwipeBackToCancel(){var cancelled=!1;return null!==options.cancelThreshold&&null!==direction&&(cancelled=getMaxDistance(direction)-distance>=options.cancelThreshold),cancelled}function validatePinchDistance(){return null!==options.pinchThreshold?pinchDistance>=options.pinchThreshold:!0}function validateSwipeTime(){var result;return result=options.maxTimeThreshold?!(duration>=options.maxTimeThreshold):!0}function validateDefaultEvent(jqEvent,direction){if(options.preventDefaultEvents!==!1)if(options.allowPageScroll===NONE)jqEvent.preventDefault();else{var auto=options.allowPageScroll===AUTO;switch(direction){case LEFT:(options.swipeLeft&&auto||!auto&&options.allowPageScroll!=HORIZONTAL)&&jqEvent.preventDefault();break;case RIGHT:(options.swipeRight&&auto||!auto&&options.allowPageScroll!=HORIZONTAL)&&jqEvent.preventDefault();break;case UP:(options.swipeUp&&auto||!auto&&options.allowPageScroll!=VERTICAL)&&jqEvent.preventDefault();break;case DOWN:(options.swipeDown&&auto||!auto&&options.allowPageScroll!=VERTICAL)&&jqEvent.preventDefault();break;case NONE:}}}function validatePinch(){var hasCorrectFingerCount=validateFingers(),hasEndPoint=validateEndPoint(),hasCorrectDistance=validatePinchDistance();return hasCorrectFingerCount&&hasEndPoint&&hasCorrectDistance}function hasPinches(){return!!(options.pinchStatus||options.pinchIn||options.pinchOut)}function didPinch(){return!(!validatePinch()||!hasPinches())}function validateSwipe(){var hasValidTime=validateSwipeTime(),hasValidDistance=validateSwipeDistance(),hasCorrectFingerCount=validateFingers(),hasEndPoint=validateEndPoint(),didCancel=didSwipeBackToCancel(),valid=!didCancel&&hasEndPoint&&hasCorrectFingerCount&&hasValidDistance&&hasValidTime;return valid}function hasSwipes(){return!!(options.swipe||options.swipeStatus||options.swipeLeft||options.swipeRight||options.swipeUp||options.swipeDown)}function didSwipe(){return!(!validateSwipe()||!hasSwipes())}function validateFingers(){return fingerCount===options.fingers||options.fingers===ALL_FINGERS||!SUPPORTS_TOUCH}function validateEndPoint(){return 0!==fingerData[0].end.x}function hasTap(){return!!options.tap}function hasDoubleTap(){return!!options.doubleTap}function hasLongTap(){return!!options.longTap}function validateDoubleTap(){if(null==doubleTapStartTime)return!1;var now=getTimeStamp();return hasDoubleTap()&&now-doubleTapStartTime<=options.doubleTapThreshold}function inDoubleTap(){return validateDoubleTap()}function validateTap(){return(1===fingerCount||!SUPPORTS_TOUCH)&&(isNaN(distance)||distance<options.threshold)}function validateLongTap(){return duration>options.longTapThreshold&&DOUBLE_TAP_THRESHOLD>distance}function didTap(){return!(!validateTap()||!hasTap())}function didDoubleTap(){return!(!validateDoubleTap()||!hasDoubleTap())}function didLongTap(){return!(!validateLongTap()||!hasLongTap())}function startMultiFingerRelease(event){previousTouchEndTime=getTimeStamp(),fingerCountAtRelease=event.touches.length+1}function cancelMultiFingerRelease(){previousTouchEndTime=0,fingerCountAtRelease=0}function inMultiFingerRelease(){var withinThreshold=!1;if(previousTouchEndTime){var diff=getTimeStamp()-previousTouchEndTime;diff<=options.fingerReleaseThreshold&&(withinThreshold=!0)}return withinThreshold}function getTouchInProgress(){return!($element.data(PLUGIN_NS+"_intouch")!==!0)}function setTouchInProgress(val){$element&&(val===!0?($element.on(MOVE_EV,touchMove),$element.on(END_EV,touchEnd),LEAVE_EV&&$element.on(LEAVE_EV,touchLeave)):($element.off(MOVE_EV,touchMove,!1),$element.off(END_EV,touchEnd,!1),LEAVE_EV&&$element.off(LEAVE_EV,touchLeave,!1)),$element.data(PLUGIN_NS+"_intouch",val===!0))}function createFingerData(id,evt){var f={start:{x:0,y:0},last:{x:0,y:0},end:{x:0,y:0}};return f.start.x=f.last.x=f.end.x=evt.pageX||evt.clientX,f.start.y=f.last.y=f.end.y=evt.pageY||evt.clientY,fingerData[id]=f,f}function updateFingerData(evt){var id=void 0!==evt.identifier?evt.identifier:0,f=getFingerData(id);return null===f&&(f=createFingerData(id,evt)),f.last.x=f.end.x,f.last.y=f.end.y,f.end.x=evt.pageX||evt.clientX,f.end.y=evt.pageY||evt.clientY,f}function getFingerData(id){return fingerData[id]||null}function setMaxDistance(direction,distance){direction!=NONE&&(distance=Math.max(distance,getMaxDistance(direction)),maximumsMap[direction].distance=distance)}function getMaxDistance(direction){return maximumsMap[direction]?maximumsMap[direction].distance:void 0}function createMaximumsData(){var maxData={};return maxData[LEFT]=createMaximumVO(LEFT),maxData[RIGHT]=createMaximumVO(RIGHT),maxData[UP]=createMaximumVO(UP),maxData[DOWN]=createMaximumVO(DOWN),maxData}function createMaximumVO(dir){return{direction:dir,distance:0}}function calculateDuration(){return endTime-startTime}function calculateTouchesDistance(startPoint,endPoint){var diffX=Math.abs(startPoint.x-endPoint.x),diffY=Math.abs(startPoint.y-endPoint.y);return Math.round(Math.sqrt(diffX*diffX+diffY*diffY))}function calculatePinchZoom(startDistance,endDistance){var percent=endDistance/startDistance*1;return percent.toFixed(2)}function calculatePinchDirection(){return 1>pinchZoom?OUT:IN}function calculateDistance(startPoint,endPoint){return Math.round(Math.sqrt(Math.pow(endPoint.x-startPoint.x,2)+Math.pow(endPoint.y-startPoint.y,2)))}function calculateAngle(startPoint,endPoint){var x=startPoint.x-endPoint.x,y=endPoint.y-startPoint.y,r=Math.atan2(y,x),angle=Math.round(180*r/Math.PI);return 0>angle&&(angle=360-Math.abs(angle)),angle}function calculateDirection(startPoint,endPoint){if(comparePoints(startPoint,endPoint))return NONE;var angle=calculateAngle(startPoint,endPoint);return 45>=angle&&angle>=0?LEFT:360>=angle&&angle>=315?LEFT:angle>=135&&225>=angle?RIGHT:angle>45&&135>angle?DOWN:UP}function getTimeStamp(){var now=new Date;return now.getTime()}function getbounds(el){el=$(el);var offset=el.offset(),bounds={left:offset.left,right:offset.left+el.outerWidth(),top:offset.top,bottom:offset.top+el.outerHeight()};return bounds}function isInBounds(point,bounds){return point.x>bounds.left&&point.x<bounds.right&&point.y>bounds.top&&point.y<bounds.bottom}function comparePoints(pointA,pointB){return pointA.x==pointB.x&&pointA.y==pointB.y}var options=$.extend({},options),useTouchEvents=SUPPORTS_TOUCH||SUPPORTS_POINTER||!options.fallbackToMouseEvents,START_EV=useTouchEvents?SUPPORTS_POINTER?SUPPORTS_POINTER_IE10?"MSPointerDown":"pointerdown":"touchstart":"mousedown",MOVE_EV=useTouchEvents?SUPPORTS_POINTER?SUPPORTS_POINTER_IE10?"MSPointerMove":"pointermove":"touchmove":"mousemove",END_EV=useTouchEvents?SUPPORTS_POINTER?SUPPORTS_POINTER_IE10?"MSPointerUp":"pointerup":"touchend":"mouseup",LEAVE_EV=useTouchEvents?SUPPORTS_POINTER?"mouseleave":null:"mouseleave",CANCEL_EV=SUPPORTS_POINTER?SUPPORTS_POINTER_IE10?"MSPointerCancel":"pointercancel":"touchcancel",distance=0,direction=null,currentDirection=null,duration=0,startTouchesDistance=0,endTouchesDistance=0,pinchZoom=1,pinchDistance=0,pinchDirection=0,maximumsMap=null,$element=$(element),phase="start",fingerCount=0,fingerData={},startTime=0,endTime=0,previousTouchEndTime=0,fingerCountAtRelease=0,doubleTapStartTime=0,singleTapTimeout=null,holdTimeout=null;try{$element.on(START_EV,touchStart),$element.on(CANCEL_EV,touchCancel)}catch(e){$.error("events not supported "+START_EV+","+CANCEL_EV+" on jQuery.swipe")}this.enable=function(){return this.disable(),$element.on(START_EV,touchStart),$element.on(CANCEL_EV,touchCancel),$element},this.disable=function(){return removeListeners(),$element},this.destroy=function(){removeListeners(),$element.data(PLUGIN_NS,null),$element=null},this.option=function(property,value){if("object"==typeof property)options=$.extend(options,property);else if(void 0!==options[property]){if(void 0===value)return options[property];options[property]=value}else{if(!property)return options;$.error("Option "+property+" does not exist on jQuery.swipe.options")}return null}}var VERSION="1.6.18",LEFT="left",RIGHT="right",UP="up",DOWN="down",IN="in",OUT="out",NONE="none",AUTO="auto",SWIPE="swipe",PINCH="pinch",TAP="tap",DOUBLE_TAP="doubletap",LONG_TAP="longtap",HORIZONTAL="horizontal",VERTICAL="vertical",ALL_FINGERS="all",DOUBLE_TAP_THRESHOLD=10,PHASE_START="start",PHASE_MOVE="move",PHASE_END="end",PHASE_CANCEL="cancel",SUPPORTS_TOUCH="ontouchstart"in window,SUPPORTS_POINTER_IE10=window.navigator.msPointerEnabled&&!window.PointerEvent&&!SUPPORTS_TOUCH,SUPPORTS_POINTER=(window.PointerEvent||window.navigator.msPointerEnabled)&&!SUPPORTS_TOUCH,PLUGIN_NS="TouchSwipe",defaults={fingers:1,threshold:75,cancelThreshold:null,pinchThreshold:20,maxTimeThreshold:null,fingerReleaseThreshold:250,longTapThreshold:500,doubleTapThreshold:200,swipe:null,swipeLeft:null,swipeRight:null,swipeUp:null,swipeDown:null,swipeStatus:null,pinchIn:null,pinchOut:null,pinchStatus:null,click:null,tap:null,doubleTap:null,longTap:null,hold:null,triggerOnTouchEnd:!0,triggerOnTouchLeave:!1,allowPageScroll:"auto",fallbackToMouseEvents:!0,excludedElements:".noSwipe",preventDefaultEvents:!0};$.fn.swipe=function(method){var $this=$(this),plugin=$this.data(PLUGIN_NS);if(plugin&&"string"==typeof method){if(plugin[method])return plugin[method].apply(plugin,Array.prototype.slice.call(arguments,1));$.error("Method "+method+" does not exist on jQuery.swipe")}else if(plugin&&"object"==typeof method)plugin.option.apply(plugin,arguments);else if(!(plugin||"object"!=typeof method&&method))return init.apply(this,arguments);return $this},$.fn.swipe.version=VERSION,$.fn.swipe.defaults=defaults,$.fn.swipe.phases={PHASE_START:PHASE_START,PHASE_MOVE:PHASE_MOVE,PHASE_END:PHASE_END,PHASE_CANCEL:PHASE_CANCEL},$.fn.swipe.directions={LEFT:LEFT,RIGHT:RIGHT,UP:UP,DOWN:DOWN,IN:IN,OUT:OUT},$.fn.swipe.pageScroll={NONE:NONE,HORIZONTAL:HORIZONTAL,VERTICAL:VERTICAL,AUTO:AUTO},$.fn.swipe.fingers={ONE:1,TWO:2,THREE:3,FOUR:4,FIVE:5,ALL:ALL_FINGERS}});



function load_more() {
	$('.load_more').click(function() {
		var url = $(this).attr('href');

		$.get(url, function(data) {
			$('.load_more').remove();
			$(data).insertBefore('.load_more_before');

			setTimeout(load_more, 200);
		})

		return false;
	});
}

$(function(){

	$('.article table').each(function() {
		$(this).addClass('table').css('width', '').wrap("<div class='table-responsive'></div>");
		$(this).parent().prepend('<div class="table-helper"></div>');
	});

	$('.notifications_link').click(function() {
		var url = $(this).data('url');
		$.get(url, function(data) {
			$('.notifications_wrap').html(data.data);
		});
	});

	$('.message_link').click(function() {
		var url = $(this).data('url');
		$.get(url, function(data) {
			$('.messages_wrap').html(data.data);
		});
	});

    $('.warning_link').click(function() {
		var url = $(this).data('url');
		$.get(url, function(data) {
            $('.warnings_wrap').html($(data.data).html());
		});
	});

	load_more();

	$('.comment_delete').click(function() {
		return confirm('Подтвердите действие');
	});


	// checkwidth
	var ifMob=false, ifTab=false;
	if($(window).width()<=1024){ifTab=true};
	if($(window).width()<=768){ifMob=true};


	/*if(ifTab){
		$('#forum-prime .forum-thumbs').append($('#forum-additional').html());
		$('#forum-prime').insertAfter('#forum-additional');
		$('#forum-additional').remove();
		$('#news-prime').append($('#news-additional').html());
		$('#news-additional').remove();
		if(!ifMob){
			$('#news-tab-additional').append($('#news-prime .news-thumb').slice(-5));
			$('#news-tab-additional').append($('#news-prime>.btn'));
		}
	}*/
	$('#forum-additional .forum-thumb').addClass('cloned')
	$('#forum-prime').clone().insertAfter('#forum-additional').addClass('lap-hide');
	$('.banner-through-add.banners-row').insertBefore('.events-header');
	$('#forum-additional+#forum-prime .forum-thumbs').append($('#forum-additional').clone().html());
	$('#news-prime').append($('#news-additional').html());
	$('#news-tab-additional').append($('#news-prime .news-thumb').slice(-5).clone());
	$('#news-tab-additional').append($('#news-prime>.btn'));

	$('.events-cell .side-banner-fix.bottom-banner-fix').append($('.events-cell .events-header .btn').clone());

	//$('#news-additional').remove();


	$('.filter-toggle').click(function(e){
		e.preventDefault();
		$(this).toggleClass('clicked');
		$('#'+$(this).attr('href')).slideToggle();
	})

	// pop up
	function close_pop(pop){
	  var pop=pop || $('.pop-up:visible'), glow=$('.pop-fade');
	  $('html').removeClass('pop-called');
	  pop.removeClass('pop-shown');
	  glow.hide();
	}
	$('.pop-close, .pop-up .close, .pop-fade').click(function(e){
	  e.preventDefault();
	  var pop=($(this).parents('.pop-up').length) ? $(this).parents('.pop-up') : $('.pop-up:visible');
	  close_pop(pop);
	})
	$('[data-pop-link]').click(function(e){
	  e.preventDefault();
	  var pop=$('.pop-up[data-pop="'+$(this).attr('data-pop-link')+'"]');
	  $('html').addClass('pop-called');
	  $('.pop-fade').show().css('height',$(document).height());
	  pop.addClass('pop-shown').css({'top':$(window).scrollTop()+$(window).height()/2-pop.height()/2, "marginLeft":pop.width()/-2});
	})


	// slider
    var tch=false, autop=true;
	if (Modernizr.touch) {
	  var tch=true;
	  autop=false;
	}


	$('.content-slider-itself').each(function(){
	    var $this = $(this);
	    $this.data('linkedEl', $this.bxSlider({
	        auto: autop,
	        controls: true,
	        pager: true,
	        pause: 6500,
	        autoHover: true,
	        speed:500,
	        adaptiveHeight: true,
	        prevText:'',
	        nextText:'',
	        touchEnabled:false,
	        preventDefaultSwipeY: false

	    }));
	})


	if (Modernizr.touch) {
		jQuery('.content-slider').swipe( {
		    swipeStatus:function(event, phase, direction, distance, duration, fingerCount, fingerData, currentDirection)
		    {
		        if (phase=="start"){
		        } 
		        if (phase=="end"){ 
		            var sld=$('.content-slider-itself').data('linkedEl');
		            if (direction == 'left') {
		            	sld.goToNextSlide();
		            }
		            if (direction == 'right') {
		            	sld.goToPrevSlide();
		            }
		            if (direction == 'up') {
		            }
		            if (direction == 'down') {
		            }
		      }
		 },
		 triggerOnTouchEnd:false,
		 threshold:30,
		 allowPageScroll:"vertical",
		});
	}

	var qSlides=5;
	if($(window).width()<1200){
		qSlides=4;
	}
	if(ifTab){
		qSlides=3;
	}
	if(ifMob){
		qSlides=2;
	}

	$('.brands-carusel-proper').each(function(){
	    var $this = $(this);
	    $this.data('linkedEl', $this.bxSlider({
	    	slideWidth: 20000,
	    	minSlides: qSlides,
	    	maxSlides: qSlides,
	    	slideMargin: 0,
	    	pager:false,
	    	prevText:'',
	    	nextText:''
	    }));
	})

	if(ifMob){
		$('.event-thumbs').each(function(){
		    var $this = $(this);
		    $this.data('linkedEl', $this.bxSlider({
		    	slideWidth: 20000,
		    	minSlides: qSlides,
		    	maxSlides: qSlides,
		    	slideMargin: 16,
		    	pager:false,
		    	prevText:'',
		    	nextText:''
		    }));
		})
		$('.content-row-interviews .content-cells').each(function(){
		    var $this = $(this);
		    $this.data('linkedEl', $this.bxSlider({
		        auto: false,
		        controls: true,
		        pager: false,
		        pause: 6500,
		        autoHover: true,
		        speed:500,
		        adaptiveHeight: false,
		        touchEnabled:false,
		        prevText:'',
		        nextText:''
		    }));
		})
	}

	function resizedw(){
		qSlides=5;
		if($(window).width()<1200){
			qSlides=4;
		}
		if($(window).width()<1024){
			qSlides=3;
		};
		if($(window).width()<768){
			qSlides=2;
		};

		$('.event-thumbs').each(function(){
			var x = $(this).data('linkedEl');
			x.destroySlider();
			if(qSlides==2){
				$('.event-thumbs').each(function(){
				    var $this = $(this);
				    $this.data('linkedEl', $this.bxSlider({
				    	slideWidth: 20000,
				    	minSlides: qSlides,
				    	maxSlides: qSlides,
				    	slideMargin: 16,
				    	pager:false,
				    	prevText:'',
				    	nextText:''
				    }));
				})

			}
		})

		$('.brands-carusel-proper').each(function(){
		    var slider = $(this).data('linkedEl');
		    slider.reloadSlider({
		    	slideWidth: 20000,
		    	minSlides: qSlides,
		    	maxSlides: qSlides,
		    	slideMargin: 0,
		    	pager:false,
		    	prevText:'',
		    	nextText:''
		    });
		})
	}


	var doit;
	window.onresize = function(){
  		clearTimeout(doit);
  		doit = setTimeout(resizedw, 100);
	};




	$('.gallery-thumb').each(function(){
		$(this).find('a').append('<span class="bg" style="background-image:url('+$(this).find('img').attr('src')+')"/>');
	})


	$('.header-nav-toggle').click(function(e){
		e.preventDefault();
		$('html').removeClass('share-called');
		$(this).toggleClass('on').parents('html').toggleClass('additional-nav-called');
		$('.additional-navi ul').html($('.header-nav-prime li').clone().slice(-($('.header-nav-prime li:hidden').length)));
		if(ifMob){
			if($('html').hasClass('additional-nav-called')){
				document.ontouchmove = function (e) {
				  e.preventDefault();
				}
			}
			else{
				document.ontouchmove = function (e) {
				  return true;
				}
			}
		}
	})


	$('.personal-area-tools>li>a').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		$('html').removeClass('share-called');
		$(this).parents('li').siblings('li').find('.personal-area-drop').hide().end().find('a.on').removeClass('on');
		$(this).toggleClass('on').siblings('.personal-area-drop').toggle();
		if($('.personal-area-drop:visible').length){
			$('html').addClass('menu-called');
			if(ifMob){
				document.ontouchmove = function (e) {
				  e.preventDefault();
				}
			}
		}
		else{
			$('html').removeClass('menu-called');
			if(ifMob){
				document.ontouchmove = function (e) {
				  return true;
				}
			}
		}
	})
	$(window).click(function() {
		if($('.personal-area-drop:visible').length){
			$('.personal-area-drop').hide();
			$('html').removeClass('menu-called');
			if(ifMob){
				document.ontouchmove = function (e) {
				  return true;
				}
			}
		}
	});

	$('.personal-area-drop').click(function(event){
	    event.stopPropagation();
	});


	$('.searh-call, .close-search').click(function(e){
		e.preventDefault();
		$('html').toggleClass('search-called');
		if($("html").is('.search-called')){
			$('.form-search input[type="text"]').focus();
		}
	})

	$('.share-call').click(function(e){
		e.preventDefault();
		$('html').toggleClass('share-called');
	})

	$('.scroll-top').click(function(e){
		e.preventDefault()
		$('body,html').animate({'scrollTop':0},400)
	})
	$('body,html').animate({'scrollTop':$(window).scrollTop()-1},20);

	$(window).scroll(function(){
		if($(window).scrollTop()>375){
			$('html').addClass('navi-fixed');
			$('.side-panel').css('top','57px');
		}
		else{
			$('html').removeClass('navi-fixed');
			$('.side-panel').css('top',432-$(window).scrollTop()+'px');
		}
	})


	$('.mob-pop .close-pop').click(function(e){
		e.preventDefault();
		$('.mob-pop').fadeOut();
		$('html').removeClass('mob-pop-called')
	})

	$('.subscription-form input[type="text"]').focus(function(){
		$(this).parents('.input-place').addClass('focus')
	}).blur(function(){
		$(this).parents('.input-place').removeClass('focus')
	})

	$('.custom-select').selectmenu();


	$('.article-header:not(.news-header) .article-header-in').each(function(){
		$(this).append('<div class="article-header-bg-div" style="background-image:url('+$(this).find('.article-header-bg').attr('src')+')" />');
	})


	if($('.scrollable').length){
		$('.scrollable').jScrollPane(
		  	{
			    autoReinitialise: true
		  	}
		);

	}



	/*

	// window scroll

	// accordeon content
	$('.acc-h').click(function(e){
		e.preventDefault();
		$(this).parents('.acc').toggleClass('show').find('.acc-ct').slideToggle();
	})

	// navi for mobile
	$('.navi-close, .navi-call').click(function(e){
		e.preventDefault();
		$('html').toggleClass('navi-called');
		if($('html').hasClass('navi-called')){
			$(document).bind('touchmove',function (e) {
  				e.preventDefault();
			})
		}
		else{
			$(document).unbind('touchmove')

		}
	})

	// index slider
	$('.index-slider ul').bxSlider({
		controls:false
	})

	// index carusel
	var qSlides=5;
	if($(window).width()<992){
		qSlides=3
	}
	if($(window).width()<600){
		qSlides=1
	}
	$('.items-roll ul').bxSlider({
		slideWidth: 20000,
		minSlides: qSlides,
		maxSlides: qSlides,
		slideMargin: 0,
		pager:false,
		prevText:'',
		nextText:''
	})

	// testimonials slider
	var slidesAlso=4;
	if(ifMob){
		slidesAlso=2;
		setTimeout(function(){
			$('.items-list-testimonials').siblings('.show-more').remove().end().wrap('<div class="slider-container tst-container" />').bxSlider({
				slideWidth: 2000,
				slideMargin: 0,
				pager:false,
				prevText:'',
				nextText:''
			})
		},300)
	}
	$('.item-list-also').wrap('<div class="slider-container" />').bxSlider({
		slideWidth: 2000,
		minSlides: slidesAlso,
		maxSlides: slidesAlso,
		slideMargin: 0,
		pager:false,
		prevText:'',
		nextText:''
	})

	// item carusel
	$('.go-top').click(function(e){
		e.preventDefault()
		$('body,html').animate({'scrollTop':0},400)
	})


	// item carusel
	$('.image-thumbs ul').bxSlider({
		slideWidth: 2000,
		minSlides: 3,
		maxSlides: 3,
		slideMargin: 0,
		pager:false,
		prevText:'',
		nextText:''
	})
	$('.image-thumbs').on("click", 'li a', function(e){
		e.preventDefault();
		$(this).parents('li').addClass('ac').siblings().removeClass('ac').parents('.image-complex').find('.image-big img').attr('src',$(this).attr('href'));
	})
	// item page pop-up
	$('.image-complex').on('click','.image-big>a',function(e){
		e.preventDefault();
		if($(window).width()>1024){
			var papa=$(this).parents('.image-complex'), cnt=papa.find('.item-popup-cnt').html();
			$('body').append('<div class="pop-glow-generated" />').append('<div class="pop-up-generated" />')
			$('.pop-up-generated').html('<div class="in"></div><a class="pop-close-generated" href=""><i class="icon icon-cross"></i></a>');
			$('.pop-up-generated .in').html(cnt);
			$('.pop-up-generated .images-slideshow ul').each(function(){
				var $this = $(this);
		        $this.data('linkedEl', $this.bxSlider({
					pager:false,
					prevText:'',
					nextText:'',
					onSlideAfter:function($slideElement, oldIndex, newIndex){
						$(this).parents('.pop-up-generated').find('.colors-select li:eq('+newIndex+')').addClass('active').siblings().removeClass('active');
					}
				}));
			});
			$('.pop-up-generated').css({'top':$(window).scrollTop()+$(window).height()/2-$('.pop-up-generated').height()/2});
			$('.pop-up-generated .colors-select li').click(function(){
				var slider=$(this).parents('.pop-up-generated').find('.images-slideshow ul').data('linkedEl');
				slider.goToSlide($(this).index());
			})
		}
	})
	$('body').on('click','.pop-close-generated, .pop-glow-generated',function(e){
		e.preventDefault();
		$('.pop-glow-generated, .pop-up-generated').remove();
	})

	// item page false select
	$('.false-select .current').click(function(e){
		e.preventDefault();
		$(this).parents('.false-select').toggleClass('opened').find('.false-select-vars').slideToggle();
	})
	$('.false-select-vars li').click(function(e){
		e.preventDefault();
		$(this).find("a").addClass('c-current').end().siblings('li').find('.c-current').removeClass('c-current');
		$(this).parents('.false-select').find('.current span').text($(this).text()).end().find('.false-select-vars').slideToggle();
	})


	// inspiration scroll
	if(!ifMob){
		if($('.scrollable').length){
			$('.scrollable').jScrollPane(
			  	{
				    contentWidth: '0px',
				    autoReinitialise: true
			  	}
			);

		}
	}
	else{
		if($('.scrollable').length){
			$('.scrollable').jScrollPane(
			  	{
				    autoReinitialise: true
			  	}
			);

		}
	}
	$('.inspiration-recipe .h').click(function(){
		$(this).parents('.inspiration-recipe').toggleClass('shown');
	})

	// tabs
	$('.tabs-controls li').click(function(e){
		e.preventDefault();
		$(this).addClass('active').siblings().removeClass('active').parents('.tabs').find('.tabs-content').find('.tab:eq('+$(this).index()+')').addClass('on').siblings().removeClass('on')
	}).first().click();

	// amount
	$('.ammount-control a').click(function(e){
		e.preventDefault();
		var inp=$(this).siblings('input'), amt=parseInt(inp.val());
		if($(this).is('.qty-up')){
			inp.val(++amt);
		}
		else{
			if(amt>1){
				inp.val(--amt)
			}
		}
	})


	// rates
	$('.rated').each(function(){
		var rate=parseInt($(this).text());
		for(var i=0;i<5;i++){
			if(rate>i){
				$(this).append('<i class="icon icon-fav-star"></i>');
			}
			else{
				$(this).append('<i class="icon icon-fav-star-empty"></i>');
			}
		}
	})

	// datepicker
	if($(".datepicker").length){
		$(".datepicker").datepicker()
	}

	// adding to favs
	$('.btn-fav').click(function(e){
		e.preventDefault();
		$(this).toggleClass('faved');
		if($(this).is('.faved')){
			$(this).find('span').text('В избранном')
		}
		else{
			$(this).find('span').text('Добавить в избранное')
		}
	})

	// sidenavi
	$('.call-sidenavi').click(function(e){
		e.preventDefault();
		// if(!$('.catalog-sidenavi').is('[style]')){
		// 	$('.catalog-sidenavi').css('top',$('.catalog-sidenavi').offset().top)
		// }
		// else{
		// 	$('.catalog-sidenavi').removeAttr('style');
		// }
		$('html').toggleClass('sidenavi-called');
	})

	$('.catalog-sidenavi ul a:not(:last-child)').click(function(e){
		e.preventDefault();
		$(this).toggleClass('opened').siblings('ul').slideToggle();
	})

	*/

	$('[data-fancybox]:not(.is-video)').fancybox({
		buttons: [
        //"zoom",
        //"share",
        //"slideShow",
        //"fullScreen",
        "download",
        //"thumbs",
        //"close"
    ],
    baseTpl:
            '<div class="fancybox-container" role="dialog" tabindex="-1">' +
            '<div class="fancybox-bg"></div>' +
            '<div class="fancybox-inner">' +
            '<div class="fancybox-navigation">{{arrows}}</div>' +
            '<div class="fancybox-stage">' +
            '<div class="fancybox-caption"></div>' +
            '<div class="fancybox-toolbar">{{buttons}} <div class="fancybox-count"><span data-fancybox-index></span>&nbsp;/&nbsp;<span data-fancybox-count></span></div></div>' +
            "</div>" +
            "</div>" +
            "</div>",
		    btnTpl: {
		        download:
		            '<a download data-fancybox-download href="" class="btn-reg fullpic" title="{{DOWNLOAD}}"  >Скачать оригинал</a>',
		        arrowLeft:
		            '<span data-fancybox-prev class="fancybox-button fancybox-button--arrow_left">' +
		            '<svg viewBox="0 0 40 40">' +
		            '<path d="M18,12 L10,20 L18,28 M10,20 L30,20"></path>' +
		            "</svg>" +
		            "</span>",
		        arrowRight:
		            '<span data-fancybox-next class="fancybox-button fancybox-button--arrow_right" >' +
		            '<svg viewBox="0 0 40 40">' +
		            '<path d="M10,20 L30,20 M22,12 L30,20 L22,28"></path>' +
		            "</svg>" +
		            "</span>",
		    },

			afterShow:function(current, previous){
				$('.fancybox-content').find('.fancybox-caption,.fancybox-toolbar, .fancybox-content').remove();
				
				$('.fancybox-slide--current .fancybox-content').append($('.fancybox-toolbar').clone());
				$('.fancybox-slide--current .fancybox-content').find('.fullpic').attr('href', current.group[(parseInt($('.fancybox-slide--current [data-fancybox-index]').text())-1)].src);
				$('.fancybox-slide--current .fancybox-content').append('<button data-fancybox-close="" class="fancybox-button fancybox-button--close" title="Close"><svg viewBox="0 0 40 40"><path d="M10,10 L30,30 M30,10 L10,30"></path></svg></button>');
				console.log(current.group[(parseInt($('.fancybox-slide--current [data-fancybox-index]').text())-1)].opts.caption);
				if(current.group[(parseInt($('.fancybox-slide--current [data-fancybox-index]').text())-1)].opts.caption){
					$('.fancybox-slide--current .fancybox-content').append(('<div class="fancybox-caption">'+current.group[(parseInt($('.fancybox-slide--current [data-fancybox-index]').text())-1)].opts.caption+'</div>'));
				}
				//$('.fancybox-content .fancybox-caption').text(current.group[(parseInt($('.fancybox-slide--current [data-fancybox-index]').text())-1)].opts.caption);


				//console.log(current);
				//console.log(current.opts.index);
				//console.log();

				// if (previous) {
		  //           console.info( 'Navigating: ' + (current.index > previous.index ? 'right' : 'left') );     
		  //       }
			}
		}
	)
	$('[data-fancybox].is-video').fancybox();
	$('.ajax_form').submit(function() {
		var form = $(this);

     	if (!validate_form(form)) {
	        return false;
	    }

	    $.post(form.attr('action'), form.serialize()).done(function(data) {
			if (data.title != 'Ошибка') {
	        	form.trigger('reset');
			}
			if (typeof grecaptcha != 'undefined') grecaptcha.reset();
	        show_msg(data.title, data.text);
	    }).fail(function() {
	        show_msg('Ошибка', 'Произошла ошибка. Попробуйте выполнить запрос позже');
	    });

		return false;
	})






function show_msg(title, msg) {
	var pop=$('.pop-up:visible');
	  close_pop(pop);

	var msg_pop = $('#msg_popup');
	$('.title', msg_pop).html(title);
	$('.pop-cnt', msg_pop).html(msg);

	$('html').addClass('pop-called');
	$('.pop-fade').show().css('height',$(document).height());
	msg_pop.addClass('pop-shown').css({'top':$(window).scrollTop()+$(window).height()/2-msg_pop.height()/2, "marginLeft":msg_pop.width()/-2});
}

// Проводит валидацию формыы
function validate_form(form) {
	var valid = true;

	$('.required', form).each(function() {
		if ($(this).attr('type') == 'checkbox') {
			if (!$(this).is(':checked')) {
				var chkbx = $(this).closest('.checkbox_wrap');
				chkbx.addClass('error');

				setTimeout(function() {
					chkbx.removeClass('error');
				}, 2000);

				$(this).change(function(){
					if ($(this).is(':checked')) {
						$(this).closest('.checkbox_wrap').removeClass('error');
					}
				});

				valid = false;
			} else {
				$(this).closest('.checkbox_wrap').removeClass('error');
			}
		} else {
			if (!$(this).val()) {
				var input = $(this);
				input.addClass('error');

				setTimeout(function() {
					input.removeClass('error');
				}, 2000);

				$(this).change(function(){
					if ($(this).val()) {
						$(this).removeClass('error');
					}
				});

				valid = false;
			} else {
				$(this).removeClass('error');
			}
		}
	});

	return valid;
}
})
