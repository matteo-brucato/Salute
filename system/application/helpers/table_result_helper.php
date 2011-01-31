<?php
function view_table($table) {
	echo '<table class="tables-1" id="'.$table['table-name'].'" cellpadding="0" cellspacing="0">';
	echo '<tr>';
	for ($i = 0; $i < count($table['th']); $i++) {
		echo '<th class="'.$table['th_class'][$i].'">'.$table['th'][$i].'</th>';
	}
	echo '</tr>';
	foreach ($table['tuples'] as $tr) {
		echo '<tr>';
			for ($i = 0; $i < count($table['attr']); $i++) {
				echo '<td class="'.$table['td_class'][$i].'">'.$tr[$table['attr'][$i]].'</td>';
			}
		echo '</tr>';
	}
	echo '</table>';
}
?>
