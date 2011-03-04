/* Functions needed by the application */

var AJAX_ACTIVE = true;

const HISTORY_MAX = 30;
var hist = new Array(HISTORY_MAX);
var hist_i;

// Initialize the history
for (i=0; i<HISTORY_MAX; i++) hist[i] = null;
hist_i = 0;
hist[hist_i] = window.location.href;


function show_history() {
	var hist = '';
	for (i=0; i<HISTORY_MAX; i++) {
		if (hist_i == i) hist += '->'; else hist += '   ';
		hist += hist[i] + '\n';
	}
	hist += '\n';
	alert(hist);
}

function upload_history(href) {
	// New page, new history element
	// Upload history and history index
	hist_i = (hist_i + 1) % HISTORY_MAX;
	hist[hist_i] = href;
}

$(document).ready(function() {			// Wait for the document to be able to be manipulated
	
	// First thing... hide everything... then show it slowly
	$("#wrapper").hide().animate({"height": "toggle", "opacity": "toggle"}, 1000);
	
	if ($("#message").html() != '') {
		$("#message").show();
	}
	
	layout_bindings();
});

function layout_bindings() {
	$("a").live("click", function(event) {
		
		if ($(this).hasClass('noajax')) {
			return;
		}
		
		if ($(this).hasClass('confirm')) {
			if (! confirm('Please confirm')) {
				event.preventDefault();
				return;
			}
		}
		
		if ($(this).attr('href').charAt(0) == '/') {
			if (! AJAX_ACTIVE) return;
			event.preventDefault();
			
			var href = $(this).attr('href');
			
			upload_history(href);
		}
		
		// If it's a history.back() request
		else if ($(this).hasClass('history_back')) {
			if (! AJAX_ACTIVE) return;
			event.preventDefault();
			
			var i = (hist_i + HISTORY_MAX - 1) % HISTORY_MAX;
			if (hist[i] == null) {
				// No history
				history.back();
				return;
			} else {
				href = hist[i];
				hist_i = i;
			}
		}
		
		// If it's a history.forward() request
		else if ($(this).hasClass('history_forth')) {
			if (! AJAX_ACTIVE) return;
			event.preventDefault();
			
			var i = (hist_i + 1) % HISTORY_MAX;
			if (hist[i] == null) {
				// No history
				history.forward();
				return;
			} else {
				href = hist[i];
				hist_i = i;
			}
		}
		
		else return;
		
		//show_history();
		execute_ajax(href);
	});
	
	// Form submit bindings
	$('form').live('submit', function(event) {
		//alert($(this).serialize());
		if ($(this).hasClass('noajax')) return;
		if (! AJAX_ACTIVE) return;
		event.preventDefault();
		href = $(this).attr('action');
		upload_history(href);
		execute_ajax(href, $(this).serialize());
		return false;
	});
	
	$("#mypatients-table tr, #mydoctors-table tr").live("hover",
	function() {
		$(this).children().toggleClass("tables-1-selected-tds");
	},
	function() {
		$(this).children().toggleClass("tables-1-selected-tds");
	});
	
	$("#message a").live('click', function(event) {
		event.preventDefault();
		$('#message').slideUp(150);
	});
	
	$('.checkAll').live('click', function() {
		$("input[type='checkbox'][name!='checkAll']")
			.attr('checked', $('.checkAll input').is(':checked'));
	}
)
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

function execute_ajax(href, postdata) {
	//if (href == curpage) return;
	$("#message").fadeOut(100);
	$("#leftcolumn_content").slideUp(50, function() {
		$.post(href, postdata, function(data, status, request) {
			
			if (data.redirect != "") {
				// data.redirect contains the string URL to redirect to
				window.location.href = data.redirect;
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
			
			if (data.navbar != null) {
				$("#navbar").slideUp(20).empty().append(data.navbar);
				$("#navbar").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.footer != null) {
				$("#footer").slideUp(20).empty().append(data.header);
				$("#footer").animate({"height": "toggle", "opacity": "toggle"}, 200);
			}
			
			if (data.curr_url != null) {
				$("#curr_url").html(data.curr_url);
			}
			
			if (data.message != null) {
				$("#message").html(data.message).show();
			}
			
		}, 'json');
	});
}
