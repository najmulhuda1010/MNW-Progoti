@extends('backend.layouts.master')

@section('title','Zonal Dashboard')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Zonal Dashboard</h5>
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
      $Zonal = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year <= '$cyear' group by year,month order by year DESC, month DESC  limit 6"));
      foreach ($Zonal as $row) 
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
           
            $eventid= $row->id;
            $arcode = $row->area_id;

            $clustersearch = DB::select(DB::raw("select * from mnw_progoti.cluster where area_id='$arcode' and z_associate_id='$userpin'"));
            if(!empty($clustersearch))
            {
                $cluster = $clustersearch[0]->cluster_id;
				$c_associate_id = $clustersearch[0]->c_associate_id;
                //$getpointcluster = DB::select(DB::raw("select sum(point) as p, sum(question_point) as q from mnw_progoti.cal_section_point  where event_id='$eventid' and section ='2' and sub_id='3' or section='4' and sub_id='5' or section='4' and sub_id='6' or section='4' and sub_id='7' or section='4' and sub_id='8' or section='4' and sub_id='9' or section='4' and sub_id='10' or section='4' and sub_id='11' or section='5' and sub_id='3' or section='5' and sub_id='4' or section='5' and sub_id='5' or section='5' and sub_id='16'"));
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
                $datainsert = DB::select(DB::raw("insert into mnw_progoti.cluster_temp(cluster_id,area_id,month,year,score,c_associate_id,z_associate_id) VALUES('$cluster','$arcode','$mon','$year','$totalscore','$c_associate_id','$userpin')"));
                $ct++;
            }    
          }  
        }
      }
	   $zonalname = DB::select(DB::raw("select* from mnw_progoti.zonal where z_associate_id='$userpin'"));
	   if(!empty($zonalname))
	   {
		   $zname = $zonalname[0]->zonal_name;
	   }
	   else
	   {
		   $zname ='';
	   }
      ?>
            <div class="card-body">
               <div class="row">
                   <div class="col-12"> 
                    <p class="card-title float-left"><strong>Monthwise lowest scoring area list (10%)</strong></p>
                    <p class="card-title float-right"><strong>Zone Name : <?php echo $zname; ?></strong></p>
                </div>
                </div>
              <?php
              $cluster_id = DB::select(DB::raw("select cluster_id from mnw_progoti.cluster_temp group by cluster_id order by cluster_id ASC"));
              if(!empty($cluster_id))
              {
               foreach ($cluster_id as $row) 
               {
                 $id  = $row->cluster_id;
                 $totallimit =0;
                 $clustername = DB::select(DB::raw("select * from mnw_progoti.cluster where cluster_id='$id' and z_associate_id='$userpin'"));
                 ?>
                 <div class="row">
                    <div class="col-12"> 
                        <p class="card-title">Cluster : <?php echo $id."(".$clustername[0]->cluster_name.")"; ?></p>
                    </div>
                 </div>
              <div class="table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th width="40%">Month</th>
                  <th width="60%">Area Code</th>
                </tr>
                <tbody>
                    <?php
                    $getlimit = DB::select(DB::raw("select year,month from mnw_progoti.cluster_temp where cluster_id='$id' group by year,month order by year,month ASC"));
                    if(!empty($getlimit))
                    {
                      foreach ($getlimit as $row) 
                      {
                        $cnt =0;
                        $flg =0;
                        $month = $row->month;
                        $year = $row->year;
                        $getcount = DB::select(DB::raw("select count(*) as cnt from mnw_progoti.cluster_temp where cluster_id='$id' and year='$year' and month='$month'"));
                         if(!empty($getcount))
                         {
                           $cnt = $getcount[0]->cnt;
                           $totallimit =0;
                           if($cnt !=0)
                           {
                            $totallimit =round($cnt/10);
                           }
                         }
                        // echo $totallimit."/";
                        $getdata = DB::select(DB::raw("select *  from mnw_progoti.cluster_temp where year='$year' and month='$month' and cluster_id='$id' order by CAST(score as float) ASC limit '$totallimit'"));
      
                        if(!empty($getdata))
                        {
                          foreach ($getdata as $row) 
                          {
                             $mn = $row->month;
                             $year = $row->year;
                             $arnc = $row->area_id;
                             $arname = DB::select(DB::raw("select * from branch where branch_id='$arnc'"));
                              if(!empty($arname))
                              {
                                  $areaname = $arname[0]->area_name;
                              }
                              else
                              {
                                  $areaname ='';
                              }
                             ?>
                             <tr>
                              <?php
                               if($flg=='0')
                               {
                                ?>
                                 <td rowspan="<?php echo $totallimit; ?>"><?php echo $year."-".$mn; ?></td>
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
        </table>
    </div>

         <?php
       }
     }
     ?>
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