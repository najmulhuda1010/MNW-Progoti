<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Redirect;
use Illuminate\Support\Facades\Input;
use Log;
use Session;
use Dompdf\Dompdf;

class NationalController extends Controller
{
    public function dashboard(Request $request){
        $dataload =true;
		$eventyear ='';
		$eventquarter='';
		$data = $request->get('event');

		if($data !='')
		{
			$exp = explode(',',$data);
			$eventyear = $exp[0];
			$eventquarter = $exp[1];
			//$a_id = $exp[2]
		}
		$national = DB::select( DB::raw("select year,quarterly from mnw_progoti.monitorevents group by year, quarterly order by year DESC, quarterly DESC LIMIT 2"));
		//$national = DB::table('mnw_progoti.monitorevents')->select('year,quarterly')->groupBy('year','quarterly')->orderBy('year','DESC')->orderBy('quarterly','DESC')->limit(2)->get();

		return view('backend.National.NationalDashboard',compact('national','dataload','eventyear','eventquarter'));
    }

    public function DivisionWise(Request $request)
	{
		$div= $request->get('division');
		$exp = explode(",", $div);
		$sec = $exp[0];
		$year = $exp[1];
		$quarter = $exp[2];

		return view('backend.National.DivisionWiseAcheivement',compact('sec','year','quarter'));
    }
    
    public function MonthDivisionWise(Request $request)
	{
		$data = $request->get('month');
		if($data !='')
		{
			$exp =  explode(",", $data);
			$section = $exp[0];
			$month = $exp[1];
			$year = $exp[2];
			$quarter = $exp[3];
			$division = $exp[4];
		}
		return view('backend.National.MonthWiseDivision',compact('division','quarter','year','month','section'));
    }
    
    public function areawise(Request $request)
	{
		$regionid = $request->get('regionid');
		$exp = explode(',',$regionid);
		$sec = $exp[0];
		$region_id = $exp[1];
		$year = $exp[2];
		$quarter = $exp[3];
		$arname =array();
		$scoreary =array();

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
			// $cnt = strlen($brcode);
			// if($cnt ==3)
			// {
			// 	$brcode = '0'.$brcode;
			// }
			$mnth = $row->month;
			//echo $mnth;
			$year = $row->year;
			$quar= $row->quarterly;
			$event_id = $row->id;
			$eventidary[] = $row->id;
			//echo $event_id."/";
			$data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$sec'"));
			
			if(!empty($data))
			{
			$sp =$data[0]->sp;
			$qsp =$data[0]->qsp;
			}
			$score =0;
			if($sp !=0)
			{
			$score =round((($sp*100)/$qsp),2);
			$scoreary[] =round((($sp*100)/$qsp),2);
			}
			//echo $event_id."-".$sp."/".$score."*";
			//die();
			$name ='';
			$arean='';
			$areaname = DB::select( DB::raw("select * from branch where area_id='$arcode'"));
			if(!empty($areaname))
			{
			$arname[] = $areaname[0]->area_name;
			}
		
		}
		$json['serial']=$exp[0];
		$json['year']=$exp[2];
		$json['quarter']=$exp[3];
		$json['eventid']=$eventidary;
		$json['areaname']=$arname;
		$json['score']=$scoreary;

		$myJSON = json_encode($json);
		
		echo $myJSON;

