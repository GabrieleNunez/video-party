
var is_syncing = false;
var ignore_update = false;
var missed_updates = 0;
var written_messages = {};
var fast_messages = 0;
var current_stream_file = '';
var last_message_id = 0;
var manual_message = '';
var manual_username = '';
var MAXIMUM_MESSAGES = 30;

var WEB_DEVICE_XBOXONE = 2;
var WEB_DEVICE_IOS  = 1;
var WEB_DEVICE_OTHER = 0;
var WEB_DEVICE = WEB_DEVICE_OTHER;

// quick device check
if(/Xbox One/.test(navigator.userAgent))
	WEB_DEVICE = WEB_DEVICE_XBOXONE;
else if(navigator.userAgent.match(/(iPod|iPhone|iPad)/))
	WEB_DEVICE = WEB_DEVICE_IOS;
else
	WEB_DEVICE = WEB_DEVICE_OTHER;

var chat_colors = [
	'#1abc9c',
	'#2ecc71',
	'#3498db',
	'#9b59b6',
	'#e91e63',
	'#f1c40f',
	'#e67e22',
	'#e74c3c',
	'#95a5a6'
];


var chatter_colors = {};

var GAMEPADS_CONNECTED = {};
var GAMEPAD_ACTIVE = null;




$(document).ready(function(){

	// initialize foundation 
	$(document).foundation();


	/*
	if(WEB_DEVICE === WEB_DEVICE_XBOXONE)
		$.gamepad(); */
	


	// home page scripts
	if($("div#videoArea").length > 0) {


		//TODO this is now fixed
		/*
		if(WEB_DEVICE === WEB_DEVICE_IOS) {
			hide_chatwindow();
			hide_hotbar();
		} */


		var gamepad = new Gamepad();

		gamepad.on('connect', function(event){
			console.log("Controller Connected");
		});

		gamepad.on('press','button_3', function() {
			console.log("Toggling chat window and hot bar appropriatly");
			if($('div#videoArea').hasClass('chat')) {
				hide_chatwindow();
				show_hotbar();
			} else {
				show_chatwindow();
				hide_hotbar();
			}
		});

		// video js
		$("video.video-js").each(function(index, item) {
			
			var player = videojs(item, {
				autoplay : true,
				preload : "auto",
				controls : true,
				loop : true,
				muted : WEB_DEVICE === WEB_DEVICE_IOS
			});

			player.play();


			// are we a master control
			var is_master = $(item).data("master") == "1" ? true : false;
			var current_timestamp = $(item).data("current");
			current_stream_file = $(item).data("stream-file");

			$(item).sync_player(player, is_master, current_timestamp);
		});


		$("div#chatWindow").each(function(index, item){
			console.log("Chat Window FOund");
			$(item).chat_window();
		});

		$("form#chatForm").ajaxForm({
			beforeSend : function(xhr) {
				
				var message = { username : $("form#chatForm").data("username"), message : $("input#message").val() };

				if(message.message.trim().length > 0) {
					
					var username_color = typeof chatter_colors[message.username] != "undefined" ? chatter_colors[message.username] : assign_random_color(message.username);
					var list = $("ul#chatMessages").first();

					var username_color = typeof chatter_colors[message.username] != "undefined" ? chatter_colors[message.username] : assign_random_color(message.username);

					if($(list).find("li").length > MAXIMUM_MESSAGES)
						$(list).find("li").first().remove();


					var new_element = $('<li><span class="username" style="color:' +  username_color + ';">' 
									+ '</span><span class="divider">:</span><span class="message">' 
									+ '</span></li>');

					new_element.find("span.username").first().text(message.username);
					new_element.find("span.message").first().text(message.message);
					$(list).append(new_element);


					manual_message = message.message;
					manual_username = message.username;
					
					var myDiv = list;
					myDiv.animate({ scrollTop: myDiv.prop("scrollHeight") - myDiv.height() }, 250);
					$('input#message').val('');

					return true;
				} else {
					xhr.abort();
					return false;
				}
			},
			success: function(status) {
				$('input#message').val('');

			},
			error : function(error_data) {
				alert("An intenral error has occurred");
			}
		});


		$("button#hideChatButton").on("click", function(event) {
			event.preventDefault();

			hide_chatwindow();	
			show_hotbar();		

			return false;
		});


		$("div#showChatButton").on("click", function(event) {
			event.preventDefault();

			show_chatwindow();
			hide_hotbar();

			return false;
		});


		$('div#chatPopupButton').on("click", function(event) {
			event.preventDefault();

			window.open('/chat','_blank','width=400,height=600');			

			return false;
		});


		$('div#logoutButton').on('click', function(event) {
			event.preventDefault();

			window.location.href = '/logout';

			return false;
		});

		$("a.slide-button").on("click", function(event) {
			event.preventDefault();

			var target_slide = $(this).data("slide");
			var first_slide = $("div.slide").first(); // get the first slide 

			var slide_element = $("#" + target_slide);

			var slide_visible = $("#" + target_slide).is(":visible");

			if(slide_visible) {
				$("div.slide").hide();
				$(first_slide).show();

				console.log("slide visible, hiding and showing first slide");
			} else {

				$("div.slide").hide();
				$(slide_element).show();
			}

			return false;
		});

		$("button#fullScreenButton").on("click", function(event) {
			event.preventDefault();
			goFullscreen();
			$("div#fullscreenPrompt").remove();
			return false;
		});

		$("button#fullScreenCancelButton").on("click", function(event){
			event.preventDefault();
			$("div#fullscreenPrompt").remove();
			return false;
		});
	}


	// we are on the login page
	if($("div#loginPage").length > 0) {

		$("form#loginForm").ajaxForm({
			success : function(status) {
				if(status.success) {
					$("form#chatForm button").first().button_success("Success! Logging in");
					window.location.href = status.response.chatonly ? '/chat' : '/';
				} else {
					$("form#chatForm button").first().button_failure("Unable to login");
				}
			},
			error : function(status) {
				$("form#chatForm button").first().button_failure("An internal error has occurred");
			}
		})
	}

	
});

