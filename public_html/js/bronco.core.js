$(document).ready(function(){
	$(document).foundation(); // initialize foundation
});

// bronco form that will hook in and append the bronco form object
$.fn.broncoForm = function(callbacks, reset) {

	var form = $(this);
	var reset = typeof reset == "undefined" ? false : reset;
	var callbacks = typeof callbacks == "undefined" ? {} : callbacks;
	
	var defaultCallbacks = {
		success: function(status, response) {},
		error: function(status, response) {},
		failure: function(xhr) {}
	};

	var activeCallbacks = $.extend(true, {}, defaultCallbacks, callbacks);
	if(reset)
		$(form).off("submit");

	$(form).on("submit", function(event) {
		event.preventDefault();

		$.ajax({
			url: $(form).attr("action"),
			method: $(form).attr("method"),
			dataType: "json",
			data: $(form).serializeArray(),
			cache: false,
			async: true,
			statusCode : {
				400: function() {
					alert("Bad Request");
				},
				401: function() { // 401 forbidden
					window.location.href = "/account/login";
				},
				403: function() {  // 403 unauthorized
					alert("Access denied");
				},
				404: function() { // 404 not found
					alert("Unable to reach destination");
				},
				500: function() {
					alert("An internal error has occurred...");
				}
			},
			success: function(status, textStatus, xhr) {	
				if(status.success)
					activeCallbacks['success'](status, status.response);
				else
					activeCallbacks['error'](status, status.response);
		
			},
			error: function(xhr, textStatus, errorThrown) {
				activeCallbacks['failure'](xhr);
			}
		});
		return false;
	});
}
