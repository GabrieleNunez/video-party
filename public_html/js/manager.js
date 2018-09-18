$(document).ready(function() {

	// initiate foundation
	$(document).foundation();


	// hook all ticket forms on the page
	hook_ticket_forms();

	// buttons that set flags
	$('div#managerArea').on('click','button[data-flag]', function(event) {
		var self = $(this);
		var parent_form = $(self).parents('form').first();
		var flag_element = $(parent_form).find('input[data-flag="' + $(self).data('flag') + '"]').first();
		$(flag_element).val('1'); // set the input to value 1
	});

	// any buttons with an action should be triggered and handled appropriatly
	$('div#managerArea').on('click','button[data-action="cancel"],button[data-action="clear"]', function(event) {

		
		var self = $(this);
		var parent_form = $(self).parents("form").first();
		var action = $(self).data("action");

		// reset all fields that have an original value back to that original value
		$(parent_form).find('input[data-original]').each(function(index, element) {
			$(element).val($(element).data("original"));
			var has_visual = $(element).data("visual") && $(element).data("visual").length > 0 ? true : false;
			if(has_visual) {
				var visual_element = $(parent_form).find('span[data-visual="' + $(element).data("visual") + '"]').first();
				visual_element.html($(element).data("original"));
			}
		});

		if(action == 'cancel')
			$(parent_form).removeClass("editing");

	});

	// edit action
	$('div#managerArea').on('click','button[data-action="edit"]', function(event) {
		var self = $(this);
		var parent_form = $(self).parents("form").first();
		$(parent_form).addClass("editing");
	});

	// delete action
	$('div#managerArea').on('click','button[data-action="delete"]', function(event) {

		var self = $(this);
		var parent_form = $(self).parents('form').first();
		var flag_element = $(parent_form).find('input[data-flag="' + $(self).data('flag') + '"]').first();
		$(flag_element).val('1'); // set the input to value 1
		$(parent_form).submit(); // submit the form

	});

});

// hook any and all ticket forms on the page
function hook_ticket_forms() {

	$('div.section#ticketSection form').each(function(index, item) {

		// hook in our ajax form plugin
		$(item).ajaxForm({
			success : ticket_save_success,
			error : ticket_save_error
		});

	});

}


// whenever we save our tickets this callback will handle it
function ticket_save_success(ajax) {

	if(ajax.success) {

		var ticket_body = ajax.response.ticket_html;
		var ticket_id = ajax.response.ticket.id;

		console.log(ajax.response.flags);

		// we are updating our ticket so replace the content 
		if(ajax.response.flags.ticket_update) {
			console.log('Update');
			var ticket_row = $('div.ticket[data-ticket="' + ticket_id + '"]');
			$(ticket_row).parents('form.editable').first().replaceWith(ticket_body);

			// reselect our ticket row and then hook the ajax form 
			var ticket_row = $('div.ticket[data-ticket="' + ticket_id + '"]');
			var parent_form = $(ticket_row).parents('form.editable').first();
			$(parent_form).ajaxForm({
				success : ticket_save_success,
				error : ticket_save_error
			});

		} else if(ajax.response.flags.ticket_delete) {

			console.log('Deleting');
			console.log('div.ticket[data-ticket="' + ticket_id + '"]');

			var ticket_row = $('div.ticket[data-ticket="' + ticket_id + '"]');
			$(ticket_row).parents('form.editable').first().remove();
		} else if(ajax.response.flags.ticket_new) {
			
			console.log('new');

			$('div#ticketSection form.new-form').before(ticket_body);
			$('div#ticketSection form.new-form button[data-action="clear"]').click();

			// reselect our ticket row and then hook the ajax form 
			var ticket_row = $('div.ticket[data-ticket="' + ticket_id + '"]');
			var parent_form = $(ticket_row).parents('form.editable').first();
			$(parent_form).ajaxForm({
				success : ticket_save_success,
				error : ticket_save_error
			});
			
		}



	} else {
		alert("Ticket information did not save");
	}
}

// whenever we save our tickets and our response completely fails this callback will trigger
function ticket_save_error(ajax) {
	alert("An internal error has occurred");
}