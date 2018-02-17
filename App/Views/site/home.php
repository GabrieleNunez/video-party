<div id="homepage">
	<div class="padding">
		<div class="row maximum expanded">
			<div class="small-12 medium-12 large-8 columns">
				<div class="videoplayer" id="videoplayer">
					<video data-stream-file="<?php echo $player_stream_file;?>" data-current="<?php echo $player_time; ?>" class="video-js <?php echo $ticket_master ? '' : 'watcher'; ?>" preload="auto" controls="controls" <?php echo $ticket_master ? 'data-master="1"' : 'data-master="0"'; ?>>
						<source src="<?php echo $player_stream_file; ?>" type="application/x-mpegURL"></source>
					</video>
				</div>
			</div>
			<div class="small-12 medium-12 large-4 columns">
				<div id="chatwindow">
					<div class="stat-bar">
						<div class="row small-up-2 medium-up-4 large-up-4">
							<div class="column column-block">
								<a href="#" href="/chat" id="chatButton" class="button expanded slide-button" data-slide="chat-slide">Chat</a>
							</div>
							<div class="column column-block">
								<a href="#" href="/viewerlist" id="viewerlistButton" class="button expanded slide-button" data-slide="viewer-slide">Viewers:<span id="viewers"><?php echo count($viewers); ?></span></a>
							</div>
						</div>
					</div>
					<div id="chat-slide" class="slide">
						<form action="/chat" method="POST" data-ping="1000"  id="chatForm">
							<ul id="chatMessages">
							</ul>
							<hr />
							<input type="text" name="message" id="message" placeholder="Send a message to chat" />
							<input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket_code; ?>" />
							<button id="submitButton" class="button expanded" type="submit">Send Message</button>
						</form>
					</div>
					<div id="viewer-slide" class="slide">
						<ul id="viewerlist">
							<?php foreach($viewers as $viewer) { ?>
								<li data-username="<?php echo $viewer['username'];?>"><?php echo $viewer['username']; ?></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>