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
</ul>
