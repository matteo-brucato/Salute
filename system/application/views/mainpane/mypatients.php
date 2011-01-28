<h1>My Patients</h1>

<table class="tables-1" id="mypatients-table" cellpadding="0" cellspacing="0">
	<tr>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Actions</th>
	</tr>
	<?php foreach ($pat_list as $hcp) : ?>
	<tr>
		<td><a class="ajaxlink" href="/profile/view_patient/<?php echo $hcp['account_id']; ?>"><?php echo $hcp['first_name']; ?></a></td>
		<td><?php echo $hcp['last_name']; ?></td>
		<td>
			<ul>
				<li><a href="">Send Email</a></li>
				<li><a href="">Delete Connection</a></li>
				<li><a href="">Upload Medical Record</a></li>
				<li><a href="">Issue Bill</a></li>
			</ul>
		</td>
	</tr>
	<? endforeach ?>
</table>