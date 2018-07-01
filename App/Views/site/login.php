<div id="loginPage">
	<form action="/login" method="POST" id="loginForm">
		<h4>Please enter your ticket code</h4>
		<div class="row">
			<div class="small-12 medium-4 large-3 columns">
				<label for="code">Code</label>
			</div>
			<div class="small-12 medium-8 large-9 columns">
				<input type="password" name="code" id="code" placeholder="Ticket code..." />
			</div>
		</div>
		<div class="row">
			<div class="small-12 medium-4 large-3 columns">
				<label>Chat Only</label>
			</div>
			<div class="small-12 medium-8 large-9 columns">
				<div class="switch large">
					<input class="switch-input" id="chatmode_only" name="chatmode_only" type="checkbox" value="1" />
					<label for="chatmode_only" class="switch-paddle">
						<span class="switch-active">On</span>
						<span class="switch-inactive">Off</span>
					</label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="small-12 medium-12 large-12 columns">
				<hr />
				<p>
					<span class="red bold">Note:</span> &quot;<span class="italic underline">Chat Only</span>&quot; is designed for people who want to use a phone or other device solely for chat. 
					You can login on your xbox and use your phone to make keeping up with chat easier. Portrait mode works best and this is not a optimized feature and may have layout issues depending on your device
				</p>
				<button type="submit" id="submitButton" class="button expanded">Login</button>
			</div>
		</div>
	</form>
</div>