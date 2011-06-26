<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$export_excel = @$_GET['export_excel'];
$hash = @$_GET['hash'];

function doSomething($data) {

	return "This column was processed by a function.";

}

function get_all_projects($search_text, $offset, $sort_column, $sort_dir, $vars=false) {

	$array = array();
	$array[] = array(
		"id" => "1",
		"keyword" => "interesting stuff",
		"local_vol" => "590",
		"global_vol" => "720",
		"adv_comp" => "0.9627",
		"req" => "27",
		"act" => "10",
		"ranking" => "4",
		"options" => "",
	);
	$array[] = array(
		"id" => "2",
		"keyword" => "stupid stuff",
		"local_vol" => "427",
		"global_vol" => "285",
		"adv_comp" => "0.125",
		"req" => "4",
		"act" => "1",
		"ranking" => "9",
		"options" => "",
	);

	$return_array = array(
		"array" => $array,
		"total_records" => sizeof($array),
	);
	return $return_array;

}

function objectToArray($object) {
    if( !is_object( $object ) && !is_array( $object ) )
    {
        return $object;
    }
    if( is_object( $object ) )
    {
        $object = get_object_vars( $object );
    }
    return array_map( 'objectToArray', $object );
}

class DataTableProcessor {

	private $json;
	private $aadata_size;
	private $col_counter;
	private $unique_id_field;
	private $excel_counter = 0;

