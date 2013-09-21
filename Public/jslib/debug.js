if(!console){
	window['console'] = {}
}
if(!console.log){
	window['console'].log = function (){
	}
}
if(JS_DEBUG){
	console.log('现在是调试模式～');
}
