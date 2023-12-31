<?php include('includes/authenticate.php'); ?>
<?php
	$getmaxreg = mysqli_query($dbconnect,"SELECT Max(drugitem_code) as OPNO FROM tbl_drugs");
	$asreg = mysqli_fetch_array($getmaxreg);
	$opnos = $asreg['OPNO'];
	$nextcode = $opnos+1;
	$postnextcode = str_pad($nextcode,4,"0",STR_PAD_LEFT);
	$result = mysqli_query($dbconnect,"SELECT * FROM tbl_drug_categories");
	$result1 = mysqli_query($dbconnect,"SELECT * FROM tbl_paymentschemes");
	?>
<!DOCTYPE html>
<html>

<head>

    <?php include('includes/meta.php');?>
	
    <title>Drug Catalog - <?php echo "$smart_name"; ?></title>
	
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">
	<meta name="description" content=""/>
	<meta name="keywords" content=""/>

</head>

<body>
    <div id="wrapper">
    <!-- Navigation -->
	<?php include('includes/sidebar.php');?>
    <!-- Navigation -->

        <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
			<?php include('includes/top-nav.php'); ?>
        </div>
		<div class="row wrapper border-bottom white-bg page-heading">
			<div class="col-lg-7">
				<h2>Drugs</h2>
				<ol class="breadcrumb">
					<li>
						<a href="drugs.php"> Add List</a>
					</li>                        
					<li class="active">
						<strong>New Drugs</strong>
					</li>
				</ol>
			</div>
				<div class="col-lg-5">
				<p class="pull-right"><br>
				<span><a href="drugs.php"><button class="btn btn-success" type="button"><i class="fa fa-money"></i>&nbsp;&nbsp;<span class="bold"> Drug Prices</span></button></a></span> &nbsp; <span><a href="drugs.php"><button class="btn btn-primary" type="button"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;<span class="bold"> Back to Drugs</span></button></a></span>
				</p>
				</div>
		</div>

        <div class="wrapper wrapper-content">
                <div class="row">					
					<div class="row">					
									
					</div>				
					<div class="col-lg-4">					
						<div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Add Drugs </h5>
						</div>
                        <div class="ibox-content">
                           <div class="row">
								<form role="form" method="post">
									<div class="col-sm-12">													
										<?php
											if(isset($_POST['newcounty'])){
											if(empty($_POST['brand'])){
												echo "<div class=\"alert alert-danger alert-dismissable\"><button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button><i class=\"fa fa-exclamation-triangle\"></i> Name is required</div>";
														}
												else {
													
												$brand = $dbconnect->real_escape_string($_POST['brand']);
												$generic = 'Not stated';
												$strength = 'Not stated';
												$dosage = 'Not stated';
												$manufacturer = 'Not stated';
												$prescription = 'Not stated';
												$category = $dbconnect->real_escape_string($_POST['category']);
                                                $description = $dbconnect->real_escape_string($_POST['description']);
												$imageurl = 'Not stated';
												

												$checkcounty = mysqli_query($dbconnect, "SELECT * FROM tbl_drugs WHERE brand_name='$brand'");
												$countNo = mysqli_num_rows($checkcounty);
												if($countNo >= 1){
															echo "<div class=\"alert alert-danger alert-dismissable\"><button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button><i class=\"fa fa-exclamation-triangle\"></i> Code or name already exists.</div>";
														}
												else {
													$sql5 = "INSERT INTO `tbl_drugs` (`brand_name`, `generic_name`, `strength`, `dosage_form`, `manufacturer`, `prescription_required`, `description`, `image_url`,`drugitem_cat`,`drugitem_code`) VALUES ('$brand', '$generic', '$strength','$dosage', '$manufacturer', '$prescription','$description', '$imageurl','$category','$postnextcode')";
													$result5 = $dbconnect->query($sql5);	
													echo "<div class=\"alert alert-success alert-dismissable\"><button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button><i class=\"fa fa-exclamation-triangle\"></i>Successful</div>";
													
															}
														}
													}
												?>
											</div>
												
											<div class="col-sm-12">
                                                
                                            <div class="form-group">
                                                <label for="brand-name">Name:</label>
                                                <input type="text" class="form-control" id="brand-name" name="brand">
                                            </div>
											<div class="form-group">
                                                <label for="brand-name">Alert Qty:</label>
                                                <input type="text" class="form-control" id="brand-name" name="brand">
                                            </div>
													<div class="form-group">
															<label>Category</label>
															<select name="category" class="form-control" readonly>
															
																	<option value='Phamacitical'>Phamacitical</option>
																	<option value='Non Phamacitical'>Non phamacitical</option>
																

															</select>
														</div>
                                            
                                            <div class="form-group">
                                                <label for="description">Description:</label>
                                                <textarea class="form-control" id="description" rows="3" value="Not Stated" name="description"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="image-url">Image URL:</label>
                                                <input disabled type="file" class="form-control" id="image-url" name="image-url">
                                            </div>
											
											<div class="form-group">
												<button name="newcounty" class="btn btn-md btn-primary" type="submit"><i class="fa fa-plus"></i> Add Drug </button>
											</div>	
											</div>																					
								</form>
							</div>
						</div>
					 </div>
					</div>
					
					
					<div class="col-lg-8">					
						<div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Drug Catalog</h5>
						</div>
                        <div class="ibox-content">
                           <div class="row">
								<table class="table table-striped table-bordered table-hover dataTables-example" >
								
								<thead>
								<tr>
									
									<th>S/NO </th>
									<th>Code</th>
									<th>Drug Name</th>
									<th>Action </th>
								</tr>
								</thead>
								<tbody>
								
								<?php 
								$No = 0;
								$getcountyname =mysqli_query($dbconnect,"SELECT * FROM tbl_drugs");
								while($gcn = mysqli_fetch_array($getcountyname)){
									$No=$No+1;
									$drugid = $gcn['drug_id'];
									$brand_name = $gcn['brand_name'];
									$drugitem_code = $gcn['drugitem_code'];
									//$drugitem_sellingprice = $gcn['drugitem_sellingprice'];
								?>	<td><?php echo $No; ?></td>
									<td><?php echo $drugitem_code; ?></td>
									<td><?php echo $brand_name; ?></td>
									<td><a href="edit-drug.php?drug_id=<?php echo $drugitem_code; ?>"><button class="btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit </button></a> | <button class="btn-xs btn-danger"><i class="fa fa-trash"></i></button></td>
								</tr>
								<?php
								}
								?>
								
								</tbody>
								</table>
							</div>
						</div>
					 </div>
					</div>
			</div>
        </div>

		<?php include 'includes/footer.php'?>

        </div>
    </div>

   <?php include 'includes/footer-scripts.php';?>
        <!-- Data Tables -->
    <script src="js/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script src="js/plugins/dataTables/dataTables.responsive.js"></script>
    <script src="js/plugins/dataTables/dataTables.tableTools.min.js"></script>

    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function() {
            $('.dataTables-example').dataTable({
                responsive: true,
                "dom": 'T<"clear">lfrtip',
                "tableTools": {
                    "sSwfPath": "js/plugins/dataTables/swf/copy_csv_xls_pdf.swf"
                }
            });

            /* Init DataTables */
            var oTable = $('#editable').dataTable();

            /* Apply the jEditable handlers to the table */
            oTable.$('td').editable( '../example_ajax.php', {
                "callback": function( sValue, y ) {
                    var aPos = oTable.fnGetPosition( this );
                    oTable.fnUpdate( sValue, aPos[0], aPos[1] );
                },
                "submitdata": function ( value, settings ) {
                    return {
                        "row_id": this.parentNode.getAttribute('id'),
                        "column": oTable.fnGetPosition( this )[2]
                    };
                },

                "width": "90%",
                "height": "100%"
            } );


        });
	 </script>
</body>
</html>