	function json()
	{

		$model = "datatable_model";

		@$sort_col_index = $_POST['iSortCol_0'];
		@$sort_dir = $_POST['iSortDir_0'];
		$limit = $_POST['iDisplayLength'];
		$search = $_POST['sSearch'];
		$offset = $_POST['iDisplayStart'];
		$refnum = $_POST['refnum'];
		$secho = $_POST['sEcho'];

		$this->json = array();

		$filepath = "./cache/" . $refnum;
		$cache_serial = file_get_contents($filepath);
		$cache = unserialize($cache_serial);
		$method_var_array = $cache["method_vars"];
		$data_source = $cache["data_source"];
		$has_children = $cache["has_children"];
		$has_header_data_row = $cache["has_header_data_row"];
		$this->unique_id_field = $cache["unique_id_field"];

		if ( $search == "" ) {
			$local_search = 0;
		}

		$search_text = '';

		if ( $search != '' ) {

			$search = addslashes($search);

			$search_counter = 0;

			foreach ( $cache["headers"] as $header ) {

				if ( $header["searchable"] == "1" ) {

					$search_counter++;

					if ( $search_counter == 1 ) {

						if ( @$header['sql_field'] != '' ) {

							$search_text .= " ( {$header['sql_field']} LIKE '%{$search}%' REPLACEMEDT1 ) ";

						} else {

							$search_text .= " ( {$header['field']} LIKE '%{$search}%' REPLACEMEDT1 ) ";

						}

					} else {

						if ( @$header['sql_field'] != '' ) {

							$search_text .= " OR ( {$header['sql_field']} LIKE '%{$search}%' REPLACEMEDT1 ) ";

						} else {

							$search_text .= " OR ( {$header['field']} LIKE '%{$search}%' REPLACEMEDT1 ) ";

						}

					}

				}

			}

		}

		$headers = $cache["headers"];

		function compare_asc($x, $y) {

			global $sort_col;
		 	if ( $x[$sort_col] == $y[$sort_col] ) {
		  		return 0;
		 	} else if ( $x[$sort_col] < $y[$sort_col] ) {
		  		return -1;
		 	} else {
		  		return 1;
			}
		}

		function compare_desc($x, $y) {

			global $sort_col;
		 	if ( $x[$sort_col] == $y[$sort_col] ) {
		  		return 0;
		 	} else if ( $x[$sort_col] > $y[$sort_col] ) {
		  		return -1;
		 	} else {
		  		return 1;
			}
		}

		$header_counter = -1;
		foreach ( $headers as $header ) {
			$header_counter++;
			if ( $header_counter == $sort_col_index ) {
				$sort_col = $header['data']['field'];
			}
			if ( sizeof(@$header['children']) > 0 ) {
				// This header has child headers.
				foreach ( $header['children'] as $child ) {
					$header_counter++;
					if ( $header_counter == $sort_col_index ) {
						$sort_col = $child['data']['field'];
					}
				}
			}
		}

		if ( $sort_col != "" ) {
			$sort_text = " ORDER BY {$sort_col} {$sort_dir} ";
		} else {
			$sort_text = "";
		}

		if ( $offset != '' && $search == '' ) {

			$offset_b = $offset + $limit;

			$offset_text = " LIMIT {$limit} OFFSET {$offset} ";

		} else {

			$offset_text = '';

		}

		$sort_col_field = @$cache["default_sort_col_field"];
		$sort_col_dir = @$cache["default_sort_col_dir"];

		if ( sizeof($method_var_array) > 0 ) {
			$eval_string = "\$result = \$data_source(\"\", \"0\", \$sort_col_field, \$sort_col_dir, \$method_var_array);";
		} else {
			$eval_string = "\$result = \$data_source(\"\", \"0\", \$sort_col_field, \$sort_col_dir);";
		}

		eval($eval_string);

		if ( is_array($result) ) {

			if ( isset($result['array']) && is_array($result['array']) ) {

				$total_entries = $result['total_records'];
				$total_returned_entries = sizeof($result['array']);
				$data_src_array = array();
				foreach ( $result['array'] as $key => $object ) {
					$data_src_array[$key] = objectToArray($object);
				}

			}

		} else {

			$sql = $result;

			if ( !$sql ) {
				$nothing = array();
				$nothing['sEcho'] = $secho;
				$nothing['aaData'] = array();
				$nothing['iTotalRecords'] = 0;
				$nothing['iTotalDisplayRecords'] = 0;
				$this->json = json_encode($nothing);
				echo $this->json;
				exit();
			}

			$data_src = $this->db->query($sql);
			$total_entries = $this->db->query("SELECT FOUND_ROWS() AS found_rows")->row()->found_rows;
			$total_returned_entries = $data_src->num_rows();
			$data_src_array = array();
			$result_set = $data_src->result();
			foreach ( $result_set as $key => $object ) {
				$data_src_array[$key] = objectToArray($object);
			}

		}

		$this->json['sEcho'] = $secho;
		$this->json['aaData'] = array();

		$this->json['iTotalRecords'] = $total_entries;
		$this->json['iTotalDisplayRecords'] = $total_entries;

		$limit_counter = 0;

		foreach ( $data_src_array as $row ) {
			if ( $limit_counter < $limit ) {
				$this->col_counter = 0;
				$this->aadata_size = sizeof($this->json['aaData']);
				foreach ( $headers as $header ) {
					if ( sizeof(@$header['children']) > 0 ) {
						// This header has children.
						foreach ( $header['children'] as $child ) {
							$this->processHeader($child['data'], $row);
						}
					} else {
						// This header has no children.
						$this->processHeader($header['data'], $row);
					}
				}
			}
			$limit_counter++;
		}

		$this->json = json_encode($this->json);
		echo $this->json;

	}

	function excelHeader($header)
	{
		$final = '';
		if ( @$header["enable_excel"] == 1 ) {
			$this->excel_counter++;
			if ( $this->excel_counter == 1 ) {
				$final = '"' . $header['label'] . '"';
			} else {
				$final = ',"' . $header['label'] . '"';
			}
		}
		return $final;
	}

	function excelRow($field)
	{
		$this->excel_counter++;
		$field = str_replace('"', '\"', $field);
		if ( $this->excel_counter == 1 ) {
			$final = '"' . $field . '"';
		} else {
			$final = ',"' . $field . '"';
		}
		return $final;
	}

