<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Counting Weighing</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-4">
                <div class="small-box bg-success">
                  <div class="inner">
                   <h3 style="text-align: center; font-size: 80px">
                     100.00
                     <sup style="font-size: 20px">PCs</sup>
                   </h3>
                  </div>
                    <a href="#" class="small-box-footer">
                     TOTAL COUNT / PCS
                    </a>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 style="text-align: center; font-size: 80px">
                      0.01
                      <sup style="font-size: 20px">Kg/g</sup>
                    </h3>
                  </div>
                    <a href="#" class="small-box-footer">
                      UNIT WEIGHT Kg/g
                    </a>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3 style="text-align: center; font-size: 80px">
                      1.00Kg
                      <sup style="font-size: 20px">Kg/g</sup>
                    </h3>
                  </div>
                    <a href="#" class="small-box-footer">
                        TOTAL WEIGHT Kg/g
                    </a>
                </div>
              </div>
            </div>
          
            <div class="row">
              <div class="col-lg-12">
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-4">
                        <div class="input-group-text bg-primary color-palette"><i>Indicator Connected</i></div>
                      </div>
                      <div class="col-4">
                        <div class="input-group-text color-palette"><i>Checking Connection</i></div>
                      </div>
                      <div class="col-4">
                        <button type="button" class="btn btn-block bg-gradient-primary"  data-toggle="modal" data-target="#modal-xl">
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
                            <option>Sales</option>
                            <option>Purchase</option>
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
                            <option>ABC1234</option>
                            <option>WWE1234</option>
                            <option>WWW1234</option>
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
        
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Serial No.</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Unit Weight</th>
                        <th>Tare</th>
                        <th>Current Weight</th>
                        <th>Actual Weight</th>
                        <th>Total Weight</th>
                        <th>Total PCS</th>
                        <th>MOQ</th>
                        <th>Unit Price <br> (RM)</th>
                        <th>Total Price <br> (RM)</th>
                        <th>Price/Pcs Amount <br> (RM)</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="cell-1" data-toggle="collapse" data-target="#demo1">
                        <td>S000001</td>
                        <td>Screw nut</td>
                        <td>kg</td>
                        <td>0.1</td>
                        <td>0.05</td>
                        <td>100.00</td>
                        <td>99.95</td>
                        <td>1.00</td>
                        <td>100</td>
                        <td>10</td>
                        <td>20.00</td>
                        <td>200.00</td>
                        <th>2,000.00</th>
                        <td class="table-elipse" data-toggle="collapse" data-target="#demo"><i class="fas fa-angle-down"></i></td>
                      </tr>
                      <tr id="demo1" class="collapse expand-body cell-1 row-child">
                        <td colspan="13">
                          <div class="row">
                            <div class="col-md-3">
                              <p>Vehicle No.: F88</p>
                            </div>
                            <div class="col-md-3">
                              <p>Lot No.: L013</p>
                            </div>
                            <div class="col-md-3">
                              <p>Batch No.: A021-21A001</p>
                            </div>
                            <div class="col-md-3">
                              <p>Invoice No.: SO22/123456</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-3">
                              <p>Delivery No.: SO22/123456</p>
                            </div>
                            <div class="col-md-3">
                              <p>Purchase No.: PO8754/MCT</p>
                            </div>
                            <div class="col-md-3">
                              <p>Customer: SM Metel Sdn Bhd</p>
                            </div>
                            <div class="col-md-3">
                              <p>Package: Jambo Bag</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-3">
                              <p>Date: 10/01/2022</p>
                            </div>
                            <div class="col-md-3">
                              <p>Time: 13:55:55</p>
                            </div>
                            <div class="col-md-3">
                              <p>Remark: </p>
                            </div>
                            <div class="col-md-3">
                              <div class="row">
                                <div class="col-3">
                                  <button type="button" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i>
                                  </button>
                                </div>
                                <div class="col-3">
                                  <button type="button" class="btn btn-warning btn-sm">
                                    <i class="fas fa-file"></i>
                                  </button>
                                </div>
                                <div class="col-3">
                                  <button type="button" class="btn btn-warning btn-sm">
                                    <i class="fas fa-print"></i>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                      <th colspan="3" style="text-align: right;">Total Accumulate</th>
                      <th>0.1 kg</th>
                      <th>0.05 kg</th>
                      <th>100.00 kg</th>
                      <th>99.95 kg</th>
                      <th>1.00 kg</th>
                      <th>100 Pcs</th>
                      <th>10</th>
                      <th>RM 20.00</th>
                      <th>RM 200.00</th>
                      <th>RM 2,000.00</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- pop out page -->
        <div class="modal fade" id="modal-xl">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header bg-gray-dark color-palette">
                <h4 class="modal-title">Add New Entry</h4>
                <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">

            <div class="row">
                <div class="col-lg-4">
                  <div class="small-box bg-success">
                    <div class="inner">
                     <h3 style="text-align: center; font-size: 80px">
                       100.00
                       <sup style="font-size: 20px">PCs</sup>
                     </h3>
                    </div>
                      <a href="#" class="small-box-footer">
                       TOTAL COUNT / PCS
                      </a>
                  </div>
                </div>

                <div class="col-lg-4">
                    <div class="small-box bg-success">
                      <div class="inner">
                       <h3 style="text-align: center; font-size: 80px">
                         0.01
                         <sup style="font-size: 20px">Kg/g</sup>
                       </h3>
                      </div>
                        <a href="#" class="small-box-footer">
                         UNIT WEIGHT Kg/g
                        </a>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="small-box bg-success">
                      <div class="inner">
                       <h3 style="text-align: center; font-size: 80px">
                         1.00Kg
                         <sup style="font-size: 20px">Kg/g</sup>
                       </h3>
                      </div>
                        <a href="#" class="small-box-footer">
                            TOTAL WEIGHT Kg/g
                        </a>
                    </div>
                  </div>
            </div>

                <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label>Date & Time:</label>
                      </div>
                    </div>
  
                    <div class="col-md-1">
                    </div>
  
                    <div class="form-group col-md-3">
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                          <label>Unit Weight</label>
                          <select class="form-control unitWeight1" style="width: 100%;">
                            <option selected="selected">kg</option>
                            <option>g</option>
                          </select>
                        </div>
                      </div>
                  </div>

                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Status :</label>
                      <select class="form-control Status" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-1">
                    <div class="form-group">
                      <label>Lot No :</label>
                      <select class="form-control lotNo" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-3">
                    <label>Invoice No</label>
                    <input class="form-control" type="text" placeholder="Invoice No">
                  </div>
                  
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Vehicle No</label>
                      <select class="form-control vehicleNo" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Customer No</label>
                      <select class="form-control customerNo" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-3">
                    <label>Delivery No</label>
                    <input class="form-control" type="text" placeholder="Delivery No">
                  </div>

                  <div class="form-group col-md-3">
                    <label>Unit Weight</label>
                    <div class="input-group" id="unitWeight" data-target-input="currentWeight">
                      <input class="form-control" type="text" placeholder="Current Weight"/>
                      <div class="input-group-text bg-primary color-palette"><i>KG/G</i></div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-3">
                    <label>Batch No</label>
                    <input class="form-control" type="text" placeholder="Batch No">
                  </div>

                  <div class="form-group col-md-3">
                    <label>Purchase No</label>
                    <input class="form-control" type="text" placeholder="Purchase No">
                  </div>

                  <div class="form-group col-md-3">
                    <label>Current Weight</label>
                    <div class="input-group" id="currentWeight" data-target-input="currentWeight">
                      <input class="form-control" type="text" placeholder="Current Weight"/>
                      <div class="input-group-text bg-primary color-palette"><i>KG/G</i></div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Product</label>
                      <select class="form-control product" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-3">
                    <label>M.O.Q</label>
                    <input class="form-control" type="text" placeholder="customer">
                  </div>

 
                    <div class="form-group col-md-3">
                      <label>Tare Weight</label>
                      <div class="input-group" id="tareWeight" data-target-input="tareWeight">
                        <input class="form-control" type="text" placeholder="Tare Weight"/>
                        <div class="input-group-text bg-danger color-palette"><i>KG/G</i></div>
                      </div>
                    </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Package</label>
                      <select class="form-control package" style="width: 100%;">
                        <option selected="selected">Alabama</option>
                        <option>Alaska</option>
                        <option>California</option>
                        <option>Delaware</option>
                        <option>Tennessee</option>
                        <option>Texas</option>
                        <option>Washington</option>
                      </select>
                    </div>
                  </div>

                    <div class="form-group col-md-3">
                      <label>Unit Price</label>
                      <div class="input-group" id="unitPrice" data-target-input="unitPrice">
                        <div class="input-group-text"><i>RM</i></div>
                        <input class="form-control" type="text" placeholder="unitPrice"/>                        
                      </div>
                  </div>

                    <div class="form-group col-md-3">
                      <label>Actual Weight</label>
                      <div class="input-group" id="actualWeight" data-target-input="actualWeight">
                        <input class="form-control" type="text" placeholder="Actual Weight"/>
                        <div class="input-group-text bg-success color-palette"><i>KG/G</i></div>
                      </div>
                    </div>
                </div>

                <div class="row">
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Remark</label>
                      <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
                    </div>
                  </div>

                    <div class="form-group col-md-3">
                      <label>Total Price</label>
                      <div class="input-group" id="totalPrice" data-target-input="totalPrice">
                        <div class="input-group-text"><i>RM</i></div>
                        <input class="form-control" type="text" placeholder="Total Price"/>                        
                      </div>
                  </div>

                    <div class="form-group col-md-3">
                      <label>Total PCs</label>
                      <div class="input-group" id="totalWeight" data-target-input="totalWeight">
                        <input class="form-control" type="text" placeholder="Total Weight"/>
                        <div class="input-group-text bg-primary color-palette"><i>PCs</i></div>
                      </div>
                    </div>
                </div>
                
              </div>
              <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Capture Indicator</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

      </div>

    </div>

  </div>

  <!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>

<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="../../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="../../plugins/moment/moment.min.js"></script>
<script src="../../plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="../../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Page specific script -->

<script>
  $(function () {
    
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


  });
</script>