<?php
$username = Session::get('username');
if ($username == '') {

?>
  <script>
    window.location.href = 'logout';
  </script>

  <?php

}
$calculation = 1;
$dataload1 = "true";
$dataload1 = $dataload;
if ($dataload1 == "true") {

  if ($calculation == 1) {
  ?>

    @include('backend.DataProcessing.DataProcessing')
    <script type="text/javascript">
      //window.location = "BRDashboard?dataload=false;"
      //alert("test");
    </script>

<?php
    $speed = 0;
  }
}
if (isset($_GET['dataload'])) {
  $calculation = $_GET['dataload'];
  if ($calculation == "false") {
    $calculation = 0;
  }
}


?>
@extends('backend.layouts.master')

@section('title','Division Dashboard')

@section('content')
<?php
$calculation = 1;
$dataload1 = "true";
//echo $dataload1;
$dataload1 = $dataload;
if ($dataload1 == "true") {
  //echo "Test";
  if ($calculation == 1) {
  }
  $mont = date('m');
  $yar = date('Y');
  $cdate = date('Y-m-d');
  $completestatus = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and dateend < '$cdate'"));
  if (!empty($completestatus)) {
    foreach ($completestatus as $r) {
      $month  = $r->month;
      $eid = $r->id;
      $area_id = $r->area_id;
      $year = $r->year;
      $checkstatus = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and id='$eid' and areacompletestatus=1"));
      if (empty($checkstatus)) {
        if ($mont >= '01' and $mont <= '03') {
          if (($month >= '01' and $month <= '03') and $yar == $year) {
          } else {
            if ($month < '01') {
              $updateareacompletestatus = DB::table('mnw_progoti.monitorevents')->where('id', $eid)->where('area_id', $area_id)->update(['areacompletestatus' => 1]);
            }
          }
        } else if ($mont >= '04' and $mont <= '06') {
          if (($month >= '04' and $month <= '06') and $yar == $year) {
          } else {
            if ($month < '04') {
              $updateareacompletestatus = DB::table('mnw_progoti.monitorevents')->where('id', $eid)->where('area_id', $area_id)->update(['areacompletestatus' => 1]);
            }
          }
        } else if ($mont >= '07' and $mont <= '09') {
          if (($month >= '07' and $month <= '09')  and $yar == $year) {
          } else {
            if ($month < '07') {
              $updateareacompletestatus = DB::table('mnw_progoti.monitorevents')->where('id', $eid)->where('area_id', $area_id)->update(['areacompletestatus' => 1]);
            }
          }
        } else if ($mont >= '10' and $mont <= '12') {
          if (($month >= '10' and $month <= '12') and $yar == $year) {
          } else {
            if ($month < '10') {
              $updateareacompletestatus = DB::table('mnw_progoti.monitorevents')->where('id', $eid)->where('area_id', $area_id)->update(['areacompletestatus' => 1]);
            }
          }
        }
      }
    }
  }
}
$divisionname = "";
$regionname = "";
$areaname = "";
$region_id = 0;
$div_id = 0;
$area_id = 0;
$divisionname = $divisiondata[0]->division_name;
$div_id = $divisiondata[0]->division_id;

