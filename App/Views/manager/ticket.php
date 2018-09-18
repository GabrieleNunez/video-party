<form action="/manager/tickets" method="POST" class="editable">
	<input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>" />
	<div class="row ticket" data-ticket="<?php echo $ticket['id'];?>">
		<input type="hidden" class="ticket-new" name="ticket_new" id="ticket_new_<?php echo $ticket['id'];?>" value="0" />
		<div class="small-12 medium-4 large-4 columns">
			<span class="ticket-user primary-element" data-visual="ticket-user"><?php echo $ticket['username']; ?></span>
			<input type="text" placeholder="Username..." class="ticket-user editing-element" data-visual="ticket-user" data-original="<?php echo $ticket['username']; ?>" value="<?php echo $ticket['username']; ?>" name="ticket_user" id="ticket_user_<?php echo $ticket['id']; ?>"  />
		</div>
		<div class="small-12 medium-4 large-4 columns">
			<span class="ticket-code primary-element" data-visual="ticket-code"><?php echo $ticket['code']; ?></span>
			<input class="ticket-code editing-element" placeholder="Unique Ticket Code..." type="text" data-visual="ticket-code" data-original="<?php echo $ticket['code']; ?>" value="<?php echo $ticket['code']; ?>" name="ticket_code" id="ticket_code_<?php echo $ticket['id'];?>" />
		</div>
		<div class="small-12 medium-2 large-4 columns primary-action">
			<button type="button" data-action="edit" data-flag="ticket-update"><i class="fi-pencil"></i></button>
			<input class="ticket-updated" type="hidden" name="ticket_updated" data-flag="ticket-update" id="ticket_deleted_<?php echo $ticket['id']?>" value="0" data-original="0" />

			<button type="button" data-action="delete" data-flag="ticket-delete"><i class="fi-x"></i></button>
			<input class="ticket-deleted" name="ticket_deleted" data-flag="ticket-delete" id="ticket_deleted_<?php echo $ticket['id'];?>" type="hidden" value="0" data-original="0" />
		</div>
		<div class="small-12 medium-2 large-4 columns editing-action">
			<button type="submit" data-action="save"><i class="fi-save"></i></button>
			<button type="button" data-action="cancel"><i class="fi-trash"></i></button>
		</div>
	</div>
</form>