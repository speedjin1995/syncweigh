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
$searchQuery = " ";
/*if($searchValue != ''){
   $searchQuery = " and (users.name like '%".$searchValue."%' or 
        users.username like '%".$searchValue."%' or
        roles.role_name like'%".$searchValue."%' ) ";
}*/

## Total number of records without filtering
$sel = mysqli_query($db,"select count(*) as allcount from weight, vehicles, packages, lots, customers, products, status, units WHERE weight.vehicleNo = vehicles.id AND weight.package = packages.id AND weight.lotNo = lots.id AND weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unit");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
//echo "select count(*) as allcount from users, roles WHERE".$searchQuery;
$sel = mysqli_query($db,"select count(*) as allcount from weight, vehicles, packages, lots, customers, products, status, units WHERE weight.vehicleNo = vehicles.id AND weight.package = packages.id AND weight.lotNo = lots.id AND weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unit".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select weight.serialNo, vehicles.veh_number, lots.lots_no, weight.batchNo, weight.invoiceNo, weight.deliveryNo, weight.purchaseNo, customers.customer_name, products.product_name, packages.packages, weight.unitWeight, weight.tare, weight.totalWeight, weight.actualWeight, units.units, weight.moq, weight.date, weight.time, weight.unitPrice, weight.totalPrice, weight.remark, status.status from weight, vehicles, packages, lots, customers, products, units, status WHERE weight.vehicleNo = vehicles.id AND weight.package = packages.id AND weight.lotNo = lots.id AND weight.customer = customers.id AND weight.productName = products.id AND status.id=weight.status AND units.id=weight.unit".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();

while($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "serialNo"=>$row['serialNo'],
    "veh_number"=>$row['veh_number'],
    "lots_no"=>$row['lots_no'],
    "batchNo"=>$row['batchNo'],
    "invoiceNo"=>$row['invoiceNo'],
    "deliveryNo"=>$row['deliveryNo'],
    "purchaseNo"=>$row['purchaseNo'],
    "customer_name"=>$row['customer_name'],
    "product_name"=>$row['product_name'],
    "packages"=>$row['packages'],
    "unitWeight"=>$row['unitWeight'],
    "tare"=>$row['tare'],
    "totalWeight"=>$row['totalWeight'],
    "actualWeight"=>$row['actualWeight'],
    "unit"=>$row['unit'],
    "moq"=>$row['moq'],
    "date"=>$row['date'],
    "time"=>$row['time'],
    "unitPrice"=>$row['unitPrice'],
    "totalPrice"=>$row['totalPrice'],
    "remark"=>$row['remark'],
    "status"=>$row['status']
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);

?>