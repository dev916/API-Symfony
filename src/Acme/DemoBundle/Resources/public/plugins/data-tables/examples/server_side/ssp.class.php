<?php
// REMOVE THIS BLOCK - used for DataTables test environment only!
$file = $_SERVER['DOCUMENT_ROOT'].'/datatables/mysql.php';
if ( is_file( $file ) ) {
	include( $file );
}


class SSP {
	
	static function data_output ( $columns, $data )
	{
		$out = array();

		for ( $i=0, $ien=count($data) ; $i<($ien); $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
				}
				else {
					$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
				}
				
				
				
				
			}
			//$row[$column['dt']]='Edit | Delete';
			$out[] = $row;
		}

		return $out;
	}


	static function limit ( $request, $columns )
	{
		$limit = '';

		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
		}

		return $limit;
	}


	static function order ( $request, $columns )
	{
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = SSP::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';

					$orderBy[] = '`'.$column['db'].'` '.$dir;
				}
			}

			$order = 'ORDER BY '.implode(', ', $orderBy);
		}

		return $order;
	}


	static function filter ( $request, $columns, &$bindings )
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = SSP::pluck( $columns, 'dt' );

		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['searchable'] == 'true' ) {
					$binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					$globalSearch[] = "`".$column['db']."` LIKE ".$binding;
				}
			}
		}

		// Individual column filtering
		for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $request['columns'][$i];
			$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $columns[ $columnIdx ];

			$str = $requestColumn['search']['value'];

			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				$binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
				$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			$where = '('.implode(' OR ', $globalSearch).')';
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		if ( $where !== '' ) {
			$where = 'WHERE '.$where;
		}

		return $where;
	}


	
	static function simple ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", SSP::pluck($columns, 'db'))."`
			 FROM `$table`
			 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}

	static function simple_country ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", SSP::pluck($columns, 'db'))."`
			 FROM `$table` 
			 where `type`='CO'
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}

	
	
	
	static function simple_awn_course_unit ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT `awn_course_unit`.*,`awn_course`.course FROM `awn_course_unit` LEFT JOIN `awn_course` ON `awn_course`.course_id=`awn_course_unit`.course_id	 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}
	
	static function simple_awn_quiz_taken ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT `awn_course_unit`.unit_name,`awn_quiz_taken`.*,`awn_course`.course FROM `awn_quiz_taken` LEFT JOIN `awn_course` ON `awn_course`.course_id=`awn_quiz_taken`.course_id left JOIN `awn_course_unit` ON `awn_course_unit`.unit_id=`awn_quiz_taken`.unit_id	 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}
	
	static function simple_awn_question ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT `awn_course_unit`.unit_name,`awn_question`.*,`awn_course`.course FROM `awn_question` LEFT JOIN `awn_course` ON `awn_course`.course_id=`awn_question`.course_id left JOIN `awn_course_unit` ON `awn_course_unit`.unit_id=`awn_question`.unit_id	 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}

	static function simple_awn_answer ( $request, $sql_details, $table, $primaryKey, $columns )
	{
		$bindings = array();
		$db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request
		$limit = SSP::limit( $request, $columns );
		$order = SSP::order( $request, $columns );
		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."
		$data = SSP::sql_exec( $db, $bindings,
			"SELECT `awn_answer`.*,`awn_question`.question
			 FROM `$table` LEFT JOIN `awn_question` ON `awn_question`.quest_id= `awn_answer`.ans_id
			 $where
			 $order
			 $limit"
		);

		// Data set length after filtering
		$resFilterLength = SSP::sql_exec( $db,
			"SELECT FOUND_ROWS()"
		);
		$recordsFiltered = $resFilterLength[0][0];

		// Total data set length
		$resTotalLength = SSP::sql_exec( $db,
			"SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
		);
		$recordsTotal = $resTotalLength[0][0];


		/*
		 * Output
		 */
		return array(
			"draw"            => intval( $request['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => SSP::data_output( $columns, $data )
		);
	}


	static function sql_connect ( $sql_details )
	{
		try {
			$db = @new PDO(
				"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
				$sql_details['user'],
				$sql_details['pass'],
				array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
			);
		}
		catch (PDOException $e) {
			SSP::fatal(
				"An error occurred while connecting to the database. ".
				"The error reported by the server was: ".$e->getMessage()
			);
		}

		return $db;
	}


	
	static function sql_exec ( $db, $bindings, $sql=null )
	{
		// Argument shifting
		if ( $sql === null ) {
			$sql = $bindings;
		}

		$stmt = $db->prepare( $sql );
		//echo $sql;

		// Bind parameters
		if ( is_array( $bindings ) ) {
			for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
				$binding = $bindings[$i];
				$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
			}
		}

		// Execute
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			SSP::fatal( "An SQL error occurred: ".$e->getMessage() );
		}

		// Return all
		return $stmt->fetchAll();
	}


	static function fatal ( $msg )
	{
		echo json_encode( array( 
			"error" => $msg
		) );

		exit(0);
	}

	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}



	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}

		return $out;
	}
}

