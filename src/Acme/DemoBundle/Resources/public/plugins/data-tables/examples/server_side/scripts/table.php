 <!-- table -->
 
	<link rel="stylesheet" type="text/css" href="assets/plugins/data-tables/media/css/jquery.dataTables.css">
	<script type="text/javascript" language="javascript" src="assets/plugins/data-tables/media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="assets/plugins/data-tables/media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" class="init">

$(document).ready(function() {
	$('#example').dataTable( {
		"processing": true,
		"serverSide": true,
		"ajax": "scripts/server_processing.php"
	} );
} );

	</script>


		

			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>First name</th>
						<th>Last name</th>
						<th>Position</th>
						<th>Office</th>
						<th>Start date</th>
						<th>Salary</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th>First name</th>
						<th>Last name</th>
						<th>Position</th>
						<th>Office</th>
						<th>Start date</th>
						<th>Salary</th>
					</tr>
				</tfoot>
			</table>

			


 <!-- table / --> 