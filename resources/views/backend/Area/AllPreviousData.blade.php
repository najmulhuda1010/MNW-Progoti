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

@section('title','Area Previous Data')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">All Previous Data</h5>
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
              <table style="text-align: center;font-size:13"  class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>Monitoring Event</th>
                  <th>Monitoring Period</th>
                  <th>Result</th>
                </tr>
                <tbody>
                    <?php
                     $id= 1;
                     foreach ($alldata as $row) 
                     {
                        $g =0;
                        $m=0;
                        $p =0;
                        $cycle = $row->event_cycle;
                        $area_id = $row->area_id;
                        if($cycle =='')
                        {
                          $cycle="";
                        }
                        $score = $row->score;
                        if($score >=85)
                        {
                          $g = "Good";
                          
                        }
                        else if($score >=70 and $score < 85)
                        {
                          $m = "Modarate";
                          
                        }
                        else if($score < 70)
                        {
                          $p = "Need Improvment"; 
                        }
                        ?>    
          
                        <tr>
                            <td>
                            <a style="color: #3699FF" href="AreaDashboard?evid=<?php echo $row->id.","."true".",".$area_id; ?>"><?php echo $cycle; ?></a></td>
                            <td><?php echo $row->datestart." to ".$row->dateend; ?></td>
                            <td><?php if($g){
                            echo "Good";
                            } if($m){
                            echo "Moderate";
                            } if($p){
                            echo "Need Improvment";
                            } ?></td>
                        </tr>
                        <?php 
                        $id++;
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
        if($evcycle !='')
        {
          $cycle = $evcycle;
        }
        else{
          $cycle="Default";
        }
        
        ?>

        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
              <h3 class="card-title">Monitoring Event: {{ $cycle }}  </h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13"  class="table">
                <tr class="brac-color-pink">
                  <th>Section</th>
                  <th width="60%">Section Name</th>
                  <th width="20%">Achievement %</th>
                </tr>
              </table>
              <?php

             
           $totalscore =0;
           $totalpoint =0;
           $totalqpoint =0;
           $section=0;
           $name ='';
           //$eid ='';
           $falg=0;
           if($evid !='')
           {
             
             $datareadmnevents =DB::select(DB::raw("select section from mnw_progoti.cal_section_point where event_id='$evid' group by section 
order by section ASC"));
             if(!empty($datareadmnevents))
             {
               foreach($datareadmnevents as $row)
               {
                  $section = $row->section;
                $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='0' and qno=0"));
                if(empty($secname))
                {
                  
                }
                else{
                  $name = $secname[0]->qdesc;
                }
                  $br_datareadmonievents = DB::select(DB::raw("select sum(point) as point, sum(question_point) as qpoint from mnw_progoti.cal_section_point where event_id='$evid' and section='$section'"));
                if(!empty($br_datareadmonievents))
                {
                  $totalpoint = $br_datareadmonievents[0]->point;
                  $totalqpoint = $br_datareadmonievents[0]->qpoint;
                  $totalscore = round((($totalpoint/$totalqpoint)*100));
                  
                  ?>
                  <table style="text-align: center;font-size:13"  class="table table-striped dt-responsive nowrap" cellspacing="0" width="100%">
                    <tr>
                        <td nowrap="nowrap" style="color: black; "><button id="<?php echo $section; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $section; ?>);" >+</button>
                        <a style="color: black;"><?php echo "Section: ".$section; ?></a></td>
                        <td width="60%" style="color: black;"><?php echo $name; ?></td>
                        <td width="20%" style="color: black; "><?php echo $totalscore."%"; ?></td>
                    </tr>
                  </table>
                  <table style="text-align: center;font-size:13"  id="<?php echo "dv".$section; ?>" class="table table-striped dt-responsive nowrap" cellspacing="0" width="100%">
                      
                      <thead>
                        <tr class="brac-color-pink">
                          <th>Section Number</th>
                        
                          <th>Details</th>
                          
                          <th>Achievement %</th>
          
                        </tr>
                      </thead>
                      <?php 
                      $subsectiondata = DB::select( DB::raw("select * from mnw_progoti.cal_section_point  where event_id='$evid' and section='$section' order by sub_id ASC"));
                      // dd($subsectiondata);
                      if(empty($subsectiondata))
                      {
                        
                      }
                      else
                      {
                        foreach($subsectiondata as $row)
                        {
                            $qno = $row->qno;
                          $subid= $row->sub_sec_id;
                          $point = $row->point;
                          $qpoint = $row->question_point;
                          $scores = round(($point/$qpoint)*100,2);
                          ?>
                           <tbody>
                            <tr>
                              <?php 
                              $sec = $row->section;
                              $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subid' and qno=0"));
                              if(empty($secname))
                              {
                                
                              }
                              else{
                                $name = $secname[0]->qdesc;
                              }
                              if($sec=='1')
                              {
                                if($point=='6')
                                {
                                  $percent = 100;
                                }
                                else if($point=='4')
                                {
                                  $percent = 60;
                                }
                                else if($point=='2')
                                {
                                  $percent = 40;
                                }
                                $id=1;
                                if($subid=='5'){
                                    ?>
                                   <td style=""><a href="frauddocuments?event=<?php echo $evid; ?>"><?php echo $row->section.".".$row->sub_id;  ?></a></td>
                                   <?php 
                                   }else{
                                   ?>
                                   <td style=""><?php echo $row->section.".".$row->sub_id;  ?></td>
                                   <?php } ?>
                                <td><?php echo $name; ?></td>
                                <td style=""><?php echo  $scores."%"; ?></td>
                                <?php
                                $id++;
                              }
                              else if($sec=='2')
                              {
                                $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subid' and qno=0"));
                                if(empty($secname))
                                {
                                  
                                }
                                else{
                                  $name = $secname[0]->qdesc;
                                }
                                if($point=='6')
                                {
                                  $percent = 100;
                                }
                                else if($point=='4')
                                {
                                  $percent = 60;
                                }
                                else if($point=='2')
                                {
                                  $percent = 40;
                                }
                                ?>
                                <td style=""><?php echo $row->section.".".$row->sub_id;;  ?></td>
                                <td><?php echo $name; ?></td>
                                <td style=""><?php echo  $scores."%"; ?></td>
                                <?php
                              }
                              else if($sec=='3')
                              {
                                $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subid' and qno=0"));
                                if(empty($secname))
                                {
                                  
                                }
                                else{
                                  $name = $secname[0]->qdesc;
                                }
                                if($point=='6')
                                {
                                  $percent = 100;
                                }
                                else if($point=='4')
                                {
                                  $percent = 60;
                                }
                                else if($point=='2')
                                {
                                  $percent = 40;
                                }
                                ?>
                                <td style=""><?php echo $row->section.".".$row->sub_id;;  ?></td>
                                <td><?php echo $name; ?></td>
                                <td style=""><?php echo  $scores."%"; ?></td>
                                <?php
                              }
                              else if($sec=='4')
                              {
                                $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subid' and qno=0"));
                                if(empty($secname))
                                {
                                  
                                }
                                else{
                                  $name = $secname[0]->qdesc;
                                }
                                if($point=='6')
                                {
                                  $percent = 100;
                                }
                                else if($point=='4')
                                {
                                  $percent = 60;
                                }
                                else if($point=='2')
                                {
                                  $percent = 40;
                                }
                                else
                                {
                                  $percent =0;
                                }
                                ?>
                                <td style=""><?php echo $row->section.".".$row->sub_id;;  ?></td>
                                <td><?php echo $name; ?></td>
                                <td style=""><?php echo  $scores."%"; ?></td>
                                <?php
                              }
                              else if($sec=='5')
                              {
                                $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subid' and qno=0"));
                                if(empty($secname))
                                {
                                  
                                }
                                else{
                                  $name = $secname[0]->qdesc;
                                }
                                if($point=='6')
                                {
                                  $percent = 100;
                                }
                                else if($point=='4')
                                {
                                  $percent = 60;
                                }
                                else if($point=='2')
                                {
                                  $percent = 40;
                                }
                                else
                                {
                                  $percent =0;
                                }
                                ?>
                                <td style=""><?php echo $row->section.".".$row->sub_id;  ?></td>
                                <td><?php echo $name; ?></td>
                                <td style=""><?php echo  $scores."%"; ?></td>
                                <?php
                              }
                              ?>
                              
                            </tr>
                          </tbody>
                          <?php
                        }
                      }
                      
                      ?>
                    </table> 
                  <?php
                }
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