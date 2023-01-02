<?php
namespace App\Http\Controllers;

use view;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use App\Http\Requests;
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 1200);
use ZipArchive;
use Log;
//use App\Http\Controllers\TestingController_Version;
use Illuminate\Support\Facades\Storage;
use File;
header('Content-Type: text/html; charset=utf-8');
class DataProcessingController extends Controller
{
	public function DataProcess(Request $req)
	{
		//echo "Huda";
		$db ='progoti_snapshot';
		$db2='mnw_progoti';
		$cdate = date('Y-m-d');
		$events = DB::Table($db2.'.monitorevents')->where('datestart','<=',$cdate)->where('dateend','>=',$cdate)->get();
		if(!$events->isEmpty())
		{
			foreach($events as $row)
			{
				$branchcode = $row->branchcode;
				$eventid = $row->id;
				$area_id = $row->area_id;
				$pdate = $row->datestart;
				for($i =1;$i <=5; $i++)
				{
					if($i==1)
					{
						$this->section1($db,$pdate,$i,$branchcode,$area_id,$eventid);
					}
					else if($i==3)
					{
						
					}
					else if($i==4)
					{
						
					}
					else if($i==5)
					{
						
					}
				}
			}
		}		
	}//1.4
	public function section1($db,$pdate,$j,$branchcode,$area_id,$eventid)
	{
		$pdate2mnth = date('Y-m-d', strtotime($pdate. ' - 2 month'));
		$sub1 = 1;
		$sub2 = 2;
		$sub3 = 3;
		$sub4 = 4;
		$sub5 = 5;
		if($sub1=='1')
		{
			$section1sub1count= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->count();
			if($section1sub1count >=25)
			{
				$this->get_sub_id($db,1,$pdate2mnth,$pdate,$j,$branchcode,$area_id,$eventid);
			}
			else
			{
				$member = 0;
				$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth. ' - 1 month'));
				$totalmonthcount =0;
				//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
				$totalmonth =DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
				if(!empty($totalmonth))
				{
					foreach($totalmonth as $row)
					{
						$totalmonthcount +=1;
					}
				}
				for($i=1;$i <= $totalmonthcount;$i++)
				{
					if($member <=25)
					{
						$section1sub1count= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->count();
						if($section1sub1count >=25)
						{
							$section1sub1= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->get();
							//dd($section1sub1);
							if(!$section1sub1->isEmpty())
							{
								foreach($section1sub1 as $rows)
								{
									$sec_no=$j;
									$sub_id=1;
									$branchcode = $branchcode;
									$orgmemno =$rows->orgmemno;
									$disbdate = $rows->disbdate;
									$loanslno = $rows->loanslno;
									$productno = $rows->productno;
									$eventid= $eventid;
									$area_id= $area_id;
									$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('sub_id',$sub_id)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
									if($checkrespondents->isEmpty())
									{
										$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
										'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
									}
									
								}
							}
							
							$member = $section1sub1count;
						}
						else
						{
							echo $pdate1mnth;
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
							echo $pdate1mnth."/";
						}
					}
					
				}
			}
		}
		//section1 sub_id 1
		/*if($sub2=='2')
		{
			$section1sub2count= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->count();
			if($section1sub2count >=12)
			{
				$section1sub1= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->get();
				//dd($section1sub1);
				if(!$section1sub1->isEmpty())
				{
					foreach($section1sub1 as $rows)
					{
						$sec_no=$j;
						$sub_id=2;
						$branchcode = $branchcode;
						$orgmemno =$rows->orgmemno;
						$disbdate = $rows->disbdate;
						$loanslno = $rows->loanslno;
						$productno = $rows->productno;
						$eventid= $eventid;
						$area_id= $area_id;
						$checkrespondents  = DB::Table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
						//dd($checkrespondents);
						if($checkrespondents->isEmpty())
						{
							$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
							'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
						}
						else
						{
							continue;
						}
						
						
					}
				}
				//dd("Huda");
				$checkrespondentscounts  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$j)->where('sub_id',2)->count();
				if($checkrespondentscounts >=12)
				{
					
				}
				else
				{
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth. ' - 1 month'));
					$totalmonthcount =0;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					$totalmonth =DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
					if(!empty($totalmonth))
					{
						foreach($totalmonth as $row)
						{
							$totalmonthcount +=1;
						}
					}
					for($i=1;$i <= $totalmonthcount;$i++)
					{
						if($member <=12)
						{
							$section1sub1count= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->count();
							if($section1sub1count >=12)
							{
								$section1sub1= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->get();
								//dd($section1sub1);
								if(!$section1sub1->isEmpty())
								{
									foreach($section1sub1 as $rows)
									{
										$sec_no=$j;
										$sub_id=2;
										$branchcode = $branchcode;
										$orgmemno =$rows->orgmemno;
										$disbdate = $rows->disbdate;
										$loanslno = $rows->loanslno;
										$productno = $rows->productno;
										$eventid= $eventid;
										$area_id= $area_id;
										$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
										if($checkrespondents->isEmpty())
										{
											$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
											'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
										}
									}
								}
								$checkrespondentscounts  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$j)->where('sub_id',2)->count();
								if($checkrespondentscounts >=12)
								{
									$member = $section1sub1count;
								}
								else
								{
									echo $pdate1mnth;
									$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
									echo $pdate1mnth."/";
								}
								
								
							}
							else
							{
								echo $pdate1mnth;
								$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
								echo $pdate1mnth."/";
							}
						}
						
					}
				}
				
			}
			else
			{
				$member = 0;
				$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth. ' - 1 month'));
				$totalmonthcount =0;
				//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
				$totalmonth =DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
				if(!empty($totalmonth))
				{
					foreach($totalmonth as $row)
					{
						$totalmonthcount +=1;
					}
				}
				for($i=1;$i <= $totalmonthcount;$i++)
				{
					if($member <=12)
					{
						$section1sub1count= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->count();
						if($section1sub1count >=12)
						{
							$section1sub1= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->get();
							//dd($section1sub1);
							if(!$section1sub1->isEmpty())
							{
								foreach($section1sub1 as $rows)
								{
									$sec_no=$j;
									$sub_id=2;
									$branchcode = $branchcode;
									$orgmemno =$rows->orgmemno;
									$disbdate = $rows->disbdate;
									$loanslno = $rows->loanslno;
									$productno = $rows->productno;
									$eventid= $eventid;
									$area_id= $area_id;
									$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('sub_id',$sub_id)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
									if($checkrespondents->isEmpty())
									{
										$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
										'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
									}	
									
								}
							}
							
							$member = $section1sub1count;
						}
						else
						{
							echo $pdate1mnth;
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
							echo $pdate1mnth."/";
						}
					}
					
				}
			}
		}*/
		//section1 sub_id2
		if($sub2=='2')
		{
			$section1sub2count= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->count();
			if($section1sub2count >=12)
			{
				$this->get_sub_id($db,2,$pdate2mnth,$pdate,$j,$branchcode,$area_id,$eventid);
			}
			else
			{
				$member = 0;
				$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth. ' - 1 month'));
				$totalmonthcount =0;
				//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
				$totalmonth =DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
				if(!empty($totalmonth))
				{
					foreach($totalmonth as $row)
					{
						$totalmonthcount +=1;
					}
				}
				for($i=1;$i <= $totalmonthcount;$i++)
				{
					if($member <=12)
					{
						$section1sub1count= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->count();
						if($section1sub1count >=12)
						{
							$section1sub1= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->get();
							//dd($section1sub1);
							if(!$section1sub1->isEmpty())
							{
								foreach($section1sub1 as $rows)
								{
									$sec_no=$j;
									$sub_id=2;
									$branchcode = $branchcode;
									$orgmemno =$rows->orgmemno;
									$disbdate = $rows->disbdate;
									$loanslno = $rows->loanslno;
									$productno = $rows->productno;
									$eventid= $eventid;
									$area_id= $area_id;
									$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('sub_id',$sub_id)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
									if($checkrespondents->isEmpty())
									{
										$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
										'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
									}	
									
								}
							}
							
							$member = $section1sub1count;
						}
						else
						{
							echo $pdate1mnth;
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
							echo $pdate1mnth."/";
						}
					}
					
				}
			}
		}
		if($sub5=='5')
		{
			$countrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('loanslno','>',1)->count();
			if($countrespondents >= 25)
			{
				
			}
			else
			{
				$section1sub1count= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->count();
				if($section1sub1count >=25)
				{
					$this->get_sub_id($db,5,$pdate2mnth,$pdate,$j,$branchcode,$area_id,$eventid);
				}
				else
				{
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth. ' - 1 month'));
					$totalmonthcount =0;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					$totalmonth =DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
					if(!empty($totalmonth))
					{
						foreach($totalmonth as $row)
						{
							$totalmonthcount +=1;
						}
					}
					for($i=1;$i <= $totalmonthcount;$i++)
					{
						if($member <=25)
						{
							$section1sub1count= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->count();
							if($section1sub1count >=25)
							{
								$section1sub1= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate1mnth)->where('disbdate','<=',$pdate)->get();
								//dd($section1sub1);
								if(!$section1sub1->isEmpty())
								{
									foreach($section1sub1 as $rows)
									{
										$sec_no=$j;
										$sub_id=5;
										$branchcode = $branchcode;
										$orgmemno =$rows->orgmemno;
										$disbdate = $rows->disbdate;
										$loanslno = $rows->loanslno;
										$productno = $rows->productno;
										$eventid= $eventid;
										$area_id= $area_id;
										$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
										if($checkrespondents->isEmpty())
										{
											$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
											'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
										}
										
									}
								}
								
								$member = $section1sub1count;
							}
							else
							{
								echo $pdate1mnth;
								$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth. ' - 1 month'));
								echo $pdate1mnth."/";
							}
						}
						
					}
				}
			}
		}
	}
	public function get_sub_id($db,$sub_id,$pdate2mnth,$pdate,$j,$branchcode,$area_id,$eventid)
	{
		
		
		if($sub_id=='1')
		{
			$section1sub1= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->get();
		}
		else if($sub_id=='2' or $sub_id=='5')
		{
			$section1sub1= DB::Table($db.'.cloans')->where('loanslno','>',1)->where('disbdate','>=',$pdate2mnth)->where('disbdate','<=',$pdate)->get();
		}
		//dd($section1sub1);
		if(!$section1sub1->isEmpty())
		{
			foreach($section1sub1 as $rows)
			{
				$sec_no=$j;
				$sub_id=$sub_id;
				$branchcode = $branchcode;
				$orgmemno =$rows->orgmemno;
				$disbdate = $rows->disbdate;
				$loanslno = $rows->loanslno;
				$productno = $rows->productno;
				$eventid= $eventid;
				$area_id= $area_id;
				$checkrespondents  = DB::table($db.'.respondents')->where('eventid',$eventid)->where('area_id',$area_id)->where('branchcode',$branchcode)->where('sec_no',$sec_no)->where('sub_id',$sub_id)->where('orgmemno',$orgmemno)->where('disbdate',$disbdate)->get();
				if($checkrespondents->isEmpty())
				{
					$section1sub1insert = DB::Table($db.'.respondents')->insert(['sec_no'=>$sec_no,'sub_id'=>$sub_id,'branchcode'=>$branchcode,'orgmemno'=>$orgmemno,
					'disbdate'=>$disbdate,'loanslno'=>$loanslno,'productname'=>$productno,'eventid'=>$eventid,'area_id'=>$area_id]);
				}
				
			}
		}
	}
	
}