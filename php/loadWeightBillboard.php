<?php
## Database configuration
require_once 'db_connect.php';

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($db,$_POST['search']['value']); // Search value

## Search 
$searchQuery = "";
if($searchValue != ''){
   $searchQuery = " and (weight.serialNo like '%".$searchValue."%' or 
                    weight.vehicleNo like '%".$searchValue."%' )";
}

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from weight, packages, lots, customers, products, status, units, transporters WHERE weight.package = packages.id AND weight.lotNo = lots.id AND weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unitWeight AND transporters.id=weight.transporter AND weight.deleted = '0' AND weight.pStatus = 'Complete'");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($db,"select count(*) as allcount from weight, packages, lots, customers, products, status, units, transporters WHERE weight.package = packages.id AND weight.lotNo = lots.id AND weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unitWeight AND transporters.id=weight.transporter AND weight.deleted = '0' AND weight.pStatus = 'Complete'".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select weight.id, weight.serialNo, weight.vehicleNo, lots.lots_no, weight.batchNo, weight.invoiceNo, weight.deliveryNo, users.name,
weight.purchaseNo, customers.customer_name, customers.customer_phone, customers.customer_address, products.product_name, packages.packages, weight.unitWeight, weight.tare, 
weight.totalWeight, weight.actualWeight, weight.supplyWeight, weight.varianceWeight, weight.currentWeight, units.units, weight.moq, weight.dateTime, 
weight.unitPrice, weight.totalPrice, weight.remark, weight.status as Status, status.status, weight.manual, weight.manualVehicle, weight.manualOutgoing, weight.reduceWeight,
weight.outGDateTime, weight.inCDateTime, weight.pStatus, weight.variancePerc, transporters.transporter_name from weight, packages, lots, customers, products, units, status, users, transporters
WHERE weight.package = packages.id AND weight.lotNo = lots.id AND users.id = weight.created_by AND weight.pStatus = 'Complete' AND
weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND 
units.id=weight.unitWeight AND transporters.id=weight.transporter AND weight.deleted = '0'".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$counter = 1;
$sales = 0;
$salesWeight = 0;
$salesTare = 0;
$salesNett = 0;
$purchase = 0;
$purchaseWeight = 0;
$purchaseTare = 0;
$purchaseNett = 0;
$local = 0;
$localWeight = 0;
$localTare = 0;
$localNett = 0;

while($row = mysqli_fetch_assoc($empRecords)) {
  $manual = '';
  
  if($row['manual'] == '1'){
      $manual = "** This is manual weighing!";
  }

  if($row['outGDateTime'] == null || $row['outGDateTime'] == ''){
    $outGDateTime = '-';
  }
  else{
    $dateOut = new DateTime($row['outGDateTime']);
    $outGDateTime = date_format($dateOut,"d/m/Y H:m:s A");
  }

  if($row['inCDateTime'] == null || $row['inCDateTime'] == ''){
    $outGDateTime = '-';
  }
  else{
    $dateInt = new DateTime($row['inCDateTime']);
    $inCDateTime = date_format($dateInt,"d/m/Y H:m:s A");
  }

  if($row['Status'] == '1'){
    $sales++;
    $salesWeight += (float)$row['currentWeight'];
    $salesTare += (float)$row['tare'];
    $salesNett += (float)$row['actualWeight'];
  }
  else if($row['Status'] == '2'){
    $purchase++;
    $purchaseWeight += (float)$row['currentWeight'];
    $purchaseTare += (float)$row['tare'];
    $purchaseNett += (float)$row['actualWeight'];
  }
  else if($row['Status'] == '3'){
    $local++;
    $localWeight += (float)$row['currentWeight'];
    $localTare += (float)$row['tare'];
    $localNett += (float)$row['actualWeight'];
  }
    
  $data[] = array( 
    "no"=>$counter,
    "id"=>$row['id'],
    "serialNo"=>$row['serialNo'],
    "veh_number"=>$row['vehicleNo'],
    "lots_no"=>$row['lots_no'],
    "batchNo"=>$row['batchNo'],
    "invoiceNo"=>$row['invoiceNo'],
    "deliveryNo"=>$row['deliveryNo'],
    "purchaseNo"=>$row['purchaseNo'],
    "userName"=>$row['name'],
    "customer_name"=>$row['customer_name'],
    "customer_phone"=>$row['customer_phone'],
    "customer_address"=>$row['customer_address'],
    "product_name"=>$row['product_name'],
    "packages"=>$row['packages'],
    "unitWeight"=>$row['unitWeight'],
    "supplyWeight"=>$row['supplyWeight'],
    "varianceWeight"=>$row['varianceWeight'],
    "tare"=>$row['tare'],
    "totalWeight"=>$row['totalWeight'],
    "actualWeight"=>$row['actualWeight'],
    "currentWeight"=>$row['currentWeight'],
    "unit"=>$row['units'],
    "moq"=>$row['moq'],
    "dateTime"=>$row['dateTime'],
    "unitPrice"=>$row['unitPrice'],
    "totalPrice"=>$row['totalPrice'],
    "remark"=>$row['remark'],
    "status"=>$row['status'],
    "manual"=>$manual,
    "manualVehicle"=>$row['manualVehicle'],
    "manualOutgoing"=>$row['manualOutgoing'],
    "reduceWeight"=>$row['reduceWeight'],
    "outGDateTime"=>$outGDateTime,
    "inCDateTime"=>$inCDateTime,
    "pStatus"=>$row['pStatus'],
    "variancePerc"=> $row['variancePerc'],
    "transporter_name"=> $row['transporter_name']
  );

  $counter++;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "salesTotal" => $sales,
  "salesWeight" => $salesWeight,
  "salesTare" => $salesTare,
  "salesNet" => $salesNett,
  "purchaseTotal" => $purchase,
  "purchaseWeight" => $purchaseWeight,
  "purchaseTare" => $purchaseTare,
  "purchaseNet" => $purchaseNett,
  "localTotal" => $local,
  "localWeight" => $localWeight,
  "localTare" => $localTare,
  "localNet" => $localNett
);

echo json_encode($response);

?>