?>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Division Dashboard</h5>
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
                  <div class="col-md-3">
                    <p class="font-size-h4">Division : <?php echo $divisionname; ?></p>
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
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>Monitoring Event</th>
                  <th>Monitoring Period</th>
                  <th>Result</th>
                </tr>
                <tbody>
                  <?php
                  $month = 0;
                  $mnth = date('m');
                  $cmonth = date('m');
                  $cyear = date('Y');
                  $totalscore = 0;
                  $g = '';
                  $m = '';
                  $p = '';
                  $ct = 0;
                  $score = 0;
                  $year = '';
                  $quarter = '';
                  $m = '';
                  $mn = '';
                  //$checkdata= DB::select(DB::raw("select area_id, year,quarterly from mnw.monitorevents where area_id='$a_id' group by area_id,year,quarterly order by year DESC, quarterly DESC OFFSET 0 LIMIT 3"));
                  $checkdataCurrent = DB::select(DB::raw("select year,quarterly from mnw_progoti.monitorevents where division_id='$a_id' and areacompletestatus =0 group by year,quarterly  order by year DESC, quarterly DESC LIMIT 1"));
                  $checkdataPrevious = DB::select(DB::raw("select year,quarterly from mnw_progoti.monitorevents where division_id='$a_id' and areacompletestatus =1 group by year,quarterly  order by year DESC, quarterly DESC LIMIT 2"));

                  if (!empty($checkdataCurrent)) {

                    foreach ($checkdataCurrent as $row) {
                      $year = $row->year;
                      $quarter = $row->quarterly;
                      //echo $quarter;
                      $m = '';
                      $p = '';
                      $g = '';
                      foreach ($divisiondata as $rw) {

                        $area_id = $rw->area_id;
                        //echo $brcode."/";
                        // $cnt= strlen($brcode);
                        // if($cnt == 3)
                        // {
                        //   $brcode='0'.$brcode;
                        // }
                        $checkbrnch = DB::select(DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_id' and year='$year' and quarterly = '$quarter' and division_id='$a_id'"));

                        if (!empty($checkbrnch)) {
                          $myear = $checkbrnch[0]->year;
                          //echo $myear;
                          $month = $checkbrnch[0]->month;
                          //  echo $month."/".$cmonth;
                          if ($month <= '03') {
                            if (($mnth >= '01' and $mnth <= '03') and ($myear == $cyear)) {
                              $period = $myear . "," . "JAN" . " to " . $myear . "," . "MAR";

                              if ($checkbrnch[0]->score == null) {
                                $ongoing = 1;
                              }
                              $score = $checkbrnch[0]->score;
                            } else {

                              $period = $myear . "," . "JAN" . " to " . $myear . "," . "MAR";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '04' and $month <= '06') {
                            if (($mnth >= '04' and $mnth <= '06') and ($year == $cyear)) {

                              $period = $myear . "," . "APR" . " to " . $myear . "," . "JUN";

                              if ($checkbrnch[0]->score == null) {
                                $ongoing = 1;
                              }
                              $score = $checkbrnch[0]->score;
                            } else {
                              $period = $myear . "," . "APR" . " to " . $myear . "," . "JUN";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '07' and $month <= '09') {
                            if (($mnth >= '07' and $mnth <= '09') and ($myear == $cyear)) {
                              $period = $myear . "," . "JUL" . " to " . $myear . "," . "SEP";
                              if ($checkbrnch[0]->score == null) {
                                $ongoing = 1;
                              }
                              $score = $checkbrnch[0]->score;
                            } else {
                              //if()
                              $period = $myear . "," . "JUL" . " to " . $myear . "," . "SEP";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '10' and $month <= '12') {
                            if (($mnth >= '10' and $mnth <= '12') and ($myear == $cyear)) {

                              $period = $myear . "," . "OCT" . " to " . $myear . "," . "DEC";
                              if ($checkbrnch[0]->score == null) {
                                $ongoing = 1;
                              }
                              $score = $checkbrnch[0]->score;
                            } else {

                              $period = $myear . "," . "OCT" . " to " . $myear . "," . "DEC";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          }
                        }
                        //echo $brcode."/";
                      }
                      //echo $score."/";
                      //die();
                      if ($score != 0 or $score == null) {
                        if ($score != null) {
                          $totalscore = round($score / $ct, 2);
                        }
                        if ($totalscore >= 85) {
                          $g = 'Good';
                        } else if ($totalscore >= 70 and $totalscore < 85) {
                          $m = 'Modarate';
                        } else if ($totalscore >= 0.1 and $totalscore < 70) {
                          $p = 'Need Improvment';
                        } elseif ($score == null) {
                          $current = 'current';
                        }

                        //echo $g."/".$m."*".$p;
                  ?>
                        <tr>
                          <td>
                            <a style="color: #3699FF" href="DivisionDashboard?event=<?php echo $year . "-" . $quarter . "-" . $a_id; ?>"><?php echo $year . "-" . $quarter; ?></a>
                          </td>

                          <td><?php echo $period; ?></td>
                          <td style="color: black;"><?php if ($g) {
                                                      echo "Good";
                                                    }
                                                    if ($m) {
                                                      echo "Moderate";
                                                    }
                                                    if ($p) {
                                                      echo "Need Improvment";
                                                    }
                                                    if ($score == null) {
                                                      echo "Ongoing";
                                                    } ?></td>
                        </tr>
                      <?php
                      }

                      //echo $score."/".$ct."-".$year."*".$quarter;
                      //die();
                    }
                  }
                  if (!empty($checkdataPrevious)) {

                    foreach ($checkdataPrevious as $row) {
                      $year = $row->year;
                      $quarter = $row->quarterly;
                      //echo $quarter;
                      $m = '';
                      $p = '';
                      $g = '';
                      foreach ($divisiondata as $rw) {
                        $area_id = $rw->area_id;
                        //echo $brcode."/";
                        // $cnt= strlen($brcode);
                        // if($cnt == 3)
                        // {
                        //   $brcode='0'.$brcode;
                        // }
                        $checkbrnch = DB::select(DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_id' and year='$year' and quarterly = '$quarter' and division_id='$a_id'"));
                        if (!empty($checkbrnch)) {
                          $myear = $checkbrnch[0]->year;
                          //echo $myear;
                          $month = $checkbrnch[0]->month;
                          //  echo $month."/".$cmonth;
                          if ($month <= '03') {

                            if (($mnth >= '01' and $mnth <= '03') and ($myear == $cyear)) {
                            } else {

                              $period = $myear . "," . "JAN" . " to " . $myear . "," . "MAR";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '04' and $month <= '06') {
                            if (($mnth >= '04' and $mnth <= '06') and ($year == $cyear)) {
                            } else {
                              $period = $myear . "," . "APR" . " to " . $myear . "," . "JUN";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '07' and $month <= '09') {
                            if (($mnth >= '07' and $mnth <= '09') and ($myear == $cyear)) {
                            } else {
                              //if()
                              $period = $myear . "," . "JUL" . " to " . $myear . "," . "SEP";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          } else if ($month >= '10' and $month <= '12') {
                            if (($mnth >= '10' and $mnth <= '12') and ($myear == $cyear)) {
                            } else {

                              $period = $myear . "," . "OCT" . " to " . $myear . "," . "DEC";
                              $score += $checkbrnch[0]->score;
                              $ct += 1;
                            }
                          }
                        }
                        //echo $brcode."/";
                      }
                      //echo $score."/";
                      //die();

                      if ($score != null) {
                        // dd($checkbrnch);

                        $totalscore = round($score / $ct, 2);
                      }
                      if ($totalscore >= 85) {
                        $g = 'Good';
                      } else if ($totalscore >= 70 and $totalscore < 85) {
                        $m = 'Modarate';
                      } else if ($totalscore >= 0.1 and $totalscore < 70) {
                        $p = 'Poor';
                      }

                      //echo $g."/".$m."*".$p;
                      ?>
                      <tr>
                        <td>
                          <a style="color: #3699FF" href="DivisionDashboard?event=<?php echo $year . "-" . $quarter . "-" . $a_id; ?>"><?php echo $year . "-" . $quarter; ?></a>
                        </td>

                        <td><?php echo $period; ?></td>
                        <td style="color: black;"><?php if ($g) {
                                                    echo "Good";
                                                  }
                                                  if ($m) {
                                                    echo "Moderate";
                                                  }
                                                  if ($p) {
                                                    echo "Need Improvment";
                                                  } ?></td>
                      </tr>
                  <?php

                      //echo $score."/".$ct."-".$year."*".$quarter;
                      //die();
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
        $evyear = '';
        $evquarter = '';
        $totalscore3 = 0;
        $totalscore2 = 0;
        $totalscore4 = 0;
        $ct2 = 0;
        $ct1 = 0;
        $ct3 = 0;
        $ct4 = 0;
        $evyear = $eventyear;
        $evquarter = $eventquarter;
        //$year ='';
        //$quarter ='';
        if ($evyear != '' and $evquarter != '') {

          $quarter = '';
          $year  = '';
          $y = date('Y');
          $mon = date('m');
          $checkLast = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and areacompletestatus =1 order by id DESC LIMIT 1"));
          if (!empty($checkLast)) {

            $year = $checkLast[0]->year;
            $quarter = $checkLast[0]->quarterly;
          }

        ?>

          <div class="col-md-12">
            <div class="card card-custom gutter-b">
              <!--begin::Form-->
              <div class="card-header">
                <h3 class="card-title">Monitoring Event: <?php echo $evyear . "-" . $evquarter; ?> </h3>
              </div><!-- /.box-header -->
              <div class="card-body table-responsive">

                <?php
                $cl = 0;
                $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$evquarter' group by month order by month ASC"));
                // dd($month);

                if (!empty($month)) {
                  foreach ($month as $rw) {
                    $cl++;
                  }
                }
                ?>

                <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered">
                  <thead>
                    <tr class="brac-color-pink">
                      <th rowspan="2" width="15%" style="text-align: center">Section</th>
                      <th rowspan="2" width="20%">Section Name</th>
                      <th colspan="<?php echo $cl; ?>" width="20%" style="text-align: center">Month wise achievement %</th>
                      <th rowspan="2" width="15%">Quarterly achievement %</th>
                    </tr>
                    <tr class="brac-color-pink">
                      <?php
                      foreach ($month as $row) {
                        $mon = $row->month;
                        if ($mon == '01') {
                          $j = "JAN";
                        } else if ($mon == '02') {
                          $j = "FEB";
                        } else if ($mon == '03') {
                          $j = "MAR";
                        } else if ($mon == '04') {
                          $j = "APR";
                        } else if ($mon == '05') {
                          $j = "MAY";
                        } else if ($mon == '06') {
                          $j = "JUN";
                        } else if ($mon == '07') {
                          $j = "JUL";
                        } else if ($mon == '08') {
                          $j = "AUG";
                        } else if ($mon == '09') {
                          $j = "SEP";
                        } else if ($mon == '10') {
                          $j = "OCT";
                        } else if ($mon == '11') {
                          $j = "NOV";
                        } else if ($mon == '12') {
                          $j = "DEC";
                        } else {
                          $j = '0';
                        }
                      ?>
                        <th width="10%" style="text-align: center"><?php echo $j; ?></th>
                      <?php
                      }
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php

                    $monthscore1 = 0;
                    $monthscore2 = 0;
                    $monthscore3 = 0;
                    $monthscore4 = 0;
                    $totalscore = 0;
                    $mnthh1 = 0;
                    $mnthh2 = 0;
                    $mnthh = 0;
                    $cycle = $evyear . "-" . $evquarter;
                    $cyear = date('Y');
                    //$mnth =date('m');
                    $sectionpoint = 0;
                    $question_point = 0;
                    $data = array();
                    $exp = explode('-', $cycle);
                    $evyear = $exp[0];
                    $quarter = $exp[1];

                    $areas = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter'"));
                    if (!empty($areas)) {
                      for ($i = 1; $i <= 5; $i++) {
                        $c = 0;
                        $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter' group by month order by month ASC"));
                        if (!empty($month)) {
                          $c = 0;
                          foreach ($month as $row) {
                            $sectionpoint1 = 0;
                            $question_point1 = 0;
                            $mn = $row->month;
                            $getmonth = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter' and month='$mn'"));
                            if (!empty($getmonth)) {
                              foreach ($getmonth as $r) {
                                $event_id = $r->id;
                                $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                                if (!empty($data)) {
                                  $sectionpoint1 += $data[0]->sp;
                                  $question_point1 += $data[0]->qsp;
                                }
                              }

                              if ($c == '0') {
                                if ($sectionpoint1 != 0) {
                                  $monthscore1 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                  $mnthh1 = $mn;
                                } else {
                                  $monthscore1 = 0;
                                  $mnthh1 = $mn;
                                }
                              } else if ($c == '1') {
                                if ($sectionpoint1 != 0) {
                                  $monthscore2 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                  $mnthh2 = $mn;
                                } else {
                                  $monthscore2 = 0;
                                  $mnthh2 = $mn;
                                }
                              } else if ($c == '2') {
                                if ($sectionpoint1 != 0) {
                                  $monthscore3 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                  $mnthh3 = $mn;
                                } else {
                                  $monthscore3 = 0;
                                  $mnthh3 = $mn;
                                }
                              }
                            }

                            $c++;
                          }
                        }
                        //echo $monthscore1."/".$monthscore2;
                        $sectionpoint = 0;
                        $question_point = 0;
                        foreach ($areas as $r) {
                          $mnth = $r->month;
                          //echo $mnth;
                          $year = $r->year;
                          $quar = $r->quarterly;
                          $event_id = $r->id;
                          $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                          if (!empty($data)) {
                            $sectionpoint += $data[0]->sp;
                            $question_point += $data[0]->qsp;
                          }
                        }
                        // echo $sectionpoint."/".$question_point."*";
                        //die();

                        if ($sectionpoint != 0) {
                          $totalscore = round((($sectionpoint / $question_point) * 100), 2);
                        }
                        $secname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
                        if (empty($secname)) {
                        } else {
                          $name = $secname[0]->qdesc;
                        }

                        $dataExist = DB::select(DB::raw("select * from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                        if (!empty($dataExist)) {
                          //echo $mntt;
                    ?>
                          <tr style="height:20px; color:black;">
                            <td style="text-align:center"><a class="btn btn-light" target="_blank" href="RegionWise?region=<?php echo $i . "," . $year . "," . $quarter . "," . $a_id; ?>"><?php echo "Section: " . $i; ?></a></td>
                            <td width="50%"><?php echo $name; ?></td>

                            <?php
                            if ($mnthh1 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh1 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore1 . "%"; ?></button></a></td>
                            <?php
                            }
                            if ($mnthh2 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh2 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore2 . "%"; ?></button></a></td>
                            <?php
                            }
                            if ($mnthh3 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh3 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore3 . "%"; ?></button></a></td>
                            <?php
                            }
                            ?>

                            <td width="30%" style="text-align: center;"><?php echo $totalscore . "%"; ?></td>
                          </tr>
                        <?php
                        } else {
                        ?>
                          <tr style="height:20px; color:black;">
                            <td style="text-align:center"><a class="btn btn-light" target="_blank" href="RegionWise?region=<?php echo $i . "," . $year . "," . $quarter . "," . $a_id; ?>"><?php echo "Section: " . $i; ?></a></td>
                            <td width="50%"><?php echo $name; ?></td>

                            <?php
                            if ($mnthh1 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh1 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore1 . "%"; ?></button></a></td>
                            <?php
                            }
                            if ($mnthh2 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh2 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore2 . "%"; ?></button></a></td>
                            <?php
                            }
                            if ($mnthh3 != 0) {
                            ?>
                              <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh3 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore3 . "%"; ?></button></a></td>
                            <?php
                            }
                            ?>

                            <td width="30%" style="text-align: center;"><?php echo $totalscore . "%"; ?></td>
                          </tr>
                    <?php
                        }
                        $totalscore = 0;
                        $monthscore1 = 0;
                        $monthscore2 = 0;
                        $monthscore3 = 0;
                      }
                    }
                    ?>
                  </tbody>
                </table>
              <?php
            } else {
              $month = '';
              $y = date('Y');
              $mon = date('m');
              $year = '';
              $quarter = '';
              $cycle = '';
              $cycl = DB::select(DB::raw("select year,quarterly from mnw_progoti.monitorevents where division_id='$a_id' and areacompletestatus=1 group by year, quarterly order by year DESC, quarterly DESC LIMIT 1"));
              if (!empty($cycl)) {
                $year = $cycl[0]->year;
                $quarter = $cycl[0]->quarterly;
                //$cycle= $cycl[0]->year."-".$cycl[0]->quarterly;
              }
              //echo $month;
              ?>
                <div class="col-md-12">
                  <div class="card card-custom gutter-b">
                    <!--begin::Form-->
                    <div class="card-header">
                      <h3 class="card-title">Monitoring Event: <?php echo $year . "-" . $quarter; ?> </h3>
                    </div><!-- /.box-header -->
                    <div class="card-body table-responsive">
                      <?php
                      $cl = 0;
                      $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where division_id='$a_id' and year='$year' and quarterly='$quarter' group by month order by month ASC"));
                      if (!empty($month)) {
                        foreach ($month as $rw) {
                          $cl++;
                        }
                      }
                      ?>
                      <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered">
                        <thead>
                          <tr class="brac-color-pink">
                            <th rowspan="2" width="15%" style="text-align: center">Section</th>
                            <th rowspan="2" width="20%">Section Name</th>
                            <th colspan="<?php echo $cl; ?>" width="20%" style="text-align: center">Month wise achievement %</th>
                            <th rowspan="2" width="15%">Quarterly achievement %</th>
                          </tr>
                          <tr class="brac-color-pink">
                            <?php
                            foreach ($month as $row) {
                              $mon = $row->month;
                              if ($mon == '01') {
                                $j = "JAN";
                              } else if ($mon == '02') {
                                $j = "FEB";
                              } else if ($mon == '03') {
                                $j = "MAR";
                              } else if ($mon == '04') {
                                $j = "APR";
                              } else if ($mon == '05') {
                                $j = "MAY";
                              } else if ($mon == '06') {
                                $j = "JUN";
                              } else if ($mon == '07') {
                                $j = "JUL";
                              } else if ($mon == '08') {
                                $j = "AUG";
                              } else if ($mon == '09') {
                                $j = "SEP";
                              } else if ($mon == '10') {
                                $j = "OCT";
                              } else if ($mon == '11') {
                                $j = "NOV";
                              } else if ($mon == '12') {
                                $j = "DEC";
                              } else {
                                $j = '0';
                              }
                            ?>
                              <th width="10%" style="text-align: center"><?php echo $j; ?></th>
                            <?php
                            }
                            ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php

                          $mnthh1 = 0;
                          $mnthh2 = 0;
                          $mnthh = 0;
                          $monthscore1 = 0;
                          $monthscore2 = 0;
                          $monthscore3 = 0;
                          $monthscore4 = 0;
                          $totalscore = 0;
                          $cycle = $year . "-" . $quarter;
                          $cyear = date('Y');
                          //$mnth =date('m');
                          $sectionpoint = 0;
                          $question_point = 0;
                          $data = array();
                          $exp = explode('-', $cycle);
                          $evyear = $exp[0];
                          $quarter = $exp[1];

                          $areas = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter'"));
                          if (!empty($areas)) {
                            for ($i = 1; $i <= 5; $i++) {
                              $c = 0;
                              $month = DB::select(DB::raw("select month from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter' group by month order by month ASC"));
                              if (!empty($month)) {
                                $c = 0;
                                //$mntt = $month[0]->month;
                                foreach ($month as $row) {
                                  $sectionpoint1 = 0;
                                  $question_point1 = 0;
                                  $mn = $row->month;
                                  $getmonth = DB::select(DB::raw("select * from mnw_progoti.monitorevents where division_id='$a_id' and year='$evyear' and quarterly='$quarter' and month='$mn'"));
                                  if (!empty($getmonth)) {
                                    foreach ($getmonth as $r) {
                                      $event_id = $r->id;
                                      $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                                      // $data = DB::select(DB::raw("select * from mnw_progoti.cal_section_point where event_id='7' and section='4'"));
                                      // dd($data);
                                      if (!empty($data)) {
                                        $sectionpoint1 += $data[0]->sp;
                                        $question_point1 += $data[0]->qsp;
                                      }
                                    }

                                    if ($c == '0') {
                                      if ($sectionpoint1 != 0) {
                                        $monthscore1 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                        $mnthh1 = $mn;
                                      } else {
                                        $monthscore1 = 0;
                                        $mnthh1 = $mn;
                                      }
                                    } else if ($c == '1') {
                                      if ($sectionpoint1 != 0) {
                                        $monthscore2 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                        $mnthh2 = $mn;
                                      } else {
                                        $monthscore2 = 0;
                                        $mnthh2 = $mn;
                                      }
                                    } else if ($c == '2') {
                                      if ($sectionpoint1 != 0) {
                                        $monthscore3 = round((($sectionpoint1 / $question_point1) * 100), 2);
                                        $mnthh3 = $mn;
                                      } else {
                                        $monthscore3 = 0;
                                        $mnthh3 = $mn;
                                      }
                                    }
                                  }

                                  $c++;
                                }
                                //echo $mn;

                              }
                              //echo $monthscore1."/".$monthscore2;
                              $sectionpoint = 0;
                              $question_point = 0;
                              foreach ($areas as $r) {
                                $mnth = $r->month;
                                //echo $mnth;
                                $year = $r->year;
                                $quar = $r->quarterly;
                                $event_id = $r->id;
                                $data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));
                                // dd($data);
                                if (!empty($data)) {
                                  $sectionpoint += $data[0]->sp;
                                  $question_point += $data[0]->qsp;
                                }
                              }
                              // echo $sectionpoint."/".$question_point."*";
                              //die();

                              if ($sectionpoint != 0) {
                                $totalscore = round((($sectionpoint / $question_point) * 100), 2);
                              }
                              $secname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
                              if (empty($secname)) {
                              } else {
                                $name = $secname[0]->qdesc;
                              }
                              $dataExist = DB::select(DB::raw("select * from mnw_progoti.cal_section_point where event_id='$event_id' and section='$i'"));

                              if (!empty($dataExist)) {
                                //echo $mntt;
                          ?>

                                <tr style="height:20px; color:black;">
                                  <td style="text-align:center;"><a class="btn btn-light" target="_blank" href="RegionWise?region=<?php echo $i . "," . $year . "," . $quarter . "," . $a_id; ?>"><?php echo "Section: " . $i; ?></a></td>
                                  <td width="50%"><?php echo $name; ?></td>

                                  <?php
                                  if ($mnthh1 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh1 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore1 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  if ($mnthh2 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh2 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore2 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  if ($mnthh3 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh3 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore3 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  ?>


                                  <td width="30%" style="text-align: center;"><?php echo $totalscore . "%"; ?></td>
                                </tr>
                              <?php
                              } else {
                              ?>
                                <tr style="height:20px; color:black;">
                                  <td style="text-align:center"><a class="btn btn-light" target="_blank" href="RegionWise?region=<?php echo $i . "," . $year . "," . $quarter . "," . $a_id; ?>"><?php echo "Section: " . $i; ?></a></td>
                                  <td width="50%"><?php echo $name; ?></td>

                                  <?php
                                  if ($mnthh1 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh1 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore1 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  if ($mnthh2 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh2 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore2 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  if ($mnthh3 != 0) {
                                  ?>
                                    <td style="text-align:center"><a target="_blank" href="monthRegionWise?month=<?php echo $i . "," . $mnthh3 . "," . $evyear . "," . $quarter . "," . $a_id; ?>"><button class="btn btn-light"><?php echo $monthscore3 . "%"; ?></button></a></td>
                                  <?php
                                  }
                                  ?>
                                  <td width="30%" style="text-align: center;"><?php echo $totalscore . "%"; ?></td>
                                </tr>
                          <?php
                              }
                              $totalscore = 0;
                              $monthscore1 = 0;
                              $monthscore2 = 0;
                              $monthscore3 = 0;
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
                <div class="col-md-12">
                  <div class="card card-custom gutter-b">
                    <!--begin::Form-->
                    <div class="card-header">
                      <h3 class="card-title">Area Count</h3>
                    </div><!-- /.box-header -->
                    <div class="card-body table-responsive">
                      <table style="text-align: center;font-size:13" style="font-size:13" class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                          <tr class="brac-color-pink">
                            <th rowspan="3">Monitoring Event</th>
                            <th rowspan="3">Division Total Area No</th>
                            <th colspan="2">Total area monitored</th>
                            <th colspan="6">Rating wise area no</th>
                          </tr>
                          <tr class="brac-color-pink">
                            <th rowspan="2">No</th>
                            <th rowspan="2">% (of all area)</th>
                            <th colspan="2">Good</th>
                            <th colspan="2">Moderate</th>
                            <th colspan="2">Need Improvment</th>
                          </tr>
                          <tr class="brac-color-pink">
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

                          $good = 0;
                          $moderate = 0;
                          $poor = 0;
                          $monitorpercent = '';
                          $cntdivision = DB::select(DB::raw("select count(*) as cnt from branch where division_id='$a_id' and program_id=5"));
                          if (!empty($cntdivision)) {
                            $divcount =  $cntdivision[0]->cnt;
                          }
                          $quarterlist = DB::select(DB::raw("select year,quarterly from mnw_progoti.monitorevents where division_id='$a_id' and areacompletestatus=1 group by year,quarterly order by year,quarterly DESC Limit 4"));
                          if (!empty($quarterlist)) {
                            foreach ($quarterlist as $row) {
                              $good = 0;
                              $moderate = 0;
                              $poor = 0;
                              $g = 0;
                              $m = 0;
                              $p = 0;

                              $years = $row->year;
                              $quarter = $row->quarterly;
                              $monitoredbranch = DB::select(DB::raw("select count(*) as arcount from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and division_id='$a_id' and score !='0'"));
                              if (!empty($monitoredbranch)) {
                                $arcnt = $monitoredbranch[0]->arcount;
                              } else {
                                $arcnt = 0;
                              }

                              $monitorpercent = round($arcnt / $divcount * 100, 2);
                              // dd($arcnt);

                              $goodmodaratepoor = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$years' and quarterly='$quarter' and division_id='$a_id' and score !='0'"));
                              if (!empty($goodmodaratepoor)) {
                                foreach ($goodmodaratepoor as $rw) {
                                  $score =  $rw->score;
                                  if ($score >= '85') {
                                    $g += 1;
                                  } else if ($score >= '70' and $score < '85') {
                                    $m += 1;
                                  } else if ($score < 70) {
                                    $p += 1;
                                  } else {
                                    $g = 0;
                                    $m = 0;
                                    $p = 0;
                                  }
                                }
                              }
                              if ($arcnt > 0 and  $g > 0) {
                                $good = round($g / $arcnt * 100, 2);
                              }
                              if ($arcnt > 0 and  $m > 0) {
                                $moderate = round($m / $arcnt * 100, 2);
                              }
                              if ($arcnt > 0 and  $p > 0) {
                                $poor = round($p / $arcnt * 100, 2);
                              }

                          ?>
                              <tr>
                                <td><?php echo $years . "-" . $quarter; ?></td>
                                <td><?php echo $divcount; ?></td>
                                <td><?php echo $arcnt; ?></td>
                                <td><?php echo $monitorpercent . " %"; ?></td>
                                <td><?php echo $g; ?></td>
                                <td><?php echo $good . " %"; ?></td>
                                <td><?php echo $m; ?></td>
                                <td><?php echo $moderate . " %"; ?></td>
                                <td><?php echo $p; ?></td>
                                <td><?php echo $poor . " %"; ?></td>
                              </tr>
                          <?php
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