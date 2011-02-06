<div id="profile-welcome">
<h5>Doctor Application</h5>
<h1>Welcome <?php
	echo $this->session->userdata('first_name').' '
	.$this->session->userdata('last_name');
?>!</h1>
</div>

<div id="profile-log-actions">
	<ul>
		<li><a href="/profile/edit">Edit Info</a></li>
		<li><a href="/home/logout">Log out</a></li>
	</ul>
</div>

<!--div id="notifications">
	<h2>Notifications</h2>
	<ul>
		<li><a href=""></a></li>
	</ul>
</div-->
