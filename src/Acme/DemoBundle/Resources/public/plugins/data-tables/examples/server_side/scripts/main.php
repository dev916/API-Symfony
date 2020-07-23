<?php @session_start();
@ob_start();

class main extends database
{
	   /**********************************************Dashboard**************************************************************/
	
	function clear_url($value)
	{
	$pattern=array('/"/',"/'/","/&/","/ /","/\//");
	$replace=array("","","and","-","-");
	$title=preg_replace($pattern,$replace,$value);
	$title=strtolower(trim($title));
	return $title;
	}
	
	function deletetable($table, $con, $id)
	{
	  $this->connect();
	  $sql= "delete from ".$table." where ".$con."= '".$id."' ";
	  $file=$this->dbc->query($sql);
	  return $file;
	  $this->disconnect();
	  }
	
	 function add_designation($data)
	  {
	 	
		$add_designation['designation']=$data['designation'];
		$add_designation['added_date']=date('Y-m-d');
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_designation',$add_designation); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_designation',$add_designation,"`deg_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=designation&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=designation&tab='.$tab);
	  	 }	
	  }
	  // Company Master section start here
	  function add_companymaster($data)
	  {
	 	
		$add_companymaster['companyname']=$data['companyname'];
		$add_companymaster['added_date']=date('Y-m-d');
		$add_companymaster['designation']=$data['designation'];
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_company',$add_companymaster); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_company',$add_companymaster,"`company_id`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=companymaster&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=companymaster&tab='.$tab);
	  	 }	
	  }
	  
	  function add_branchmaster($data)
	  {
	 	
		$add_branchmaster['branchname']=$data['branchname'];
		$add_branchmaster['added_date']=date('Y-m-d');
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_branches',$add_branchmaster); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_branches',$add_branchmaster,"`branch_id`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=branchmaster&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=branchmaster&tab='.$tab);
	  	 }	
	  }
	  
	  function add_companyusers($data)
	  {
		$add_companyusers['username']=$data['username'];
		$add_companyusers['name']=$data['name'];
		$add_companyusers['address']=$data['address'];
		$add_companyusers['email']=$data['email'];
		$add_companyusers['mobile']=$data['mobile'];
		$add_companyusers['company']=$data['company'];
		$add_companyusers['branch']=$data['branch'];
		$add_companyusers['designation']=$data['designation'];
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_companyusers',$add_companyusers); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_companyusers',$add_companyusers,"`cu_id`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=companyusers&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=companyusers&tab='.$tab);
	  	 }	
	  }
	  
	  function addpoints_scheme($data)
	  {
		$addpoints_scheme['company']=$data['company'];
		$addpoints_scheme['rs']=$data['rs'];
		$addpoints_scheme['points']=$data['points'];

	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_addpoints_scheme',$addpoints_scheme); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_addpoints_scheme',$addpoints_scheme,"`add_points_id`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=addpoints_scheme&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=addpoints_scheme&tab='.$tab);
	  	 }	
	  }
	  
	  function addredeempoints_scheme($data)
	  {
		$addredeempoints_scheme['company']=$data['company'];
		$addredeempoints_scheme['points']=$data['points'];
		$addredeempoints_scheme['rs']=$data['rs'];

	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_redeempoints_scheme',$addredeempoints_scheme); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_redeempoints_scheme',$addredeempoints_scheme,"`redeem_points_id`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=redeempoints_scheme&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=redeempoints_scheme&tab='.$tab);
	  	 }	
	  }
	  
	function add_clients_user($data)
	  {
	  
		$add_clients_user['uname']=$data['uname'];
		$add_clients_user['ufathername']=$data['ufathername'];
		$add_clients_user['umobile']=$data['umobile'];
		$add_clients_user['uaddress']=$data['uaddress'];
		$add_clients_user['password']=$data['password'];
		$add_clients_user['cid']=$data['cid'];
		$add_clients_user['bid']=$data['bid'];
		$add_clients_user['cuid']=$data['cuid'];
		$add_clients_user['email']=$data['email'];
		$add_clients_user['state']=$data['state'];
		$add_clients_user['city']=$data['city'];
		$add_clients_user['pincode']=$data['pincode'];
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_clients_user',$add_clients_user); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_clients_user',$add_clients_user,"`uid`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=clients_user&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=clients_user&tab='.$tab);
	  	 }	
	  }
	  
	  function add_awn_admin_user($data)
	  {
	  
		$add_awn_admin_user['designation']=$data['designation'];
		$add_awn_admin_user['cid']=$data['cid'];
		$add_awn_admin_user['bid']=$data['bid'];
		$add_awn_admin_user['password']=$data['password'];
		$add_awn_admin_user['name']=$data['name'];
		$add_awn_admin_user['email']=$data['email'];
		$add_awn_admin_user['mobile']=$data['mobile'];
		$add_awn_admin_user['address']=$data['address'];
		$add_awn_admin_user['state']=$data['state'];
		$add_awn_admin_user['city']=$data['city'];
		$add_awn_admin_user['pincode']=$data['pincode'];
		$add_awn_admin_user['datetime']=date('Y-m-d');
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		 $sql=$this->insert('awn_user',$add_awn_admin_user); $tab="View";
		 }
		 elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_user',$add_awn_admin_user,"`awnid`='".$_POST['editid']."'");$tab="View"; 
		}
		 if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=awn_admin_user&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=awn_admin_user&tab='.$tab);
	  	 }	
	  }
	  
	  function add_course($data)
	  {
	 	
		$add_course['course']=$data['course'];
	
		if($_REQUEST['tab']=="Add") 
		 { 
		
		$add_course['added_date']=date('Y-m-d');
		 $sql=$this->insert('awn_course',$add_course); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_course',$add_course,"`course_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=course&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=course&tab='.$tab);
	  	 }	
	  }
	
	
	 function add_answer($data)
	  {
	 	
		$add_answer['quest_id']=$data['quest_id'];
		$add_answer['answer']=$data['answer'];
		$add_answer['iscorrect']=$data['iscorrect'];
		
		if($_REQUEST['tab']=="Add") 
		 { 
		$add_answer['added_date']=date('Y-m-d');
		 $sql=$this->insert('awn_answer',$add_answer); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_answer',$add_answer,"`ans_id`='".$_POST['editid']."'");$tab="View"; 
		}
		//print_r($data); die;
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=answer&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=answer&tab='.$tab);
	  	 }	
	  }
	  
		
	  function add_course_unit($data)
	  {
	 	
		$add_course['course_id']=$data['course'];
		$add_course['unit_name']=$data['unit'];
		if($_REQUEST['tab']=="Add") 
		 { 
		
		$add_course['added_date']=date('Y-m-d');
		 $sql=$this->insert(COURSE_UNIT,$add_course); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update(COURSE_UNIT,$add_course,"`unit_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=unit&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=unit&tab='.$tab);
	  	 }	
	  }
	  
	  function question_list($id)
	  {
		$this->connect();
		$str="select * from `awn_question`";
		$fire=$this->dbc->query($str);
		while($fetch=$fire->fetch_object())
		{
			if($fetch->quest_id==$id){ $sel='selected'; } else { $sel=''; }		
			print '<option value="'.$fetch->quest_id.'" '.$sel.'>'.$fetch->question.'</option>';
		}
		$this->disconnect();  
	  }
	   function add_question($data)
	  {
	 	if($_FILES['vedio']['name']!='' && $_FILES['vedio']['error'][0]!=4){
				
				$path = "assets/images/";
				 
						
						$name = $_FILES['vedio']['name'];
						$tmp = $_FILES['vedio']['tmp_name'];
						$ext = substr($name, strrpos($name, '.') + 1); 
						$filename=time().'_'.rand(0,300).'.'.$ext;
		      			$file = $path.$filename;
						 $r=move_uploaded_file($tmp, $file);
					
					$add_question['file']=$filename;
					
			}
		$add_question['course_id']=$data['course_id'];
		$add_question['unit_id']=$data['unit_id'];
		$add_question['question_type']=$data['qtype'];
		$add_question['question']=$data['question'];
		
		if($_REQUEST['tab']=="Add") 
		 { 
		
		$add_question['added_date']=date('Y-m-d');
		 $sql=$this->insert('awn_question',$add_question); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
			$sql=$this->update('awn_question',$add_question,"`quest_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=question&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=question&tab='.$tab);
	  	 }	
	  }
	  
	
	  
	 function fetch_course_id()
	 {
		$this->connect();
		$str="select * from ".COURSE." where `course_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_designation_id()
	 {
		$this->connect();
		$str="select * from ".awn_designation." where `deg_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_list_designation($int="")
	   {
		$this->connect();
	    $sel="SELECT DISTINCT `designation`,`deg_id` FROM `awn_designation` ";
		$fire=$this->dbc->query($sel);
		print '<option value="">Select Designation   </option>';
		while($fetch=$fire->fetchObject())
		{
						//print_r($fetch);
			           $select=(!empty($int) && $fetch->deg_id==$int)?"selected":"";
					   print'<option value="'.$fetch->deg_id.'" '.$select.'>'.$fetch->designation.'</option>';
		}
		$this->disconnect();
	}
	 
	 // Company Master edit section code start here
	  function fetch_companymaster_id()
	 {
		$this->connect();
		$str="select * from ".awn_company." where `company_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	/* function fetch_companymaster_id($table,$attr)
	 {
	   $this->connect();
	   $qey="SELECT * FROM ".$table." WHERE `".$attr."`='".$_GET['id']."' ";
	   $fire=$this->dbc->query($qey);
	   $run=$fire->fetchObjct();
	   return $run;
	   $this->disconnect();
	  }*/
	 
	 // add point scheme section
	 function fetch_list_company($int="")
	   {
		$this->connect();
	    $sel="SELECT DISTINCT `companyname`,`company_id`,`designation` FROM `awn_company` ";
		$fire=$this->dbc->query($sel);
		print '<option value="">Select Company   </option>';
		while($fetch=$fire->fetchObject())
		{
						//print_r($fetch);
			           $select=(!empty($int) && $fetch->company_id==$int)?"selected":"";
					   print'<option value="'.$fetch->company_id.'" '.$select.'>'.$fetch->companyname.' </option>';
		}
		$this->disconnect();
	}
	 
	 function fetch_branchmaster_id()
	 {
		$this->connect();
		$str="select * from ".awn_branches." where `branch_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_list_branches($int="")
	   {
		$this->connect();
	    $sel="SELECT DISTINCT `branchname`,`branch_id` FROM `awn_branches` ";
		$fire=$this->dbc->query($sel);
		print '<option value="">Select Branch   </option>';
		while($fetch=$fire->fetchObject())
		{
						//print_r($fetch);
			           $select=(!empty($int) && $fetch->branch_id==$int)?"selected":"";
					   print'<option value="'.$fetch->branch_id.'" '.$select.'>'.$fetch->branchname.'</option>';
		}
		$this->disconnect();
	}
	 
	  function fetch_companyusers_id()
	 {
		$this->connect();
		$str="select * from ".awn_companyusers." where `cu_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_addpoints_scheme_id()
	 {
		$this->connect();
		$str="select * from ".awn_addpoints_scheme." where `add_points_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_redeempoints_scheme_id()
	 {
		$this->connect();
		$str="select * from ".awn_redeempoints_scheme." where `redeem_points_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	  function fetch_clients_user_id()
	 {
		$this->connect();
		$str="select * from ".awn_clients_user." where `uid`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	 function fetch_awn_admin_user_id()
	 {
		$this->connect();
		$str="select * from ".awn_user." where `awnid`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetchObject();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	  function fetch_answer_id()
	 {
		$this->connect();
		$str="select * from `awn_answer` where `ans_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	  function fetch_member_id()
	 {
		$this->connect();
		$str="select * from `awn_member` where `member_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	  function fetch_unit_id()
	 {
		$this->connect();
		$str="select * from ".COURSE_UNIT." where `unit_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	   function fetch_question_id()
	 {
		$this->connect();
		$str="select * from awn_question where `quest_id`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
	 }
	 
	  function fetch_course_dropdown($id)
	 {
		$this->connect();
		 $str="select * from ".COURSE."";
		$fire=$this->dbc->query($str);
		while($fetch=$fire->fetch_object())
		{
			if($fetch->course_id==$id){ $sel='selected';} else { $sel='';}
			print '<option value="'.$fetch->course_id.'" '.$sel.'>'.$fetch->course.'</option>';	
		}
		
		$this->disconnect(); 
	 }
	 
	 
	 
	   function member_list($id)
	 {
		$this->connect();
		 $str="select * from `awn_member`";
		$fire=$this->dbc->query($str);
		while($fetch=$fire->fetch_object())
		{
			if($fetch->member_id==$id){ $sel='selected';} else { $sel='';}
			print '<option value="'.$fetch->member_id.'" '.$sel.'>'.$fetch->name.'</option>';	
		}
		
		$this->disconnect(); 
	 }
	 
	 
	  function fetch_unit_dropdown($id,$cid)
	 {
		$this->connect();
		if($cid!='')
		{
			$str="select * from ".COURSE_UNIT." where course_id=".$cid."";
		} else {$str="select * from ".COURSE_UNIT."";}
		$fire=$this->dbc->query($str);
		while($fetch=$fire->fetch_object())
		{
			if($fetch->unit_id==$id){ $sel='selected';} else { $sel='';}
			print '<option value="'.$fetch->unit_id.'" '.$sel.'>'.$fetch->unit_name.'</option>';	
		}
		
		$this->disconnect(); 
	 }
	
	  function add_user($data)
	  {
	 
	 if($_FILES['image']['name']!='' && $_FILES['image']['error'][0]!=4){
				
				$path = "assets/images/";
				 
						
						$name = $_FILES['image']['name'];
						$tmp = $_FILES['image']['tmp_name'];
						$ext = substr($name, strrpos($name, '.') + 1); 
						$filename='user'.time().'_'.rand(0,300).'.'.$ext;
		      			$file = $path.$filename;
						 $r=move_uploaded_file($tmp, $file);
					
					$data_subject['image']=$filename;
					
			}	
			
		$data_subject['username']=$data['username'];
		$data_subject['user_type']=$data['usertype'];
		$data_subject['name']=$data['name'];
		$data_subject['address']=$data['address'];
		$data_subject['phone']=$data['phone'];
		$data_subject['email']=$data['email'];
		$data_subject['country']=$data['country'];
		$data_subject['state']=$data['state'];
		$data_subject['city']=$data['city'];
		
		
		if($_REQUEST['tab']=="Add") 
		 { 
		
		
		 $sql=$this->insert('awn_user',$data_subject); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
		
		
		$sql=$this->update('awn_user',$data_subject,"`user_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=user&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=user&tab='.$tab);
	  	 }	
		
		
		
	  }
	  
	  function add_member($data)
	  {
		  
	 
	 if($_FILES['image']['name']!='' && $_FILES['image']['error'][0]!=4){
				
				$path = "assets/images/";
				 
						
						$name = $_FILES['image']['name'];
						$tmp = $_FILES['image']['tmp_name'];
						$ext = substr($name, strrpos($name, '.') + 1); 
						$filename='user'.time().'_'.rand(0,300).'.'.$ext;
		      			$file = $path.$filename;
						 $r=move_uploaded_file($tmp, $file);
					
					$add_member['image']=$filename;
					
			}	
			
		$add_member['username']=$data['username'];
		$add_member['user_type']=$data['usertype'];
		$add_member['name']=$data['name'];
		$add_member['address']=$data['address'];
		$add_member['phone']=$data['phone'];
		$add_member['email']=$data['email'];
		$add_member['country']=$data['country'];
		$add_member['state']=$data['state'];
		$add_member['city']=$data['city'];
		$add_member['utype']=$data['utype'];
		
		if($_REQUEST['tab']=="Add") 
		 { 
		
		
		 $sql=$this->insert('awn_member',$add_member); $tab="View";
		 }
		elseif($_REQUEST['tab']=="Edit") 
		{ 
		
		
		$sql=$this->update('awn_member',$add_member,"`member_id`='".$_POST['editid']."'");$tab="View"; 
		}
		
		if($sql)
		 {
		   @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=member&tab='.$tab);
		 }
		 else
		 {
		  @header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=member&tab='.$tab);
	  	 }	
		
		
		
	  
	  }
	  
	 function view_user (){
		$this->connect();
		$qey="SELECT `awn_user`.*,user_type.designation  FROM `awn_user` LEFT JOIN `user_type` ON `awn_user`.user_type=`user_type`.deg_id";
		$fire=$this->dbc->query($qey);
		$i=1;
		while($fetch=$fire->fetch_object())
		{ 
			   print '<tr class="gradeC">
			 <td>'.($i).'</td>
							
							
							<td>'.$fetch->designation.'</td>
							<td>'.$fetch->username.'</td>
							<td>'.$fetch->first_name.' '.$fetch->last_name.'</td>
							
							<td>'.$fetch->address.'</td>
							<td>'.$fetch->email.'</td>	
							<td>'.$fetch->phone.'</td>
								
							<td>'.$fetch->added_date.'</td>											
							<td><a id="edit" href="?token='.$_SESSION['token'].'&url=user&tab=Edit&id='.$fetch->user_id.'" title="Edit"><i class="icon-edit"></i></a> | <a id="'.$fetch->user_id.'" tble="awn_scope" del="one" title="Delete" attr="scope_id" class="del" href="javascript:void(0);"><i class="icon-trash"></i></a></td>

						</tr>';
						$i++;
		}  
		$this->disconnect(); 
		 
		 
	 } 
	 
	 function fetch_user_id()
	 {
		$this->connect();
		$str="select * from `awn_user` where `awnid`=".$_GET['id']."";
		$fire=$this->dbc->query($str);
		$fetch=$fire->fetch_object();
		return $fetch;
		$this->disconnect(); 
		 
	 }
	 
	 
	
	function user_deg($data)
	{
		$user['designation']=$data['desig'];
		
		
		if($_REQUEST['tab']=='Add')
		{
		
		$sql=$this->insert('user_type',$user); 
		}
		else if($_REQUEST['tab']=='Edit')
		{
			$sql=$this->update('user_type',$user,"`deg_id`='".$_POST['editid']."'"); 
		}
		if($sql)
		{
			header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=user_type&tab=View&sta=1');
		} 
		else
		{
			header('location:'.WEBSITE.'?token='.$_SESSION['token'].'&url=user_type&tab=View$sta=0');
		}
		
	}
	function user_type_view()
	{
		$this->connect();
		
		 $abc="SELECT * FROM `user_type`";
		
	    $fire=$this->dbc->query($abc);
		 $i=1;
		while($fetch=$fire->fetch_object())
		{ 
		     
			 print '<tr>
			 <td>'.$i.'</td>
			 <td>'.$fetch->designation.'</td>
			 <td><a id="edit" href="?token='.$_SESSION['token'].'&url=user_type&tab=Edit&id='.$fetch->deg_id.'" title="Edit"><i class="icon-edit"></i></a> | <a id="'.$fetch->deg_id.'" tble="awn_user" del="one" title="Delete" attr="user_id" class="del" href="javascript:void(0);"><i class="icon-trash"></i></a></td>
			 </tr>';
			 $i++;
		}
        
		
		 $this->disconnect();   	
 	}
	
	
	
	
	
	

	function import_inventory1()
	{
		$this->connect();
		$path=WEBSITE.'csv file/';
		if(isset($_POST['csv']))
		{
		$name=date('Y-m-d').'_'.$_File['csv']['name'];
		$csv_file=$path.$name;
		
		move_uploaded_file($_file['csv']['temp_name'], $csv_file);
		
		 if(($getfile = fopen($csv_file, "r")) !== FALSE)
		{
		
		$data=fgetcsv($getfile,1000,",");
		
		$num=count($data);
		for ($c=0; $c < $num/2; $c++)
		{
		
		$str=implode(",",$data);
		$abc=explode(",",$str);
		
		$col1=$abc[0];
		$col2=$abc[1];
	    $col3=$abc[2];
	    
		
		
		
 $str="INSERT INTO `awn_inventory`(`name`, `description`, `model` `brand`,) VALUES ('".$col1."','".$col2."','".$col3."')";	
		   $fire=$this->dbc->query($str);
	  
		
		}
		}
		}
		$this->disconnect();
	}
	
	function import_inventory()
	{
		$this->connect();
	$path='csv_file/';
		if($_FILES["csv"]["name"]!=''){
			
     date_default_timezone_set('Asia/Singapore');
     $medianame = $_FILES["csv"]["name"];
     $media_tmpname = $_FILES["csv"]["tmp_name"];
    
     $media_name = date('Y-m-d').$medianame;	 
     $destination = "csv_file/".$media_name ;
	

							 $rtr=move_uploaded_file($media_tmpname,$destination);					
							
							$fieldseparator = ",";
							$lineseparator = "\n";
							$csvfile = "csv_file/".$media_name;
							$addauto = 0;
							$save = 1;

if(!file_exists($csvfile)) {
    echo "File not found. Make sure you specified the correct path.\n";
    exit;
}

$file = fopen($csvfile,"r");
if(!$file) {
    echo "Error opening data file.\n";
    exit;

}

$size = filesize($csvfile);
if(!$size) {
    echo "File is empty.\n";
    exit;
}

$csvcontent = fread($file,$size);
fclose($file);
$lines = 1;
$queries = "";
$linearray = array();
$arrOfRecords = explode($lineseparator,$csvcontent);
$total = count($arrOfRecords);
print $totalLines = $total-1;


$j=0;
$n=0;
$flag=0;
$flag1=0;
$st=0;
$u=1;
foreach($arrOfRecords as $key => $line) 
{ 
  if(($lines > 1) and ($lines <=$totalLines))
  { 
   $line = trim($line," \t");
   $line = str_replace("\r","",$line);
     
    /************************************
    This line escapes the special character. remove it if entries are already escaped in the csv file
    ************************************/
    $line = str_replace("'","\'",$line);
    $line = str_replace('"','',$line);
    /*************************************/

    $linearray = split($fieldseparator,$line);
   
   if($key!='0'){
 
           //$name =  $linearray[0];
           $added_date = @date("Y-m-d");
                  	 //echo $adddate;
				   
			
				   
				   $name[]= rtrim(ltrim($linearray[0]," ")," ");
				   $description[]=rtrim(ltrim($linearray[1]," ")," ");
				   $model[]=rtrim(ltrim($linearray[2]," ")," ");
                   $brand[]= rtrim(ltrim($linearray[3]," ")," ");
                   
                  
                    }
               }
			   
               $lines++;
			  $u++;
              }
			
						
			if($flag1=="0")
			{ $tot=0;
			   for($i=0;$i<$totalLines-1; $i++)
			   {
				  
					   $str="INSERT INTO `awn_inventory`(`name`, `description`, `model`,`brand`) VALUES ('".$name[$i]."','".$description[$i]."','".$model[$i]."','". $brand[$i]."')";	
						//print '<br>';
                    $fire=$this->dbc->query($str);
				
			   }
	   
			   }  
				
			}
			 @header('location:'.WEBSITE.$_SESSION['token'].'/inventory/inventory/View'); 
 		 $this->disconnect();
	}
	
	
	
	
	
	

	function clean_editor($desc)
   {
   $desc=str_replace("\\\\r\\\\n","",$desc);
   $desc=str_replace("\r\n","",$desc);
   $desc=str_replace("<p>","",$desc);
   $desc=str_replace("</p>","",$desc);
   $this->escape($desc);
   return $desc;
   }
	
	
   }
?>