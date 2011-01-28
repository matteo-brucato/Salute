<table class="tables-1" id="<?php echo $table['table-name'];?>" cellpadding="0" cellspacing="0">
	<tr>
		<?php for ($i = 0; $i < count($table['th']); $i++) : ?>
		<th class="<?php echo $table['th_class'][$i];?>"><?php echo $table['th'][$i];?></th>
		<? endfor ?>
	</tr>
	<?php foreach ($table['tuples'] as $tr) : ?>
	<tr>
		<?php for ($i = 0; $i < count($table['attr']); $i++) : ?>
		<td class="<?php echo $table['td_class'][$i];?>"><?php echo $tr[$table['attr'][$i]];?></td>
		<? endfor ?>
	</tr>
	<? endforeach ?>
</table>
