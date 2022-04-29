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

<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
          max-width: 1800px; /* New width for default modal */
        }
    }
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
              <div class="col-6">Billboard Description</div>
              <div class="col-4"></div>
              <!--div class="col-1">
                <button type="button" class="btn btn-info btn-sm"  onclick="newEntry()">
                <i class="fas fa-plus"></i>
                </button>
              </div-->
              <div class="col-1">
                <button type="button" class="btn btn-success btn-sm" id="excelSearch">
                <i class="fas fa-file-excel"></i>
                </button>
              </div>
              <div class="col-1">
                <button type="button" class="btn btn-warning btn-sm" id="filterSearch">
                <i class="fas fa-search"></i>
                </button>
              </div-->
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
                      <option value="<?=$row1['veh_number'] ?>"><?=$row1['veh_number'] ?></option>
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
</div>

<script>
$(function () {
  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': true,
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
    "rowCallback": function( row, data, index ) {

        $('td', row).css('background-color', '#E6E6FA');
    },
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
  });

  $('#toDate').datetimepicker({
    format: 'D/MM/YYYY h:m:s A'
  });

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
    }
  });

  $('#customerNoHidden').hide();
  $('#supplierNoHidden').hide();

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
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div></div></div></div>'+
  '<div class="row"><div class="col-md-3"><p>Supply Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+ '</div></div>';
  ;
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
  '")"><i class="fas fa-print"></i></button></div></div></div></div>'+
  '<div class="row"><div class="col-md-3"><p>Supply Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+ '</div></div>';
}

/*function newEntry(){
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
  $('#extendModal').find('#supplyWeight').val("");
  $('#extendModal').find('#varianceWeight').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#totalPrice').val("");
  $('#extendModal').find('#unitPrice').val("");
  $('#extendModal').find('#totalWeight').val("");
  $('#extendModal').find('#manual').prop('checked', false);
  $('#extendModal').find('#manualVehicle').prop('checked', false);
  $('#extendModal').find('#vehicleNoTct').val("");
  $('#extendModal').find('#vehicleNo').removeAttr('hidden');
  $('#extendModal').find('#vehicleNoTct').attr('hidden', 'hidden');
  $('#extendModal').find('#currentWeight').attr('readonly', true);
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
      $('#extendModal').find('#deliveryNo').val(obj.message.deliveryNo);
      $('#extendModal').find('#batchNo').val(obj.message.batchNo);
      $('#extendModal').find('#purchaseNo').val(obj.message.purchaseNo);
      $('#extendModal').find('#currentWeight').val(obj.message.unitWeight);
      $('#extendModal').find('#product').val(obj.message.productName);
      $('#extendModal').find('#moq').val(obj.message.moq);
      $('#extendModal').find('#tareWeight').val(obj.message.tare);
      $('#extendModal').find('#package').val(obj.message.package);
      $('#extendModal').find('#actualWeight').val(obj.message.actualWeight);
      $('#extendModal').find('#supplyWeight').val(obj.message.supplyWeight);
      $('#extendModal').find('#varianceWeight').val(obj.message.varianceWeight);
      $('#extendModal').find('#remark').val(obj.message.remark);
      $('#extendModal').find('#totalPrice').val(obj.message.totalPrice);
      $('#extendModal').find('#unitPrice').val(obj.message.unitPrice);
      $('#extendModal').find('#totalWeight').val(obj.message.totalWeight);
      $('#dateTime').datetimepicker({
        format: 'D/MM/YYYY h:m:s A'
      });
      $('#extendModal').find('#dateTime').val(obj.message.dateTime.toLocaleString("en-US"));
      
    
      if($('#extendModal').find('#status').val() == '1'){
        $('#extendModal').find('#customerNo').html($('select#customerNoHidden').html()).append($('#extendModal').find('#status').val());
        $('#extendModal').find('.labelStatus').text('Customer No');
        $('#extendModal').find('#customerNo').val(obj.message.customer);
        
      }
      else if($('#extendModal').find('#status').val() == '2'){
        $('#extendModal').find('#customerNo').html($('select#supplierNoHidden').html()).append($('#extendModal').find('#status').val());
        $('#extendModal').find('.labelStatus').text('Supplier No');
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
}*/

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $.post('php/deleteWeight.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
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