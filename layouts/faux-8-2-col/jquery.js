/* Functions needed by the application */

var AJAX_ACTIVE = true;

const HISTORY_MAX = 30;
var history = new Array(HISTORY_MAX);
var history_i = HISTORY_MAX - 1;

// Initialize the history
for (i=0; i<HISTORY_MAX; i++) history[i] = null;

function show_history() {
	var hist = '';
	for (i=0; i<HISTORY_MAX; i++) {
		if (history_i == i) hist += '->'; else hist += '   ';
		hist += history[i] + '\n';
	}
	hist += '\n';
	alert(hist);
}


//function callback(href) {
//	alert(href);
//	execute_ajax(href);
//}

$(document).ready(function() {			// Wait for the document to be able to be manipulated
	
	// First thing... hide everything... then show it slowly
	$("#wrapper").hide().animate({"height": "toggle", "opacity": "toggle"}, 1000);
	
	// Get the current page
	var curpage = window.location.pathname;
	
	/*$("#rightcolumn > a").click(function(event) {		// Do something on click on a specific tag <a/>
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
	});*/
	
	// History plugin
	//$.history.init(execute_ajax);
	/*$("a[rel|='history']").click(function(event) {
		event.preventDefault();
		$.history.load(this.href.replace(/^.*#/, ''));
		return false;
	});*/
	
	layout_bindings();
});

function layout_bindings() {
	$("a").live("click", function(event) {
		if ($(this).hasClass('confirm')) {
			if (! confirm('Please confirm')) {
				event.preventDefault();
				return;
			}
		}
		if ($(this).hasClass('ajax')) {
			if (! AJAX_ACTIVE) return;
			event.preventDefault();
			
			var href = $(this).attr('href');
			
			// If it's a history.back() request
			if ($(this).hasClass('history_back')) {
				//alert('history backward');
				var i = (history_i + HISTORY_MAX - 1) % HISTORY_MAX;
				if (history[i] == null) {
					// No history
					history.back();
					return;
				} else {
					href = history[i];
					history_i = i;
				}
			} else if ($(this).hasClass('history_forth')) {
				//alert('history forward');
				var i = (history_i + 1) % HISTORY_MAX;
				if (history[i] == null) {
					// No history
					history.forward();
					return;
				} else {
					href = history[i];
					history_i = i;
				}
			} else {
				// New page, new history element
				// Upload history and history index
				history_i = (history_i + 1) % HISTORY_MAX;
				history[history_i] = href;
			}
			
			//show_history();
			
			execute_ajax(href);
		}
	});
	
	/*$("a.ajax").live("click", function(event) {
		if (! AJAX_ACTIVE) return;
		event.preventDefault();
		var href = $(this).attr('href');
		execute_ajax(href);
	});
	
	$("a.confirm").live("click", function(event) {
		if (! confirm('Please confirm')) {
			event.preventDefault();
			return;
		}
		/*if (! AJAX_ACTIVE) return;
		event.preventDefault();
		var href = $(this).attr('href');
		execute_ajax(href);*
	});*/
	
	$("#mypatients-table tr, #mydoctors-table tr").live("hover",
	function() {
		$(this).children().toggleClass("tables-1-selected-tds");
	},
	function() {
		$(this).children().toggleClass("tables-1-selected-tds");
	});
	
	/*$("#mypatients-table tr, #mydoctors-table tr").live("click", function(event) {
		//var href = $(this).find("a:first").attr('href');
		var account_id = $(this).find('.id_keeper:first').html();
		alert(account_id);
		execute_ajax('/profile/user/' + account_id);
	});*/
}

function show_patient_form() {
	//$("#type-selection").fadeOut(400);
	$("#registration-hcp-fieldset").hide();
	$("#registration-patient-fieldset").fadeIn(400);
}

function show_hcp_form() {
	//$("#type-selection").fadeOut(400);
	$("#registration-patient-fieldset").hide();
	$("#registration-hcp-fieldset").fadeIn(400);
}

function execute_ajax(href) {
	//if (href == curpage) return;
	$("#leftcolumn_content").slideUp(50, function() {
		//var beenslow = false;
		//$("#leftcolumn").empty();
		//$("#leftcolumn").append("<center>Loading...</center>").delay(600, "beenslow").slideToggle(1000, function() {
		//	beenslow = true;
		//});
		/*$.ajax({
			type: "GET",
			url: href,
			complete: function(request, status) {
					//alert(request.getResponseHeader('Last-Modified'));
					alert(request.status + ' ' + status);
				},
			dataType: 'json'
		});
		$.get(href, function(data, status, request) {
			//alert(request.getResponseHeader('Last-Modified'));
			alert(request.status);
		});*/
		$.get(href, function(data, status, request) {
			//$("#leftcolumn").dequeue("beenslow");
			//$("#leftcolumn").stop();
			//$("#leftcolumn").hide();
			//$("#leftcolumn").empty();
			//if (beenslow) {
				//$("#leftcolumn").slideToggle(1);
			//}
			//alert(data.redirect);
			
			if (data.redirect != "") {
				// data.redirect contains the string URL to redirect to
				window.location.href = data.redirect;
				//$(window.location).attr('href', data.redirect);
				return;
			}
			
			if (data.mainpane != null) {
				$("#leftcolumn_content").empty().append(data.mainpane);
				$("#leftcolumn_content").animate({"height": "toggle", "opacity": "toggle"}, 200);
			} else {
				$("#leftcolumn_content").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.sidepane != null) {
				$("#rightcolumn").slideUp(20).empty().append(data.sidepane);
				$("#rightcolumn").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.header != null) {
				$("#header").slideUp(20).empty().append(data.header);
				$("#header").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.footer != null) {
				$("#footer").slideUp(20).empty().append(data.header);
				$("#footer").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.curr_url != '') {
				$("#curr_url").html(data.curr_url);
			}
			
		}, 'json');
	});
}
