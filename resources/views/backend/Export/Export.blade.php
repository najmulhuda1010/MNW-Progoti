@extends('backend.layouts.master')

@section('title','Excel Export')

@section('style')
<style>
.table-wrapper{
  overflow-y: scroll;
  height: 550px;
}

.table-wrapper th{
    position: sticky;
    top: 0;
    background-color: #FB3199;
    color: #fff;
    border: 1px solid #fff;
}
/* A bit more styling to make it look better */

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
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Excel Export</h5>
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
            <?php session_start(); ?>
            <?php 
                if (isset($_SESSION['alert'])) {
                ?>
                   <div class="alert brac-color-pink" role="alert">
                    {{ $_SESSION['alert'] }}
                  </div>
                <?php  
                    // print $_SESSION['alert'];
                    unset($_SESSION['alert']);
                }
                ?>
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <form method="get">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Division</label>
                    <select class="form-control" name="division" id="divs">
                        <option value="">select</option>
                        <?php
				   $div ='';
				   $reg ='';
				   $area='';
				   $quarter1 ='';
				   if(isset($_GET['division']))
				   {
             $div = $_GET['division'];
					   $division1 = DB::select(DB::raw("select division_id,division_name from branch where program_id=5 group by division_id,division_name order by division_id ASC"));
				   }
				   if(isset($_GET['region']))
				   {
              $reg = $_GET['region'];
              //  dd($reg);
              if($reg!='')
              {
                $resionall = DB::select(DB::raw("select region_id,region_name from branch where division_id='$div' and program_id=5 group by region_id,region_name order by region_id ASC"));
              }
				   }
				   if(isset($_GET['quarter']))
				   {
					   $quarter1 = $_GET['quarter'];
				   }
				  if($div !='select' and $div !='')
				  {
					  $division = DB::select(DB::raw("select * from branch where division_id='$div'"));
					  if(!empty($division))
					  {
						  $division_name =  $division[0]->division_name;
					  }
					?>
					<?php
					foreach($division1 as $row)
					{
						$division_name =  $row->division_name;
						$division_id = $row->division_id;
						?>
						   <option value="<?php echo $division_id; ?>"><?php echo $division_name; ?></option>
						<?php
					}
				  }
				  else
				  {
					?>
					 {{-- <option>select</option> --}}
					  <?php
					  foreach ($db as $row) 
					  {
						$division_name =  $row->division_name;
						$division_id = $row->division_id;
						?>
						
							<option value="<?php echo $division_id; ?>"><?php echo $division_name; ?></option>
						
						<?php
					  }
					
				  }
				  ?>
                    </select>
                </div>
                <div class="col-md-3">
                <label class="control-label">Region</label>
                    <select class="form-control" name="region" id="region_id">
                        <option value="">select</option>
                        <?php 
				  if($reg !='')
				  {
					  $region = DB::select(DB::raw("select * from branch where region_id='$reg' and program_id=5"));
					  if(!empty($region))
					  {
						  $region_name =  $region[0]->region_name;
					  }
					?>
					<?php
					foreach($resionall as $row)
					{
						$region_id = $row->region_id;
						$region_name = $row->region_name;
						?>
						<option value="<?php echo $region_id; ?>"><?php echo $region_name; ?></option>
						<?php
					}
				  }
				 ?>
                    </select>
                </div>
                <?php
          $quarters=DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->groupBy('year','quarterly')->orderBy('year','DESC')->get();
          // dd($periods[0]->datestart);
      ?>
                <div class="col-md-3">
                <label class="control-label">Quarter</label>
                    <select class="form-control" name="quarter" id="quarter">
                        <option value="">select</option>
                        <?php
                        if($quarters !='')
                        {
                            ?>
                            <?php
                            foreach($quarters as $row)
                            {
                                ?>
                                <option value="<?php echo $row->year."-".$row->quarterly; ?>"><?php echo $row->year."-".$row->quarterly; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
          $periods=DB::table('mnw_progoti.monitorevents')->select('datestart','dateend')->groupBy('datestart','dateend')->orderBy('datestart','DESC')->get();
          // dd($periods[0]->datestart);
					  ?>
                <div class="col-md-3">
                <label class="control-label">Period</label>
                    <select class="form-control" name="period" id="period">
                        <option value="">select</option>
                        <?php
                        foreach($periods as $row){
                        ?>
                                    <option value="<?php echo $row->datestart."/".$row->dateend; ?>"><?php echo $row->datestart." to ".$row->dateend; ?></option>
                        <?php   
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    
                </div>
            </div>
            <div class="row mt-7">
                <div class="col-12">
                    <input id="submit" class="btn btn-secondary" type="submit" name="submit" value="submit">
                </div>
            </div>
            </form>
            <?php
            if(isset($_GET['submit']))
                {
            ?>
            <div class="row mt-7">
                <div class="col-12">
                    <button id="btn" class="btn btn-secondary">Excel Export</button>
                </div>
            </div>

            <?php } ?> 

            <?php
          $server=1;
          $year ='';
          $area ='';
          $region='';
          $period='';
          $quarterly ='';
          $results= [];
          $data = [];
          $areas = [];
		      $results= [];
          $data = [];
          $sum = [];
          $sum_question = [];
          $total_sum = [];
          $areas = [];
          $areas_total = [];
          $total_percent=[];
          $weightage=[];
          $final_weightage=[];
          $total_sum_question=0;
          $araa =$a_id;
          $tqpoint =0;
          $tqpoint1 =0;
          $tqpoint2 =0;
          $tqpoint3 =0;
          $tqpoint4 =0;
          $qpoint =0;
          $qpoint1 =0;
          $qpoint2 =0;
          $qpoint3 =0;
          $qpoint4 =0;
          if(isset($_GET['submit']))
          {
            $divisionid = $_GET['division'];
            if(isset($_GET['region']))
            {
              $region = $_GET['region'];
            }
            if(isset($_GET['period']))
            {
              $period = $_GET['period'];
              // dd($period);
            }
            
            if(isset($_GET['quarter']))
            {
              $quarter = $_GET['quarter'];
              if($quarter){
                $exp = explode("-",$quarter);
                $year = $exp[0];
                $quarterly = $exp[1];
              }
            }
            if($divisionid!='' and $divisionid!='select' and $region=='' and $quarterly=='' and $period=='')
            {
              // dd("Only Division");
              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $area = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid') order by area_id asc"));
              foreach ($area as $r) 
              {
                $areas[] = $r->area_id;
              } 
                if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid!='' and $region !='' and $area == '' and $quarterly=='' and $period=='')
            {
              // dd("Division and region");
              $divisionid = $_GET['division'];
              $regionid = $_GET['region'];
              // dd($region);
              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              header("Location: http://scm.brac.net/mnwprogoti/Export");
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid!='' and $region !='' and $area!='' and $quarterly=='' and $period=='')
            {
              // dd("Division and region and area");
              $divisionid = $_GET['division'];
              $regionid = $_GET['region'];
              // dd($region);
              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid' and area_id='$area'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid' and area_id='$area') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and region_id='$regionid' and area_id='$area') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid!='' and $region !='' and $area!='' and $quarterly != '' and $period=='')
            {
              // dd("Division and region and area and quterly");
              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
            }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            // }elseif(isset($_GET['period']) and ($divisionid=='' || $divisionid=='select'))
            }elseif($divisionid !='' and $region !='' and $area!='' and $quarterly != '' and $period !='')
            {
              // dd("Division and region and area and quterly and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region !='' and $area=='' and $quarterly != '' and $period =='')
            {
              // dd("Division and region and quterly");

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly' ) order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region !='' and $area=='' and $quarterly != '' and $period !='')
            {
              // dd("Division and region and quterly and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region =='' and $area=='' and $quarterly != '' and $period !='')
            {
              // dd("Division and quterly and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region =='' and $area=='' and $quarterly != '' and $period =='')
            {
              // dd("Division and quterly");

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly'"));
              foreach ($query as $row) 
              {
                $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
              $_SESSION['alert'] = "No area Found!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg(DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  year='$year' and quarterly='$quarterly') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid =='' and $region =='' and $area=='' and $quarterly != '' and $period !='')
            {
              // dd("quterly and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid =='' and $region =='' and $area=='' and $quarterly == '' and $period !='')
            {
            //   dd("period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                    //   dd($sectionsdata);
            }elseif($divisionid !='' and $region =='' and $area=='' and $quarterly == '' and $period !='')
            {
              // dd("division and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and  datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE division_id='$divisionid' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region !='' and $area=='' and $quarterly == '' and $period !='')
            {
              // dd("division and region and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE region_id='$region' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and  datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE region_id='$region' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid !='' and $region !='' and $area!='' and $quarterly == '' and $period !='')
            {
              // dd("division and region and area and period");
              $periods = $_GET['period'];
              $period_ary=explode("/",$periods);
              $datestart=$period_ary[0];
              $dateend=$period_ary[1];

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE area_id='$area' and datestart='$datestart' and  dateend='$dateend'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and  datestart='$datestart' and  dateend='$dateend') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE area_id='$area' and datestart='$datestart' and  dateend='$dateend') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }elseif($divisionid =='' and $region =='' and $area=='' and $quarterly != '' and $period =='')
            {
              // dd("quarter");

              $query=DB::select(DB::raw("SELECT * FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly'"));
              foreach ($query as $row) 
              {
              $results[] = $row->id;
              }
              $arnch = DB::select(DB::raw("SELECT DISTINCT area_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly') order by area_id asc"));
              foreach ($arnch as $r) 
              {
                $areas[] = $r->area_id;
              } 
              if(empty($areas)){
                $_SESSION['alert'] = "No area Found!";
                if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
                exit ;
              }
              $sectionsdata = DB::select(DB::raw("SELECT CONCAT(section,'.', sub_id) AS si,string_agg
                      (DISTINCT CONCAT( area_id, ':', point), ',')  as  brands, section, sub_id, question_point as fullmark,event_id FROM mnw_progoti.cal_section_point WHERE event_id IN(SELECT id FROM mnw_progoti.monitorevents WHERE year='$year' and quarterly='$quarterly') GROUP BY section,sub_id,question_point,area_id,si,event_id"));
                      // dd($sectionsdata);
            }else{
              $_SESSION['alert'] = "Filter not selected properley!";
              if($server==1){
                  header("Location: http://scm.brac.net/mnwprogoti/Export");
                }else{
                  header( "Location: localhost:8000/Export" );
                }
              exit ;
            }

            foreach ($sectionsdata as $rw) 
            {
				
              $sub_id=$rw->sub_id;
              $data[$rw->si]['fullmark'] = $rw->fullmark;
              $data[$rw->si]['section'] = $rw->section;
              $tmp = explode(",", $rw->brands);
              //var_dump($tmp);

              foreach ($tmp as $key => $value) 
              {
                $tmp2 = explode(":", $value);
                $tmp0 = $tmp2[0];
                $tmp1 = $tmp2[1];
                $data[$rw->si]['areas'][$tmp0] = $tmp1;
              }


                $tmp1 = explode(".", $rw->si);
                $section = $tmp1[0];
                $qestion = $tmp1[1];
                $tmp2 = explode(":", $rw->brands);
                $area = $tmp2[0];
                $point = $tmp2[1];
                $event_id=$rw->event_id;
                // if($event_id==1029){
                //   dd($rw);
                // }
                // dd('test');
                $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$qestion)->where('qno',0)->get();
                 
                if($qname->isEmpty())
                {
                  //echo "Huda";
                    $data[$rw->si]['questions']='';
                }
                else
                {
                  $data[$rw->si]['questions']=$qname[0]->qdesc;
                }
				//dd($qname->qdesc);
               // $data[$rw->si]['questions']=$qname->qdesc;
        //var_dump($data);
                $area=strval($area);
                if($section==1){
                    $sum_data=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='1'"));
                    $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) as qponit FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='1'"));
                    $qpoint = $sum_question_data[0]->qponit;
                    if($tqpoint < $qpoint)
                    {
                      $tqpoint = $qpoint;
                    }
                    
                    // if(strlen($branch)==3){
                    //   $branch='0'.$branch;
                    // }
                    // foreach ($areas as $row) {
                    //     while($branch != $row){
                    //     }
                    // }
                    // if(in_array( $branch ,$areas )){
                    //   dd($row);
                    // }
                    $sum[$section][$area]=$sum_data[0]->sum;
                    
                    //$sum_question[$section]=$sum_question_data[0]->sum;
                    $sum_question[$section]=$tqpoint;
                    $percent[$section][$area]=Round(($sum_data[0]->sum*100)/$qpoint,2);
                    $weightage[$area][$section]=ROUND(5*($sum_data[0]->sum/$qpoint));
					
                    /* $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='1'"));

                    $sum[$section][$branch]=$sum_data[0]->sum;
                    $sum_question[$section]=$sum_question_data[0]->sum;
                    $percent[$section][$branch]=Round($sum_data[0]->sum/$sum_question_data[0]->sum*100,2);
                    $total_sum_question+=$sum_question_data[0]->sum;
                    $weightage[$branch][$section]=ROUND(5*($sum_data[0]->sum/$sum_question_data[0]->sum)); */
                  // dd($sum_question);
                    }elseif($section==2){
                        $sum_data=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='2'"));
                        $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) as qponit FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='2'"));
                        $qpoint = $sum_question_data[0]->qponit;
                        if($tqpoint1 < $qpoint)
                        {
                          $tqpoint1 = $qpoint;
                        }
                        $sum[$section][$area]=$sum_data[0]->sum;
                        //$sum_question[$section]=$sum_question_data[0]->sum;
                        $sum_question[$section]=$tqpoint1;
                        $percent[$section][$area]=Round(($sum_data[0]->sum*100)/$qpoint,2);
                        $weightage[$area][$section]=ROUND(10*($sum_data[0]->sum/$qpoint));
                        /* $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='2'"));

                        $sum[$section][$branch]=$sum_data[0]->sum;
                        $sum_question[$section]=$sum_question_data[0]->sum;
                        $percent[$section][$branch]=Round($sum_data[0]->sum/$sum_question_data[0]->sum*100,2);
                        $weightage[$branch][$section]=ROUND(10*($sum_data[0]->sum/$sum_question_data[0]->sum)); */
                    }elseif($section==3){
                        $sum_data=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='3'"));
                        $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) as qponit FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='3'"));
                        $qpoint = $sum_question_data[0]->qponit;
                        if($tqpoint2 < $qpoint)
                        {
                          $tqpoint2 = $qpoint;
                        }
                        $sum[$section][$area]=$sum_data[0]->sum;
                        //$sum_question[$section]=$sum_question_data[0]->sum;
                        $sum_question[$section]=$tqpoint2;
                        $percent[$section][$area]=Round(($sum_data[0]->sum*100)/$qpoint,2);
                        $weightage[$area][$section]=ROUND(30*($sum_data[0]->sum/$qpoint));
                        /* $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='3'"));

                        $sum[$section][$branch]=$sum_data[0]->sum;
                        $sum_question[$section]=$sum_question_data[0]->sum;
                        $percent[$section][$branch]=Round($sum_data[0]->sum/$sum_question_data[0]->sum*100,2);
                        $weightage[$branch][$section]=ROUND(30*($sum_data[0]->sum/$sum_question_data[0]->sum)); */

                    }elseif($section==4){
                        $sum_data=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='4'"));
                        $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) as qponit FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='4'"));
                        $qpoint = $sum_question_data[0]->qponit;
                        if($tqpoint3 < $qpoint)
                        {
                          $tqpoint3 = $qpoint;
                        }
                        $sum[$section][$area]=$sum_data[0]->sum;
                        //$sum_question[$section]=$sum_question_data[0]->sum;
                        $sum_question[$section]=$tqpoint3;
                        $percent[$section][$area]=Round(($sum_data[0]->sum*100)/$qpoint,2);
                        $weightage[$area][$section]=ROUND(35*($sum_data[0]->sum/$qpoint));
                        /* $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='4'"));

                        $sum[$section][$branch]=$sum_data[0]->sum;
                        $sum_question[$section]=$sum_question_data[0]->sum;
                        $percent[$section][$branch]=Round($sum_data[0]->sum/$sum_question_data[0]->sum*100,2);
                        $weightage[$branch][$section]=ROUND(35*($sum_data[0]->sum/$sum_question_data[0]->sum)); */

                    }elseif($section==5){
                        $sum_data=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='5'"));
                        $sum_question_data=DB::select(DB::raw("SELECT sum(question_point) as qponit FROM mnw_progoti.cal_section_point where event_id='$event_id' and section='5'"));
                        $qpoint = $sum_question_data[0]->qponit;
                        if($tqpoint4 < $qpoint)
                        {
                          $tqpoint4 = $qpoint;
                        }
                        $sum[$section][$area]=$sum_data[0]->sum;
                        //$sum_question[$section]=$sum_question_data[0]->sum;
                        $sum_question[$section]=$tqpoint4;
                        // $percent[$section][$branch]=Round($sum_data[0]->sum/$qpoint*100,2);
                        $percent[$section][$area]=Round(($sum_data[0]->sum*100)/$qpoint,2);
                        $weightage[$area][$section]=ROUND(20*($sum_data[0]->sum/$qpoint));

                        // dd($percent);
                        
                    }


                    $total=DB::select(DB::raw("SELECT sum(question_point) as question_point,sum(point) as point FROM mnw_progoti.cal_section_point where event_id='$event_id'"));
                    $total_percent[$area]=Round(($total[0]->point*100)/$total[0]->question_point,2);
              /*$data[$values['si']]['fullmark'] = $values['fullmark'];
              $data[$values['si']]['section'] = $values['section'];     
              $tmp = explode(",", $values['brands']);
              foreach ($tmp as $key => $value) {
                  $tmp2 = explode(":", $value);
                  $data[$values['si']]['areas'][$tmp2[0]] = $tmp2[1];
              }*/
			  //echo $sub_id."/".$section."-";
            }

            // dd($sum);
            for ($i=1; $i < 6; $i++) { 
              $areacount=count($areas);
              if (array_key_exists($i, $sum)) {
                $sumcount=count($sum[$i]);
                if($sumcount!=$areacount){
                  $sumkey=array_keys($sum[$i]);
                  $output = array_merge(array_diff($sumkey, $areas), array_diff($areas, $sumkey));
                  foreach ($output as $row) {
                    $sum[$i][$row]=0;
                  }
                }
              }
              
            }

            for ($i=1; $i < 6; $i++) { 
              $areacount=count($areas);
              if (array_key_exists($i, $percent)) {
                $percentcount=count($percent[$i]);
                if($percentcount!=$areacount){
                  $percentkey=array_keys($percent[$i]);
                  $output = array_merge(array_diff($percentkey, $areas), array_diff($areas, $percentkey));
                  foreach ($output as $row) {
                    $percent[$i][$row]=0;
                  }
                }
              }
              
            }
            // dd($sum);
            //die;
            $total_sum_question=array_sum($sum_question);
            foreach ($sectionsdata as $row) 
            {
              $tmp = explode(":", $row->brands);
              $brn=$tmp[0];
              $total=DB::select(DB::raw("SELECT sum(point) FROM mnw_progoti.cal_section_point where area_id='$brn' and event_id='$row->event_id'"));
              $areas_total[$brn]=$total[0]->sum;
              // $total_percent[$brn]=Round($total[0]->sum/$total_sum_question*100,2);
            }
            $total_wieght=0;
            foreach ($weightage as $key=>$area) 
            {
              // dd($key);
              foreach ($area as $weight) {
                $total_wieght+=$weight;
              }
              $final_weightage[$key]=$total_wieght;
              $total_wieght=0;
            }

            if(!empty($sum)){
              for ($i=1; $i < 6; $i++) {
                if (array_key_exists($i, $sum)) {
                  ksort($sum[$i]);
                }
              }
            }
            if(!empty($percent)){
              for ($i=1; $i < 6; $i++) {
                if (array_key_exists($i, $percent)) {
                  ksort($percent[$i]);
                }
              }
            }

            ksort($areas_total);
            ksort($total_percent);
            ksort($final_weightage);

            // dd($sum);
            
            
            // dd($areas_total);
            // dd($final_weightage);
            // echo "<pre>";  
            // var_dump($section);
            // var_dump($sum);
            // die();
          ?>

<?php
$aname ='';
$rname='';
$dname='';
//  $area = DB::table('branch')->where('area_id',$area)->get();
//  if(!$area->isEmpty())
//  {
if($_GET['division'] !='' and $_GET['division']!='select'){
  $division_id = $_GET['division'];
  $dname = DB::table('branch')->select('division_name')->where('division_id',$division_id)->groupBy('division_name')->first();
  $dname = $dname->division_name;
}
if(isset($_GET['region'])){
  if($_GET['region'] !=''){
  $region_id = $_GET['region'];
  $rname = DB::table('branch')->select('region_name')->where('region_id',$region_id)->groupBy('region_name')->first();
  $rname = $rname->region_name;
  }
}


// } 
?>
            <div class="row mt-7">
                <div class="col-12">
                  <table class="table table-borderless" style="margin-bottom: 0;">
                    <tr style="text-align: center">
                      <th style="padding: 0.35rem" colspan="3">BRAC Microfinance</th>
                    </tr>
                    <tr style="text-align: center">
                      <th style="padding: 0.35rem" colspan="3">Dabi Monitoring Unit</th>
                    </tr>
                    <tr style="text-align: center">
                      <th style="padding: 0.35rem" colspan="3">Area Wise Monitoring New Way Score</th>
                    </tr>
                    <tr style="height: 8px"><td></td></tr>
                    <tr>
                      <th style="padding: 0.35rem" width="33%">Division: <?php echo $dname; ?></th>
                      <th style="padding: 0.35rem" width="33%">Region: <?php echo $rname; ?></th>
                    </tr>
                    <tr>
                      <th style="padding: 0.35rem">Quarter : <?php
                        if(isset($_GET['quarter'])){
                            echo $_GET['quarter'];
                          }
                         ?> </th>
                      <th style="padding: 0.35rem" colspan="2">Period : <?php
                        if(isset($_GET['period'])){
                            $periods = $_GET['period'];
                            if($periods){
                                $period_ary=explode("/",$periods);
                                $datestart=$period_ary[0];
                                $dateend=$period_ary[1];
                                echo $datestart." to ".$dateend;
                            }
                          }
                         ?> </th>
                    </tr>
                    <tr style="height: 8px"><td></td></tr>
                    <tr>
                  </table>
                  <div class="table-responsive table-wrapper" style="height: 550px">
                  <table style="font-size:13" class="table table-bordered" id="tblReport">
                    <thead class="noExl" style="display: none">
                        <tr align="center">
                            <td></td>
                            <td></td>
                            <td>BRAC Microfinance</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr align="center">
                          <td></td>
                          <td></td>
                          <td>Dabi Monitoring Unit</td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                           <td></td>
                           <td></td>
                           <td>Area Wise Monitoring New Way Score</td>
                           <td></td>
                           <td></td>
                           <td></td>
                        </tr>
                              
                        <tr>
                          <td>Division: <?php echo $dname; ?></td>
                          <td>Region: <?php echo $rname; ?></td>
                        </tr>
                        <tr>
                            <td>Quarter: 
                              <?php
                                if(isset($_GET['quarter'])){
                                    echo $_GET['quarter'];
                                  }
                                 ?> 
                              </td>
                            <td>Period: 
                              <?php
                                if(isset($_GET['period'])){
                                  $periods = $_GET['period'];
                                  if($periods){
                                    $period_ary=explode("/",$periods);
                                    $datestart=$period_ary[0];
                                    $dateend=$period_ary[1];
                                    echo $datestart." to ".$dateend;
                                  }
                                  }
                                 ?> 
                              </td>
                        </tr>
                    </thead>
                        
                        <tr class="brac-color-pink">
                            <th style="text-align: center;vertical-align:middle;">SI</th>
                            <th style="text-align: center;vertical-align:middle;">Full Marks</th>
                            <th style="text-align: center;vertical-align:middle;">Section & Indicator Name</th>
                            <?php 

                              foreach ($areas as $area) {
                                // $arcode =$area[0];
                                $arname = DB::table('branch')->where('area_id',$area)->get();
                                if($arname->isEmpty())
                                {
                                  $name = '';
                                }
                                else
                                {
                                  $name = $arname[0]->area_name;
                                }
                                  echo '<th style="text-align: center;vertical-align:middle;">'.$name.'<br>'.$area.'</th>';
                              }
                            ?>
                        </tr>
                        <tbody>
                            {{-- Section 1 --}}
                              <?php 

                              $flg =0;
                              //var_dump($data);
                              $check =0;
                              foreach ($data as $si  => $info)
                              {
                                 $explode = explode(".", $si);
                                 $section = $explode[0];
                                 $sub_id = $explode[1];
                                           if($section =='1')
                                           {
                                    $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                    
                                    ?>
                                    <tr align="center">
                                      <td><?php echo $si; ?></td>
                                      <td><?php echo $info['fullmark']; ?></td>
                                      <td><?php echo $info['questions']; ?></td>
                                       <?php
                                       foreach ($areas as $arn) 
                                       {
                                        if(isset($info['areas'][$arn]))
                                        {
                                          ?>
                                          <td><?php echo $info['areas'][$arn]; ?></td>
                                          <?php
                                        }
                                        else
                                        {
                                          ?>
                                          <td></td>
                                          <?php
                                        }
                                       }
                                       ?>
                                    </tr>
          
                                    <?php
                                           }
                                          //  else
                                          //  {
                                            //     $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                          //  }
                                  
                                   ?>
                                <?php
                                 }
          
                                //  if($qname->isEmpty())
                                //  {
                                //   $qnme = '';
                                //  }
                                //  else
                                //  {
                                //   $qnme = $qname[0]->qdesc;
                                //  }
                                ?>
                                <?php
                              
                              ?>
                              <tr style="color: red;">
                                <td>Section Total</td>
                                <td><?php //echo $sum_question[1]; ?></td>
                                <td></td>
                                <?php
                                  foreach ($sum[1] as $total) 
                                  {                          
                                ?>
                                    <td align="center"><?php echo $total; ?></td>
                                <?php
                                  }
                                ?>
                              </tr>
                              <tr style="color: blue">
                                <td>Section Percentage</td>
                                <td></td>
                                <td></td>
                                 <?php
                                  foreach ($percent[1] as $total) 
                                  {
                                    
                                    ?>
                                    <td align="center"><?php echo $total; ?></td>
                                    <?php
                                 }
                                 ?>
                              </tr>
                              {{-- send of section 1 --}}
                              {{-- section 2 --}}
                              <?php 
                              $flg =0;
                              //var_dump($data);
                                        $check =0;
                              foreach ($data as $si  => $info)
                              {
                                 $explode = explode(".", $si);
                                 $section = $explode[0];
                                 $sub_id = $explode[1];
                                           if($section =='2')
                                           {
                                    $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                    ?>
                                    <tr align="center">
                                      <td><?php echo $si; ?></td>
                                      <td><?php echo $info['fullmark']; ?></td>
                                      <td><?php echo $info['questions']; ?></td>
                                       <?php
                                       foreach ($areas as $arn) 
                                       {
                                        if(isset($info['areas'][$arn]))
                                        {
                                          ?>
                                          <td><?php echo $info['areas'][$arn]; ?></td>
                                          <?php
                                        }
                                        else
                                        {
                                          ?>
                                          <td></td>
                                          <?php
                                        }
                                       }
                                       ?>
                                    </tr>
          
                                    <?php
                                           }
                                          //  else
                                          //  {
                                            //     $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                          //  }
                                  
                                   ?>
                                <?php
                                 }
                                 ?>
                                 <tr style="color: red">
                                  <td>Section Total</td>
                                  <td><?php //echo $sum_question[2]; ?></td>
                                  <td></td>
                                   <?php
                                    foreach ($sum[2] as $total) 
                                    {
                                      
                                      ?>
                                      <td align="center"><?php echo $total; ?></td>
                                      <?php
                                   }
                                   ?>
                                </tr>
                                <tr style="color: blue">
                                    <td>Section Percentage</td>
                                    <td></td>
                                    <td></td>
                                     <?php
                                      foreach ($percent[2] as $total) 
                                      {
                                        
                                        ?>
                                        <td align="center"><?php echo $total; ?></td>
                                        <?php
                                     }
                                     ?>
                                  </tr>
                                {{-- end of section 2 --}}
                                {{-- section 3 --}}
                                <?php 
                              $flg =0;
                              //var_dump($data);
                                        $check =0;
                              foreach ($data as $si  => $info)
                              {
                                 $explode = explode(".", $si);
                                 $section = $explode[0];
                                 $sub_id = $explode[1];
                                           if($section =='3')
                                           {
                                    $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                    ?>
                                    <tr align="center">
                                      <td><?php echo $si; ?></td>
                                      <td><?php echo $info['fullmark']; ?></td>
                                      <td><?php echo $info['questions']; ?></td>
                                       <?php
                                       foreach ($areas as $arn) 
                                       {
                                        if(isset($info['areas'][$arn]))
                                        {
                                          ?>
                                          <td><?php echo $info['areas'][$arn]; ?></td>
                                          <?php
                                        }
                                        else
                                        {
                                          ?>
                                          <td></td>
                                          <?php
                                        }
                                       }
                                       ?>
                                    </tr>
          
                                    <?php
                                           }
                                          //  else
                                          //  {
                                            //     $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                          //  }
                                  
                                   ?>
                                <?php
                                 }
                                 ?>
                                 <tr style="color: red">
                                    <td>Section Total</td>
                                    <td><?php //echo $sum_question[3]; ?></td>
                                    <td></td>
                                     <?php
                                      foreach ($sum[3] as $total) 
                                      {
                                        
                                        ?>
                                        <td align="center"><?php echo $total; ?></td>
                                        <?php
                                     }
                                     ?>
                                  </tr>
                                  <tr style="color: blue">
                                      <td>Section Percentage</td>
                                      <td></td>
                                      <td></td>
                                       <?php
                                        foreach ($percent[3] as $total) 
                                        {
                                          
                                          ?>
                                          <td align="center"><?php echo $total; ?></td>
                                          <?php
                                       }
                                       ?>
                                    </tr>
                                  {{-- end of section 3 --}}
                                  {{-- section 4 --}}
                                  <?php 
                              $flg =0;
                              //var_dump($data);
                                        $check =0;
                              foreach ($data as $si  => $info)
                              {
                                 $explode = explode(".", $si);
                                 $section = $explode[0];
                                 $sub_id = $explode[1];
                                           if($section =='4')
                                           {
                                    $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                    ?>
                                    <tr align="center">
                                      <td><?php echo $si; ?></td>
                                      <td><?php echo $info['fullmark']; ?></td>
                                      <td><?php echo $info['questions']; ?></td>
                                       <?php
                                       foreach ($areas as $arn) 
                                       {
                                        if(isset($info['areas'][$arn]))
                                        {
                                          ?>
                                          <td><?php echo $info['areas'][$arn]; ?></td>
                                          <?php
                                        }
                                        else
                                        {
                                          ?>
                                          <td></td>
                                          <?php
                                        }
                                       }
                                       ?>
                                    </tr>
          
                                    <?php
                                           }
                                          //  else
                                          //  {
                                            //     $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                          //  }
                                  
                                   ?>
                                <?php
                                 }
                                 ?>
                                 <?php
                                  if (array_key_exists($i, $percent)) {
                                 ?>
                                 <tr style="color: red">
                                    <td>Section Total</td>
                                    <td>
                                      <?php //echo $sum_question[4]; ?>
                                    </td>
                                    <td></td>
                                     <?php
                                      foreach ($sum[4] as $total) 
                                      {
                                        
                                        ?>
                                        <td align="center"><?php echo $total; ?></td>
                                        <?php
                                     }
                                     ?>
                                  </tr>
                                  <tr style="color: blue">
                                      <td>Section Percentage</td>
                                      <td></td>
                                      <td></td>
                                       <?php
                                        foreach ($percent[4] as $total) 
                                        {
                                          
                                          ?>
                                          <td align="center"><?php echo $total; ?></td>
                                          <?php
                                       }
                                       ?>
                                    </tr>
                                    <?php } ?>
                                  {{-- end of section 4 --}}
                                  {{-- section 5 --}}
                                  <?php 
                                  $flg =0;
                                  //var_dump($data);
                                  $check =0;
                                  foreach ($data as $si  => $info)
                                  {
                                     $explode = explode(".", $si);
                                     $section = $explode[0];
                                     $sub_id = $explode[1];
                                     if($section =='5')
                                     {
                                    // dd($info);
          
                                        $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                        ?>
                                        <tr align="center">
                                          <td><?php echo $si; ?></td>
                                          <td><?php echo $info['fullmark']; ?></td>
                                          <td><?php echo $info['questions']; ?></td>
                                           <?php
                                           foreach ($areas as $arn) 
                                           {
                                            if(isset($info['areas'][$arn]))
                                            {
                                              ?>
                                              <td><?php echo $info['areas'][$arn]; ?></td>
                                              <?php
                                            }
                                            else
                                            {
                                              ?>
                                              <td></td>
                                              <?php
                                            }
                                           }
                                           ?>
                                        </tr>
              
                                        <?php
                                     }
                                    //  else
                                    //  {
                                    //     $qname = DB::table('mnw_progoti.def_questions')->where('sec_no',$section)->where('sub_sec_no',$sub_id)->where('qno',0)->get();
                                    //  }
                                      
                                       ?>
                                    <?php
                                     }
                                     ?>
                                     <tr style="color: red">
                                        <td>Section Total</td>
                                        <td><?php //echo $sum_question[5]; ?></td>
                                        <td></td>
                                         <?php
                                          foreach ($sum[5] as $total) 
                                          {
                                            
                                            ?>
                                            <td align="center"><?php echo $total; ?></td>
                                            <?php
                                         }
                                         ?>
                                      </tr>
                                      <tr style="color: blue">
                                          <td>Section Percentage</td>
                                          <td></td>
                                          <td></td>
                                           <?php
                                            foreach ($percent[5] as $total) 
                                            {
                                              
                                              ?>
                                              <td align="center"><?php echo $total; ?></td>
                                              <?php
                                           }
                                           ?>
                                      </tr>
                                      {{-- send of section 5 --}}
                                      {{-- Tatal section --}}
                                      <tr style="color: green">
                                        <td></td>
                                        <td align="center">Total</td>
                                        <td></td>
                                        <?php
                                          foreach ($areas_total as $total) 
                                          {
                                            
                                            ?>
                                            <td align="center"><?php echo $total; ?></td>
                                            <?php } ?>
                                      </tr>
                                      <tr style="color: green">
                                        <td></td>
                                        <td align="center" scope="row">Total Percentage</td>
                                        <td></td>
                                        <?php
                                          foreach ($total_percent as $total) 
                                          {
                                            
                                            ?>
                                            <td align="center"><?php echo $total; ?></td>
                                            <?php
                                        }
                                        ?>
                                      </tr>
                                      <tr style="color: green">
                                        <td></td>
                                        <td align="center" scope="row">Final weightage score</td>
                                        <td></td>
                                        <?php
                                          foreach ($final_weightage as $total) 
                                          {
                                            
                                            ?>
                                            <td align="center"><?php echo $total; ?></td>
                                            <?php
                                        }
                                        ?>
                                      </tr>
                                      <tr style="color: green">
                                        <td></td>
                                        <td align="center" scope="row">Area Rating</td>
                                        <td></td>
                                        <?php
                                        foreach ($final_weightage as $total) 
                                        {
                                          if($total>='85'){
                                            $g='G';
                                          }elseif($total>='70' && $total<'85'){
                                            $g='M';
                                          }elseif($total<'70'){
                                            $g='Need Improvement';
                                          }else{
                                            $g='';
                                          }
                                          ?>
          
                                          <td align="center"><?php echo $g; ?></td>
                                          <?php
                                        }
                                        ?>
                                      </tr>
                          </tbody>
                  </table>
                  </div>
                </div>
            </div>
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
<script src="{{ asset('ui/backend/js/jquery.table2excel.min.js') }}"></script>
<script>       
    $(document).ready(function(){

    $("#submit").click(function(){
    $("#myForm").trigger("reset");
    });

    $("#btn").click(function () {
    // alert("Click");
    $("#tblReport").table2excel({ 
        exclude: ".noExl",
        name: "Results",
        filename: "<?php echo date('Y-m-d'); ?>_Export",
        fileext: ".xls",
    });
    });

    });

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
         // $("#quarter").empty();
         
         var option2 = "<option value=''>select</option>";
         $("#region_id").append(option2);
         for(var i = 0; i < len; i++)
         {
           var option = "<option value='"+data[i].region_id+"'>"+data[i].region_name+"</option>"; 
 
           $("#region_id").append(option); 
         }
         
       },
       error: function (ex) {
         alert('Failed to retrieve Area.');
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
         $("#area_id").empty();
         $("#branch_id").empty();
         
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
   $('#area_id').on('change', function() {
     var area_id= this.value;
     //alert(area_id);
     if(area_id !='')
     {  
       $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
       type: 'POST',
       url: '/mnwprogoti/quarter',cache: false,
       dataType: 'json',
       data: { id: area_id },
       success: function (data) {
         
         //var d = data[0].region_id;
         //console.log(d);
         var len = data.length;
         $("#quarter").empty();
         
         var option2 = "<option value=''>select</option>";
         $("#quarter").append(option2);
         for(var i = 0; i < len; i++)
         {
           var option = "<option value='"+data[i].year+"-"+data[i].quarterly+"'>"+data[i].year+"-"+data[i].quarterly+"</option>"; 
 
           $("#quarter").append(option); 
         }
         
       },
       error: function (ex) {
         alert('Failed to retrieve Area.');
       }
     });
       
       return;
     }
   });
 
   $('#quarter').on('change', function() {
     var quarterly= this.value;
     var region_id = $('#region_id').val();
    //  alert(area_id);
     var res = quarterly.split("-");
     var year=res[0];
     var quarter=res[1];
    //  if(quarterly !='')
    //  {  
       $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
         type: 'POST',
         url: '/mnwprogoti/period',cache: false,
         dataType: 'json',
         data: { year: year,quarter: quarter,region_id:region_id },
         success: function (data) {
           //var d = data[0].region_id;
           console.log(data);
           var len = data.length;
           $("#period").empty();
           
           var option2 = "<option value=''>select</option>";
           $("#period").append(option2);
           for(var i = 0; i < len; i++)
           {
             var option = "<option value='"+data[i].datestart+"/"+data[i].dateend+"'>"+data[i].datestart+" to "+data[i].dateend+"</option>"; 
 
             $("#period").append(option); 
           }
           
         },
         error: function (ex) {
           alert('Failed to retrieve Area.');
         }
       });
       
       return;
    //  }
   });
  </script>
@endsection