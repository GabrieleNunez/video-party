
<div id="videoArea" class="chat">
	<div class="videoplayer" id="videoplayer">
		<video autoplay playsinline playinline data-stream-file="<?php echo $player_stream_file;?>" data-current="<?php echo $player_time; ?>" class="video-js <?php echo $ticket_master ? '' : 'watcher'; ?>" preload="auto" controls="controls" <?php echo $ticket_master ? 'data-master="1"' : 'data-master="0"'; ?>>
			<source src="<?php echo $player_stream_file; ?>" type="application/x-mpegURL"></source>
		</video>
		<div id="hotbar">
			<div class="hotbar-button" id="viewersButton" title="Viewers"><i class="fi-eye large"></i> <span id="viewers"><?php echo count($viewers); ?></span></div>
			<div class="hotbar-button" id="showChatButton" title="View Chat"><i class="fi-comments large"></i><span id="newComments"></span></div>
			<div class="hotbar-button" id="chatPopupButton" title="Pop out Chat"><i class="fi-page-edit large"></i></div>
			<div class="hotbar-button" id="reloadPageButton" title="Reload Page" onclick="window.location.reload(true);"><i class="fi-refresh large large"></i></div>
			<div class="hotbar-button" id="logoutButton" title="Logout"><i class="fi-power large"></i></div>
			<div class="clear"></div>
		</div>
		<div id="chatArea">
			<div id="chatWindow">
				<div id="chat-slide" class="slide">
					<form action="/chat" method="POST" data-ping="1000"  id="chatForm" data-username="<?php echo $ticket_username; ?>">
						<ul id="chatMessages">
						</ul>
						<div class="bottom" id="chatBottomControls">
							<hr />
							<input type="text" name="message" id="message" placeholder="Send a message to chat" autocomplete="off" />
							<input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket_code; ?>" />
							<button id="submitButton" class="button submit expanded" type="submit">Send Message</button>
							<button id="hideChatButton" class="button expanded" type="button">Hide Chat</button>
						</div>
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
	<div id="fullscreenPrompt" class="hide-for-small-only">
		<div class="row">
			<div class="small-12 medium-6 large-6 columns">
				<button id="fullScreenButton" class="button expanded">Go Full Screen</button>
			</div>
			<div class="small-12 medium-6 large-6 columns">
				<button id="fullScreenCancelButton" class="button expanded">No I'm good :)</button>
			</div>
		</div>
	</div>
</div>