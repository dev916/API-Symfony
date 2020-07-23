

<!-- table -->
<?php include ('table-js.php') ?>
<script type="text/javascript" language="javascript" class="init">
	
	<!-- table / -->
function format ( d ) {
    return 'Designation: '+d.invoice+
        
        '&nbsp; &nbsp;<a class="btn btn-success" href="<?=WEBSITE?>?token=<?=$_SESSION['token']?>&main=transaction&url=transaction&tab=Edit&id='+d.id+' "> <i class="icon-edit">&nbsp</i></a>';
}
 
$(document).ready(function() {
    var dt = $('#example').DataTable( {
        "processing": true,
        "serverSide": true,
		"scrollX": true,
        "ajax": "<?=WEBSITE?>assets/plugins/data-tables/examples/server_side/scripts/server_processing.php?table=transaction&id=id",
        "columns": [
            {
                "class":          "details-control",
                "orderable":      true,
                "data":           null,
                "defaultContent": ""
            },
            <?php //$trimmed = rtrim($functions->columname_data('awn_geoplan'),',')?>
			
		
		{ "data": "companyname" },
		{ "data": "branchname" },
		/*{ "data": "type" },
		{ "data": "uid" },
		{ "data": "invoice" },
		{ "data": "amount" },
		{ "data": "points" },*/
		
		
			  /*{ "data": "uname" },
			  { "data": "ufathername" },
			  { "data": "umobile" },
			   { "data": "uaddress" },
			  { "data": "password" },
			  { "data": "cid" },
			  { "data": "bid" },
			  { "data": "cuid" },
			  { "data": "email" },
			   { "data": "state" },
			  { "data": "city" },
			  { "data": "pincode" },*/
			 
			
        ],
        "order": [[1, 'asc']]
    } );
 
    // Array to track the ids of the details displayed rows
    var detailRows = [];
 
    $('#example tbody').on( 'click', 'tr td:first-child', function () {
        var tr = $(this).parents('tr');
        var row = dt.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child( format( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    dt.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child').trigger( 'click' );
        } );
    } );
} );
</script>




<h1 class="row">Transaction</h1>
  <!-----------------------------------------------------------  ********* main working area ***************** -------------------------------------------------------------->

  
<div class="heading padding-10 bg-light-gray row  line-height-33"><a href="./?token=<?=$_SESSION['token']?>"><i class="icon-home"></i> Home </a> &raquo; Master     &raquo; <strong>Transaction</strong>

<?php if($_GET['tab']=='Add'||$_GET['tab']=='Edit') {?>
<a href="<?=WEBSITE?>?token=<?=$_SESSION['token']?>&url=transaction&tab=View" class="btn btn-success float-right"> <i class="icon-eye-open"></i> View </a>
<?php }else { ?><a href="<?=WEBSITE?>?token=<?=$_SESSION['token']?>&url=transaction&tab=Add" class="btn btn-success float-right"> <i class="icon-plus"></i> Add</a> <?php } ?>


