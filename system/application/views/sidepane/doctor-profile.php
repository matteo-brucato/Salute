<nl>
	<li><a href="">Messages</a>
		<ul>
			<li href="#introduction"><a href="">Inbox</a></li>
			<li href="#may"><a href="">Sent</a></li>
			<li href="#should"><a href="">Compose</a></li>
		</ul>
	</li>
	
	<li><a href="">Appointments</a>
		<ul>
			<li href="#introduction"><a href="">Upcoming</a></li>
			<li href="#introduction"><a href="">Requests</a></li>
		</ul>
	</li>
	
	<li><a href="">Bills</a>
		<ul>
			<li href="#introduction"><a href="">All</a></li>
			<li href="#may"><a href="">Current</a></li>
			<li href="#may"><a href="">Past</a></li>
		</ul>
	
	<li><a href="">My Patients</a>
	
	<li><a href="/connection/list_doctors">My Collegues</a>
	
	<li><a href="">Advanced Search</a>
	<form method="post" action="/" id="quick-search-form">
		<fieldset id="quick-search-fs">
			<legend>Quick Serach</legend>
			<label for="quick-search-field"></label><br />
			<input type="text" name="quick-search-field" id="quick-search-field" class="input-field" /><br />
			<label for="radio-medical">Patients</label>
			<input type ="radio" name="quick-search-radio" value="medical-record" id="radio-medical" checked="checked" /><br />
			<label for="radio-doctor">Doctors</label>
			<input type ="radio" name="quick-search-radio" value="doctor" id="radio-doctor" /><br />
			<label for="radio-message">Messages</label>
			<input type ="radio" name="quick-search-radio" value="message" id="radio-message" /><br />
			<p>
				<input type="submit" name="submit" value="Search" class="submit-button" />
			</p>
		</fieldset>
	</form>
</nl>
