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
				<td>Middle Name</td>
				<td><?php echo $info['middle_name'];?></td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td><?php echo $info['last_name'];?></td>
			</tr>
			<tr>
				<td>Date of birth</td>
				<td><?php echo $info['dob'];?></td>
			</tr>
			<tr>
				<td>Sex</td>
				<td><?php echo $info['sex'];?></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><?php echo $info['address'];?></td>
			</tr>
			<tr>
				<td>Phone number</td>
				<td><?php echo $info['tel_number'];?></td>
			</tr>
			<tr>
				<td>Fax number</td>
				<td><?php echo $info['fax_number'];?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<?php
?>
