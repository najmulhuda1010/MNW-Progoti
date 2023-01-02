<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Illuminate\Support\Facades\Input;
use Log;
use Session;

class ExportController extends Controller
{
    public function Export(Request $request)
	{
		$a_id =$request->session()->get('asid');
		$db = DB::select(DB::raw("select division_id,division_name from branch where program_id=5 group by division_id,division_name order by division_id ASC"));
		return view('backend.Export.Export',compact('a_id','db'));
	}
	public function quarter(Request $request)
	{
		$areaid = $request->get('id');
		$Quarter = DB::table('mnw_progoti.monitorevents')->select('year','quarterly')->where('area_id',$areaid)->GROUPBY('year','quarterly')->get();
		if(!empty($Quarter))
		{
			echo json_encode($Quarter);
		}
	}
	public function period(Request $request)
	{
		$year = $request->get('year');
		$quarter = $request->get('quarter');
		$regionid = $request->get('region_id');
		if($regionid == '' and $year!=''){
			$periods=DB::table('mnw_progoti.monitorevents')->select('datestart','dateend')->where('year',$year)->where('quarterly',$quarter)->groupBy('datestart','dateend')->orderBy('datestart','DESC')->get();		
		}elseif($year== '' and $regionid==''){
			$periods=DB::table('mnw_progoti.monitorevents')->select('datestart','dateend')->groupBy('datestart','dateend')->orderBy('datestart','DESC')->get();		
		}elseif($year== '' and $regionid!=''){
			$periods=DB::table('mnw_progoti.monitorevents')->select('datestart','dateend')->where('region_id',$regionid)->groupBy('datestart','dateend')->orderBy('datestart','DESC')->get();		
		}else{
			$periods=DB::table('mnw_progoti.monitorevents')->select('datestart','dateend')->where('region_id',$regionid)->where('year',$year)->where('quarterly',$quarter)->groupBy('datestart','dateend')->orderBy('datestart','DESC')->get();		
		}
		if(!empty($periods))
		{
			echo json_encode($periods);
		}
	}
}
