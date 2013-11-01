/*$.fn.register_middle_hack = function (selector){
 "use strict";
 // 模拟鼠标中键点击
 this.on({
 'mousedown': function (e){
 if(e.which !== 2){
 return;
 }
 $(this).data({'mousedown': 1, 'mouseinside': 1});
 },
 'mouseup'  : function (e){
 if(e.which !== 2){
 return;
 }
 var self = $(this);
 if(self.data('mousedown') && self.data('mouseinside', 1)){
 var newe = new $.Event('click');
 x = {altKey                    : false,
 bubbles                    : true,
 button                     : 1,
 buttons                    : undefined,
 cancelable                 : true,
 clientX                    : 290,
 clientY                    : 616,
 ctrlKey                    : false,
 currentTarget              : 'a.user-link',
 data                       : undefined,
 delegateTarget             : 'div#WeiboContainer.col-sm-9 loaded',
 eventPhase                 : 3,
 handleObj                  : Object,
 isDefaultPrevented         : 'returnFalse',
 jQuery204010525027615949512: true,
 metaKey                    : false,
 offsetX                    : 88,
 offsetY                    : 6,
 pageX                      :  290,
 pageY                      : 1149,
 relatedTarget              : null,
 screenX                    : 2210,
 screenY                    : 703,
 shiftKey                   : false,
 target                     : a.user - link,
 timeStamp                  : 1383212887200,
 toElement                  : a.user - link,
 which                      : 2
 }
 console.log(e);
 return;
 $.extend(newe, e);
 self.trigger(newe);
 }
 self.data({'mousedown': 0, 'mouseinside': 0});
 },
 'mouseover': function (e){
 if(e.which !== 2){
 return;
 }
 var self = $(this);
 if(self.data('mousedown')){
 $(this).data({'mouseinside': 1});
 }
 },
 'mouseout' : function (e){
 if(e.which !== 2){
 return;
 }
 $(this).data({'mouseinside': 0});
 }
 }, selector);
 return this;
 };
 */
$.fn.register_middle_hack = function (selector, cb){
	"use strict";
	this.on('mousedown', selector, function (){
		if($(this).data('register_middle_hack')){
			return;
		}
		$(this).data('register_middle_hack', true).on('click', cb);
	})
};
