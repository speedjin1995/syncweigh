<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
  $role = 'NORMAL';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $lots = $db->query("SELECT * FROM lots WHERE deleted = '0'");
  $vehicles = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $vehicles2 = $db->query("SELECT * FROM vehicles WHERE deleted = '0'");
  $products2 = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $packages = $db->query("SELECT * FROM packages WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $suppliers = $db->query("SELECT * FROM customers WHERE customer_status = 'SUPPLIERS' AND deleted = '0'");
  $units = $db->query("SELECT * FROM units WHERE deleted = '0'");
  $units1 = $db->query("SELECT * FROM units WHERE deleted = '0'");
  $status = $db->query("SELECT * FROM `status` WHERE deleted = '0'");
  $status2 = $db->query("SELECT * FROM `status` WHERE deleted = '0'");
}
?>

<select class="form-control" style="width: 100%;" id="customerNoHidden" style="display: none;">
  <option selected="selected">-</option>
  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
  <?php } ?>
</select>

<select class="form-control" style="width: 100%;" id="supplierNoHidden" style="display: none;">
  <option selected="selected">-</option>
  <?php while($rowCustomer=mysqli_fetch_assoc($suppliers)){ ?>
    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
  <?php } ?>
</select>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Weight Weighing</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <!--div class="col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3 style="text-align: center; font-size: 80px" class="head">
              100.00
              <sup style="font-size: 20px">KG</sup>
            </h3>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <div class="input-group-text bg-primary color-palette"><i>Indicator Connected</i></div>
              </div>
              <div class="col-6">
                <div class="input-group-text color-palette"><i>Checking Connection</i></div>
              </div>
            </div>
            <div class="row" style="margin-top: 3%;">
              <div class="col-12">
                <button type="button" class="btn btn-block bg-gradient-primary btn-lg"  onclick="newEntry()">
                  New Entry
                </button>
              </div>
            </div>
          </div>
        </div>
      </div-->

      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-4">
                <div class="input-group-text color-palette" id="indicatorConnected"><i>Indicator Connected</i></div>
              </div>
              <div class="col-4">
                <div class="input-group-text bg-danger color-palette" id="checkingConnection"><i>Checking Connection</i></div>
              </div>
              <div class="col-4">
                <button type="button" class="btn btn-block bg-gradient-primary"  onclick="setup()">
                  Setup
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h3 class="card-title">Billboard Description :</h3>
              </div>
              <div class="col-3"></div>
              <div class="col-1">
                <button type="button" class="btn btn-info btn-sm"  onclick="newEntry()">
                <i class="fas fa-plus"></i>
                </button>
              </div>
              <div class="col-1">
                <button type="button" class="btn btn-success btn-sm" id="excelSearch">
                <i class="fas fa-file-excel"></i>
                </button>
              </div>
              <div class="col-1">
                <button type="button" class="btn btn-warning btn-sm" id="filterSearch">
                <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="form-group col-3">
                <label>From Date:</label>
                <div class="input-group date" id="fromDate" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" id="fromDateValue" data-target="#fromDate"/>
                  <div class="input-group-append" data-target="#fromDate" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
                </div>
              </div>

              <div class="form-group col-3">
                <label>To Date:</label>
                <div class="input-group date" id="toDate" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" id="toDateValue"  data-target="#toDate"/>
                  <div class="input-group-append" data-target="#toDate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control Status" id="statusFilter" name="statusFilter" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($rowStatus=mysqli_fetch_assoc($status2)){ ?>
                      <option value="<?=$rowStatus['id'] ?>"><?=$rowStatus['status'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Customer No</label>
                  <select class="form-control" style="width: 100%;" id="customerNoFilter" name="customerNoFilter"></select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label>Vehicle No</label>
                  <select class="form-control vehicleNo" id="vehicleFilter" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($row1=mysqli_fetch_assoc($vehicles2)){ ?>
                      <option value="<?=$row1['id'] ?>"><?=$row1['veh_number'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group col-3">
                <label>Invoice No</label>
                <input class="form-control" type="text" id="invoiceFilter" placeholder="Invoice No">
              </div>

              <div class="form-group col-3">
                <label>Batch No</label>
                <input class="form-control" type="text" id="batchFilter" placeholder="Batch No">
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Product</label>
                  <select class="form-control" id="productFilter" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($rowProduct=mysqli_fetch_assoc($products2)){ ?>
                      <option value="<?=$rowProduct['id'] ?>"><?=$rowProduct['product_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <table id="weightTable" class="table table-bordered table-striped display">
            <thead>
              <tr>
                <th>Serial No.</th>
                <th>Product Name</th>
                <th>Unit</th>
                <th>Unit Weight</th>
                <th>Tare</th>
                <th>Total Weight</th>
                <th>Actual Weight</th>
                <th>MOQ</th>
                <th>Unit Price <br> (RM)</th>
                <th>Total Price <br> (RM)</th>
                <th></th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="3" style="text-align: right;">Total Accumulate</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

    <form role="form" id="extendForm">
      <div class="modal-header bg-gray-dark color-palette">
        <h4 class="modal-title">Add New Entry</h4>
        <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
      <div class="row">
        <div class="col-3">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Serial No.</label>
                <input class="form-control" type="text" placeholder="Serial No" id="serialNumber" name="serialNumber" readonly>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Date</label>
                <div class="input-group date" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#dateTime" id="dateTime" name="dateTime" required/>
                  <div class="input-group-append" data-target="#dateTime" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6 col-6 d-flex justify-content-center">
          <div class="small-box bg-success">
            <div class="inner">
            <h3 style="text-align: center; font-size: 80px" id="indicatorWeight">0.00kg</h3>
            </div>
          </div>
        </div>
      
      </div>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label>Status *</label>
              <select class="form-control" style="width: 100%;" id="status" name="status" required>
                <option selected="selected">-</option>
                <?php while($rowS=mysqli_fetch_assoc($status)){ ?>
                  <option value="<?=$rowS['id'] ?>"><?=$rowS['status'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="form-group">
              <label>Lot No *</label>
              <select class="form-control" style="width: 100%;" id="lotNo" name="lotNo" required>
                <option selected="selected">-</option>
                <?php while($row3=mysqli_fetch_assoc($lots)){ ?>
                  <option value="<?=$row3['id'] ?>"><?=$row3['lots_no'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label>Invoice No</label>
            <input class="form-control" type="text" placeholder="Invoice No" id="invoiceNo" name="invoiceNo" >
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              <label>Vehicle No</label>
              <select class="form-control" style="width: 100%;" id="vehicleNo" name="vehicleNo">
                <option selected="selected">-</option>
                <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                  <option value="<?=$row2['id'] ?>"><?=$row2['veh_number'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="labelStatus">Customer No *</label>
              <select class="form-control" style="width: 100%;" id="customerNo" name="customerNo" required></select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label>Delivery No</label>
            <input class="form-control" type="text" placeholder="Delivery No" id="deliveryNo" name="deliveryNo" >
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label>Unit Weight *</label>
              <select class="form-control" style="width: 100%;" id="unitWeight" name="unitWeight" required> 
                <option selected="selected">-</option>
                <?php while($rowunits=mysqli_fetch_assoc($units)){ ?>
                  <option value="<?=$rowunits['id'] ?>"><?=$rowunits['units'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-4">
            <label>Batch No</label>
            <input class="form-control" type="text" placeholder="Batch No" id="batchNo" name="batchNo" >
          </div>

          <div class="form-group col-md-3">
            <label>Purchase No</label>
            <input class="form-control" type="text" placeholder="Purchase No" id="purchaseNo" name="purchaseNo" >
          </div>

          <div class="form-group col-md-3">
            <label>Current Weight</label>
            <div class="input-group">
              <input class="form-control" type="number" placeholder="Current Weight" id="currentWeight" name="currentWeight" readonly required/>
              <div class="input-group-text bg-primary color-palette"><i id="changeWeight">KG/G</i></div>
            </div>
          </div>
          
          <?php 
            if($role == "ADMIN"){
              echo '<div class="form-group col-md-2">
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="manual" name="manual">
                    <label class="form-check-label" for="exampleCheck1">Manual</label>
                  </div>
              </div>';
            }
          ?>
          
          
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Product *</label>
              <select class="form-control" style="width: 100%;" id="product" name="product" required>
                <option selected="selected">-</option>
                <?php while($row5=mysqli_fetch_assoc($products)){ ?>
                  <option value="<?=$row5['id'] ?>"><?=$row5['product_name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label>M.O.Q *</label>
            <input class="form-control" type="number" placeholder="moq" id="moq" name="moq" min="0" required>
          </div>

          <div class="form-group col-md-3">
            <label>Tare Weight *</label>
            <div class="input-group">
              <input class="form-control" type="number" placeholder="Tare Weight" id="tareWeight" name="tareWeight" min="0" required/>
              <div class="input-group-text bg-danger color-palette"><i id="changeWeightTare">KG/G</i></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Package *</label>
              <select class="form-control" style="width: 100%;" id="package" name="package" required>
                <option selected="selected">-</option>
                <?php while($row6=mysqli_fetch_assoc($packages)){ ?>
                  <option value="<?=$row6['id'] ?>"><?=$row6['packages'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

            <div class="form-group col-md-3">
              <label>Unit Price</label>
              <div class="input-group">
                <div class="input-group-text"><i>RM</i></div>
                <input class="form-control" type="number" placeholder="unitPrice" id="unitPrice" name="unitPrice" min="0" required/>                        
              </div>
          </div>

            <div class="form-group col-md-3">
              <label>Actual Weight</label>
              <div class="input-group">
                <input class="form-control" type="number" placeholder="Actual Weight" id="actualWeight" name="actualWeight" readonly required/>
                <div class="input-group-text bg-success color-palette"><i id="changeWeightActual">KG/G</i></div>
              </div>
            </div>
        </div>

        <div class="row">
          <div class="col-sm-4">
            <div class="form-group">
              <label>Remark</label>
              <textarea class="form-control" rows="3" placeholder="Enter ..." id="remark" name="remark"></textarea>
            </div>
          </div>

            <div class="form-group col-md-3">
              <label>Total Price</label>
              <div class="input-group">
                <div class="input-group-text"><i>RM</i></div>
                <input class="form-control" type="number" placeholder="Total Price"  id="totalPrice" name="totalPrice" readonly required/>                        
              </div>
          </div>

            <div class="form-group col-md-3">
              <label>Total Weight</label>
              <div class="input-group">
                <input class="form-control" type="number" placeholder="Total Weight" id="totalWeight" name="totalWeight" readonly required/>
                <div class="input-group-text bg-success color-palette"><i id="changeWeightTotal">KG/G</i></div>
              </div>
            </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between bg-gray-dark color-palette">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="captureWeight" disabled>Capture Indicator</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="setupModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

    <form role="form" id="setupForm">
      <div class="modal-header bg-gray-dark color-palette">
        <h4 class="modal-title">Setup</h4>
        <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Serial Port</label>
              <select class="form-control" style="width: 100%;" id="serialPort" name="serialPort" required></select>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Baud Rate</label>
              <input class="form-control" type="number" id="serialPortBaudRate" name="serialPortBaudRate" value="9600" required>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Data Bits</label>
              <select class="form-control" style="width: 100%;" id="serialPortDataBits" name="serialPortDataBits" required>
                <option value="8">8</option>
                <option value="7">7</option>
                <option value="6">6</option>
                <option value="5">5</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Parity</label>
              <select class="form-control" style="width: 100%;" id="serialPortParity" name="serialPortParity" required>
                <option value="N">None</option>
                <option value="O">Odd</option>
                <option value="E">Even</option>
                <option value="M">Mark</option>
                <option value="S">Space</option>
              </select>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Stop bits</label>
              <select class="form-control" style="width: 100%;" id="serialPortStopBits" name="serialPortStopBits" required>
                <option value="1">1</option>
                <option value="1.5">1.5</option>
                <option value="2">2</option>
              </select>
            </div>
          </div>
          <!--div class="col-4">
            <div class="form-group">
              <label>Flow control</label>
              <select class="form-control" style="width: 100%;" id="serialPortFlowControl" name="serialPortFlowControl" required>
                <option value="None">None</option>
                <option value="XOnXOff">XOnXOff</option>
                <option value="RequestToSend">RTS (Request to send)</option>
                <option value="RequestToSendXOnXOff">RTS XOnXOff</option>
              </select>
            </div>
          </div-->
        </div>
      </div>
      <div class="modal-footer justify-content-between bg-gray-dark color-palette">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>      

<script>
// Objects
var _serialComm = null;
/*var _dataToSend = '';
var _dataReceived = '';*/

// Values
var serialComm = "COM5";
var baurate = 9600;
var parity = "N";
var stopbits = "1";
var databits = "8";
var controlflow = "None";
var indicatorUnit = "kg";
var weightUnit = "1";

$(function () {
  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': false,
    'ajax': {
        'url':'php/loadWeights.php'
    },
    'columns': [
      { data: 'serialNo' },
      { data: 'product_name' },
      { data: 'unit' },
      { data: 'unitWeight' },
      { data: 'tare' },
      { data: 'totalWeight' },
      { data: 'actualWeight' },
      { data: 'moq' },
      { data: 'unitPrice' },
      { data: 'totalPrice' },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
    "footerCallback": function ( row, data, start, end, display ) {
      var api = this.api();

      // Remove the formatting to get integer data for summation
      var intVal = function (i) {
        return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      };

      // Total over all pages
      total = api.column(3).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total2 = api.column(4).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total3 = api.column(5).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total4 = api.column(6).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total5 = api.column(7).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total6 = api.column(8).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      total7 = api.column(9).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

      // Total over this page
      pageTotal = api.column(3, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal2 = api.column(4, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal3 = api.column(5, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal4 = api.column(6, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal5 = api.column(7, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal6 = api.column(8, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      pageTotal7 = api.column(9, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );

      // Update footer
      $(api.column(3).footer()).html(pageTotal +' kg ( '+ total +' kg)');
      $(api.column(4).footer()).html(pageTotal2 +' kg ( '+ total2 +' kg)');
      $(api.column(5).footer()).html(pageTotal3 +' kg ( '+ total3 +' kg)');
      $(api.column(6).footer()).html(pageTotal4 +' kg ( '+ total4 +' kg)');
      $(api.column(7).footer()).html(pageTotal5 +' ('+ total5 +')');
      $(api.column(8).footer()).html('RM'+pageTotal6 +' ( RM'+ total6 +' total)');
      $(api.column(9).footer()).html('RM'+pageTotal7 +' ( RM'+ total7 +' total)');
    }
  });

  // Add event listener for opening and closing details
  $('#weightTable tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      // Open this row
      <?php 
        if($role == "ADMIN"){
          echo 'row.child( format(row.data()) ).show();tr.addClass("shown");';
        }
        else{
          echo 'row.child( formatNormal(row.data()) ).show();tr.addClass("shown");';
        }
      ?>
    }
  });
  
  //Date picker
  $('#fromDate').datetimepicker({
    format: 'D/MM/YYYY h:m:s A'
  }),
  $('#toDate').datetimepicker({
    format: 'D/MM/YYYY h:m:s A'
  });

  //JSPrintManager WebSocket settings
  /*JSPM.JSPrintManager.auto_reconnect = true;
  JSPM.JSPrintManager.start();
  JSPM.JSPrintManager.WS.onStatusChanged = function () {
    if (jspmWSStatus()) {
      //get serial ports
      JSPM.JSPrintManager.getSerialPorts().then(function (portsList) {
        var options = '';

        for (var i = 0; i < portsList.length; i++) {
          options += '<option value="' + portsList[i] + '">' + portsList[i] + '</option>';
        }

        $('#serialPort').html(options);
      });
    }
  };*/
  
  $.post('http://127.0.0.1:5002/getcomport', function(data){
    var decoded = JSON.parse(data);
    var options = '';

    for (var i = 0; i < decoded.length; i++) {
      options += '<option value="' + decoded[i] + '">' + decoded[i] + '</option>';
    }

    $('#serialPort').html(options);
  });
  
  setInterval(function () {
      $.post('http://127.0.0.1:5002/handshaking', function(data){
          if(data != "Error"){
            console.log("Data Received:" + data);
            var text = data.split(" ");
            $('#indicatorWeight').html(text[text.length - 1]);
            $('#indicatorConnected').addClass('bg-primary');
            $('#checkingConnection').removeClass('bg-danger');
          }
          else{
            $('#indicatorConnected').removeClass('bg-primary');
            $('#checkingConnection').addClass('bg-danger');
          }
      });
  }, 500);

  $.validator.setDefaults({
    submitHandler: function () {
        if($('#extendModal').hasClass('show')){
          $.post('php/insertWeight.php', $('#extendForm').serialize(), function(data){
            var obj = JSON.parse(data); 
            if(obj.status === 'success'){
              $('#extendModal').modal('hide');
              toastr["success"](obj.message, "Success:");
              $('#weightTable').DataTable().ajax.reload();
            }
          
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            alert("Something wrong when edit");
          }
        });
      }
      else if ($('#setupModal').hasClass('show')){
        /*serialComm = $('#serialPort').val();
        baurate = parseInt($('#serialPortBaudRate').val());
        parity = $('#serialPortParity').val();
        stopbits = $('#serialPortStopBits').val();
        databits = $('#serialPortDataBits').val();*/
        //controlflow = $('#serialPortFlowControl').val();
        //doOpen();
        
        
        $.post('http://127.0.0.1:5002/', $('#setupForm').serialize(), function(data){
            if(data == "true"){
                $('#indicatorConnected').addClass('bg-primary');
                $('#checkingConnection').removeClass('bg-danger');
                $('#captureWeight').removeAttr('disabled');
            }
            else{
                $('#indicatorConnected').removeClass('bg-primary');
                $('#checkingConnection').addClass('bg-danger');
                $('#captureWeight').attr('disabled', true);
            }
        })
        
        $('#setupModal').modal('hide');
      }
    }
  });

  $('#customerNoHidden').hide();
  $('#supplierNoHidden').hide();
  
  <?php 
    if($role == "ADMIN"){
      echo "$('#manual').on('click', function(){
        if($(this).is(':checked')){
            $('#currentWeight').removeAttr('readonly');
        }
        else{
            $('#currentWeight').attr('readonly', 'readonly');
        }
      })";
    }
  ?>
  
  $('#currentWeight').on('keyup', function(){
    var tareWeight =  $('#tareWeight').val();
    var currentWeight =  $('#currentWeight').val();
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if(tareWeight != ''){
      actualWeight = currentWeight - tareWeight;
      $('#actualWeight').val(actualWeight.toFixed(2));
    }
    else{
      $('#actualWeight').val((0).toFixed(2))
    }

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )

    $('#unitPrice').trigger("keyup");
  });

  $('#filterSearch').on('click', function(){
    var fromDateValue = $('#fromDateValue').val() ? $('#fromDateValue').val() : '';
    var toDateValue = $('#toDateValue').val() ? $('#toDateValue').val() : '';
    var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var vehicleFilter = $('#vehicleFilter').val() ? $('#vehicleFilter').val() : '';
    var invoiceFilter = $('#invoiceFilter').val() ? $('#invoiceFilter').val() : '';
    var batchFilter = $('#batchFilter').val() ? $('#batchFilter').val() : '';
    var productFilter = $('#productFilter').val() ? $('#productFilter').val() : '';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching': false,
      'ajax': {
        'type': 'POST',
        'url':'php/filterWeight.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          status: statusFilter,
          customer: customerNoFilter,
          vehicle: vehicleFilter,
          invoice: invoiceFilter,
          batch: batchFilter,
          product: productFilter,
        } 
      },
      'columns': [
        { data: 'serialNo' },
        { data: 'product_name' },
        { data: 'unit' },
        { data: 'unitWeight' },
        { data: 'tare' },
        { data: 'totalWeight' },
        { data: 'actualWeight' },
        { data: 'moq' },
        { data: 'unitPrice' },
        { data: 'totalPrice' },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ],
      "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api();

        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
          return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
        };

        // Total over all pages
        total = api.column(3).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total2 = api.column(4).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total3 = api.column(5).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total4 = api.column(6).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total5 = api.column(7).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total6 = api.column(8).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
        total7 = api.column(9).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

        // Total over this page
        pageTotal = api.column(3, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal2 = api.column(4, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal3 = api.column(5, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal4 = api.column(6, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal5 = api.column(7, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal6 = api.column(8, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
        pageTotal7 = api.column(9, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );

        // Update footer
        $(api.column(3).footer()).html(pageTotal +' kg ( '+ total +' kg)');
        $(api.column(4).footer()).html(pageTotal2 +' kg ( '+ total2 +' kg)');
        $(api.column(5).footer()).html(pageTotal3 +' kg ( '+ total3 +' kg)');
        $(api.column(6).footer()).html(pageTotal4 +' kg ( '+ total4 +' kg)');
        $(api.column(7).footer()).html(pageTotal5 +' ('+ total5 +')');
        $(api.column(8).footer()).html('RM'+pageTotal6 +' ( RM'+ total6 +' total)');
        $(api.column(9).footer()).html('RM'+pageTotal7 +' ( RM'+ total7 +' total)');
      }
    });
  });

  $('#excelSearch').on('click', function(){
    var fromDateValue = $('#fromDateValue').val() ? $('#fromDateValue').val() : '';
    var toDateValue = $('#toDateValue').val() ? $('#toDateValue').val() : '';
    var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var vehicleFilter = $('#vehicleFilter').val() ? $('#vehicleFilter').val() : '';
    var invoiceFilter = $('#invoiceFilter').val() ? $('#invoiceFilter').val() : '';
    var batchFilter = $('#batchFilter').val() ? $('#batchFilter').val() : '';
    var productFilter = $('#productFilter').val() ? $('#productFilter').val() : '';
    
    window.open("php/export.php?file=weight&fromDate="+fromDateValue+"&toDate="+toDateValue+
    "&status="+statusFilter+"&customer="+customerNoFilter+"&vehicle="+vehicleFilter+
    "&invoice="+invoiceFilter+"&batch="+batchFilter+"&product="+productFilter);

  });

  $('#statusFilter').on('change', function () {
    if($(this).val() == '1'){
      $('#customerNoFilter').html($('select#customerNoHidden').html()).append($(this).val());
    }
    else if($(this).val() == '2'){
      $('#customerNoFilter').html($('select#supplierNoHidden').html()).append($(this).val());
    }
  });

  $('#extendModal').find('#status').on('change', function () {
    if($(this).val() == '1'){
      $('#extendModal').find('#customerNo').html($('select#customerNoHidden').html()).append($(this).val());
      $('#extendModal').find('.labelStatus').text('Customer No');
      
    }
    else if($(this).val() == '2'){
      $('#extendModal').find('#customerNo').html($('select#supplierNoHidden').html()).append($(this).val());
      $('#extendModal').find('.labelStatus').text('Supplier No');

    }
  });

  $('#extendModal').find('#product').on('change', function () {
    var id = $(this).val();

    $.post('php/getProduct.php', {userID: id}, function(data){
      var obj = JSON.parse(data);
        
      if(obj.status === 'success'){
        $('#extendModal').find('#unitPrice').val(obj.message.product_price);
        $('#extendModal').find('#moq').trigger("keyup");
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
    });
  });

  $('#unitWeight').on('change', function () {
    var unitWeight = $(this).val();

    if(unitWeight == 1){
      weightUnit = "1";
      $('#changeWeight').text("KG");
      $('#changeWeightTare').text("KG");
      $('#changeWeightActual').text("KG");
      $('#changeWeightTotal').text("KG");
      
      //if(indicatorUnit == "g"){
          if($('#currentWeight').val()){
              $('#currentWeight').val(parseFloat(parseFloat($('#currentWeight').val()) / 1000).toFixed(2));
          }
          
          if($('#tareWeight').val()){
              $('#tareWeight').val(parseFloat(parseFloat($('#tareWeight').val()) / 1000).toFixed(2));
          }
          
          if($('#actualWeight').val()){
              $('#actualWeight').val(parseFloat(parseFloat($('#actualWeight').val()) / 1000).toFixed(2));
          }
          
          if($('#totalWeight').val()){
              $('#totalWeight').val(parseFloat(parseFloat($('#totalWeight').val()) / 1000).toFixed(2));
          }
      //}
    }
    else if(unitWeight == 2){
      weightUnit = "2";
      $('#changeWeight').text("G");
      $('#changeWeightTare').text("G");
      $('#changeWeightActual').text("G");
      $('#changeWeightTotal').text("G");
      
      //if(indicatorUnit == "kg"){
          if($('#currentWeight').val()){
              $('#currentWeight').val(parseFloat(parseFloat($('#currentWeight').val()) * 1000).toFixed(2));
          }
          
          if($('#tareWeight').val()){
              $('#tareWeight').val(parseFloat(parseFloat($('#tareWeight').val()) * 1000).toFixed(2));
          }
          
          if($('#actualWeight').val()){
              $('#actualWeight').val(parseFloat(parseFloat($('#actualWeight').val()) * 1000).toFixed(2));
          }
          
          if($('#totalWeight').val()){
              $('#totalWeight').val(parseFloat(parseFloat($('#totalWeight').val()) * 1000).toFixed(2));
          }
      //}
    }
    else if(unitWeight == 3){
      weightUnit = "3";
      $('#changeWeight').text("LB");
      $('#changeWeightTare').text("LB");
      $('#changeWeightActual').text("LB");
      $('#changeWeightTotal').text("LB");
    }
  });

  $('#captureWeight').on('click', function () {
    var text = $('#indicatorWeight').text();
    
    if(text[text.length-2] == 'k'){
        if(weightUnit == "2"){
            $('#currentWeight').val(parseFloat(parseFloat(text.substring(0, text.length-2)) * 1000).toFixed(2));
        }
        else{
            $('#currentWeight').val(text.substring(0, text.length-2));
        }
        
        indicatorUnit = "kg";
    }
    else{
        if(weightUnit == "1"){
            $('#currentWeight').val(parseFloat(parseFloat(text.substring(0, text.length-1)) / 1000).toFixed(2));
        }
        else{
            $('#currentWeight').val(text.substring(0, text.length-1)); 
        }
         
        indicatorUnit = "g";
    }
    
    var tareWeight =  $('#tareWeight').val();
    var currentWeight =  $('#currentWeight').val();
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if(tareWeight != ''){
      actualWeight = currentWeight - tareWeight;
      $('#actualWeight').val(actualWeight.toFixed(2));
    }
    else{
      $('#actualWeight').val((0).toFixed(2))
    }

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )

    $('#unitPrice').trigger("keyup");
  });

  $('#tareWeight').on('keyup', function () {
    var currentWeight =  $('#currentWeight').val();
    var actualWeight;
    var moq = $('#moq').val();
    var totalWeight;

    if(currentWeight != '' && $(this).val() != ''){
      var actualWeight = currentWeight - $(this).val();
      $('#actualWeight').val(actualWeight.toFixed(2));
    }
    else{
      $('#actualWeight').val((0).toFixed(2))
    }

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else{
      $('#totalWeight').val((0).toFixed(2))
    }

    $(unitPrice).trigger("keyup");
  });

  $('#moq').on('keyup', function () {
    var actualWeight = $("#actualWeight").val();
    var moq = $(this).val();
    var totalWeight;

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )

    $(unitPrice).trigger("keyup");
  });

  $('#unitPrice').on('keyup', function () {
    var totalPrice;
    var unitPrice = $(this).val();
    var totalWeight = $('#totalWeight').val();

    if(unitPrice != '' &&  totalWeight != ''){
      totalPrice = unitPrice * totalWeight;
      $('#totalPrice').val(totalPrice.toFixed(2));
    }
    else(
      $('#totalPrice').val((0).toFixed(2))
    )
  });

});

function format (row) {
  return '<div class="row"><div class="col-md-3"><p>Vehicle No.: '+row.veh_number+
  '</p></div><div class="col-md-3"><p>Lot No.: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Batch No.: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Invoice No.: '+row.invoiceNo+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Delivery No.: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Purchase No.: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><p>Customer: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Date: '+row.dateTime+
  '</p></div><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>'+row.manual+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div></div></div>';
}

function formatNormal (row) {
  return '<div class="row"><div class="col-md-3"><p>Vehicle No.: '+row.veh_number+
  '</p></div><div class="col-md-3"><p>Lot No.: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Batch No.: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Invoice No.: '+row.invoiceNo+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Delivery No.: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Purchase No.: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><p>Customer: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Date: '+row.dateTime+
  '</p></div><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print("'+row.id+
  '")"><i class="fas fa-print"></i></button></div></div></div></div>';
}

function newEntry(){
  let dateTime = new Date();
  $('#extendModal').find('#serialNumber').val("");
  $('#extendModal').find('#unitWeight').val('');
  $('#extendModal').find('#invoiceNo').val("");
  $('#extendModal').find('#status').val('');
  $('#extendModal').find('#lotNo').val('');
  $('#extendModal').find('#vehicleNo').val('');
  $('#extendModal').find('#customerNo').val('');
  $('#extendModal').find('#deliveryNo').val("");
  $('#extendModal').find('#batchNo').val("");
  $('#extendModal').find('#purchaseNo').val("");
  $('#extendModal').find('#currentWeight').val("");
  $('#extendModal').find('#product').val('');
  $('#extendModal').find('#moq').val("1");
  $('#extendModal').find('#tareWeight').val("0.00");
  $('#extendModal').find('#package').val('');
  $('#extendModal').find('#actualWeight').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#totalPrice').val("");
  $('#extendModal').find('#unitPrice').val("");
  $('#extendModal').find('#totalWeight').val("");
  $('#extendModal').find('#manual').prop('checked', false);
  $('#dateTime').datetimepicker({
    format: 'D/MM/YYYY h:m:s A'
  });
  $('#extendModal').find('#dateTime').val(dateTime.toLocaleString("en-US"));
  $('#extendModal').modal('show');
  
  $('#extendForm').validate({
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
}

function setup(){
  $('#setupModal').find('#serialPortBaudRate').val('9600');
  $('#setupModal').find('#serialPortDataBits').val("8");
  $('#setupModal').find('#serialPortParity').val('N');
  $('#setupModal').find('#serialPortStopBits').val('1');
  $('#setupModal').find('#serialPortFlowControl').val('None');
  $('#setupModal').modal('show');

  $('#setupForm').validate({
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
}

function edit(id) {
  $.post('php/getWeights.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#serialNumber').val(obj.message.serialNo);
      $('#extendModal').find('#unitWeight').val(obj.message.unit);
      $('#extendModal').find('#invoiceNo').val(obj.message.invoiceNo);
      $('#extendModal').find('#status').val(obj.message.status);
      $('#extendModal').find('#lotNo').val(obj.message.lotNo);	
      $('#extendModal').find('#vehicleNo').val(obj.message.vehicleNo);
      $('#extendModal').find('#customerNo').val(obj.message.customer);
      $('#extendModal').find('#deliveryNo').val(obj.message.deliveryNo);
      $('#extendModal').find('#batchNo').val(obj.message.batchNo);
      $('#extendModal').find('#purchaseNo').val(obj.message.purchaseNo);
      $('#extendModal').find('#currentWeight').val(obj.message.unitWeight);
      $('#extendModal').find('#product').val(obj.message.productName);
      $('#extendModal').find('#moq').val(obj.message.moq);
      $('#extendModal').find('#tareWeight').val(obj.message.tare);
      $('#extendModal').find('#package').val(obj.message.package);
      $('#extendModal').find('#actualWeight').val(obj.message.actualWeight);
      $('#extendModal').find('#remark').val(obj.message.remark);
      $('#extendModal').find('#totalPrice').val(obj.message.totalPrice);
      $('#extendModal').find('#unitPrice').val(obj.message.unitPrice);
      $('#extendModal').find('#totalWeight').val(obj.message.totalWeight);
      $('#dateTime').datetimepicker({
        format: 'D/MM/YYYY h:m:s A'
      });
      $('#extendModal').find('#dateTime').val(obj.message.dateTime.toLocaleString("en-US"));
      $('#extendModal').modal('show');
      
      $('#lotForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
  });
}

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $.post('php/deleteWeight.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
        /*$.get('weightPage.php', function(data) {
          $('#mainContents').html(data);
        });*/
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
    });
  }
}

function print(id) {
  $.post('php/print.php', {userID: id, file: 'weight'}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      var printWindow = window.open('', '', 'height=400,width=800');
      printWindow.document.write(obj.message);
      printWindow.document.close();
      printWindow.print();

      /*$.get('weightPage.php', function(data) {
        $('#mainContents').html(data);
      });*/
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
  });
}

//Check JSPM WebSocket status
/*function jspmWSStatus() {
  if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
    return true;
  else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
    console.warn('JSPrintManager (JSPM) is not installed or not running!');
    return false;
  }
  else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
    alert('JSPM has blocked this website!');
    return false;
  }
}

//Sending data to port
function doSendData() {
  if (!this._serialComm) {
    this._dataReceived += "Serial port is not open!\r\n";
    this.refreshDisplay();
  } 
  else {
    this._dataToSend = $('#txtDataToSend').val();
    if (this._dataToSend.length > 0) {
      this._serialComm.send(this._dataToSend + $('#endLineChars').val().replace('CR', '\r').replace('LF', '\n'));
      this._dataReceived += "> " + this._dataToSend + "\r\n";
      this.refreshDisplay();
    }
  }
}

//Open port
function doOpen() {
  this._serialComm = new JSPM.SerialComm(serialComm, baurate, JSPM.Serial.Parity[parity], JSPM.Serial.StopBits[stopbits], JSPM.Serial.DataBits[databits], JSPM.Serial.Handshake[controlflow]);

  this._serialComm.onDataReceived = function (data) {
    //_this.dataReceived += "< " + data + "\r\n";
    //console.log("Data Received:" + data);
    $('#indicatorConnected').addClass('bg-primary');
    $('#checkingConnection').removeClass('bg-danger');
    _this.refreshDisplay(data);
  };

  this._serialComm.onError = function (data, is_critical) {
    //_this.dataReceived += "ERROR: " + data + "\r\n";
    console.log("Error: " + data);
    $('#indicatorConnected').removeClass('bg-primary');
    $('#checkingConnection').addClass('bg-danger');
    //_this.refreshDisplay();
  };

  this._serialComm.onClose = function (data) {
    //_this.dataReceived += "COMM CLOSED!" + "\r\n";
    console.log("Closed: " + data);
    $('#indicatorConnected').removeClass('bg-primary');
    $('#checkingConnection').addClass('bg-danger');
    //_this.refreshDisplay();
  };

  this._serialComm.open().then(_ => {
      debugger;
      console.log("open connection");
    //_this.dataReceived += "COMM OPEN!" + "\r\n";
    //_this.refreshDisplay();
  });
}

//close port
function doClose() {
  if (!this._serialComm && this._serialComm.isOpen !== true) {
    this._dataReceived += "Serial port is not open!\r\n";
    this.refreshDisplay();
  } 
  else{
    this._serialComm.close();
  }
}

function refreshDisplay(data) {
  console.log("Data Received:" + data);
  var text = data.split(" ");
  $('#indicatorWeight').html(text[text.length - 1] +' <sup style="font-size: 20px" id="indicatorUnits">KG</sup>');
}*/

</script>