
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
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Area Previous Data View</h5>
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
            <form action="AreaAllPrevious" method="GET" target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Year</label>
                <select class="form-control" name="year" id="year" required>
                    <option value="">select</option>
                    <?php 
                    foreach ($allyear as $row) {
                        ?>
                            <option value="<?php echo $row->year; ?>"><?php echo $row->year; ?></option>
                        <?php
                    }
                    ?>
                </select>
                </div>
                <div class="col-md-3">
                <label class="control-label">Period</label>
                <select class="form-control" name="period" id="period">
                    <option>select</option>
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
    $(document).ready(function(){
        $('#year').on('change', function() {
        var year= this.value;
        if(year !='')
        {  
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          type: 'POST',
          url: 'Area/Period',cache: false,
          dataType: 'json',
          data: { id: year },
          success: function (data) {
            
            //var d = data[0].region_id;
            console.log(data);
            var len = data.length;
            $("#period").empty();
            
            var option2 = "<option value=''>select</option>";
            $("#period").append(option2);
            for(var i = 0; i < len; i++)
            {
              var option = "<option value='"+data[i].datestart+"to"+data[i].dateend+"'>"+data[i].datestart+" to "+data[i].dateend+"</option>"; 

              $("#period").append(option); 
            }
            
          },
          error: function (ex) {

            alert('Failed to retrieve Period.');
          }
        });
          
          return;
        }
      }); 
    });
</script> 
@endsection