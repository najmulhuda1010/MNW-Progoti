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
class CodeController extends Controller
{
  public function Mnwsection3()
  {
    $respondent = 32;
    $db = 'progoti_snapshot';
    $eventid = '33';
    $section = 3;
    $branchcode = '607,2089';
    $branch_array = explode(',', $branchcode);
    $branchcount = count($branch_array);
    $respondentForEveryBranch = round(32 / $branchcount);
    foreach ($branch_array as $branchcode) {
      $branchcode = str_pad($branchcode, 4, "0", STR_PAD_LEFT);
      $section3sub2 = DB::select(DB::raw("SELECT rs.*, tr.*, ((tr.amt / rs.cinstlamt) + Case When tr.amt % rs.cinstlamt > 0 then 1 else 0 end) as mfact FROM (SELECT l.*, c.loanno as cloanno, c.disbdate as cdisbdate, Cast(c.instlamt as Integer) as cinstlamt,c.principalamt as cprincipalamt, c.instlpassed as cinstlpassed,c.eventid as ceventid, datediff('d', c.closeddate, l.disbdate) as diff, ROW_NUMBER() over (PARTITION BY l.branchcode, l.orgmemno ORDER BY l.branchcode, l.orgmemno, c.closeddate desc) as RN  FROM progoti_snapshot.cloans l inner join progoti_snapshot.closedloan c on l.branchcode = c.branchcode and l.orgmemno=c.orgmemno and l.eventid=c.eventid where l.branchcode='$branchcode' and l.lnstatus=1 and l.disbdate >= c.closeddate and datediff('d', c.closeddate, l.disbdate) <= 150 and l.loanno not in (select r.loanno from progoti_snapshot.refinanaceloan r) and l.eventid='$eventid' ) rs, (SELECT t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate, sum(cast(t1.tranamount as Integer)) as amt, count(t1.*) FROM progoti_snapshot.transectionsloan t1 left outer join progoti_snapshot.transectionsloan t2 ON (t1.branchcode= t2.branchcode and t1.orgmemno=t2.orgmemno and t1.loanno=t2.loanno AND t1.colcdate < t2.colcdate) where t1.branchcode='$branchcode' and t1.eventid='$eventid' and t2.loanno is null group by t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate) tr where rs.branchcode='$branchcode' and rs.rn=1 and rs.branchcode=tr.branchcode and rs.orgmemno=tr.orgmemno and rs.loanno=tr.loanno order by tr.amt/rs.cinstlamt desc"));
      if (empty($section3sub2)) {
        $emptybranch = array("branchcode" => $branchcode);
        $emptybranchcnt = count($emptybranch);
        $totalRes = $emptybranchcnt * $respondentForEveryBranch;
        echo $totalRes;
      } else {
      }
    }
  }
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
              // $this->section1($db, $pdate, $previousdate, $i, $branchcode, $area_id, $eventid);
            } else if ($i == 3) {
              $this->section3($db, $pdate, $i, $branchcode, $area_id, $eventid);
            } else if ($i == 4) {
            } else if ($i == 5) {
            }
          }
          DB::commit();

          //DB::table($db2 . '.monitorevents')->where('id', $eventid)->update(['data_proccess_status' => 1]);

          return "Sample Respondents Created Sucessfully";
        } catch (\Throwable $e) {
          DB::rollback();
          throw $e;
        }
      }
    }
  }
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
        $section3sub2 = DB::select(DB::raw("SELECT rs.*, tr.*, ((tr.amt / rs.cinstlamt) + Case When tr.amt % rs.cinstlamt > 0 then 1 else 0 end) as mfact FROM (SELECT l.*, c.loanno as cloanno, c.disbdate as cdisbdate, Cast(c.instlamt as Integer) as cinstlamt,c.principalamt as cprincipalamt, c.instlpassed as cinstlpassed,c.eventid as ceventid, datediff('d', c.closeddate, l.disbdate) as diff, ROW_NUMBER() over (PARTITION BY l.branchcode, l.orgmemno ORDER BY l.branchcode, l.orgmemno, c.closeddate desc) as RN  FROM progoti_snapshot.cloans l inner join progoti_snapshot.closedloan c on l.branchcode = c.branchcode and l.orgmemno=c.orgmemno and l.eventid=c.eventid where  l.lnstatus=1 and l.disbdate >= c.closeddate and datediff('d', c.closeddate, l.disbdate) <= 150 and l.loanno not in (select r.loanno from progoti_snapshot.refinanaceloan r) and l.eventid='$eventid' ) rs, (SELECT t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate, sum(cast(t1.tranamount as Integer)) as amt, count(t1.*) FROM progoti_snapshot.transectionsloan t1 left outer join progoti_snapshot.transectionsloan t2 ON (t1.branchcode= t2.branchcode and t1.orgmemno=t2.orgmemno and t1.loanno=t2.loanno AND t1.colcdate < t2.colcdate) where  t1.eventid='$eventid' and t2.loanno is null group by t1.branchcode, t1.orgmemno, t1.loanno, t1.paidby,t1.colcdate) tr where  rs.rn=1 and rs.branchcode=tr.branchcode and rs.orgmemno=tr.orgmemno and rs.loanno=tr.loanno order by tr.amt/rs.cinstlamt desc"));
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
  }
}
