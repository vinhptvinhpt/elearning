/*Nestable Init*/
$( document ).ready(function() {
	"use strict";
	$('.dd').nestable('collapseAll');
	
	/*Nestable*/
	$('#nestable').nestable('collapseAll');
	$('#nestable2').nestable({group: 1});

});