<h1>My 
<?php
if ($this->session->userdata('type') === 'patient')
	echo 'Doctors';
else
	echo 'Collegues';
?>
</h1>

<?php $hcp_list = array(
	array('first_name' => 'Mario', 'last_name' => 'Rossi', 'specialty' => 'Murderer'),
	array('first_name' => 'Matteo', 'last_name' => 'Brucato', 'specialty' => 'Surgeon')
); ?>

<table class="tables-1" cellpadding="0" cellspacing="0">
	<tr>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Specialty</th>
		<th>Actions</th>
	</tr>
	<?php foreach ($hcp_list as $hcp) : ?>
	<tr>
		<td><?php echo $hcp['first_name']; ?></td>
		<td><?php echo $hcp['last_name']; ?></td>
		<td><?php echo $hcp['specialty']; ?></td>
		<td>
			<ul>
				<li><a href="">Send Email</a></li>
				<li><a href="">Delete Connection</a></li>
				<?php if ($this->session->userdata('type') === 'patient') : ?>
				<li><a href="">Request Appointment</a></li>
				<?php endif ?>
			</ul>
		</td>
	</tr>
	<? endforeach ?>
</table>
