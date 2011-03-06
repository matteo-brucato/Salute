<h2><?php echo $info['first_name'];?>'s Profile</h2>

<table width="100%" cellpadding="0" cellspacing="5" border="0">
	<tr>
		<td valign="top" width="5"><img class="profile-picture" src="/download/account_picture/<?php echo $aid; ?>"/></td>
		<td valign="top">
		<table class="tables-1">
			<tr>
				<th colspan="2"><center><?php echo $info['first_name'];?> <?php echo $info['last_name'];?></center></th>
			</tr>
			<tr>
				<td>First Name</td>
				<td><?php echo $info['first_name'];?></td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td><?php echo $info['last_name'];?></td>
			</tr>

		</table>
		</td>
	</tr>
</table>
<?php
?>
