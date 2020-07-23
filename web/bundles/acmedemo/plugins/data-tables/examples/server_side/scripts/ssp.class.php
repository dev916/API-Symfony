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







			$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']+1);







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







	



	/*******************************************Open Home Banner*********************************************************/


	static function simple_banner( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT `banner`.bid, `games`.game_name,  concat('<img src=\"http:/\/awn.pw/flickandplay/awn-admin/assets/images/' ,`banner`.banner_image, '\"/>') as banner_image FROM `banner` LEFT JOIN games ON `banner`.game_id=`games`.id $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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







	







	/***************************************************End Home Banner*************************************************/



	

/************************************************************Open Highlights Banner*************************************************************/

	static function simple_highlights_banner( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT `highlights_banner`.hb_id, `games`.game_name,  concat('<img src=\"http:/\/awn.pw/flickandplay/awn-admin/assets/images/' ,`highlights_banner`.hb_image, '\"/>') as hb_image FROM `highlights_banner` LEFT JOIN games ON `highlights_banner`.game_id=`games`.id $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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




/************************************************************End Highlights Banner*************************************************************/



/************************************************************Open Child Highlights Banner*************************************************************/

	static function simple_child_highlights_banner( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT `child_highlights_banner`.chb_id, `games`.game_name,  concat('<img src=\"http:/\/awn.pw/flickandplay/awn-admin/assets/images/' ,`child_highlights_banner`.chb_image, '\"/>') as chb_image FROM `child_highlights_banner` LEFT JOIN games ON `child_highlights_banner`.game_id=`games`.id $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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




/************************************************************End Child Highlights Banner*************************************************************/





	/*******************************************Open Master Category*********************************************************/


	static function simple_master_category( $request, $sql_details, $table, $primaryKey, $columns )
    {



		$bindings = array();







		$db = SSP::sql_connect( $sql_details );















		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );















		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,







			"SELECT category_id, cat_name, cat_name_bulgarian, cat_name_catalan, cat_name_chinese_hongkong, cat_name_chinese_prc, cat_name_chinese_taiwan, cat_name_czech, cat_name_dutch, cat_name_french, cat_name_german, cat_name_greek, cat_name_hungarian, cat_name_indonesian, cat_name_italian, cat_name_japanese, cat_name_korean, cat_name_malay, cat_name_portuguese_brazilian, cat_name_russian, cat_name_romanian, cat_name_spanish_spain, cat_name_slovak, cat_name_thai, cat_name_turkish, cat_name_ukrainian, cat_seo_url, cat_seo_url_bulgarian, cat_seo_url_catalan, cat_seo_url_chinese_hongkong, cat_seo_url_chinese_prc, cat_seo_url_chinese_taiwan, cat_seo_url_czech, cat_seo_url_dutch, cat_seo_url_french, cat_seo_url_german, cat_seo_url_greek, cat_seo_url_hungarian, cat_seo_url_indonesian, cat_seo_url_italian, cat_seo_url_japanese, cat_seo_url_korean, cat_seo_url_malay, cat_seo_url_portuguese_brazilian, cat_seo_url_russian, cat_seo_url_romanian, cat_seo_url_spanish_spain, cat_seo_url_slovak, cat_seo_url_thai, cat_seo_url_turkish, cat_seo_url_ukrainian, cat_seo_title, cat_seo_title_bulgarian, cat_seo_title_catalan, cat_seo_title_chinese_hongkong, cat_seo_title_chinese_prc, cat_seo_title_chinese_taiwan, cat_seo_title_czech, cat_seo_title_dutch, cat_seo_title_french, cat_seo_title_german, cat_seo_title_greek, cat_seo_title_hungarian, cat_seo_title_indonesian, cat_seo_title_italian, cat_seo_title_japanese, cat_seo_title_korean, cat_seo_title_malay, cat_seo_title_portuguese_brazilian, cat_seo_title_russian, cat_seo_title_romanian, cat_seo_title_spanish_spain, cat_seo_title_slovak, cat_seo_title_thai, cat_seo_title_turkish, cat_seo_title_ukrainian, cat_seo_keyword, cat_seo_keyword_bulgarian, cat_seo_keyword_catalan, cat_seo_keyword_chinese_hongkong, cat_seo_keyword_chinese_prc, cat_seo_keyword_chinese_taiwan, cat_seo_keyword_czech, cat_seo_keyword_dutch, cat_seo_keyword_french, cat_seo_keyword_german, cat_seo_keyword_greek, cat_seo_keyword_hungarian, cat_seo_keyword_indonesian, cat_seo_keyword_italian, cat_seo_keyword_japanese, cat_seo_keyword_korean, cat_seo_keyword_malay, cat_seo_keyword_portuguese_brazilian, cat_seo_keyword_russian, cat_seo_keyword_romanian, cat_seo_keyword_spanish_spain, cat_seo_keyword_slovak, cat_seo_keyword_thai, cat_seo_keyword_turkish, cat_seo_keyword_ukrainian, cat_seo_description, cat_seo_description_bulgarian, cat_seo_description_catalan, cat_seo_description_chinese_hongkong, cat_seo_description_chinese_prc, cat_seo_description_chinese_taiwan, cat_seo_description_czech, cat_seo_description_dutch, cat_seo_description_french, cat_seo_description_german, cat_seo_description_greek, cat_seo_description_hungarian, cat_seo_description_indonesian, cat_seo_description_italian, cat_seo_description_japanese, cat_seo_description_korean, cat_seo_description_malay, cat_seo_description_portuguese_brazilian, cat_seo_description_russian, cat_seo_description_romanian, cat_seo_description_spanish_spain, cat_seo_description_slovak, cat_seo_description_thai, cat_seo_description_turkish, cat_seo_description_ukrainian, concat('<img src=\"http:/\/awn.pw/flickandplay/awn-admin/assets/images/' ,cat_image, '\"/>') as image, sort_order FROM  `master_category` order by sort_order asc"//$where $order $limit







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







	







	/***************************************************End Master Category*************************************************/



/*******************************************Open Master Share Link*********************************************************/


	static function simple_master_sharelink( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT * from master_sharelink $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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





	/***************************************************End Master Share Link*************************************************/




/*******************************************Open Master Settings*********************************************************/


	static function simple_master_settings( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT * from master_settings $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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





	/***************************************************End Master Settings*************************************************/
	
	
	
	
	/*******************************************Open Master Company*********************************************************/


	static function simple_master_company( $request, $sql_details, $table, $primaryKey, $columns )

    {



		$bindings = array();



        $db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );





		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,



			"SELECT * from master_company $where $order $limit"



		);







		// Data set length after filtering







		$resFilterLength = SSP::sql_exec( $db,







			"SELECT FOUND_ROWS()"







		);







		$recordsFiltered = $resFilterLength[0][0];





		// Total data set length







		$resTotalLength = SSP::sql_exec( $db,



			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"



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





	/***************************************************End Master Company*************************************************/




/*********************************************************Open Country Language*****************************************************************/


	static function simple_country_language( $request, $sql_details, $table, $primaryKey, $columns )
    {

		$bindings = array();

        $db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request


		$limit = SSP::limit( $request, $columns );


		$order = SSP::order( $request, $columns );


		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."


		$data = SSP::sql_exec( $db, $bindings,



			"SELECT * from country_language $where $order $limit"



		);


		// Data set length after filtering

		$resFilterLength = SSP::sql_exec( $db,



			"SELECT FOUND_ROWS()"


		);


		$recordsFiltered = $resFilterLength[0][0];


		// Total data set length


		$resTotalLength = SSP::sql_exec( $db,

			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"

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



	/*************************************************************End Country Language***********************************************************/



/*********************************************************Open Home Page Content*****************************************************************/


	static function simple_homepage_content( $request, $sql_details, $table, $primaryKey, $columns )
    {

		$bindings = array();

        $db = SSP::sql_connect( $sql_details );

		// Build the SQL query string from the request


		$limit = SSP::limit( $request, $columns );


		$order = SSP::order( $request, $columns );


		$where = SSP::filter( $request, $columns, $bindings );

		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."


		$data = SSP::sql_exec( $db, $bindings,



			"SELECT Distinct *, `country_language`.lang_code from homepage_content LEFT JOIN country_language ON `country_language`.lang_id=`homepage_content`.lang_id $where $order $limit"



		);


		// Data set length after filtering

		$resFilterLength = SSP::sql_exec( $db,



			"SELECT FOUND_ROWS()"


		);


		$recordsFiltered = $resFilterLength[0][0];


		// Total data set length


		$resTotalLength = SSP::sql_exec( $db,

			"SELECT COUNT(`{$primaryKey}`) FROM   `$table`"

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



	/*************************************************************End Home Page Content***********************************************************/




	/*******************************************Open Add Game*********************************************************/





	static function simple_games($request, $sql_details, $table, $primaryKey, $columns )







	{







		$bindings = array();







		$db = SSP::sql_connect( $sql_details );















		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );















		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,







			"SELECT `games`.id, `games`.game_name, concat('<img src=\"http:/\/awn.pw/flickandplay/awn-admin/assets/images/' ,`games`.image, '\"/>') as image, `games`.description, `games`.game_views, `games`.game_likes, `games`.link, `games`.ad_link, `games`.share_link, `games`.game_rotation, `games`.seo_url, `games`.seo_title, `games`.seo_keyword, `games`.seo_description, `games`.datetime, `master_category`.cat_name FROM `games` LEFT JOIN master_category ON `games`.cat_id=`master_category`.category_id $where $order $limit"







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







	







	/***************************************************End Add Game*************************************************/







	/*******************************************Open Feedback*********************************************************/

	static function simple_feedback($request, $sql_details, $table, $primaryKey, $columns )
	{


		$bindings = array();


		$db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request



		$limit = SSP::limit( $request, $columns );


		$order = SSP::order( $request, $columns );


		$where = SSP::filter( $request, $columns, $bindings );



		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."


		$data = SSP::sql_exec($db, $bindings,


			"SELECT * FROM `feedback` $where $order $limit"


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




	/***************************************************End Feedback*************************************************/



	/*******************************************Open Game Users*********************************************************/

	static function simple_users($request, $sql_details, $table, $primaryKey, $columns )
	{


		$bindings = array();


		$db = SSP::sql_connect( $sql_details );



		// Build the SQL query string from the request



		$limit = SSP::limit( $request, $columns );


		$order = SSP::order( $request, $columns );


		$where = SSP::filter( $request, $columns, $bindings );



		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."


		$data = SSP::sql_exec($db, $bindings,


			"SELECT `users`.id, `users`.token, `users`.country, `users`.membership_upto, `users`.datetime, `master_company`.company_name FROM `users` LEFT JOIN master_company ON `users`.company_id= `master_company`.master_company_id $where $order $limit"


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




	/***************************************************End Game Users*************************************************/
	



/*******************************************Open Privacy and Agreement*********************************************************/



	static function simple_privacyandagreement($request, $sql_details, $table, $primaryKey, $columns )

	{







		$bindings = array();







		$db = SSP::sql_connect( $sql_details );















		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );















		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec($db, $bindings,







			"SELECT * FROM `privacyandagreement` $where $order $limit"







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





	/***************************************************End Privacy and Agreement*************************************************/









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















































// awn_user table content start here















static function simple_awn_user( $request, $sql_details, $table, $primaryKey, $columns )







	{







		$bindings = array();







		$db = SSP::sql_connect( $sql_details );















		// Build the SQL query string from the request







		$limit = SSP::limit( $request, $columns );







		$order = SSP::order( $request, $columns );







		$where = SSP::filter( $request, $columns, $bindings );















		// Main query to actually get the data  ".implode("`, `", SSP::pluck($columns, 'db'))."







		$data = SSP::sql_exec( $db, $bindings,







			"  SELECT c.awnid,c.name,c.email,c.mobile,c.address,ca.designation







FROM awn_user c







INNER JOIN awn_designation ca







    ON ca.deg_id = c.designation







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















































//















// searching transaction section start here







































































	







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







		$out1 = array();







		//print count($a);







		//print_r($a); print $prop;







		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {







			$out1[] = $a[$i][$prop];







		}







//print_r($out1);







		return $out1;







	}







}















