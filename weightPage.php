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
  $port = 'COM5';
  $baudrate = 9600;
  $databits = "8";
  $parity = "N";
  $stopbits = '1';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
    $port = $row['port'];
    $baudrate = $row['baudrate'];
    $databits = $row['databits'];
    $parity = $row['parity'];
    $stopbits = $row['stopbits'];
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
  $transporters = $db->query("SELECT * FROM `transporters` WHERE deleted = '0'");
}
?>

<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
          max-width: 1800px; /* New width for default modal */
        }
    }

/* .grid-container {
  display: grid;
  grid-template-columns: auto auto auto auto;
  gap: 10px;
  background-color: #2196F3;
  padding: 10px;
}

.grid-container > div {
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid black;
  text-align: center;
  font-size: 30px;
} */

    /* div.dataTables_wrapper {
      position : relative;
    }

    div.b {
      position: absolute;
      bottom: 700px;
      right: 148px;
      width: 100px;
      height: 40px;
      border: 2px solid black;
      text-align: center;
      font-size: 18px;
      background-color: #2196F3;
      color: white;
    } 

    div.c {
      position: absolute;
      bottom: 700px;
      right: 50px;
      width: 100px;
      height: 40px;
      border: 2px solid black;
      text-align: center;
      font-size: 18px;
      background-color: #2196F3;
      color: white;
    }
    div.d {
      position: absolute;
      margin-top: 38px;
      right: 148px;
      width: 100px;
      height: 45px;
      border: 2px solid black;
      text-align: center;
      font-size: 15px;
      padding-top: 10px;
      background-color: #eee;
    } 

    div.e {
      position: absolute;
      margin-top: 38px;
      right: 50px;
      width: 100px;
      height: 45px;
      border: 2px solid black;
      text-align: center;
      font-size: 15px;
      padding-top: 10px;
      background-color: #eee;
    }  */

