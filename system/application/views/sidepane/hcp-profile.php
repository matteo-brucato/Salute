<h3>HCP Account</h3>
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
			<li href="#introduction"><a href="/bills/all">All</a></li>
			<li href="#current"><a href="/bills/current">Current</a></li>
			<li href="#past"><a href="/bills/past">Past</a></li>
		</ul>
	
	<li><a href="/connections/mypatients" class="ajaxlink">My Patients</a>
		<ul>
			<li><a href="/connections/pending/in">Pending incoming</a></li>
		</ul>
	</li>
	
	<li><a href="/connections/myhcps" class="ajaxlink">My Collegues</a>
		<ul>
			<li><a href="/connections/pending/out">Pending outgoing</a></li>
			<li><a href="/connections/pending/in">Pending incoming</a></li>
		</ul>
	</li>
	
	<li><a href="/search">Advanced Search</a>
	<form method="post" action="/" id="quick-search-form">
		<fieldset id="quick-search-fs">
			<legend>Quick Serach</legend>
			<label for="quick-search-field"></label><br />
			<input type="text" name="quick-search-field" id="quick-search-field" class="input-field" /><br />
			
			<input type ="radio" name="quick-search-radio" value="medical-record" id="radio-medical" checked="checked" />
			<label for="radio-medical">Patients</label><br />
			
			<input type ="radio" name="quick-search-radio" value="doctor" id="radio-doctor" />
			<label for="radio-doctor">Doctors</label><br />
			
			<input type ="radio" name="quick-search-radio" value="message" id="radio-message" />
			<label for="radio-message">Messages</label><br />
			
			<p>
				<input type="submit" name="submit" value="Search" class="submit-button" />
			</p>
		</fieldset>
	</form>
</nl>
