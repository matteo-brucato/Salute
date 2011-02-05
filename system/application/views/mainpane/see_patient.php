<h2>Patient Profile</h2>

<table class="tables-1">
	<tr>
		<th colspan="2"><center><?php echo $info['first_name'];?></center></th>
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

<ul>
	<li><a href="">Send message</a></li>
</ul>
<ul>
	<li><a href="">Delete connection with this patient</a></li>
</ul>

<?php
$this->load->helper('actions_helper');
echo '<ul>';
echo get_action_strings(array('send-email','delete-conn'), $info);
echo '</ul>';
?>
