<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $lots = $db->query("SELECT * FROM lots");
  $vehicles = $db->query("SELECT * FROM vehicles");
  $products = $db->query("SELECT * FROM products");
  $packages = $db->query("SELECT * FROM packages");
  $customers = $db->query("SELECT * FROM customers");
}
?>

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
      <div class="col-6">
        <div class="small-box bg-success">
          <div class="inner">
            <h3 style="text-align: center; font-size: 80px">
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
      </div>

      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-6">
                <h3 class="card-title">Billboard Description :</h3>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm"  data-toggle="modal" data-target="#">
                  Export Excel
                </button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  data-toggle="modal" data-target="#">
                  Search
                </button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="form-group col-3">
                <label>From Date:</label>
                <div class="input-group date" id="fromDate" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#fromDate"/>
                  <div class="input-group-append" data-target="#fromDate" data-toggle="datetimepicker"><div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
                </div>
              </div>

              <div class="form-group col-3">
                <label>To Date:</label>
                <div class="input-group date" id="toDate" data-target-input="nearest">
                  <input type="text" class="form-control datetimepicker-input" data-target="#toDate"/>
                  <div class="input-group-append" data-target="#toDate" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>

              <div class="form-group col-3">
                <label>Customer</label>
                <input class="form-control" type="text" placeholder="customer">
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control Status" style="width: 100%;">
                    <option selected="selected">-</option>
                    <option value="SALES">Sales</option>
                    <option value="PURCHASES">Puchases</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-3">
                <div class="form-group">
                  <label>Vehicle No</label>
                  <select class="form-control vehicleNo" style="width: 100%;">
                    <option selected="selected">-</option>
                    <?php while($row=mysqli_fetch_assoc($vehicles)){ ?>
                      <option value="<?=$row['id'] ?>"><?=$row['veh_number'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group col-3">
                <label>Invoice No</label>
                <input class="form-control" type="text" placeholder="Invoice No">
              </div>

              <div class="form-group col-3">
                <label>Batch No</label>
                <input class="form-control" type="text" placeholder="Batch No">
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
        <div class="col-lg-12 col-12 d-flex justify-content-center">
          <div class="small-box bg-success">
            <div class="inner">
              <h3 style="text-align: center; font-size: 80px">
                100.00
                <sup style="font-size: 20px">KG</sup>
              </h3>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label>Status :</label>
              <select class="form-control" style="width: 100%;" id="status" name="status" required>
                <option selected="selected">-</option>
                <option value="SALES">Sales</option>
                <option value="PURCHASES">Puchases</option>
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label>Lot No :</label>
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
            <input class="form-control" type="text" placeholder="Invoice No" id="invoiceNo" name="invoiceNo" required>
          </div>
          
          <div class="col-md-3">
            <div class="form-group">
              <label>Vehicle No</label>
              <select class="form-control" style="width: 100%;" id="vehicleNo" name="vehicleNo" required>
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
              <label>Customer No</label>
              <select class="form-control" style="width: 100%;" id="customerNo" name="customerNo" required>
                <option selected="selected">-</option>
                <?php while($row4=mysqli_fetch_assoc($customers)){ ?>
                  <option value="<?=$row4['id'] ?>"><?=$row4['customer_name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label>Delivery No</label>
            <input class="form-control" type="text" placeholder="Delivery No" id="deliveryNo" name="deliveryNo" required>
          </div>

          <div class="col-md-3">
            <div class="form-group">
              <label>Unit Weight</label>
              <select class="form-control" style="width: 100%;" id="unitWeight" name="unitWeight" required> 
                <option selected="selected">-</option>
                <option value="KG">KG</option>
                <option value="G">G</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="form-group col-md-4">
            <label>Batch No</label>
            <input class="form-control" type="text" placeholder="Batch No" id="batchNo" name="batchNo" required>
          </div>

          <div class="form-group col-md-3">
            <label>Purchase No</label>
            <input class="form-control" type="text" placeholder="Purchase No" id="purchaseNo" name="purchaseNo" required>
          </div>

          <div class="form-group col-md-3">
            <label>Current Weight</label>
            <div class="input-group">
              <input class="form-control" type="text" placeholder="Current Weight" id="currentWeight" name="currentWeight" required/>
              <div class="input-group-text bg-primary color-palette"><i>KG/G</i></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Product</label>
              <select class="form-control" style="width: 100%;" id="product" name="product" required>
                <option selected="selected">-</option>
                <?php while($row5=mysqli_fetch_assoc($products)){ ?>
                  <option value="<?=$row5['id'] ?>"><?=$row5['product_name'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-md-3">
            <label>M.O.Q</label>
            <input class="form-control" type="text" placeholder="moq" id="moq" name="moq" required>
          </div>

          <div class="form-group col-md-3">
            <label>Tare Weight</label>
            <div class="input-group">
              <input class="form-control" type="text" placeholder="Tare Weight" id="tareWeight" name="tareWeight" required/>
              <div class="input-group-text bg-danger color-palette"><i>KG/G</i></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>Package</label>
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
                <input class="form-control" type="text" placeholder="unitPrice" id="unitPrice" name="unitPrice" required/>                        
              </div>
          </div>

            <div class="form-group col-md-3">
              <label>Actual Weight</label>
              <div class="input-group">
                <input class="form-control" type="text" placeholder="Actual Weight" id="actualWeight" name="actualWeight" required/>
                <div class="input-group-text bg-success color-palette"><i>KG/G</i></div>
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
                <input class="form-control" type="text" placeholder="Total Price"  id="totalPrice" name="totalPrice" required/>                        
              </div>
          </div>

            <div class="form-group col-md-3">
              <label>Total Weight</label>
              <div class="input-group">
                <input class="form-control" type="text" placeholder="Total Weight" id="totalWeight" name="totalWeight" required/>
                <div class="input-group-text bg-success color-palette"><i>KG/G</i></div>
              </div>
            </div>
        </div>
        
      </div>
      <div class="modal-footer justify-content-between bg-gray-dark color-palette">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Capture Indicator</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    
    </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
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
      row.child( format(row.data()) ).show();
      tr.addClass('shown');
    }
  });

    //Initialize Select2 Elements
  $('.Status').select2({
    theme: 'bootstrap4'
  }),
  $('.vehicleNo').select2({
    theme: 'bootstrap4'
  }),
  $('.lotNo').select2({
    theme: 'bootstrap4'
  }),
  $('.customerNo').select2({
    theme: 'bootstrap4'
  }),
  $('.unitWeight').select2({
    theme: 'bootstrap4'
  }),
  $('.product').select2({
    theme: 'bootstrap4'
  }),
  $('.package').select2({
    theme: 'bootstrap4'
  }),
      //Date picker
  $('#fromDate').datetimepicker({
    format: 'L'
  }),
  $('#toDate').datetimepicker({
    format: 'L'
  });

  $.validator.setDefaults({
    submitHandler: function () {
        if($('#extendModal').hasClass('show')){
          $.post('/php/insertWeight.php', $('#extendForm').serialize(), function(data){
            var obj = JSON.parse(data); 
            if(obj.status === 'success'){
              $('#extendModal').modal('hide');
              toastr["success"](obj.message, "Success:");
                  
            // $.get('insertWeight.php', function(data) {
            //           $('#mainContents').html(data);
            //       });
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
});

function format (row) {
  return '<div class="row"><div class="col-md-3"><p>Vehicle No.: '+row.veh_number+'</p></div><div class="col-md-3"><p>Lot No.: '+row.lots_no+'</p></div><div class="col-md-3"><p>Batch No.: '+row.batchNo+'</p></div><div class="col-md-3"><p>Invoice No.: '+row.invoiceNo+'</p></div></div><div class="row"><div class="col-md-3"><p>Delivery No.: '+row.deliveryNo+'</p></div><div class="col-md-3"><p>Purchase No.: '+row.purchaseNo+'</p></div><div class="col-md-3"><p>Customer: '+row.customer_name+'</p></div><div class="col-md-3"><p>Package: '+row.packages+'</p></div></div><div class="row"><div class="col-md-3"><p>Date: '+row.date+'</p></div><div class="col-md-3"><p>Time: '+row.time+'</p></div><div class="col-md-3"><p>Remark: '+row.remark+'</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-success btn-sm" onclick="excel('+row.serialNo+')"><i class="fas fa-file-excel"></i></button></div><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="view('+row.serialNo+')"><i class="fas fa-file"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.serialNo+')"><i class="fas fa-print"></i></button></div></div></div></div>';
}

function newEntry(){
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
  $('#extendModal').find('#moq').val("");
  $('#extendModal').find('#tareWeight').val("");
  $('#extendModal').find('#package').val('');
  $('#extendModal').find('#actualWeight').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#totalPrice').val("");
  $('#extendModal').find('#unitPrice').val("");
  $('#extendModal').find('#totalWeight').val("");
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

function excel(id) {

}

function view(id) {

}

function print(id) {

}

</script>