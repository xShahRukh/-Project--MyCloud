
(function($){$.fn.inputHintBox=function(options){options=$.extend({},$.inputHintBoxer.defaults,options);this.each(function(){new $.inputHintBoxer(this,options);});return this;}
$.inputHintBoxer=function(input,options){var $guideObject=$(options.el||input),$input=$(input),box,boxMouseDown=false,html='';if(($guideObject.attr('type')=='radio'||$guideObject.attr('type')=='checkbox')&&$guideObject.parent().is('label')){$guideObject=$guideObject.parent();}
function init(){var boxHtml;boxHtml=options.html||(options.source=='attr'?$input.attr(options.attr):'');boxHtml=boxHtml===undefined?'':boxHtml;box=options.div||$("<div/>").addClass(options.className);box.css('display','none').addClass('_hintBox').appendTo(options.attachTo);if(options.isFly){box.css('z-index',Runner.genZIndexMax());}
$input.click(function(){$(this).trigger("focus");}).focus(function(){box.data("inUse",true);$('body').bind("mousedown",global_mousedown_listener);box.data("tooltip",boxHtml);show();}).blur(function(){box.data("inUse",false);prepare_hide();}).bind('mouseenter',function(e){if(box.data("inUse")){return;}
$('body').bind("mousedown",global_mousedown_listener);box.data("tooltip",boxHtml);show();}).bind('mouseleave',function(e){if(box.data("inUse")){return;}
prepare_hide();});}
function align(){var offset=$guideObject.offset();box.css({position:"absolute",top:offset.top+"px",left:offset.left+(Runner.isDirRTL()?-box.outerWidth():$guideObject.outerWidth())+options.incrementLeft+"px"});}
function show(){clearTimeout($.inputHintBoxer.mostRecentHideTimer);var $tooltipBox=options.div_sub?$(options.div_sub,box):box;$('div.shiny_box').hide();align();$tooltipBox.html(box.data("tooltip"));if($guideObject.position().top>0){box.show();}}
function prepare_hide(noTimeout){$('body').click(global_click_listener);if(boxMouseDown){return;}
if(noTimeout){hide(true);return;}
$.inputHintBoxer.mostRecentHideTimer=setTimeout(function(){hide()},300);}
var global_click_listener=function(e){var $e=$(e.target);clearTimeout($.inputHintBoxer.mostRecentHideTimer);if(!$e.closest('.titleHintBox').length&&!$e.closest('._hintBox').length){hide();}};var global_mousedown_listener=function(e){var $e=$(e.target);boxMouseDown=$e.parents('._hintBox').length||$e.is('._hintBox');};function hide(noTimeout){clearTimeout($.inputHintBoxer.mostRecentHideTimer);$('body').unbind('click',global_click_listener).unbind('mousedown',global_mousedown_listener);noTimeout?box.hide():box.fadeOut('fast');};init();};$.inputHintBoxer.mostRecentHideTimer=0;$.inputHintBoxer.defaults={div:'',className:'input_hint_box',source:'attr',div_sub:'',attr:'title',html:'',incrementLeft:0,incrementTop:0,attachTo:'body'}})(jQuery);