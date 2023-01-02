@extends('backend.layouts.master')

@section('title','User Update')

@section('content')
  <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
      <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center flex-wrap mr-2">
          <!--begin::Page Title-->
          <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Monitor User Update</h5>
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
                <form id="UserEditStore" method="POST" action="UserEditStore">
                <?php 
                foreach($edit as $e)
                {
                ?>
                <input type="hidden" name="id" class="form-control" value="<?php echo $e->id; ?>" required>
                <div class="box-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $e->name; ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Email</label>
                    <input type="text" class="form-control uname" id="email" name="email" placeholder="Please Enter Your Email" value="<?php echo $e->email; ?>" required data-parsley-error-message="Please Enter Email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Phone</label>
                    <input type="text" class="form-control phone" id="phone" value="<?php echo $e->phone; ?>" name="phone" placeholder="Please Enter Your Phone" data-parsley-type="phone" required data-parsley-error-message="Please Enter Phone">
                  </div>
                  
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="exampleInputEmail1">Username</label>
                              <input type="text" name="username" class="form-control" value="<?php echo $e->username; ?>" placeholder="Please Enter Your Username" required>
                            </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Password</label>
                            <input type="password" name="password" class="form-control" value="<?php echo $e->password; ?>" placeholder="Please Enter Your Password" required>
                            </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label for="exampleInputEmail1">User Pin</label>
                              <input type="text" name="userpin" class="form-control" value="<?php echo $e->user_pin; ?>" placeholder="Please Enter Your User Pin" required>
                            </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                            <label for="exampleInputEmail1">Device Id</label>
                            <input type="text" name="deviceid" class="form-control" value="<?php echo $e->device_id; ?>" placeholder="Please Enter Your Device Id">
                            </div>
                      </div>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Cluster</label>
                    <input type="text" name="cluster" class="form-control" value="<?php echo $e->cluster_id; ?>" list="cluster" autocomplete="off">
                    <datalist id="cluster">
                      @foreach ($clusters as $row)
                        <option value="{{$row->cluster_id}}-{{$row->cluster_name}}"><option>
                      @endforeach
                    </datalist>
                  </div>
                </div><!-- /.box-body -->

                <div class="form-group">
                  <center><button type="submit" class="btn btn-block btn-secondary">Submit</button></center>
                </div>
              </form>
              <?php } ?>

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