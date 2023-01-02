<?php

namespace App\Http\Controllers;

use Log;
use Redire;
use redirect;
use Illuminate\Http\Request;
use App\LoginModel;
use Illuminate\Support\Facades\Input;
use DB;
use App\Http\Controllers\SessionController;
use Session;
use App\Http\Controllers\Route;

header('Content-Type: text/html; charset=utf-8');
class LoginController extends Controller
{
	public function login(Request $req)
	{

		//dd('Huda');
		$db = 'mnw_progoti';
		$tkn = '';
		$datestart = '';
		$dateend = '';
		$br_code = 0;
		$brname = '';
		$role = 0;
		$username = '';
		$name = '';
		$userpin = '';
		$id = '';
		$apikey = '';
		$Key = 'dhaka1207#qsoft%net';
		$username = $req->get('username');
		$password = $req->input('password');
		$apikey = $req->header('apikey');
		//dd($username);
		$checklogin = DB::table($db.'.user')->where(['username' => $username, 'password' => $password])->get();
		//dd($checklogin);
		$token = uniqid();
		if ($Key == $apikey) {
			//$cnt = count($checklogin);
			//echo $cnt;
			//die;

			if (count($checklogin) > 0) {

				Session::put('token', $token);
				Session::put('username', $username);
				DB::table($db . '.user')->where('username', $username)->update(['session_data' => $token]);
				$userpin = $checklogin[0]->user_pin;
				//$id = $user_pin[0]->id;
				$username = $checklogin[0]->username;
				$name = $checklogin[0]->name;
				$va = date('Y-m-d');
				$var = date('Y-m-d'); //date('Y-m-d', strtotime($va . " +4 days"));
				$code =  DB::select(DB::raw("select * from $db.monitorevents where datestart <= '$va' and dateend >='$var' and (monitor1_code='$userpin' or monitor2_code='$userpin')  order by id desc limit 1"));
				$myArray = array();
				//$id = $code[0]->id;
				if (!empty($code)) {
					$id = 0;
					foreach ($code as $row) {
						$m1 = $row->monitor1_code;
						$m2 = $row->monitor2_code;
						$ds = $row->datestart;
						$de = $row->dateend;
						if ($m1 == $userpin) {
							// echo "Hud";
							if ($ds <= $va and $de >= $va) {
								$data = DB::table($db . '.monitorevents')->select('id', 'datestart', 'dateend', 'area_id')->where('datestart', '<=', $va)->where('dateend', '>=', $va)->where('monitor1_code', '=', $userpin)->get();
								//dd($data);
								$datestart = $data[0]->datestart;
								$dateend = $data[0]->dateend;
								$area_id = $data[0]->area_id;
								$id = $data[0]->id;
								$role = 1;
								$br_name = DB::table('branch')->select('area_name')->where('area_id', '=', $area_id)->get();
								$areaname = $br_name[0]->area_name;
								break;
							} else {
								//Log::useDailyFiles(storage_path().'/logs/debug.log');
								// Log::info('No Monitoring Event For Today.');
								$ar = array("status" => 'error', "message" => 'No Monitoring Event for Today!');
								$json = json_encode($ar);
								echo $json;
							}
						} else if ($m2 == $userpin) {
							if ($ds <= $va and $de >= $va) {
								$data = DB::table($db.'.monitorevents')->select('id', 'datestart', 'dateend', 'area_id')->where('datestart', '<=', $va)->where('dateend', '>=', $va)->where('monitor2_code', '=', $userpin)->get();
								$datestart = $data[0]->datestart;
								$dateend = $data[0]->dateend;
								$id = $data[0]->id;
								$role = 2;
								$area_id = $data[0]->area_id;
								$br_name = DB::table('branch')->select('area_name')->where('area_id', '=', $area_id)->get();
								$areaname = $br_name[0]->area_name;
								//echo $brname;
								break;
							} else {

								//Log::useDailyFiles(storage_path().'/logs/debug.log');
								// Log::info('No Monitoring Event For Today.');
								$ar = array("status" => 'error', "message" => 'No Monitoring Event for Today!');
								$json = json_encode($ar);
								echo $json;
							}
						}
					}
					//dd($id);
					$checkrespondent = DB::table($db.'.respondents')->where('eventid', $id)->get();
					//dd($checkrespondent);
					if ($checkrespondent->isEmpty()) {
						//$changeroll = DB::table($db . '.monitorevents')->where('area_id', $area_id)->where('changeroll', 1)->orderBy('id','DESC')->limit(1)->get();
						$changeroll = DB::select(DB::raw("select *  from  $db.monitorevents where area_id='$area_id' and id='$id' and changeroll= 1 order by id desc limit 1"));
						//dd($changeroll);
						if (empty($changeroll)) {
							//dd("j");
							$takn = DB::table($db . '.user')->select('session_data')->where('user_pin', '=', $userpin)->get();
							$tkn = $takn[0]->session_data;
							$areacode =  DB::table('branch')->where('area_id', $area_id)->where('program_id', 5)->get();
							if ($areacode->isEmpty()) {
							} else {
								$areaname = $areacode[0]->area_name;
								$regionname = $areacode[0]->region_name;
								$division = $areacode[0]->division_name;
								$ar = array("status" => 'success', "message" => 'Login Successfull', "token" => $tkn, "userid" => $username, "username" => $name, "userpin" => $userpin, "eventid" => $id, "datestart" => $datestart, "dateend" => $dateend, "eventrole" => $role, "CanChangeRole" => "Yes", "areaname" => $areaname, "area_id" => $area_id, "regionname" => $regionname, "divisionname" => $division);
								$json = json_encode($ar);
								echo $json;
								//Log::useDailyFiles(storage_path().'/logs/debug.log');
								//Log::info('Login Success '.$json);
							}
						} else {
							//dd("naj");
							$takn = DB::table($db . '.user')->select('session_data')->where('user_pin', '=', $userpin)->get();
							$tkn = $takn[0]->session_data;
							$areacode =  DB::table('branch')->where('area_id', $area_id)->where('program_id', 5)->get();
							if ($areacode->isEmpty()) {
							} else {
								$areaname = $areacode[0]->area_name;
								$regionname = $areacode[0]->region_name;
								$division = $areacode[0]->division_name;
								$ar = array("status" => 'success', "message" => 'Login Successfull', "token" => $tkn, "userid" => $username, "username" => $name, "userpin" => $userpin, "eventid" => $id, "datestart" => $datestart, "dateend" => $dateend, "eventrole" => $role, "CanChangeRole" => "No", "areaname" => $areaname, "area_id" => $area_id, "regionname" => $regionname, "divisionname" => $division);
								$json = json_encode($ar);
								echo $json;
								//Log::useDailyFiles(storage_path().'/logs/debug.log');
								//Log::info('Login Success '.$json);
							}
						}
					} else {
						//dd("Hu");
						$takn = DB::table($db . '.user')->select('session_data')->where('user_pin', '=', $userpin)->get();
						$tkn = $takn[0]->session_data;
						$areacode =  DB::table('branch')->where('area_id', $area_id)->where('program_id', 5)->get();
						if ($areacode->isEmpty()) {
						} else {
							$areaname = $areacode[0]->area_name;
							$regionname = $areacode[0]->region_name;
							$division = $areacode[0]->division_name;
							$ar = array("status" => 'success', "message" => 'Login Successfull', "token" => $tkn, "userid" => $username, "username" => $name, "userpin" => $userpin, "eventid" => $id, "datestart" => $datestart, "dateend" => $dateend, "eventrole" => $role, "CanChangeRole" => "No", "areaname" => $areaname, "area_id" => $area_id, "regionname" => $regionname, "divisionname" => $division);
							$json = json_encode($ar);
							echo $json;
							//Log::useDailyFiles(storage_path().'/logs/debug.log');
							//log::info('Login Success '.$json);
						}
					}
				} else {
					Log::info('No Monitoring Event For Today.');
					$ar = array("status" => 'error', "message" => 'No Monitoring Event for Today!');
					$json = json_encode($ar);
					echo $json;
				}
			} else {
				//return redirect()->back()->with('error','Error!! Invalied Username & Password!!!');
				$ar = array("status" => 'error', "message" => 'Login Failed!');
				$json = json_encode($ar);
				echo $json;
				//Log::useDailyFiles(storage_path().'/logs/debug.log');
				//Log::info('Login Failed '. $json);
			}
		} else {
			$ar = array("status" => 'error', "message" => 'Api Key Not Match!');
			$json = json_encode($ar);
			echo $json;
			//Log::useDailyFiles(storage_path().'/logs/debug.log');
			//Log::info('Login Failed '. $json);
		}
	}
	public function weblogin(Request $req)
	{
		$roll = 0;
		$pin = $req->input('user_pin');
		$roll = $req->input('roll_id');
		$name = $req->input('name');
		$user_id = $req->input('user_id');
		$as_id = $req->input('as_id');

		$token = uniqid();
		Session::put('token', $token);
		Session::put('username', $name);
		Session::put('roll', $roll);
		Session::put('asid', $as_id);
		Session::put('user_pin', $pin);

		if ($roll == '2') {
			return redirect('/AreaDashboard');
		} else if ($roll == '3') {
			return redirect('/RegionDashboard');
		} else if ($roll == '4') {
			return redirect('/DivisionDashboard');
		} else if ((($roll == '5' or $roll == '8') or ($roll == '9' or $roll == '10')) or ($roll == '11' or $roll == '12') or ($roll == '14')) //else if(((($roll=='8') or ($roll=='9'))) or ((($roll=='10') or ($roll=='11')) or ($roll=='12')))
		{
			return redirect('/NationalDashboard');
		} else if ($roll == '16' or ($roll == '7' or $roll == '14')) {
			return redirect('/NationalDashboard');
		} else if ($roll == '17') {
			return redirect('/ClDashboard');
		} else if ($roll == '18') {
			return redirect('/ZonalDashboard');
		} else {
			return redirect('/');
		}
	}
	public function changemonitor(Request $request)
	{
		$db='mnw_progoti';
		$session_data = $request->get('token');
		$eventid  =$request->get('evenId');
		$role =$request->get('evenRoll');
		$area_id =$request->get('area_id');
		$var = Date('Y-m-d');
		$checkuser = DB::table($db.'.user')->where('session_data',$session_data)->get();
		//var_dump($checkuser);
		if($checkuser->isEmpty())
		{
			$ar = array("status"=>'success',"message"=>'Token Mismatch!!');
			$json = json_encode($ar);
			echo $json;
		}
		else
		{
			$userpin = $checkuser[0]->user_pin;
			//$checkmonitor = DB::table('mnw.monitorevents')->where('branchcode',$brcode)->where('datestart','<',)->get();
			$checkmonitor = DB::select( DB::raw("select * from $db.monitorevents where area_id='$area_id' and id=$eventid"));
			if(empty($checkmonitor))
			{
				$ar = array("status"=>'success',"message"=>'No found Data!');
                $json = json_encode($ar);
			    echo $json;
			}
			else
			{
				$m1 = $checkmonitor[0]->monitor1_code;
				
				$m2 = $checkmonitor[0]->monitor2_code;
				//echo $m1."/".$m2;
				/* $changeroll = DB::table('mnw.monitorevents')->where('branchcode',$brcode)->where('changeroll',1)->get();
				if($changeroll->isEmpty())
				{  */
					if($role =='1')
					{
						if($userpin==$m1)
						{
							//echo $m1."m1";
							$response =DB::table($db.'.monitorevents')->where('id',$eventid)->update(['monitor1_code' =>$m1,'monitor2_code'=>$m2,'changeroll'=>1]);
						}
						else if($userpin==$m2)
						{
							
							$response =DB::table($db.'.monitorevents')->where('id',$eventid)->update(['monitor1_code' =>$m2,'monitor2_code'=>$m1,'changeroll'=>1]);
						}
						else
						{
							$ar = array("status"=>'success',"message"=>'No found monitor!');
							$json = json_encode($ar);
							echo $json;
						}
						$ar = array("status"=>'success',"message"=>'Change Monitor successfull',"token"=>$session_data,"eventid"=>$eventid,"area_id"=>$area_id,"eventrole"=>$role);
						$json = json_encode($ar);
						echo $json;
					}
					else if($role=='2')
					{
						//echo $userpin;
						if($userpin==$m1)
						{
							
							$response =DB::table($db.'.monitorevents')->where('id',$eventid)->update(['monitor1_code' =>$m2,'monitor2_code'=>$m1,'changeroll'=>1]);
						}
						else if($userpin==$m2)
						{
							
							$response =DB::table($db.'.monitorevents')->where('id',$eventid)->update(['monitor1_code' =>$m1,'monitor2_code'=>$m2,'changeroll'=>1]);
						}
						else
						{
							$ar = array("status"=>'success',"message"=>'No found monitor!');
							$json = json_encode($ar);
							echo $json;
						}
						$ar = array("status"=>'success',"message"=>'Change Monitor successfull',"token"=>$session_data,"eventid"=>$eventid,"area_id"=>$area_id,"eventrole"=>$role);
						$json = json_encode($ar);
						echo $json;
					}
					else
					{
						$ar = array("status"=>'success',"message"=>'No found Role!');
						$json = json_encode($ar);
						echo $json;
					}
				/* }
				else
				{
					$ar = array("status"=>'cannot change',"message"=>'Cannot change this roll',"token"=>$session_data,"eventid"=>$eventid,"branchcode"=>$brcode,"eventrole"=>$role);
					$json = json_encode($ar);
					echo $json;
				} */
				
				
				
			}
		}
	}

	public function Logout(Request $request)
	{
		$request->session()->forget('username');
		$request->session()->forget('token');
		return redirect('https://trendx.brac.net/home');
	}
}
