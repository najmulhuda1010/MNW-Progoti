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

@section('title','Cluster Dashboard')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Cluster Dashboard</h5>
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
            <form action="ClDashboard" method="GET" target="_blank">
              @csrf
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
                <div class="col-md-3">
                <label class="control-label">Cluster</label>
                <select class="form-control" name="asid" id="cluster" required>
                    <option value="">select</option>
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
<script>
    $('#zone').on('change', function() {
    var zone_id= this.value;
    // alert(zone_id);
    if(zone_id !='')
    {  
      $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      type: 'POST',
      url: '/mnwprogoti/ClusterData',cache: false,
      dataType: 'json',
      data: { id: zone_id },
      success: function (data) {
        
        //var d = data[0].region_id;
        //console.log(d);
        var len = data.length;
        $("#cluster").empty();;
        
        var option2 = "<option value=''>select</option>";
        $("#cluster").append(option2);
        for(var i = 0; i < len; i++)
        {
          var option = "<option value='"+data[i].c_associate_id+"'>"+data[i].cluster_name+"</option>"; 

          $("#cluster").append(option); 
        }
        
      },
      error: function (ex) {
        alert('Failed to retrieve Area.');
      }
    });
      
      return;
    }
  });
</script> 
@endsection