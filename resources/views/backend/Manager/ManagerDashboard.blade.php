<?php
$username = Session::get('username');
$rollid = Session::get('roll');
if($username=='')
{

  ?>
  <script>
    window.location.href = 'Logout';
  </script>
  
  <?php 
  
}
// dd($area_data);

$calculation=1;
$dataload1="true";
$dataload1 = $dataload;
if($dataload1 =="true")
{
  if($calculation==1)
  {
    ?>
  {{-- @extends('backend.DataProcessing.AdminDataProcessing') --}}
    <script type="text/javascript">
      //window.location = "BRDashboard?dataload=false;"
      //alert("test");
    </script>
    
    <?php
    $speed=0;
  }
}
if(isset($_GET['dataload']))
{
  $calculation = $_GET['dataload'];
  if($calculation=="false")
  {
    $calculation=0;
  }
}

?>
@extends('backend.layouts.master')

@section('title','Manager Dashboard')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Manager Dashboard</h5>
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
        @if ($rollid!='1' && $rollid!='2' && $rollid!='3' && $rollid!='4' )
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <form target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Division</label>
                    <select class="form-control" name="division" id="divs" required>
                        <option value="">select</option>
                        <?php
                        foreach ($division as $row) 
                        {
                          $division_name =  $row->division_name;
                          $division_id = $row->division_id;
                          ?>
                          
                          <option value="<?php echo $division_id; ?>"><?php echo $division_name; ?></option>
                          
                          <?php
                        }
                      ?>
                    </select>
                </div>
                <div class="col-md-3">
                <label class="control-label">Region</label>
                    <select class="form-control" name="region" id="region_id" required>
                        <option>select</option>
                        <?php 
                        if($reg!='' and $area_data !='')
                        {
                          $fetchregion = DB::select( DB::raw("select  region_id from branch  where area_id= '$area_data' and program_id='5' group by region_id order by region_id"));
                          $reg=$fetchregion[0]->region_id;
                        }
                        if($reg !='')
                        {
                            $regions = DB::select( DB::raw("select  division_id,region_name,region_id from branch  where region_id= '$reg' and program_id='5' group by division_id,region_name,region_id order by region_id"));
                            $div=$regions[0]->division_id;
                            $allregions = DB::select( DB::raw("select  region_name,region_id from branch  where division_id= '$div' and program_id='5' group by region_name,region_id order by region_id"));
                            ?>
                            <?php
                            foreach ($allregions as $row) 
                            {
                            ?>
                            <option value="<?php echo $row->region_id; ?>"><?php echo $row->region_name; ?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                <label class="control-label">Area</label>
                    <select class="form-control" name="area" id="area_id" required>
                        <option>select</option>
                        <?php 
                        if($reg!='' and $area_data !='')
                        {
                        $areas = DB::select( DB::raw("select  region_id,area_name,area_id from branch  where area_id= '$area_data' and program_id='5' group by region_id,area_name,area_id order by area_id"));
                        $reg=$areas[0]->region_id;
                        $allareas = DB::select( DB::raw("select  area_name,area_id from branch  where region_id= '$reg' and program_id='5' group by area_name,area_id order by area_id"));
                        ?>
                        <?php
                        foreach ($allareas as $row) 
                        {
                            ?>
                            <option value="<?php echo $row->area_id; ?>"><?php echo $row->area_name; ?></option>
                            <?php
                        }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary" style="margin: 25px 0px 0px 25px;">Submit</button>
                </form>
                    <?php
                    $region ='';
                    $area='';
                    if(isset($_GET['area']))
                    {
                      $area = $_GET['area'];
                      $areas = DB::select( DB::raw("select  region_id from branch  where area_id= '$area' and program_id='5' group by region_id order by region_id"));
                      $region = $areas[0]->region_id;
                    ?>
                        <a target="_blank" class="btn btn-secondary" style="margin: 25px 0px 0px 10px;" href="Remarks?region=<?php echo $region; ?>&area=<?php echo $area; ?>&event={{ $evnt }}" class="btn btn-success">All Remarks</a>
        
                    <?php
                }
                ?>
                </div>
            </div>
            <div class="row mt-7">
              <div class="col-md-12">
                  <form action="ManagerDashboard" method="GET" target="_blank">
                      <label for="example-search-input" class="control-label">Area Search</label>
                      <div class="form-group row">
                          <div class="col-9">
                           <input class="form-control" type="text" list="browsers" id="selected" name="area" required autocomplete="off"/>
                           <datalist id="browsers">
                            <?php 
                            $userpin = Session::get('user_pin');
                            $area = DB::select(DB::raw("select area_id,area_name from branch where program_id=5 group by area_id,area_name order by area_id ASC"));
                            if($rollid =='17')
                            {
                               $area =DB::select(DB::raw("select area_name,area_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where c_associate_id='$userpin') and program_id=5 group by area_name,area_id order by area_id ASC"));
                            }
                            else if($rollid=='18')
                            {
                               $area =DB::select(DB::raw("select area_name,area_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where z_associate_id='$userpin') and program_id=5 group by area_name,area_id order by area_id ASC"));
                            }
                            if(!empty($area))
                            {
                              foreach($area as $row)
                              {
                                ?>
                                <option data-value="{{ $row->area_id }}" value="<?php echo $row->area_name; ?>"></option>
                                <?php
                              }
                          
                            }
                            ?>
                          </datalist>
                          </div>
                          <div class="col-3">
                              <button id="submit" type="submit" class="btn btn-secondary" style="margin: 0px 0px 0px 25px;">Search</button>

                          </div>
                      </div>
                </form>
              </div>
          </div>
        @endif

            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <br>
        <?php
        $brnc =0;
        if(isset($_GET['division']))
        {
        $div = $_GET['division'];
        }
        if(isset($_GET['region']))
        {
        $reg = $_GET['region'];
        }
        if(isset($_GET['area']))
        {
          $are = $_GET['area'];
        }
        if($area_data !='')
        {
        ?>
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" style="font-size: 13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>SL</th>
                  <th>Monitoring Period</th>
                  <th>Result</th>
                </tr>
                <tbody>
                    <?php
                    $id =1;
                    $g =0;
                    $m=0;
                    $p=0;
                    foreach ($ar as $row) 
                    {
                      $g =0;
                      $m=0;
                      $p=0;
                      $evnetid= $row->id;
                      $score = $row->score;
                      $year = $row->year;
                      $quarter = $row->quarterly;
                      if($score >='85')
                      {
                        $g ="Good";
                      }
                      else if($score >='70' and $score <'85')
                      {
                        $m = "Modarate";
                      }
                      else if($score < 70)
                      {
                        $p ='Poor';
                      }
                    ?>
                    <tr>
                      <td><?php echo $id++; ?></td>
                      <td><a style="color: #3699FF" href="ManagerDashboard?eventid=<?php echo $evnetid; ?>&division=<?php echo $div; ?>&region=<?php echo $reg; ?>&area=<?php echo $are; ?>"> <?php echo $row->datestart." to ".$row->dateend; ?></a></td>
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
                  ?>
                </tbody>
              </table>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
        <?php
        if($evnt !='')
        {
          $cycle ='';
          $area1 = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' and id='$evnt' order by id DESC limit 1"));
          if(!empty($area1))
          {
           $cycle= $area1[0]->datestart." to ".$area1[0]->dateend;
          }
          ?>

        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
              <h3 class="card-title">Monitoring Event: <?php echo $cycle; ?> </h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" style="font-size: 13" class="table" width="100%">
                <tr class="brac-color-pink">
                    <th nowrap="nowrap" width="15%">Section No</th>
                    <th width="45%">Section Name</th>
                    <th width="20%">Score</th>
                    <th width="20%">Status</th>
                </tr>
              </table>
              <?php 
  $area_data = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' and id='$evnt' order by id DESC limit 1"));
  if(!empty($area_data))
  {
   foreach ($area_data as $row) 
   {
     $dst = $row->datestart;
     $dend = $row->dateend;
     $evnetid = $row->id;
     $c_date = Date('Y-m-d');
     if($dst <= $c_date and $dend >=$c_date)
     {
       $stus ="Ongoining!";
     }
     else
     {
       $stus ="Complete!";
     }
     $sections = DB::select( DB::raw("select * from mnw_progoti.cal_sections_score where event_id='$evnetid' order by sec_no ASC"));
     if(!empty($sections))
     {
      // foreach ($sections as $row) 
      for ($section=1;$section<6;$section++) 
      {
        // $section = $row->sec_no;
        $sectiondata = DB::select( DB::raw("select * from mnw_progoti.cal_sections_score where event_id='$evnetid' and sec_no=$section order by sec_no ASC"));
        if(empty($sectiondata)){
          $score = 0;
          $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='0' and qno=0"));
          if(empty($secname))
          {
            $name ='';
          }
          else{
            $name = $secname[0]->qdesc;
          }
          ?>
          <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
            <tr>
              <td nowrap="nowrap" width="15%"><button id="<?php echo $section; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $section; ?>);" >+</button><span class="ml-3"><?php echo "Section: ".$section; ?></span></td>
              <td width="45%"><?php echo $name; ?></td>
              <td width="20%" ><?php echo $score; ?></td>
              <td width="20%" ><?php echo $stus; ?></td>
            </tr>
          </table>
          <table style="text-align: center;font-size:13" id="<?php echo "dv".$section; ?>" class="table" cellspacing="0" width="100%">
            <thead>
              <tr class="brac-color-pink">
                <th width="10%" style=" text-align: center;">SL</th>
                <th style=" " width="50%">Details</th>
                <th style=" " width="20%">Score</th>
                <th style=" " width="20%">Achievement %</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $pnt =0;
              $qpnt =0;
              $totalsc =0;
              // $alldata = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where section = '$section' and event_id='$evnetid' order by sub_id ASC"));
              $sec_subs = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and  qno='0' and sub_sec_no != '0'"));
              // dd($sec_subs);
              if(!empty($sec_subs))
              {
                foreach ($sec_subs as $data) 
                {
                  // dd($data);
                  $name = $data->qdesc;
                  $pnt = 0;
                  $qpnt = 0;
                  $totalsc =0;
                  if($pnt !=0)
                  {
                    $totalsc = round(($pnt/$qpnt*100),2);
                  }
                  ?>
                  <tr>
                  <td style="text-align:center"><a class="btn btn-light" target="_blank" href="MSectionDetails?data=<?php echo $section.",".$data->sub_sec_no.",".$evnetid; ?>"><?php echo $data->sec_no.".".$data->sub_sec_no; ?></a></td>
                  <td><?php echo $name; ?></td>
                  <td ><?php echo $pnt; ?></td>
                  <td ><?php echo $totalsc; ?></td>
                </tr>
                <?php
              }
            }
          ?>
          </tbody>
          </table>
          <?php
        }else{
          $score = round($sectiondata[0]->score,2);
          $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='0' and qno=0"));
          if(empty($secname))
          {
            $name ='';
          }
          else{
            $name = $secname[0]->qdesc;
          }
          ?>
              <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
                <tr>
                  <td nowrap="nowrap" width="15%"><button id="<?php echo $section; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $section; ?>);" >+</button><span class="ml-3"><?php echo "Section: ".$section; ?></span></td>
                  <td width="45%"><?php echo $name; ?></td>
                  <td width="20%" ><?php echo $score; ?></td>
                  <td width="20%" ><?php echo $stus; ?></td>
                </tr>
              </table>
              <table style="text-align: center;font-size:13" id="<?php echo "dv".$section; ?>" class="table" cellspacing="0" width="100%">
                <thead>
                  <tr class="brac-color-pink">
                    <th width="10%" style=" text-align: center;">SL</th>
                    <th style=" " width="50%">Details</th>
                    <th style=" " width="20%">Score</th>
                    <th style=" " width="20%">Achievement %</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $pnt =0;
                  $qpnt =0;
                  $totalsc =0;
                  $alldata = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where section = '$section' and event_id='$evnetid' order by sub_id ASC"));
                  if(!empty($alldata))
                  {
                    foreach ($alldata as $data) 
                    {
                      
                      $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='$data->sub_id' and qno='0'"));

                      if(empty($secname))
                      {

                      }
                      else{
                        $name = $secname[0]->qdesc;
                      }
                      $pnt = $data->point;
                      $qpnt = $data->question_point;
                      $totalsc =0;
                      if($pnt !=0)
                      {
                        $totalsc = round(($pnt/$qpnt*100),2);
                      }
                      ?>
                      <tr>
                      <td style="text-align:center"><a class="btn btn-light" target="_blank" href="MSectionDetails?data=<?php echo $section.",".$data->sub_id.",".$evnetid; ?>"><?php echo $data->section.".".$data->sub_id; ?></a></td>
                      <td><?php echo $name; ?></td>
                      <td ><?php echo $data->point; ?></td>
                      <td ><?php echo $totalsc; ?></td>
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
        
  <?php
  }
  }
  
  }
  }
}
else
{

  $cycle ='';
  $area1 = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' order by id DESC limit 1"));
  // dd($brnch);

  if(!empty($area1))
  {
  $cycle= $area1[0]->datestart." to ".$area1[0]->dateend;
  }
  ?>
  <div class="col-md-12">
    <div class="card card-custom gutter-b">
      <!--begin::Form-->
      <div class="card-header">
        <h3 class="card-title">Monitoring Event: <?php echo $cycle; ?> </h3>
      </div><!-- /.box-header -->
      <div class="card-body table-responsive">
        <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
          <tr class="brac-color-pink">
              <th nowrap="nowrap" width="10%">Section No</th>
              <th width="45%">Section Name</th>
              <th width="20%">Score</th>
              <th width="20%">Status</th>
          </tr>
        </table>
  <?php 
  $area_data = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' order by id DESC limit 1"));
  if(!empty($area_data))
  {
  foreach ($area_data as $row) 
  {
    
    $dst = $row->datestart;
    $dend = $row->dateend;
    $evnetid = $row->id;
    $c_date = Date('Y-m-d');
    if($dst <= $c_date and $dend >=$c_date)
    {
      $stus ="Ongoining!";
    }
    else
    {
      $stus ="Complete!";
    }
    $sections = DB::select( DB::raw("select * from mnw_progoti.cal_sections_score where event_id='$evnetid' order by sec_no ASC"));
    if(!empty($sections))
    {
      // foreach ($sections as $row) 
      for ($section=1;$section<6;$section++) 
      {
        // $section = $row->sec_no;
        $sectiondata = DB::select( DB::raw("select * from mnw_progoti.cal_sections_score where event_id='$evnetid' and sec_no=$section order by sec_no ASC"));
        if(empty($sectiondata)){
          $score = 0;
          $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='0' and qno=0"));
          if(empty($secname))
          {
            $name ='';
          }
          else{
            $name = $secname[0]->qdesc;
          }
          ?>
          <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
            <tr>
              <td nowrap="nowrap" width="15%"><button id="<?php echo $section; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $section; ?>);" >+</button><span class="ml-3"><?php echo "Section: ".$section; ?></span></td>
              <td width="45%"><?php echo $name; ?></td>
              <td width="20%" ><?php echo $score; ?></td>
              <td width="20%" ><?php echo $stus; ?></td>
            </tr>
          </table>
          <table style="text-align: center;font-size:13" id="<?php echo "dv".$section; ?>" class="table" cellspacing="0" width="100%">
            <thead>
              <tr class="brac-color-pink">
                <th width="10%" style=" text-align: center;">SL</th>
                <th style=" " width="50%">Details</th>
                <th style=" " width="20%">Score</th>
                <th style=" " width="20%">Achievement %</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $pnt =0;
              $qpnt =0;
              $totalsc =0;
              // $alldata = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where section = '$section' and event_id='$evnetid' order by sub_id ASC"));
              $sec_subs = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and  qno='0' and sub_sec_no != '0'"));
              // dd($sec_subs);
              if(!empty($sec_subs))
              {
                foreach ($sec_subs as $data) 
                {
                  // dd($data);
                  $name = $data->qdesc;
                  $pnt = 0;
                  $qpnt = 0;
                  $totalsc =0;
                  if($pnt !=0)
                  {
                    $totalsc = round(($pnt/$qpnt*100),2);
                  }
                  ?>
                  <tr>
                  <td style="text-align:center"><a class="btn btn-light" target="_blank" href="MSectionDetails?data=<?php echo $section.",".$data->sub_sec_no.",".$evnetid; ?>"><?php echo $data->sec_no.".".$data->sub_sec_no; ?></a></td>
                  <td><?php echo $name; ?></td>
                  <td ><?php echo $pnt; ?></td>
                  <td ><?php echo $totalsc; ?></td>
                </tr>
                <?php
              }
            }
          ?>
          </tbody>
          </table>
          <?php
        }else{
          $score = round($sectiondata[0]->score,2);
          $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='0' and qno=0"));
          if(empty($secname))
          {
            $name ='';
          }
          else{
            $name = $secname[0]->qdesc;
          }
          ?>
              <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
                <tr>
                  <td nowrap="nowrap" width="15%"><button id="<?php echo $section; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $section; ?>);" >+</button><span class="ml-3"><?php echo "Section: ".$section; ?></span></td>
                  <td width="45%"><?php echo $name; ?></td>
                  <td width="20%" ><?php echo $score; ?></td>
                  <td width="20%" ><?php echo $stus; ?></td>
                </tr>
              </table>
              <table style="text-align: center;font-size:13" id="<?php echo "dv".$section; ?>" class="table" cellspacing="0" width="100%">
                <thead>
                  <tr class="brac-color-pink">
                    <th width="10%" style=" text-align: center;">SL</th>
                    <th style=" " width="50%">Details</th>
                    <th style=" " width="20%">Score</th>
                    <th style=" " width="20%">Achievement %</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $pnt =0;
                  $qpnt =0;
                  $totalsc =0;
                  $alldata = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where section = '$section' and event_id='$evnetid' order by sub_id ASC"));
                  if(!empty($alldata))
                  {
                    foreach ($alldata as $data) 
                    {
                      
                      $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='$data->sub_id' and qno='0'"));

                      if(empty($secname))
                      {

                      }
                      else{
                        $name = $secname[0]->qdesc;
                      }
                      $pnt = $data->point;
                      $qpnt = $data->question_point;
                      $totalsc =0;
                      if($pnt !=0)
                      {
                        $totalsc = round(($pnt/$qpnt*100),2);
                      }
                      ?>
                      <tr>
                      <td style="text-align:center"><a class="btn btn-light" target="_blank" href="MSectionDetails?data=<?php echo $section.",".$data->sub_id.",".$evnetid; ?>"><?php echo $data->section.".".$data->sub_id; ?></a></td>
                      <td><?php echo $name; ?></td>
                      <td ><?php echo $data->point; ?></td>
                      <td ><?php echo $totalsc; ?></td>
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
        
  <?php
  }
  }

  }
  }
}

?>
<?php } ?>
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
    $('#divs').on('change', function() {
     var division_id= this.value;
       //alert(division_id);
       if(division_id !='')
       {  
         $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
           type: 'POST',
           url: '/mnwprogoti/RegionData',cache: false,
           dataType: 'json',
           data: { id: division_id },
           success: function (data) {
   
           //var d = data[0].region_id;
           //console.log(d);
           var len = data.length;
           $("#region_id").empty();
           $("#area_id").empty();
           $("#branch_id").empty();
           
           var option2 = "<option value=''>select</option>";
           $("#region_id").append(option2);
           for(var i = 0; i < len; i++)
           {
             var option = "<option value='"+data[i].region_id+"'>"+data[i].region_name+"</option>"; 
   
             $("#region_id").append(option); 
           }
           
         },
         error: function (ex) {
           alert('Failed to retrieve Region.');
         }
       });
         
         return;
       }
     });
    $('#region_id').on('change', function() {
     var region_id= this.value;
       //alert(region_id);
       if(region_id !='')
       {  
         $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
           type: 'POST',
           url: '/mnwprogoti/AreaData',cache: false,
           dataType: 'json',
           data: { id: region_id },
           success: function (data) {
   
           //var d = data[0].region_id;
           //console.log(d);
           var len = data.length;
           $("#area_id").empty();$("#branch_id").empty();
           
           var option2 = "<option value=''>select</option>";
           $("#area_id").append(option2);
           for(var i = 0; i < len; i++)
           {
             var option = "<option value='"+data[i].area_id+"'>"+data[i].area_name+"</option>"; 
   
             $("#area_id").append(option); 
           }
           
         },
         error: function (ex) {
           alert('Failed to retrieve Area.');
         }
       });
         
         return;
       }
     }); 
    
   </script>
   <script>
     $(document).ready(function(){

      $('#submit').click(function()
      {
          var value = $('#selected').val();
          var dataval=$('#browsers [value="' + value + '"]').data('value');
          $("#selected").val(dataval);
          setTimeout(function(){ $("#selected").val(value); }, 100);
      });

      $("#dv1").hide();
      $("#dv2").hide();
      $("#dv3").hide();
      $("#dv4").hide();
      $("#dv5").hide();
    });
     function getDiv(judu){
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