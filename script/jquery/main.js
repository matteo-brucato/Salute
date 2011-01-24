/* Functions needed by the application */

$(document).ready(function() {			// Wait for the document to be able to be manipulated
	
	// First thing... hide everything... then show it slowly
	$("#wrapper").hide().animate({"height": "toggle", "opacity": "toggle"}, 1500);
	
	// Get the current page
	var curpage = window.location.pathname;
	
	$("#rightcolumn > a").click(function(event) {		// Do something on click on a specific tag <a/>
		event.preventDefault(); 						// Don't use <a/> as usual, stay here
		alert("Good job! Now enjoy the magic");
		$(this).hide("slow");
		$("#lorem-ipsum").delay(800).show("slow");
	});
	
	$("#lorem-ipsum > a").click(function(event) {
		event.preventDefault();
		$(this).parent().hide(2500, function() {	// very slow fade out!
			$("#rightcolumn > a").show();			// this is a callback example
		});
	});
	
	layout_bindings();
});

function layout_bindings() {
	$(".ajaxlink").live("click", function(event) {
		event.preventDefault();
		var href = $(this).attr('href');
		//if (href == curpage) return;
		$("#leftcolumn").slideUp(200);
		$.get(href, function(data) {
			$("#leftcolumn").empty();
			$("#leftcolumn").append(data.left).slideDown(400);
		}, 'json');
	});
}
