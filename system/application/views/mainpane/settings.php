<h2>Settings</h2>

<ul>
	<li><a href="/settings/change_email">Change Email</a></li>
	<li><a href="/settings/change_password">Change Password</a></li>
	<?php if ($this->auth->get_type() === 'patient') 
		echo '<li><a href="/settings/change_privacy">Change Privacy</a></li>'; ?>
	<li><a href="/settings/change_picture">Change Picture</a></li>
	<li><a href="/settings/deactivate" class="confirm">Deactivate</a></li>
</ul>
