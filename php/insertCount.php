<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

if(isset($_POST['status'], $_POST['lotNo'], $_POST['invoiceNo'], $_POST['vehicleNo'], $_POST['customerNo'], $_POST['deliveryNo'], $_POST['unitWeight']
, $_POST['batchNo'], $_POST['purchaseNo'], $_POST['currentWeight'], $_POST['product'], $_POST['moq'], $_POST['tareWeight'], $_POST['package'], $_POST['unitPrice'], $_POST['actualWeight']
, $_POST['remark'], $_POST['totalPrice'], $_POST['totalPCS'], $_POST['unitWeight1'])){

	$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
	$lotNo = filter_input(INPUT_POST, 'lotNo', FILTER_SANITIZE_STRING);
	$invoiceNo = filter_input(INPUT_POST, 'invoiceNo', FILTER_SANITIZE_STRING);
	$vehicleNo = filter_input(INPUT_POST, 'vehicleNo', FILTER_SANITIZE_STRING);
	$customerNo = filter_input(INPUT_POST, 'customerNo', FILTER_SANITIZE_STRING);
	$deliveryNo = filter_input(INPUT_POST, 'deliveryNo', FILTER_SANITIZE_STRING);
	$unitWeight = filter_input(INPUT_POST, 'unitWeight', FILTER_SANITIZE_STRING);
	$batchNo = filter_input(INPUT_POST, 'batchNo', FILTER_SANITIZE_STRING);
	$purchaseNo = filter_input(INPUT_POST, 'purchaseNo', FILTER_SANITIZE_STRING);
	$currentWeight = filter_input(INPUT_POST, 'currentWeight', FILTER_SANITIZE_STRING);
	$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
	$moq = filter_input(INPUT_POST, 'moq', FILTER_SANITIZE_STRING);
	$tareWeight = filter_input(INPUT_POST, 'tareWeight', FILTER_SANITIZE_STRING);
	$package = filter_input(INPUT_POST, 'package', FILTER_SANITIZE_STRING);
	$unitPrice = filter_input(INPUT_POST, 'unitPrice', FILTER_SANITIZE_STRING);
	$actualWeight = filter_input(INPUT_POST, 'actualWeight', FILTER_SANITIZE_STRING);
	$remark = filter_input(INPUT_POST, 'remark', FILTER_SANITIZE_STRING);
	$totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_SANITIZE_STRING);
    $totalPCS = filter_input(INPUT_POST, 'totalPCS', FILTER_SANITIZE_STRING);
    $unitWeight1 = filter_input(INPUT_POST, 'unitWeight1', FILTER_SANITIZE_STRING);

	if ($insert_stmt = $db->prepare("INSERT INTO count (vehicleNo, lotNo, batchNo, invoiceNo, deliveryNo, purchaseNo, customer, productName, package
	, unitWeight, tare, currentWeight, actualWeight, unit, moq, unitPrice, totalPrice, remark, status, totalPCS) 
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
		$insert_stmt->bind_param('ssssssssssssssssssss', $vehicleNo, $lotNo, $batchNo, $invoiceNo, $deliveryNo, $purchaseNo, $customerNo, $product
		, $package, $unitWeight1, $tareWeight, $currentWeight, $actualWeight, $unitWeight, $moq, $unitPrice, $totalPrice, $remark, $status
        , $totalPCS);
		
		// Execute the prepared query.
		if (! $insert_stmt->execute()){

            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $insert_stmt->error
                )
            );

		} else{

            $insert_stmt->close();
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
                "message"=> $insert_stmt->error
            )
        );
	}		

} else{

    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
    
}

?>