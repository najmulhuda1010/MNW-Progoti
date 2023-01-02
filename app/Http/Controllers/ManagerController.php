<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class ManagerController extends Controller
{
    public function ManagerDashboard(Request $request)
	{
		$dataload =true;
		$ar ='';
		$area_data ='';
		$div='';
		$reg='';
		$rollid='';
		$evnt ='';
		$userpin='';
		$div = $request->get('division');
		$reg = $request->get('region');
		$area_data = $request->get('area');
		$evnt = $request->get('eventid');
		// dd($evnt);
		$rollid =$request->session()->get('roll');
		$userpin =$request->session()->get('user_pin');
		if($area_data !='' and $reg != '')
		{
			$ar = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' order by id DESC limit 2"));
		}elseif($area_data !='' and $evnt!='')
		{
			$ar = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' order by id DESC limit 2"));
		}
		elseif($area_data !=''and $reg == '' and $evnt=='')
		{
			// $areaaryy=explode('-',$area_data);
			// $area_data=$areaaryy[1];
			$ar = DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area_data' order by id DESC limit 2"));
		}
		else
		{
			$division = DB::select( DB::raw("select  division_name,division_id from branch  where program_id='5' group by division_name,division_id order by division_id"));
		}
		$division = DB::select( DB::raw("select  division_name,division_id from branch  where program_id='5' group by division_name,division_id order by division_id"));
		if($rollid =='17')
		{
         $division = DB::select( DB::raw("select division_name,division_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where c_associate_id='$userpin') and program_id=5 group by division_id,division_name order by division_id ASC"));
		}
		else if($rollid =='18')
		{
         $division = DB::select( DB::raw("select division_name,division_id from branch where area_id in (select cast(area_id as INT) from mnw_progoti.cluster where z_associate_id='$userpin') and program_id=5 group by division_id,division_name order by division_id ASC"));
		}
		return view('backend.Manager.ManagerDashboard',compact('ar','division','dataload','div','reg','area_data','evnt','rollid','userpin'));
    }
    
	public function Region_Data(Request $request)
	{
		$div_id = $request->get('id');
		$regiondata = DB::select( DB::raw("select  region_name,region_id from branch  where division_id='$div_id' and program_id='5' group by region_name,region_id order by region_id"));
		echo json_encode($regiondata);
    }
    
	public function Area_Data(Request $request)
	{
		$region_id = $request->get('id');
		$areadata = DB::select( DB::raw("select  area_name,area_id from branch  where region_id='$region_id' and program_id='5' group by area_name,area_id order by area_id"));
		echo json_encode($areadata);
	}
	
	public function SectionDetails(Request $request)
	{
		$data = $request->get('data');
		$exp = explode(",", $data);
		$sec = $exp[0];
		$subsec = $exp[1];
		$event = $exp[2];

		$survey_data = DB::select( DB::raw("select * from mnw_progoti.survey_data where event_id='$event' and sec_no='$sec' and sub_id='$subsec'"));

		// if($sec=='3' and $subsec=='10'){
		// 	$survey_data = DB::select( DB::raw("select * from mnw_progoti.survey_data where event_id='$event' and sec_no='$sec' and sub_id='$subsec' and score=1"));
		// }else{
		// }

		//var_dump($survey_data);
		return view('backend.Manager.SectionDetails',compact('survey_data','sec','subsec','event'));
	}
	public function Remarks(Request $request)
	{
		$area = $request->get('area');
		$region = $request->get('region');
		$event = $request->get('event');
		if($event){
			$eventid= DB::select( DB::raw("select * from mnw_progoti.monitorevents where id=$event"));
		}else{
			$eventid= DB::select( DB::raw("select * from mnw_progoti.monitorevents where area_id='$area' and region_id='$region' order by id DESC"));
		}
		
		if(!empty($eventid))
		{
			$event_id = $eventid[0]->id;
			$datestart = $eventid[0]->datestart;
			$dateend = $eventid[0]->dateend;
		}
		else
		{
			$event_id =0;
			$datestart='';
			$dateend='';
		}
		$remarks = DB::select( DB::raw("select event_id,sec_no,sub_id from mnw_progoti.survey_data where event_id='$event_id' and remarks !='' and remarks !='--' group by event_id,sec_no,sub_id order by sec_no ASC"));
		return view('backend.Manager.AllRemarks',compact('remarks','area','region','datestart','dateend'));
		
	}

	public function frauddocuments(Request $request)
	{
		$event_id = $request->get('event');
		$fradDocuments=[];

		$event= DB::select( DB::raw("select * from mnw_progoti.monitorevents where id='$event_id'"));
		if(!empty($event))
		{
			$datestart = $event[0]->datestart;
			$dateend = $event[0]->dateend;
			$area_id = $event[0]->area_id;
			$region_id = $event[0]->region_id;
			$division_id = $event[0]->division_id;

			$area=DB::table('branch')->select('area_name')->where('area_id',$area_id)->first();
			$region=DB::table('branch')->select('region_name')->where('region_id',$region_id)->first();
			$division=DB::table('branch')->select('division_name')->where('division_id',$division_id)->first();
			
			$area_name=$area->area_name;
			$region_name=$region->region_name;
			$division_name=$division->division_name;
		}

		$documents = DB::table('mnw_progoti.survey_data')->where('event_id',$event_id)->where('sec_no',1)->where('sub_id',5)->where('remarks', '!=' ,'--')->get();
		foreach ($documents as $row) {
			$dataset=[];
			$remark=$row->remarks;
			$document=explode('--',$remark);
			if($document[0]){
				$dataset['branchcode']=$row->branchcode;
				$dataset['orgmemno']=$row->orgmemno;
				$dataset['document']=$document[0];
				$fradDocuments[]=$dataset;
			}
		}
		// dd($fradDocuments);

		return view('backend.Manager.fraddocuments',compact('fradDocuments','area_name','region_name','division_name','datestart','dateend'));
		
	}
}
