<?php 
$username = Session::get('username');
if($username=='')
{
  
  ?>
  <script>
    window.location.href = 'Logout';
  </script>
  
  <?php 
  
}
$calculation=1;
$dataload1="true";
$dataload1 = $dataload;
if($dataload1 =="true")
{
  if($calculation==1)
  {
    ?>
    @extends('backend.DataProcessing.DataProcessing')
    <script type="text/javascript">
      //window.location = "BRDashboard?dataload=false;"
      //alert("test");
    </script>
    
    <?php
     $speed=0;
  }
}
if(isset($_GET['dataload']))
{
  $calculation = $_GET['dataload'];
  if($calculation=="false")
  {
    $calculation=0;
  }
}

?>
@extends('backend.layouts.master')

@section('title','Zonal Update')

@section('content')
  <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
      <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-2">
          <!--begin::Page Title-->
          <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Zonal Update</h5>
        </div>
        <!--end::Info-->
      </div>
    </div>
    <div class="d-flex flex-column-fluid">
      <!--begin::Container-->
      <div class="container">
        <!--begin::Card-->
        <div class="card card-custom">
          {{-- <div class="card-header flex-wrap py-5">
            <div class="card-title">
              <h3 class="card-label">Form </h3>
            </div>
          </div> --}}
          <div class="card-body">
            <!--begin: Datatable-->
            <div class="row">  
              <div class="col-md-8 col-xs-12 col-sm-12 offset-md-2">   
                @if (Session::has('success'))
                <div class="alert alert-success" role="success">
                  {{ Session::get('success') }}
                </div>
                @endif
                @if (Session::has('error'))
                <div class="alert alert-danger" role="success">
                  {{ Session::get('error') }}
                </div>
                @endif
                <form id="form_signup" method="POST" action="{{ url('ZonalUpdate') }}">
                  @csrf
                  <input type="hidden" class="form-control" name="id" value="{{ $zonal->id }}">
                  <input type="hidden" class="form-control" name="zonalid" value="{{ $zonal->zonal_code }}">
                <div class="box-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Zonal Name</label>
                    <input type="text" class="form-control fname" name="zonalname" value="{{ $zonal->zonal_name }}" placeholder="Zonal Name" required oninvalid="this.setCustomValidity('Please Enter Zonal Name')">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Zonal Id</label>
                    <input type="text" class="form-control lname" name="zonalcode" value="{{ $zonal->zonal_code }}" placeholder="Zonal Code" required oninvalid="this.setCustomValidity('Please Enter Zonal Code')">
                  </div>
                </div><!-- /.box-body -->

                <div class="box-footer">
                  <center><button type="submit" class="btn btn-secondary btn-block">Update</button></center>
                </div>
              </form>
              </div>
              </div>
            <!--end: Datatable-->
          </div>
        </div>
        <!--end::Card-->
      </div>
      <!--end::Container-->
    </div>
  </div>
@endsection