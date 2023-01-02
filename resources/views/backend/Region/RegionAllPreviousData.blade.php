<?php 
$username = Session::get('username');
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

@section('title','Region Previous Data View')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Region Previous Data View</h5>
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
              <table style="text-align: center;font-size:13" style="font-size: 13;" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>Monitoring Event</th>
                  <th>Monitoring Period</th>
                  <th>Result</th>
                </tr>
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
                    if(!empty($alldata))
                    {
                        foreach($alldata as $row)
                        {
                        $year = $row->year;
                        $quarter = $row->quarterly;
                        //$brcode = $row->branchcode;
                                //echo $quarter;
                        $m='';
                        $p='';
                        $g='';
                        $score =0;
                        $ct =0;
                        $checkbrnch= DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly = '$quarter' and region_id='$region_id'"));
                        // dd($checkbrnch);
                        if(!empty($checkbrnch))
                        {
                            $myear = $checkbrnch[0]->year;
                                //echo $myear;
                            $month = $checkbrnch[0]->month;
                                //  echo $month."/".$cmonth;
                                if($month <='03')
                              {
                                
                                  if(($mnth >='01' and $mnth <='03') and ($myear==$cyear))
                                {
                                  $period = $myear.","."JAN"." to ".$myear.","."MAR";
                                  
                                  if($checkbrnch[0]->score==null){
                                    $ongoing=1;
                                  }
                                  $score =$checkbrnch[0]->score;
                                  $ct +=1;
                                }
                                else
                                {
                                  
                                  $period = $myear.","."JAN"." to ".$myear.","."MAR";
                                  $score +=$checkbrnch[0]->score;
                                        $ct +=1;
                                }
                              }
                              else if($month >='04' and $month <='06')
                              {
                                  if(($mnth >='04' and $mnth <='06') and ($year==$cyear))
                                {
                                  $period = $myear.","."APR"." to ".$myear.","."JUN";
                                  
                                  if($checkbrnch[0]->score==null){
                                    $ongoing=1;
                                  }
                                  $score =$checkbrnch[0]->score;
                                  $ct +=1;
                                }
                                else
                                {
                                  $period = $myear.","."APR"." to ".$myear.","."JUN";
                                  $score +=$checkbrnch[0]->score;
                                        $ct +=1;
                                }
                              }
                              else if($month >='07' and $month <='09')
                              {
                                if(($mnth >='07' and $mnth <='09') and ($myear==$cyear))
                                {
                                  $period = $myear.","."JUL"." to ".$myear.","."SEP";
                                  if($checkbrnch[0]->score==null){
                                    $ongoing=1;
                                  }
                                  $score =$checkbrnch[0]->score;
                                  $ct +=1;
                                }
                                else
                                {
                                  //if()
                                  $period = $myear.","."JUL"." to ".$myear.","."SEP";
                                  $score +=$checkbrnch[0]->score;
                                        $ct +=1;
                                }
                              }
                              else if($month >='10' and $month <='12')
                              {
                                if(($mnth >='10' and $mnth <='12') and ($myear==$cyear))
                                {
                                  $period = $myear.","."OCT"." to ".$myear.","."DEC";
                                  if($checkbrnch[0]->score==null){
                                    $ongoing=1;
                                  }
                                  $score =$checkbrnch[0]->score;
                                  $ct +=1;
                                }
                                else
                                {
                                
                                  $period = $myear.","."OCT"." to ".$myear.","."DEC";
                                  $score +=$checkbrnch[0]->score;
                                        $ct +=1;
                                }
                              }
                        }
                        if($score !=0 or $score==null)
                          {
                            // dd($score/1);

                            if($score!=null){
                              $totalscore = round($score/$ct,2);
                            }

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
                              $p = 'Need Improvment';
                            }elseif($score==null){
                              $current='current';
                            }
                            //echo $g."/".$m."*".$p;
                            ?>
                           <tr>
                             <td>
                             <a style="color: #3699FF" href="RegionDashboard?event=<?php echo $year."-".$quarter."-".$region_id; ?>"><?php echo $year."-".$quarter; ?></a></td>
                           
                             <td><?php echo $period; ?></td>
                             <td><?php if($g){
                                      echo "Good";
                                  } if($m){
                                  echo "Moderate";
                                  } if($p){
                                  echo "Need Improvment";
                                  } if($score==null){
                                    echo "Ongoing";
                                  }?></td>
                           </tr>
                          <?php
                          }

                                //echo $score."/".$ct."-".$year."*".$quarter;
                                //die();
                        }
                    }
                    ?>
              </table>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <?php
          if($yr !='' and $q !='')
          {
             $evyear ='';
             $evquarter ='';
             $totalscore3=0;$totalscore2=0;$totalscore4=0;
             $ct2=0;$ct1=0;$ct3=0;$ct4=0;
             $quarter ='';
             $year  ='';
             $y = date('Y');
             $mon = date('m');
             $checkLast= DB::select(DB::raw("select * from mnw_progoti.monitorevents where region_id='$region_id' and areacompletestatus =1 and year='$yr' and quarterly='$q'"));
             if(!empty($checkLast))
             {
       
               $year = $checkLast[0]->year;
               $quarter = $checkLast[0]->quarterly;
             }
             ?>



            <div class="card-header">
              <h3 class="card-title">Monitoring Event: <?php echo $year."-".$quarter; ?> </h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" style="font-size: 13;" class="table">
                <tr class="brac-color-pink">
                  <th>Section</th>
                  <th width="60%">Section Name</th>
                  <th width="20%">Achievement %</th>
                </tr>
              </table>
              <?php
                  $totalscore =0;
                  $cycle = $year."-".$quarter;
                  $cyear = date('Y');       
                         //$mnth =date('m');
                  $sectionpoint =0;
                  $question_point=0;
                  $data = array();
                  $exp = explode('-',$cycle);
                  $evyear = $exp[0];
                  $quarter = $exp[1];
                  $regions = DB::select(DB::raw("select * from mnw_progoti.monitorevents where region_id='$region_id' and year='$evyear' and quarterly='$quarter'"));
                  if(!empty($regions))
                  {
                   for($i=1; $i <=5; $i++)
                   {
                      $sectionpoint =0;
                      $question_point=0;
                      foreach($regions as $r)
                      {
                        $mnth = $r->month;
                                  //echo $mnth;
                        $year = $r->year;
                        $quar= $r->quarterly;
                        $event_id = $r->id;
                        $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                        if(!empty($data))
                        {
                          $sectionpoint += $data[0]->sp;
                          $question_point += $data[0]->qsp;
                        }                 
                      }
                             //echo $sectionpoint."/".$question_point."*";
                             //die();
            
                    if($sectionpoint !=0)
                    {
                     $totalscore = round(($sectionpoint/$question_point)*100,2); 
                    }
                    // if($i==3){
                    //   dd(round(($sectionpoint/$question_point)*100,2));
                    // }

                   $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no=$i and sub_sec_no='0' and qno=0"));
                   if(empty($secname))
                   {
            
                   }
                   else
                   {
                     $name = $secname[0]->qdesc;
                   }
                   ?>
                   <table style="text-align: center;font-size:13" class="table table-striped dt-responsive nowrap" cellspacing="0" width="100%">
                    <?php
                    if($totalscore !=0)
                    {
                      ?>
                      <tr>
                        <td nowrap="nowrap"><button id="<?php echo $i; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $i; ?>);" >+</button>
                          <a><?php echo "Section: ".$i; ?></a></td>
                          <td width="50%" ><?php echo $name; ?></td>
                          <td width="30%" ><?php echo $totalscore."%"; ?></td>
                        </tr>
                        <?php
                    }
                      ?>
                    </table>
                    <table style="text-align: center;font-size:13" id="<?php echo "dv".$i; ?>" class="table dt-responsive nowrap" cellspacing="0" width="100%"> 
                      <thead>
                        <tr class="brac-color-pink">
                          <th style="text-align:center;">Area Name</th>
                          <th style="">Achievement %</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $regiondata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly='$quarter' and region_id='$region_id'"));
                                  // var_dump($areadata);
                        $d = count($regiondata);
                        $c = round($d/2);
            
                                 // $sec = $sect;
            
                        foreach($regiondata as $row)
                        {
                         $sp =0;
                         $qsp=0;
                         $arcode = $row->area_id;
                                     //echo $brcode."/";
                        //  $cnt = strlen($brcode);
                        //  if($cnt ==3)
                        //  {
                        //    $brcode = '0'.$brcode;
                        //  }
                         $mnth = $row->month;
                                      //echo $mnth;
                         $year = $row->year;
                         $quar= $row->quarterly;
                         $event_id = $row->id;
                                    //echo $event_id."/";
                         $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
            
                          if(!empty($data))
                          {
                            $sp =$data[0]->sp;
                            $qsp =$data[0]->qsp;
                          }
                          $score =0;
                          if($sp !=0)
                          {
                            $score =round(($sp/$qsp)*100,2);
                          }
                                    //echo $event_id."-".$sp."/".$score."*";
                                    //die();
                        $ct++;
                        $name ='';
                        $arean='';
                        $brname ='';
                        $areaname = DB::select( DB::raw("select * from branch where area_id='$arcode'"));
                        if(!empty($areaname))
                        {
                          $arname = $areaname[0]->area_name;
                        }
                        ?>
                        <tr>
                            <td width="10%" style="color: black; text-align:center;"><button class="btn btn-light" onClick="sectionDiv(<?php echo $event_id; ?>,<?php echo $i; ?>);"><?php echo $arname; ?></button></td>
                          <td width="10%" align="center"><?php echo $score."%"; ?></td>
                        </tr>
                        <?php
                        }
                      ?>
                    <?php 
                    foreach($regiondata as $row)
                    {
                        $event_id=$row->id;
                 ?>
                 <table style="text-align: center;font-size:13" id="{{ $event_id."_".$i."_1"  }}" class="sectionView1" style="display: none">
                    <th>Area Name:- <span id="{{ $event_id."_".$i."_areaname"  }}"></span> </th>
                 </table> 
                    <table style="text-align: center;font-size:13" id="{{ $event_id."_".$i."_2"  }}" style="display: none" class="table dt-responsive nowrap sectionView2" cellspacing="0" width="100%"> 
                        <thead>
                                <tr class="brac-color-pink">
                                <th style="">SL</th>
                                <th style=" width: 60%;">Details</th>
                                <th style=" width: 20%;">Achievement %</th>
                                </tr>
                        </thead>
                            <tbody>
                                
                            </tbody> 
                        </table>
                    </tbody> 
                    </table> 
                @php
                }
                @endphp
                  <?php
                  }
                }
               }
               else
               {
                  $evyear ='';
                  $evquarter ='';
                  $totalscore3=0;$totalscore2=0;$totalscore4=0;
                  $ct2=0;$ct1=0;$ct3=0;$ct4=0;
                  $quarter ='';
                  $year  ='';
                  $y = date('Y');
                  $mon = date('m');
                  $checkLast= DB::select(DB::raw("select * from mnw_progoti.monitorevents where region_id='$region_id' and areacompletestatus =1 order by id DESC LIMIT 1"));
                  if(!empty($checkLast))
                  {
            
                    $year = $checkLast[0]->year;
                    $quarter = $checkLast[0]->quarterly;
                  }
                  ?>
                  <div class="card-header">
                    <h3 class="card-title">Monitoring Event: <?php echo $year."-".$quarter; ?> </h3>
                  </div><!-- /.box-header -->
                  <div class="card-body table-responsive">
                    <table style="text-align: center;font-size:13" style="font-size: 13;" class="table">
                      <tr class="brac-color-pink">
                        <th>Section</th>
                        <th width="60%">Section Name</th>
                        <th width="20%">Achievement %</th>
                      </tr>
                    </table>
                  <?php
                  $totalscore =0;
                  $cycle = $year."-".$quarter;
                  $cyear = date('Y');       
                         //$mnth =date('m');
                  $sectionpoint =0;
                  $question_point=0;
                  $data = array();
                  $exp = explode('-',$cycle);
                  $evyear = $exp[0];
                  $quarter = $exp[1];
                  $regions = DB::select(DB::raw("select * from mnw_progoti.monitorevents where region_id='$region_id' and year='$evyear' and quarterly='$quarter'"));
                  if(!empty($regions))
                  {
                   for($i=1; $i <=5; $i++)
                   {
                      $sectionpoint =0;
                      $question_point=0;
                      foreach($regions as $r)
                      {
                        $mnth = $r->month;
                                  //echo $mnth;
                        $year = $r->year;
                        $quar= $r->quarterly;
                        $event_id = $r->id;
                        $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                        if(!empty($data))
                        {
                          $sectionpoint += $data[0]->sp;
                          $question_point += $data[0]->qsp;
                        }                 
                      }
                             //echo $sectionpoint."/".$question_point."*";
                             //die();
            
                    if($sectionpoint !=0)
                    {
                     $totalscore = round((($sectionpoint/$question_point)*100),2); 
                    }
                   $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no=$i and sub_sec_no='0' and qno=0"));
                   if(empty($secname))
                   {
            
                   }
                   else
                   {
                     $name = $secname[0]->qdesc;
                   }
                   ?>
                   <table style="text-align: center;font-size:13" class="table table-striped dt-responsive nowrap" cellspacing="0" width="100%">
                    <?php
                    if($totalscore !=0)
                    {
                      ?>
                      <tr>
                        <td nowrap="nowrap"><button id="<?php echo $i; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $i; ?>);" >+</button>
                          <a ><?php echo "Section: ".$i; ?></a></td>
                          <td width="50%" ><?php echo $name; ?></td>
                          <td width="30%"  ><?php echo $totalscore."%"; ?></td>
                        </tr>
                        <?php
                    }
                      ?>
                    </table>
                    <table style="text-align: center;font-size:13" id="<?php echo "dv".$i; ?>" class="table dt-responsive nowrap" cellspacing="0" width="100%"> 
                      <thead>
                        <tr class="brac-color-pink">
                          <th style="text-align:center;">Area Name</th>
                          <th style="">Achievement %</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $regiondata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly='$quarter' and region_id='$region_id'"));
                                  // var_dump($areadata);
                        $d = count($regiondata);
                        $c = round($d/2);
            
                                 // $sec = $sect;
            
                        foreach($regiondata as $row)
                        {
                         $sp =0;
                         $qsp=0;
                         $arcode = $row->area_id;
                                     //echo $brcode."/";
                        //  $cnt = strlen($brcode);
                        //  if($cnt ==3)
                        //  {
                        //    $brcode = '0'.$brcode;
                        //  }
                         $mnth = $row->month;
                                      //echo $mnth;
                         $year = $row->year;
                         $quar= $row->quarterly;
                         $event_id = $row->id;
                                    //echo $event_id."/";
                         $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
            
                          if(!empty($data))
                          {
                            $sp =$data[0]->sp;
                            $qsp =$data[0]->qsp;
                          }
                          $score =0;
                          if($sp !=0)
                          {
                            $score =round((($sp*100)/$qsp),2);
                          }
                                    //echo $event_id."-".$sp."/".$score."*";
                                    //die();
                        $ct++;
                        $name ='';
                        $arean='';
                        $brname ='';
                        $areaname = DB::select( DB::raw("select * from branch where area_id='$arcode'"));
                        if(!empty($areaname))
                        {
                          $arname = $areaname[0]->area_name;
                        }
                        ?>
                        <tr>
                            <td width="10%" style="color: black; text-align:center;"><button class="btn btn-light" onClick="sectionDiv(<?php echo $event_id; ?>,<?php echo $i; ?>);"><?php echo $arname; ?></button></td>
                          <td width="10%" align="center"><?php echo $score."%"; ?></td>
                        </tr>
                        <?php
                        }
                      ?>
                    <?php 
                    foreach($regiondata as $row)
                    {
                        $event_id=$row->id;
                    ?>
                    <table style="text-align: center;font-size:13" id="{{ $event_id."_".$i."_1"  }}" class="sectionView1" style="display: none">
                        <th>Area Name:- <span id="{{ $event_id."_".$i."_areaname"  }}"></span> </th>
                    </table> 
                        <table style="text-align: center;font-size:13" id="{{ $event_id."_".$i."_2"  }}" style="display: none" class="table dt-responsive nowrap sectionView2" cellspacing="0" width="100%"> 
                            <thead>
                                    <tr class="brac-color-pink">
                                    <th style="">SL</th>
                                    <th style=" width: 60%;">Details</th>
                                    <th style=" width: 20%;">Achievement %</th>
                                    </tr>
                            </thead>
                                <tbody>
                                    
                                </tbody> 
                            </table>
                        </tbody> 
                        </table> 
                    @php
                    }
                    @endphp
                  <?php
               }
                
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
<script>
  $(document).ready(function(){
   $("#dv1").hide();
   $("#dv2").hide();
   $("#dv3").hide();
   $("#dv4").hide();
   $("#dv5").hide();
   $(".sectionView1").hide(); 
     $(".sectionView2").hide(); 
 });

 function sectionDiv(event,serial){
      $(".sectionView1").hide(); 
      $(".sectionView2").hide(); 
      $("#" +event+"_"+serial+"_1").show(); 
      $("#" +event+"_"+serial+"_2").show(); 
      $.ajax({
          type: 'GET',
          url: '/mnwprogoti/SectionDetails?section='+serial+','+event,
          dataType: 'json',
          success: function (data) {
              $("#" +event+"_"+serial+"_2 tbody").empty();
              console.log(data.serials.length);
              $("#" +event+"_"+serial+"_areaname").text(data.areaname); 
              if(data.serials.length==0){
                  $("#"+event+"_"+serial+"_2 tbody").append('<tr><td colspan="3" align="center">No data available</td></tr>')
              }else{
                  for(var i=0; i<data.serials.length; i++){
                      var section    =data.serials[i].section
                      var subsection =data.serials[i].sub_id
                      var question   =data.questions[i]
                      var score      =data.scores[i]
                      
                      if(section==1 && subsection==5){
                        var div="<tr><td><a href='frauddocuments?event="+event+"'>"
                        div += section + "."
                        div += subsection
                        div += "</a></td><td>"
                        div += question
                        div += "</td><td>"
                        div += score
                        div += "%</td></tr>"
                      }else{
                        var div="<tr><td>"
                        div += section + "."
                        div += subsection
                        div += "</td><td>"
                        div += question
                        div += "</td><td>"
                        div += score
                        div += "%</td></tr>"
                      }

                      $("#"+event+"_"+serial+"_2 tbody").append(div)
                  }
              }
          },
          error: function (ex) {
              alert('Failed to retrieve Period.');
          }
      });
  }

  function getDiv(judu){
      $(".sectionView1").hide(); 
     $(".sectionView2").hide();  
   var button_text = $('#' + judu).text();
   if(button_text == '+')
   {
    for (var i = 1; i < 6; i++)
    {
      if(judu == i)
      {
        $("#dv" + judu).show();
        $('#' + judu).html('-');

      }
      else
      {
        $("#dv" + i).hide();       
        $('#' + i).html('+');
      }
    }

  }
  else
  {
    $('#dv' + judu).hide();
    $('#' + judu).html('+');
  }
}

</script>
@endsection