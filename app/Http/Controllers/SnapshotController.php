<?php

namespace App\Http\Controllers;

use view;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\DataProcessingController;
use App\Http\Requests;

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 28000);
ini_set('default_socket_timeout', 6000);
// set_time_limit(28000);

use ZipArchive;
use Log;
//use App\Http\Controllers\TestingController_Version;
use Illuminate\Support\Facades\Storage;
use File;

header('Content-Type: text/html; charset=utf-8');
class SnapshotController extends Controller
{
	public function sync(Request $req)
	{
		//dd("Huda");
		$db = 'mnw_progoti';
		$db2 = 'progoti_snapshot';
		$serverstatus = 1;
		$securitykey = '5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae';
		$projectcode = '060';
		$cdate = date('Y-m-d');

		// $cloans = "http://35.229.220.168:9090/scapir/CollectionInfoForMonitoring?BranchCode=0607&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
		// $cloans = "https://erp.brac.net/node/scapir/CollectionInfoForMonitoring?BranchCode=0607&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
		// $cloans = "https://erp.brac.net/node/scapir/ClosedLoanInfo?BranchCode=0607&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
		// $ch = curl_init();  
		// curl_setopt($ch,CURLOPT_URL,$cloans);
		// curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		// curl_setopt($ch,CURLOPT_HEADER, false);     
		// $cloansoutput=curl_exec($ch);
		// curl_close($ch);

		// $collection = json_decode($cloansoutput);
		// dd($collection);

		// dd($cdate);

		$getBranch  = DB::Table($db . '.monitorevents')->where('datestart', $cdate)->where('data_proccess_status', 0)->get();
		// dd($getBranch);

		if ($getBranch->isEmpty()) {
			Log::info("No Event Today" . $cdate);
		} else {
			$_url = '';
			$serverurlmnw = DB::select(DB::raw("select * from $db.server_url where server_status='$serverstatus' and status='1'"));
			//dd($serverurlmnw);
			if (!empty($serverurlmnw)) {
				$_url = $serverurlmnw[0]->url;
				//echo $_url;
				$_url_test = "http://104.199.211.13:9090/scapir/";
				$server_message = $serverurlmnw[0]->server_message;
				$server_downstatus = $serverurlmnw[0]->maintenance_status;
				//$server_message = $serverurlmnw[0]->maintenance_message;
				if ($server_downstatus == '0') {
					DB::beginTransaction();
					try {
						foreach ($getBranch as $row) {
							$event_id = $row->id;
							$BranchCode = $row->branchcode;
							$branch_array = explode(',', $BranchCode);
							// dd($branch_array);
							foreach ($branch_array as $BranchCode) {




								$DateStart1 = $row->datestart;
								//echo $DateStart1;
								// $timechange = strtotime($DateStart1);
								// $DateStart = date('Y-m-d', $timechange);
								// $DateStart = date('Y-m-d', strtotime('last day of previous month'));
								$DateStart = strtotime('last day of previous month', strtotime($DateStart1));
								$DateStart = date('Y-m-d', $DateStart);
								// dd($DateStart);
								//echo $DateStart;
								$dateexpolde = explode('-', $DateStart);
								$year = $dateexpolde[0];
								$month = $dateexpolde[1];
								$date = $dateexpolde[2];
								$previousYears = $year - 2;
								$previousOneYear = $year - 1;
								$presixmonths = $month - 6;
								$prethreemonths = $month - 3;

								$newdate = strtotime('-3 months', strtotime($DateStart1));
								$newdate = date('Y-m', $newdate);
								$ClosedEndtdate = $newdate . "-01";

								// $StartDate = $previousYears . "-" . $presixmonths . "-" . $date;
								$StartDate = strtotime('-2 years -6 month', strtotime($DateStart1));
								$previousTwoYears = strtotime('-2 years', strtotime($DateStart));
								$previousOneMonth = strtotime('-1 month', strtotime($DateStart));
								// dd($);
								$StartDate = date('Y-m-d', $StartDate);
								$previousTwoYears = date('Y-m-d', $previousTwoYears);
								$previousOneMonth = date('Y-m-d', $previousOneMonth);
								// $asdasd = date('Y-m-d', strtotime('-2 years -6 month'));
								// dd($StartDate);
								$previousOneYears = $previousOneYear . "-" . $month . "-" . $date . " 12:00:00";
								// dd($previousOneYears);
								$previousthreeMonths = $year . "-" . $prethreemonths . "-" . $date . "%2012:00:00";
								$getarea  = DB::Table('branch')->where('branch_id', $BranchCode)->get();
								if ($getarea->isEmpty()) {
									$area_id = 0;
								} else {
									$area_id = $getarea[0]->area_id;
								}
								$BranchCode = str_pad($BranchCode, 4, "0", STR_PAD_LEFT);
								// dd($previousOneYears);

								// $tansectionsloan = "http://104.199.211.13:9090/scapir/TransactionsForMonitoring?BranchCode=0607&ProjectCode=060&StartDate=2019-10-03&EndDate=2022-04-03&LastId=2020-03-31&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// //echo $tansectionsloan;
								// //die;
								// $ch = curl_init();
								// curl_setopt($ch, CURLOPT_URL, $tansectionsloan);
								// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, false);
								// $transectionsoutput = curl_exec($ch);
								// // dd($ch);

								// curl_close($ch);
								// $transectionsjsondecode = json_decode($transectionsoutput);
								// dd($transectionsjsondecode);
								Log::info("Transection For Monitoring Start" . $BranchCode);

								$tansectionsloan = Http::get($_url . 'TransactionsForMonitoring', [
									'BranchCode' => $BranchCode,
									'ProjectCode' => $projectcode,
									'StartDate' => $StartDate,
									'EndDate' => $DateStart1,
									'key' => $securitykey
								]);
								// dd($tansectionsloan);
								$transectionsjsondecode = $tansectionsloan->object();

								// return $transectionsjsondecode;										///////////testing api

								// dd($transectionsjsondecode);
								if ($transectionsjsondecode != NULL) {
									$transections = $transectionsjsondecode->data;
									Log::channel('daily')->info('Transection Http: ' . $transectionsjsondecode->code);
									Log::channel('daily')->info('Transection Message: ' . $transectionsjsondecode->message);
									if ($transections) {
										$nextUrlJson = $transectionsjsondecode->nextUrl;
										if ($nextUrlJson != null) {
											$nextUrlArrya = explode('/', $nextUrlJson);
											$nextUrl = $nextUrlArrya[1];
										} else {
											$nextUrl = null;
										}
										// dd($transectionsjsondecode);

										foreach ($transections as $rows) {
											$ProjectCode = $rows->ProjectCode;
											$OrgNo = $rows->OrgNo;
											$OrgMemNo = $rows->OrgMemNo;
											$LoanNo = $rows->LoanNo;
											$Tranamount = $rows->Tranamount;
											$ColcDate = $rows->ColcDate;
											$TrxType = $rows->TrxType;
											$TransNo = $rows->TransNo;
											$ColcFor = $rows->ColcFor;
											$BufferId = $rows->BufferId;
											$UpdatedAt = $rows->UpdatedAt;
											$PaidBy = $rows->PaidBy;
											DB::Table($db2 . '.transectionsloan')->insert(['projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'loanno' => $LoanNo, 'tranamount' => $Tranamount, 'colcdate' => $ColcDate, 'trxtype' => $TrxType, 'transno' => $TransNo, 'colcfor' => $ColcFor, 'bufferid' => $BufferId, 'updatedat' => $UpdatedAt, 'area_id' => $area_id, 'branchcode' => $BranchCode, 'paidby' => $PaidBy, 'eventid' => $event_id]);
										}
										// dd($tansectionsloan);
										$count = 1;
										while ($nextUrl != null) {
											$tansectionsurl = $_url . $nextUrl;
											echo $count . '-';
											// echo $tansectionsurl;
											$tansectionsloan = Http::get($tansectionsurl);
											$transectionsjsondecode = $tansectionsloan->object();
											// dd($transectionsjsondecode);

											if ($transectionsjsondecode != NULL) {
												$transections = $transectionsjsondecode->data;
												$nextUrlJson = $transectionsjsondecode->nextUrl;
												if ($nextUrlJson != null) {
													$nextUrlArrya = explode('/', $nextUrlJson);
													$nextUrl = $nextUrlArrya[1];
												} else {
													$nextUrl = null;
												}
												// dd($transectionsjsondecode);
												$count++;
												if ($transections != NULL) {
													foreach ($transections as $rows) {
														$ProjectCode = $rows->ProjectCode;
														$OrgNo = $rows->OrgNo;
														$OrgMemNo = $rows->OrgMemNo;
														$LoanNo = $rows->LoanNo;
														$Tranamount = $rows->Tranamount;
														$ColcDate = $rows->ColcDate;
														$TrxType = $rows->TrxType;
														$TransNo = $rows->TransNo;
														$ColcFor = $rows->ColcFor;
														$BufferId = $rows->BufferId;
														$UpdatedAt = $rows->UpdatedAt;
														$PaidBy = $rows->PaidBy;
														DB::Table($db2 . '.transectionsloan')->insert(['projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'loanno' => $LoanNo, 'tranamount' => $Tranamount, 'colcdate' => $ColcDate, 'trxtype' => $TrxType, 'transno' => $TransNo, 'colcfor' => $ColcFor, 'bufferid' => $BufferId, 'updatedat' => $UpdatedAt, 'area_id' => $area_id, 'branchcode' => $BranchCode, 'paidby' => $PaidBy, 'eventid' => $event_id]);
													}
												}
											}
										}
									}
								}
								Log::info("Transection For Monitoring End" . $BranchCode);
								// dd('finished');
								// phpinfo();
								//$refinanceloan = $_url_test . "RefinanceLoan?BranchCode=$BranchCode&ProjectCode=$projectcode&UpdatedAt=$previousOneYears&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// $refinanceloan = "http://104.199.211.13:9090/scapir/RefinanceLoan?BranchCode=0607&ProjectCode=060&UpdatedAt=2021-02-17%2012:00:00&key=5d0a4a85-df7a-scapir-bits-93eb-145f6a9902ae";
								// $refinanceloan = "https://bracapitesting.brac.net";
								// $refinanceloan = "http://104.199.211.13:9090/scapir/CollectionInfoForMonitoring?BranchCode=0605&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// $refinanceloan = "http://ff.fatulah.com:8888";
								// $refinanceloan = "http://104.199.211.13:9090/scapir/CollectionInfoForMonitoring?BranchCode=0605&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// $refinanceloan = "https://erp.brac.net/node/scapir/CollectionInfoForMonitoring?BranchCode=0605&ProjectCode=060&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// $refinanceloan = $_url_test . "RefinanceLoan?BranchCode=$BranchCode&ProjectCode=$projectcode&UpdatedAt=$previousOneYears&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
								// $refinanceloan = str_replace(" ", '%20', $refinanceloan);

								// $ch = curl_init();
								// curl_setopt($ch, CURLOPT_URL, $refinanceloan);
								// curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
								// // curl_setopt($ch, CURLOPT_PROXY, '104.199.211.13');
								// // curl_setopt($ch, CURLOPT_PROXYPORT, '9090');
								// // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
								// // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
								// // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
								// curl_setopt(
								// 	$ch,
								// 	CURLOPT_RETURNTRANSFER,
								// 	1
								// );
								// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
								// curl_setopt($ch, CURLOPT_HEADER, 1);
								// $data = curl_exec($ch);
								// $curl_info = curl_getinfo($ch);
								// dd($curl_info);

								// curl_close($ch);
								//	die;
								// $ch = curl_init();
								// curl_setopt($ch, CURLOPT_URL, $refinanceloan);
								// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, false);
								// $refinanceloansoutput = curl_exec($ch);
								// dd($ch);
								// curl_close($ch);



								Log::info("RefinaceLoan Start" . $BranchCode);
								$refinanace = Http::get($_url . 'RefinanceLoan', [
									'BranchCode' => $BranchCode,
									'ProjectCode' => $projectcode,
									'UpdatedAt' => $previousOneYears,
									'key' => $securitykey
								]);

								$refinanaceloanary = $refinanace->object();
								// dd($refinanaceloanary);

								if ($refinanaceloanary != NULL) {
									$refinanceloansdata = $refinanaceloanary->data;
									Log::channel('daily')->info('Refinanace Http: ' . $refinanaceloanary->code);
									Log::channel('daily')->info('Refinanace Message: ' . $refinanaceloanary->message);
									if ($refinanceloansdata) {
										$nextUrlJson = $refinanaceloanary->nextUrl;
										if ($nextUrlJson != null) {
											$nextUrlArrya = explode('/', $nextUrlJson);
											$nextUrl = $nextUrlArrya[1];
										} else {
											$nextUrl = null;
										}
										// dd($refinanaceloanary);

										foreach ($refinanceloansdata as $rows) {
											$ProjectCode = $rows->ProjectCode;
											$OrgNo = $rows->OrgNo;
											$OrgMemNo = $rows->OrgMemNo;
											$LoanNo = $rows->LoanNo;
											$LoanSlNo = $rows->LoanSlNo;
											$ProductNo = $rows->ProductNo;
											$PrincipalAmt = $rows->PrincipalAmt;
											$DisbDate = $rows->DisbDate;
											$UpdatedAt = $rows->UpdatedAt;
											$ProductName = $rows->ProductName;
											$ProductShortName	 = $rows->ProductShortName;
											$ProductType = $rows->ProductType;
											$LnStatus = $rows->LnStatus;

											DB::Table($db2 . '.refinanaceloan')->insert([
												'eventid' => $event_id,
												'ProjectCode' => $ProjectCode, 'OrgNo' => $OrgNo, 'OrgMemNo' => $OrgMemNo, 'loanno' => $LoanNo, 'LoanSlNo' => $LoanSlNo,
												'ProductNo' => $ProductNo, 'PrincipalAmt' => $PrincipalAmt, 'DisbDate' => $DisbDate, 'UpdatedAt' => $UpdatedAt, 'ProductName' => $ProductName, 'ProductShortName' => $ProductShortName, 'ProductType' => $ProductType, 'LnStatus' => $LnStatus
											]);
										}
										// dd($tansectionsloan);
										while ($nextUrl != null) {
											$refinanceurl = $_url . $nextUrl;
											$refinanace = Http::get($refinanceurl);

											//echo $refinanceurl;

											$refinanaceloanary = $refinanace->object();
											// dd($transectionsjsondecode);
											if ($refinanaceloanary != NULL) {
												$refinanceloansdata = $refinanaceloanary->data;
												$nextUrlJson = $refinanaceloanary->nextUrl;
												if ($nextUrlJson != null) {
													$nextUrlArrya = explode('/', $nextUrlJson);
													$nextUrl = $nextUrlArrya[1];
												} else {
													$nextUrl = null;
												}
												// dd($refinanaceloanary);
												if ($refinanceloansdata != NULL) {
													foreach ($refinanceloansdata as $rows) {
														$ProjectCode = $rows->ProjectCode;
														$OrgNo = $rows->OrgNo;
														$OrgMemNo = $rows->OrgMemNo;
														$LoanNo = $rows->loanno;
														$LoanSlNo = $rows->LoanSlNo;
														$ProductNo = $rows->ProductNo;
														$PrincipalAmt = $rows->PrincipalAmt;
														$DisbDate = $rows->DisbDate;
														$UpdatedAt = $rows->UpdatedAt;
														$ProductName = $rows->ProductName;
														$ProductShortName	 = $rows->ProductShortName;
														$ProductType = $rows->ProductType;
														$LnStatus = $rows->LnStatus;

														DB::Table($db2 . '.refinanaceloan')->insert([
															'eventid' => $event_id,
															'ProjectCode' => $ProjectCode, 'OrgNo' => $OrgNo, 'OrgMemNo' => $OrgMemNo, 'loanno' => $LoanNo, 'LoanSlNo' => $LoanSlNo,
															'ProductNo' => $ProductNo, 'PrincipalAmt' => $PrincipalAmt, 'DisbDate' => $DisbDate, 'UpdatedAt' => $UpdatedAt, 'ProductName' => $ProductName, 'ProductShortName' => $ProductShortName, 'ProductType' => $ProductType, 'LnStatus' => $LnStatus
														]);
													}
												}
											}
										}
									}
								}
								Log::info("RefinaceLoan End" . $BranchCode);





								// $refinanceloan = $_url_test . "RefinanceLoan?BranchCode=$BranchCode&ProjectCode=$projectcode&UpdatedAt=2022-01-22 00:00:00&key=5d0a4a85-df7a-scapir-bits-93eb-145f6a9902ae";
								// $refinanceloan = 'http://104.199.211.13:9090/scapir/RefinanceLoan?BranchCode=0547&ProjectCode=060&UpdatedAt=2022-01-22 00:00:00&key=5d0a4a85-df7a-scapir-bits-93eb-145f6a9902ae';
								// $refinanceloan = urlencode($refinanceloan);
								// echo $refinanceloan;
								// //	die;
								// $ch = curl_init();
								// curl_setopt($ch, CURLOPT_URL, $refinanceloan);
								// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, false);
								// $refinanceloansoutput = curl_exec($ch);
								// curl_close($ch);

								// $refinanaceloanary = json_decode($refinanceloansoutput);
								// dd($refinanaceloanary);
								// if ($refinanaceloanary != NULL) {
								// 	$refinanceloansdata = $refinanaceloanary->data;

								// 	foreach ($refinanceloansdata as $rows) {
								// 		$ProjectCode = $rows->ProjectCode;
								// 		$OrgNo = $rows->OrgNo;
								// 		$OrgMemNo = $rows->OrgMemNo;
								// 		$LoanNo = $rows->LoanNo;
								// 		$LoanSlNo = $rows->LoanSlNo;
								// 		$ProductNo = $rows->ProductNo;
								// 		$PrincipalAmount = $rows->PrincipalAmount;
								// 		$DisbDate = $rows->DisbDate;
								// 		$UpdatedAt = $rows->UpdatedAt;
								// 		$ProductName = $rows->ProductName;
								// 		$ProductShortName	 = $rows->ProductShortName;
								// 		$ProductType = $rows->ProductType;
								// 		$LnStatus = $rows->LnStatus;

								// 		DB::Table($db2 . '.refinanaceloan')->insert([
								// 			'eventid' => $event_id,
								// 			'ProjectCode' => $ProjectCode, 'OrgNo' => $OrgNo, 'OrgMemNo' => $OrgMemNo, 'LoanNo' => $LoanNo, 'LoanSlNo' => $LoanSlNo,
								// 			'ProductNo' => $ProductNo, 'productsymbol' => $ProductSymbol, 'PrincipalAmount' => $PrincipalAmount, 'disbdate' => $DisbDate, 'UpdatedAt' => $UpdatedAt, 'ProductName' => $ProductName, 'ProductShortName' => $ProductShortName, 'ProductType' => $ProductType, 'lnstatus' => $LnStatus
								// 		]);
								// 	}
								// }

								Log::info("CollectionInfoForMonitoring Start" . $BranchCode);
								$cloans = $_url . "CollectionInfoForMonitoring?BranchCode=$BranchCode&ProjectCode=$projectcode&key=$securitykey";
								//echo $cloans;
								//	die;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $cloans);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, false);
								$cloansoutput = curl_exec($ch);
								curl_close($ch);
								Log::info("Collection-" . $cloansoutput);
								$collection = json_decode($cloansoutput);
								// dd($collection);
								if ($collection != NULL) {
									$cloansdata = $collection->data;
									Log::channel('daily')->info('Cloan Http: ' . $collection->code);
									Log::channel('daily')->info('Cloan Message: ' . $collection->message);
									foreach ($cloansdata as $rows) {
										$ProjectCode = $rows->ProjectCode;
										$OrgNo = $rows->OrgNo;
										$OrgMemNo = $rows->OrgMemNo;
										$LoanNo = $rows->LoanNo;
										$LoanSlNo = $rows->LoanSlNo;
										$ProductNo = $rows->ProductNo;
										$ProductSymbol = $rows->ProductSymbol;
										$ProductName = $rows->ProductShortName;
										$IntrFactorLoan = $rows->IntrFactorLoan;
										$PrincipalAmount = $rows->PrincipalAmount;
										$InstlAmtLoan = $rows->InstlAmtLoan;
										$DisbDate = $rows->DisbDate;
										$LnStatus = $rows->LnStatus;
										$PrincipalDue = $rows->PrincipalDue;
										$InterestDue = $rows->InterestDue;
										$TotalDue = $rows->TotalDue;
										$TargetAmtLoan = $rows->TargetAmtLoan;
										$TotalReld = $rows->TotalReld;
										$Overdue = $rows->Overdue;
										$BufferIntrAmt = $rows->BufferIntrAmt;
										$InstlPassed = $rows->InstlPassed;
										$TargetDate = $rows->TargetDate;
										$UpdatedAt = $rows->UpdatedAt;
										$Insurance = $rows->Insurance;
										$Premium = $rows->Premium;

										DB::Table($db2 . '.cloans')->insert([
											'projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'loanno' => $LoanNo, 'loanslno' => $LoanSlNo,
											'productno' => $ProductNo, 'productsymbol' => $ProductSymbol, 'interfactorloan' => $IntrFactorLoan, 'principalamt' => $PrincipalAmount, 'instlamtloan' => $InstlAmtLoan, 'disbdate' => $DisbDate, 'lnstatus' => $LnStatus, 'principaldue' => $PrincipalDue,
											'interestdue' => $InterestDue, 'totaldue' => $TotalDue, 'targetamtloan' => $TargetAmtLoan, 'totalreld' => $TotalReld, 'overdue' => $Overdue, 'bufferintramt' => $BufferIntrAmt,
											'instlpassed' => $InstlPassed, 'targetdate' => $TargetDate, 'updateat' => $UpdatedAt, 'area_id' => $area_id, 'branchcode' => $BranchCode, 'insurence' => $Insurance, 'premium' => $Premium, 'productname' => $ProductName, 'eventid' => $event_id
										]);
									}
								}
								Log::info("CollectionInfoForMonitoring End" . $BranchCode);
								Log::info("SavingsInfo Start" . $BranchCode);
								$SavingsInfo = $_url . "SavingsInfo?BranchCode=$BranchCode&ProjectCode=$projectcode&Status=2&key=$securitykey";
								//echo $SavingsInfo;
								// die;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $SavingsInfo);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, false);
								$output = curl_exec($ch);
								curl_close($ch);
								$savingsjsondecode = json_decode($output);
								if ($savingsjsondecode != NULL) {
									$savings = $savingsjsondecode->data;
									foreach ($savings as $rows) {
										$OrgNo = $rows->OrgNo;
										$OrgMemNo = $rows->OrgMemNo;
										$ProjectCode = $rows->ProjectCode;
										$BranchCode = $rows->BranchCode;
										$MemberName = $rows->MemberName;
										$SavBalan = $rows->SavBalan;
										$SavPayable = $rows->SavPayable;
										$CalcIntrAmt = $rows->CalcIntrAmt;
										$TargetAmtSav = $rows->TargetAmtSav;
										$ApplicationDate = $rows->ApplicationDate;
										$NationalId = $rows->NationalId;
										$FatherName = $rows->FatherName;
										$MotherName = $rows->MotherName;
										$SpouseName = $rows->SpouseName;
										$ContactNo = $rows->ContactNo;
										$BkashWalletNo = $rows->BkashWalletNo;
										$AssignedPO = $rows->AssignedPO;
										$UpdatedAt = $rows->UpdatedAt;

										$savingsinsert = DB::Table($db2 . '.memberlists')->insert([
											'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'projectcode' => $ProjectCode, 'branchcode' => $BranchCode, 'membername' => $MemberName,
											'savbalan' => $SavBalan, 'savpayable' => $SavPayable, 'calcintramt' => $CalcIntrAmt, 'targetamtsav' => $TargetAmtSav, 'applicationdate' => $ApplicationDate,
											'nationalid' => $NationalId, 'fathername' => $FatherName, 'mothername' => $MotherName,
											'spousename' => $SpouseName, 'contactno' => $ContactNo, 'bkashwalletno' => $BkashWalletNo, 'assignedpo' => $AssignedPO, 'updatedat' => $UpdatedAt, 'area_id' => $area_id, 'eventid' => $event_id
										]);
									}
								}
								Log::info("SavingsInfo End" . $BranchCode);
								Log::info("ClosedLoanInfo Start" . $BranchCode);
								$closedloan = $_url . "ClosedLoanInfo?BranchCode=$BranchCode&ProjectCode=$projectcode&key=$securitykey";
								//echo $closedloan;
								//die;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $closedloan);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, false);
								$closedoutput = curl_exec($ch);
								curl_close($ch);
								$closedjsondecode = json_decode($closedoutput);
								if ($closedjsondecode != NULL) {
									$closed = $closedjsondecode->data;
									Log::channel('daily')->info('ClosedLoan Http: ' . $closedjsondecode->code);
									Log::channel('daily')->info('ClosedLoan Message: ' . $closedjsondecode->message);
									foreach ($closed as $rows) {
										$ProjectCode = $rows->ProjectCode;
										$OrgNo = $rows->OrgNo;
										$OrgMemNo = $rows->OrgMemNo;
										$LoanNo = $rows->LoanNo;
										$LoanSlNo = $rows->LoanSlNo;
										$ProductNo = $rows->ProductNo;
										$ProductSymbol = $rows->ProductSymbol;
										$ProductName = $rows->ProductShortName;
										$IntrRate = $rows->IntrRate;
										$PrincipalAmt = $rows->PrincipalAmt;
										$InstlAmt = $rows->InstlAmt;
										$DisbDate = $rows->DisbDate;
										$LnStatus = $rows->LnStatus;
										$TotalReld = $rows->TotalReld;
										$InstlPassed = $rows->InstlPassed;
										$LoanTragetAmount = $rows->LoanTragetAmount;
										$ClosedDate = $rows->ClosedDate;
										$WriteOffAmount = $rows->WriteOffAmount;
										$UpdatedAt = $rows->UpdatedAt;

										$closedinsert = DB::Table($db2 . '.closedloan')->insert([
											'projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'loanno' => $LoanNo, 'loanslno' => $LoanSlNo,
											'productno' => $ProductNo, 'productsymbol' => $ProductSymbol, 'interrate' => $IntrRate, 'principalamt' => $PrincipalAmt, 'instlamt' => $InstlAmt, 'disbdate' => $DisbDate, 'lnstatus' => $LnStatus, 'totalreld' => $TotalReld,
											'instlpassed' => $InstlPassed, 'loantragetamount' => $LoanTragetAmount, 'closeddate' => $ClosedDate, 'writeoffamount' => $WriteOffAmount, 'updatedat' => $UpdatedAt, 'area_id' => $area_id, 'productname' => $ProductName, 'branchcode' => $BranchCode, 'eventid' => $event_id
										]);
									}
								}
								Log::info("ClosedLoanInfo End" . $BranchCode);
								// $tansectionsloan = $_url . "TransactionsForMonitoring?BranchCode=$BranchCode&ProjectCode=$projectcode&StartDate=$StartDate&EndDate=$DateStart&key=$securitykey";
								// //echo $tansectionsloan;
								// //die;
								// $ch = curl_init();
								// curl_setopt($ch, CURLOPT_URL, $tansectionsloan);
								// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, false);
								// $transectionsoutput = curl_exec($ch);
								// curl_close($ch);
								// $transectionsjsondecode = json_decode($transectionsoutput);
								// if ($transectionsjsondecode != NULL) {
								// 	$transections = $transectionsjsondecode->data;
								// 	foreach ($transections as $rows) {
								// 		$ProjectCode = $rows->ProjectCode;
								// 		$OrgNo = $rows->OrgNo;
								// 		$OrgMemNo = $rows->OrgMemNo;
								// 		$LoanNo = $rows->LoanNo;
								// 		$Tranamount = $rows->Tranamount;
								// 		$ColcDate = $rows->ColcDate;
								// 		$TrxType = $rows->TrxType;
								// 		$TransNo = $rows->TransNo;
								// 		$ColcFor = $rows->ColcFor;
								// 		$BufferId = $rows->BufferId;
								// 		$UpdatedAt = $rows->UpdatedAt;
								// 		$PaidBy = $rows->PaidBy;
								// 		$transectionsdinsert = DB::Table($db2 . '.transectionsloan')->insert([
								// 			'projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'loanno' => $LoanNo, 'tranamount' => $Tranamount,
								// 			'colcdate' => $ColcDate, 'trxtype' => $TrxType, 'transno' => $TransNo, 'colcfor' => $ColcFor, 'bufferid' => $BufferId, 'updatedat' => $UpdatedAt, 'area_id' => $area_id, 'branchcode' => $BranchCode, 'paidby' => $PaidBy, 'eventid' => $event_id
								// 		]);
								// 	}
								// }
								Log::info("Targets Start" . $BranchCode);

								$targets = $_url . "Targets?BranchCode=$BranchCode&ProjectCode=$projectcode&StartDate=$StartDate&EndDate=$DateStart1&key=$securitykey";
								// $targets = "https://erp.brac.net/node/scapir/Targets?BranchCode=$BranchCode&ProjectCode=$projectcode&StartDate=$StartDate&EndDate=$DateStart&key=$securitykey";
								//echo $targets;
								//die;
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $targets);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, false);
								$targetsoutput = curl_exec($ch);
								curl_close($ch);
								$targetsjsondecode = json_decode($targetsoutput);
								if ($targetsjsondecode != NULL) {
									$targetsf = $targetsjsondecode->data;
									Log::channel('daily')->info('Target Http: ' . $targetsjsondecode->code);
									Log::channel('daily')->info('Target Message: ' . $targetsjsondecode->message);
									foreach ($targetsf as $rows) {
										$ProjectCode = $rows->ProjectCode;
										$OrgNo = $rows->OrgNo;
										$OrgMemNo = $rows->OrgMemNo;
										$CONo = $rows->CONo;
										$LoanNo = $rows->LoanNo;
										$DisbursementDate = $rows->DisbursementDate;
										$TargetDate = $rows->TargetDate;
										$TargetAmount = $rows->TargetAmount;
										$UpdatedAt = $rows->UpdatedAt;
										$LoanStatusId = $rows->LoanStatusId;
										$targetsinsert = DB::Table($db2 . '.targets')->insert([
											'projectcode' => $ProjectCode, 'orgno' => $OrgNo, 'orgmemno' => $OrgMemNo, 'cono' => $CONo, 'loanno' => $LoanNo, 'disbdate' => $DisbursementDate,
											'targetdate' => $TargetDate, 'targetamt' => $TargetAmount, 'updatedat' => $UpdatedAt, 'loanstatusid' => $LoanStatusId, 'area_id' => $area_id, 'branchcode' => $BranchCode, 'eventid' => $event_id
										]);
									}
								}
								Log::info("Targets End" . $BranchCode);
							}
						}
						DB::commit();
						Log::info("DataProcessingController Start");
						$dataprocess = new DataProcessingController;
						$dataprocess->DataProcess();	//respondent creation method
						Log::info("DataProcessingController End");
					} catch (\Throwable $e) {
						DB::rollback();
						throw $e;
					}
				} else {
					$server_downmessage = array("status" => "CUSTMSG", "message" => $server_message);
					$json3 = json_encode($server_downmessage);
					echo $json3;
				}
			}
		}
	}
	public function Get_Respondents(Request $request)
	{
		$eventid = $request->get('eventid');

		$db = 'progoti_snapshot';
		$res = DB::Table($db . '.respondents')->where('eventid', $eventid)->get();
		echo json_encode($res);
	}
	public function ClosedLoan(Request $request)
	{
		$area_id = $request->get('area_id');
		$eventid = $request->get('eventid');

		$db = 'progoti_snapshot';
		$res = DB::Table($db . '.closedloan')->where('area_id', $area_id)->where('eventid', $eventid)->get();
		echo json_encode($res);
	}
	public function COLIST(Request $request)
	{
		$db = 'mnw_progoti';
		$eventid = $request->get('eventid');
		// dd($eventid);
		$getBranch  = DB::Table($db . '.monitorevents')->where('id', $eventid)->get();
		// dd($getBranch);
		if ($getBranch->isEmpty()) {
		} else {
			foreach ($getBranch as $row) {
				$BranchCode = $row->branchcode;
				$branch_array = explode(',', $BranchCode);
				//dd($branch_array);
				foreach ($branch_array as $BranchCode) {

					$BranchCode = str_pad($BranchCode, 4, "0", STR_PAD_LEFT);
					//dd($BranchCode);
					$url = "https://erp.brac.net/node/scapir/BranchEmployeeList?BranchCode=$BranchCode&ProjectCode=060&UpdatedAt=2000-10-16%2010:00:00&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_HEADER, false);
					$colists = curl_exec($ch);
					// dd($colists);
					curl_close($ch);
					$decodecolist = json_decode($colists);
					if ($decodecolist != NULL) {
						$colist =  $decodecolist->data;
						if (!empty($colist)) {
							foreach ($colist as $row) {
								$desig = $row->Desig;
								if ($desig != 'Area Manager') {
									$alldata[] = array("CoNo" => $row->PIN, "CoName" => $row->PName, "BranchCode" => $BranchCode);
								}
							}
						}
					}
					/*$Colist = DB::table('bmfpoerp.polist')->where('branchcode',$BranchCode)->where('status',1)->get();
					if(!$Colist->isEmpty())
					{
						foreach($Colist as $rows)
						{
							$alldata[] =array("CoNo"=>$rows->cono,"CoName"=>$rows->coname);
						}
					}*/
				}
			}
			echo json_encode($alldata);
		}
	}
	public function Transections(Request $request)
	{
		$db = 'mnw_progoti';
		$db_snapshot = 'progoti_snapshot';
		$eventid = $request->get('eventid');
		$res = DB::Table($db . '.respondents')->select('orgmemno')->where('eventid', $eventid)->groupBy('orgmemno')->get();
		if (!$res->isEmpty()) {
			foreach ($res as $row) {
				$memno = $row->orgmemno;
				$transections = DB::table($db_snapshot . '.transectionsloan')->where('orgmemno', $memno)->get();
				if (!$transections->isEmpty()) {
					$alldatas[] = $transections;
				}
			}
			echo json_encode(array("code" => "200", "data" => $alldatas));
		}
	}
	public function MemberList(Request $request)
	{
		$db = 'progoti_snapshot';
		$area_id = $request->get('area_id');
		$eventid = $request->get('eventid');
		$res = DB::Table($db . '.memberlists')->where('area_id', $area_id)->where('eventid', $eventid)->get();
		echo json_encode(array("status" => "success", "data" => $res));
	}
	public function BranchCode(Request $request)
	{
		$db = 'mnw_progoti';
		$eventid = $request->get('eventid');
		$getevents = DB::Table($db . '.monitorevents')->where('id', $eventid)->get();
		if ($getevents->isEmpty()) {
		} else {
			$BranchCodes =  $getevents[0]->branchcode;
			$branch_array = explode(',', $BranchCodes);
			//dd($branch_array);
			foreach ($branch_array as $BranchCode) {
				$BranchCode = $BranchCode;
				/*$cnt = substr($BranchCode1,0);
				if($cnt=='0')
				{
					echo substr($BranchCode1,1);
				}*/

				$getbcname = DB::Table('branch')->where('branch_id', $BranchCode)->get();
				if (!$getbcname->isEmpty()) {
					$bname = $getbcname[0]->branch_name;
					$BranchCode = str_pad($BranchCode, 4, "0", STR_PAD_LEFT);
					$gtbname[] = array("branchcode" => $BranchCode, "BranchName" => $bname);
				}
			}
			echo json_encode(array("status" => "success", "data" => $gtbname));
		}
	}
	public function CLoans(Request $request)
	{
		$db = 'progoti_snapshot';
		$area_id = $request->get('area_id');
		$eventid = $request->get('eventid');
		$res = DB::Table($db . '.cloans')->where('area_id', $area_id)->where('eventid', $eventid)->get();
		echo json_encode(array("status" => "success", "data" => $res));
	}
}
