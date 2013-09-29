$.build = function (type, option){
	"use strict";
	return $.build[type](option);
};
