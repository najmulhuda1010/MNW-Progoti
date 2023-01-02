<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Redirect;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class ZonalController extends Controller
{
    public function ZDataInsert(Request $request)
	{
		$datainsert = DB::select(DB::raw("select * from branch where branch_id IN(9294,2073
,1834,2992,5661,402,407,9199,409,3081,3080,4853,491,5809,3019,9197,706,8934,1833,4854,9193,428,3706,1195,2072,3122,3082,3003,2409,309,2999,1408,261,2998,8940,3752,1361,308,9316,9314,2037,3001,3000,368,311,4848,3121,3002,1411,1410,310,225,9191,9188,8946,8942,1324,224,9296,9190,9189,4850,3086,1428,366,9297,8944,4849,5803,3084,328,226,9187,3653,3113,3101,314,3642,1353,1243,253,9041,8262,3062,1354,254,8264,549,4847,4846,8415,1355,1237,255,5667,3023,9200,9195,8936,3024,3016,420,4513,5666,511,9295,5665,3119,1194,9198,5664,3017,166,8937,8404,9293,5658,5657,5731,2996,1267,167,9196,5660,2193,1230,517,4511,8390,2134,441,9369,9291,5659,2946,424,9292,4856,8396,5868,1351,535)"));

		if(!empty($datainsert))
		{
			foreach ($datainsert as $row) 
			{
				$cluster = 2;
				$cluster_name = "Rangpur";
				$brcode =  $row->branch_id;
				$brname = $row->branch_name;
				$areaname = $row->area_name;
				$regionname = $row->region_name;
				$divisionname = $row->division_name;
				$check = DB::select(DB::raw("select * from mnw_progoti.cluster where branch_code='$brcode'"));
				if(empty($check))
				{
					$check = DB::select(DB::raw("Insert INTO mnw_progoti.cluster(cluster_id,cluster_name,branch_name,branch_code,area_name,region_name,division_name) VALUES('$cluster','$cluster_name','$brname','$brcode','$areaname','$regionname','$divisionname')"));
				}
			}
		}
	}
    public function ZonalDashboard(Request $request)
	{
		$dataload = true;
		$zone = $request->get('zone');
		if($zone !='')
		{
			$userpin = $zone;
		}
		else
		{
			$userpin = $request->session()->get('user_pin');
		}
		
		return view('backend.Zonal.ZonalDashboard',compact('dataload','userpin'));
	}
	public function All_PreviousDataView(Request $request)
	{
		$allyear = DB::table('mnw_progoti.monitorevents')->select('year')->groupBy('year')->get();
		$allmonth = DB::table('mnw_progoti.monitorevents')->select('month')->groupBy('month')->orderBy('month','asc')->get();
		$allcluster = DB::table('mnw_progoti.cluster')->select('cluster_id','cluster_name')->groupBy('cluster_id','cluster_name')->get();
		return view('backend.Zonal.AllPreviousView',compact('allyear','allmonth','allcluster'));
	}
	public function AllPrevious(Request $request)
	{
		$evid ='';
		$evcycle ='';
		$period ='';
		$year='';
		$datestart='';
		$alldata='';
		$dateend ='';
		$userpin = $request->session()->get('user_pin');
		// dd($userpin);
		$year = $request->get('year');
		$month1 = $request->get('month1');
		$month2 = $request->get('month2');
		$cluster = $request->get('cluster');
		
		if($year !='' and $month1 =='' and $month2 =='')
		{
			$alldata = DB::table('mnw_progoti.monitorevents')->where('year',$year)->get();

			$getlastid = DB::table('mnw_progoti.monitorevents')->where('year',$year)->orderBy('id','desc')->limit(1)->get();

			if(!$getlastid->isEmpty())
			{
				$evcycle = $getlastid[0]->event_cycle;
				$evid = $getlastid[0]->id;
				
			}
		}
		else if($year !='' and $month1 !='' and $month2 =='')
		{
			//dd($datestart."/".$dateend);
			$alldata = DB::table('mnw_progoti.monitorevents')->where('year',$year)->where('month',$month1)->get();
			if(!$alldata->isEmpty())
			{
				$evcycle = $alldata[0]->event_cycle;
				$evid = $alldata[0]->id;
				//dd($evid);
			}
		}
		else if($year !='' and $month1 !='' and $month2 !='')
		{
			$alldata = DB::table('mnw_progoti.monitorevents')->where('year',$year)->whereBetween('month', [$month1, $month2])->get();
			if(!$alldata->isEmpty())
			{
				$evcycle = $alldata[0]->event_cycle;
				$evid = $alldata[0]->id;
				//dd($evid);
			}
		}
		// dd($month);
		// dd($year);
		
		return view('backend.Zonal.AllPreviousData',compact('alldata','evid','evcycle','year','month1','month2','cluster','userpin'));
	}
}
