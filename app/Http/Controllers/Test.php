<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
//use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Input;
//use Illuminate\Support\Facades\Validator;
//use DB;
class Test extends Controller
{
	public function Login(Request $request)
	{
		//$data = Input::get("test");
		$data = $request->get('msg');
		if($data =='')
		{
			echo "Set empty(var)";
		}
		$test = DB::Table('mnw_progoti.cal_sections_score')->get();
		dd($test);
		return View('Test');
	}
    
}