</style>

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
    <div div class="row">
      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fas fa-shopping-cart"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Sales</span>
            <span class="info-box-number" id="salesInfo">0</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-success">
            <i class="fas fa-shopping-basket"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Purchase</span>
            <span class="info-box-number" id="purchaseInfo">0</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
          <span class="info-box-icon bg-warning">
            <i class="fas fa-warehouse" style="color: white;"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">Local</span>
            <span class="info-box-number" id="localInfo">0</span>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6 col-12">
        <div class="input-group-text color-palette" id="indicatorConnected"><i>Indicator Connected</i></div>
        <div class="input-group-text bg-danger color-palette" id="checkingConnection"><i>Checking Connection</i></div>
      </div>
    </div>

    <div class="row">

      <!-- <div class="col-lg-12">
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
      </div> -->

      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-9"></div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  onclick="newEntry()">Add New Weight</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Status</th>
                  <th>Weight Status</th>
                  <th>Serial No</th>
                  <th>Vehicle No</th>
                  <th>Product Description Detail</th>
                  <th>Incoming (Gross Weight)</th>
                  <th>Incoming (Gross) Date Time</th>
                  <th>Outgoing (Tare) Weight</th>
                  <th>Outgoing (Tare) Date Time</th>
                  <th>Total Nett Weight</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
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

        <div class="modal-body" >
        
          <div class="row">
            <div class="col-md-3">
              <div class="d-flex">
                <div class="small-box bg-success">
                  <div class="inner">
                  <h3 style="text-align: center; font-size: 100px" id="indicatorWeight">0.00kg</h3>
                  </div>
                </div>
              </div>      
            </div>
            
            <div class="row col-md-9">
              <div class="row col-md-12">
                    <div class="col-2">
                      <input type="hidden" class="form-control" id="id" name="id">
                      <div class="form-group">
                        <label>Serial No.</label>
                        <input class="form-control" type="text" placeholder="Serial No" id="serialNumber" name="serialNumber" readonly>
                      </div>
                    </div>

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

                    <div class="form-group col-md-2">
                      <label>Invoice No</label>
                      <input class="form-control" type="text" placeholder="Invoice No" id="invoiceNo" name="invoiceNo" >
                    </div>

                    <div class="form-group col-md-2">
                      <label>Delivery No</label>
                      <input class="form-control" type="text" placeholder="Delivery No" id="deliveryNo" name="deliveryNo" >
                    </div>

                    <div class="form-group col-md-2">
                      <label>Purchase Order</label>
                      <input class="form-control" type="text" placeholder="Purchase No" id="purchaseNo" name="purchaseNo" >
                    </div>

                    <div class="form-group col-md-2">
                      <label class="labelOrder">Order Weight</label>
                      <div class="input-group">
                        <input class="form-control" type="number" id="supplyWeight" name="supplyWeight"/>
                        <div class="input-group-text bg-success color-palette"><i id="changeSupplyWeight">KG/G</i></div>
                      </div>
                    </div>

              </div>

              <div class="row col-md-12">

              <div class="col-2">
                <div class="form-group">
                  <label>Date / Time</label>
                  <div class="input-group date" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#dateTime" id="dateTime" name="dateTime" required/>
                    <div class="input-group-append" data-target="#dateTime" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-2">
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

                <div class="col-md-2">
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

                <div class="form-group col-md-2">
                  <label>Batch No</label>
                  <input class="form-control" type="text" placeholder="Batch No" id="batchNo" name="batchNo" >
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

                <div class="form-group col-md-2">
                    <label style="color:red;">Variance Weight</label>
                    <div class="input-group">
                      <input class="form-control" type="text" placeholder="Variance Weight" id="varianceWeight" name="varianceWeight" readonly/>
                      <div class="input-group-text bg-success color-palette"><i id="changeWeightVariance">KG/G</i></div>
                    </div>
                </div>

              </div>
            </div>
          </div>

          <div class="row">
              
                <div class="col-md-4">
                    <div class="form-group">
                      <label class="labelStatus">Customer No *</label>
                      <select class="form-control" id="customerNo" name="customerNo" required></select>
                    </div>
                </div>

                <div class="row col-md-8">
                  <div class="row col-md-12">


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

                <div class="form-group" hidden>
                  <label>M.O.Q *</label>
                  <input class="form-control" type="number" placeholder="moq" id="moq" name="moq" min="0">
                </div>

                <div class="form-group col-md-4">
                  <label>Transporter</label>
                  <select class="form-control" style="width: 100%;" id="transporter" name="transporter" required>
                      <option selected="selected">-</option>
                      <?php while($row5=mysqli_fetch_assoc($transporters)){ ?>
                        <option value="<?=$row5['id'] ?>"><?=$row5['transporter_name'] ?></option>
                      <?php } ?>
                  </select>
                </div>

                <div class="form-group col-md-2">
                    <label>Unit Price</label>
                    <div class="input-group">
                      <div class="input-group-text"><i>RM</i></div>
                      <input class="form-control money" type="number" placeholder="unitPrice" id="unitPrice" name="unitPrice" min="0" required/>                        
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <label>Total Price</label>
                    <div class="input-group">
                      <div class="input-group-text"><i>RM</i></div>
                      <input class="form-control money" type="number" placeholder="Total Price"  id="totalPrice" name="totalPrice" readonly required/>                        
                    </div>
                </div>
              </div>
            </div>

          </div>

          <div class="row">
              <div class="col-md-2">
                  <div class="form-group">
                    <label>Vehicle No
                        <span style="padding-left: 80px;"><input type="checkbox" class="form-check-input" id="manualVehicle" name="manualVehicle" value="0"/>Manual</span>
                    </label>

                    <select class="form-control" id="vehicleNo" name="vehicleNo">
                      <option selected="selected">-</option>
                      <?php while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                        <option value="<?=$row2['veh_number'] ?>" data-weight="<?=$row2['vehicleWeight'] ?>"><?=$row2['veh_number'] ?></option>
                      <?php } ?>
                    </select>

                    <input class="form-control" type="text" placeholder="Vehicle No." id="vehicleNoTct" name="vehicleNoTxt" hidden>
                  </div>
              </div>

              <div class="form-group col-md-2">
                <label>Incoming - G.W *
                <?php 
                  if($role == "ADMIN"){         
                    echo '<span style="padding-left: 80px;"><input type="checkbox" class="form-check-input" id="manual" name="manual" value="0"/>Manual</span>';
                  }
                ?>
                </label>
                <div class="input-group">
                  <input class="form-control" type="number" placeholder="Current Weight" id="currentWeight" name="currentWeight" readonly required/>
                  <div class="input-group-text bg-primary color-palette"><i id="changeWeight">KG/G</i></div>
                  <button type="button" class="btn btn-primary" id="inCButton"><i class="fas fa-sync"></i></button>
                </div>
              </div>              

              <input type="hidden" id="outGDateTime" name="outGDateTime">
              <input type="hidden" id="inCDateTime" name="inCDateTime">

              <div class="form-group col-md-2 hidOutgoing">
                <label>Outgoing - T.W *
                  <span style="padding-left: 80px;"><input type="checkbox" class="form-check-input" id="manualOutgoing" name="manualOutgoing" value="0"/>Manual</span>
                </label>
                <div class="input-group">
                  <input class="form-control" type="number" placeholder="Tare Weight" id="tareWeight" name="tareWeight" min="0" readonly/>
                  <div class="input-group-text bg-primary color-palette"><i id="changeWeightTare">KG/G</i></div>
                  <button type="button" class="btn btn-primary" id="outGButton"><i class="fas fa-sync"></i></button>
                </div>
              </div>
              
            <div class="row col-md-6">
              <div class="row col-md-12">

                <div class="form-group col-md-3">
                  <label>Reduce Weight</label>
                  <div class="input-group">
                    <input class="form-control" type="number" placeholder="Reduce Weight" id="reduceWeight" name="reduceWeight" min="0"/>
                    <div class="input-group-text bg-danger color-palette"><i id="changeReduceWeight">KG/G</i></div>
                  </div>
                </div>

                <div class="form-group col-md-3">
                  <label>Sub Nett Weight</label>
                  <div class="input-group">
                    <input class="form-control" type="number" placeholder="Actual Weight" id="actualWeight" name="actualWeight" readonly required/>
                    <div class="input-group-text bg-success color-palette"><i id="changeWeightActual">KG/G</i></div>
                  </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                      <label>Remark</label>
                      <textarea class="form-control" rows="1" placeholder="Enter ..." id="remark" name="remark"></textarea>
                    </div>
                </div>

              </div>
            </div>
        </div>

        <div class="form-group col-md-3" hidden>
            <label>Total Weight</label>
            <div class="input-group">
              <input class="form-control" type="number" placeholder="Total Weight" id="totalWeight" name="totalWeight" readonly required/>
              <div class="input-group-text bg-success color-palette"><i id="changeWeightTotal">KG/G</i></div>
            </div>
        </div>

        <input type="hidden" id="pStatus" name="pStatus">
        <input type="hidden" id="variancePerc" name="variancePerc">

        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
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
              <input class="form-control" type="text" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$port ?>">
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Baud Rate</label>
              <input class="form-control" type="number" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$baudrate ?>">
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Data Bits</label>
              <input class="form-control" type="text" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$databits ?>">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label>Parity</label>
              <input class="form-control" type="text" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$parity ?>">
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label>Stop bits</label>
              <input class="form-control" type="text" id="serialPortBaudRate" name="serialPortBaudRate" value="<?=$stopbits ?>">
            </div>
          </div>
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
// Values
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
    'searching': true,
    'order': [[ 1, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
        'url':'php/loadWeights.php'
    },
    'columns': [
      { data: 'no' },
      { data: 'pStatus' },
      { data: 'status' },
      { data: 'serialNo' },
      { data: 'veh_number' },
      { data: 'product_name' },
      { data: 'currentWeight' },
      { data: 'inCDateTime' },
      { data: 'tare' },
      { data: 'outGDateTime' },
      { data: 'totalWeight' },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
    "rowCallback": function( row, data, index ) {
      $('td', row).css('background-color', '#E6E6FA');
    },
    "drawCallback": function(settings) {
      $('#salesInfo').text(settings.json.salesTotal);
      $('#purchaseInfo').text(settings.json.purchaseTotal);
      $('#localInfo').text(settings.json.localTotal);
    }
    // "footerCallback": function ( row, data, start, end, display ) {
    //   var api = this.api();

    //   // Remove the formatting to get integer data for summation
    //   var intVal = function (i) {
    //     return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
    //   };

    //   // Total over all pages
    //   total = api.column(3).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total2 = api.column(4).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total3 = api.column(5).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total4 = api.column(6).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total5 = api.column(7).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total6 = api.column(8).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
    //   total7 = api.column(9).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

    //   // Total over this page
    //   pageTotal = api.column(3, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal2 = api.column(4, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal3 = api.column(5, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal4 = api.column(6, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal5 = api.column(7, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal6 = api.column(8, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
    //   pageTotal7 = api.column(9, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );

    //   // Update footer
    //   $(api.column(3).footer()).html(pageTotal +' kg ( '+ total +' kg)');
    //   $(api.column(4).footer()).html(pageTotal2 +' kg ( '+ total2 +' kg)');
    //   $(api.column(5).footer()).html(pageTotal3 +' kg ( '+ total3 +' kg)');
    //   $(api.column(6).footer()).html(pageTotal4 +' kg ( '+ total4 +' kg)');
    //   $(api.column(7).footer()).html(pageTotal5 +' ('+ total5 +')');
    //   $(api.column(8).footer()).html('RM'+pageTotal6 +' ( RM'+ total6 +' total)');
    //   $(api.column(9).footer()).html('RM'+pageTotal7 +' ( RM'+ total7 +' total)');
    // }
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
  });

  $('#toDate').datetimepicker({
    format: 'D/MM/YYYY h:m:s A'
  });

  $.post('http://127.0.0.1:5002/', $('#setupForm').serialize(), function(data){
    if(data == "true"){
      $('#indicatorConnected').addClass('bg-primary');
      $('#checkingConnection').removeClass('bg-danger');
      //$('#captureWeight').removeAttr('disabled');
    }
    else{
      $('#indicatorConnected').removeClass('bg-primary');
      $('#checkingConnection').addClass('bg-danger');
      //$('#captureWeight').attr('disabled', true);
    }
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
      /*else if ($('#setupModal').hasClass('show')){
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
        });
        
        $('#setupModal').modal('hide');
      }*/
    }
  });

  $('#customerNoHidden').hide();
  $('#supplierNoHidden').hide();
  
  <?php 
    if($role == "ADMIN"){
      echo "$('#manual').on('click', function(){
        if($(this).is(':checked')){
          $(this).val(1);
            $('#currentWeight').removeAttr('readonly');
        }
        else{
          $(this).val(0);
            $('#currentWeight').attr('readonly', 'readonly');
        }
      })";
    }
  ?>

  //  $("input.money").each((i,ele)=>{
  //     let clone=$(ele).clone(false)
  //     clone.attr("type","text")
  //     let ele1=$(ele)
  //     clone.val(Number(ele1.val()).toLocaleString("en", { minimumFractionDigits: 2 }))
  //     $(ele).after(clone)
  //     $(ele).hide()
  //     clone.mouseenter(()=>{

  //       ele1.show()
  //       clone.hide()
  //     })
  //     setInterval(()=>{
  //       let newv=Number(ele1.val()).toLocaleString("en", { minimumFractionDigits: 2 })
  //       if(clone.val()!=newv){
  //         clone.val(newv)
  //       }
  //     },10)

  //     $(ele).mouseleave(()=>{
  //       $(clone).show()
  //       $(ele1).hide()
  //   })
  // })

  $('#inCButton').on('click', function(){
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
    var reduceWeight =  $('#reduceWeight').val();
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if(tareWeight != ''){
      actualWeight =  tareWeight - currentWeight - reduceWeight;
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

    $('#currentWeight').trigger("keyup");
    $('#variancePerc').trigger("keyup");
    $('#reduceWeight').trigger("keyup");
    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#outGButton').on('click', function(){
    var text = $('#indicatorWeight').text();
    
    if(text[text.length-2] == 'k'){
        if(weightUnit == "2"){
            $('#tareWeight').val(parseFloat(parseFloat(text.substring(0, text.length-2)) * 1000).toFixed(2));
        }
        else{
            $('#tareWeight').val(text.substring(0, text.length-2));
        }
        
        indicatorUnit = "kg";
    }
    else{
        if(weightUnit == "1"){
            $('#tareWeight').val(parseFloat(parseFloat(text.substring(0, text.length-1)) / 1000).toFixed(2));
        }
        else{
            $('#tareWeight').val(text.substring(0, text.length-1)); 
        }
         
        indicatorUnit = "g";
    }
    
    var tareWeight =  $('#tareWeight').val();
    var currentWeight =  $('#currentWeight').val();
    var reduceWeight =  $('#reduceWeight').val();
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if(tareWeight != ''){
      actualWeight =  tareWeight - currentWeight - reduceWeight;
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

    $('#variancePerc').trigger("keyup");
    $('#tareWeight').trigger("keyup");
    $('#reduceWeight').trigger("keyup");
    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#vehicleNo').on('change', function(){
    $vehicleWeight = $('#vehicleNo option:selected').data("weight");
    if($vehicleWeight != null && $vehicleWeight != ''){
      $('#currentWeight').val(($vehicleWeight).toFixed(2));
    }
    $('#currentWeight').trigger("keyup");
  });

  $('#manualVehicle').on('click', function(){
    if($(this).is(':checked')){
      $(this).val(1);
      $('#vehicleNoTct').removeAttr('hidden');
      $('#vehicleNo').attr('hidden', 'hidden');
    }
    else{
      $(this).val(0);
      $('#vehicleNo').removeAttr('hidden');
      $('#vehicleNoTct').attr('hidden', 'hidden');
    }
  });

  $('#manualOutgoing').on('click', function(){
    if($(this).is(':checked')){
      $(this).val(1);
        $('#tareWeight').removeAttr('readonly');
    }
    else{
      $(this).val(0);
        $('#tareWeight').attr('readonly', 'readonly');
    }
  });

  $('#currentWeight').on('keyup', function(){
    var tareWeight =  0;
    var currentWeight =  $('#currentWeight').val();
    var reduceWeight = 0;
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if($('#tareWeight').val()){
      tareWeight =  $('#tareWeight').val();
    }

    if($('#reduceWeight').val()){
      reduceWeight =  $('#reduceWeight').val();
    }

    actualWeight = tareWeight - currentWeight - reduceWeight;
    $('#actualWeight').val(actualWeight.toFixed(2));

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )
    
    var today  = new Date();
    $('#extendModal').find('#inCDateTime').val(today.toLocaleString("en-US"));

    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#tareWeight').on('keyup', function(){
    var tareWeight =  0;
    var currentWeight =  $('#currentWeight').val();
    var reduceWeight = 0;
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if($('#tareWeight').val()){
      tareWeight =  $('#tareWeight').val();
    }

    if($('#reduceWeight').val()){
      reduceWeight =  $('#reduceWeight').val();
    }

    actualWeight = tareWeight - currentWeight - reduceWeight;
    $('#actualWeight').val(actualWeight.toFixed(2));

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )

    var today  = new Date();
    $('#extendModal').find('#outGDateTime').val(today.toLocaleString("en-US"));

    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#reduceWeight').on('keyup', function(){
    var tareWeight =  0;
    var currentWeight =  $('#currentWeight').val();
    var reduceWeight = 0;
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if($('#tareWeight').val()){
      tareWeight =  $('#tareWeight').val();
    }

    if($('#reduceWeight').val()){
      reduceWeight =  $('#reduceWeight').val();
    }

    actualWeight = tareWeight - currentWeight - reduceWeight;
    $('#actualWeight').val(actualWeight.toFixed(2));

    if(actualWeight != '' &&  moq != ''){
      totalWeight = actualWeight * moq;
      $('#totalWeight').val(totalWeight.toFixed(2));
    }
    else(
      $('#totalWeight').val((0).toFixed(2))
    )

    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
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
      'searching': true,
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
      ]
      // "footerCallback": function ( row, data, start, end, display ) {
      //   var api = this.api();

      //   // Remove the formatting to get integer data for summation
      //   var intVal = function (i) {
      //     return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
      //   };

      //   // Total over all pages
      //   total = api.column(3).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total2 = api.column(4).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total3 = api.column(5).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total4 = api.column(6).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total5 = api.column(7).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total6 = api.column(8).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
      //   total7 = api.column(9).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

      //   // Total over this page
      //   pageTotal = api.column(3, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal2 = api.column(4, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal3 = api.column(5, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal4 = api.column(6, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal5 = api.column(7, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal6 = api.column(8, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );
      //   pageTotal7 = api.column(9, {page: 'current'}).data().reduce( function (a, b) {return intVal(a) + intVal(b);}, 0 );

      //   // Update footer
      //   $(api.column(3).footer()).html(pageTotal +' kg ( '+ total +' kg)');
      //   $(api.column(4).footer()).html(pageTotal2 +' kg ( '+ total2 +' kg)');
      //   $(api.column(5).footer()).html(pageTotal3 +' kg ( '+ total3 +' kg)');
      //   $(api.column(6).footer()).html(pageTotal4 +' kg ( '+ total4 +' kg)');
      //   $(api.column(7).footer()).html(pageTotal5 +' ('+ total5 +')');
      //   $(api.column(8).footer()).html('RM'+pageTotal6 +' ( RM'+ total6 +' total)');
      //   $(api.column(9).footer()).html('RM'+pageTotal7 +' ( RM'+ total7 +' total)');
      // }
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
      $('#extendModal').find('.labelOrder').text('Order Weight');
    }
    else if($(this).val() == '2'){
      $('#extendModal').find('#customerNo').html($('select#supplierNoHidden').html()).append($(this).val());
      $('#extendModal').find('.labelOrder').text('Supply Weight');

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
      $('#changeSupplyWeight').text("KG");
      $('#changeWeightVariance').text("KG");
      $('#changeReduceWeight').text("KG");

      if(indicatorUnit == "g"){
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

        if($('#reduceWeight').val()){
          $('#reduceWeight').val(parseFloat(parseFloat($('#reduceWeight').val()) / 1000).toFixed(2));
        }
      }
    }
    else if(unitWeight == 2){
      weightUnit = "2";
      $('#changeWeight').text("G");
      $('#changeWeightTare').text("G");
      $('#changeWeightActual').text("G");
      $('#changeWeightTotal').text("G");
      $('#changeSupplyWeight').text("G");
      $('#changeWeightVariance').text("G");
      $('#changeReduceWeight').text("G");
      
      if(indicatorUnit == "kg"){
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

        if($('#reduceWeight').val()){
          $('#reduceWeight').val(parseFloat(parseFloat($('#reduceWeight').val()) * 1000).toFixed(2));
        }
      }
    }
    else if(unitWeight == 3){
      weightUnit = "3";
      $('#changeWeight').text("LB");
      $('#changeWeightTare').text("LB");
      $('#changeWeightActual').text("LB");
      $('#changeWeightTotal').text("LB");
      $('#changeSupplyWeight').text("LB");
      $('#changeWeightVariance').text("LB");
      $('#changeReduceWeight').text("LB");
    }
    else if(unitWeight == 6){
      weightUnit = "6";
      $('#changeWeight').text("mg");
      $('#changeWeightTare').text("mg");
      $('#changeWeightActual').text("mg");
      $('#changeWeightTotal').text("mg");
      $('#changeSupplyWeight').text("mg");
      $('#changeWeightVariance').text("mg");
      $('#changeReduceWeight').text("mg");
    }
    else if(unitWeight == 8){
      weightUnit = "8";
      $('#changeWeight').text("ct");
      $('#changeWeightTare').text("ct");
      $('#changeWeightActual').text("ct");
      $('#changeWeightTotal').text("ct");
      $('#changeSupplyWeight').text("ct");
      $('#changeWeightVariance').text("ct");
      $('#changeReduceWeight').text("ct");
    }
    else if(unitWeight == 9){
      weightUnit = "9";
      $('#changeWeight').text("Oz");
      $('#changeWeightTare').text("Oz");
      $('#changeWeightActual').text("Oz");
      $('#changeWeightTotal').text("Oz");
      $('#changeSupplyWeight').text("Oz");
      $('#changeWeightVariance').text("Oz");
      $('#changeReduceWeight').text("Oz");
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
    var reduceWeight =  $('#reduceWeight').val();
    var moq = $('#moq').val();
    var totalWeight;
    var actualWeight;

    if(tareWeight != ''){
      actualWeight =  tareWeight - currentWeight - reduceWeight;
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

    $('#variancePerc').trigger("keyup");
    $('#reduceWeight').trigger("keyup");
    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#tareWeight').on('keyup', function () {
    var currentWeight =  $('#currentWeight').val();
    var reduceWeight =  $('#reduceWeight').val();
    var actualWeight;
    var moq = $('#moq').val();
    var totalWeight;

    if(currentWeight != '' && $(this).val() != '' ){
      var actualWeight = $(this).val() - currentWeight - reduceWeight;
      $('#actualWeight').val((actualWeight.toFixed(2)));
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

    var today  = new Date();
    $('#extendModal').find('#outGDateTime').val(today.toLocaleString("en-US"));
    
    $('#variancePerc').trigger("keyup");
    $('#reduceWeight').trigger("keyup");
    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
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

    $('#unitPrice').trigger("keyup");
    $('#supplyWeight').trigger("keyup");
  });

  $('#unitPrice').on('keyup', function () {
    var totalPrice;
    var unitPrice = $(this).val();
    var moq = $('#moq').val();
    var actualWeight = $("#actualWeight").val();

    if(unitPrice != '' &&  moq != '' && actualWeight != ''){
      totalPrice = unitPrice * moq * actualWeight;
      $('#totalPrice').val(totalPrice.toFixed(2));
    }
    else(
      $('#totalPrice').val((0).toFixed(2))
    )
  });

  $('#supplyWeight').on('keyup', function () {
    var varianWeight = $('#totalWeight').val() - $(this).val();

    if(supplyWeight != '' && varianWeight != ''){
      $('#varianceWeight').val(varianWeight.toFixed(2));
    }
    else{
      $('#varianceWeight').val((0).toFixed(2))
    }
  });

  $('#reduceWeight').on('keyup', function () {
    var actualWeight =  $('#tareWeight').val() - $('#currentWeight').val() - $(this).val();

    if(actualWeight != ''){
      $('#actualWeight').text(actualWeight.toFixed(2));
      $('#actualWeight').val(actualWeight.toFixed(2));
    }
    else{
      $('#actualWeight').val((0).toFixed(2))
    }

    $('#variancePerc').trigger("keyup");
  });

  $('#variancePerc').on('keyup', function(){
    var supplyWeight =  $('#supplyWeight').val();
    var actualWeight =  $('#actualWeight').val();
    
    $('#variancePerc').val(((supplyWeight - actualWeight) / actualWeight * 100).toFixed(2));
  });

});

