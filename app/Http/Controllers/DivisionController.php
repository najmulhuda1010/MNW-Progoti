<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class DivisionController extends Controller
{
    public function DDashboard(Request $request)
	{
		$dataload =true;
		$cyear =date('Y');
		$cmonth =date('m');
		$count =0;
		$month =0;
		$eventyear ='';
		$eventquarter ='';
		if(isset($_GET['divid']))
		{
           $a_id =$_GET['divid'];
		}
		else
		{
			$a_id =$request->session()->get('asid');
		}
		$divisiondata = DB::table('branch')->where('division_id',$a_id)->where('program_id',5)->get();
		$data = $request->get('event');

		if($data !='')
		{
			$exp = explode('-',$data);
			$eventyear = $exp[0];
			$eventquarter = $exp[1];
			$a_id = $exp[2];
			$divisiondata = DB::table('branch')->where('division_id',$a_id)->where('program_id',5)->get();
		}
		return view('backend.Division.DivisionDashboard',compact('divisiondata','a_id','eventyear','eventquarter','dataload'));
	}

	public function DivisionAllPreviousView(Request $request)
	{
		$division_id =$request->session()->get('asid');

		$allyear = DB::table('mnw_progoti.monitorevents')->select('year')->where('division_id',$division_id)->groupBy('year')->get();
		return view('backend.Division.DivisionAllPreviousView',compact('allyear'));
	}

	public function DivisionPreviousData(Request $request)
	{
		$year='';
		$quarter ='';
		$yr ='';
		$q='';
		$alldata='';
		$allgroup='';
		$asid =$request->session()->get('asid');

		$year = $request->get('year');

		$quarter = $request->get('quarter');

		if($year !='' and $quarter=='')
		{
			$yr = $year;
			$allgroup = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('division_id',$asid)->where('areacompletestatus',1)->groupBy('year','quarterly')->get();
			//dd($allgroup);
		}
		else if($year !='' and $quarter !='')
		{
			$exp = explode("-", $quarter);
			$yr=$exp[0];
			$q = $exp[1];
			$alldata = DB::table('mnw_progoti.monitorevents')->where('year',$year)->where('division_id',$asid)->where('quarterly',$q)->whereNotNull('score')->get();
			//dd($alldata);
		}
		return view('backend.Division.DivisionAllPreviousData',compact('alldata','asid','yr','q','allgroup'));
	}


	public function RegionWise(Request $request)
	{
		$section = '';
		$year ='';
		$quarter ='';
		$division ='';
		$data = $request->get('region');
		if($data !='')
		{
			$exp =  explode(",", $data);
			$section = $exp[0];
			$year = $exp[1];
			$quarter = $exp[2];
			$division = $exp[3];

		}
		return view('backend.Division.RegionWise',compact('division','quarter','year','section'));
	}
	public function MonthWiseRegion(Request $request)
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
		return view('backend.Division.monthWiseRegion',compact('division','quarter','year','month','section'));
	}

	public function Division_Search(Request $request)
	{
		$a_id =$request->session()->get('asid');
		$divisionsearch = DB::table('branch')->select('region_id')->where('division_id',$a_id)->where('program_id',5)->groupBy('region_id')->orderBy('region_id','ASC')->get();
		return view('backend.Division.Division_Search',compact('divisionsearch','a_id'));
	}
}
