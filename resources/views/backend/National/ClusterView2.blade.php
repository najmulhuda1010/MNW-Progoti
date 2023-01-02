@extends('mainpage')

@section('title','Cluster View')


@section('content')

  <div class="row">
    <div class="panel panel-default">
      <div class="header">
        <h3><i>Cluster Report:</i></h3>
        <br />
		<a target="_blank" href="ClusterAdd"><button>Add Cluster</button></a>
		<div class="input_search">
		   Search: <input type="text" id="myInput" name="search" autofocus placeholder="Only Search Branch & Cluster Name">
	    </div>
      </div>
     <table style="text-align: center;font-size:13" class="table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Cluster Id</th>
            <th>Cluster Name</th>
            <th>Branch Code</th>
            <th>Branch Name</th>
            <th>Area Name</th>
            <th>Region Name</th>
            <th>Division Name</th>
            <th>Zonal Code</th>
            <th>Zonal Name</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $name='';
            $id=1;
            foreach($clusterdata as $row)
            {
				 
                 $cluster_id = $row->cluster_id;
                 $cluster_name = $row->cluster_name;
                 $branch_code = $row->branch_code;
                 $branch_name = $row->branch_name;
                 $area_name = $row->area_name;
                 $region_name = $row->region_name;
				 $division_name =  $row->division_name;
				 $zonal_code = $row->zonal_code;
				 
				 $zonalname = DB::select(DB::raw("select * from mnwv2.zonal where zonal_code='$zonal_code'"));
				 if(!empty($zonalname))
				 {
					 $name = $zonalname[0]->zonal_name;
				 }   
              ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $cluster_id; ?></td>
                    <td><?php echo $cluster_name; ?></td>
                    <td><?php echo $branch_code; ?></td>
                    <td><?php echo $branch_name; ?></td>
                    <td><?php echo $area_name; ?></td>
                    <td><?php echo $region_name; ?></td>
                    <td><?php echo $division_name; ?></td>
                    <td><?php echo $zonal_code; ?></td>
                    <td><?php echo $name; ?></td>
                </tr>
              <?php
              $id++;
            }

           ?>
          
        </tbody>
      </table>
    </div>
 </div>
    <!--pagination-->

  <nav aria-label="Page navigation example" style="float: right" >
 {{$clusterdata->links()}}
  </nav>          
  <!--End pagination--> 
 
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>

$(document).ready(function(){
	 $('#myInput').keyup(function() {
		 var datas = this.value;
		 if(datas !='')
		 {
			 $.ajax({
				  type: 'POST',
				  url: '/mnwprogoti/mnwv2/ClusterViewSearch',cache: false,
				  dataType: 'json',
				  data: { id: datas },
				  success: function (data) {
					   console.log(data);
					   var len = data.length;
					   $("tbody").empty();
					   var id=1;
					   for(var i = 0; i < len; i++)
						{
						  //var option = "<option value='"+data[i].area_id+"'>"+data[i].area_id+"-"+data[i].area_name+"</option>";
						   var trHTML =
									// '<tr><td>'+ id +'</td><td>'+ data[i].cluster_id +'</td><td>' + data[i].cluster_name + '</td><td>' +  data[i].branch_code + '</td><td>' + data[i].branch_name + '</td><td>'+ data[i].area_name + '</td><td>'+data[i].region_name+'</td><td>'+data[i].division_name+'</td><td>'+data[i].zonal_code+'</td><td>'+data[i].zonal_name+'</td><td><a class="btn btn-primary" href="#">Edit</a>|<a class="btn btn-danger" href="">Delete</a></td></tr>';
									 '<tr><td>'+ id +'</td><td>'+ data[i].cluster_id +'</td><td>' + data[i].cluster_name + '</td><td>' +  data[i].branch_code + '</td><td>' + data[i].branch_name + '</td><td>'+ data[i].area_name + '</td><td>'+data[i].region_name+'</td><td>'+data[i].division_name+'</td><td>'+data[i].zonal_code+'</td><td>'+data[i].zonal_name+'</td></tr>';
						  $("tbody").append(trHTML);
						  id++;
						}
				  },
				  error: function (ex) {
					alert('Failed to retrieve NewData.');
				  }
				 
			 });
		 }
		 else
		 {
			 //alert("huda");
			 var datas ='';
			 $.ajax({
				  type: 'POST',
				  url: '/mnwprogoti/mnwv2/ClusterViewSearch',cache: false,
				  dataType: 'json',
				  data: { id: datas },
				  success: function (data) {
					  window.location.href = "Cluster";
				  },
				  error: function (ex) {
					alert('Failed to retrieve Data.');
				  }
				 
			 });
		 } 
    });
	 
 });
</script> 
