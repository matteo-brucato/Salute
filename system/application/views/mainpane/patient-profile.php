<div id="welcome-profile">
<h1>Welcome <?php
	echo $this->session->userdata('first_name').' '
	.$this->session->userdata('last_name');
?>!</h1>
</div>

<div id="profile-log-actions">
	<ul>
		<li><a href="">Log out</a></li>
	</ul>
</div>
