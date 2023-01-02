
@extends('backend.layouts.master')

@section('title','Area Wise Search')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Area Search</h5>
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
          @if (Session::has('message'))
          <div class="alert alert-danger" role="alert">
            {{ Session::get('message') }}
          </div>
          @endif
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <form action="GlobalReport" method="GET" target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Region Name</label>
                <select class="form-control" name="region" id="regionid" required>
                    <option value="">select</option>
                    <?php
                    foreach ($divisionsearch as $r)
                    {
                    $aid = $r->region_id;
                    $regionname = DB::table('branch')->where('region_id',$aid)->where('program_id',5)->get();
                    if(!empty($regionname))
                    {
                        $rname = $regionname[0]->region_name;
                    }
                    else
                    {
                        $rname ='';
                    }
                    ?>
                        <option value="<?php echo $r->region_id; ?>"><?php echo $rname; ?></option>
                    <?php
                    }
                ?>
                </select>
                </div>
                <div class="col-md-3">
                <label class="control-label">Area Name</label>
                <select class="form-control" name="area" id="areaid" required>
                    <option value="">select</option>
                </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary" style="margin: 25px 0px 0px 25px;">Submit</button>
                </div>
            </form>

            </div>
            <div class="row mt-7">
                <div class="col-md-12">
                    <form action="GlobalReport" method="GET" target="_blank">
                        <label for="example-search-input" class="control-label">Area Search</label>
                        <div class="form-group row">
                            <div class="col-6">
                             <input class="form-control" type="text" id="selected" list="browsers" name="area" autocomplete="off" required/>
                             <datalist id="browsers">
                                <?php 
                                $area = DB::select(DB::raw("select area_id,area_name from branch  where division_id='$a_id' group by area_id,area_name order by area_id ASC"));
                                if(!empty($area))
                                {
                                    foreach($area as $row)
                                    {
                                        ?>
                                        <option data-value="{{ $row->area_id }}" value="<?php echo $row->area_name; ?>"></option>
                                    <?php
                                    }
                                }
                                ?>
                              </datalist>
                            </div>
                            <div class="col-3">
                                <button id="submit" type="submit" class="btn btn-secondary" style="margin: 0px 0px 0px 25px;">Search</button>

                            </div>
                        </div>
                  </form>
                </div>
            </div>
            </div>
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
  $(document).ready(function() {
    $('#submit').click(function()
    {
      var value = $('#selected').val();
      var dataval=$('#browsers [value="' + value + '"]').data('value');
      $("#selected").val(dataval);
      setTimeout(function(){ $("#selected").val(value); }, 100);
    });
  });
</script>
<script>
    $('#regionid').on('change', function() {
    //alert( this.value );
   // $("#divs").empty();
    var region_id= this.value;
    //alert(area_id);
    if(region_id !='')
    {  
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
      type: 'POST',
      url: '/mnwprogoti/AreaData',cache: false,
      dataType: 'json',
      data: { id: region_id },
      success: function (data) {
        
        //var d = data[0].region_id;
        //console.log(d);
        var len = data.length;
        $("#areaid").empty();
        
        var option2 = "<option value=''>select</option>";
        $("#areaid").append(option2);
        for(var i = 0; i < len; i++)
        {
          var option = "<option value='"+data[i].area_id+"'>"+data[i].area_name+"</option>"; 

          $("#areaid").append(option); 
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