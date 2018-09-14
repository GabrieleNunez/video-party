
<div id="managerArea">
	
	<form action="/manager/player" method="POST">
		<h4>Player Actions - TODO:</h4>
	</form>

	<form action="/manager/chat" method="POST">
		<h4>Chat Actions - TODO:</h4>
	</form>

	<!-- create tickets using this form -->
	<form action="/manager/tickets" method="POST">
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
		<div class="row ticket">
			<div class="small-12 medium-4 large-4 columns">
				<span class="ticket-user">Username Here</span>
			</div>
			<div class="small-12 medium-4 large-4 columns">
				<span class="ticket-code">Code Here</span>
			</div>
			<div class="small-12 medium-2 large-2 columns">
				<button class="button expanded positive" type="button" data-action="edit"><i class="fi-pencil large"></i></button>
			</div>
			<div class="small-12 medium-2 large-2 columns">
				<button class="button expanded negative" type="button" data-action="delete"><i class="fi-x large"></i></button>
			</div>
		</div>

	</form>

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
				<button class="button expanded positive" type="button" data-action="rename"><i class="fi-pencil large"></i></button>
			</div>
			<div class="small-12 medium-2 large-1 columns">
				<button class="button expanded negative" type="button" data-action="delete"><i class="fi-x large"></i></button>
			</div>
		</div>
	</form>

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
				<button class="button expanded positive" type="button" data-action="play"><i class="fi-play large"></i></button>
			</div>
			<div class="small-12 medium-1 large-1 columns">
				<button class="button expanded negative" type="button" data-action="delete"><i class="fi-x large"></i></button>
			</div>
			<div class="small-12 medium-1 large-1 columns">
				<button class="button expanded" type="button" data-action="move-up"><i class="fi-arrow-up large"></i></button>
			</div>
			<div class="small-12 medium-1 large-1 columns">
				<button class="button expanded" type="button" data-action="move-down"><i class="fi-arrow-down large"></i></button>
			</div>
		</div>
	</form>
</div>