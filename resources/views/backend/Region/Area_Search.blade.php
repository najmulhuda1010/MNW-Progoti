
@extends('backend.layouts.master')

@section('title','Area Search')

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
            <div class="card-body">
              
              <div class="row">
                <div class="col-md-12">
                  <form target="__blank" class="form-inline" action="GlobalReport" method="get">
                    
                    <div class="form-group mr-4">
                      <label for="inputPassword2" class="mr-4">Area Name</label>
                      <input type="text" class="form-control" id="selected" list="brsearch" name="area" required autocomplete="off">
                      <datalist id="brsearch">
                        <?php
                            foreach ($areasearch as $r)
                            {
                              ?>
                                <option data-value="{{$r->area_id }}" value="<?php echo $r->area_name; ?>"></option>
                              <?php
                            }
                          ?>
                        </datalist>
                    </div>
                    <button id="submit" type="submit" class="btn btn-secondary">Submit</button>
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
      var dataval=$('#brsearch [value="' + value + '"]').data('value');
      $("#selected").val(dataval);
      setTimeout(function(){ $("#selected").val(value); }, 100);
    });
  });
</script>
@endsection