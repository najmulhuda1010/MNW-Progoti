@extends('mainpage')

@section('title','Table')


@section('content')

  <div class="row">
    <div class="panel panel-default">
      <div class="header">
        <h3><i>Branch Search:</i></h3>
        <br />
      </div>
      <div id="rcorners">
      <div class="row">
        <form action="BranchDashboard" method="GET">
          <label>Division Name</label>
          <select name="divisionname" id="divisionid">
            <option>select</option>
            <?php
                foreach ($cluster as $r)
                {
                  /*$aid = $r->region_id;
                  $regionname = DB::table('branch')->where('region_id',$aid)->where('program_id',1)->get();
                  if(!empty($regionname))
                  {
                    $rname = $regionname[0]->region_name;
                  }
                  else
                  {
                     $rname ='';
                  }*/
                  ?>
                    <option value="<?php echo $r->division_id; ?>"><?php echo $r->division_id."-".$r->division_name; ?></option>
                  <?php
                }
              ?>
          </select>
          <label>Region Name</label>
          <select name="regionid" id="regionid">

          </select>
          <label>Area Name:</label>
          <select name="areaid" id="areaid">
    
          </select>
          &nbsp;&nbsp;&nbsp;
           <label>Branch Name:</label>
           <select class="options" name="brnch" id="branch_id">
                
            </select>
            &nbsp;&nbsp;&nbsp;
            <input type="submit" name="" value="Submit">
        </form>
        <script>
          $('#divisionid').on('change', function() {
                //alert( this.value );
               // $("#divs").empty();
                var division_id= this.value;
                //alert(area_id);
                if(division_id !='')
                {  
                  $.ajax({
                  type: 'POST',
                  url: '/mnwprogoti/MNWV2 _1/public/Division',cache: false,
                  dataType: 'json',
                  data: { id: division_id },
                  success: function (data) {
                    
                    //var d = data[0].region_id;
                    //console.log(d);
                    var len = data.length;
                    $("#regionid").empty();
                    
                    var option2 = "<option value=''>select</option>";
                    $("#regionid").append(option2);
                    for(var i = 0; i < len; i++)
                    {
                      var option = "<option value='"+data[i].region_id+"'>"+data[i].region_id+"-"+data[i].region_name+"</option>"; 

                      $("#regionid").append(option); 
                    }
                    
                  },
                  error: function (ex) {
                    alert('Failed to retrieve Area.');
                  }
                });
                  
                  return;
                }
              });
                $('#regionid').on('change', function() {
                //alert( this.value );
               // $("#divs").empty();
                var region_id= this.value;
                //alert(area_id);
                if(region_id !='')
                {  
                  $.ajax({
                  type: 'POST',
                  url: '/mnwprogoti/MNWV2 _1/public/Region',cache: false,
                  dataType: 'json',
                  data: { id: region_id },
                  success: function (data) {
                    
                    //var d = data[0].region_id;
                    //console.log(d);
                    var len = data.length;
                    $("#areaid").empty();
                    
                    var option2 = "<option value=''>select</option>";
                    $("#areaid").append(option2);
                    for(var i = 0; i < len; i++)
                    {
                      var option = "<option value='"+data[i].area_id+"'>"+data[i].area_id+"-"+data[i].area_name+"</option>"; 

                      $("#areaid").append(option); 
                    }
                    
                  },
                  error: function (ex) {
                    alert('Failed to retrieve Area.');
                  }
                });
                  
                  return;
                }
              });  

               $('#areaid').on('change', function() {
                //alert( this.value );
               // $("#divs").empty();
                var area_id= this.value;
                //alert(area_id);
                if(area_id !='')
                {  
                  $.ajax({
                  type: 'POST',
                  url: '/mnwprogoti/MNWV2 _1/public/BranchData',cache: false,
                  dataType: 'json',
                  data: { id: area_id },
                  success: function (data) {
                    
                    //var d = data[0].region_id;
                    //console.log(d);
                    var len = data.length;
                    $("#branch_id").empty();
                    
                    var option2 = "<option value=''>select</option>";
                    $("#branch_id").append(option2);
                    for(var i = 0; i < len; i++)
                    {
                      var option = "<option value='"+data[i].branch_id+"'>"+data[i].branch_id+"-"+data[i].branch_name+"</option>"; 

                      $("#branch_id").append(option); 
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
      </div>   
    </div>
    </div>
 </div>
@endsection
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
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