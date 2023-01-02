<?php 
$username = Session::get('username');
//$userpin = Session::get('user_pin');
if($username=='')
{
  
  ?>
  <script>
    window.location.href = 'logout';
  </script>
  
  <?php 
  
}
?>
@extends('backend.layouts.master')

@section('title','Claster Dashboard')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Claster Dashboard</h5>
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
            <?php
      $month ='';
      $cyear = date('Y');
      $cmnth = date('m');
      $i=0;
      $ct =0;
      $truncate = DB::select(DB::raw("truncate mnw_progoti.cluster_temp RESTART IDENTITY"));
      $Cluster = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year <= '$cyear' group by year,month order by year DESC, month DESC  limit 6"));
      foreach ($Cluster as $row) 
      {
        $ct =0;
        $mon = $row->month;
        $year = $row->year;
        //echo $mon;
         $i++;
        if($mon == $cmnth && $year==$cyear)
        {
           continue;
        }
        $getdata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year ='$year' and month='$mon'"));
        if(!empty($getdata))
        {
          $sp =0;
          $qsp =0;
          foreach ($getdata as $row) 
          {
            $sp =0;
            $qsp =0;
           // $cluster = 1;
            $eventid= $row->id;
            $arcode = $row->area_id;
            // $tcnt = strlen($brcode);
            // if($tcnt=='03')
            // {
            //   $brcode = '0'.$brcode;
            // }
			$sql = DB::select(DB::raw("select * from mnw_progoti.cluster"));
			if(!empty($sql))
			{
				foreach($sql as $r)
				{
					$arcode1 = $r->area_id;
					$id =$r->id;
					// $tcnt = strlen($brcode1);
					// if($tcnt=='03')
					// {
					//   $brcode2 = '0'.$brcode1;
					//   $clusterupdate = DB::select(DB::raw("update mnw_progoti.cluster set branch_code='$brcode2' where id='$id'"));
					// }
				}
				
				 
			}
            $clustersearch = DB::select(DB::raw("select * from mnw_progoti.cluster where c_associate_id='$userpin' and area_id='$arcode'"));
            if(!empty($clustersearch))
            {
                $cluster = $clustersearch[0]->cluster_id;
                $getpointcluster = DB::select(DB::raw("select sum(point) p, sum(question_point) q from mnw_progoti.cal_section_point where event_id='$eventid' and (section='4' and (sub_id=5 or sub_id=6 or sub_id=8 or sub_id=9 or sub_id=10 or sub_id=11)) or event_id='$eventid' and (section='5' and (sub_id=3 or sub_id=4 or sub_id=5 or sub_id=16)) or event_id='$eventid' and (section='2' and sub_id=3 )"));
                if(!empty($getpointcluster))
                {
                    $sp = $getpointcluster[0]->p;
                    $qsp = $getpointcluster[0]->q;
                }
                $totalscore =0;
                if($sp !=0)
                {
                  $totalscore = round($sp/$qsp*100,2);
                }
                $datainsert = DB::select(DB::raw("insert into mnw_progoti.cluster_temp(cluster_id,area_id,month,year,score,c_associate_id) VALUES('$cluster','$arcode','$mon','$year','$totalscore','$userpin')"));
                $ct++;
            }    
          }  
        }
      }
     $clustername = DB::select(DB::raw("select * from mnw_progoti.cluster where c_associate_id='$userpin'"));
      ?>
            <div class="card-body table-responsive">
              <p class="card-title">Monthwise lowest scoring area list (10%)</p>
              <p class="card-title">Cluster Name : <?php echo $clustername[0]->cluster_name; ?></p>
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>Month</th>
                  <th>Area Code</th>
                </tr>
                <tbody>
                    <?php
                    $totallimit =0;
                    $getlimit = DB::select(DB::raw("select year,month from mnw_progoti.cluster_temp where c_associate_id='$userpin' group by year,month order by year,month ASC"));
                     if(!empty($getlimit))
                     {
                      foreach ($getlimit as $row) 
                      {
                        $cnt =0;
                        $flg =0;
                        $month = $row->month;
                        $year = $row->year;
                        $getcount = DB::select(DB::raw("select count(*) as cnt from mnw_progoti.cluster_temp  where c_associate_id='$userpin' and year='$year' and month='$month'"));
                         if(!empty($getcount))
                         {
                           $cnt = $getcount[0]->cnt;
                           $totallimit =0;
                           if($cnt !=0)
                           {
                            $totallimit =round($cnt/10);
                           }
                         }
                        //echo $totallimit."/";
                    // dd($getcount);

                        $getdata = DB::select(DB::raw("select *  from mnw_progoti.cluster_temp where c_associate_id='$userpin' and year='$year' and month='$month' order by CAST(score as float) ASC limit '$totallimit'"));
        
                        if(!empty($getdata))
                        {
                          foreach ($getdata as $row) 
                          {
                            $arcod = $row->area_id;
                            $arname = DB::select(DB::raw("select * from branch where area_id='$arcod'"));
                            if(!empty($arname))
                            {
                                $areaname = $arname[0]->area_name;
                            }
                            else
                            {
                                $areaname ='';
                            }
                            $mn = $row->month;
                            $yr = $row->year;
                             ?>
                             <tr>
                              <?php
                               if($flg=='0')
                               {
                                ?>
                                 <td rowspan="<?php echo $totallimit; ?>"><?php echo $yr."-".$mn; ?></td>
                                <?php
                                $flg =1;
                               }
                              ?>
                              <td><?php echo $areaname."(".$row->area_id.")"; ?></td>
                             </tr>
                             <?php
                          }
                        }
                      }
                     }
                    ?>
                   </tbody>
                </tr>
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

@section('script')

@endsection