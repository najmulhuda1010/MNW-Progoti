<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class AreaController extends Controller
{
	private $db;

	public function __construct()
	{
		$this->db = config('database.name');
	}

	public function Area_Dashboard(Request $request)
	{
		$dataload = true;
		$cyear = date('Y');
		$cmonth = date('m');
		$count = 0;
		$month = 0;
		$eventyear = '';
		$eventid = "";
		$eventquarter = '';
		$ev = '';
		$calculation = 0;
		$lastevid = 0;
		$a_id = $request->session()->get('asid');


		$currentDate = date('Y-m-d');
		$area2 = DB::table($this->db . '.monitorevents')->where('area_id', $a_id)->orderBy('id', 'desc')->limit(2)->get();

		if (!$area2->isEmpty()) {
			$limit = 0;
			$offset = 0;
			foreach ($area2 as $row) {
				$datestart = $row->datestart;
				$dateend = $row->dateend;
				//echo $datestart."<=".$currentDate."-".$dateend.">=".$currentDate;
				//if($currentDate >='$datestart' and $dateend >='$currentDate')
				if ($datestart <= $currentDate and $dateend >= $currentDate) {
					$offset++;
				} else {

					$limit++;

					//$br_monevents = DB::table($this->db.'.monitorevents')->where('branchcode',$brcode)->orderBy('id', 'desc')->limit(2)->get();
					$area_monevents = DB::table($this->db . '.monitorevents')->where('area_id', $a_id)->where('areacompletestatus', 1)->orderBy('dateend', 'desc')->offset($offset)->limit(2)->get();

					if ($eventid == "") {
						//$br_maxevent = DB::select( DB::raw("select max(id) as evntid,branchcode from mnw_progoti.monitorevents where branchcode='$brcode' group by branchcode"));
						$br_maxevent = DB::table($this->db . '.monitorevents')->where('area_id', $a_id)->where('areacompletestatus', 1)->orderBy('id', 'desc')->offset($offset)->limit(1)->get();
						// dd($br_maxevent);


						if ($br_maxevent->isEmpty()) {
							$lastevid = 0;
						} else {
							$lastevid = $br_maxevent[0]->id;
							$calculation = 1;
							$dataload = "true";
						}
					} else {
						$ev = $eventid;
					}
					$offset++;
				}
			}
		}



		$br_info = DB::table('branch')->where('area_id', $a_id)->where('program_id', 5)->get();
		if ($br_info->isEmpty()) {
			$area_name = '';
			$region_name = '';
			$division_name = '';
			$area_id = '';
			$region_id = '';
			$division_id = '';
		} else {
			$area_name = $br_info[0]->area_name;
			$region_name = $br_info[0]->region_name;
			$division_name = $br_info[0]->division_name;
			$area_id = $br_info[0]->area_id;
			$region_id = $br_info[0]->region_id;
			$division_id = $br_info[0]->division_id;
		}

		$eve = $request->get('evid');
		if ($eve != '') {
			$data = explode(",", $eve);
			$eventid = $data[0];
			$dataload = $data[1];
			$brcode = $data[2];
		}
		//die();
		// dd($lastevid);

		return view('backend/Area/AreaDashboard', compact('area_name', 'region_name', 'division_name', 'area_id', 'lastevid', 'calculation', 'dataload', 'a_id', 'eventyear', 'area_monevents', 'eventquarter', 'ev'));
		// return view('backend/Area/AreaDashboard');
	}

	public function All_PreviousDataView(Request $request)
	{
		$area_id = $request->session()->get('asid');
		// $cnt = strlen($area_id);
		// if($cnt=='3')
		// {
		// 	$branchcode ="0".$branchcode;
		// }

		$allyear = DB::table($this->db . '.monitorevents')->select('year')->where('area_id', $area_id)->groupBy('year')->get();
		$allperiod = DB::table($this->db . '.monitorevents')->select('datestart', 'dateend')->where('area_id', $area_id)->groupBy('datestart', 'dateend')->orderBy('datestart', 'desc')->get();
		return view('backend.Area.AllPreviousView', compact('allyear', 'allperiod'));
	}
	public function AllPrevious(Request $request)
	{
		$evid = '';
		$evcycle = '';
		$period = '';
		$year = '';
		$datestart = '';
		$dateend = '';
		$area_id = $request->session()->get('asid');
		// if(strlen($branchcode)=='3')
		// {
		// 	$branchcode ='0'.$branchcode;
		// }
		//dd($branchcode);
		$year = $request->get('year');
		$period = $request->get('period');
		// dd($year);
		if ($year != '' and $period == '') {
			$alldata = DB::table($this->db . '.monitorevents')->where('area_id', $area_id)->where('year', $year)->get();

			$getlastid = DB::table($this->db . '.monitorevents')->where('area_id', $area_id)->where('year', $year)->orderBy('id', 'desc')->limit(1)->get();

			if (!$getlastid->isEmpty()) {
				$evcycle = $getlastid[0]->event_cycle;
				$evid = $getlastid[0]->id;
			}
		} else if ($year != '' and $period != '') {
			$exp = explode("to", $period);
			$datestart = $exp[0];
			$dateend = $exp[1];
			//dd($datestart."/".$dateend);
			$alldata = DB::table($this->db . '.monitorevents')->where('area_id', $area_id)->where('year', $year)->where('datestart', '>=', $datestart)->where('dateend', '<=', $dateend)->get();
			if (!$alldata->isEmpty()) {
				$evcycle = $alldata[0]->event_cycle;
				$evid = $alldata[0]->id;
				//dd($evid);
			}
		}

		return view('backend.Area.AllPreviousData', compact('alldata', 'evid', 'evcycle', 'area_id'));
	}
	public function Period(Request $request)
	{
		$year = '';
		$year = $request->get('id');
		$area_id = $request->session()->get('asid');
		// if(strlen($branchcode)=='3')
		// {
		// 	$branchcode ='0'.$branchcode;
		// }
		$data = DB::table($this->db . '.monitorevents')->where('area_id', $area_id)->where('year', $year)->get();
		echo json_encode($data);
	}
}
