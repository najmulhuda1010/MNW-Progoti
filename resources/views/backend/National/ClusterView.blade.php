
@extends('backend.layouts.master')

@section('title','Cluster Report')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Cluster Report</h5>
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
            <div class="card-body">
                    
              <div class="row">
                <div class="col-md-12 gutter-b float-right">
                    <a href="ClusterAdd" class="btn btn-secondary">Add Cluster</a>
                    <a href="ClusterAddAccId" class="btn btn-secondary">Add Associate Id</a>
                    <a href="{{ url('Cluster/Excel') }}" class="btn btn-secondary">Excel Export</a>
                    <a href="{{ url('Cluster/PDF') }}" class="btn btn-secondary">PDF Export</a>
                    <a target="_blank" href="{{ url('Cluster/Print') }}" class="btn btn-secondary">Print</a>
                </div>
                <div class="col-md-12">
                  <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered" id="data-table">
                    <thead>
                        <tr class="brac-color-pink">
                          <th>Cluster Id</th>
                          <th>Cluster Name</th>
                          <th>Branch Code</th>
                          <th>Branch Name</th>
                          <th>Area Name</th>
                          <th>Region Name</th>
                          <th>Division Name</th>
                          <th>Zonal Code</th>
                          <th>Zonal Name</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($clusterdata as $row)
                        @php
                            $area_id=$row->area_id;
                            $branch_ary=DB::table('branch')->where('area_id',$area_id)->where('program_id',5)->get();
                            $branch_count=$branch_ary->count();
                            // dd($branch_ary[0]->branch_id);
                        @endphp
                          <tr>
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->cluster_id }}</td>
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->cluster_name }}</td>
                            @if ($branch_ary->isEmpty())
                              <td></td>
                              <td></td>
                            @else
                              <td>{{ $branch_ary[0]->branch_id }}</td>
                              <td>{{ $branch_ary[0]->branch_name }}</td>
                            @endif
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->area_name }}</td>
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->region_name }}</td>
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->division_name }}</td>
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $row->zonal_code }}</td>
                            @php
                                $zonal=DB::table('mnw_progoti.zonal')->where('zonal_code',$row->zonal_code)->first();
                            @endphp
                            <td class="align-middle" rowspan="{{ $branch_count }}">{{ $zonal->zonal_name }}</td>
                            <td style="white-space:nowrap;" class="align-middle" rowspan="{{ $branch_count }}"><a href="{{ url('editCluster/'.$row->cluster_id) }}" class="btn btn-light btn-sm">Edit</a> <a href="{{ url('deleteCluster/'.$row->cluster_id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')">Delete</a></td>
                          </tr>
                          @foreach($branch_ary as $key => $branch)
                          @if($key > 0)
                            <tr>
                              <td>{{ $branch->branch_id }}</td>
                              <td>{{ $branch->branch_name }}</td>
                            </tr>
                          @endif
                          @endforeach
                        @endforeach
                      </tbody>
                  </table>    
                  {{ $clusterdata->links() }}
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
   $(document).ready( function () {
    // $('#data-table').DataTable();
} );
  //   $(function () {
  //      var table = $('#data-table').DataTable({
  //      processing: true,
  //      serverSide: true,
  //      responsive: true,
  //      ajax: "{{ url('ClusterLoad') }}",
  //      columns: [
  //          {data: 'cluster_id', name: 'cluster_id',searchable: false },
  //          {data: 'cluster_name', name: 'cluster_name'},
  //          {data: 'branch_code', name: 'branch_code'},
  //          {data: 'branch_name', name: 'branch_name',searchable: false},
  //          {data: 'area_name', name: 'area_name',searchable: false},
  //          {data: 'region_name', name: 'region_name',searchable: false},
  //          {data: 'division_name', name: 'division_name',searchable: false},
  //          {data: 'zonal_code', name: 'zonal_code',searchable: false},
  //          {data: 'zonal_name', name: 'zonal_name',searchable: false},
  //          ]
  //       });
  //  });
   
   </script> 
@endsection