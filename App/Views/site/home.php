<div id="homepage">
	<div class="row maximum">
		<div class="small-12 medium-6 large-8 columns">
			<div class="videoplayer" id="videoplayer">
				<video class="video-js" controls="controls"  preload="auto">
					<source src="/movie1/movie1.m3u8" type="application/x-mpegURL"></source>
				</video>
			</div>
		</div>
		<div class="small-12 medium-6 large-4 columns">
			<div id="chatwindow">
				<form action="/chat" method="POST" data-ping="1000"  id="chatForm">
					<ul id="chatMessages">
					</ul>
					<hr />
					<input type="text" name="message" id="message" />
					<input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket_code; ?>" />
					<button id="submitButton" class="button expanded" type="submit">Send Message</button>
				</form>
			</div>
		</div>
	</div>
</div>