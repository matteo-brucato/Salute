<h3>HCP Account</h3>
<nl>
<!--	<li><a href="/messages">Messages</a>
		<ul>

			<li><a href="">Inbox</a></li>
			<li><a href="">Sent</a></li>
			<li><a href="/messages/compose">Compose</a></li>
		</ul>
	</li>
-->	
	<li><a href="/appointments">Appointments</a>
		<ul>
			<li><a href="/appointments/all">All</a></li>
			<li><a href="/appointments/upcoming">Upcoming</a></li>
			<li><a href="/appointments/past">Past</a></li>
			<!--li><a href="/appointments/request">Requests</a></li-->
		</ul>
	</li>
	
	<li><a href="/bills">Bills</a>
		<ul>
			<li><a href="/bills/all">All</a></li>
			<li><a href="/bills/current">Current</a></li>
			<li><a href="/bills/past">Past</a></li>
		</ul>
	
	<li><a href="/connections/mypatients" class="ajax">My Patients</a>
		<ul>
			<li><a href="/connections/pending/in">Pending incoming</a></li>
		</ul>
	</li>
	
	<li><a href="/connections/myhcps" class="ajax">My Collegues</a>
		<ul>
			<li><a href="/connections/pending/out">Pending outgoing</a></li>
			<li><a href="/connections/pending/in">Pending incoming</a></li>
		</ul>
	</li>
	
	<!--<li><a href="/referal/?" class="ajax">My Referals</a>
	</li> -->
	
	<li><a href="/settings" class="ajax">Settings</a>
	</li>
	
	<li><a href="/search">Advanced Search</a>
	<li><a href="/home/logout">Logout</a>
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
