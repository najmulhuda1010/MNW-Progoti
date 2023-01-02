<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class RegionController extends Controller
{
    public function Region_Dashboard(Request $request)
	{
		$dataload =true;
		$cyear =date('Y');
		$cmonth =date('m');
		$count =0;
		$month =0;
		$eventyear ='';
		$eventquarter ='';
		if(isset($_GET['regid']))
		{
           $region_id = $_GET['regid'];
		}
		else
		{
			$region_id =$request->session()->get('asid');
		}
		$regiondata = DB::table('branch')->where('region_id',$region_id)->where('program_id',5)->get();
		$data = $request->get('event');
		if($data !='')
		{
			$exp = explode('-',$data);
			$eventyear = $exp[0];
			$eventquarter = $exp[1];
			$region_id = $exp[2];
			$regiondata = DB::table('branch')->where('region_id',$region_id)->where('program_id',5)->get();
		}
		//die();
		return view('backend.Region.RegionDashboard',compact('regiondata','region_id','eventyear','eventquarter','dataload'));
    }
    
    public function SectionDetails(Request $request)
	{
		$details = $request->get('section');
		$exp = explode(",", $details);
		$section = $exp[0];
		$eventid = $exp[1];
		$arcode='';
		$areaname='';
		$name =array();
		$tscore =array();
		$findarea = DB::select(DB::raw("select * from mnw_progoti.monitorevents where id='$eventid'"));
		if(!empty($findarea))
		{
			$arcode = $findarea[0]->area_id;
			$arname = DB::select(DB::raw("select * from branch where area_id='$arcode'"));
			if(!empty($arname))
			{
			$areaname = $arname[0]->area_name;
			}
		}
		$sectioname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no=$section and sub_sec_no='0' and qno=0"));
		if(!empty($sectioname))
		{
			$secname =  $sectioname[0]->qdesc;
		}
		else
		{
		$secname ='';
		}

        $p=0;
        $qp =0;
        $detailsdata = DB::select(DB::raw("select * from mnw_progoti.cal_section_point where event_id='$eventid' and section='$section' order by sub_id ASC"));
		//var_dump($detailsdata);
        foreach ($detailsdata as $row) 
        {
          $subid= $row->sub_id;
		  //echo $subid."/";
          $p = $row->point;
          $qp = $row->question_point;
          if($p !=0)
          {
            $tscore[] = round(($p/$qp*100),2);
          }
          else
          {
            $tscore[]=0;
          }
          $questionname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='$subid' and qno=0"));
		  
          
		 // var_dump($questionname);
          if(!empty($questionname))
          {
            $name[] = $questionname[0]->qdesc;
		  }
		}  
		$data['areaname']=$areaname;
		$data['serials']=$detailsdata;
		$data['questions']=$name;
		$data['scores']=$tscore;

		$myJSON = json_encode($data);
		
		echo $myJSON;
		// return view('Area/SectionDetails',compact('section','eventid'));
    }
    
	public function monthlySectionDetails(Request $request)
	{
		$details = $request->get('section');
		$exp = explode(",", $details);
		$section = $exp[0];
		$eventid = $exp[1];
		$month = $exp[2];
		if(strlen($month)==1){
			$month='0'.$exp[2];
		}
		$arcode='';
		$areaname='';
		$name =array();
		$tscore =array();
		$findarea = DB::select(DB::raw("select * from mnw_progoti.monitorevents where id='$eventid' and month='$month'"));
		if(!empty($findarea))
		{
			$arcode = $findarea[0]->area_id;
			$arname = DB::select(DB::raw("select * from branch where area_id='$arcode'"));
			if(!empty($arname))
			{
			$areaname = $arname[0]->area_name;
			}
		}
		$sectioname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no=$section and sub_sec_no='0' and qno=0"));
		if(!empty($sectioname))
		{
			$secname =  $sectioname[0]->qdesc;
		}
		else
		{
		$secname ='';
		}

        $p=0;
        $qp =0;
        $detailsdata = DB::select(DB::raw("select * from mnw_progoti.cal_section_point where event_id='$eventid' and section='$section' order by sub_id ASC"));
		//var_dump($detailsdata);
        foreach ($detailsdata as $row) 
        {
          $subid= $row->sub_id;
		  //echo $subid."/";
          $p = $row->point;
          $qp = $row->question_point;
          if($p !=0)
          {
            $tscore[] = round(($p/$qp*100),2);
          }
          else
          {
            $tscore[]=0;
          }
		  $questionname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$section' and sub_sec_no='$subid' and qno='0'"));
          
		 // var_dump($questionname);
          if(!empty($questionname))
          {
            $name[] = $questionname[0]->qdesc;
		  }
		}  
		$data['areaname']=$areaname;
		$data['serials']=$detailsdata;
		$data['questions']=$name;
		$data['scores']=$tscore;

		$myJSON = json_encode($data);
		
		echo $myJSON;
		// return view('Area/SectionDetails',compact('section','eventid'));
	}

	public function All_PreviousDataView(Request $request)
	{
		$region_id =$request->session()->get('asid');
		$allyear = DB::table('mnw_progoti.monitorevents')->select('year')->where('region_id',$region_id)->groupBy('year')->get();
		return view('backend.Region.RegionAllPreviousView',compact('allyear'));
	}

	public function AllPrevious(Request $request)
	{
		$year='';
		$quarter ='';
		$yr ='';
		$q='';
		$region_id =$request->session()->get('asid');
		$year = $request->get('year');
		$quarter = $request->get('quarter');
		if($year !='' and $quarter=='')
		{
			$alldata = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('region_id',$region_id)->groupBy('year','quarterly')->get();

		}
		else if($year !='' and $quarter !='')
		{
			$exp = explode("-", $quarter);
			$yr=$exp[0];
			$q = $exp[1];
			$alldata = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('region_id',$region_id)->where('quarterly',$q)->groupBy('year','quarterly')->get();
		}
		return view('backend.Region.RegionAllPreviousData',compact('alldata','region_id','yr','q'));
	}

	public function quarter(Request $request)
	{
		$year = $request->get('id');
		$asid =$request->session()->get('asid');
		$roll =$request->session()->get('roll');
		if($roll=='3')
		{
			$dataset =  DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('region_id',$asid)->groupBy('year','quarterly')->get();
		}
		else if($roll=='4')
		{
			$dataset =  DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->where('division_id',$asid)->groupBy('year','quarterly')->get();
		}
		else if(($roll=='7' or $roll=='16'or $roll=='5' or $roll=='17' or $roll=='18'))
		{
			$dataset =  DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('year',$year)->groupBy('year','quarterly')->get();
		}
		else
		{
			echo "Not Found Roll";
		}
		echo json_encode($dataset);
	}

	public function Region_Search(Request $request)
	{
		$a_id =$request->session()->get('asid');
		// $areasearch = DB::table('branch')->where('region_id',$a_id)->where('program_id',5)->get();
		$areasearch = DB::table('branch')->select('area_id','area_name')->where('region_id',$a_id)->where('program_id','5')->groupBy('area_id','area_name')->get();
		return view('backend.Region.Area_Search',compact('areasearch','a_id'));
	}
}
