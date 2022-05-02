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
				<h1 class="m-0 text-dark">Currencies</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addUnits">Add Currency</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="unitTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No.</th>
									<th>Currency</th>
                                    <th>Description</th>
                                    <th>Rate</th>
									<th>Actions</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="unitModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="unitForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Currency</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
    					<input type="hidden" class="form-control" id="id" name="id">
    				</div>
    				<div class="form-group">
    					<label for="units">Currency *</label>
    					<input type="text" class="form-control" name="units" id="units" placeholder="Enter Currency" required>
    				</div>
                    <div class="form-group">
    					<label for="desc">Description *</label>
    					<input type="text" class="form-control" name="desc" id="desc" placeholder="Enter Currency Description" required>
    				</div>
                    <div class="form-group">
    					<label for="rate">Rate *</label>
    					<input type="number" class="form-control" name="rate" id="rate" placeholder="Enter Currency Rate" required>
    				</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitLot">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
$(function () {
    $("#unitTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadCurrency.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'currency' },
            { data: 'description' },
            { data: 'rate' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
        ],
        "rowCallback": function( row, data, index ) {
            $('td', row).css('background-color', '#E6E6FA');
        },
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $.post('php/currency.php', $('#unitForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#unitModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    
                    $.get('currency.php', function(data) {
                        $('#mainContents').html(data);
                    });
                }
                else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                }
                else{
                    alert("Something wrong when edit");
                }
            });
        }
    });

    $('#addUnits').on('click', function(){
        $('#unitModal').find('#id').val("");
        $('#unitModal').find('#units').val("");
        $('#unitModal').find('#desc').val("");
        $('#unitModal').find('#rate').val("");
        $('#unitModal').modal('show');
        
        $('#unitForm').validate({
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
    });
});

function edit(id){
    $.post('php/getCurrency.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#unitModal').find('#id').val(obj.message.id);
            $('#unitModal').find('#units').val(obj.message.currency);
            $('#unitModal').find('#desc').val(obj.message.description);
            $('#unitModal').find('#rate').val(obj.message.rate);
            $('#unitModal').modal('show');
            
            $('#unitForm').validate({
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
            toastr["error"]("Something wrong when activate", "Failed:");
        }
    });
}

function deactivate(id){
    $.post('php/deleteCurrency.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $.get('currency.php', function(data) {
                $('#mainContents').html(data);
            });
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