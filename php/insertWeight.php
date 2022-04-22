<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['status'], $_POST['lotNo'],$_POST['customerNo'],$_POST['unitWeight'],$_POST['moq'],$_POST['tareWeight']
,$_POST['currentWeight'],$_POST['product'],$_POST['package'],$_POST['unitPrice'],$_POST['actualWeight'],$_POST['totalPrice']
,$_POST['totalWeight'], $_POST['dateTime'])){

	$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
	$lotNo = filter_input(INPUT_POST, 'lotNo', FILTER_SANITIZE_STRING);
	$vehicleNo = '-';
	$invoiceNo = '-';
	$deliveryNo = '-';
	$batchNo = '-';
	$purchaseNo = '-';
	$remark = '';
	$manual = "0";

	if($_POST['invoiceNo'] != null && $_POST['invoiceNo'] != ''){
		$invoiceNo = filter_input(INPUT_POST, 'invoiceNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['deliveryNo'] != null && $_POST['deliveryNo'] != ''){
		$deliveryNo = filter_input(INPUT_POST, 'deliveryNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['batchNo'] != null && $_POST['batchNo'] != ''){
		$batchNo = filter_input(INPUT_POST, 'batchNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['purchaseNo'] != null && $_POST['purchaseNo'] != ''){
		$purchaseNo = filter_input(INPUT_POST, 'purchaseNo', FILTER_SANITIZE_STRING);
	}

	if($_POST['remark'] != null && $_POST['remark'] != ''){
		$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
	}
	
	if($_POST['manual'] != null && $_POST['manual'] != ''){
	    $manual = "1";
	}

	if($_POST['manualVehicle'] != null && $_POST['manualVehicle'] != ''){ //checked
	    if($_POST['vehicleNo'] != null && $_POST['vehicleNo'] != ''){
			$vehicleNo = filter_input(INPUT_POST, 'vehicleNo', FILTER_SANITIZE_STRING);
		}
	}
	else{
		if($_POST['vehicleNoTxt'] != null && $_POST['vehicleNoTxt'] != ''){
			$vehicleNo = filter_input(INPUT_POST, 'vehicleNoTxt', FILTER_SANITIZE_STRING);
		}
	}
	
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$unitWeight = filter_input(INPUT_POST, 'unitWeight', FILTER_SANITIZE_STRING);
	$currentWeight = filter_input(INPUT_POST, 'currentWeight', FILTER_SANITIZE_STRING);
	$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
	$moq = filter_input(INPUT_POST, 'moq', FILTER_SANITIZE_STRING);
	$tareWeight = filter_input(INPUT_POST, 'tareWeight', FILTER_SANITIZE_STRING);
	$package = filter_input(INPUT_POST, 'package', FILTER_SANITIZE_STRING);
	$unitPrice = filter_input(INPUT_POST, 'unitPrice', FILTER_SANITIZE_STRING);
	$actualWeight = filter_input(INPUT_POST, 'actualWeight', FILTER_SANITIZE_STRING);
	$totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_SANITIZE_STRING);
	$totalWeight = filter_input(INPUT_POST, 'totalWeight', FILTER_SANITIZE_STRING);
	$date = new DateTime($_POST['dateTime']);
	$dateTime = date_format($date,"Y-m-d h:m:s");

	if($_POST['serialNumber'] != null && $_POST['serialNumber'] != ''){
		if ($update_stmt = $db->prepare("UPDATE weight SET vehicleNo=?, lotNo=?, batchNo=?, invoiceNo=?, deliveryNo=?, purchaseNo=?, customer=?, productName=?, package=?
		, unitWeight=?, tare=?, totalWeight=?, actualWeight=?, unit=?, moq=?, unitPrice=?, totalPrice=?, remark=?, status=?, dateTime=?, manual=? WHERE serialNo=?")){
			$update_stmt->bind_param('ssssssssssssssssssssss', $vehicleNo, $lotNo, $batchNo, $invoiceNo, $deliveryNo, $purchaseNo, $customerNo, $product
			, $package, $currentWeight, $tareWeight, $totalWeight, $actualWeight, $unitWeight, $moq, $unitPrice, $totalPrice, $remark, $status, 
			$dateTime, $manual, $_POST['serialNumber']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 
			else{
				$update_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> $insert_stmt->error
				)
			);
		}
	}
	else{
		$x=$_POST['status'];

		if($update_stmt2 = $db->prepare("SELECT * FROM status WHERE id=?")){
			$update_stmt2->bind_param('s', $status);

			if (! $update_stmt2->execute()) {
          echo json_encode(
              array(
                  "status" => "failed",
                  "message" => "Something went wrong when pulling status"
              )); 
      }
      else{
				$result2 = $update_stmt2->get_result();
				$id=$_POST['status'];
				$firstChar = "";

				if ($row2 = $result2->fetch_assoc()) {
					$id = $row2['misc_id'];
					$firstChar = $row2['prefix'];
				}

				if ($update_stmt = $db->prepare("SELECT * FROM miscellaneous WHERE id=?")) {
					$update_stmt->bind_param('s', $id);
					
					// Execute the prepared query.
					if (! $update_stmt->execute()) {
						echo json_encode(
							array(
								"status" => "failed",
								"message" => "Something went wrong"
							)); 
					}
					else{
						$result = $update_stmt->get_result();
						$message = array();
						
						while ($row = $result->fetch_assoc()) {
							$charSize = strlen($row['value']);
							$misValue = $row['value'];
		
							for($i=0; $i<(5-(int)$charSize); $i++){
								$firstChar.='0';  // S0000
							}
					
							$firstChar.=$misValue;  //S00009
		
							if ($insert_stmt = $db->prepare("INSERT INTO weight (serialNo, vehicleNo, lotNo, batchNo, invoiceNo, deliveryNo, purchaseNo, customer, productName, package
							, unitWeight, tare, totalWeight, actualWeight, unit, moq, unitPrice, totalPrice, remark, status, dateTime, manual) 
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
								$insert_stmt->bind_param('ssssssssssssssssssssss', $firstChar, $vehicleNo, $lotNo, $batchNo, $invoiceNo, $deliveryNo, $purchaseNo, $customerNo, $product, $package, $currentWeight, $tareWeight, $totalWeight, $actualWeight, $unitWeight, $moq, $unitPrice, $totalPrice, $remark, $status, $dateTime, $manual);
								
								// Execute the prepared query.
								if (! $insert_stmt->execute()){
									echo json_encode(
										array(
											"status"=> "failed", 
											"message"=> $insert_stmt->error
										)
									);
								} 
								else{
									$misValue++;
									///insert miscellaneous
									if ($update_stmt = $db->prepare("UPDATE miscellaneous SET value=? WHERE id=?")){
										$update_stmt->bind_param('ss', $misValue, $id);
										
										// Execute the prepared query.
										if (! $update_stmt->execute()){
							
											echo json_encode(
												array(
													"status"=> "failed", 
													"message"=> $update_stmt->error
												)
											);
										} 
										else{
											$update_stmt->close();
											$db->close();
											
											echo json_encode(
												array(
													"status"=> "success", 
													"message"=> "Added Successfully!!" 
												)
											);
							
										}
									} else{
							
										echo json_encode(
											array(
												"status"=> "failed", 
												"message"=> $update_stmt->error
											)
										);
									}
								}
							}
						}
					}
				}
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when pulling status"
				)
			); 
		}
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

?>