// https://stackoverflow.com/questions/3900701/onclick-go-full-screen
function goFullscreen() {
  if ((document.fullScreenElement && document.fullScreenElement !== null) ||    
   (!document.mozFullScreen && !document.webkitIsFullScreen)) {
    if (document.documentElement.requestFullScreen) {  
      document.documentElement.requestFullScreen();  
    } else if (document.documentElement.mozRequestFullScreen) {  
      document.documentElement.mozRequestFullScreen();  
    } else if (document.documentElement.webkitRequestFullScreen) {  
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
    }  
  } 
}

function hide_chatwindow() {
	
	$("div#chatArea").animate({ 
		position: "absolute",
		right : '-100%'
	}, 500, function() {
		//$("div#chatArea").
	});


	$('div#videoArea').removeClass('chat');
}

function show_chatwindow() {
	
	$("div#chatArea").animate({ 
		position: "absolute",
		right : '1%'
	}, 500);

	$('div#videoArea').addClass('chat');
}

function show_hotbar() {

	$("div#hotbar").animate({
		position: "absolute",
		right : "1%"
	}, 500);

	$('div#videoArea').addClass('hotbar');

}

function hide_hotbar() {
	/// Foundation.MediaQuery.is('small only')
	$("div#hotbar").animate({
		position: "absolute",
		right : "-100%"
	}, 500);

	$('div#videoArea').removeClass('hotbar');

}


// create a syncronized player
$.fn.sync_player = function(video_player, is_master, current_timestamp) {

	var current_timestamp = current_timestamp;
	var video_player = video_player;
	is_syncing = false;
	ignore_update = false;
	missed_updates = 0;

	if(is_master) {

		// once we load our metadata go ahead and setup our constant timer
		video_player.one('loadedmetadata', function() {
			video_player.currentTime(current_timestamp);
			setInterval(function() {
				send_sync_state(video_player);
			}, 500); 

		});

	}   else {

		//fetch the current sync state 
		setInterval(function() {

			if(missed_updates > 10) {
				is_syncing = false;
				missed_updates = 0;
			}

			if(is_syncing === false)
				get_sync_state(video_player);
			else
				missed_updates++;
			

		}, 500);
	}


	// get viewerlist
	setInterval(function() {

		$.ajax({
			url : "/viewerlist",
			method : "GET",
			data : {},
			dataType : "json",
			success : function(status) {

				if(status.success) {
					
					var total_viewers = 0;
					var current_viewers = [];


					for(var index in status.response.viewers) {
						var viewer = status.response.viewers[index];
						current_viewers.push(viewer.username);	
					}


					// grab our vieer list item
					var viewer_list = $("ul#viewerlist");
					viewer_list.empty();

					// sort them alphabetically
					current_viewers.sort();
					var last_element = null;
					for(var i = 0; i < current_viewers.length; i++) {

						//  prepare to insert
						var list_item = $('<li data-username="' + current_viewers[i] + '">' + current_viewers[i] + '</li>');
						list_item.data("username", list_item);
						viewer_list.append(list_item);

						total_viewers++;

					}

					// set the total viewers
					$("#viewers").text(parseInt(total_viewers));

				}
			}
		})

	}, 10000);

	// remove from viewerlist
	$(window).on("beforeunload", function() {
		navigator.sendBeacon('/viewerlist',"state=leaving");
	});	

	return this;
}


