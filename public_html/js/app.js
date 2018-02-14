$(document).ready(function(){

	// initialize foundation 
	$(document).foundation();


	$("video.video-js").each(function(index, item) {
		var player = videojs(item);
		player.play();

		$(item).sync_player();
	});


	$("div#chatwindow").each(function(index, item){
		console.log("Chat Window FOund");
		$(item).chat_window();
	});

	$("form#chatForm").ajaxForm({
		sucess: function(status) {
			alert("Sent");
		},
		error : function(error_data) {
			alert("An intenral error has occurred");
		}
	});
});


// create a syncronized player
$.fn.sync_player = function() {
	return this;
}

// wrap a chat window into one jquery plugin
$.fn.chat_window = function() {

	var list = $(this).find("ul#chatMessages").first();

	var last_message_id = 0;
	var written_messages = {};
	setInterval(function(){

		$.ajax({
			url : "/chat",
			method : "GET",
			data : {
				last_message_id : last_message_id
			},
			dataType : "json",
			success : function(status) {
				if(status.success) {
					for(var message_index in status.response.messages) {
						var message = status.response.messages[message_index];
						if(typeof written_messages[message.message_id] == "undefined") {
							last_message_id = message.message_id;
							$(list).append('<li><span class="username">' 
											+ message.username 
											+ '</span><span class="divider">:</span><span class="message">' 
											+ message.message 
											+ '</span></li>');
							written_messages[message.message_id] = true;
						}
					}
				}
			},
			error : function() {
				console.log("An internal error has occurred");
			}
		});

	}, 1500);

	return this;
}


// adjust a button to indictate success
$.fn.button_success = function(textoutput, disabled) {

	var textoutput = typeof textoutput == "undefined" ? $(this).text() : textoutput;
	var disabled = typeof disabled == "undefined" ? true : disabled;

	var button = this;
	$(button).removeClass("error").addClass("success").text(textoutput).prop("disabled", disabled);

	return this;
}

// adjust a button appearance to indicate failure
$.fn.button_failure = function(textoutput) {
	var textoutput = typeof textoutput == "undefined" ? $(this).text() : textoutput;
	var button = this;
	$(button).removeClass("success").addClass("error").text(textoutput).parents("form").first().one("change input", "input,textarea", function(sub_event) {
		$(button).button_reset();
	});
	return this;
}

// reset button to initial state
$.fn.button_reset = function() {
	$(this).removeClass("success").removeClass("error").text($(this).data("initial-text")).prop("disabled", false);
	return this;
}

// setup form to be totally ajax 
$.fn.ajaxForm = function(callbacks) {

	var self = this;

	// default callbacks 
	var default_callbacks = {
		success : function(response) {},
		redirect : function(response, redirect_target) {},
		forbidden : function(response) {},
		not_found : function() {},
		error : function() {},
		beforeSend : function() {},
		complete : function() {}
	};

	// ensure we are going to be working some kind of data
	var callbacks = typeof callbacks == "undefined" ? {} : callbacks;

	// merge the default callbacks with the supplied callbacks into a new object
	var merged_callbacks = $.extend({}, default_callbacks, callbacks);

	// store text state of the submit button
	var submit_button = $(self).find("button[type='submit']");
	$(submit_button).data("initial-text", $(submit_button).text());


	// cover submissions
	$(self).on("submit", function(event) {

		event.preventDefault();

		// grab our method and action url 
		var method = self.attr("method");
		var action = self.attr("action");

		// store all input data from the form here
		var data = {};

		// go ahead and grab all input data
		$(self).find("input,textarea,select").each(function(index, item){
			var item = $(item);
			data[item.attr("name")] = item.val();
		});

		// clear off any errors that are currently present on the form
		$(self).find("p.error").remove();
		$(self).find("div.field-error").removeClass("field-error");

		// make the request up to the serve
		$.ajax({
			url : action,
			method : method,
			cache : false,
			dataType : "json",
			data : data, // if we send the data as raw json, jquery will automatically serialize it accordingly
			beforeSend : function() {
				$(self).find("button.submit").prop("disabled", true);
				merged_callbacks.beforeSend();
			},
			statusCode : { // expand this out to define certains status codes to get more granular control over responses

			},
			success : function(response) {

				if(response.success == false) {
					var errors = response.errors;
					for(var field in errors) {
						var element_field = $(self).find("input[name='" + field + "'],textarea[name='" + field + "'],select[name='" + field + "']");
						var element_parent_field = $("div#field-" + field);
						var is_captcha_response = field == "g-recaptcha-response" ? true : false;

						var error_html = '<p class="error">' + errors[field] + '</p>';
						if(is_captcha_response)
							$(element_parent_field).find("div.g-recaptcha").first().before(error_html);
						else
							$(element_field).after(error_html);
						
						$(element_field).one("change input", function(sub_event) {
							$(element_parent_field).removeClass("field-error");
							$(element_parent_field).find("p.error").remove();
						});

						$(element_parent_field).addClass("field-error");
						break; // currently we are only interested in showing the first error that we receive. This may change in the future
					}
				}

				merged_callbacks.success(response);
			},
			error : function(response) {
				merged_callbacks.error(response);
			},
			complete : function() {
				$(self).find("button.submit").prop("disabled", false);
				merged_callbacks.complete();
			}
		});


		return false;
	});	

	return this;
}

