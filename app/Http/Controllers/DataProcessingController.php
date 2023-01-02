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
	public function DataProcess()
	{
		//echo "Huda";
		$db = 'progoti_snapshot';
		$db2 = 'mnw_progoti';
		$cdate = date('Y-m-d');
		$events = DB::Table($db2 . '.monitorevents')->where('dateend', '>=', $cdate)->where('data_proccess_status', 0)->get();
		// $events = DB::Table($db2.'.monitorevents')->where('id',10)->get();
		// dd($events);
		if (!$events->isEmpty()) {
			foreach ($events as $row) {
				DB::beginTransaction();
				try {
					$branchcode = $row->branchcode;
					$eventid = $row->id;
					$area_id = $row->area_id;

					$pdate = $row->datestart;  //event start date
					$pdate = strtotime('last day of previous month', strtotime($pdate)); //skip the event month
					$pdate = date('Y-m-d', $pdate);
					$previousdate = strtotime('- 1 day', strtotime($row->datestart)); //skip the event month
					$previousdate = date('Y-m-d', $previousdate);
					// dd($previousdate);

					for ($i = 1; $i <= 5; $i++) {
						if ($i == 1) {
							$this->section1($db, $pdate, $previousdate, $i, $branchcode, $area_id, $eventid);
						} else if ($i == 3) {
							$this->section3($db, $pdate, $i, $branchcode, $area_id, $eventid);
						} else if ($i == 4) {
						} else if ($i == 5) {
						}
					}
					DB::commit();

					DB::table($db2 . '.monitorevents')->where('id', $eventid)->update(['data_proccess_status' => 1]);

					return "Sample Respondents Created Sucessfully";
				} catch (\Throwable $e) {
					DB::rollback();
					throw $e;
				}
			}
		}
	} //1.4
	//section 1 start here
	public function section1($db, $pdate, $previousdate, $j, $branchcode, $area_id, $eventid)
	{
		$pdate2mnth = date('Y-m-d', strtotime($previousdate . ' - 2 month')); //previous 2 month
		$branch_array = explode(',', $branchcode);
		$branchcount = count($branch_array);
		$cono = '';
		$membername = '';
		$sub1 = 1;
		$sub2 = 2;
		$sub3 = 3;
		$sub4 = 4;
		$sub5 = 5;
		if ($sub1 == '1') //section 1.1 start here
		{
			$membername = '';
			$respondentForEveryBranch = round(25 / $branchcount);

			foreach ($branch_array as $branchcode) { //loop starts for every branch of that area
				$br_lenght = strlen($branchcode);

				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$section1sub1count = DB::Table($db . '.cloans')->where('loanslno', 1)->where('branchcode', $branchcode)->where('disbdate', '>=', $pdate2mnth)->where('disbdate', '<=', $previousdate)->count();
				// dd($previousdate);
				if ($section1sub1count >= $respondentForEveryBranch) {
					$this->get_sub_id_sec_1($db, 1, $pdate2mnth, $previousdate, $j, $branchcode, $area_id, $eventid);
				} else {
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth . ' - 1 month'));
					$totalmonthcount = 30;
					// $totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					// $totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans where branchcode='$branchcode' group by disbdate order by disbdate DESC"));
					// dd($totalmonth);
					// if (!empty($totalmonth)) {
					// 	foreach ($totalmonth as $row) {
					// 		$totalmonthcount += 1;
					// 	}
					// }
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$section1sub1count = DB::Table($db . '.cloans')->where('loanslno', 1)->where('branchcode', $branchcode)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->count();

							if ($section1sub1count >= $respondentForEveryBranch) {
								$section1sub1 = DB::Table($db . '.cloans')->where('loanslno', 1)->where('branchcode', $branchcode)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->get();
								if (!$section1sub1->isEmpty()) {
									foreach ($section1sub1 as $row) {
										$membername = '';
										$sec_no = $j;
										$sub_id = 1;
										$branchcode = $branchcode;
										$orgmemno = $row->orgmemno;
										$disbdate = $row->disbdate;
										$loanslno = $row->loanslno;
										$loansize = $row->principalamt;
										$productname = $row->productname;
										$eventid = $eventid;
										$area_id = $area_id;

										$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										if ($member) {
											$membername = $member->membername;
										}

										$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										if ($assignedpo) {
											$cono = $assignedpo->assignedpo;
										}

										$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

										if ($checkrespondents->isEmpty()) {
											$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
											// dd($branchcode);
											if ($countsectiondata < $respondentForEveryBranch) {
												$section1sub1insert = DB::Table($db . '.respondents')->insert(['sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno, 'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername]);
											}
										}
									}
								}

								$member = $section1sub1count;
							} else {
								// echo $pdate1mnth;
								$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
								// echo $pdate1mnth."/";
							}
						} else {
							break;
						}
					}
				}
			}
		}
		//section1 sub_id2
		if ($sub2 == '2') {
			$respondentForEveryBranch = round(12 / $branchcount);
			$membername = '';
			foreach ($branch_array as $branchcode) {
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$section1sub2count = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate2mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->count();
				if ($section1sub2count >= $respondentForEveryBranch) {
					$this->get_sub_id_sec_1($db, 2, $pdate2mnth, $previousdate, $j, $branchcode, $area_id, $eventid);
				} else {
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth . ' - 1 month'));
					$totalmonthcount = 0;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					$totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
					if (!empty($totalmonth)) {
						foreach ($totalmonth as $row) {
							$totalmonthcount += 1;
						}
					}
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$section1sub1count = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->count();
							if ($section1sub1count >= $respondentForEveryBranch) {
								$section1sub1 = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->get();
								//dd($section1sub1);
								if (!$section1sub1->isEmpty()) {
									foreach ($section1sub1 as $row) {
										$membername = '';
										$sec_no = $j;
										$sub_id = 2;
										$branchcode = $branchcode;
										$orgmemno = $row->orgmemno;
										$disbdate = $row->disbdate;
										$loanslno = $row->loanslno;
										$loansize = $row->principalamt;
										$productname = $row->productname;
										$eventid = $eventid;
										$area_id = $area_id;

										$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										// dd($member);
										if ($member) {
											$membername = $member->membername;
										}


										$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										if ($assignedpo) {
											$cono = $assignedpo->assignedpo;
										}

										$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
										if ($checkrespondents->isEmpty()) {
											$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
											if ($countsectiondata < $respondentForEveryBranch) {
												$section1sub1insert = DB::Table($db . '.respondents')->insert([
													'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
													'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername
												]);
											}
										}
									}
								}

								$member = $section1sub1count;
							} else {
								// echo $pdate1mnth;
								$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
								// echo $pdate1mnth."/";
							}
						} else {
							break;
						}
					}
				}
			}
		}
		if ($sub5 == '5') {
			$membername = '';
			$sec_no = $j;
			$respondentForEveryBranch = round(25 / $branchcount);
			$membername = '';
			foreach ($branch_array as $branchcode) {
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$section1sub1count = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate2mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->count();
				if ($section1sub1count >= $respondentForEveryBranch) {
					$this->get_sub_id_sec_1($db, 5, $pdate2mnth, $previousdate, $j, $branchcode, $area_id, $eventid);
				} else {
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth . ' - 1 month'));
					$totalmonthcount = 0;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					$totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans group by disbdate order by disbdate DESC"));
					if (!empty($totalmonth)) {
						foreach ($totalmonth as $row) {
							$totalmonthcount += 1;
						}
					}
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$section1sub1count = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->count();
							if ($section1sub1count >= $respondentForEveryBranch) {
								$section1sub1 = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('disbdate', '>=', $pdate1mnth)->where('disbdate', '<=', $previousdate)->where('branchcode', $branchcode)->get();
								//dd($section1sub1);
								if (!$section1sub1->isEmpty()) {
									foreach ($section1sub1 as $row) {
										$membername = '';
										$sec_no = $j;
										$sub_id = 5;
										$branchcode = $branchcode;
										$orgmemno = $row->orgmemno;
										$disbdate = $row->disbdate;
										$loanslno = $row->loanslno;
										$loansize = $row->principalamt;
										$productname = $row->productname;
										$eventid = $eventid;
										$area_id = $area_id;

										$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										if ($member) {
											$membername = $member->membername;
										}

										$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
										if ($assignedpo) {
											$cono = $assignedpo->assignedpo;
										}

										$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
										if ($checkrespondents->isEmpty()) {
											$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
											if ($countsectiondata < $respondentForEveryBranch) {
												$section1sub1insert = DB::Table($db . '.respondents')->insert([
													'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
													'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername
												]);
											}
										}
									}
								}

								$member = $section1sub1count;
							} else {
								// echo $pdate1mnth;
								$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
								// echo $pdate1mnth."/";
							}
						} else {
							break;
						}
					}
				}
			}
		}
	}
	//section 3 start here
	public function section3($db, $pdate, $j, $branchcode, $area_id, $eventid)
	{

		$pdate5mnth = date('Y-m-d', strtotime($pdate . ' - 5 month'));
		$pdate2mnth = date('Y-m-d', strtotime($pdate . ' - 2 month'));
		$pdate3mnth = date('Y-m-d', strtotime($pdate . ' - 3 month'));
		$previous1mnth = date('Y-m-d', strtotime($pdate . ' - 1 month'));
		$cdate = date('Y-m-d');
		$sec_no = $j;
		$branch_array = explode(',', $branchcode);
		$branchcount = count($branch_array);
		$cono = '';
		$sub1 = 1;
		$sub2 = 2;
		$sub3 = 3;
		$sub4 = 4;
		$sub5 = 5;
		$sub6 = 6;
		$sub7 = 7;
		$sub8 = 8;
		$sub9 = 9;
		// $sub1 = 0;
		// $sub2 = 0;
		// $sub3 = 0;
		// $sub4 = 0;
		// $sub5 = 0;
		// $sub6 = 0;
		// $sub7 = 0;
		// $sub8 = 0;
		// $sub9 = 9;

		$membername = '';
		if ($sub1 == '1') {
			$membername = '';
			$respondentForEveryBranch = round(32 / $branchcount);
			// $pdate3mnth = date('Y-m-d', strtotime($cdate . ' - 3 month'));
			foreach ($branch_array as $branchcode) {
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				// dd($branch_array);

				$currentborrower = DB::Table($db . '.cloans')->where('branchcode', $branchcode)->where('lnstatus', 1)->get();
				$dataset = array();
				foreach ($currentborrower as $row) {
					$orgmemno = $row->orgmemno;
					$loanno = $row->loanno;
					$filter = DB::select(DB::raw("SELECT b.orgmemno,b.loanno,EXTRACT(MONTH FROM a.targetdate) as mismonth FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and EXTRACT(MONTH FROM a.targetdate)=EXTRACT(MONTH FROM b.colcdate) and a.targetdate > '$pdate3mnth' group by b.orgmemno,b.loanno,a.targetdate"));
					// $filter = DB::select(DB::raw("SELECT b.orgmemno,b.loanno,EXTRACT(MONTH FROM a.targetdate) as mismonth FROM $db.targets a left join $db.transectionsloan b on  a.loanno=b.loanno where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and a.targetdate > '$previous1mnth' group by b.orgmemno,b.loanno,a.targetdate"));

					// if ($eventid == '29') {
					// dd($currentborrower);
					// }
					if (!$filter) {
						$targets = DB::table($db . '.targets')->where('loanno', $loanno)->where('orgmemno', $orgmemno)->count();
						if ($targets > 0) {
							$filter = DB::select(DB::raw("SELECT EXTRACT(MONTH FROM a.targetdate) as mismonth FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and EXTRACT(MONTH FROM a.targetdate)!=EXTRACT(MONTH FROM b.colcdate) and a.targetdate > '$pdate3mnth' group by b.orgmemno,b.loanno,a.targetdate"));
							// dd($mismonth, $orgmemno, $loanno);

							// $loanno = $filter[0]->loanno;
							if ($filter) {
								$instmisdate = $filter[0]->mismonth;
								$sec3sub1 = DB::select(DB::raw("SELECT * FROM $db.cloans a,$db.transectionsloan b where a.loanno=$loanno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and a.targetdate>'$pdate3mnth' order by paidby desc limit 1;"));

								if ($sec3sub1) {

									$set = [];
									$set['orgmemno'] = $sec3sub1[0]->orgmemno;
									$set['disbdate'] = $sec3sub1[0]->disbdate;
									$set['loanslno'] = $sec3sub1[0]->loanslno;
									$set['productname'] = $sec3sub1[0]->productname;
									$set['colcdate'] = $instmisdate;
									$set['paidby'] = $sec3sub1[0]->paidby;
									$set['principalamt'] = $sec3sub1[0]->principalamt;
									if ($sec3sub1[0]->paidby == null or $sec3sub1[0]->paidby == '') {
										// dd($sec3sub1);
									}
									$dataset[] = $set;
								}
							}
						}
					}
				}
				// dd(count($dataset));
				if (count($dataset) >= $respondentForEveryBranch) {
					// $dataset = array_slice($dataset, 0, $respondentForEveryBranch);
					foreach ($dataset as $row) {
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub1;
						$orgmemno = $row['orgmemno'];
						$disbdate = $row['disbdate'];
						$loanslno = $row['loanslno'];
						$productname = $row['productname'];
						$instmisdate = $row['colcdate'];
						$paidby = $row['paidby'];
						$loansize = $row['principalamt'];
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							if ($assignedpo) {
								$cono = $assignedpo->assignedpo;
							}
						}
						// dd($assignedpo);

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();



						if ($checkrespondents->isEmpty()) {
							$section1sub1insert = DB::Table($db . '.respondents')->insert(['sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno, 'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'instmisdate' => $instmisdate, 'loansize' => $loansize, 'paidby' => $paidby, 'membername' => $membername]);
						}
					}
				}
				$countmembers = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub1)->where('branchcode', $branchcode)->count();
				if ($countmembers < $respondentForEveryBranch) {
					// dd('asd');
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate3mnth . ' - 1 month'));
					$totalmonthcount = 30;
					$dataset = [];
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					// $totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans where branchcode='$branchcode' group by disbdate order by disbdate DESC"));
					// dd($totalmonth);
					// if (!empty($totalmonth)) {
					// 	foreach ($totalmonth as $row) {
					// 		$totalmonthcount += 1;
					// 	}
					// }
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$currentborrower = DB::Table($db . '.cloans')->where('branchcode', $branchcode)->where('lnstatus', 1)->get();
							$dataset = array();
							foreach ($currentborrower as $row) {
								$orgmemno = $row->orgmemno;
								$loanno = $row->loanno;
								$filter = DB::select(DB::raw("SELECT b.orgmemno,b.loanno,EXTRACT(MONTH FROM a.targetdate) as mismonth FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and EXTRACT(MONTH FROM a.targetdate)=EXTRACT(MONTH FROM b.colcdate) and a.targetdate > '$pdate1mnth' group by b.orgmemno,b.loanno,a.targetdate"));

								// if ($eventid == '29') {
								// dd($filter);
								// }
								if ($filter) {
									// dd($filter);
									$targets = DB::table($db . '.targets')->where('loanno', $loanno)->where('orgmemno', $orgmemno)->get();
									if (!$targets->isEmpty()) {
										$loanno = $filter[0]->loanno;
										$instmisdate = $filter[0]->mismonth;
										$sec3sub1 = DB::select(DB::raw("SELECT * FROM $db.cloans a,$db.transectionsloan b where a.loanno=$loanno and a.branchcode='$branchcode' and a.branchcode=b.branchcode and a.loanno=b.loanno and a.targetdate>'$pdate1mnth' order by paidby desc limit 1;"));

										if ($sec3sub1) {

											$set = [];
											$set['orgmemno'] = $sec3sub1[0]->orgmemno;
											$set['disbdate'] = $sec3sub1[0]->disbdate;
											$set['loanslno'] = $sec3sub1[0]->loanslno;
											$set['productname'] = $sec3sub1[0]->productname;
											$set['colcdate'] = $instmisdate;
											$set['paidby'] = $sec3sub1[0]->paidby;
											$set['principalamt'] = $sec3sub1[0]->principalamt;
											if ($sec3sub1[0]->paidby == null or $sec3sub1[0]->paidby == '') {
												// dd($sec3sub1);
											}
											$dataset[] = $set;
										}
									}
								}
							}
							// dd($dataset);
							if (count($dataset) > 0) {
								// $dataset = array_slice($dataset, 0, $respondentForEveryBranch);
								foreach ($dataset as $row) {
									$membername = '';
									$sec_no = $j;
									$sub_id = $sub1;
									$orgmemno = $row['orgmemno'];
									$disbdate = $row['disbdate'];
									$loanslno = $row['loanslno'];
									$productname = $row['productname'];
									$instmisdate = $row['colcdate'];
									$paidby = $row['paidby'];
									$loansize = $row['principalamt'];
									$eventid = $eventid;
									$area_id = $area_id;

									$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($member) {
										$membername = $member->membername;
									}

									$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($assignedpo) {
										if ($assignedpo) {
											$cono = $assignedpo->assignedpo;
										}
									}
									// dd($assignedpo);

									$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();



									if ($checkrespondents->isEmpty()) {
										$section1sub1insert = DB::Table($db . '.respondents')->insert(['sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno, 'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'instmisdate' => $instmisdate, 'loansize' => $loansize, 'paidby' => $paidby, 'membername' => $membername]);
									}
								}
								$member = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
								// $member = $countdataset;
							}
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
							// echo $pdate1mnth . "/";
						} else {
							break;
						}
					}
				}
			}
		}
		if ($sub2 == '2') {
			$membername = '';
			$respondentForEveryBranch = round(32 / $branchcount);
			$cont = 0;
			foreach ($branch_array as $branchcode) {
				if ($cont == '1') {
					continue;
				}
				$dataset = array();
				$dataset1 = array();
				$dataset2 = array();
				$insertCount = 1;
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$halfrespondentForEveryBranch = round($respondentForEveryBranch / 2);
				// dd('asd');
				// $section3sub2= DB::Table($db.'.cloans')->where('loanslno',1)->where('branchcode',$branchcode)->where('disbdate','>=',$pdate5mnth)->where('disbdate','<=',$pdate)->get();
				//$section3sub2 = DB::select(DB::raw("SELECT distinct b.orgmemno, a.orgmemno,a.loanslno,b.loanno as currentloan,a.loanno as closedloan,b.disbdate,b.principalamt as cloansize,b.principalamt,c.paidby,a.instlpassed,a.instlamt, c.colcdate FROM $db.closedloan a, $db.cloans b,$db.transectionsloan c, $db.refinanaceloan d where a.orgmemno=b.orgmemno and a.closeddate>='$pdate5mnth' and b.lnstatus=1 and b.branchcode='$branchcode' and c.loanno=a.loanno and c.paidby=1 order by c.colcdate desc;"));
				// $section3sub2 = DB::select(DB::raw("SELECT distinct b.orgmemno, a.orgmemno,a.loanslno,b.loanno as currentloan,a.loanno as closedloan,b.disbdate,b.principalamt as cloansize,a.principalamt,a.instlpassed,a.instlamt, (select max(colcdate) as colcdate from $db.transectionsloan c where c.loanno=a.loanno),e.paidby FROM $db.closedloan a, $db.cloans b,$db.transectionsloan e, $db.refinanaceloan d where a.orgmemno=b.orgmemno and a.closeddate>='$pdate5mnth' and b.lnstatus=1 and b.branchcode='$branchcode' and e.loanno=a.loanno and e.paidby=1;"));
				// $section3sub2 = DB::select(DB::raw("SELECT distinct b.orgmemno, a.orgmemno,a.loanslno,b.loanno as currentloan,a.loanno as closedloan,b.disbdate,b.principalamt as cloansize,a.principalamt,a.instlpassed,a.instlamt FROM $db.closedloan a, $db.cloans b, $db.refinanaceloan d where a.orgmemno=b.orgmemno and a.closeddate>='$pdate5mnth' and b.lnstatus=1 and b.branchcode='$branchcode';"));
				$section3sub2 = DB::select(DB::raw("SELECT rs.*, tr.*, ((tr.amt / rs.cinstlamt) + Case When tr.amt % rs.cinstlamt > 0 then 1 else 0 end) as mfact FROM (SELECT l.*, c.loanno as cloanno, c.disbdate as cdisbdate, Cast(c.instlamt as Integer) as cinstlamt,c.principalamt as cprincipalamt, c.instlpassed as cinstlpassed,c.eventid as ceventid, datediff('d', c.closeddate, l.disbdate) as diff, ROW_NUMBER() over (PARTITION BY l.branchcode, l.orgmemno ORDER BY l.branchcode, l.orgmemno, c.closeddate desc) as RN  FROM progoti_snapshot.cloans l inner join progoti_snapshot.closedloan c on l.branchcode = c.branchcode and l.orgmemno=c.orgmemno and l.eventid=c.eventid where  l.lnstatus=1 and l.disbdate >= c.closeddate and datediff('d', c.closeddate, l.disbdate) <= 150 and l.loanno not in (select r.loanno from progoti_snapshot.refinanaceloan r) and l.eventid='$eventid' ) rs, (SELECT t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate, sum(cast(t1.tranamount as Integer)) as amt, count(t1.*) FROM progoti_snapshot.transectionsloan t1 left outer join progoti_snapshot.transectionsloan t2 ON (t1.branchcode= t2.branchcode and t1.orgmemno=t2.orgmemno and t1.loanno=t2.loanno AND t1.colcdate < t2.colcdate) where  t1.eventid='$eventid' and t2.loanno is null group by t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate) tr where  rs.rn=1 and rs.branchcode=tr.branchcode and rs.orgmemno=tr.orgmemno and rs.cloanno=tr.loanno order by tr.amt/rs.cinstlamt desc"));
				//dd("Huda" . $section3sub2);
				if (!empty($section3sub2)) {
					foreach ($section3sub2 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->cloanno;
						$cnt = $row->mfact;
						if ($cnt >= 3) {
							$dataset1[] = $row;
						} else if ($cnt < 3) {
							$dataset2[] = $row;
						}
					}
					$dataset = array_merge($dataset1, $dataset2);
					$countdataset = count($dataset);

					foreach ($dataset as $row) {
						// dd($row);
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub2;
						$branchcode = $branchcode;
						$orgmemno = $row->orgmemno;
						$disbdate = $row->cdisbdate;
						$loansize = $row->cprincipalamt;
						$loanslno = $row->cinstlpassed;
						$colcdate = $row->colcdate;
						$paidby = $row->paidby;
						$eventid = $eventid;
						$area_id = $area_id;
						$installmentpaid = $row->mfact;
						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						// $checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						//for uniqe case removing disdate from dublication check 
						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->get();

						if ($checkrespondents->isEmpty()) {
							DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'loanslno' => $installmentpaid, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'lstclslnpdate' => $colcdate, 'loansize' => $loansize, 'paidby' => $paidby, 'membername' => $membername
							]);
							$insertCount++;
						}
					}
				}
				$cont++;
			}
		}
		if ($sub3 == '3') {
			$membername = '';
			$respondentForEveryBranch = round(26 / $branchcount);

			foreach ($branch_array as $branchcode) {
				$dataset = array();
				$dataset1 = array();
				$dataset2 = array();
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$halfrespondentForEveryBranch = round($respondentForEveryBranch / 2);
				// $section3sub2= DB::Table($db.'.cloans')->where('loanslno',1)->where('branchcode',$branchcode)->where('disbdate','>=',$pdate5mnth)->where('disbdate','<=',$pdate)->get();
				$currentborrower = DB::Table($db . '.cloans')->where('lnstatus', 1)->where('branchcode', $branchcode)->where('disbdate', '>', $pdate5mnth)->get();
				foreach ($currentborrower as $row) {
					$orgmemno = $row->orgmemno;
					$loanno = $row->loanno;
					$disbdate = $row->disbdate;
					$firstinstdate = date('Y-m-d', strtotime($disbdate . '1 month')); //first installment date
					$filter1 = DB::select(DB::raw("SELECT a.principalamt,a.disbdate,b.orgmemno,b.loanno,b.colcdate,b.paidby FROM $db.cloans a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.loanno=b.loanno and a.disbdate > '$pdate5mnth' and b.colcdate < '$firstinstdate' group by a.disbdate,a.principalamt,b.orgmemno,b.loanno,b.colcdate,b.paidby order by paidby desc"));
					if (array_key_exists(0, $filter1)) {
						$demo = [];
						$demo['orgmemno'] = $filter1[0]->orgmemno;
						$demo['disbdate'] = $filter1[0]->disbdate;
						$demo['loansize'] = $filter1[0]->principalamt;
						$demo['colcdate'] = $filter1[0]->colcdate;
						$demo['paidby'] = $filter1[0]->paidby;
						$demo['fstinstdate'] = $firstinstdate;
						$dataset1[] = $demo;
					}
					$filter2 = DB::select(DB::raw("SELECT a.principalamt,a.disbdate,b.orgmemno,b.loanno,b.colcdate,a.instlamtloan,b.tranamount,b.paidby FROM $db.cloans a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.loanno=b.loanno and a.instlamtloan < cast(b.tranamount as int) group by a.disbdate,a.principalamt,b.orgmemno,b.loanno,b.colcdate,b.paidby,a.instlamtloan,b.tranamount order by paidby desc"));
					// dd($filter2);
					if (array_key_exists(0, $filter2)) {
						$demo = [];
						$demo['orgmemno'] = $filter2[0]->orgmemno;
						$demo['disbdate'] = $filter2[0]->disbdate;
						$demo['loansize'] = $filter2[0]->principalamt;
						$demo['colcdate'] = $filter2[0]->colcdate;
						$demo['paidby'] = $filter2[0]->paidby;
						$demo['fstinstdate'] = $firstinstdate;
						$dataset2[] = $demo;
					}
				}
				// if($dataset1){
				// 	$dataset1 = array_slice($dataset1, 0, $halfrespondentForEveryBranch);
				// }
				// if($dataset2){
				// 	$dataset2 = array_slice($dataset1, 0, $halfrespondentForEveryBranch);
				// }
				$dataset = array_merge($dataset1, $dataset2);
				// dd($dataset);
				$countdataset = count($dataset);

				if ($countdataset >= $respondentForEveryBranch) {

					foreach ($dataset as $row) {
						$tranamount = 0;
						// if(property_exists($row[0], "tranamount")){

						// 	$instlamtloan=$row[0]->instlamtloan;
						// 	$tranamount=$row[0]->tranamount;
						// 	$colcdate=$row[0]->colcdate;
						// 	$lumsum=$tranamount-$instlamtloan;

						// }
						// dd($row['orgmemno']);
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub3;
						$branchcode = $branchcode;
						$orgmemno = $row['orgmemno'];
						$disbdate = $row['disbdate'];
						$loansize = $row['loansize'];
						$colcdate = $row['colcdate'];
						$paidby = $row['paidby'];
						$fstinstdate = $row['fstinstdate'];
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						if ($checkrespondents->isEmpty()) {
							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'fstinstpdate' => $fstinstdate, 'loansize' => $loansize, 'paidby' => $paidby, 'membername' => $membername, 'lumpsumamnt' => $tranamount, 'lumpsumpaiddate' => $colcdate
							]);
						}
					}
					// $this->get_sub_id_sec_1($db,1,$pdate5mnth,$pdate,$j,$branchcode,$area_id,$eventid);
				}
				$countmembers = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub3)->where('branchcode', $branchcode)->count();
				if ($countmembers < $respondentForEveryBranch) {
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate5mnth . ' - 1 month'));
					$totalmonthcount = 30;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					// $totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans where branchcode='$branchcode' group by disbdate order by disbdate DESC"));
					// dd($totalmonth);
					// if (!empty($totalmonth)) {
					// 	foreach ($totalmonth as $row) {
					// 		$totalmonthcount += 1;
					// 	}
					// }
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$currentborrower = DB::Table($db . '.cloans')->where('lnstatus', 1)->where('branchcode', $branchcode)->where('disbdate', '>', $pdate1mnth)->get();
							foreach ($currentborrower as $row) {
								$orgmemno = $row->orgmemno;
								$loanno = $row->loanno;
								$disbdate = $row->disbdate;
								$firstinstdate = date('Y-m-d', strtotime($disbdate . '1 month')); //first installment date
								$filter1 = DB::select(DB::raw("SELECT a.principalamt,a.disbdate,b.orgmemno,b.loanno,b.colcdate,b.paidby FROM $db.cloans a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.loanno=b.loanno and a.disbdate > '$pdate5mnth' and b.colcdate < '$firstinstdate' group by a.disbdate,b.orgmemno,b.loanno,b.colcdate,b.paidby,a.principalamt order by paidby desc"));
								if (array_key_exists(0, $filter1)) {
									$demo = [];
									$demo['orgmemno'] = $filter1[0]->orgmemno;
									$demo['disbdate'] = $filter1[0]->disbdate;
									$demo['loansize'] = $filter1[0]->principalamt;
									$demo['colcdate'] = $filter1[0]->colcdate;
									$demo['paidby'] = $filter1[0]->paidby;
									$demo['fstinstdate'] = $firstinstdate;
									$dataset1[] = $demo;
								}
								$filter2 = DB::select(DB::raw("SELECT a.principalamt,a.disbdate,b.orgmemno,b.loanno,b.colcdate,a.instlamtloan,b.tranamount,b.paidby FROM $db.cloans a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno=$loanno and a.orgmemno=b.orgmemno and a.loanno=b.loanno and a.instlamtloan < cast(b.tranamount as int) group by a.principalamt,a.disbdate,b.orgmemno,b.loanno,b.colcdate,b.paidby,a.instlamtloan,b.tranamount order by paidby desc"));
								// dd($filter2);
								if (array_key_exists(0, $filter2)) {
									$demo = [];
									$demo['orgmemno'] = $filter2[0]->orgmemno;
									$demo['disbdate'] = $filter2[0]->disbdate;
									$demo['loansize'] = $filter2[0]->principalamt;
									$demo['colcdate'] = $filter2[0]->colcdate;
									$demo['paidby'] = $filter2[0]->paidby;
									$demo['fstinstdate'] = $firstinstdate;
									$dataset2[] = $demo;
								}
							}
							if ($dataset1) {
								$dataset1 = array_slice($dataset1, 0, $halfrespondentForEveryBranch);
							}
							if ($dataset2) {
								$dataset2 = array_slice($dataset1, 0, $halfrespondentForEveryBranch);
							}
							$dataset = array_merge($dataset1, $dataset2);
							$countdataset = count($dataset);
							if ($countdataset >= $respondentForEveryBranch) {
								foreach ($dataset as $row) {
									// dd($row);
									$tranamount = 0;
									if (array_key_exists("tranamount", $row)) {
										$instlamtloan = $row->instlamtloan;
										$tranamount = $row->tranamount;
										$lumsum = $tranamount - $instlamtloan;
									}
									$fstinstdate = null;
									$membername = '';
									$sec_no = $j;
									$sub_id = $sub3;
									$branchcode = $branchcode;
									$orgmemno = $row['orgmemno'];
									$disbdate = $row['disbdate'];
									$loansize = $row['loansize'];
									$colcdate = $row['colcdate'];
									$paidby = $row['paidby'];
									$fstinstdate = $row['fstinstdate'];
									$eventid = $eventid;
									$area_id = $area_id;

									$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($member) {
										$membername = $member->membername;
									}

									$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($assignedpo) {
										$cono = $assignedpo->assignedpo;
									}

									$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

									if ($checkrespondents->isEmpty()) {
										$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->count();
										if ($countsectiondata < 26) {
											$section1sub1insert = DB::Table($db . '.respondents')->insert([
												'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
												'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'fstinstpdate' => $fstinstdate, 'loansize' => $loansize, 'paidby' => $paidby, 'membername' => $membername, 'lumpsumamnt' => $tranamount
											]);
										}
									}
								}

								$member = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
								// $member = $countdataset;
							}
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
						} else {
							break;
						}
					}
				}
			}
		}
		if ($sub4 == '4') {
			$membername = '';
			$respondentForEveryBranch = round(30 / $branchcount);

			foreach ($branch_array as $branchcode) {
				$outstandingamnt_ary_high = [];
				$outstandingamnt_ary_low = [];
				$dataset = [];
				$dataset1 = [];
				$dataset2 = [];
				$dataset3 = [];
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$quaterrespondentForEveryBranch = round($respondentForEveryBranch / 3);
				// $section3sub2= DB::Table($db.'.cloans')->where('loanslno',1)->where('branchcode',$branchcode)->where('disbdate','>=',$pdate5mnth)->where('disbdate','<=',$pdate)->get();
				$filter1 = DB::Table($db . '.cloans')->where('lnstatus', 3)->take($respondentForEveryBranch)->where('branchcode', $branchcode)->where('eventid', $eventid)->get();
				$count = count($filter1);
				if ($count <= $respondentForEveryBranch) {
					foreach ($filter1 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$disbdate = $row->disbdate;
						$lnstatus = $row->lnstatus;
						$principalamt = $row->principalamt;
						$totalreld = $row->totalreld;
						// $outstandingamnt = round($principalamt - $totalreld);
						$outstandingamnt = $row->totaldue;
						if ($outstandingamnt > 0) {
							$data['outstandingamnt'] = $outstandingamnt;
							$data['orgmemno'] = $orgmemno;
							$data['loanno'] = $loanno;
							$data['disbdate'] = $disbdate;
							$data['lnstatus'] = $lnstatus;
							$data['principalamt'] = $principalamt;

							$dataset1[] = $data;
						}
					}
				} else {
					foreach ($filter1 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$irraguler = DB::select(DB::raw("SELECT * FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno='$loanno' and a.orgmemno=b.orgmemno and a.loanno=b.loanno;"));
						if (!$irraguler) {
							$orgmemno = $row->orgmemno;
							$loanno = $row->loanno;
							$disbdate = $row->disbdate;
							$lnstatus = $row->lnstatus;
							$principalamt = $row->principalamt;
							$totalreld = $row->totalreld;
							// $outstandingamnt = round($principalamt - $totalreld);
							$outstandingamnt = $row->totaldue;
							if ($outstandingamnt > 0) {
								$data['outstandingamnt'] = $outstandingamnt;
								$data['orgmemno'] = $orgmemno;
								$data['loanno'] = $loanno;
								$data['disbdate'] = $disbdate;
								$data['lnstatus'] = $lnstatus;
								$data['principalamt'] = $principalamt;
								$outstandingamnt_ary_high[] = $data;
								$outstandingamnt_ary_low[] = $data;
							}
						}
					}
					$columns = array_column($outstandingamnt_ary_high, 'outstandingamnt');
					array_multisort($columns, SORT_DESC, $outstandingamnt_ary_high);
					$columns = array_column($outstandingamnt_ary_low, 'outstandingamnt');
					array_multisort($columns, SORT_ASC, $outstandingamnt_ary_low);

					$half = round($respondentForEveryBranch / 2);
					$lowestfirst = array_slice($outstandingamnt_ary_low, 0, $half);
					$highestfirst = array_slice($outstandingamnt_ary_high, 0, $half);
					$dataset1 = array_merge($lowestfirst, $highestfirst);
				}

				$filter2 = DB::Table($db . '.cloans')->where('lnstatus', 4)->take($respondentForEveryBranch)->where('branchcode', $branchcode)->where('eventid', $eventid)->where('branchcode', $branchcode)->get();
				$count = count($filter2);
				if ($count <= $respondentForEveryBranch) {
					foreach ($filter2 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$disbdate = $row->disbdate;
						$lnstatus = $row->lnstatus;
						$principalamt = $row->principalamt;
						$totalreld = $row->totalreld;
						// $outstandingamnt = round($principalamt - $totalreld);
						$outstandingamnt = $row->totaldue;
						if ($outstandingamnt > 0) {
							$data['outstandingamnt'] = $outstandingamnt;
							$data['orgmemno'] = $orgmemno;
							$data['loanno'] = $loanno;
							$data['disbdate'] = $disbdate;
							$data['lnstatus'] = $lnstatus;
							$data['principalamt'] = $principalamt;
						}
						$dataset2[] = $data;
					}
				} else {
					foreach ($filter2 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$irraguler = DB::select(DB::raw("SELECT * FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno='$loanno' and a.orgmemno=b.orgmemno and a.loanno=b.loanno;"));
						if (!$irraguler) {
							$orgmemno = $row->orgmemno;
							$loanno = $row->loanno;
							$disbdate = $row->disbdate;
							$lnstatus = $row->lnstatus;
							$principalamt = $row->principalamt;
							$totalreld = $row->totalreld;
							// $outstandingamnt = round($principalamt - $totalreld);
							$outstandingamnt = $row->totaldue;
							if ($outstandingamnt > 0) {
								$data['outstandingamnt'] = $outstandingamnt;
								$data['orgmemno'] = $orgmemno;
								$data['loanno'] = $loanno;
								$data['disbdate'] = $disbdate;
								$data['lnstatus'] = $lnstatus;
								$data['principalamt'] = $principalamt;
								$outstandingamnt_ary_high[] = $data;
								$outstandingamnt_ary_low[] = $data;
							}
						}
					}
					$columns = array_column($outstandingamnt_ary_high, 'outstandingamnt');
					array_multisort($columns, SORT_DESC, $outstandingamnt_ary_high);
					$columns = array_column($outstandingamnt_ary_low, 'outstandingamnt');
					array_multisort($columns, SORT_ASC, $outstandingamnt_ary_low);

					$half = round($respondentForEveryBranch / 2);
					$lowestfirst = array_slice($outstandingamnt_ary_low, 0, $half);
					$highestfirst = array_slice($outstandingamnt_ary_high, 0, $half);
					$dataset2 = array_merge($lowestfirst, $highestfirst);
				}
				$filter3 = DB::Table($db . '.cloans')->where('lnstatus', 5)->take($respondentForEveryBranch)->where('branchcode', $branchcode)->where('branchcode', $branchcode)->where('eventid', $eventid)->get();
				$count = count($filter3);
				if ($count <= $respondentForEveryBranch) {
					foreach ($filter3 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$disbdate = $row->disbdate;
						$lnstatus = $row->lnstatus;
						$principalamt = $row->principalamt;
						$totalreld = $row->totalreld;
						// $outstandingamnt = round($principalamt - $totalreld);
						$outstandingamnt = $row->totaldue;
						if ($outstandingamnt > 0) {
							$data['outstandingamnt'] = $outstandingamnt;
							$data['orgmemno'] = $orgmemno;
							$data['loanno'] = $loanno;
							$data['disbdate'] = $disbdate;
							$data['lnstatus'] = $lnstatus;
							$data['principalamt'] = $principalamt;
						}
						$dataset3[] = $data;
					}
				} else {
					foreach ($filter3 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$irraguler = DB::select(DB::raw("SELECT * FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno='$loanno' and a.orgmemno=b.orgmemno and a.loanno=b.loanno;"));
						if (!$irraguler) {
							$orgmemno = $row->orgmemno;
							$loanno = $row->loanno;
							$disbdate = $row->disbdate;
							$lnstatus = $row->lnstatus;
							$principalamt = $row->principalamt;
							$totalreld = $row->totalreld;
							// $outstandingamnt = round($principalamt - $totalreld);
							$outstandingamnt = $row->totaldue;
							if ($outstandingamnt > 0) {
								$data['outstandingamnt'] = $outstandingamnt;
								$data['orgmemno'] = $orgmemno;
								$data['loanno'] = $loanno;
								$data['disbdate'] = $disbdate;
								$data['lnstatus'] = $lnstatus;
								$data['principalamt'] = $principalamt;
								$outstandingamnt_ary_high[] = $data;
								$outstandingamnt_ary_low[] = $data;
							}
						}
					}
					$columns = array_column($outstandingamnt_ary_high, 'outstandingamnt');
					array_multisort($columns, SORT_DESC, $outstandingamnt_ary_high);
					$columns = array_column($outstandingamnt_ary_low, 'outstandingamnt');
					array_multisort($columns, SORT_ASC, $outstandingamnt_ary_low);

					$half = round($respondentForEveryBranch / 2);
					$lowestfirst = array_slice($outstandingamnt_ary_low, 0, $half);
					$highestfirst = array_slice($outstandingamnt_ary_high, 0, $half);
					$dataset3 = array_merge($lowestfirst, $highestfirst);
				}
				// $dataset = array_merge($dataset1, $dataset2, $dataset3);

				$dataset = array_merge($dataset1, $dataset2, $dataset3);
				// dd($dataset);
				if ($dataset) {
					foreach ($dataset as $row) {
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub4;
						$branchcode = $branchcode;
						$eventid = $eventid;
						$area_id = $area_id;
						$outstandingamnt = $row['outstandingamnt'];
						$orgmemno = $row['orgmemno'];
						$loanno = $row['loanno'];
						$disbdate = $row['disbdate'];
						$lnstatus = $row['lnstatus'];
						$principalamt = $row['principalamt'];

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
						$asd[] = $checkrespondents;

						if ($checkrespondents->isEmpty()) {

							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'loansize' => $principalamt, 'lnstatus' => $lnstatus, 'membername' => $membername, 'osamnt' => $outstandingamnt
							]);
						}
					}
					// dd($asd);
				}
			}
		}
		if ($sub5 == '5') {
			$membername = '';
			$respondentForEveryBranch = round(30 / $branchcount);

			foreach ($branch_array as $branchcode) {
				$outstandingamnt_ary_high = [];
				$outstandingamnt_ary_low = [];
				$dataset1 = [];
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				// $section3sub2= DB::Table($db.'.cloans')->where('loanslno',1)->where('branchcode',$branchcode)->where('disbdate','>=',$pdate5mnth)->where('disbdate','<=',$pdate)->get();
				$filter1 = DB::Table($db . '.cloans')->where('lnstatus', 6)->where('branchcode', $branchcode)->get();
				// dd($filter1);
				// $count=count($filter1);
				if ($filter1) {
					foreach ($filter1 as $row) {
						$orgmemno = $row->orgmemno;
						$loanno = $row->loanno;
						$irraguler = DB::select(DB::raw("SELECT * FROM $db.targets a,$db.transectionsloan b where a.orgmemno='$orgmemno' and a.loanno='$loanno' and a.orgmemno=b.orgmemno and a.loanno=b.loanno;"));
						if (!$irraguler) {
							$orgmemno = $row->orgmemno;
							$loanno = $row->loanno;
							$disbdate = $row->disbdate;
							$lnstatus = $row->lnstatus;
							$principalamt = $row->principalamt;
							$totalreld = $row->totalreld;
							$outstandingamnt = $row->overdue;
							if ($outstandingamnt > 0) {
								$data['outstandingamnt'] = $outstandingamnt;
								$data['orgmemno'] = $orgmemno;
								$data['loanno'] = $loanno;
								$data['disbdate'] = $disbdate;
								$data['lnstatus'] = $lnstatus;
								$data['principalamt'] = $principalamt;
								$outstandingamnt_ary_high[] = $data;
								$outstandingamnt_ary_low[] = $data;
							}
						}
					}
					$columns = array_column($outstandingamnt_ary_high, 'outstandingamnt');
					array_multisort($columns, SORT_DESC, $outstandingamnt_ary_high);
					$columns = array_column($outstandingamnt_ary_low, 'outstandingamnt');
					array_multisort($columns, SORT_ASC, $outstandingamnt_ary_low);

					$half = round($respondentForEveryBranch / 2);
					$lowestfirst = array_slice($outstandingamnt_ary_low, 0, $half);
					$highestfirst = array_slice($outstandingamnt_ary_high, 0, $half);
					$dataset1 = array_merge($lowestfirst, $highestfirst);
				}
				// $dataset = array_merge($dataset1, $dataset2, $dataset3);

				$dataset = $dataset1;
				// dd($dataset);
				if ($dataset) {
					foreach ($dataset as $row) {
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub5;
						$branchcode = $branchcode;
						$eventid = $eventid;
						$area_id = $area_id;
						$outstandingamnt = $row['outstandingamnt'];
						$orgmemno = $row['orgmemno'];
						$loanno = $row['loanno'];
						$disbdate = $row['disbdate'];
						$lnstatus = $row['lnstatus'];
						$principalamt = $row['principalamt'];

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
						$asd[] = $checkrespondents;
						// dd($checkrespondents);

						if ($checkrespondents->isEmpty()) {
							// dd('asd');

							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'loansize' => $principalamt, 'lnstatus' => $lnstatus, 'membername' => $membername, 'osamnt' => $outstandingamnt
							]);
							// dd($section1sub1insert);
						}
					}
					// dd($branch_array);
				}
			}
			foreach ($branch_array as $branchcode) {
				$sub_id = $sub5;
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$checkdata = DB::Table($db . '.respondents')->where('branchcode', $branchcode)->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->count();
				$dataset1 = [];
				if ($checkdata < $respondentForEveryBranch) {
					$filter1 = DB::Table($db . '.cloans')->where('lnstatus', 6)->where('branchcode', $branchcode)->get();
					if ($filter1) {
						foreach ($filter1 as $row) {
							$orgmemno = $row->orgmemno;
							$loanno = $row->loanno;
							$disbdate = $row->disbdate;
							$lnstatus = $row->lnstatus;
							$principalamt = $row->principalamt;
							$totalreld = $row->totalreld;
							$outstandingamnt = $row->overdue;
							$data['outstandingamnt'] = $outstandingamnt;
							$data['orgmemno'] = $orgmemno;
							$data['loanno'] = $loanno;
							$data['disbdate'] = $disbdate;
							$data['lnstatus'] = $lnstatus;
							$data['principalamt'] = $principalamt;
							$dataset1[] = $data;
						}
						// dd($dataset1);
						if ($dataset1) {
							foreach ($dataset1 as $row) {
								$membername = '';
								$sec_no = $j;
								$sub_id = $sub5;
								$branchcode = $branchcode;
								$eventid = $eventid;
								$area_id = $area_id;
								$outstandingamnt = $row['outstandingamnt'];
								$orgmemno = $row['orgmemno'];
								$loanno = $row['loanno'];
								$disbdate = $row['disbdate'];
								$lnstatus = $row['lnstatus'];
								$principalamt = $row['principalamt'];

								$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
								if ($member) {
									$membername = $member->membername;
								}

								$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
								if ($assignedpo) {
									$cono = $assignedpo->assignedpo;
								}

								$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
								$asd[] = $checkrespondents;
								// dd($checkrespondents);

								if ($checkrespondents->isEmpty()) {
									// dd('asd');

									DB::Table($db . '.respondents')->insert([
										'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
										'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'loansize' => $principalamt, 'lnstatus' => $lnstatus, 'membername' => $membername, 'osamnt' => $outstandingamnt
									]);

									// dd($section1sub1insert);
								}
							}
							// dd($branch_array);
						}
					}
				}
			}
		}
		if ($sub6 == '6') {
			$membername = '';
			$respondentForEveryBranch = intval(round(20 / $branchcount));
			// dd($respondentForEveryBranch);
			foreach ($branch_array as $branchcode) {
				$dataset = array();
				$dataset1 = array();
				$dataset2 = array();
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);

				$section3sub6 = DB::select(DB::raw("SELECT a.orgmemno,a.loanslno,b.disbdate,b.principalamt,c.colcdate,c.tranamount as cloansize,a.principalamt,c.paidby,a.instlpassed,a.instlamt,c.colcdate FROM $db.closedloan a, $db.cloans b,$db.transectionsloan c where a.orgmemno=b.orgmemno and a.closeddate>='$pdate2mnth' and b.lnstatus=1 and c.loanno=a.loanno and b.branchcode='$branchcode' and b.branchcode=c.branchcode and c.trxtype=6 and c.paidby=2;"));

				if (!$section3sub6) {
					$section3sub6 = DB::select(DB::raw("SELECT a.orgmemno,a.loanslno,b.disbdate,b.principalamt,c.colcdate,c.tranamount as cloansize,a.principalamt,c.paidby,c.colcdate FROM $db.closedloan a, $db.cloans b,$db.transectionsloan c  where a.orgmemno=b.orgmemno and b.branchcode=c.branchcode and b.branchcode='$branchcode' and a.closeddate>='$pdate2mnth' and b.lnstatus=1 and c.loanno=a.loanno and c.trxtype=6;"));
				}
				// dd($section3sub6);
				$countdataset = count($section3sub6);
				if ($countdataset >= $respondentForEveryBranch) {
					foreach ($section3sub6 as $row) {
						// dd($row);
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub6;
						$branchcode = $branchcode;
						$orgmemno = $row->orgmemno;
						$disbdate = $row->disbdate;
						$loansize = $row->principalamt;
						$tranamount = $row->cloansize;
						$colcdate = $row->colcdate;
						$paidby = $row->paidby;
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						if ($checkrespondents->isEmpty()) {

							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'lstinstpamnt' => $tranamount, 'area_id' => $area_id, 'lstclslnpdate' => $colcdate, 'loansize' => $loansize, 'paidby' => $paidby, 'cono' => $cono, 'membername' => $membername
							]);
						}
					}
					// $this->get_sub_id_sec_1($db,1,$pdate5mnth,$pdate,$j,$branchcode,$area_id,$eventid);
				}

				$countmembers = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub6)->where('branchcode', $branchcode)->count();
				if ($countmembers < $respondentForEveryBranch) {
					$member = 0;
					$pdate1mnth = date('Y-m-d', strtotime($pdate2mnth . ' - 1 month'));
					$totalmonthcount = 30;
					//$totalmonth= DB::Table($db.'.cloans')->where('loanslno',1)->where('disbdate','>=','2020-08-15')->where('disbdate','<=','2020-10-15')->groupBy('disbdate','sl')->get();
					// $totalmonth = DB::select(DB::raw("SELECT disbdate FROM $db.cloans where branchcode='$branchcode' group by disbdate order by disbdate DESC"));
					// dd($respondentForEveryBranch);
					// if (!empty($totalmonth)) {
					// 	foreach ($totalmonth as $row) {
					// 		$totalmonthcount += 1;
					// 	}
					// }
					for ($i = 1; $i <= $totalmonthcount; $i++) {
						if ($member < $respondentForEveryBranch) {
							$section3sub6 = DB::select(DB::raw("SELECT a.orgmemno,a.loanslno,b.disbdate,b.principalamt,c.colcdate,c.tranamount as cloansize,a.principalamt,c.paidby,a.instlpassed,a.instlamt,c.colcdate FROM $db.closedloan a, $db.cloans b,$db.transectionsloan c where a.orgmemno=b.orgmemno and a.closeddate>='$pdate1mnth' and b.branchcode=c.branchcode and b.branchcode='$branchcode' and b.lnstatus=1 and c.loanno=a.loanno and c.trxtype=6 and c.paidby=2;"));
							if (!$section3sub6) {
								$section3sub6 = DB::select(DB::raw("SELECT a.orgmemno,a.loanslno,b.disbdate,b.principalamt,c.colcdate,c.tranamount as cloansize,a.principalamt,c.paidby,a.instlpassed,a.instlamt,c.colcdate FROM $db.closedloan a, $db.cloans b,$db.transectionsloan c where a.orgmemno=b.orgmemno and a.closeddate>='$pdate1mnth' and b.branchcode=c.branchcode and b.branchcode='$branchcode' and b.lnstatus=1 and c.loanno=a.loanno and c.trxtype=6;"));
							}

							if ($section3sub6) {
								foreach ($section3sub6 as $row) {
									// dd($row);
									$membername = '';
									$sec_no = $j;
									$sub_id = $sub6;
									$branchcode = $branchcode;
									$orgmemno = $row->orgmemno;
									$disbdate = $row->disbdate;
									$loansize = $row->principalamt;
									$tranamount = $row->cloansize;
									$colcdate = $row->colcdate;
									$paidby = $row->paidby;
									$eventid = $eventid;
									$area_id = $area_id;

									$memberaryy = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($memberaryy) {
										$membername = $memberaryy->membername;
									}

									$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($assignedpo) {
										$cono = $assignedpo->assignedpo;
									}

									$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

									if ($checkrespondents->isEmpty()) {
										$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
										if ($countsectiondata < $respondentForEveryBranch) {
											DB::Table($db . '.respondents')->insert([
												'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
												'disbdate' => $disbdate, 'eventid' => $eventid, 'lstinstpamnt' => $tranamount, 'area_id' => $area_id, 'lstclslnpdate' => $colcdate, 'loansize' => $loansize, 'paidby' => $paidby, 'cono' => $cono, 'membername' => $membername
											]);
										}
									}
								}
							}

							$countdataset = count($section3sub6);
							if ($countdataset >= $respondentForEveryBranch) {
								foreach ($section3sub6 as $row) {
									// dd($row);
									$membername = '';
									$sec_no = $j;
									$sub_id = $sub6;
									$branchcode = $branchcode;
									$orgmemno = $row->orgmemno;
									$disbdate = $row->disbdate;
									$loansize = $row->principalamt;
									$tranamount = $row->cloansize;
									$colcdate = $row->colcdate;
									$paidby = $row->paidby;
									$eventid = $eventid;
									$area_id = $area_id;

									$memberaryy = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($memberaryy) {
										$membername = $memberaryy->membername;
									}

									$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
									if ($assignedpo) {
										$cono = $assignedpo->assignedpo;
									}

									$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

									if ($checkrespondents->isEmpty()) {
										$countsectiondata = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
										if ($countsectiondata < $respondentForEveryBranch) {
											$section1sub1insert = DB::Table($db . '.respondents')->insert([
												'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
												'disbdate' => $disbdate, 'eventid' => $eventid, 'lstinstpamnt' => $tranamount, 'area_id' => $area_id, 'lstclslnpdate' => $colcdate, 'loansize' => $loansize, 'paidby' => $paidby, 'cono' => $cono, 'membername' => $membername
											]);
										}
									}
								}

								$member = DB::Table($db . '.respondents')->where('eventid', $eventid)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('branchcode', $branchcode)->count();
								// $member = $countdataset;
							}
							$pdate1mnth = date('Y-m-d', strtotime($pdate1mnth . ' - 1 month'));
							// echo $pdate1mnth;
						} else {
							break;
						}
					}
				}
			}
		}
		if ($sub7 == '7') {

			$respondentForEveryBranch = round(20 / $branchcount);

			foreach ($branch_array as $branchcode) {
				$membername = '';
				$dataset = array();
				$dataset1 = array();
				$dataset2 = array();
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				// $halfrespondentForEveryBranch=round($respondentForEveryBranch/2);
				// $section3sub2= DB::Table($db.'.cloans')->where('loanslno',1)->where('branchcode',$branchcode)->where('disbdate','>=',$pdate5mnth)->where('disbdate','<=',$pdate)->get();
				// $section3sub6= DB::select(DB::raw("SELECT b.orgmemno,b.loanno,c.colcdate FROM $db.closedloan b,$db.transectionsloan c where b.orgmemno=c.orgmemno and b.disbdate > '$pdate2mnth' and b.loanno=c.loanno and c.trxtype=7 and branchcode='$branchcode' group by b.orgmemno,b.loanno,c.colcdate;"));
				$section3sub7 = DB::select(DB::raw("SELECT * FROM $db.closedloan where disbdate > '$pdate2mnth' and area_id='$area_id';"));
				// dd($pdate2mnth);
				if ($section3sub7) {
					foreach ($section3sub7 as $closedloan) {
						$orgmemno = $closedloan->orgmemno;
						$closedloanamount = $closedloan->principalamt;
						$closedloandisbdate = $closedloan->disbdate;
						$orgmemno = $closedloan->orgmemno;
						$loanno = $closedloan->loanno;
						$savingadjustments = DB::select(DB::raw("SELECT * FROM $db.transectionsloan where loanno='$loanno' and orgmemno='$orgmemno' and branchcode='$branchcode' and trxtype = 3 and colcdate=(SELECT max(colcdate) as maxcolcdate FROM $db.transectionsloan where loanno='$loanno' and orgmemno='$orgmemno' and branchcode='$branchcode');"));
						if ($savingadjustments) {
							// dd($savingadjustments);

							$adjustdate = $savingadjustments[0]->colcdate;
							$cashrefund = DB::select(DB::raw("SELECT * FROM $db.transectionsloan where trxtype = 2 and colcfor='S' and orgmemno='$orgmemno' and branchcode='$branchcode';"));
							if ($cashrefund) {
								// dd($cashrefund);
								$refunddate = $cashrefund[0]->colcdate;
								$data = [];
								$data['orgmemno'] = $orgmemno;
								$data['loansize'] = $closedloan->principalamt;
								$data['disbdate'] = $closedloan->disbdate;
								$data['adjustdate'] = $adjustdate;
								$data['refunddate'] = $refunddate;
								$dataset1[] = $data;
							}
						}
					}
				}

				$countdataset = count($dataset1);
				// dd($countdataset);
				if ($countdataset < 20) {
					$take = 20 - $countdataset;

					$smlcashrefund = DB::Table($db . '.transectionsloan')->where('trxtype', 2)->where('colcfor', 'S')->where('branchcode', $branchcode)->where('colcdate', '>', $pdate2mnth)->take($take)->orderByRaw('CAST (tranamount AS INTEGER) desc')->get();
					// dd($pdate2mnth);

					if (!$smlcashrefund->isEmpty()) {
						foreach ($smlcashrefund as $row) {
							// dd($row);
							$data = [];
							$data['orgmemno'] = $row->orgmemno;
							// dd($row->orgmemno);
							$data['refunddate'] = $row->colcdate;
							$dataset2[] = $data;
						}
					}

					$dataset = array_merge($dataset1, $dataset2);

					foreach ($dataset as $row) {
						// dd($row);
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub7;
						$branchcode = $branchcode;
						$orgmemno = $row['orgmemno'];
						$refunddate = $row['refunddate'];
						if (array_key_exists('adjustdate', $row)) {
							$adjustdate = $row['adjustdate'];
							$loansize = $row['loansize'];
							$disbdate = $row['disbdate'];
						}
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->get();

						if ($checkrespondents->isEmpty()) {
							if (array_key_exists('adjustdate', $row)) {
								$section1sub1insert = DB::Table($db . '.respondents')->insert([
									'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
									'disbdate' => $disbdate, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername, 'adjustdate' => $adjustdate, 'refunddate' => $refunddate
								]);
							} else {
								$section1sub1insert = DB::Table($db . '.respondents')->insert(['sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno, 'eventid' => $eventid, 'area_id' => $area_id, 'cono' => $cono, 'cono' => $cono, 'membername' => $membername, 'refunddate' => $refunddate]);
							}
						}
					}
				}
			}
		}
		if ($sub8 == '8') {
			$membername = '';
			$pdate3mnth = date('Y-m-d', strtotime($pdate . ' - 3 month'));
			foreach ($branch_array as $branchcode) {
				$br_lenght = strlen($branchcode);
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$currentborrower = DB::Table($db . '.cloans')->where('lnstatus', 1)->where('branchcode', $branchcode)->where('disbdate', '>', $pdate3mnth)->get();

				if (!$currentborrower->isEmpty()) {
					foreach ($currentborrower as $row) {
						// dd($row);
						$membername = '';
						$sec_no = $j;
						$sub_id = $sub8;
						$branchcode = $branchcode;
						$orgmemno = $row->orgmemno;
						$disbdate = $row->disbdate;
						$loansize = $row->principalamt;
						$lnstatus = $row->lnstatus;
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						if ($checkrespondents->isEmpty()) {
							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'lnstatus' => $lnstatus, 'area_id' => $area_id, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername
							]);
						}
					}
					// $this->get_sub_id_sec_1($db,1,$pdate5mnth,$pdate,$j,$branchcode,$area_id,$eventid);
				}
			}
		}
		if ($sub9 == '9') {
			foreach ($branch_array as $branchcode) {
				$br_lenght = strlen($branchcode);
				$dataset = [];
				$dataset1 = [];
				$dataset2 = [];
				$branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
				$insurfree = DB::Table($db . '.cloans')->where('insurence', 'N')->where('branchcode', $branchcode)->where('disbdate', '>', $pdate2mnth)->get();
				if (!$insurfree->isEmpty()) {
					foreach ($insurfree as $row) {
						// dd($row);
						$data = [];
						$data['orgmemno'] = $row->orgmemno;
						$data['disbdate'] = $row->disbdate;
						$data['principalamt'] = $row->principalamt;
						$data['lnstatus'] = $row->lnstatus;
						$data['insustatus'] = $row->insurence;

						$dataset1[] = $data;
					}
				}
				$countdataset1 = count($dataset1);
				if ($countdataset1 < 20) {
					$take = 20 - $countdataset1;
					$singleinsur = DB::Table($db . '.cloans')->where('insurence', 'S')->where('branchcode', $branchcode)->where('disbdate', '>', $previous1mnth)->take($take)->get();

					foreach ($singleinsur as $row) {
						// dd($row);
						$data = [];
						$data['orgmemno'] = $row->orgmemno;
						$data['disbdate'] = $row->disbdate;
						$data['principalamt'] = $row->principalamt;
						$data['lnstatus'] = $row->lnstatus;
						$data['insustatus'] = $row->insurence;
						$data['premium'] = $row->premium;

						$dataset2[] = $data;
					}
					$dataset = array_merge($dataset1, $dataset2);
					// dd($dataset);

					foreach ($dataset as $row) {
						// dd($row);
						$membername = '';
						$premium = 0;
						$sec_no = $j;
						$sub_id = $sub9;
						$branchcode = $branchcode;
						$orgmemno = $row['orgmemno'];
						$disbdate = $row['disbdate'];
						$loansize = $row['principalamt'];
						$lnstatus = $row['lnstatus'];
						$insustatus = $row['insustatus'];
						if (array_key_exists('premium', $row)) {
							$premium = $row['premium'];
						}
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						if ($checkrespondents->isEmpty()) {

							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'lnstatus' => $lnstatus, 'area_id' => $area_id, 'loansize' => $loansize, 'insustatus' => $insustatus, 'cono' => $cono, 'membername' => $membername, 'premamnt' => $premium
							]);
						}
					}
				} else {
					foreach ($dataset1 as $row) {
						// dd($row);
						$membername = '';
						$premium = 0;
						$sec_no = $j;
						$sub_id = $sub9;
						$branchcode = $branchcode;
						$orgmemno = $row['orgmemno'];
						$disbdate = $row['disbdate'];
						$loansize = $row['principalamt'];
						$lnstatus = $row['lnstatus'];
						$insustatus = $row['insustatus'];
						if (array_key_exists('premium', $row)) {
							$premium = $row['premium'];
						}
						$eventid = $eventid;
						$area_id = $area_id;

						$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($member) {
							$membername = $member->membername;
						}

						$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
						if ($assignedpo) {
							$cono = $assignedpo->assignedpo;
						}

						$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();

						if ($checkrespondents->isEmpty()) {

							$section1sub1insert = DB::Table($db . '.respondents')->insert([
								'sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno,
								'disbdate' => $disbdate, 'eventid' => $eventid, 'lnstatus' => $lnstatus, 'area_id' => $area_id, 'loansize' => $loansize, 'insustatus' => $insustatus, 'cono' => $cono, 'membername' => $membername, 'premamnt' => $premium
							]);
						}
					}
				}
				// $this->get_sub_id_sec_1($db,1,$pdate5mnth,$pdate,$j,$branchcode,$area_id,$eventid);
			}
		}
	}
	public function get_sub_id_sec_1($db, $sub_id, $pdate2mnth, $pdate, $j, $branchcode, $area_id, $eventid)
	{
		$membername = '';
		if ($sub_id == '1') {
			$repondentforsub = 25;
			$section1sub1 = DB::Table($db . '.cloans')->where('loanslno', 1)->where('branchcode', $branchcode)->where('disbdate', '>=', $pdate2mnth)->where('disbdate', '<=', $pdate)->get();
			// dd($pdate);
		} else if ($sub_id == '2' or $sub_id == '5') {
			$section1sub1 = DB::Table($db . '.cloans')->where('loanslno', '>', 1)->where('branchcode', $branchcode)->where('disbdate', '>=', $pdate2mnth)->where('disbdate', '<=', $pdate)->get();
		}

		if ($sub_id == '2') {
			$repondentforsub = 12;
		}
		if ($sub_id == '5') {
			$repondentforsub = 25;
		}
		// if($sub_id !='1' and $sub_id!='2' and $sub_id!='5'){
		// 	dd($sub_id);
		// }
		if (!$section1sub1->isEmpty()) {
			foreach ($section1sub1 as $row) {
				$membername = '';
				$sec_no = $j;
				$sub_id = $sub_id;
				$branchcode = $branchcode;
				$orgmemno = $row->orgmemno;
				$disbdate = $row->disbdate;
				$loanslno = $row->loanslno;
				$loansize = $row->principalamt;
				$productname = $row->productname;
				$eventid = $eventid;
				$area_id = $area_id;

				$member = DB::Table($db . '.memberlists')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
				if ($member) {
					$membername = $member->membername;
				}

				$assignedpo = DB::Table($db . '.memberlists')->select('assignedpo')->where('orgmemno', $orgmemno)->where('branchcode', $branchcode)->first();
				if ($assignedpo) {
					$cono = $assignedpo->assignedpo;
				}

				$checkrespondents  = DB::table($db . '.respondents')->where('eventid', $eventid)->where('area_id', $area_id)->where('branchcode', $branchcode)->where('sec_no', $sec_no)->where('sub_id', $sub_id)->where('orgmemno', $orgmemno)->where('disbdate', $disbdate)->get();
				// dd($branchcode);
				if ($checkrespondents->isEmpty()) {

					$section1sub1insert = DB::Table($db . '.respondents')->insert(['sec_no' => $sec_no, 'sub_id' => $sub_id, 'branchcode' => $branchcode, 'orgmemno' => $orgmemno, 'disbdate' => $disbdate, 'loanslno' => $loanslno, 'productname' => $productname, 'eventid' => $eventid, 'area_id' => $area_id, 'loansize' => $loansize, 'cono' => $cono, 'membername' => $membername]);
				}
			}
		}
	}
}