<div class="clear"></div>
</div>
  
 
 
 <?php if($_GET['tab']=='View') { ?>
 
 <div class="row">
 <div class="demo_jui">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		<th>Option</th>
		<th>Company</th>
        <th>Branch</th>
        <th>Billing</th>
        <th>Type</th>
        <th>User Id</th>
        <th>invoice</th>
        <th>Amount</th>
        <th>Points</th>
        
		<!--<th>Uname</th>
        <th>Ufathername</th>
		<th>Umobile</th>
		<th>Uaddress</th>
		<th>Password</th>
		<th>Cid</th>
		<th>Bid</th>
		<th>CUid</th>
		<th>Email</th>
		<th>State</th>
		<th>City</th>
		<th>Pincode</th>	-->
       
        
        <!--<th> Added Date </th>-->	
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
			</div>


 </div>
 
  <? }  if($_GET['tab']=='Edit' || $_GET['tab']=='Add' ) {
  
  if($_GET['tab']=='Edit'){
	   $user=$main->fetch_transaction(); 
	   //$huser=$function->head_user($data ,$ab);
  }
  
   ?> 
  
   <form action="<?=WEBSITE?>pass.php?token=<?=$_SESSION['token']?>&action=transaction&tab=<?=$_GET['tab']?>" method="post" enctype="multipart/form-data">
 <div class="row">
  
   
    <div class="span-3">
    <label class="control-label"  for="firstName">Company</label>
															  <div class="controls">
                                                            <select name="company" class="m-wrap span12" id="company" required="required" >
															   <?= $main->fetch_list_company($companyusers->company);?> 
                                                            </select>
                                                            
                                                            <label class="control-label"  for="firstName">Branch</label>
															  <div class="controls">
                                                            <select name="branch" class="m-wrap span12" id="branch" required="required" >
                                                               <?= $main->fetch_list_branches($companyusers->branch);?>
                                                            </select>
                                                            <label class="control-label"  for="firstName">Type</label>
															  <div class="controls">
                                                            <select name="designation" class="m-wrap span12" id="designation" required="required"  >
                                                              <?= $main->fetch_list_branches($companyusers->branch);?>
                                                            </select>
                                                            
                                                            <label>User Name</label>
     <div><input type="text" name="uid" value="<?=$user->uid?>"/> </div>
     
     <div>Invoice<input type="text" name="invoice" value="<?=$user->invoice?>"/> </div>
     
     <label> amount</label>
     <div><input type="text" name="amount" value="<?=$user->amount?>"/> </div>
    
     <label> points</label>
     <div><input type="text" name="points" value="<?=$user->points?>"/> </div>
     
     
     
     
      
    </div></div>
    
    
    
     </div>
	 <!--<div class="row">
	 <div class="span-3">
     <label> Ufathername</label>
     <div><input type="text" name="ufathername" value=""/> </div>
    </div>
     </div>-->
	 
	 <!--<div class="row">
	 <div class="span-3">
     <label> Umobile</label>
     <div><input type="text" name="umobile" value=""/> </div>
    </div>
     </div>-->
	 
	 <!--<div class="row">
	 <div class="span-3">
     <label> Uaddress</label>
     <div><input type="text" name="uaddress" value=""/> </div>
    </div>
     </div>-->
	 
	 <!--<div class="row">
	 <div class="span-3">
     <label> Password</label>
     <div><input type="text" name="password" value=""/> </div>
    </div>
     </div>-->
	 
	 <!--<div class="row">
	 <div class="span-3">
     <label> Cid</label>
     <div><input type="text" name="cid" value=""/> </div>
    </div>
     </div>-->
	 
	 <!--<div class="row">
	 <div class="span-3">
     <label> Bid</label>
     <div><input type="text" name="bid" value=""/> </div>
    </div>
     </div>-->
	 
	<!-- <div class="row">
	 <div class="span-3">
     <label> CUid</label>
     <div><input type="text" name="cuid" value=""/> </div>
    </div>
     </div>
	 
	 <div class="row">
	 <div class="span-3">
     <label> Email</label>
     <div><input type="text" name="email" value=""/> </div>
    </div>
     </div>-->
	 
	<!-- <div class="row">
	 <div class="span-3">
     <label> State</label>
     <div><input type="text" name="state" value=""/> </div>
    </div>
     </div>
	 
	 <div class="row">
	 <div class="span-3">
     <label> City</label>
     <div><input type="text" name="city" value=""/> </div>
    </div>
     </div>-->
	 
	<!-- <div class="row">
	 <div class="span-3">
     <label> Pincode</label>
     <div><input type="text" name="pincode" value=""/> </div>
    </div>
     </div>-->
   
  <div class="row">
    
    
 <input type="hidden" name="editid" value="<?=$_GET['id']?>" />
  <input type="submit" value=" Submit " name="sub" id="add_user" class="btn btn-danger float-right" />
  <div class="clear"></div>
  </div>

 
 </form>
  <? } ?>
 
 
 
 
 