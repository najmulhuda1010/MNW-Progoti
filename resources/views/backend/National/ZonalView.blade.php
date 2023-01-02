@extends('backend.layouts.master')

@section('title','Zonal Report')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Zonal Report</h5>
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
          <div class="card card-custom gutter-b">
            {{-- <div class="card-header">
              <h4 class="card-title">Monitoring Event: asd </h4>
            </div> --}}
            <!--begin::Form-->
            <div class="card-body table-responsive">
              <p class="card-title"><a class="btn btn-secondary" href="ZonalAdd">Add Zonal</a></p>
              <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered" id="data-table">
                <thead>
                <tr class="brac-color-pink">
                    <th>Zonal Code</th>
                    <th>Zonal Name</th>
                    <th>Action</th>
                </tr>
              </thead>
                <tbody>
                  </tbody>
              </table>
            </div>
            <div class="table-responsive">
                {{$zonaldata->links()}}
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

@section('script')
<script>

$(function () {
   
   var table = $('#data-table').DataTable({
    dom: 'fBrtip',
    dom:"<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>\n\t\t\t<'row'<'col-sm-12'tr>>\n\t\t\t<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
        buttons: [
            'print',
            'excelHtml5',
            'pdfHtml5'
        ],
   processing: true,
   serverSide: true,
   responsive: true,
   ajax: "{{ url('/zonalDataLoad') }}",
   columns: [
       {data: 'zonal_code', name: 'zonal_code'},
       {data: 'zonal_name', name: 'zonal_name'},
       {data: 'action', name: 'action'},
       ]
   });
});
   
   </script>
@endsection