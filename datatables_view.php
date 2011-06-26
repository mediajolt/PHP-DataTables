<div class='sortableTableNew' id='stn<?php echo $this->id ?>'>

<?php if ( sizeof($this->actions) > 0 || $this->enable_excel == '1' ) { ?>

<div class="tableToolbar">

	<select name="stn<?php echo $this->id ?>Actions" id="stn<?php echo $this->id ?>Actions">
		<option value="">-- Bulk Actions --</option>
		<?php

		foreach ( $this->actions as $action ) {

			print "<option value=\"" . $action['js_function'] . "\">" . $action['label'] . "</option>";

		}

		if ( $this->enable_excel == '1' ) {

			print "<option value=\"stn{$this->id}ExportCSV();\">Export to CSV File</option>";

		}

		?>
	</select>

	<input type="button" onClick="stn<?php echo $this->id ?>ApplyAction();" value="Apply" />

</div>

<script type="text/javascript">
function stn<?php echo $this->id ?>ApplyAction() {
	var tableFunction = $("#stn<?php echo $this->id ?>Actions").val();
	if ( tableFunction != '' ) {
		eval(tableFunction);
	}
}
function stn<?php echo $this->id ?>ExportCSV() {
	window.location = "<?php echo $this->processor_path; ?>?export_excel=1&hash=<?php echo $this->refnum; ?>";
}
</script>

<?php } ?>

<table cellpadding="0" cellspacing="0" border="0" class="comparison" id="<?php echo $this->id; ?>">
<thead>
<tr>

<?php

function processHeaderSorting($header, $counter, $valid_counter) {
	if ( @$header['default_sort'] != '' ) {
		if ( $valid_counter > 0 ) {
			print ",";
		}
		print "[{$counter}, '" . $header['default_sort'] . "']";
		$valid_counter++;
	}
	$counter++;

	$return = array();
	$return['counter'] = $counter;
	$return['valid_counter'] = $valid_counter;
	return $return;
}

function processHeaderFull($header) {
	global $counter;
	$printed = 0;
	$output = '';

	if ( $counter > 0 ) {
		print ",\n";
	}

	if ( @$header['type'] != '' ) {
		$output .= "'sType' : '" . $header['type'] . "'";
		$printed = 1;
	}

	if ( @$header['visible'] == '0' ) {
		if ( $printed == 1 ) { $output .= ", "; }
		$output .= "'bVisible' : false";
		$printed = 1;
	}

	if ( @$header['sortable'] == '0' ) {

		if ( $printed == 1 ) { $output .= ", "; }

		$output .= "'bSortable' : false ";

		$printed = 1;

	}

	if ( @$header['class'] != '' ) {

		if ( $printed == 1 ) { $output .= ", "; }

		$output .= "'sClass' : '" . $header['class'] . "' ";

		$printed = 1;

	}

	if ( @$header['width'] != '' ) {

		if ( $printed == 1 ) { $output .= ", "; }

		$output .= "'sWidth' : '" . $header['width'] . "' ";

		$printed = 1;

	}

	if ( @$header['checkbox'] == '1' ) {

		if ( $printed == 1 ) { $output .= ", "; }

		$output .= "'sClass' : 'dtCenter' ";

		$printed = 1;

	}

	if ( @$header['align'] != '' ) {

		if ( $printed == 1 ) { $output .= ", "; }

		switch ( @$header['align'] ) {

			case "center" :
			$output .= "'sClass' : 'dtCenter' ";
			break;

			case "default" :
			$output .= "'sClass' : 'dtLeft' ";
			break;

		}

		$printed = 1;

	}

	if ( $output != '' ) {

		print "{ ";
		print $output;
		print "} ";

	} else {

		print "null";

	}

	$counter++;

}

$children = 0;

foreach ( $this->headers as $header ) {

	if ( !empty($header['data']['class']) ) {
		$class = $header['data']['class'];
	} else {
		$class = '';
	}

	if ( sizeof(@$header['children']) > 0 ) {

		// This header has child headers.

		$children += sizeof($header['children']);

		print "<th class='{$class}' colspan='" . sizeof($header['children']) . "'><b>{$header['data']['label']}</b></th>";

	} else {

		// This header has no child headers.

		if ( @$header['data']['checkbox'] != '1' ) {
			print "<th class='{$class}' rowspan='{$this->header_rowspan}'><b>{$header['data']['label']}</b></th>";
		} else {
			print "<th class='{$class}' rowspan='{$this->header_rowspan}'><input type='checkbox' /></th>";
		}

	}

}

?>

</tr>

<?php

