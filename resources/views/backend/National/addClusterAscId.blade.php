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

@section('title','Add Cluster Associate')

@section('content')
  <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
      <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-2">
          <!--begin::Page Title-->
          <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Add Cluster Associate Id</h5>
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
                <form id="form_signup" method="POST" action="ClusterAddAccId">
                  @csrf
                <div class="box-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Cluster</label>
                    <input type="text" class="form-control lname" name="cluster" placeholder="Type Cluster" required data-parsley-error-message="Please Enter Cluster Code" list="clusters" autocomplete="off">
                    <datalist id="clusters">
                        @foreach ($clusters as $row)
                            <option value="<?php echo $row->cluster_id."-".$row->cluster_name; ?>">
                        @endforeach
                    </datalist>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Cluster PIN</label>
                    <input type="text" class="form-control lname" name="cassociate_id" placeholder="Type PIN" required data-parsley-error-message="Please Enter Cluster Code" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Zonal</label>
                    <input type="text" class="form-control lname" name="zonal" placeholder="Type Cluster" required data-parsley-error-message="Please Enter Cluster Code" list="zonal" autocomplete="off">
                    <datalist id="zonal">
                        @foreach ($zonal as $row)
                            <option value="<?php echo $row->zonal_code."-".$row->zonal_name; ?>">
                        @endforeach
                    </datalist>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Zonal PIN</label>
                    <input type="text" class="form-control lname" name="zassociate_id" placeholder="Type PIN" required data-parsley-error-message="Please Enter Cluster Code" autocomplete="off">
                  </div>
                  
                </div><!-- /.box-body -->

                <div class="box-footer">
                  <center><button type="submit" class="btn btn-secondary btn-block">Submit</button></center>
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