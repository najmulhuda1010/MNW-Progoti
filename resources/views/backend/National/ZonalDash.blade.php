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

@section('title','Zonal Dashboard')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Zonal Dashboard</h5>
      </div>
      <!--end::Info-->
    </div>
  </div>
  <!--end::Subheader-->
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Dashboard-->
      <!--begin::Row-->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <form action="ZonalDashboard" method="GET" target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Zone</label>
                <select class="form-control" name="zone" id="zone" required>
                    <option value="">select</option>
                    @if ($zoneall)
                    <?php foreach($zoneall as $row){
                      ?>
                      <option value="<?php echo $row->z_associate_id; ?>"><?php  echo $row->zonal_name; ?></option>
                      <?php
                    } ?>
                    @endif
                    
                </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-secondary" style="margin: 25px 0px 0px 25px;">Submit</button>
                </div>
            </div>
            </div>
            </form>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <br>
        
      </div>
      <!--end::Row-->
      <!--begin::Row-->
      
      <!--end::Row-->
      <!--end::Dashboard-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Entry-->
</div>
    
@endsection

@section('script')

@endsection