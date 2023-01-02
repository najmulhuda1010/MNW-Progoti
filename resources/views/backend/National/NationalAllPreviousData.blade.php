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

@section('title','National Previous Data View')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">National Previous Data View</h5>
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
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th width="50%">Monitoring Event</th>
                  <th width="50%">Result</th>
                </tr>
                <tbody>
                    <?php
                    $month=0;
                    $mnth=date('m');
                    $cmonth = date('m');
                    $cyear = date('Y');
                    $totalscore =0;
                    $g='';$m='';$p='';
                    $ct=0;
                    $score =0;
                    $year ='';
                    $quarter ='';
                    $m='';
                    $mn='';
            
                    if(!$alldata->isEmpty())
                    {
                       foreach($alldata as $row)
                       {
                         $year = $row->year;
                         $quarter = $row->quarterly;
                               //echo $quarter;
                         $m='';
                         $p='';
                         $g='';
                        //  $branchscore = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly='$quarter'"));
                         $areascore = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly='$quarter' and areacompletestatus=1"));
                         if(!empty($areascore))
                         {
                          foreach($areascore as $rw)
                          {
                            $arcode = $rw->area_id;
                                             //echo $brcode."/";
                          //   $cnt= strlen($brcode);
                          //   if($cnt == 3)
                          //   {
                          //    $brcode='0'.$brcode;
                          //  }
                           $myear = $rw->year;
                           $month = $rw->month;
                           if($month <='03')
                           {
            
                            if(($mnth >='01' and $mnth <='03') and ($myear==$cyear))
                            {
            
                                     // echo $mnth;
                            }
                            else
                            {
            
                              $period = $myear.","."JAN"." to ".$myear.","."MAR";
                              $score +=$rw->score;
                              $ct +=1;
                            }
                          }
                          else if($month >='04' and $month <='06')
                          {
                            if(($mnth >='04' and $mnth <='06') and ($year==$cyear))
                            {
            
            
                            }
                            else
                            {
                              $period = $myear.","."APR"." to ".$myear.","."JUN";
                              $score +=$rw->score;
                              $ct +=1;
                            }
                          }
                          else if($month >='07' and $month <='09')
                          {
                            if(($mnth >='07' and $mnth <='09') and ($myear==$cyear))
                            {
            
            
                            }
                            else
                            {
                                    //if()
                              $period = $myear.","."JUL"." to ".$myear.","."SEP";
                              $score +=$rw->score;
                              $ct +=1;
                            }
                          }
                          else if($month >='10' and $month <='12')
                          {
                            if(($mnth >='10' and $mnth <='12') and ($myear==$cyear))
                            {
            
            
                            }
                            else
                            {
            
                              $period = $myear.","."OCT"." to ".$myear.","."DEC";
                              $score +=$rw->score;
                              $ct +=1;
                            }
                          }
                        } 
                      }
                      if($score !=0)
                      {
                       $totalscore = round($score/$ct,2);
                       if($totalscore >=85)
                       {
                         $g = 'Good';
                       }
                       else if($totalscore >=70 and $totalscore < 85)
                       {
                         $m ='Modarate';
                       }    
                       else if($totalscore >=0.1 and $totalscore < 70)
                       {
                         $p = 'Poor';
                       }
                                 //echo $g."/".$m."*".$p;
                       ?>
                       <tr>
                         <td style="color: #3699FF"><a href="NationalDashboard?event=<?php echo $year.",".$quarter; ?>"><?php echo $year."-".$quarter; ?></a></td>
                         <td><?php if($g){
                            echo "Good";
                         } if($m){
                           echo "Moderate";
                         } if($p){
                           echo "Need Improvment";
                           } ?></td>
                       </tr>
                       <?php
                     }
                   }
                 }
                 ?>
                   </tbody>
              </table>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <?php
        if($yr !='' )
        {
          $cycle ='';
          $cycle= $yr."-".$q;
          ?>

        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
              <h3 class="card-title">Monitoring Event: <?php echo $cycle; ?></h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <thead>
                    <?php
                    $cl =0;
                    $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where year='$yr' and quarterly='$q' and areacompletestatus=1 group by month order by month ASC"));
                    
                    if(!empty($month))
                    {
                        foreach ($month as $rw) 
                        {
                          $cl++;
                        }
                    }
                    ?>
                  <tr  class="brac-color-pink">
                    <th rowspan="2" width="15%" style="text-align: center">Section</th>
                    <th rowspan="2" width="20%">Section Name</th>
                    <th colspan="<?php echo $cl; ?>" width="20%" style="text-align: center">Month wise achievement %</th>
                    <th rowspan="2" width="15%">Quarterly achievement %</th>
                  </tr>
                  <tr class="brac-color-pink">
                    <?php 
                    foreach ($month as $row) 
                    {
                    $mon = $row->month;
                        //echo $mon;
                    if($mon =='01'){$j = "JAN";} else if($mon =='02'){$j="FEB";}else if($mon=='03'){$j="MAR";}else if($mon =='04'){$j="APR";}else if($mon=='05'){$j="MAY";}else if($mon =='06'){$j="JUN";}else if($mon=='07'){$j="JUL";}else if($mon=='08'){$j="AUG";}else if($mon=='09'){$j="SEP";}else if($mon=='10'){$j="OCT";}else if($mon=='11'){$j="NOV";}else if($mon =='12'){$j="DEC";}else{$j='0';}
                    ?>
                    <th width="10%" style="text-align: center"><?php echo $j; ?></th>
                    <?php
                    }
                    ?>
                  </tr>
                 </thead>
                 <tbody>
                    <?php
                    $mnthh1=0;
                    $mnthh2=0;
                    $mnthh=0;
                    $monthscore1 =0;
                    $monthscore2 =0;
                    $monthscore3 =0;
                    $monthscore4 =0;
                    $totalscore =0;
                    $sectionpoint =0;
                    $question_point=0;
                    for($i=1; $i<=5; $i++)
                    {
                     $c =0;
                     $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where year='$yr' and quarterly='$q' and areacompletestatus=1 group by month order by month ASC"));
                     if(!empty($month))
                     {
                      $c=0;
                          //$mntt = $month[0]->month;
                      foreach ($month as $row) 
                      {
                        $sectionpoint1 =0;
                        $question_point1 =0;
                        $mn = $row->month;
                        $getmonth = DB::select(DB::raw("select * from mnw_progoti.monitorevents where  year='$yr' and quarterly='$q' and month='$mn' and score !='0'"));
                        if(!empty($getmonth))
                        {
                          foreach ($getmonth as $r) 
                          {
                            $event_id = $r->id;
                            $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                            if(!empty($data))
                            {
                              $sectionpoint1 += $data[0]->sp;
                              $question_point1 += $data[0]->qsp;
                            }
                          }
          
                          if($c =='0')
                          {
                            if($sectionpoint1 !=0)
                            {
                              $monthscore1 = round((($sectionpoint1/$question_point1)*100),2);
                              $mnthh =$mn;  
                            }
          
          
                          }
                          else if($c=='1')
                          {
                            if($sectionpoint1 !=0)
                            {
                              $monthscore2 = round((($sectionpoint1/$question_point1)*100),2);
                              $mnthh1 =$mn;
                            }
                          }
                          else if($c=='2')
                          {
                            if($sectionpoint1 !=0)
                            {
                              $monthscore3 = round((($sectionpoint1/$question_point1)*100),2);
                              $mnthh2 =$mn;
                            }
          
                          }
          
                        }
          
                        $c++;
                      }
                          //echo $mn;
          
                    }
                    $alldata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$yr' and quarterly='$q' and score !='0' and areacompletestatus=1"));
                    $sectionpoint =0;
                    $question_point=0;
                    if(!empty($alldata))
                    {
                     foreach($alldata as $r)
                     {
                      $mnth = $r->month;
                            //echo $mnth;
                      $year = $r->year;
                      $quar= $r->quarterly;
                      $event_id = $r->id;
                      $div_id = $r->division_id;
                      $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                      if(!empty($data))
                      {
                        $sectionpoint += $data[0]->sp;
                        $question_point += $data[0]->qsp;
                      }                 
                    }
                        // echo $sectionpoint."/".$question_point."*";
                         //die();
          
                    if($sectionpoint !=0)
                    {
                     $totalscore = round((($sectionpoint/$question_point)*100),2); 
                   }
                   $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
                   if(empty($secname))
                   {
          
                   }
                   else
                   {
                     $name = $secname[0]->qdesc;
                   }
          
                   if($totalscore !=0)
                   {
                            //echo $mntt;
                    ?>
                    <tr  style="height:20px; color:black;">
                      <td style=" text-align:center;"><a target="_blank" class="btn btn-light" href="DivisionWiseAc?division=<?php echo $i.",".$yr.",".$q.",".$div_id; ?>" style=""><?php echo "Section: ".$i; ?></a></td>
                      <td width="50%" ><?php echo $name; ?></td>
                      <?php
                      if($monthscore1 !=0)
                      {
                                  //echo "Huda";
                        ?>
                        <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh.",".$yr.",".$q.",".$div_id; ?>"><?php echo $monthscore1."%"; ?></a></td>
                        <?php
                      }
                      if($monthscore2 !=0)
                      {
                                  //echo "Huda";
                        ?>
                        <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh1.",".$yr.",".$q.",".$div_id; ?>"><?php echo $monthscore2."%"; ?></a></td>
                        <?php
                      }
                      if($monthscore3 !=0)
                      {
                                  //echo "Huda";
                        ?>
                        <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh2.",".$yr.",".$q.",".$div_id; ?>"><?php echo $monthscore3."%"; ?></a></td>
                        <?php
                      }					
                      ?>
                      <td width="30%"><?php echo $totalscore."%"; ?></td>
                    </tr>
                    <?php
                  }
                }  
              }
              ?>
            </tbody>
              </table>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
              <h3 class="card-title">Area Count</h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                 <tr class="brac-color-pink">
                   <th rowspan="3">Monitoring Event</th>
                   <th rowspan="3">Division Total Ar No</th>
                   <th colspan="2">Total area monitored</th>
                   <th colspan="6">Rating wise area no</th>
                 </tr>
                 <tr  class="brac-color-pink">
                   <th rowspan="2">No</th>
                   <th rowspan="2">% (of all area)</th>
                   <th colspan="2">Good</th>
                   <th colspan="2">Moderate</th>
                   <th colspan="2">Need Improvment</th>
                 </tr>
                 <tr  class="brac-color-pink">
                   <th>No</th>
                   <th>%</th>
                   <th>No</th>
                   <th>%</th>
                   <th>No</th>
                   <th>%</th>
                 </tr>
               </thead>
               <tbody>
                <?php
                $good =0;
                $moderate =0;
                $poor=0;
                $monitorpercent ='';
                $cntdivision = DB::select( DB::raw("select count(*) as cnt from branch where program_id=5"));
                if(!empty($cntdivision))
                {
                  $divcount =  $cntdivision[0]->cnt;
                } 
                // $quarterlist = DB::select( DB::raw("select year,quarterly from mnw_progoti.monitorevents group by year,quarterly order by year,quarterly DESC Limit 4"));
                $quarterlist = DB::select( DB::raw("select year,quarterly from mnw_progoti.monitorevents where areacompletestatus=1 group by year,quarterly order by year,quarterly DESC Limit 4"));
                if(!empty($quarterlist))
                {
                  foreach ($quarterlist as $row) 
                  {
                    $good =0;$moderate =0;$poor=0;$g =0;$m=0;$p=0;$brcnt=0;$score =0;$ct =0;$tscore =0;
                    $years = $row->year;
                    $quarter = $row->quarterly;
                    $monitoredArea = DB::select( DB::raw("select count(*) as arcount from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and score !='0'"));
                    if(!empty($monitoredArea))
                    {
                      $arcnt = $monitoredArea[0]->arcount;
                    }
                    else
                    {
                      $arcnt='';
                    }
                    $monitorpercent = round($arcnt/$divcount*100,2);
                    $goodmodaratepoor = DB::select( DB::raw("select * from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and score !='0'"));
                    if(!empty($goodmodaratepoor))
                    {
                      foreach ($goodmodaratepoor as $rw) 
                      {
                        $score = $rw->score;
                        if($score >= '85')
                        {
                          $g +=1;
                        }
                        else if($score >=70 and $score < 85)
                        {
                          $m +=1;
                        }
                        else if($score < 70)
                        {
                          $p +=1;
                        }
                                  //$ct++;
                      }
                                /*if($ct !=0)
                                {
                                  $tscore = round($score/$ct,2);
                                }
                               // echo $score."/";
                                if($tscore >='85')
                                {
                                  $g +=1;
                                }
                                else if($tscore >='70' and $tscore <='84')
                                {
                                  $m +=1;
                                }
                                else if($tscore < 70)
                                {
                                  $p +=1;
                                }
                                else
                                {
                                  $g =0;
                                  $m=0;
                                  $p=0;
                                }*/
                              }
                              if($g !=0)
                              {
                                $good = round($g/$arcnt*100,2);
                              }
                              if($m !=0)
                              {
                               $moderate = round($m/$arcnt*100,2);
                             }
                             if($p !=0)
                             {
                               $poor = round($p/$arcnt*100,2);
                             }
              
                             ?>
                             <tr>
                              <td><?php echo $years."-".$quarter; ?></td>
                              <td><?php echo $divcount; ?></td>
                              <td><?php echo $arcnt; ?></td>
                              <td><?php echo $monitorpercent." %"; ?></td>
                              <td><?php echo $g; ?></td>
                              <td><?php echo $good." %"; ?></td>
                              <td><?php echo $m; ?></td>
                              <td><?php echo $moderate." %"; ?></td>
                              <td><?php echo $p; ?></td>
                              <td><?php echo $poor." %"; ?></td>
                            </tr>
                            <?php
                          }
                        }
                        ?>
                      </tbody>
              </table>
              <?php
    }
    else
    {
      $cycle ='';
      $cycl = DB::select( DB::raw("select year,quarterly from mnw_progoti.monitorevents where areacompletestatus=1 group by year, quarterly order by year DESC, quarterly DESC LIMIT 1"));
      if(!empty($cycl))
      {
        $y =$cycl[0]->year;
        $q= $cycl[0]->quarterly;
        $cycle= $cycl[0]->year."-".$cycl[0]->quarterly;
      }
      ?>
     <div class="col-md-12">
        <div class="card card-custom gutter-b">
          <!--begin::Form-->
          <div class="card-header">
            <h3 class="card-title">Monitoring Event: <?php echo $yr."-".$q; ?></h3>
          </div><!-- /.box-header -->
          <div class="card-body table-responsive">
            <table style="text-align: center;font-size:13" class="table table-bordered">
              <thead>
     <?php
     $cl =0;
     $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where year='$y' and quarterly='$q' and areacompletestatus=1 group by month order by month ASC"));
     if(!empty($month))
     {
      foreach ($month as $rw) 
      {
        $cl++;
      }
    }
    ?>
    <tr  class="brac-color-pink">
        <th rowspan="2" width="15%" style="text-align: center">Section</th>
        <th rowspan="2" width="20%">Section Name</th>
        <th colspan="<?php echo $cl; ?>" width="20%" style="text-align: center">Month wise achievement %</th>
        <th rowspan="2" width="15%">Quarterly achievement %</th>
      </tr>
      <tr class="brac-color-pink">
          <?php 
          foreach ($month as $row) 
          {
            $mon = $row->month;
            if($mon =='01'){$j = "JAN";} else if($mon =='02'){$j="FEB";}else if($mon=='03'){$j="MAR";}else if($mon =='04'){$j="APR";}else if($mon=='05'){$j="MAY";}else if($mon =='06'){$j="JUN";}else if($mon=='07'){$j="JUL";}else if($mon=='08'){$j="AUG";}else if($mon=='09'){$j="SEP";}else if($mon=='10'){$j="OCT";}else if($mon=='11'){$j="NOV";}else if($mon =='12'){$j="DEC";}else{$j='0';}
            ?>
            <th width="10%"><?php echo $j; ?></th>
            <?php
          }
          ?>
        </tr>
      </thead>
      <tbody>
        <?php
        $mnthh1=0;
        $mnthh2=0;
        $mnthh=0;
        $monthscore1 =0;
        $monthscore2 =0;
        $monthscore3 =0;
        $monthscore4 =0;
        $totalscore =0;
        $sectionpoint =0;
        $question_point=0;
        for($i=1; $i<=5; $i++)
        {
         $c =0;
         $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where year='$y' and quarterly='$q' and areacompletestatus=1 group by month order by month ASC"));
         if(!empty($month))
         {
          $c=0;
                //$mntt = $month[0]->month;
          foreach ($month as $row) 
          {
            $sectionpoint1 =0;
            $question_point1 =0;
            $mn = $row->month;
            $getmonth = DB::select(DB::raw("select * from mnw_progoti.monitorevents where  year='$y' and quarterly='$q' and month='$mn' and score !='0'"));
            if(!empty($getmonth))
            {
              foreach ($getmonth as $r) 
              {
                $event_id = $r->id;
                $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                if(!empty($data))
                {
                  $sectionpoint1 += $data[0]->sp;
                  $question_point1 += $data[0]->qsp;
                }
              }

              if($c =='0')
              {
                if($sectionpoint1 !=0)
                {
                  $monthscore1 = round((($sectionpoint1/$question_point1)*100),2);
                  $mnthh =$mn;  
                }


              }
              else if($c=='1')
              {
                if($sectionpoint1 !=0)
                {
                  $monthscore2 = round((($sectionpoint1/$question_point1)*100),2);
                  $mnthh1 =$mn;
                }
              }
              else if($c=='2')
              {
                if($sectionpoint1 !=0)
                {
                  $monthscore3 = round((($sectionpoint1/$question_point1)*100),2);
                  $mnthh2 =$mn;
                }

              }

            }

            $c++;
          }
                //echo $mn;

        }
			 //echo $y."/".$q;
        $alldata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$y' and quarterly='$q' and score !='0' and areacompletestatus=1"));
        $sectionpoint =0;
        $question_point=0;
        if(!empty($alldata))
        {
         foreach($alldata as $r)
         {
          $mnth = $r->month;
                  //echo $mnth;
          $year = $r->year;
          $quar= $r->quarterly;
          $event_id = $r->id;
          $div_id = $r->division_id;
          $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
          if(!empty($data))
          {
            $sectionpoint += $data[0]->sp;
            $question_point += $data[0]->qsp;
          }                 
        }
              // echo $sectionpoint."/".$question_point."*";
               //die();

        if($sectionpoint !=0)
        {
         $totalscore = round((($sectionpoint/$question_point)*100),2); 
       }
       $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
       if(empty($secname))
       {

       }
       else
       {
         $name = $secname[0]->qdesc;
       }

       if($totalscore !=0)
       {
                  //echo $mntt;
        ?>
        <tr  style="height:20px; color:black;">
          <td style=" text-align:center;"><a target="_blank" class="btn btn-light" href="DivisionWiseAc?division=<?php echo $i.",".$y.",".$q.",".$div_id; ?>" style=""><?php echo "Section: ".$i; ?></a></td>
          <td width="50%" ><?php echo $name; ?></td>
          <?php
          if($monthscore1 !=0)
          {
            ?>
            <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh.",".$y.",".$q.",".$div_id; ?>"><?php echo $monthscore1."%"; ?></a></td>
            <?php
          }
          if($monthscore2 !=0)
          {
            ?>
            <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh1.",".$y.",".$q.",".$div_id; ?>"><?php echo $monthscore2."%"; ?></a></td>
            <?php
          }
          if($monthscore3 !=0)
          {
            ?>
            <td style="text-align:center;"><a target="_blank" class="btn btn-light" href="monthDivisionWise?month=<?php echo $i.",".$mnthh2.",".$y.",".$q.",".$div_id; ?>"><button><?php echo $monthscore3."%"; ?></a></td>
            <?php
          } 
          ?>

          <td width="30%"><?php echo $totalscore."%"; ?></td>
        </tr>
        <?php
      }
    }  
  }
  ?>
</tbody>
</table>
</div>
<!--end::Form-->
</div>
<!--end::Advance Table Widget 4-->
</div>
<div class="col-md-12">
<div class="card card-custom gutter-b">
<!--begin::Form-->
<div class="card-header">
<h3 class="card-title">Area Count</h3>
</div><!-- /.box-header -->
<div class="card-body table-responsive">
<table style="text-align: center;font-size:13" class="table table-bordered" width="100%" cellspacing="0">
  <thead>
   <tr class="brac-color-pink">
     <th rowspan="3">Monitoring Event</th>
     <th rowspan="3">Division Total Ar No</th>
     <th colspan="2">Total area monitored</th>
     <th colspan="6">Rating wise area no</th>
   </tr>
   <tr  class="brac-color-pink">
     <th rowspan="2">No</th>
     <th rowspan="2">% (of all area)</th>
     <th colspan="2">Good</th>
     <th colspan="2">Moderate</th>
     <th colspan="2">Need Improvment</th>
   </tr>
   <tr  class="brac-color-pink">
     <th>No</th>
     <th>%</th>
     <th>No</th>
     <th>%</th>
     <th>No</th>
     <th>%</th>
   </tr>
 </thead>
<tbody>
  <?php
  $months = date('m');
  $good =0;
  $moderate =0;
  $poor=0;
  $monitorpercent ='';
  $cntdivision = DB::select( DB::raw("select count(*) as cnt from branch where program_id=5"));
  if(!empty($cntdivision))
  {
    $divcount =  $cntdivision[0]->cnt;
  }
  if($months >='01' and $months <='03')
  {

  }
  else if($months >='06' and $months <='06')
  {

  }
			//else 
  $quarterlist = DB::select( DB::raw("select year,quarterly from mnw_progoti.monitorevents where areacompletestatus=1 group by year,quarterly order by year,quarterly DESC Limit 3"));
  if(!empty($quarterlist))
  {
    foreach ($quarterlist as $row) 
    {
      $good =0;$moderate =0;$poor=0;$g =0;$m=0;$p=0;$brcnt=0;$score =0;$ct =0;$tscore =0;
      $years = $row->year;
				//echo $years;
      $quarter = $row->quarterly;
      $monitoredArea = DB::select( DB::raw("select count(*) as arcount from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and score !='0'"));
      if(!empty($monitoredArea))
      {
        $arcnt = $monitoredArea[0]->arcount;
      }
      else
      {
        $arcnt='';
      }
      $monitorpercent = round($arcnt/$divcount*100,2);
      $goodmodaratepoor = DB::select( DB::raw("select * from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and score !='0'"));
      if(!empty($goodmodaratepoor))
      {
        foreach ($goodmodaratepoor as $rw) 
        {
          $score = $rw->score;
          if($score >= '85')
          {
            $g +=1;
          }
          else if($score >=70 and $score < 85)
          {
            $m +=1;
          }
          else if($score < 70)
          {
            $p +=1;
          }
                    //$ct++;
        }
                  /*if($ct !=0)
                  {
                    $tscore = round($score/$ct,2);
                  }
                 // echo $score."/";
                  if($tscore >='85')
                  {
                    $g +=1;
                  }
                  else if($tscore >='70' and $tscore <='84')
                  {
                    $m +=1;
                  }
                  else if($tscore < 70)
                  {
                    $p +=1;
                  }
                  else
                  {
                    $g =0;
                    $m=0;
                    $p=0;
                  }*/
                }
                if($g !=0)
                {
                 $good = round($g/$arcnt*100,2);
               }
               if($m !=0)
               {
                $moderate = round($m/$arcnt*100,2);
              }
              if($p !=0)
              {
               $poor = round($p/$arcnt*100,2);
             }



             ?>
             <tr>
              <td><?php echo $years."-".$quarter; ?></td>
              <td><?php echo $divcount; ?></td>
              <td><?php echo $arcnt; ?></td>
              <td><?php echo $monitorpercent." %"; ?></td>
              <td><?php echo $g; ?></td>
              <td><?php echo $good." %"; ?></td>
              <td><?php echo $m; ?></td>
              <td><?php echo $moderate." %"; ?></td>
              <td><?php echo $p; ?></td>
              <td><?php echo $poor." %"; ?></td>
            </tr>
            <?php
          }
        }
        ?>
      </tbody>
    </table>
    <?php
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