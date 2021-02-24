if(typeof(_ccsjQ)=='undefined')
{var _ccsjQ=jQuery.noConflict();}
(function($){$.ccs={register:function(widget,base,obj,short){var bits=widget.split('.');var namespace=bits[0];var name=bits[1];if(obj&&!short){short=obj;obj=base;base=$.ccsBase;}else if(!obj&&!short){obj=base;base=$.ccsBase;}
$[namespace]=$[namespace]||{};if($[namespace][name]){return;}
$[namespace][name]=function(options,element){if(arguments.length){this._instantiate(options,element);}};var newBase=new base();newBase.options=$.extend(true,{},newBase.options);$[namespace][name].prototype=$.extend(true,newBase,obj);if(!short){short=namespace+name;}
$.ccs.bridge(short,$[namespace][name]);},bridge:function(name,object){$.fn[name]=function(options){var isMethodCall=typeof options==="string",args=Array.prototype.slice.call(arguments,1),returnValue=this;options=!isMethodCall&&args.length?$.extend.apply(null,[true,options].concat(args)):options;if(isMethodCall&&options.charAt(0)==="_"){return returnValue;}
if(isMethodCall){this.each(function(){var instance=$.data(this,name),methodValue=instance&&$.isFunction(instance[options])?instance[options].apply(instance,args):instance;if(methodValue!==instance&&methodValue!==undefined){returnValue=methodValue;return false;}});}else{this.each(function(){var instance=$.data(this,name);if(instance){instance.option(options||{})._init();}else{$.data(this,name,new object(options,this));}});}
return returnValue;};},util:{fitImage:function(imageDims,boxDims){var newSize={},aspect=imageDims['w']/imageDims['h'];if((imageDims['w']<=boxDims['w'])||(imageDims['h']<=boxDims['h'])){return{'w':imageDims['w'],'h':imageDims['h']};}
newSize['w']=boxDims['w'];newSize['h']=newSize['w']/aspect;if(newSize['h']<boxDims['h']){newSize['h']=boxDims['h'];newSize['w']=newSize['h']*aspect;}
return{'w':newSize['w'],'h':newSize['h'],'l':Math.ceil((boxDims['w']-newSize['w'])/2),'t':Math.ceil((boxDims['h']-newSize['h'])/2)};}}};$.ccsBase=function(options,element){if(arguments.length){this._instantiate(options,element);}};$.ccsBase.prototype={name:'ccsWidget',options:{},_instantiate:function(options,element){$.data(element,this.name,this);this.element=$(element);this.options=$.extend(true,{},this.options,options);this._init();},_init:function(){},option:function(key,value)
{if($.isPlainObject(key)){this.options=$.extend(true,this.options,key);}else if(key&&typeof value==='undefined'){return this.options[key];}else{this.options[key]=value;}
return this;}};})(_ccsjQ);if(typeof(jsDebug)=='undefined')
{var jsDebug=false;}
var Debug={write:function(text){if(jsDebug&&window.console){console.log(text);}},dir:function(values){if(jsDebug&&window.console){console.dir(values);}}};(function($,undefined){$.ccs.register('ips.articleBlocks',{options:{minBlockWidth:200,heightFactor:0.9,imageHeightFactor:0.6,margin:10},_wrapper:null,_width:0,_total:0,_init:function(){var self=this;self.recalculateBlocks();$(window).resize(function(){self.recalculateBlocks();});},recalculateBlocks:function()
{this._wrapper=$('.ccsArticles_wrap',this.element);this._width=this._wrapper.width();this._total=$('.ccsArticles_block',this.element).length;var margin=this.option('margin'),minimum=this.option('minBlockWidth'),heightFactor=this.option('heightFactor'),imageHeightFactor=this.option('imageHeightFactor'),k=this._width,self=this;var getOptimum=function(){for(i=self._total;i>1;i--){k=Math.floor((self._width-((i-1)*margin))/i);if(k>minimum){return[i,k];}
else
{return[i,minimum];}}
return k;};var optimumSize=getOptimum();$('.ccsArticles_block',this.element).each(function(idx,item){$(this).css('width',optimumSize[1]+'px').css('height',(optimumSize[1]*heightFactor)+'px').css('margin-right',((idx+1)%optimumSize[0]!=0)?margin+'px':'0px');var img=$(this).find('.ccsArticles_img > img'),imgWrap=$(this).find('.ccsArticles_img'),imageSize=[],newSize={};if(imgWrap.length){imgWrap.css({width:'auto',height:'auto'});var w=imgWrap.width();imageSize=[w,(w*imageHeightFactor)];var newSize=$.ccs.util.fitImage({'w':imgWrap.data('width'),'h':imgWrap.data('height')},{'w':imageSize[0],'h':imageSize[1]});img.css('width',newSize['w']+'px').css('height',newSize['h']+'px').css('top',newSize['t']+'px').css('left',newSize['l']+'px');imgWrap.css('width',imageSize[0]+'px').css('height',imageSize[1]+'px').hide().show();}});return this;}},'articleBlocks');})(_ccsjQ);(function($,undefined){$.ccs.register('ips.gallerySlideshow',{options:{autoPlay:true,speed:4},_paused:false,_boxSize:{},_init:function()
{var self=this;if(!this.option('imageData')||this.option('imageData')==''){return;}
this._getBoxSize();$(this.element).append($('<div/>').attr('id',this.option('baseID')+'_preloader').addClass('ccsSlideshow_preloader').css('position','absolute').css('left','-2000px').css('overflow','hidden'));this._preloadImage(this.option('imageData').slice(0,1)[0]);$('.ccsSlideshow',this.element).mouseenter(function(e){self.pause();}).mouseleave(function(e){self.unpause();});$('.ccsSlideshow_moveLeft',this.element).click(function(e){e.preventDefault();var prevID=self.getPrevID();self._update(prevID);return false;});$('.ccsSlideshow_moveRight',this.element).click(function(e){e.preventDefault();var nextID=self.getNextID();self._update(nextID);return false;});this.play();},play:function()
{var self=this;var _mainLoop=function(){if(self._paused)
{self._mainLoop=window.setTimeout(_mainLoop,(self.option('speed')*1000));return;}
var next=self.getNextID();self._update(next);self._preloadImage(self.getNextID(next[0]));self._mainLoop=window.setTimeout(_mainLoop,(self.option('speed')*1000));}
_mainLoop();},pause:function()
{this._paused=true;},unpause:function()
{this._paused=false;},_update:function(image)
{if(this.element.data('curID')==image[0]){return;}
this._preloadImage(image);var self=this,newImage=this._getImgObj(image[0]);if((!newImage[0].complete)||!newImage.width())
{window.setTimeout(function(){Debug.write('('+image[0]+') Waiting for load...');return self._update.call(self,image)},50);return;}
this._getBoxSize();this._resizeAndPositionImage(image);this._animateImageInfo(image);$('.ccsSlideshow_images').append(newImage.hide());if(this.element.data('curID')==undefined){newImage.fadeIn();}else{var current=this._getImgObj(this._findFromID(this.element.data('curID'))[0]);current.fadeOut();newImage.fadeIn();}
this.element.data('curID',image[0]);},_animateImageInfo:function(image)
{var infoDiv=$('.ccsSlideShow_imageinfo > div[data-id="'+image[0]+'"]',this.element);if(!infoDiv.length){var content=this.option('infoTemplate').replace('[title]',image[1]['caption']).replace('[desc]',image[1]['description']).replace('[album]',image[1]['album_name']);infoDiv=$('<div/>').attr('data-id',image[0]).html(content).hide();$('.ccsSlideshow_imageinfo',this.element).append(infoDiv);}
$('.ccsSlideshow_imageinfo > div:not(div[data-id="'+image[0]+'"])').fadeOut('slow');infoDiv.fadeIn('slow');},_resizeAndPositionImage:function(image)
{var img=this._getImgObj(image[0]);if(img.height()<this._boxSize.height)
{img.css('left',(this._boxSize.width-img.width())/2);return;}
img.css('height',this._boxSize.height);img.css('left',(this._boxSize.width-img.width())/2);return;if(!img.data('origWidth')||!img.data('origHeight')){img.data('origWidth',img.width()).data('origHeight',img.height());}
var aspect=img.data('origWidth')/img.data('origHeight');var newSize={};if(img.data('origWidth')>this._boxSize.width){newSize.width=this._boxSize.width;newSize.height=(newSize.width/aspect);}else{newSize.width=img.data('origWidth');newSize.height=img.data('origHeight');}
img.css('width',newSize.width+'px').css('height',newSize.height+'px');var posLeft=Math.ceil((this._boxSize.width-newSize.width)/2);var posTop=Math.ceil((this._boxSize.height-newSize.height)/2);img.css('left',posLeft+'px').css('top',posTop+'px');},_getImgObj:function(id)
{return $('img[data-id="'+id+'"]',this.element);},_preloadImage:function(image)
{var self=this;if(!this._getImgObj(image[0]).length){$('.ccsSlideshow_preloader',this.element).append($('<img/>').attr('src',this._getFilename(image[1])).attr('data-id',image[0]).bind('click',function(e){return self._clickImage(e);}));}},_clickImage:function(e)
{var imageData=this._findFromID($(e.target).data('id'));window.top.location=imageData[1]['url'].replace(/&amp;/,'&');},getNextID:function(curID)
{var curID=curID||this.element.data('curID'),imageData=this.option('imageData');if(!curID){return imageData.slice(0,1)[0];}
for(i=0;i<imageData.length;i++){if(imageData[i][0]==curID){var pos=i;break;}}
if(pos==(imageData.length-1)){return imageData[0];}else{return imageData[pos+1];}},getPrevID:function(curID)
{var curID=curID||this.element.data('curID'),imageData=this.option('imageData');if(!curID){return imageData.slice(0,1)[0];}
for(i=0;i<imageData.length;i++){if(imageData[i][0]==curID){var pos=i;break;}}
if(pos===0){return imageData[imageData.length-1];}else{return imageData[pos-1];}},_findFromID:function(id)
{var imageData=this.option('imageData');for(i=0;i<imageData.length;i++){if(imageData[i][0]==id){return imageData[i];}}
return false;},_getBoxSize:function()
{var elem=$('.ccsSlideshow_images',this.element);this._boxSize={width:elem.outerWidth(),height:elem.outerHeight()};},_getFilename:function(image)
{return this.option('rootImageURL')+image['fileName'];}},'gallerySlideshow');})(_ccsjQ);(function($,undefined){$.ccs.register('ips.ticker',{options:{speed:3,select:'li'},_items:[],_loop:null,_init:function()
{var self=this;this._items=$(this.option('select'),this.element);this._items.hide().first().show();$(this.element).bind('mouseenter',function(e){self.pause();}).bind('mouseleave',function(e){self.play();}).find('.ccsTicker_next').bind('click',function(e){return self.nextItem();}).end().find('.ccsTicker_prev').bind('click',function(e){return self.prevItem();});this.play();},_update:function()
{this.nextItem();this.play();},pause:function()
{clearTimeout(this._loop);},play:function()
{var self=this;this._loop=window.setTimeout(function(e){self._update();},(this.option('speed')*1000));},prevItem:function()
{var cur=$(this.option('select')+':visible',this.element),prev=(cur.prev().length)?cur.prev():this._items.last();cur.fadeOut('slow');prev.fadeIn('slow');},nextItem:function()
{var cur=$(this.option('select')+':visible',this.element),next=(cur.next().length)?cur.next():this._items.first();cur.fadeOut('slow');next.fadeIn('slow');}},'ipsTicker');})(_ccsjQ);