<?php

/*
Library for managing tables built with the jQuery "DataTables" plugin:
http://www.datatables.net
*/

class DataTable
{

	public $default_size = 25;
	public $id;
	public $user_id;
	public $data_source;
	public $enable_ajax;
	public $enable_excel;
	public $enable_checkbox;
	public $has_created_checkbox = 0;
	public $unique_id_field;
	public $default_count;
	public $method_var_array = array();
	public $refnum;
	public $cols;
	public $processor_path;
	public $headers;
	public $actions;
	public $has_children;
	public $has_header_data_row;
	public $header_data_rows = array();
	public $header_rowspan = 1;
	public $default_sort_col_field = null;
	public $default_sort_col_dir = null;

	public function addMethodVar($var)
	{
		$this->method_var_array[] = $var;
	}

	public function init()
	{
		$epoch = date("U");
		
		if ( $this->has_children == '1' ) {
			$this->header_rowspan++;
		}

		$cache = array();
		$cache["data_source"] = $this->data_source;
		$cache["method_vars"] = $this->method_var_array;
		$cache["enable_checkbox"] = $this->enable_checkbox;
		$cache["unique_id_field"] = $this->unique_id_field;
		$cache["headers"] = $this->headers;
		$cache["default_sort_col_field"] = $this->default_sort_col_field;
		$cache["default_sort_col_dir"] = $this->default_sort_col_dir;
		$cache["has_children"] = $this->has_children;
		$cache["has_header_data_row"] = $this->has_header_data_row;
		$cache["header_data_rows"] = $this->header_data_rows;
		$cache_serial = serialize($cache);

		$hash_name = $this->data_source . '-' . $this->user_id . '-' . date("U") . '.datatable';

		$this->refnum = $hash_name;
		$hash_file = "./cache/" . $hash_name;
		$fh = fopen($hash_file, 'w');
		fwrite($fh, $cache_serial);
		fclose($fh);

		ob_start();
		include("datatables_view.php");
		$buffer = ob_get_clean();
 		$result = $buffer;
		return $result;
	}

	public function createHeaderDataCol($array)
	{
		$this->has_header_data_row = '1';
		$this->header_data_rows[$array['id']] = array(
			"value" => $array['value'],
			"class" => $array['class'],
		);
	}
	
	public function createAction($action)
	{
		$this->actions[] = $action;
	}
	
	public function createHeader($header)
	{
		if ( $this->enable_checkbox == '1' && $this->has_created_checkbox == 0 ) {
			
			$this->headers['datatables_checkbox']['data'] = array(
				"checkbox" => '1',
				"id" => $this->unique_id_field,
				"label" => '',
				"field" => $this->unique_id_field,
				"class" => '',
				"default_sort" => '',
				"function" => '',
				"sortable" => '0',
			);

			$this->has_created_checkbox = 1;

		}
		if ( empty($header['parent_id']) ) {

			// This is a parent header.
			
			$this->headers[$header['id']]['data'] = array(
				"id" => $header['id'],
				"label" => $header['label'],
				"field" => $header['field'],
				"class" => $header['class'],
				"default_sort" => $header['default_sort'],
				"function" => $header['function'],
				"enable_excel" => $header['enable_excel'],
			);
			
			if ( $header['default_sort'] != '' && $this->default_sort_col_field == null ) {
				$this->default_sort_col_field = $header['field'];
				$this->default_sort_col_dir = $header['default_sort'];
			}

		} else {
			
			// This is a child header.
			
			$this->has_children = '1';
			
			$this->headers[$header['parent_id']]['children'][]['data'] = array(
				"id" => $header['id'],
				"label" => $header['label'],
				"field" => $header['field'],
				"class" => $header['class'],
				"default_sort" => $header['default_sort'],
				"function" => $header['function'],
				"enable_excel" => $header['enable_excel'],
			);

			if ( $header['default_sort'] != '' && $this->default_sort_col_field == null ) {
				$this->default_sort_col_field = $header['field'];
				$this->default_sort_col_dir = $header['default_sort'];
			}
			
		}
	}

}

?>