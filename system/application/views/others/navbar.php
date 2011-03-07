<ul>
	<li><a href="javascript:history.back();" class="history_back">Back</a></li>
	<li><a href="javascript:window.location.reload();" class="history_reload">Reload</a></li>
	<li><a href="javascript:history.forward();" class="history_forth">Forth</a></li>
	<li>
		<?php 
		if ($this->auth->is_logged_in())
			echo '<a href="/profile">Profile</a>';
		else
			echo '<a href="/home">Home</a>';
		?>
	</li>
	<div class="welcome-message"><?php
	
	if ($this->auth->is_logged_in()) {
		echo 'Welcome, '.$this->auth->get_first_name().' '.$this->auth->get_last_name();
	} else {
		echo 'Welcome to Salute!';
	}
	
	?></div>
</ul>
