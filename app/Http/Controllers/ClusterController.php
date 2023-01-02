<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Redirect;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class ClusterController extends Controller
{

    public function DataInsert(Request $request)
	{
		//$datainsert = DB::select(DB::raw("select * from branch where branch_id IN(8269,8268,8266,2366,1366,8270,3366,3066,266,9109,1366,316,267,9145,4509,2042,8276,8275,1415,315,4852,5668,3011,3009,2976,2951,2420,1419,319,3420,2973,372,320,264,9045,3363,1364,263,9185,9043,8278,3070,2950,9184,9182,313,2413,2965,5670,1284,184,1830,2039,8263,2971,2964,1413,1336,4510,2363,2040,4508,3156,3007,1156,462,2041,5776,3072,2284,5669,1832,2038,4156,3074,2156,1831,5671,3010,1283,183,4851,466,4538,2981,1157,9281,3012,3008,2195,509,2070,5663,3018,469,2071,8261,8265,2980,2336,236,1352,1235,252,4506,3740,537,9038,8951,4845,5878,5662,3079,8938,4512,2243,1236,528,9042,3558,2338,1338,238,8259,2987,2236,556,3557,1412,312,3556,2985,527,9039,2966,2963,1421,321,9219,3737,2986,2984,9192)"));// syedpur
		//$datainsert = DB::select(DB::raw("select * from branch where branch_id IN(9294,2073,1834,2992,5661,402,407,9199,409,3081,3080,4853,491,5809,3019,9197,706,8934,1833,4854,9193,428,3706,1195,2072,3122,3082,3003,2409,309,2999,1408,261,2998,8940,3752,1361,308,9316,9314,2037,3001,3000,368,311,4848,3121,3002,1411,1410,310,225,9191,9188,8946,8942,1324,224,9296,9190,9189,4850,3086,1428,366,9297,8944,4849,5803,3084,328,226,9187,3653,3113,3101,314,3642,1353,1243,253,9041,8262,3062,1354,254,8264,549,4847,4846,8415,1355,1237,255,5667,3023,9200,9195,8936,3024,3016,420,4513,5666,511,9295,5665,3119,1194,9188,5664,3017,166,8937,8404,9293,5657,5731,2996,1267,167,9196,5660,2193,1230,517,4511,8390,2134,441,9369,9291,5659,2946,424,9292,4856,8396,5868,1351,535)"));//rangpur
		//$datainsert = DB::select(DB::raw("select * from branch where branch_id IN(8399,5656,5655,1134,2067,3230,530,9320,2230,8437,8431,3783,3090,1458,8436,8434,5815,5647,8435,8430,5646,5645,8447,9173,8417,524,8429,9657,5942,9172,2065,8446,8445,8425,557,8426,8421,542,532,3089,251,8449,8420,8419,8422,543,1301,8424,8442,8441,8423,241,3091,1457,9175,8440,8439,201,8443,9740,3505,3506,8433,3504,2457,1835,5802,5801,5648,2074,3511,2456,9368,258,9179,8410,1358,3640,1417,317,9035,8407,1359,259,1356,260,9366,9178,8414,8411,8406,1360,8412,256,9255,9177,8444,2351,9078,1456,956,1341,1231,520,9076,5651,9301,9089,4855,5654,9082,955,9080,9079,9077,3151,5732,5650,1954,9073,1455,9087,9086,541,5649,2219,9074,2231,9085,4535,538,9084,9083,1848,5653,5652,3745,4818,8372,5871,3678,472,1161,9122,9053,1843,9121,1844)")); //bogura
		$datainsert = DB::select(DB::raw("select * from branch where branch_id IN(3088,8450,5862,227,3651,544,9298,9111,9176,1349,249,5836,9025,1842,1255,9027,9026,3641,248,9110,5807,9030,4815,1339,4816,239,9032,1350,3655,250,4817,3255,2255,9033,9031,1433,330,2068,2069,3648,9028,4822,564,9113,333,9029,4820,4821,3647,9146,9002,9000,8976,1377,277,536,9263,8978,8977,8975,1460,360,8997,8981,625,334,8984,516,9299,9148,8982,8980,5810,435,9101,9009,5641,9104,9022,9020,4824,5642,9968,9024,9021,5738,5643,567,9103,9014,5644,434,9317,9023,1845,5874,3692,631,9264,5699,546,8988,8987,8986,8985,553,545,9226,9008,9003,5893,489,9228,8991,1376,1174,478,8993,8992,9227,5808,2234,228,3680,533,336,2046,3144,335,9065,9064,2048,5870,5834,3146,278,4536,1850,2047,5849,461,457,9070,2049)")); //rajshahi
		
		if(!empty($datainsert))
		{
			foreach ($datainsert as $row) 
			{
				$cluster = 4;
				$cluster_name ="Rajshahi";
				$brcode =  $row->branch_id;
				$brname = $row->branch_name;
				$areaname = $row->area_name;
				$regionname = $row->region_name;
				$divisionname = $row->division_name;
				$zoneid =1; //$row->zone_id;
				$check = DB::select(DB::raw("select * from mnw_progoti.cluster where branch_code='$brcode'"));
				if(empty($check))
				{
					$check = DB::select(DB::raw("Insert INTO mnw_progoti.cluster(cluster_id,cluster_name,branch_name,branch_code,area_name,region_name,division_name,zonal_code) VALUES('$cluster','$cluster_name','$brname','$brcode','$areaname','$regionname','$divisionname','$zoneid')"));
				}
			}
		}
	}
    public function ClusterView(Request $request)
	{
		$clusterid ='';
		$dataload = true;
		$clusterid = $request->get('asid');
		if($clusterid !='')
		{
			$userpin = $clusterid;
		}
		else
		{
			$userpin = $request->session()->get('user_pin');
		}
		return view('backend.Cluster.ClusterDashboard',compact('dataload','userpin'));
	}
	public function Cluster_Search(Request $request)
	{
		$a_id ='6';//$request->session()->get('asid');
		$cluster = DB::table('branch')->select('division_name','division_id')->where('program_id',5)->groupBy('division_name','division_id')->orderBy('division_id','ASC')->get();
		return view('backedn.Cluster.ClusterSearch',compact('cluster','a_id'));
	}
	public function Division_Search(Request $request)
	{
		$id= $request->get("id");
		$division = DB::select(DB::raw("select region_id,region_name from branch where division_id='$id' and program_id='5' group by region_id,region_name order by region_id ASC"));
		echo json_encode($division);
	}
	public function All_PreviousDataView(Request $request)
	{
		$allyear = DB::table('mnw_progoti.monitorevents')->select('year')->groupBy('year')->get();
		$allmonth = DB::table('mnw_progoti.monitorevents')->select('month')->groupBy('month')->orderBy('month','asc')->get();
		return view('backend.Cluster.AllPreviousView',compact('allyear','allmonth'));
	}
	public function AllPrevious(Request $request)
	{
		$evid ='';
		$evcycle ='';
		$period ='';
		$year='';
		$datestart='';
		$dateend ='';
		//dd($branchcode);
		$year = $request->get('year');
		$month1 = $request->get('month1');
		$month2 = $request->get('month2');
		
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
			//dd($datestart."/".$dateend);
			$alldata = DB::table('mnw_progoti.monitorevents')->where('year',$year)->whereBetween('month', [$month1, $month2])->get();
			if(!$alldata->isEmpty())
			{
				$evcycle = $alldata[0]->event_cycle;
				$evid = $alldata[0]->id;
				//dd($evid);
			}
		}
		$userpin = $request->session()->get('user_pin');
		// dd($month);
		// dd($year);
		
		return view('backend.Cluster.AllPreviousData',compact('alldata','evid','evcycle','year','month1','month2','userpin'));
	}
}
