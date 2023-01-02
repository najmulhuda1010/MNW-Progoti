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

@section('title','Area Report')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Area Report</h5>
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
        <?php
        if($rollid!='2' and $rollid!='3' and $rollid!='4'){          
      ?>
      
        <div class="col-md-12">
          @if (Session::has('message'))
          <div class="alert alert-danger" role="alert">
            {{ Session::get('message') }}
          </div>
          @endif
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <form target="_blank">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                <label class="control-label">Division</label>
                    <select class="form-control" name="division" id="divs">
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
                    <select class="form-control" name="region" id="region_id">
                        <option value="">select</option>
                        <?php 
                        if($reg !='')
                        {
                        $regions = DB::select( DB::raw("select  region_name,region_id from branch  where region_id= '$reg' and program_id='5' group by region_name,region_id order by region_id"));
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
                    <select class="form-control" name="area" id="area_id">
                        <option value="">select</option>
                        <?php 
                        if($area !='')
                        {
                        $areas = DB::select( DB::raw("select  area_name,area_id from branch  where area_id= '$area' and program_id='5' group by area_name,area_id order by area_id"));
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
                </div>
            </form>
            </div>
            <div class="row mt-7">
                <div class="col-md-12">
                    <form method="GET" target="_blank">
                        <label for="example-search-input" class="control-label">Area Search</label>
                        <div class="form-group row">
                            <div class="col-9">
                             <input class="form-control" type="text" list="browsers" id="selected" name="area" required autocomplete="off"/>
                             <datalist id="browsers">
                                <?php
                                if($rollid=='17' )
                                {
                                  $areas = DB::select(DB::raw("select area_id,area_name from mnw_progoti.cluster where c_associate_id ='$userpin' group by area_id,area_name"));
                                }
                                else if($rollid=='18')
                                {
                                  $areas = DB::select(DB::raw("select area_id,area_name from mnw_progoti.cluster where z_associate_id ='$userpin' group by area_id,area_name"));
                                }
                                else
                                {
                                  $areas = DB::select(DB::raw("select area_id,area_name from branch where program_id=5 group by area_id,area_name"));
                                } 
                                  
                                if(!empty($areas))
                                {
                                  foreach($areas as $row)
                                  {
                                    if($rollid=='17' or $rollid=='18')
                                    {
                                      ?>
                                      <option data-value="{{ $row->area_id }}" value="<?php echo $row->area_name; ?>"></option>
                                     <?php
                                    }
                                    else
                                    {
                                      ?>
                                    <option data-value="{{ $row->area_id }}" value="<?php echo $row->area_name; ?>"></option>
                                <?php
                                    }
                                      
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
            </div>
            </form>
            <!--end::Form-->
            <?php } ?>
          </div>
          
          <!--end::Advance Table Widget 4-->
        </div>
        </div>
        <?php 
      if($ar !='')
      {
        ?>
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" class="table table-bordered">
                <tr class="brac-color-pink">
                  <th>SL</th>
                  <th>Monitoring Period</th>
                  <th>Result</th>
                </tr>
                <tbody>
                    <?php
                    $id =1;
                    $g ='';
                    $m='';
                    $p='';
                      foreach ($ar as $row) 
                      {
                       // var_dump($row);11
                         $score =0;
                         $g ='';
                         $m='';
                         $p='';
                         $evnetid= $row->id;
                         $score = $row->score;
                         $year = $row->year;
                         $area_id = $row->area_id;
                         $quarter = $row->quarterly;                       
                         if($score >=85)
                         {
                          $g ="Good";
                         }
                         else if($score >=70 and $score <85)
                         {
                           $m = "Modarate";
                         }
                         else if($score < 70)
                         {
                          $p ='Poor';
                         }
                         else
                         {
                           $p="Poor";
                         }
                        //  dd($area);

                         ?>
                          <tr>
                            <td><?php echo $id++; ?></td>
                            <td><a style="color: #3699FF" href="GlobalReport?event=<?php echo $evnetid; ?>&area=<?php echo $area_id; ?>&region=<?php echo $reg; ?>&division=<?php echo $div; ?>"><?php echo $row->datestart." to ".$row->dateend; ?></a></td>
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
      if($eventid !='')
      {

        $cycle ='';
        $areaa = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->where('id',$eventid)->get();
        //$brnch1 = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit 1"));
        if(!empty($areaa))
        {
          $cycle= $areaa[0]->datestart." to ".$areaa[0]->dateend;
        }
        ?>

        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <!--begin::Form-->
            <div class="card-header">
              <h3 class="card-title">Monitoring Period: <?php echo $cycle; ?> </h3>
            </div><!-- /.box-header -->
            <div class="card-body table-responsive">
              <table style="text-align: center;font-size:13" class="table">
                <tr class="brac-color-pink">
                  <th>Section</th>
                  <th width="60%">Section Name</th>
                  <th width="20%">Achievement %</th>
                </tr>
              </table>
              <?php
            $sp =0;
            $qpnt =0;
            $tscore=0;
            // $brnch = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit 1"));
              if(!empty($areaa))
              {
                
                for ($i=1; $i <= 5 ; $i++) 
                {
                  $sp =0;
                  $qpnt =0; 
                  foreach ($areaa as $row) 
                  {
                    $dst = $row->datestart;
                    $dend = $row->dateend;
                    $evnetid = $row->id;
                    $section = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where event_id='$evnetid' and section='$i'"));
                    if(!empty($section))
                    {
                      foreach ($section as $row) 
                      {
                        $sp +=$row->point;
                        $qpnt +=$row->question_point; 
                      }
                      $tscore =0;
                      if($sp !=0)
                      {
                        $tscore = round($sp/$qpnt*100,2);
                      }
                    }
                  }
                  $name ='';
                  $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
                  if(empty($secname))
                  {
                    
                  }
                  else{
                    $name = $secname[0]->qdesc;
                  }
                  ?>
                  <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
                  <tr>
                      <td><button id="<?php echo $i; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $i; ?>);" >+</button><span class="ml-5"><?php echo "Section- ".$i; ?></span></td>
                      <td width="60%"><?php echo $name;  ?></td>
                      <td width="20%"><?php echo $tscore." %"; ?></td>
                  </tr>
                  </table>
                  <table style="text-align: center;font-size:13" id=<?php echo "dv".$i; ?> class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr class="brac-color-pink">
                            <th style=" ">Section Number</th>
                            <th style=" ">Details</th>
                            <th style=" ">Achievement %</th>
                          </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sp =0;
                      $qpnt=0;
                      $secname ='';
                      $sectiondetails = DB::select( DB::raw(" select * from mnw_progoti.cal_section_point where event_id='$evnetid' and section='$i' order by sub_id ASC"));
                      if(!empty($sectiondetails))
                      {
                        foreach ($sectiondetails as $row) 
                        {
						    $sp =$row->point;
						    $qpnt =$row->question_point;
						    $tscore =0;
						    if($sp !=0)
						    {
							  $tscore = round($sp/$qpnt*100,2);
						    }
						    $sub_id = $row->sub_id;
						    $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='$sub_id' and qno=0"));
                           
							if(empty($secname))
							{
								  
							}
							else{
								$name = $secname[0]->qdesc;
							}
							  ?>
							  <tr>
								<td><?php echo $i.".".$sub_id; ?></td>
								<td><?php echo $name;  ?></td>
								<td><?php echo $tscore." %"; ?></td>
							  </tr>
							<?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                  <?php
                }
                
              }
      }
      else
      {
        $cycle ='';
        $currentDate = date('Y-m-d');
        $area2 = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->limit(2)->get();
        if(!$area2->isEmpty())
        {
          $limit =0;
          $offset = 0;
          foreach($area2 as $row)
          {
            $datestart = $row->datestart;
            $dateend = $row->dateend;
            //echo $datestart."<=".$currentDate."-".$dateend.">=".$currentDate;
            //if($currentDate >='$datestart' and $dateend >='$currentDate')
            if($datestart <=$currentDate and $dateend >=$currentDate)
            {
              $offset ++;
            }
            else
            {
              $limit ++;
              
              //$br = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit $limit"));
              $area1 = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->offset($offset)->limit(1)->get();
              
            }

          }
        }

        //$brnch1 = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit 1"));
        if(!empty($area1))
        {
          $cycle= $area1[0]->datestart." to ".$area1[0]->dateend;
        }
        ?>
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
              <!--begin::Form-->
              <div class="card-header">
                <h3 class="card-title">Monitoring Period: <?php echo $cycle; ?> </h3>
              </div><!-- /.box-header -->
              <div class="card-body table-responsive">
                <table style="text-align: center;font-size:13" class="table">
                  <tr class="brac-color-pink">
                    <th>Section</th>
                    <th width="60%">Section Name</th>
                    <th width="20%">Achievement %</th>
                  </tr>
                </table>
            <?php
            $sp =0;
            $qpnt =0;
            $tscore=0;
            $currentDate = date('Y-m-d');
            $area1 = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->limit(2)->get();
            if(!$area1->isEmpty())
            {
              $limit =0;
              $offset = 0;
              foreach($area1 as $row)
              {
                $datestart = $row->datestart;
                $dateend = $row->dateend;
                //echo $datestart."<=".$currentDate."-".$dateend.">=".$currentDate;
                //if($currentDate >='$datestart' and $dateend >='$currentDate')
                if($datestart <=$currentDate and $dateend >=$currentDate)
                {
                  $offset ++;
                }
                else
                {
                  //$br = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit $limit"));
                  $areaa = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->offset($offset)->limit(1)->get();
                  
                }

              }
            
            }
            
            // $brnch = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnch' order by id DESC limit 1"));
              if(!empty($areaa))
              {
                
                for ($i=1; $i <= 5 ; $i++) 
                {
                  $sp =0;
                  $qpnt =0; 
                  foreach ($areaa as $row) 
                  {
                    $dst = $row->datestart;
                    $dend = $row->dateend;
                    $evnetid = $row->id;
                    $section = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where event_id='$evnetid' and section='$i'"));
                    if(!empty($section))
                    {
                      foreach ($section as $row) 
                      {
                        $sp +=$row->point;
                        $qpnt +=$row->question_point; 
                      }
                      $tscore =0;
                      if($sp !=0)
                      {
                        $tscore = round($sp/$qpnt*100,2);
                      }
                    }
                  }
                  $name ='';
                  $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='0' and qno=0"));
                  if(empty($secname))
                  {
                    
                  }
                  else{
                    $name = $secname[0]->qdesc;
                  }
                  ?>
                  <table style="text-align: center;font-size:13" class="table" cellspacing="0" width="100%">
                  <tr>
                      <td><button id="<?php echo $i; ?>" class="btn btn-light showdiv" onClick="getDiv(<?php echo $i; ?>);" >+</button><span class="ml-5"><?php echo "Section- ".$i; ?></span></td>
                      <td width="60%"><?php echo $name;  ?></td>
                      <td width="20%"><?php echo $tscore." %"; ?></td>
                  </tr>
                  </table>
                  <table style="text-align: center;font-size:13" id=<?php echo "dv".$i; ?> class="table" cellspacing="0" width="100%">
                    <thead>
                        <tr  class="brac-color-pink">
                            <th style=" ">Section Number</th>
                            <th style=" ">Details</th>
                            <th style=" ">Achievement %</th>
                          </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sp =0;
                      $qpnt=0;
                      $secname ='';
                      $sectiondetails = DB::select( DB::raw(" select * from mnw_progoti.cal_section_point where event_id='$evnetid' and section='$i' order by sub_id ASC"));
                      if(!empty($sectiondetails))
                      {
                        foreach ($sectiondetails as $row) 
                        {
                          $sp =$row->point;
                          $qpnt =$row->question_point;
                          $tscore =0;
                          if($sp !=0)
                          {
                            $tscore = round($sp/$qpnt*100,2);
                          }
                          $sub_id = $row->sub_id;
                          $secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and sub_sec_no='$sub_id' and qno=0"));
                          //$secname = DB::select( DB::raw("select * from mnw_progoti.def_questions where sec_no='$i' and qno='$sub_id'"));
                            if(empty($secname))
                            {
                              
                            }
                            else{
                              $name = $secname[0]->qdesc;
                            }
                          ?>
                          <tr>
                            <td><?php echo $i.".".$sub_id; ?></td>
                            <td><?php echo $name;  ?></td>
                            <td><?php echo $tscore." %"; ?></td>
                          </tr>
                        <?php
                        }
                      }
                      ?>
                    </tbody>
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
{{-- </div> --}}
    
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
          // alert(dataval);
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