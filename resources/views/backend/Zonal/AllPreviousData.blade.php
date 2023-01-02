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

@section('title','Zonal Previous Data')

@section('style')
<style>
.card-title-1rem{
    margin-bottom: 1rem;
}
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
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Zonal Previous Data</h5>
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
      $cyear = $year;
      $currentyear=date('Y');
      $cmonth1 = $month1;
      $cmonth2 = $month2;
      $clusterfilter = $cluster;
      $i=0;
      $ct =0;
      $truncate = DB::select(DB::raw("truncate mnw_progoti.cluster_temp RESTART IDENTITY"));
      if($cyear!=null and $cmonth1==null and $cmonth2==null){
        $Zonal = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year = '$cyear' group by year,month order by year DESC, month DESC  limit 12"));
      }elseif($cyear!=null and $cmonth1!=null and $cmonth2==null){
        $Zonal = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year = '$cyear' and month='$cmonth1' group by year,month order by year DESC, month DESC  limit 12"));
      }elseif($cyear!=null and $cmonth1!=null and $cmonth2!=null){
        $Zonal = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year = '$cyear' and month between '$cmonth1' and '$cmonth2' group by year,month order by year DESC, month DESC  limit 12"));
      }else{
        $Zonal = DB::select(DB::raw("select year,month from mnw_progoti.monitorevents where year <='$currentyear' group by year,month order by year DESC, month DESC  limit 12"));
      }
    //   dd($Zonal);
      foreach ($Zonal as $row) 
      {
        $ct =0;
        $mon = $row->month;
        $year = $row->year;
        //echo $mon;
         $i++;
        // if($mon == $cmonth && $year==$cyear)
        // {
        //    continue;
        // }
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
            if($clusterfilter==null){
                $clustersearch = DB::select(DB::raw("select * from mnw_progoti.cluster where area_id='$arcode' and z_associate_id='$userpin'"));
            }else {
                $clustersearch = DB::select(DB::raw("select * from mnw_progoti.cluster where area_id='$arcode' and z_associate_id='$userpin' and cluster_id='$clusterfilter'"));
            }

            // $clustersearch = DB::select(DB::raw("select * from mnw_progoti.cluster where branch_code='$brcode' and z_associate_id='$userpin'"));


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
            <div class="card-body table-responsive">
              <p class="card-title-1rem">Monthwise lowest scoring branch list (10%)</p>
              <div class="row">
                <div class="col-md-2">
                  <p class="card-title">Zone Name : <?php echo $zname; ?></p>
                </div>
                <?php
                if($cyear!=null){
                    ?>
                <div class="col-md-1">
                  <p class="card-title">Year : <?php echo $cyear; ?></p>
                </div>
                <?php
            }
            if($cmonth1!=null){
            ?>
                <div class="col-md-3">
                  <p class="card-title">Month : 
                    <?php 
                  if($cmonth1=='01'){
                    echo "January";
                  }elseif($cmonth1=='02'){
                    echo "February";
                  }elseif($cmonth1=='03'){
                    echo "March";
                  }elseif($cmonth1=='04'){
                    echo "April";
                  }elseif($cmonth1=='05'){
                    echo "May";
                  }elseif($cmonth1=='06'){
                    echo "June";
                  }elseif($cmonth1=='07'){
                    echo "July";
                  }elseif($cmonth1=='08'){
                    echo "August";
                  }elseif($cmonth1=='09'){
                    echo "September";
                  }elseif($cmonth1=='10'){
                    echo "October";
                  }elseif($cmonth1=='11'){
                    echo "November";
                  }elseif($cmonth1=='12'){
                    echo "December";
                  }
                ?>
                <?php
            }
            if($cmonth2!=null and $cmonth1!=null){
            ?>  
                    to 
                    <?php 
                    if($cmonth2=='01'){
                      echo "January";
                    }elseif($cmonth2=='02'){
                      echo "February";
                    }elseif($cmonth2=='03'){
                      echo "March";
                    }elseif($cmonth2=='04'){
                      echo "April";
                    }elseif($cmonth2=='05'){
                      echo "May";
                    }elseif($cmonth2=='06'){
                      echo "June";
                    }elseif($cmonth2=='07'){
                      echo "July";
                    }elseif($cmonth2=='08'){
                      echo "August";
                    }elseif($cmonth2=='09'){
                      echo "September";
                    }elseif($cmonth2=='10'){
                      echo "October";
                    }elseif($cmonth2=='11'){
                      echo "November";
                    }elseif($cmonth2=='12'){
                      echo "December";
                    }
                    ?></p>
                </div>
              <?php
            }
           ?>
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
              <p class="card-title-1rem">Cluster {{$id}} : <?php echo "(".$clustername[0]->cluster_name.")"; ?></p>
              <div class="table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th width="40%">Month</th>
                  <th width="40%">Area Code</th>
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
                             $arname = DB::select(DB::raw("select * from branch where area_id='$arnc'"));
                              if(!empty($brname))
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
                    <?php
                  }
                }
                  ?>
            <!--end::Form-->
          </div>
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