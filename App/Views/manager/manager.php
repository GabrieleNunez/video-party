
<div id="managerArea">

	<!-- player action section -->
	<div class="section">
		<h4>Player Actions - TODO:</h4>
	</div>

	<!-- chat action section -->
	<div class="section">
		<h4>Chat Actions - TODO:</h4>
	</div>

	<!-- create tickets using this form -->
	<div class="section" id="ticketSection">
		<h4>Tickets</h4>
		<div class="row headers">
			<div class="small-12 medium-4 large-4 columns">
				<span class="header">Username</span>
			</div>
			<div class="small-12 medium-4 large-4 columns">
				<span class="header">Code</span>
			</div>
			<div class="small-12 medium-4 large-4 columns">
				<span class="header">Actions</span>
			</div>
		</div>
		<?php 
			// render our ticket using the ticket template, this way it's consistent with what we can send back from the server
			foreach($tickets as $ticket) { 
				echo \Library\View::make('/manager/ticket.php')->with(array('ticket' => $ticket));
			}
		?>
		<form action="/manager/tickets" method="POST" class="editing new-form">
			<input type="hidden" name="ticket_id" value="0" />
			<div class="row ticket editing" data-ticket="0">
				<input type="hidden" vname="ticket_new" id="ticket_new_0" value="1" />
				<div class="small-12 medium-4 large-4 columns">
					<input type="text" class="ticket-user" value="" placeholder="Username here..." name="ticket_user" data-original="" />
				</div>
				<div class="small-12 medium-4 large-4 columns">
					<input type="text" class="ticket-code" value="" placeholder="Code here..." name="ticket_code" data-original="" />
				</div>
				<div class="small-12 medium-4 large-4 columns editing-action">
					<button type="submit" data-action="save"><i class="fi-save"></i></button>
					<button type="button" data-action="clear"><i class="fi-trash"></i></button>
					<input type="hidden" name="ticket_new" value="1" />
					<input type="hidden" name="ticket_updated" value="0" />
					<input type="hidden" name="ticket_deleted" value="0" />
				</div>
			</div>
		</form>
	</div>

	<div class="section">
		<!-- the video form handles updating videos on the server -->
		<form action="/manager/videos" method="POST" id="videoForm">
			<h4>Videos</h4>
			<div class="row video">
				<div class="small-12 medium-4 large-3 columns">
					<label for="video_name">Title</label>
				</div>
				<div class="small-12 medium-4 large-7 columns">
					<span>Video Title Here</span>
				</div>
				<div class="small-12 medium-2 large-1 columns">
					<button class="button expanded positive" type="button" data-action="rename"><i class="fi-pencil"></i></button>
				</div>
				<div class="small-12 medium-2 large-1 columns">
					<button class="button expanded negative" type="button" data-action="delete"><i class="fi-x"></i></button>
				</div>
			</div>
		</form>
	</div>

	<div class="section">
		<!-- the playlist form handles updating playlist information -->
		<form action="/manager/playlist" method="POST" id="playlistForm">
			<h4>Playlist</h4>
			<div class="row playlist-item">
				<div class="small-12 medium-4 large-3 columns">
					<label for="playlist_item">Video</label>
				</div>
				<div class="small-12 medium-4 large-5 columns">
					<span>Video Title Here</span>
				</div>
				<div class="small-12 medium-1 large-1 columns">
					<button class="button expanded positive" type="button" data-action="play"><i class="fi-play"></i></button>
				</div>
				<div class="small-12 medium-1 large-1 columns">
					<button class="button expanded negative" type="button" data-action="delete"><i class="fi-x"></i></button>
				</div>
				<div class="small-12 medium-1 large-1 columns">
					<button class="button expanded" type="button" data-action="move-up"><i class="fi-arrow-up"></i></button>
				</div>
				<div class="small-12 medium-1 large-1 columns">
					<button class="button expanded" type="button" data-action="move-down"><i class="fi-arrow-down"></i></button>
				</div>
			</div>
		</form>
	</div>
</div>