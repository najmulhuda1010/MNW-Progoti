@extends('backend.layouts.master')

@section('title','Fraud Documnets')

@section('style')
<style>
  </style>
@endsection
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">List of suspecious frad documnets</h5>
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
            <form>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                    <p class="font-size-h4">Event : <?php echo $datestart.' to '.$dateend; ?></p>
                    </div>
                    <div class="col-md-3">
                      <p class="font-size-h4">Area : <?php echo $area_name; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p class="font-size-h4">Region : <?php echo $region_name; ?></p>
                    </div>
                    <div class="col-md-2">
                        <p class="font-size-h4">Division : <?php echo $division_name; ?></p>
                    </div>
                </div>
            </div>
            </form>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <br>
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
                <h3 class="card-title">1.5: Check list of documents which are collected from clients_List of suscceptible fraud documents</h3>
            </div>
            <div class="card-body table-responsive">
              <table class="table table-bordered rounded" style="text-align: center">
                <tr class="brac-color-pink">
                  <th>SL</th>
                  <th >Branch Code</th>
                  <th >Member No</th>
                  <th >List of succeptible fraud documents</th>
                </tr>
                <tbody>
                    @php
                        $sl=1
                    @endphp
                   @foreach ($fradDocuments as $row)
                    <tr>
                       <td>{{ $sl }}</td>
                       <td>{{ $row['branchcode'] }}</td>
                       <td>{{ $row['orgmemno'] }}</td>
                       <td>{{ $row['document'] }}</td>
                       @php
                           $sl++;
                       @endphp
                    </tr>  
                   @endforeach
                </tbody>
              </table>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
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