		// $brancwisescore = DB::table('mnw_progoti.monitorevents')->where('area_id',$a_id)->where('year',$year)->where('quarterly',$quarter)->get();
		// return view('Region/BranchwiseScore',compact('brancwisescore','sec','a_id'));
    }
    
    public function monthlyareawise(Request $request)
	{
		$regionid = $request->get('regionid');
		$exp = explode(',',$regionid);
		$sec = $exp[0];
		$region_id = $exp[1];
		$year = $exp[2];
		$quarter = $exp[3];
		$month = $exp[4];
		if(strlen($month)==1){
			$month='0'.$exp[4];
		}
		$arname =array();
		$eventidary =array();
		$scoreary =array();

		$regiondata = DB::select(DB::raw("select * from mnw_progoti.monitorevents where year='$year' and quarterly='$quarter' and region_id='$region_id' and month='$month'"));
		// dd($month);
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
			// $cnt = strlen($brcode);
			// if($cnt ==3)
			// {
			// 	$brcode = '0'.$brcode;
			// }
			$mnth = $row->month;
			//echo $mnth;
			$year = $row->year;
			$quar= $row->quarterly;
			$event_id = $row->id;
			$eventidary[] = $row->id;
			// dd($eventidary);
			//echo $event_id."/";
			$data = DB::select(DB::raw("select sum(point) as sp, sum(question_point) as qsp from mnw_progoti.cal_section_point where event_id='$event_id' and section='$sec'"));
			
			if(!empty($data))
			{
			$sp =$data[0]->sp;
			$qsp =$data[0]->qsp;
			}
			$score =0;
			if($sp !=0)
			{
			$score =round((($sp*100)/$qsp),2);
			$scoreary[] =round((($sp*100)/$qsp),2);
			}
			//echo $event_id."-".$sp."/".$score."*";
			//die();
			$name ='';
			$arean='';
			$areaname = DB::select( DB::raw("select * from branch where area_id='$arcode'"));
			if(!empty($areaname))
			{
			$arname[] = $areaname[0]->area_name;
			}
		
		}
		$json['serial']=$exp[0];
		$json['year']=$exp[2];
		$json['quarter']=$exp[3];
		$json['eventid']=$eventidary;
		$json['areaname']=$arname;
		$json['score']=$scoreary;

		$myJSON = json_encode($json);
		
		echo $myJSON;

		// $brancwisescore = DB::table('mnw_progoti.monitorevents')->where('area_id',$a_id)->where('year',$year)->where('quarterly',$quarter)->get();
		// return view('Region/BranchwiseScore',compact('brancwisescore','sec','a_id'));
	}

	public function NationalAllPreviousView(Request $request)
	{
		$national_id =$request->session()->get('asid');

		$allyear = DB::table('mnw_progoti.monitorevents')->select('year')->groupBy('year')->get();
		return view('backend.National.NationalAllPreviousView',compact('allyear'));
	}

	public function NationalPreviousData(Request $request)
	{
		$year='';
		$quarter ='';
		$yr ='';
		$q='';
		$allgroup='';
		$alldata='';
		$asid =$request->session()->get('asid');

		$year = $request->get('year');

		$quarter = $request->get('quarter');

		if($year !='' and $quarter=='')
		{
			$yr = $year;
			$alldata = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('areacompletestatus',1)->groupBy('year','quarterly')->get();
			//dd($alldata);

		}
		else if($year !='' and $quarter !='')
		{
			$exp = explode("-", $quarter);
			$yr=$exp[0];
			$q = $exp[1];
			$alldata = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('quarterly',$q)->groupBy('year','quarterly')->where('areacompletestatus',1)->get();
			//dd($alldata);
		}
		return view('backend.National.NationalAllPreviousData',compact('alldata','asid','yr','q','allgroup'));
	}

	public function GlobalReport(Request $request)
	{
		$dataload =true;
		$ar ='';
		$area ='';
		$div='';
		$reg='';
		$rollid='';
		$userpin='';
		$eventid ='';
		$brnch = '';
		$div = $request->get('division');
		$reg = $request->get('region');
		$area = $request->get('area');
		$rollid =$request->session()->get('roll');
		$asid =$request->session()->get('asid');
		$userpin = $request->session()->get('user_pin');
	    $eventid = $request->get('event');

		if($rollid == '4' and $area==''){
			$exist=0;
			$area_list = DB::table('branch')->select('area_id')->where('division_id',$asid)->where('program_id',5)->groupBy('area_id')->orderBy('area_id','ASC')->get()->toArray();
			foreach($area_list as $row){
				$areaa=$row->area_id;
				if($area == $areaa){
					$exist=1;
				}
			}
			if($exist==0){
				Session::flash('message', "Area not assigned");
				return Redirect::back();
			}
		}
		if($rollid == '3' and $area==''){
			$exist=0;
			$area_list = DB::table('branch')->select('area_id')->where('region_id',$asid)->where('program_id',5)->groupBy('area_id')->orderBy('area_id','ASC')->get()->toArray();
			foreach($area_list as $row){
				$areaa=$row->area_id;
				if($area == $areaa){
					$exist=1;
				}
			}
			if($exist==0){
				Session::flash('message', "Area not assigned");
				return Redirect::back();
			}
		}
		// if($rollid!='3' and $rollid!='4' and $area==''){			
		// 	$exist=0;
		// 	$area_list = DB::table('branch')->select('area_id')->groupBy('area_id')->orderBy('area_id','ASC')->get()->toArray();
		// 	foreach($area_list as $row){
		// 		$areaa=$row->area_id;
		// 		if($area == $areaa){
		// 			$exist=1;
		// 		}
		// 	}
		// 	if($exist==0){
		// 		Session::flash('message', "Area not assigned");
		// 		return Redirect::back();
		// 	}
		// }
		else if ($div !='' and $reg !='' and $area =='') 
		{
			
			?>
			 <script type="text/javascript">
			 	window.location.href="RegionDashboard?regid=<?php echo $reg; ?>";
			 </script>>
			<?php
		}
		else if($div !=''and $reg =='' and $area =='')
		{
			?>
			 <script type="text/javascript">
			 	window.location.href="DivisionDashboard?divid=<?php echo $div; ?>";
			 </script>>
			<?php
		}

		if($reg !='' and $area !='' )
		{
			//$br = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnchcode' order by id DESC limit 2"));
			$ar =  DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->limit(2)->get();
		}
		else if($area !='' and $reg =='')
		{
			// $areaaryy=explode('-',$area);
			// $area=$areaaryy[1];
			// dd($area);
			$getarea= DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->get();
			if(!$getarea->isEmpty())
			{
				$reg = $getarea[0]->region_id;
				$div =$getarea[0]->division_id;
			}else{
				Session::flash('message', "No data available for Area");
				return Redirect::back();
			}
			$currentDate =date('Y-m-d');
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
						
						//$br_monevents = DB::table('mnw_progoti.monitorevents')->where('branchcode',$brcode)->orderBy('id', 'desc')->limit(2)->get();
						$ar = DB::table('mnw_progoti.monitorevents')->where('area_id',$area)->orderBy('id', 'desc')->offset($offset)->limit(2)->get();
						
						//$offset ++;
						
					}
					

				}
				//dd($br);
			}
			//$br = DB::select( DB::raw("select * from mnw_progoti.monitorevents where branchcode='$brnchcode' order by id DESC limit 2"));
		}
		else
		{
			//$division = DB::select( DB::raw("select  division_name,division_id from branch  where program_id='1' group by division_name,division_id order by division_id"));
			$division = DB::table('branch')->select('division_name','division_id')->where('program_id',5)->groupBy('division_name','division_id')->orderBy('division_id','ASC')->get();
		}

		//$division = DB::select( DB::raw("select  division_name,division_id from branch  where program_id='1' group by division_name,division_id order by division_id"));
		$division = DB::table('branch')->select('division_name','division_id')->where('program_id',5)->groupBy('division_name','division_id')->orderBy('division_id','ASC')->get();
		if($rollid=='17')
		{

           $division = DB::select( DB::raw("select division_name,division_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where c_associate_id='$userpin') and program_id=5 group by division_id,division_name order by division_id ASC"));
		}
		else if($rollid=='18')
		{
			$division = DB::select( DB::raw("select division_name,division_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where z_associate_id='$userpin') and program_id=5 group by division_id,division_name order by division_id ASC"));

		}
		// dd($division);
		//var_dump($division);
		return view('backend.National.GlobalReport', compact('dataload','div','area','reg','division','ar','userpin','rollid','eventid'));
	}

	public function ClusterDash(Request $request)
	{
		$dataload = true;
		$zoneall =DB::select(DB::raw("select * from mnw_progoti.zonal"));
		if(empty($zoneall))
		{
			$zoneall  ='';
		}
		return view('backend.National.ClusterDashboard',compact('zoneall','dataload'));
	}

	public function GetCluster(Request $request)
	{
		$zoneid=$request->get('id');
		$cluster =DB::select(DB::raw("select c_associate_id,cluster_id,cluster_name from mnw_progoti.cluster where z_associate_id='$zoneid' group by c_associate_id,cluster_id,cluster_name order by cluster_id ASC"));
		echo json_encode($cluster);
	}

	public function ClusterView(Request $request)
	{
		//$clusterdata = DB::select(DB::raw("select * from mnw_progoti.cluster order by cluster_id ASC"));
	    $clusterdata =DB::table('mnw_progoti.cluster')->orderBy('cluster_id','ASC')->paginate(5);
		// return view('National/ClusterView',compact('clusterdata','dataload'));
		return view('backend.National.ClusterView',compact('clusterdata'));
		// return datatables(DB::table('mnw_progoti.cluster')->orderBy('cluster_id','ASC'))->toJson();
	}
	public function ClusterViewLoad(Request $request)
	{
		//return datatables(DB::table('mnw_progoti.cluster')->orderBy('cluster_id','ASC'))->toJson();
		return datatables(DB::select(DB::raw("select cluster_id,cluster_name,branch_name,branch_code,area_name,region_name,division_name,zonal_code,(select zonal_name from mnw_progoti.zonal where mnw_progoti.zonal.zonal_code=  mnw_progoti.cluster.zonal_code) from mnw_progoti.cluster")))->toJson();
	}

	public function Cluster_Add(Request $request)
	{
		$dataload = true;
		return view('backend.National.addCluster',compact('dataload'));
	}
	public function Cluster_Store(Request $request)
	{
		$zonalcode = $request->get('zonalcode');
		$clustername = $request->get('clustername');
		$clusterid = $request->get('clusterid');
		$area = $request->get('area');
		$checkZonal = DB::table('mnw_progoti.zonal')->where('zonal_code',$zonalcode)->get();
		if($checkZonal->isEmpty())
		{
			return redirect()->back()->with('error', 'Zonal Code does not exist!!');
		}	
		// dd($area);
		foreach($area as $row)
		{
			$ar_id = $row;
			//echo $br_id."/";
			$areasql = DB::select(DB::raw("select area_name,region_name,division_name from branch where area_id='$ar_id' and program_id='5' group by area_name,region_name,division_name"));
			// dd($areasql);
			foreach($areasql as $r)
			{
				$area_name = $r->area_name;
				$region_name =  $r->region_name;
				$division_name = $r->division_name;
				$checkCluster = DB::table('mnw_progoti.cluster')->where('zonal_code',$zonalcode)->where('cluster_id',$clusterid)->where('area_id',$ar_id)->get();
				if($checkCluster->isEmpty())
				{
					$sqldata = DB::table('mnw_progoti.cluster')->insert(['cluster_id'=>$clusterid,'cluster_name'=>$clustername,'area_id'=>$ar_id,'area_name'=>$area_name,'region_name'=>$region_name,'division_name'=>$division_name,'zonal_code'=>$zonalcode]);
				}		
			}
			
		}
		if($sqldata)
		{
			return redirect()->back()->with('success', 'Cluster Data Add Success!!');
		}
		else
		{
		   return redirect()->back()->with('error', 'Cluster Data Add Failed!!');
		}
	}

	public function Cluster_Asc_Id_Add()
	{
		$clusters=DB::table('mnw_progoti.cluster')->select('cluster_id','cluster_name')->groupBy('cluster_id','cluster_name')->orderBy('cluster_id')->get();
		$zonal = DB::table('mnw_progoti.zonal')->select('zonal_code','zonal_name')->groupBy('zonal_code','zonal_name')->orderBy('zonal_code')->get();
		//dd($zonal);
		return view('backend.National.addClusterAscId',compact('clusters','zonal'));
	}
	//siam
	public function Cluster_Asc_Id_Store(Request $request)
	{
		$data=$request->all();
		$cluster=$data['cluster'];
		$associate_id=$data['cassociate_id'];
		$cluster_ary=explode('-',$cluster);
		$cluster_id=$cluster_ary[0];
		$zonal = $data['zonal'];
		$zassociate_id=$data['zassociate_id'];
		$zonal_ary=explode('-',$zonal);
		$zonal_id=$zonal_ary[0];
		// dd($cluster_id);
		//var_dump($branch);
		
		DB::table('mnw_progoti.cluster')->where('cluster_id', $cluster_id)->update(['c_associate_id' => $associate_id,'z_associate_id' => $zassociate_id]);
		DB::table('mnw_progoti.zonal')->where('zonal_code', $zonal_id)->update(['z_associate_id' => $zassociate_id]);

		return redirect()->back()->with('success', 'Associate Id Added Successfully to cluster!!');
		
	}

	public function Cluster_Edit($clustercode)
	{
		$dataload = true;
		$cluster=DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->get();
		$Cluster_area_id=DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->pluck('area_id');
		// dd($Cluster_area_id);
		return view('backend.National.editCluster',compact('dataload','cluster','Cluster_area_id'));
	}
	public function Cluster_Update(Request $request)
	{
		$id = $request->get('id');
		$clustercode = $request->get('clustercode');
		$zonalcode = $request->get('zonalcode');
		$clustername = $request->get('clustername');
		$clusterid = $request->get('clusterid');
		$area = $request->get('area');
		$Cluster_areas=DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->pluck('area_id')->toArray();
		$CheckData=DB::table('mnw_progoti.cluster')->where('cluster_id',$clusterid)->first();
		// dd($CheckData);
		if($CheckData){
			if($clustercode!=$clusterid){
				return redirect()->back()->with('error', 'Cluster Already Exist!!');
			}
		}
		$countarea=count(array_diff($Cluster_areas,$area));
		if($countarea>0){
			$extras=array_diff($Cluster_areas,$area);
			foreach ($extras as $row) {
				DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->where('area_id',$row)->delete();
			}
		}
		foreach($area as $row)
		{
			$ar_id = $row;
			//echo $br_id."/";
			$areasql = DB::select(DB::raw("select area_name,region_name,division_name from branch where area_id='$ar_id' and program_id='5' group by area_name,region_name,division_name"));
			// dd($areasql);
			foreach($areasql as $r)
			{
				$area_name = $r->area_name;
				$region_name =  $r->region_name;
				$division_name = $r->division_name;
				$checkCluster = DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->where('area_id',$ar_id)->get();
				if($checkCluster->isEmpty())
				{
					DB::table('mnw_progoti.cluster')->insert(['cluster_id'=>$clusterid,'cluster_name'=>$clustername,'area_id'=>$ar_id,'area_name'=>$area_name,'region_name'=>$region_name,'division_name'=>$division_name,'zonal_code'=>$zonalcode]);
				}else{
					DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->update(['cluster_id'=>$clusterid,'cluster_name'=>$clustername,'zonal_code'=>$zonalcode]);
				}		
			}
			
		}
		return redirect('/Cluster')->with('success', 'Cluster Data Update Successfully!!');

	}

	public function Cluster_Delete($clustercode)
	{
		DB::table('mnw_progoti.cluster')->where('cluster_id',$clustercode)->delete();
		
        return redirect('/Cluster')->with('success','Success!! Cluster Deleted!!');

    }


	public function ZonalDash(Request $request)
	{
		$dataload = true;
		$zoneall =DB::select(DB::raw("select * from mnw_progoti.zonal"));
		if(empty($zoneall))
		{
			$zoneall  ='';
		}
		return view('backend.National.ZonalDash',compact('zoneall','dataload'));
	}

	public function ZonalView(Request $request)
	{
		//$clusterdata = DB::select(DB::raw("select * from mnw_progoti.cluster order by cluster_id ASC"));
	    $zonaldata =DB::table('mnw_progoti.zonal')->paginate(10);
		return view('backend.National.ZonalView',compact('zonaldata'));
	}

	public function Zonal_Add(Request $request)
	{
		$dataload = true;
		return view('backend.National.Zonal',compact('dataload'));
	}
	public function Zonal_Store(Request $request)
	{
		$name = $request->get('zonalname');
		$id = $request->get('zonalid');
		$checkZonal = DB::table('mnw_progoti.zonal')->where('zonal_code',$id)->get();
		if($checkZonal->isEmpty())
		{
			$sqldata = DB::table('mnw_progoti.zonal')->insert(['zonal_name'=>$name,'zonal_code'=>$id]);
			if($sqldata)
			{
				return redirect()->back()->with('success', 'Zonal Data Add Success!!');
			}
			else
			{
			   return redirect()->back()->with('error', 'Zonal Data Add Failed!!');
			}
			
		}
		else
		{
		   return redirect()->back()->with('error', 'Zonal Data Already Exists!!');
		}
		
	}
	public function Zonal_Edit($id)
	{
		$dataload = true;
		$zonal=DB::table('mnw_progoti.zonal')->where('id',$id)->first();
		// dd($zonal);
		return view('backend.National.ZonalEdit',compact('dataload','zonal'));
	}
	public function Zonal_Update(Request $request)
	{
		// dd('asd');
		$id = $request->get('id');
		$zonalid = $request->get('zonalid');
		$name = $request->get('zonalname');
		$code = $request->get('zonalcode');
		$checkZonal = DB::table('mnw_progoti.zonal')->where('zonal_code',$code)->get();
		if($checkZonal->isEmpty())
		{
			DB::table('mnw_progoti.zonal')->where('id',$id)->update(['zonal_name' =>$name,'zonal_code'=>$code]);
			$checkClusters = DB::table('mnw_progoti.cluster')->where('zonal_code',$zonalid)->get();
			if($checkClusters){
				foreach($checkClusters as $cluster){
					$cluster_id=$cluster->id;
					$sqldata = DB::table('mnw_progoti.cluster')->where('id',$cluster_id)->update(['zonal_code'=>$code]);
				}
			}
			// return redirect()->back()->with('success', 'Zonal Data Update Success!!');	
			return redirect('/Zonal')->with('success','Zonal Data Update Success!!');
		}
		else
		{
			if($zonalid==$code){
				DB::table('mnw_progoti.zonal')->where('id',$id)->update(['zonal_name' =>$name,'zonal_code'=>$code]);
				
				// return redirect()->back()->with('success', 'Zonal Data Update Success!!');
				return redirect('/Zonal')->with('success','Zonal Data Update Success!!');
			}
		   return redirect()->back()->with('error', 'Zonal Data Already Exists!!');
		}
		
	}

	public function Zonal_Delete($zonalcode)
	{
		DB::table('mnw_progoti.zonal')->where('zonal_code',$zonalcode)->delete();
		DB::table('mnw_progoti.cluster')->where('zonal_code',$zonalcode)->delete();
		
        return redirect('/Zonal')->with('success','Success!! Zonal Deleted!!');

    }

	public function zonalDataLoad() {
        $zonals=DB::table('mnw_progoti.zonal');
        // dd($events);
		// return datatables($zonals)->toJson();
		return datatables($zonals)->addColumn('action', function ($zonals) {
            return '<a href="'.url('editZonal/'.$zonals->id).'" class="btn btn-light">Edit</a> <a href="'.url('deleteZonal/'.$zonals->zonal_code).'" class="btn btn-danger" onclick="return confirm('."'".'Are you sure you want to delete?'."'".')">Delete</a>';
        })->toJson();
    }

	public function excelCluster() {
        return view('backend.National.clusterexport');
    }

	public function printCluster() {
        return view('backend.National.clusterprint');
    }

	public function pdfCluster() {
        // reference the Dompdf namespace

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->loadHtml(view('backend.National.clusterpdf'));

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('clusterList.pdf');
		// return $dompdf->download('clusterList.pdf');
    }
}
