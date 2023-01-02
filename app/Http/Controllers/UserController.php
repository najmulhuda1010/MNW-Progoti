<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;

class UserController extends Controller
{   
    private $db;

    public function __construct()
    {
        $this->db=config('database.name');
    }
    

    public function index(){
        return view('backend.users.user_list');
    }

    public function create(){
        $clusters=DB::table('mnw_progoti.cluster')->select('cluster_id','cluster_name')->groupBy('cluster_id','cluster_name')->orderBy('cluster_id','desc')->get();
        return view('backend.users.user_create',compact('clusters'));
    }

    public function store(Request $request){
        $cluster_id=null;
       $name=$request->get('name');
	   $email = $request->get('email');
	   $phone=$request->get('phone');
	   $username = $request->get('username');
	   $password = $request->get('password');
	   $curdate = date('Y-m-d');
	   $userpin = $request->get('userpin');
	   $deviceid = $request->get('deviceid');
	   $cluster = $request->get('cluster');
       if($cluster){
           $cluster_ary=explode('-',$cluster); 
           $cluster_id=$cluster_ary[0];
       }
    //    dd($deviceid);
	  $checkexist=DB::table('mnw_progoti.user')->where('user_pin',$userpin)->get();
    //   dd($checkexist);
      if($checkexist->isEmpty()){
        DB::table($this->db.'.user')->insert(['name' =>$name,'email'=>$email,'phone'=>$phone,'username'=>$username,'password'=>$password,'user_pin'=>$userpin,'device_id'=>$deviceid,'cluster_id'=>$cluster_id]);	 //Monitorevent::create($data);
      
        return redirect()->back()->with('success','Success!! Monitor User Created!!');
      }else{
        return redirect()->back()->with('error','Monitor User Exist!!');
      }
	  
	  
    }

    public function UserEdit(Request $request)
	{
		$id = $request->get('id');
        $clusters=DB::table('mnw_progoti.cluster')->select('cluster_id','cluster_name')->groupBy('cluster_id','cluster_name')->orderBy('cluster_id','desc')->get();
		$edit = DB::table('mnw_progoti.user')->where('id',$id)->get();
		return view('backend.users.user_edit', compact('edit','clusters'));
		
	}

	public function UserEditStore(Request $request)
	{
        // dd('asd');
            $cluster_id=null;
		   $name=$request->get('name');
		   $email = $request->get('email');
		   $phone=$request->get('phone');
		   $username = $request->get('username');
		   $password = $request->get('password');
		   $curdate = date('Y-m-d');
		   $userpin = $request->get('userpin');
		   $deviceid = $request->get('deviceid');
		   $id = $request->get('id');
           $cluster = $request->get('cluster');
            if($cluster){
                $cluster_ary=explode('-',$cluster); 
                $cluster_id=$cluster_ary[0];
            }
            // dd($id);
		   DB::table('mnw_progoti.user')->where('id',$id)->update(['name' =>$name,'email'=>$email,'phone'=>$phone,'username'=>$username,'password'=>$password,'user_pin'=>$userpin,'device_id'=>$deviceid,'cluster_id'=>$cluster_id]);	 

           return redirect('/UserList')->with('success','Success!! Monitor User Updated!!');

	}

    public function UserDelete()
	{
        $id = $request->get('id');
		DB::table('mnw_progoti.user')->where('id',$id)->delete();
		
        return redirect('/UserList')->with('success','Success!! User Deleted!!');

    }

    public function userList()
	{
       $db=config('database.database');
       $users=DB::table($this->db.'.user')->orderBy('id','asc')->get();
		return datatables($users)->addColumn('cluster_name', function ($users) {
            $cluster_name='';
            $cluster_id=$users->cluster_id;
            $cluster_ary=DB::table('mnw_progoti.cluster')->where('cluster_id',$cluster_id)->first();
            if($cluster_ary){
                $cluster_name=$cluster_ary->cluster_name;
                return $cluster_name;
            }
            return $cluster_name;
        })->addColumn('edit', function ($users) {
            return '<a href="UserEdit?id='.$users->id.'" class="btn btn-light btn-sm" style="font-size: 12px;">Edit</a>';
        })->addColumn('delete', function ($users) {
            return '<a href="UserDelete?id='.$users->id.'" class="btn btn-danger btn-sm" style="font-size: 12px;" onclick="return confirm('."'".'Are you sure you want to delete the user?'."'".')">Delete</a>';
        })->rawColumns([
            'edit','delete'
        ])->toJson();
	}

}
