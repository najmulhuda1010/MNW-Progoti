<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Validator;
use Input;
use DataTables;
class EventController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db=config('database.name');
    }

    public function index(Request $request) {
        $data = DB::table('branch')
                    ->select('area_id','area_name')->where('program_id','5')
                    ->groupBy('area_id','area_name')
                    ->get();
        $users = DB::table($this->db.'.user')->get();

        
        return view('backend.events.eventCreate',compact('data','users'));
    }


    public function fetch($id){
        $data=DB::table('branch')->select('branch_id','branch_name')->where('area_id', $id)->where('program_id',5)->groupBy('branch_id','branch_name')->get();
         $count = DB::table('branch')->select('branch_id','branch_name')->where('area_id', $id)->where('program_id',5)->groupBy('branch_id','branch_name')->count();
         echo json_encode($data); 
    }

    public function store(Request $request)
    {
        $messsages = array(
            'area_id.required'=>'The area field can not be empty',
            'datestart.required'=>'The datestart field can not be empty',
            'dateend.required'=>'The dateend field can not be empty',
            'monitor1.required'=>'Please enter monitor 1 code',
            'monitor2.required'=>'Please enter monitor 2 code',
        );
    
        $rules = array(
            'area_id'=>'required',
            'datestart'=>'required',
            'dateend'=>'required',
            'monitor1'=>'required',
            'monitor2'=>'required',
        );
    
        $validator = Validator::make($request->all(), $rules,$messsages)->validate();
        $db=config('database.database');
        $data= array();
        $data['event_cycle'] = $request->get('cycle');
        $data['area_id'] = $request->get('area_id');
        $arr=$request->get('branch_id');
        if($arr == null){
            return redirect()->back()->with('error','Please select branchcode !');
        }
        $a = implode(",", $arr);
        $data['branchcode'] = $a;
        $data['datestart'] = $request->get('datestart');
        $data['dateend'] = $request->get('dateend');
        if($data['dateend'] < $data['datestart']){
            return redirect()->back()->with('error','Invalid Date range for event!');
        }
        $data['monitor1_code'] = $request->get('monitor1');
        $data['monitor2_code'] = $request->get('monitor2');
        $data['processing_date'] = date('Y-m-d');
        $exp= explode('-',$data['datestart']);
        $data['year'] = $exp[0];
		$data['month'] = $exp[1];
		if($data['month'] >='01' and $data['month'] <='03')
		{
			$quarter = '1st';
		}
		else if($data['month'] >='04' and $data['month'] <='06')
		{
			$quarter = '2nd';
		}
		else if($data['month'] >='07' and $data['month'] <='09')
		{
			$quarter = '3rd';
		}
		else if($data['month'] >='10' and $data['month'] <='12')
		{
			$quarter = '4th';
        }
        if($data['month'] <='03' || $data['month'] >=10)
		{
			$rc = 1;
		}
		else if($data['month']>='04' and $data['month'] <='09')
		{
			$rc = 2;
        }
        $data['quarterly']=$quarter;
        $data['rcycle']=$rc;
        $area = DB::table('branch')->where('area_id',$data['area_id'])->get();
		if($area ->isEmpty())
		{
			
		}
		else
		{
            $data['region_id'] = $area[0]->region_id;
			$data['division_id'] = $area[0]->division_id;
        }
        
        $query_insert = DB::table($this->db.'.monitorevents')->insert($data);
        return redirect()->back()->with('success','Success!! Monitor Event Created!!');
    }

    public function editOngoingEvent($id)
    {
    	$Event = DB::table($this->db.'.monitorevents')->find($id);
        $branch_ary=explode(',',$Event->branchcode);
        $branches=[];
        foreach ($branch_ary as $row) {
            $dataset=[];
            $dataset['branch_id']=$row;
            $branchname=DB::table('branch')->where('branch_id',$row)->where('program_id','5')->first();
            $dataset['branch_name']=$branchname->branch_name;
            $branches[]=$dataset;
        }
        // dd($branches);
        $data = DB::table('branch')->select('area_id','area_name')->where('program_id','5')->groupBy('area_id','area_name')->get();
        $users = DB::table($this->db.'.user')->get();
        return view('backend.events.eventOngoingUpdate', compact('Event','data','branches','users'));    
    }

    public function editUpcomingEvent($id)
    {
    	$Event = DB::table($this->db.'.monitorevents')->find($id);
        $branch_ary=explode(',',$Event->branchcode);
        $branches=[];
        foreach ($branch_ary as $row) {
            $dataset=[];
            $dataset['branch_id']=$row;
            $branchname=DB::table('branch')->where('branch_id',$row)->first();
            $dataset['branch_name']=$branchname->branch_name;
            $branches[]=$dataset;
        }
        // dd($branches);
        $data = DB::table('branch')->select('area_id','area_name')->where('program_id',5)->groupBy('area_id','area_name')->get();
        return view('backend.events.eventUpcomingUpdate', compact('Event','data','branches'));    
    }
    
    public function EventUpdate(Request $request)
    {
    //    dd($request->all());
        $messsages = array(
            'area_id.required'=>'The area field can not be empty',
            'datestart.required'=>'The datestart field can not be empty',
            'dateend.required'=>'The dateend field can not be empty',
            'monitor1.required'=>'Please enter monitor 1 code',
            'monitor2.required'=>'Please enter monitor 2 code',
        );

        $rules = array(
            'area_id'=>'required',
            'datestart'=>'required',
            'dateend'=>'required',
            'monitor1'=>'required',
            'monitor2'=>'required',
        );

        $validator = Validator::make($request->all(), $rules,$messsages)->validate();
       $db=config('database.database');
       $data= array();
       $id = $request->get('id');
       $data['event_cycle'] = $request->get('cycle');
       $data['area_id'] = $request->get('area_id');
       $arr=$request->get('branch_id');
       if($arr == null){
           return redirect()->back()->with('error','Please select branchcode !');
       }
       $a = implode(",", $arr);
       $data['branchcode'] = $a;
       $data['datestart'] = $request->get('datestart');
       $data['dateend'] = $request->get('dateend');
       if($data['dateend'] < $data['datestart']){
           return redirect()->back()->with('error','Invalid Date range for event!');
       }
       $data['monitor1_code'] = $request->get('monitor1');
       $data['monitor2_code'] = $request->get('monitor2');
       $data['processing_date'] = date('Y-m-d');
       $exp= explode('-',$data['datestart']);
       $data['year'] = $exp[0];
       $data['month'] = $exp[1];
       if($data['month'] >='01' and $data['month'] <='03')
       {
           $quarter = '1st';
       }
       else if($data['month'] >='04' and $data['month'] <='06')
       {
           $quarter = '2nd';
       }
       else if($data['month'] >='07' and $data['month'] <='09')
       {
           $quarter = '3rd';
       }
       else if($data['month'] >='10' and $data['month'] <='12')
       {
           $quarter = '4th';
       }
       if($data['month'] <='03' || $data['month'] >=10)
       {
           $rc = 1;
       }
       else if($data['month']>='04' and $data['month'] <='09')
       {
           $rc = 2;
       }
       $data['quarterly']=$quarter;
       $data['rcycle']=$rc;
       $area = DB::table('branch')->where('area_id',$data['area_id'])->get();
       if($area ->isEmpty())
       {
           
       }
       else
       {
           $data['region_id'] = $area[0]->region_id;
           $data['division_id'] = $area[0]->division_id;
       }
    //    dd($id);
        $currentdate=Date('Y-m-d');
       DB::table($this->db.'.monitorevents')->where('id',$id)->update($data);
       if($data['datestart']<=$currentdate and $data['dateend']>=$currentdate){
        return redirect(route('event.ongoing'))->with('success','Success!! Monitor Event Updated!!');
       }elseif($data['datestart']>$currentdate){
        return redirect(route('event.upcoming'))->with('success','Success!! Monitor Event Updated!!');
       }

    //    return redirect()->back()->with('success','Success!! Monitor Event Updated!!');
    }


    public function ongoingEvent() {
        return view('backend.events.ongoingEvent');
    }
    public function upcomingEvent() {
        return view('backend.events.upcomingEvent');
    }
    public function closedEvent() {
        return view('backend.events.closedEvent');
    }

    public function delete($id) {
        DB::table($this->db.'.monitorevents')->where('id', $id)->delete();
        return redirect()->back()->with('success','Success!! Monitor Event Deleted!!');
    }


    public function ongingEventDataLoad() {
        $var = Date('Y-m-d');
        $data=DB::table($this->db.'.monitorevents')->select('branchcode')->get();
        $events=DB::select(DB::raw("select m.changeroll,m.area_id,(select area_name from branch where area_id=m.area_id and program_id=5 group by area_name),m.id eventid,m.datestart,m.dateend,m.monitor1_code,(select name m1 from $this->db.user where cast(user_pin as INT)=m.monitor1_code),m.monitor2_code,(select name m2 from $this->db.user where cast(user_pin as INT)=m.monitor2_code) from $this->db.monitorevents m where datestart <='$var' and dateend >='$var'"));
        // dd($events);
		return Datatables::of($events)->addColumn('action', function ($events) { 
            if($events->changeroll=='0'){
                return '<a href="'.url('editOngoingEvent/'.$events->eventid).'" class="btn btn-light">Edit</a> <a href="#" class="btn btn-danger disabled" >Delete</a>';
            }else{
                return '<a href="'.url('editOngoingEvent/'.$events->eventid).'" class="btn btn-light disabled">Edit</a> <a href="#" class="btn btn-danger disabled" >Delete</a>';
            }
            
        })->addIndexColumn()->make(true);
    }
    public function upcomingEventDataLoad() {
        $var = Date('Y-m-d');
        $data=DB::table($this->db.'.monitorevents')->select('branchcode')->get();
        $events=DB::select(DB::raw("select m.area_id,(select area_name from branch where area_id=m.area_id and program_id=5 group by area_name),m.id eventid,m.datestart,m.dateend,m.monitor1_code,(select name m1 from $this->db.user where cast(user_pin as INT)=m.monitor1_code),m.monitor2_code,(select name m2 from $this->db.user where cast(user_pin as INT)=m.monitor2_code) from $this->db.monitorevents m where datestart > '$var'"));
        // dd($events);
		return datatables($events)->addColumn('action', function ($events) {
            return '<a href="'.url('editUpcomingEvent/'.$events->eventid).'" class="btn btn-light">Edit</a> <a href="'.url('deleteEvent/'.$events->eventid).'" class="btn btn-danger" onclick="return confirm('."'".'Are you sure you want to delete the event?'."'".')">Delete</a>';
        })->addIndexColumn()->toJson();
    }
    public function closedEventDataLoad() {
        $var = Date('Y-m-d');
        $data=DB::table($this->db.'.monitorevents')->select('branchcode')->get();
        $events=DB::select(DB::raw("select m.area_id,(select area_name from branch where area_id=m.area_id and program_id=5 group by area_name),m.id eventid,m.datestart,m.dateend,m.monitor1_code,(select name m1 from $this->db.user where cast(user_pin as INT)=m.monitor1_code),m.monitor2_code,(select name m2 from $this->db.user where cast(user_pin as INT)=m.monitor2_code) from $this->db.monitorevents m where dateend < '$var'"));
        // dd($events);
		return datatables($events)->addColumn('action', function ($events) {
            return '<a href="#edit-'.$events->eventid.'" class="btn btn-light">Edit</a>';
        })->toJson();
    }
}