function format (row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Unit Weight: '+row.unitWeight+
  '</p></div><div class="col-md-3"><p>Weight Status: '+row.status+
  '</p></div><div class="col-md-3"><p>MOQ: '+row.moq+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: '+row.customer_address+
  '</p></div><div class="col-md-3"><p>Batch No: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Weight By: '+row.userName+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Lot No: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Invoice No: '+row.invoiceNo+
  '</p></div><div class="col-md-3 money"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3 money"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
  ;
}

function formatNormal (row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Unit Weight: '+row.unitWeight+
  '</p></div><div class="col-md-3"><p>Weight Status: '+row.status+
  '</p></div><div class="col-md-3"><p>MOQ: '+row.moq+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: '+row.customer_address+
  '</p></div><div class="col-md-3"><p>Batch No: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Weight By: '+row.userName+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Lot No: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Invoice No: '+row.invoiceNo+
  '</p></div><div class="col-md-3"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
}

function newEntry(){
  var date = new Date();
  $('#extendModal').find('#id').val("");
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
  $('#extendModal').find('#transporter').val('');
  $('#extendModal').find('#moq').val("1");
  $('#extendModal').find('#tareWeight').val("0.00");
  $('#extendModal').find('#package').val('');
  $('#extendModal').find('#actualWeight').val("");
  $('#extendModal').find('#supplyWeight').val("");
  $('#extendModal').find('#varianceWeight').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#totalPrice').val("");
  $('#extendModal').find('#unitPrice').val("");
  $('#extendModal').find('#totalWeight').val("");
  $('#extendModal').find('#manual').prop('checked', false);
  $('#extendModal').find('#manualVehicle').prop('checked', false);
  $('#extendModal').find('#manualOutgoing').prop('checked', false);
  $('#extendModal').find('#vehicleNoTct').val("");
  $('#extendModal').find('#vehicleNo').removeAttr('hidden');
  $('#extendModal').find('#vehicleNoTct').attr('hidden', 'hidden');
  // $('#extendModal').find('.hidOutgoing').attr('hidden', 'hidden');
  $('#extendModal').find('#currentWeight').attr('readonly', true);
  $('#extendModal').find('#tareWeight').attr('readonly', true);
  $('#extendModal').find('#reduceWeight').val("");
  $('#extendModal').find('#outGDateTime').val("");
  $('#extendModal').find('#inCDateTime').val("");
  $('#extendModal').find('#pStatus').val("");
  $('#extendModal').find('#variancePerc').val("");
  $('#extendModal').find('#dateTime').val(date.toLocaleString('en-GB'));
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

/*function setup(){
  $('#setupModal').find('#serialPortBaudRate').val('9600');
  $('#setupModal').find('#serialPortDataBits').val("8");
  $('#setupModal').find('#serialPortParity').val('N');
  $('#setupModal').find('#serialPortStopBits').val('1');
  $('#setupModal').find('#serialPortFlowControl').val('None');
  //$('#setupModal').modal('show');

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
}*/
function numberWithCommas(x) {
  debugger;
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function edit(id) {
  $.post('php/getWeights.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#serialNumber').val(obj.message.serialNo);
      $('#extendModal').find('#unitWeight').val(obj.message.unitWeight);
      $('#extendModal').find('#invoiceNo').val(obj.message.invoiceNo);
      $('#extendModal').find('#status').val(obj.message.status);
      $('#extendModal').find('#lotNo').val(obj.message.lotNo);
      $('#extendModal').find('#deliveryNo').val(obj.message.deliveryNo);
      $('#extendModal').find('#batchNo').val(obj.message.batchNo);
      $('#extendModal').find('#purchaseNo').val(obj.message.purchaseNo);
      $('#extendModal').find('#currentWeight').val(obj.message.currentWeight);
      $('#extendModal').find('#product').val(obj.message.productName);
      $('#extendModal').find('#moq').val(obj.message.moq);
      $('#extendModal').find('#transporter').val(obj.message.transporter);
      $('#extendModal').find('#tareWeight').val(obj.message.tare);
      $('#extendModal').find('#package').val(obj.message.package);
      $('#extendModal').find('#actualWeight').val(obj.message.actualWeight);
      $('#extendModal').find('#supplyWeight').val(obj.message.supplyWeight);
      $('#extendModal').find('#varianceWeight').val(obj.message.varianceWeight);
      $('#extendModal').find('#remark').val(obj.message.remark);
      $('#extendModal').find('#totalPrice').val(obj.message.totalPrice);
      $('#extendModal').find('#unitPrice').val(obj.message.unitPrice);
      $('#extendModal').find('#totalWeight').val(obj.message.totalWeight);
      $('#extendModal').find('#reduceWeight').val(obj.message.reduceWeight);
      $('#extendModal').find('#pStatus').val(obj.message.pStatus);
      $('#extendModal').find('#outGDateTime').val(obj.message.outGDateTime);
      $('#extendModal').find('#inCDateTime').val(obj.message.inCDateTime);
      $('#extendModal').find('#variancePerc').val(obj.message.variancePerc);
      $('#extendModal').find('#dateTime').val(obj.message.dateTime.toLocaleString("en-US"));
    
      if($('#extendModal').find('#status').val() == '1'){
        $('#extendModal').find('#customerNo').html($('select#customerNoHidden').html()).append($('#extendModal').find('#status').val());
        $('#extendModal').find('.labelStatus').text('Customer No');
        $('#extendModal').find('.labelOrder').text('Order Weight');
        $('#extendModal').find('#customerNo').val(obj.message.customer);
        
      }
      else if($('#extendModal').find('#status').val() == '2'){
        $('#extendModal').find('#customerNo').html($('select#supplierNoHidden').html()).append($('#extendModal').find('#status').val());
        $('#extendModal').find('.labelStatus').text('Supplier No');
        $('#extendModal').find('.labelOrder').text('Supply Weight');
        $('#extendModal').find('#customerNo').val(obj.message.customer);
      }

      if(obj.message.manualVehicle === 1){
        $('#extendModal').find('#manualVehicle').prop('checked', true);
        $('#extendModal').find('#vehicleNoTct').removeAttr('hidden');
        $('#extendModal').find('#vehicleNo').attr('hidden', 'hidden');
        $('#extendModal').find('#vehicleNoTct').val(obj.message.vehicleNo);
      }
      else{
        $('#extendModal').find('#manualVehicle').prop('checked', false);
        $('#extendModal').find('#vehicleNo').removeAttr('hidden');
        $('#extendModal').find('#vehicleNoTct').attr('hidden', 'hidden');
        $('#extendModal').find('#vehicleNo').val(obj.message.vehicleNo);
      }

            ///still need do some changes
      if(obj.message.manual === 1){
        $('#extendModal').find('#manual').prop('checked', true);
        $('#extendModal').find('#currentWeight').attr('readonly', false);
      }

      if(obj.message.manualOutgoing === 1){
        $('#extendModal').find('#manualOutgoing').prop('checked', true);
        $('#extendModal').find('#tareWeight').attr('readonly', false);
      }

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
      setTimeout(function(){
        printWindow.print();
        printWindow.close();
      }, 500);

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

</script>