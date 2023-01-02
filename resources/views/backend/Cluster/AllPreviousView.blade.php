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
?>
@extends('backend.layouts.master')

@section('title','Previous Data View')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Cluster Previous Data View</h5>
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
            <form action="ClusterAllPreviousData" method="GET" target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Year</label>
                <select class="form-control" name="year" id="year" onchange="ShowDiv(this.value)">
                    <option>select</option>
                    <?php 
                           foreach ($allyear as $row) {
                               ?>
                                  <option value="<?php echo $row->year; ?>"><?php echo $row->year; ?></option>
                               <?php
                           }
                        ?>
                </select>
                </div>
                <div class="col-md-3" id="monthFilterLabel1" style="display: none">
                <label class="control-label">From</label>
                <select class="form-control" name="month1" id="monthFilter1" style="display: none">
                    <option value="">select month</option>
                    <?php 
                           foreach ($allmonth as $row) {
                               ?>
                                  <option value="<?php echo $row->month; ?>"><?php 
                                  if($row->month=='01'){
                                    echo "January";
                                  }elseif($row->month=='02'){
                                    echo "February";
                                  }elseif($row->month=='03'){
                                    echo "March";
                                  }elseif($row->month=='04'){
                                    echo "April";
                                  }elseif($row->month=='05'){
                                    echo "May";
                                  }elseif($row->month=='06'){
                                    echo "June";
                                  }elseif($row->month=='07'){
                                    echo "July";
                                  }elseif($row->month=='08'){
                                    echo "August";
                                  }elseif($row->month=='09'){
                                    echo "September";
                                  }elseif($row->month=='10'){
                                    echo "October";
                                  }elseif($row->month=='11'){
                                    echo "November";
                                  }elseif($row->month=='12'){
                                    echo "December";
                                  }
                                  ?></option>
                               <?php
                           }
                        ?>
                </select>
                </div>
                <div class="col-md-3" id="monthFilterLabel2" style="display: none">
                <label  class="control-label">To</label>
                <select class="form-control" name="month2" id="monthFilter2" style="display: none">
                    <option value="">select month</option>
                    <?php 
                           foreach ($allmonth as $row) {
                               ?>
                                  <option value="<?php echo $row->month; ?>"><?php 
                                  if($row->month=='01'){
                                    echo "January";
                                  }elseif($row->month=='02'){
                                    echo "February";
                                  }elseif($row->month=='03'){
                                    echo "March";
                                  }elseif($row->month=='04'){
                                    echo "April";
                                  }elseif($row->month=='05'){
                                    echo "May";
                                  }elseif($row->month=='06'){
                                    echo "June";
                                  }elseif($row->month=='07'){
                                    echo "July";
                                  }elseif($row->month=='08'){
                                    echo "August";
                                  }elseif($row->month=='09'){
                                    echo "September";
                                  }elseif($row->month=='10'){
                                    echo "October";
                                  }elseif($row->month=='11'){
                                    echo "November";
                                  }elseif($row->month=='12'){
                                    echo "December";
                                  }
                                  ?></option>
                               <?php
                           }
                        ?>
                </select>
                </div>
                <div class="col-md-3">
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
<script>
    function ShowDiv(val)
    {
        if(val){
            $("#monthFilterLabel1").show();
            $("#monthFilter1").show();
            $("#monthFilterLabel2").show();
            $("#monthFilter2").show();
        }else{
            $("#monthFilterLabel1").hide();
            $("#monthFilter1").hide();
            $("#monthFilterLabel2").hide();
            $("#monthFilter2").hide();
        }
    }
</script>
@endsection