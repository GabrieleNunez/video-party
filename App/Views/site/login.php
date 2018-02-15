<div id="loginPage">
	<form action="/login" method="POST" id="loginForm">
		<h4>Please enter your ticket code</h4>
		<div class="row">
			<div class="small-12 medium-12 large-3 columns">
				<label for="code">Code:</label>
			</div>
			<div class="small-12 medium-12 large-9 columns">
				<input type="text" name="code" id="code" placeholder="Ticket code..." />
			</div>
		</div>
		<div class="row">
			<div class="small-12 medium-12 large-9 large-offset-3 columns">
				<button type="submit" id="submitButton" class="button expanded">Login</button>
			</div>
		</div>
	</form>
</div>