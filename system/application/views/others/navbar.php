<ul>
	<li><a href="javascript:history.back();" class="ajax history_back">Back</a></li>
	<li><a href="javascript:history.forward();" class="ajax history_forth">Forth</a></li>
	<li>
		<?php 
		if ($this->auth->is_logged_in())
			echo '<a href="/profile" class="ajax">Profile</a>';
		else
			echo '<a href="/home" class="ajax">Home</a>';
		?>
	</a></li>
</ul>
