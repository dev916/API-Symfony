<?php

// SQL server connection information


$sql_details = array(

	'user' => 'awnpw_flickplay',

	'pass' => 'KPTrPBd%iuDc',

	'db'   => 'awnpw_flickandplay',

	'host' => 'localhost'

);



require( 'ssp.class.php' );



$db = SSP::sql_connect( $sql_details );



$table = $_GET['table'];//'awn_geoplan';//datatables_demo/awn_geoplan'awn_geoplan';//




$primaryKey =$_GET['id'] ;//'geoplan_id';////'geoplan_id';




$type=$_GET['type'];



/*******************************************Open Home Banner*********************************************************/

if($table=='banner')
{

	$columns = array(

    array( 'db' => 'bid',  'dt' => 'bid' ),

	array( 'db' => 'game_name',  'dt' => 'game_name' ),

	array( 'db' => 'banner_image',  'dt' => 'banner_image' ),


	);




	echo json_encode(


	SSP::simple_banner( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Home Banner*************************************************/



/*****************************************************Open Highlights Banner******************************************************************/

else if($table=='highlights_banner')
{

	$columns = array(

    array( 'db' => 'hb_id',  'dt' => 'hb_id' ),

	array( 'db' => 'game_name',  'dt' => 'game_name' ),

	array( 'db' => 'hb_image',  'dt' => 'hb_image' ),


	);




	echo json_encode(


	SSP::simple_highlights_banner( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/*****************************************************************End Highlights Banner**************************************************************/


/*****************************************************Open Child Highlights Banner******************************************************************/

else if($table=='child_highlights_banner')
{

	$columns = array(

    array( 'db' => 'chb_id',  'dt' => 'chb_id' ),

	array( 'db' => 'game_name',  'dt' => 'game_name' ),

	array( 'db' => 'chb_image',  'dt' => 'chb_image' ),


	);




	echo json_encode(


	SSP::simple_child_highlights_banner( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/*************************************************************End Child Highlights Banner**************************************************************/



/*******************************************Open Master Category*********************************************************/





else if($table=='master_category')
{

	$columns = array(


    array( 'db' => 'category_id',  'dt' => 'category_id' ),
	
    array( 'db' => 'image',  'dt' => 'image' ),

	array( 'db' => 'sort_order',  'dt' => 'sort_order' ),

	array( 'db' => 'cat_name',  'dt' => 'cat_name' ),
	
	array( 'db' => 'cat_seo_url',  'dt' => 'cat_seo_url' ),
   
	array( 'db' => 'cat_seo_title',  'dt' => 'cat_seo_title' ),
   
	array( 'db' => 'cat_seo_keyword',  'dt' => 'cat_seo_keyword' ),
   
	array( 'db' => 'cat_seo_description',  'dt' => 'cat_seo_description' ),
   




	);



	echo json_encode(



	SSP::simple_master_category( $_GET, $sql_details, $table, $primaryKey, $columns )


);




}



/***************************************************End Master Category*************************************************/




/*******************************************Open Master Share Link*********************************************************/

else if($table=='master_sharelink')
{

	$columns = array(

    array( 'db' => 'sharelink_id',  'dt' => 'sharelink_id' ),

	array( 'db' => 'share_link',  'dt' => 'share_link' ),

	array( 'db' => 'disable_all',  'dt' => 'disable_all' ),


	);




	echo json_encode(


	SSP::simple_master_sharelink( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Master Share Link*************************************************/




/*******************************************Open Master Settings*********************************************************/

else if($table=='master_settings')
{

	$columns = array(

    array( 'db' => 'settings_id',  'dt' => 'settings_id' ),

	array( 'db' => 'membership_days',  'dt' => 'membership_days' ),

	array( 'db' => 'datetime',  'dt' => 'datetime' ),


	);




	echo json_encode(


	SSP::simple_master_settings( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Master Settings*************************************************/




/*******************************************Open Master Company*********************************************************/

else if($table=='master_company')
{

	$columns = array(

    array( 'db' => 'master_company_id',  'dt' => 'master_company_id' ),

	array( 'db' => 'company_name',  'dt' => 'company_name' ),

	array( 'db' => 'datetime',  'dt' => 'datetime' ),


	);




	echo json_encode(


	SSP::simple_master_company( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Master Company*************************************************/



/*******************************************Open Country Language*********************************************************/

else if($table=='country_language')
{

	$columns = array(

    array( 'db' => 'country_language_id',  'dt' => 'country_language_id' ),

	array( 'db' => 'country_short',  'dt' => 'country_short' ),

	array( 'db' => 'country_long',  'dt' => 'country_long' ),
	
	array( 'db' => 'lang_code',  'dt' => 'lang_code' ),
	
	array( 'db' => 'lang_id',  'dt' => 'lang_id' ),


	);




	echo json_encode(


	SSP::simple_country_language( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Country Language*************************************************/



/*******************************************Open Home Page Content*********************************************************/

else if($table=='homepage_content')
{

	$columns = array(

    array( 'db' => 'homepage_id',  'dt' => 'homepage_id' ),

	array( 'db' => 'lang_code',  'dt' => 'lang_code' ),

	array( 'db' => 'subtitle1',  'dt' => 'subtitle1' ),
	
	array( 'db' => 'testtheapp',  'dt' => 'testtheapp' ),
	
	array( 'db' => 'subtitle2',  'dt' => 'subtitle2' ),
	
	array( 'db' => 'block1title',  'dt' => 'block1title' ),

	array( 'db' => 'block1desc',  'dt' => 'block1desc' ),
	
	array( 'db' => 'block2title',  'dt' => 'block2title' ),
	
	array( 'db' => 'block2desc',  'dt' => 'block2desc' ),
	
	array( 'db' => 'block3title',  'dt' => 'block3title' ),
	
	array( 'db' => 'block3desc',  'dt' => 'block3desc' ),

	array( 'db' => 'footertitle',  'dt' => 'footertitle' ),
	
	array( 'db' => 'footerdesc',  'dt' => 'footerdesc' ),
	
	array( 'db' => 'copyright',  'dt' => 'copyright' ),


	);




	echo json_encode(


	SSP::simple_homepage_content( $_GET, $sql_details, $table, $primaryKey, $columns )




);





}


/***************************************************End Home Page Content*************************************************/




/*******************************************Open Add Game*********************************************************/






else if($table=='games')







{







	$columns = array(







	array( 'db' => 'id',  'dt' => 'id' ),



	array( 'db' => 'cat_name',  'dt' => 'cat_name' ),



	array( 'db' => 'game_name',  'dt' => 'game_name' ),



	array( 'db' => 'image',  'dt' => 'image' ),

	

	array( 'db' => 'description',  'dt' => 'description' ),
	
	
	array( 'db' => 'game_views',  'dt' => 'game_views' ),
	
	
	array( 'db' => 'game_likes',  'dt' => 'game_likes' ),


	array( 'db' => 'link',  'dt' => 'link' ),
	
	
	array( 'db' => 'ad_link',  'dt' => 'ad_link' ),	
	
	
	array( 'db' => 'game_rotation',  'dt' => 'game_rotation' ),

	

	array( 'db' => 'share_link',  'dt' => 'share_link' ),



	array( 'db' => 'seo_url',  'dt' => 'seo_url' ),



	array( 'db' => 'seo_title',  'dt' => 'seo_title' ),



	array( 'db' => 'seo_keyword',  'dt' => 'seo_keyword' ),



	array( 'db' => 'seo_description',  'dt' => 'seo_description' ),



	array( 'db' => 'datetime',  'dt' => 'datetime' ),







	);







	echo json_encode(







	SSP::simple_games( $_GET, $sql_details, $table, $primaryKey, $columns )







);







	







}















/***************************************************End Add Game*************************************************/















/*******************************************Open Feedback*********************************************************/

else if($table=='feedback')
{

	$columns = array(

	array( 'db' => 'id',  'dt' => 'id' ),

	array( 'db' => 'name',  'dt' => 'name' ),

	array( 'db' => 'email',  'dt' => 'email' ),

	array( 'db' => 'feedback',  'dt' => 'feedback' ),

	array( 'db' => 'datetime',  'dt' => 'datetime' ),


	);


	echo json_encode(

	SSP::simple_feedback($_GET, $sql_details, $table, $primaryKey, $columns )

);



}


/***************************************************End Feedback*************************************************/


/*******************************************Open Game Users*********************************************************/

else if($table=='users')
{

	$columns = array(

	array( 'db' => 'id',  'dt' => 'id' ),

	array( 'db' => 'token',  'dt' => 'token' ),

	array( 'db' => 'country',  'dt' => 'country' ),
	
	array( 'db' => 'company_name',  'dt' => 'company_name' ),

	array( 'db' => 'membership_upto',  'dt' => 'membership_upto' ),

	array( 'db' => 'datetime',  'dt' => 'datetime' ),


	);


	echo json_encode(

	SSP::simple_users($_GET, $sql_details, $table, $primaryKey, $columns )

);



}


/***************************************************End Game Users*************************************************/



/*******************************************Open Privacy and Agreement*********************************************************/

else if($table=='privacyandagreement')
{


	$columns = array(


	array( 'db' => 'id',  'dt' => 'id' ),
	
	array( 'db' => 'title',  'dt' => 'title' ),

	array( 'db' => 'privacyandagreement',  'dt' => 'privacyandagreement' ),

	array( 'db' => 'seo_url',  'dt' => 'seo_url' ),

	array( 'db' => 'seo_title',  'dt' => 'seo_title' ),

	array( 'db' => 'seo_keyword',  'dt' => 'seo_keyword' ),

	array( 'db' => 'seo_description',  'dt' => 'seo_description' ),


	);




	echo json_encode(



	SSP::simple_privacyandagreement($_GET, $sql_details, $table, $primaryKey, $columns )




);






}




/***************************************************End Privacy and Agreement*************************************************/



















else if($type=='country')







{







	 







$str="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE  TABLE_NAME = '".$table."' " ;//LIMIT 0,37







$fire=$db->query($str);















$i=0;







$san='';















while($row=$fire->fetch(PDO::FETCH_ASSOC))







{ 







	$san[]=array( 'db' => $row['COLUMN_NAME'], 'dt' => $row['COLUMN_NAME']);







	







	$i++;







}















$columns=$san;















echo json_encode(







	SSP::simple_country( $_GET, $sql_details, $table, $primaryKey, $columns )







);















}







else{ 







$str="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE  TABLE_NAME = '".$table."' " ;//LIMIT 0,37







$fire=$db->query($str);















$i=0;







$san='';















while($row=$fire->fetch(PDO::FETCH_ASSOC))







{ 







	$san[]=array( 'db' => $row['COLUMN_NAME'], 'dt' => $row['COLUMN_NAME']);







	







	$i++;







}















$columns=$san;















echo json_encode(







	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )







);







}