	function excel()
	{
		global $hash;
		$filepath = "./cache/" . $hash;
		$cache_serial = file_get_contents($filepath);

		$final = '';

		$cache = unserialize($cache_serial);
		$data_source = $cache["data_source"];

		$counter = 0;

		foreach ( $cache["headers"] as $header ) {

			if ( sizeof(@$header['children']) > 0 ) {
				foreach ( $header['children'] as $child ) {
					$final .= $this->excelHeader($child['data']);
				}
			} else {
				$final .= $this->excelHeader($header['data']);
			}

		}

		$method_var_array = $cache["method_vars"];

		$sort_col_field = @$cache["default_sort_col_field"];
		$sort_col_dir = @$cache["default_sort_col_dir"];

		if ( sizeof($method_var_array) > 0 ) {
			$eval_string = "\$result = \$data_source(\"\", \"0\", \$sort_col_field, \$sort_col_dir, \$method_var_array);";
		} else {
			$eval_string = "\$result = \$data_source(\"\", \"0\", \$sort_col_field, \$sort_col_dir);";
		}

		eval($eval_string);

		if ( is_array($result) ) {

			if ( isset($result['array']) && is_array($result['array']) ) {
				foreach ( $result['array'] as $key => $row ) {

					$final .= "\n";

					$row = objectToArray($row);

					$this->excel_counter = 0;

					foreach ( $cache["headers"] as $header ) {

						if ( sizeof(@$header['children']) > 0 ) {
							foreach ( $header['children'] as $child ) {
								if ( @$child['data']["enable_excel"] == 1 ) {
									$final .= $this->excelRow($row[$child['data']['field']]);
								}
							}
						} else {
							if ( @$header['data']["enable_excel"] == 1 ) {
								$final .= $this->excelRow($row[$header['data']['field']]);
							}
						}

					}

				}
			}

			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename="data.csv"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . mb_strlen($final, 'latin1'));
			echo $final;
			exit();

		} else {

			$sql = $result;

			if ( !$sql ) {
				$nothing = array();
				$nothing['sEcho'] = $secho;
				$nothing['aaData'] = array();
				$nothing['iTotalRecords'] = 0;
				$nothing['iTotalDisplayRecords'] = 0;
				$this->json = json_encode($nothing);
				echo $this->json;
				exit();
			}

			$data_src = $this->db->query($sql);
			$total_entries = $this->db->query("SELECT FOUND_ROWS() AS found_rows")->row()->found_rows;
			$total_returned_entries = $data_src->num_rows();
			$data_src_array = array();
			$result_set = $data_src->result();
			foreach ( $result_set as $key => $object ) {
				$data_src_array[$key] = objectToArray($object);
			}

		}

	}

	function processHeader($header, $row) {

		if ( @$header['default_sort'] != '' ) {
			$default_sort = 1;
		}

		$val = '';

		if ( @$header['function'] != '' ) {

			if ( sizeof(@$header['function_array']) > 0 ) {

				$tmp_eval_string = @$header['function'] . "(";
				$tmp_eval_counter = 0;

				foreach (@$header['function_array'] as $function_key => $function_value )
				{

					if ( $tmp_eval_counter > 0 ) {
						$tmp_eval_string .= ",";
					}

					$tmp_eval_string .= "'" . $row[$function_value] . "'";

					$tmp_eval_counter++;

				}

				$tmp_eval_string .= ");";

				$val = eval("return " . $tmp_eval_string);

			} else {

				$val_temp = $row[$header['field']];
				$val = eval("return " . @$header['function'] . "('" . $val_temp . "');");

			}

		}  else {
			$val = $row[$header['field']];
		}

		if ( @$header['custom'] != '' ) {
			$custom = $header['custom'];
			$regex = "/\{(.*?)\}/";
			preg_match_all($regex, $custom, $matches);
			$string = array_unique($matches[0]);
			$keys = array_unique($matches[1]);
			foreach ( $keys as $key => $value ) {
				$var = $row[$value];
				$custom = str_replace("{" . $value . "}", $var, $custom);
			}
			$this->json['aaData'][$this->aadata_size][] = $custom;
		} else if ( @$header['checkbox'] == '1' ) {
			$this->json['aaData'][$this->aadata_size][] = "<input type='checkbox' rel='" . $row[$this->unique_id_field] . "' />";
		} else {
			$this->json['aaData'][$this->aadata_size][] = $val;
		}

		$this->col_counter++;

	}

}

$processor = new DataTableProcessor();

if ( $export_excel == '1' && !empty($hash) ) {

	// The user is requesting a CSV export of the data.

	$processor->excel();

} else {

	// A table query is being performed.

	$processor->json();

}

?>