if ( $children > 0 ) {

	print "<tr>";

	foreach ( $this->headers as $header ) {

		if ( sizeof(@$header['children']) > 0 ) {

			// This header has child headers.

			foreach ( $header['children'] as $key => $child ) {

				if ( !empty($child['class']) ) {
					$class = $child['class'];
				} else {
					$class = '';
				}

				print "<th class='{$class}'><b>{$child['data']['label']}</b></th>";

			}

		}

	}

	print "</tr>";

}

?>
</thead>

<?php if ( $this->has_header_data_row == '1' ) { ?>

<thead>
	<tr>
		<?php if ( $this->enable_checkbox == '1' ) { print "<th></th>"; } ?>

		<?php
		
		foreach ( $this->headers as $header ) {
			
			if ( sizeof(@$header['children']) > 0 ) {

				foreach ( $header['children'] as $header_child ) {
					
					$th_class = @$this->header_data_rows[$header_child['data']['id']]['class'];
					
					if ( !empty($this->header_data_rows[$header_child['data']['id']]) ) {

						print "<th class='{$th_class}'>" . $this->header_data_rows[$header_child['data']['id']]['value'] . '</th>';

					} else {
						
						print '<th></th>';
						
					}
					
				}
				
			} else {
				
				if ( @$header['data']['checkbox'] != '1' ) {
					
					$th_class = @$this->header_data_rows[$header['data']['id']]['class'];
				
					if ( !empty($this->header_data_rows[$header['data']['id']]) ) {

						print "<th class='{$th_class}'>" . $this->header_data_rows[$header['data']['id']]['value'] . '</th>';

					} else {
					
						print '<th></th>';
					
					}
				
				}
				
			}

		}

		?>
	</tr>
</thead>

<?php } ?>

<tbody>
</tbody>
</table>

<script type="text/javascript">
	$(document).ready(function() {
		setTimeout("init<?php echo $this->id; ?>();", 750);
	} );

	function init<?php echo $this->id; ?>() {

		<?php echo $this->id ?> = $('#<?php echo $this->id ?>').dataTable({

			"fnDrawCallback": function() {

				$("#stn<?php echo $this->id ?> .ajaxIMG").fadeOut("slow");
				$("#stn<?php echo $this->id ?> .export").show();

				$("#stn<?php echo $this->id ?> tr th input[type='checkbox']").change(function() {

					if ( $(this).attr('checked') ) {
						// Checking

						$("#stn<?php echo $this->id ?> tr td input[type='checkbox']").attr('checked', true);

					} else {
						// Unchecking

						$("#stn<?php echo $this->id ?> tr td input[type='checkbox']").attr('checked', false);

					}

				});

			},

			<?php

			print "'sPaginationType': 'full_numbers',\n";

			if ( $this->default_count != '' ) {

				print "
					'iDisplayLength': {$this->default_count},\n
				";

			}

			$cols_serial = serialize($this->cols);
			$cols_serial = addslashes($cols_serial);

			print "
			'bProcessing': true,
			'bServerSide': true,
			'sAjaxSource': '{$this->processor_path}',
			'sDom': 'tip',
			'fnServerData': function ( sSource, aoData, fnCallback ) {
				$('#stn{$this->id} .ajaxIMG').show();
				aoData.push( { 'name' : 'refnum' , 'value' : '{$this->refnum}' } );
				$.ajax( {
					'dataType': 'json',
					'type': 'POST',
					'url': sSource,
					'data': aoData,
					'success': fnCallback
				} );
			},
			";

			print "'aaSorting' : [";

			$counter = 0;
			$valid_counter = 0;

			foreach ( $this->headers as $header ) {
				if ( sizeof(@$header['children']) > 0 ) {
					// This header has child headers.
					foreach ( $header['children'] as $child_header ) {
						$counter_array = processHeaderSorting($child_header['data'], $counter, $valid_counter);
						$counter = $counter_array['counter'];
						$valid_counter = $counter_array['valid_counter'];
					}
				} else {
					// This header has no child headers.
					$counter_array = processHeaderSorting($header['data'], $counter, $valid_counter);
					$counter = $counter_array['counter'];
					$valid_counter = $counter_array['valid_counter'];
				}
			}

			print "],";

			print "'aoColumns' : [";

			$counter = 0;

			foreach ( $this->headers as $header ) {
				if ( sizeof(@$header['children']) > 0 ) {
					// This header has child headers.
					foreach ( $header['children'] as $child_header ) {
						processHeaderFull($child_header);
					}
				} else {
					// This header has no child headers.
					processHeaderFull($header['data']);
				}
			}

			print "]";

			?>

		});

	}

</script>

<div class="clear"></div>

</div>