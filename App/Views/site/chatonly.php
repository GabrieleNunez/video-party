<!-- this is super  ragtag. But I don't care. I am tired. Very tired. Like I need some bean burrito tired. Like coffee sipping tired. Are you stil reading this? It's a total hackjob Omega Lulz -->
<div id="videoArea" chatonly>
	<div class="videoplayer" id="videoplayer">
		<div id="hotbar" style="display:none">
			<div  class="hotbar-button" id="viewersButton"><i class="fi-eye large"></i> <span id="viewers"><?php echo count(
       $viewers
   ); ?></span></div>
			<div  class="hotbar-button" id="showChatButton"><i class="fi-comments large"></i><span id="newComments"></span></div>
			<div class="clear"></div>
		</div>
		<div id="chatArea">
			<div id="chatWindow">
				<div id="chat-slide" class="slide">
					<form action="/chat" method="POST" data-ping="1000"  id="chatForm" data-username="<?php echo $ticket_username; ?>">
						<ul id="chatMessages">
						</ul>
						<div id="chatBottomControls">
							<hr />
							<input type="text" name="message" id="message" placeholder="Send a message to chat" autocomplete="off" />
							<input type="hidden" name="ticket" id="ticket" value="<?php echo $ticket_code; ?>" />
							<button id="submitButton" class="button submit expanded" type="submit">Send Message</button>
							<button id="hideChatButton" class="button expanded" type="button" style="display:none">Hide Chat</button>
						</div>
					</form>
				</div>
				<div id="viewer-slide" class="slide">
					<ul id="viewerlist">
						<?php foreach ($viewers as $viewer) { ?>
							<li data-username="<?php echo $viewer['username']; ?>"><?php echo $viewer['username']; ?></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>