// send the current sync as the master
function send_sync_state(video_player) {
	
	var video_player = video_player;
	var player_state = video_player.paused() ? 0 : 1;
	var current_video_time = video_player.currentTime();

	$.ajax({
		url : '/sync',
		method : 'POST',
		data : {
			current : current_video_time,
			state : player_state
		},
		dataType : "json",
		beforeSend : function() {
			is_syncing = true;
		},
		success : function(status) {
			console.log("Synced Setting");

			// determine if we need to adjust the file we are in
			if(status.response.stream_file != current_stream_file) {
				current_stream_file = status.response.stream_file;
				video_player.src({ type: "application/x-mpegURL", src : current_stream_file });
				video_player.play();
			}


		},
		error : function(status) {
			console.log("Unable to send sync");
		},
		complete : function() {
			is_syncing = false;
		}
	});

}

// get the current sync set
function get_sync_state(video_player) {
	
	var video_player = video_player;
	var player_state = video_player.paused() ? 0 : 1;
	var current_video_time = video_player.currentTime();

	// only ally get_sync_state to be ignored ONE time in a set of cycles
	if(ignore_update) {
		ignore_update = false;
		return;
	}

	$.ajax({
		url : '/sync',
		method : 'GET',
		data : {
			current : current_video_time,
			state : player_state
		},
		dataType : 'json',
		success : function(status) {

			if(status.success) {

				// status is different then what we currently have
				if(status.response.state != player_state) {

					if(status.response.state == 0) { // master state is paused
						video_player.currentTime(status.response.time);
						video_player.pause();
						ignore_update = true;
					} else if(status.response.state == 1) {
						video_player.currentTime(status.response.time);
						video_player.play();

						//
						ignore_update = true;
					}
				}

				// our current time is not the same. Try to find out if it should be fixed
				if(status.response.time != current_video_time) {

					var diff = status.response.time - current_video_time;
					if(diff > 12 || diff < 0) { // if greater then 10 seconds force seek to the new position
						video_player.currentTime(status.response.time);
						ignore_update = true;
					}

				}	

				// determine if we need to adjust the file we are in
				if(status.response.stream_file != current_stream_file) {
					current_stream_file = status.response.stream_file;
					video_player.src({ type: "application/x-mpegURL", src : current_stream_file });
					video_player.play();
				}

			}
		},
		error : function(status) {
			console.log("Unable to fetch sync information")
		}
	});


}

	
// get random chat color
function assign_random_color(username) {
	var color =  chat_colors[Math.floor(Math.random()*chat_colors.length)];
	chatter_colors[username] = color;
	return color;
}

// wrap a chat window into one jquery plugin
$.fn.chat_window = function() {

	var list = $(this).find("ul#chatMessages").first();

	setInterval(function(){

		var new_message = false;
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

				
						var duplicate_message = false;
						if(message.message == manual_message && message.username == manual_username) {
							duplicate_message = true;
							written_messages[message.message_id] = true;
							new_message = false;
						}

						if(typeof written_messages[message.message_id] == "undefined") {
							last_message_id = message.message_id;
							var username_color = typeof chatter_colors[message.username] != "undefined" ? chatter_colors[message.username] : assign_random_color(message.username);

							if($(list).find("li").length > 30)
								$(list).find("li").first().remove();

							$(list).append('<li><span class="username" style="color:' +  username_color + ';">' 
											+ message.username 
											+ '</span><span class="divider">:</span><span class="message">' 
											+ message.message 
											+ '</span></li>');
							written_messages[message.message_id] = true;

							new_message = true;
						}
					}

					if(new_message) {
						var myDiv = list;
						myDiv.animate({ scrollTop: myDiv.prop("scrollHeight") - myDiv.height() }, 250);
					}
				}
			},
			error : function() {
				console.log("An internal error has occurred");
			}
		});

	}, 1300);

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


			if($(item).attr("type") == "checkbox") {
				data[item.attr("name")] = $(item).is(":checked") ? '1' : '0';
			} else {
				data[item.attr("name")] = item.val();
			}
		});


		// make the request up to the serve
		$.ajax({
			url : action,
			method : method,
			cache : false,
			dataType : "json",
			data : data, // if we send the data as raw json, jquery will automatically serialize it accordingly
			beforeSend : function(xhr) {
				var result = merged_callbacks.beforeSend(xhr);
				if(result === true)
					$(self).find("button.submit").prop("disabled", true);
			},
			statusCode : { // expand this out to define certains status codes to get more granular control over responses

			},
			success : function(response) {

				if(response.success == false) {
							// clear off any errors that are currently present on the form
					$(self).find("p.error").remove();
					$(self).find("div.field-error").removeClass("field-error");

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

