<?php
$clusterdata =DB::table('mnw_progoti.cluster')->orderBy('cluster_id','ASC')->get();
?>
                  <table id="tblReport" style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered" id="data-table">
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
                 