<?php
error_reporting(0);
set_time_limit(600);
if (Session::has('roll')) {
	$roll = Session::get('roll');
}
if ($roll == '1') {
	echo $roll;
	die;
	$br = 0;
	if (Session::has('asid')) {
		$br = Session::get('asid');
	}
	MainLoop($br);
} else if ($roll == '2') {
	if (Session::has('asid')) {
		$areaid = Session::get('asid');
	}

	GetDataFromArea($areaid);
} else if ($roll == '3') {
	if (Session::has('asid')) {
		$regionid = Session::get('asid');
	}
	GetDataFromRegion($regionid);
} else if ($roll == '4') {
	if (Session::has('asid')) {
		$divisionid = Session::get('asid');
	}
	GetDataFromDivision($divisionid);
} else {
	GetDataFromAdmin();
}

function GetDataFromArea($areaid)
{
	$getarea = DB::table('mnw_progoti.monitorevents')->where('area_id', $areaid)->get();

	if ($getarea->isEmpty()) {
	} else {
		MainLoop($areaid);
	}
}
function GetDataFromRegion($regionid)
{
	$getregion = DB::table('mnw_progoti.monitorevents')->select('area_id')->where('region_id', $regionid)->groupBy('area_id')->get();
	if ($getregion->isEmpty()) {
	} else {
		foreach ($getregion as $row) {
			$areaid = $row->area_id;
			GetDataFromArea($areaid);
		}
	}
}
function GetDataFromDivision($divisionid)
{

	$getdivision = DB::table('mnw_progoti.monitorevents')->select('region_id')->where('division_id', $divisionid)->groupBy('region_id')->get();

	if ($getdivision->isEmpty()) {
	} else {
		foreach ($getdivision as $row) {
			$regionid = $row->region_id;
			GetDataFromRegion($regionid);
		}
	}
}
function GetDataFromAdmin()
{
	$getadmin = DB::table('mnw_progoti.monitorevents')->select('division_id')->groupBy('division_id')->get();
	if ($getadmin->isEmpty()) {
	} else {
		foreach ($getadmin as $row) {
			$divisionid = $row->division_id;
			GetDataFromDivision($divisionid);
		}
	}
}
function MainLoop($br)
{
	$cur_date = date('Y-m-d');
	$checkclosed = DB::table('mnw_progoti.monitorevents')->where('area_id', $br)->where('dateend', '<', $cur_date)->get();

	if ($checkclosed->isEmpty()) {
	} else {
		foreach ($checkclosed as $closed) {
			$processdate = $closed->processing_date;
			$event_id = $closed->id;
			$area_id = $closed->area_id;

			$checksurveydate = DB::select(DB::raw("select * from mnw_progoti.survey_data where event_id='$event_id' and cast(time as date) >= cast('$processdate' as date)")); // if survey date time > monitorevents table of processing date 
			// dd($checksurveydate);

			if (empty($checksurveydate)) {
			} else {
				foreach ($checksurveydate as $row) {
					// dd($row);
					$eventid = $row->event_id;
					$secid = $row->sec_no;
					$score = $row->score;
					$question = $row->question;
					$sub_sec_id = $row->sub_sec_id;
					$orgno = $row->orgno;
					$monitorno = $row->monitorno;
					if ($secid == '1') {
						SectionOne($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id);
					} else if ($secid == '2') {
						SectionTwo($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id);
					} else if ($secid == '3') {
						SectionThree($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id);
					} else if ($secid == '4') {
						SectionFour($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id);
					} else if ($secid == '5') {
						SectionFive($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id);
					}
				}
				UpdateScoreDatabase($area_id, $event_id);
			}
		}
	}
}
function UpdateScoreDatabase($area_id, $event_id)
{

	//$allpoint = DB::select( DB::raw("select * from mnw_progoti.cal_section_point where branchcode='$brcode' and event_id='$event_id'"));
	$allpoint = DB::table('mnw_progoti.cal_section_point')->where('event_id', $event_id)->where('area_id', $area_id)->get();
	if (empty($allpoint)) {
	} else {
		$tpoint = 0;
		$time = date('Y-m-d');
		foreach ($allpoint as $data) {
			$event = $data->event_id;
			$sec = $data->section;
			$area_id = $data->area_id;
			/*if($sec==1)
			{*/
			//$checkscorepoint =DB::select( DB::raw("select * from mnw_progoti.cal_sections_score where branchcode='$brnch' and event_id='$event' and sec_no='$sec'"));

			$checkscorepoint = DB::table('mnw_progoti.cal_sections_score')->where('event_id', $event)->where('area_id', $area_id)->where('sec_no', $sec)->get();

			if ($checkscorepoint->isEmpty()) {
				//$countsectionpoint =DB::select( DB::raw("select sum(point) as point,sum(question_point) as fullscore from mnw_progoti.cal_section_point where branchcode='$brnch' and event_id='$event' and section='$sec'"));

				$countsectionpoint = DB::table('mnw_progoti.cal_section_point')->selectRaw('sum(point) as point,sum(question_point) as fullscore')->where('event_id', $event)->where('section', $sec)->where('area_id', $area_id)->get();

				if ($countsectionpoint->isEmpty()) {
				} else {

					$totalpoint = $countsectionpoint[0]->point;
					$fullscore = $countsectionpoint[0]->fullscore;
					//$sec1scoring = DB::select( DB::raw("select * from mnw_progoti.def_scoring where sec_no='$sec'"));

					$sec1scoring = DB::table('mnw_progoti.def_scoring')->where('sec_no', $sec)->get();

					if ($sec1scoring->isEmpty()) {
						//echo "No Found Data";
					} else {
						//$fullscore = $sec1scoring[0]->fullscore;
						$weight = $sec1scoring[0]->weight;
					}
					if ($fullscore > 0) {
						$sec1point = ($totalpoint / $fullscore) * $weight;
					} else {
						$sec1point = 0;
					}

					$sec1calsection = DB::table('mnw_progoti.cal_sections_score')->insert(['area_id' => $area_id, 'sec_no' => $sec, 'total' => $totalpoint, 'score' => $sec1point, 'event_id' => $event]);
				}
			} else {
				$id = $checkscorepoint[0]->id;
				$time = date('Y-m-d');
				$fullscore = 0;
				//$countsectionpoint =DB::select( DB::raw("select sum(point) as point,sum(question_point) as fullscore from mnw_progoti.cal_section_point where branchcode='$brnch' and event_id='$event' and section='$sec'"));

				$countsectionpoint = DB::table('mnw_progoti.cal_section_point')->selectRaw('sum(point) as point,sum(question_point) as fullscore')->where('event_id', $event)->where('section', $sec)->where('area_id', $area_id)->get();
				if ($countsectionpoint->isEmpty()) {
				} else {
					//$tpoint=0;
					$totalpoint = $countsectionpoint[0]->point;
					$fullscore = $countsectionpoint[0]->fullscore;
					//$sec1scoring = DB::select( DB::raw("select * from mnw_progoti.def_scoring where sec_no='$sec'"));

					$sec1scoring = DB::table('mnw_progoti.def_scoring')->where('sec_no', $sec)->get();

					if ($sec1scoring->isEmpty()) {
						//echo "No Found Data";
					} else {
						//$fullscore = $sec1scoring[0]->fullscore;
						$weight = $sec1scoring[0]->weight;
					}
					if ($fullscore > 0) {
						$sec1point = ($totalpoint / $fullscore) * $weight;
					} else {
						$sec1point = 0;
					}


					$sec1calsection = DB::table('mnw_progoti.cal_sections_score')->where('id', $id)->update(['area_id' => $area_id, 'sec_no' => $sec, 'total' => $totalpoint, 'score' => $sec1point, 'event_id' => $event]);
				}
			}
			//}
		}
		//$allscorecount =DB::select( DB::raw("select * from mnw_progoti.cal_section_point where branchcode='$brnch' and event_id='$event'"));

		$allscorecount = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $event_id)->get();

		if ($allscorecount->isEmpty()) {
		} else {
			foreach ($allscorecount as $row) {
				$event = $row->event_id;
				$sec = $row->section;
				$area_id = $row->area_id;

				$countsectionpoint = DB::table('mnw_progoti.cal_section_point')->selectRaw('sum(point) as point,sum(question_point) as fullscore')->where('event_id', $event)->where('section', $sec)->where('area_id', $area_id)->get();

				if (!$countsectionpoint->isEmpty()) {
					$totalpoint = $countsectionpoint[0]->point;
					$fullscore = $countsectionpoint[0]->fullscore;
					//$sec1scoring = DB::select( DB::raw("select * from mnw_progoti.def_scoring where sec_no='$sec'"));

					$sec1scoring = DB::table('mnw_progoti.def_scoring')->where('sec_no', $sec)->get();
					// dd($sec1scoring);
					if (!$sec1scoring->isEmpty()) {
						$weight = $sec1scoring[0]->weight;
					}

					if ($fullscore > 0) {
						$sectionpoint = ($totalpoint / $fullscore) * $weight;
					} else {
						$sectionpoint = 0;
					}

					$allsectionweight[$sec] = $sectionpoint;
				}
			}
			$tpoint = array_sum($allsectionweight);
			// dd($);
			$time = date('Y-m-d');
			//$point = $allscorecount[0]->tpoint;

			$updatemonitorevents = DB::table('mnw_progoti.monitorevents')->where('id', $event_id)->update(['processing_date' => $time, 'score' => $tpoint]);
			// dd($time);

		}
	}
}
function SectionOne($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id)
{
	$score = 0;
	$question_point = 0;
	$sec1 = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $eventid)->where('section', $secid)->where('sub_id', $sub_sec_id)->get();

	if ($sec1->isEmpty()) {

		$sec1_survey = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
		// $sec1_survey =DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id',$eventid)->where('sub_id',$sub_sec_id)->where('sec_no',$secid)->get();


		if (!empty($sec1_survey)) {
			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 1) {
				if ($score >= '18') {
					$p = 9;
				} else if ($score > '13' && $score < '18') {
					$p = 6;
				} else if ($score > '9' && $score < '14') {
					$p = 3;
				} else if ($score < '10') {
					$p = 0;
				}
			}


			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 2) {
				if ($score >= '7') {
					$p = 9;
				} else if ($score > '4' && $score < '7') {
					$p = 6;
				} else if ($score > '2' && $score < '5') {
					$p = 3;
				} else if ($score < '3') {
					$p = 0;
				}
			}

			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 3) {
				if ($score >= '4') {
					$p = 6;
				} else if ($score > '2' && $score < '4') {
					$p = 4;
				} else if ($score > '1' && $score < '3') {
					$p = 2;
				} else if ($score < '2') {
					$p = 0;
				}
			}

			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 4) {
				if ($score >= '8') {
					$p = 6;
				} else if ($score > '5' && $score < '8') {
					$p = 4;
				} else if ($score > '3' && $score < '6') {
					$p = 2;
				} else if ($score < '4') {
					$p = 0;
				}
			}


			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 5) {
				if ($score >= '35') {
					$p = 6;
				} else if ($score > '29' && $score < '35') {
					$p = 4;
				} else if ($score > '24' && $score < '30') {
					$p = 2;
				} else if ($score < '25') {
					$p = 0;
				}
			}
			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 6 || $sub_sec_id == 7 || $sub_sec_id == 8) {
				if ($score >= '16') {
					$p = 6;
				} else if ($score > '11' && $score < '16') {
					$p = 4;
				} else if ($score > '7' && $score < '12') {
					$p = 2;
				} else if ($score < '8') {
					$p = 0;
				}
			}
			if ($sub_sec_id == 9) {
				$sub_point1 = 0;
				$sub_point2 = 0;
				$sub_point3 = 0;
				$sub9 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
				if (!$sub9->isEmpty()) {
					foreach ($sub9 as $row) {
						$question = $row->question;
						if ($question == 1) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '16') {
								$sub_point1 = 1;
							} else {
								$sub_point1 = 0;
							}
						}
						if ($question == 2) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '16') {
								$sub_point2 = 1;
							} else {
								$sub_point2 = 0;
							}
						}
						if ($question == 3) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score == '20') {
								$sub_point3 = 1;
							} else {
								$sub_point3 = 0;
							}
						}
					}
					$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3;
					if ($sum_sub_point == '3') {
						$p = 6;
					} else if ($sum_sub_point == '2') {
						$p = 4;
					} else if ($sum_sub_point == '1') {
						$p = 2;
					} else {
						$p = 0;
					}
				}
			}
			if ($sub_sec_id == 10) {
				$sub_point1 = 0;
				$sub_point2 = 0;
				$sub10 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
				if (!$sub10->isEmpty()) {
					foreach ($sub10 as $row) {
						$question = $row->question;
						if ($question == 1) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score == '10') {
								$sub_point1 = 2;
							} else {
								$sub_point1 = 0;
							}
						}
						if ($question == 2) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '8') {
								$sub_point2 = 1;
							} else {
								$sub_point2 = 0;
							}
						}
					}
					$sum_sub_point = $sub_point1 + $sub_point2;
					if ($sum_sub_point == '3') {
						$p = 6;
					} else if ($sum_sub_point == '2') {
						$p = 4;
					} else if ($sum_sub_point == '1') {
						$p = 2;
					} else {
						$p = 0;
					}
				}
			}

			if ($sub_sec_id == '1' or $sub_sec_id == '2') {
				$question_point = 9;
			} else {
				$question_point = 6;
			}

			$sec1_insert = DB::table('mnw_progoti.cal_section_point')->insert(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'sub_id' => $sub_sec_id, 'question_point' => $question_point]);
		}
	} else {
		$id = $sec1[0]->id;

		$sec1_survey = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
		// $sec1_survey =DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id',$eventid)->where('sub_id',$sub_sec_id)->where('sec_no',$secid)->get();


		if (!empty($sec1_survey)) {
			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 1) {
				if ($score >= '18') {
					$p = 9;
				} else if ($score > '13' && $score < '18') {
					$p = 6;
				} else if ($score > '9' && $score < '14') {
					$p = 3;
				} else if ($score < '10') {
					$p = 0;
				}
			}


			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 2) {
				if ($score >= '7') {
					$p = 9;
				} else if ($score > '4' && $score < '7') {
					$p = 6;
				} else if ($score > '2' && $score < '5') {
					$p = 3;
				} else if ($score < '3') {
					$p = 0;
				}
			}

			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 3) {
				if ($score >= '4') {
					$p = 6;
				} else if ($score > '2' && $score < '4') {
					$p = 4;
				} else if ($score > '1' && $score < '3') {
					$p = 2;
				} else if ($score < '2') {
					$p = 0;
				}
			}

			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 4) {
				if ($score >= '8') {
					$p = 6;
				} else if ($score > '5' && $score < '8') {
					$p = 4;
				} else if ($score > '3' && $score < '6') {
					$p = 2;
				} else if ($score < '4') {
					$p = 0;
				}
			}


			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 5) {
				if ($score >= '35') {
					$p = 6;
				} else if ($score > '29' && $score < '35') {
					$p = 4;
				} else if ($score > '24' && $score < '30') {
					$p = 2;
				} else if ($score < '25') {
					$p = 0;
				}
			}
			$score = $sec1_survey[0]->score;
			if ($sub_sec_id == 6 || $sub_sec_id == 7 || $sub_sec_id == 8) {
				if ($score >= '16') {
					$p = 6;
				} else if ($score > '11' && $score < '16') {
					$p = 4;
				} else if ($score > '7' && $score < '12') {
					$p = 2;
				} else if ($score < '8') {
					$p = 0;
				}
			}
			if ($sub_sec_id == 9) {
				$sub_point1 = 0;
				$sub_point2 = 0;
				$sub_point3 = 0;
				$sub9 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
				if (!$sub9->isEmpty()) {
					foreach ($sub9 as $row) {
						$question = $row->question;
						if ($question == 1) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '16') {
								$sub_point1 = 1;
							} else {
								$sub_point1 = 0;
							}
						}
						if ($question == 2) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '16') {
								$sub_point2 = 1;
							} else {
								$sub_point2 = 0;
							}
						}
						if ($question == 3) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score == '20') {
								$sub_point3 = 1;
							} else {
								$sub_point3 = 0;
							}
						}
					}
					$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3;
					if ($sum_sub_point == '3') {
						$p = 6;
					} else if ($sum_sub_point == '2') {
						$p = 4;
					} else if ($sum_sub_point == '1') {
						$p = 2;
					} else {
						$p = 0;
					}
				}
			}
			if ($sub_sec_id == 10) {
				$sub_point1 = 0;
				$sub_point2 = 0;
				$sub10 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
				if (!$sub10->isEmpty()) {
					foreach ($sub10 as $row) {
						$question = $row->question;
						if ($question == 1) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score == '10') {
								$sub_point1 = 2;
							} else {
								$sub_point1 = 0;
							}
						}
						if ($question == 2) {
							$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
							$score = $checkScore[0]->score;
							// dd($score);
							if ($score >= '8') {
								$sub_point2 = 1;
							} else {
								$sub_point2 = 0;
							}
						}
					}
					$sum_sub_point = $sub_point1 + $sub_point2;
					if ($sum_sub_point == '3') {
						$p = 6;
					} else if ($sum_sub_point == '2') {
						$p = 4;
					} else if ($sum_sub_point == '1') {
						$p = 2;
					} else {
						$p = 0;
					}
				}
			}

			if ($sub_sec_id == '1' or $sub_sec_id == '2') {
				$question_point = 9;
			} else {
				$question_point = 6;
			}

			$sec1_insert = DB::table('mnw_progoti.cal_section_point')->where('id', $id)->update(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'sub_id' => $sub_sec_id, 'question_point' => $question_point]);
		}
	}
}
function SectionTwo($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id)
{
	$sec2 = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $eventid)->where('section', $secid)->where('sub_sec_id', $sub_sec_id)->get();
	if ($sec2->isEmpty()) {
		if ($sub_sec_id == '4') {
			$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
			$score = $checkScore[0]->score;
			if ($sum_sub_point >= '16') {
				$p = 6;
			} else if ($sum_sub_point <= '15' and $sum_sub_point >= '12') {
				$p = 4;
			} else if ($sum_sub_point <= '11' and $sum_sub_point >= '8') {
				$p = 2;
			} else {
				$p = 0;
			}
		} else {
			$sum_array = [];
			$sub1 = DB::table('mnw_progoti.survey_data')->select('orgmemno')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('orgmemno')->get();
			if (!$sub1->isEmpty()) {
				foreach ($sub1 as $row) {
					$orgmemno = $row->orgmemno;
					$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('orgmemno', $orgmemno)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
					$score = $checkScore[0]->score;
					// dd($orgmemno);
					if ($sub_sec_id == '1') {
						if ($score >= '3') {
							$sum_array[] = 1;
						} else {
							$sum_array[] = 0;
						}
					}
					if ($sub_sec_id == '2') {
						if ($score >= '4') {
							$sum_array[] = 1;
						} else {
							$sum_array[] = 0;
						}
					}
					if ($sub_sec_id == '3') {
						if ($score >= '2') {
							$sum_array[] = 1;
						} else {
							$sum_array[] = 0;
						}
					}
					if ($sub_sec_id == '5') {
						if ($score == '2') {
							$sum_array[] = 1;
						} else {
							$sum_array[] = 0;
						}
					}
				}
				$sum_sub_point = array_sum($sum_array);

				if ($sum_sub_point >= '16') {
					$p = 6;
				} else if ($sum_sub_point <= '15' and $sum_sub_point >= '12') {
					$p = 4;
				} else if ($sum_sub_point <= '11' and $sum_sub_point >= '8') {
					$p = 2;
				} else {
					$p = 0;
				}
			}
		}

		$question_point = 6;

		$sec2_insert = DB::table('mnw_progoti.cal_section_point')->insert(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'sub_id' => $sub_sec_id, 'question_point' => $question_point]);
	} else {
		$id = $sec2[0]->id;
		$sec2 = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $eventid)->where('section', $secid)->where('sub_sec_id', $sub_sec_id)->get();
		if ($sec2->isEmpty()) {
			if ($sub_sec_id == '4') {
				$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
				$score = $checkScore[0]->score;
				if ($sum_sub_point >= '16') {
					$p = 6;
				} else if ($sum_sub_point <= '15' and $sum_sub_point >= '12') {
					$p = 4;
				} else if ($sum_sub_point <= '11' and $sum_sub_point >= '8') {
					$p = 2;
				} else {
					$p = 0;
				}
			} else {
				$sum_array = [];
				$sub1 = DB::table('mnw_progoti.survey_data')->select('orgmemno')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('orgmemno')->get();
				if (!$sub1->isEmpty()) {
					foreach ($sub1 as $row) {
						$orgmemno = $row->orgmemno;
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('orgmemno', $orgmemno)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($orgmemno);
						if ($sub_sec_id == '1') {
							if ($score >= '3') {
								$sum_array[] = 1;
							} else {
								$sum_array[] = 0;
							}
						}
						if ($sub_sec_id == '2') {
							if ($score >= '4') {
								$sum_array[] = 1;
							} else {
								$sum_array[] = 0;
							}
						}
						if ($sub_sec_id == '3') {
							if ($score >= '2') {
								$sum_array[] = 1;
							} else {
								$sum_array[] = 0;
							}
						}
						if ($sub_sec_id == '5') {
							if ($score == '2') {
								$sum_array[] = 1;
							} else {
								$sum_array[] = 0;
							}
						}
					}
					$sum_sub_point = array_sum($sum_array);

					if ($sum_sub_point >= '16') {
						$p = 6;
					} else if ($sum_sub_point <= '15' and $sum_sub_point >= '12') {
						$p = 4;
					} else if ($sum_sub_point <= '11' and $sum_sub_point >= '8') {
						$p = 2;
					} else {
						$p = 0;
					}
				}
			}
			$question_point = 6;

			$sec1_insert = DB::table('mnw_progoti.cal_section_point')->where('id', $id)->update(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
		}
	}
}
function SectionThree($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id)
{
	$question_point = 0;
	$sec3 = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $eventid)->where('section', $secid)->where('sub_sec_id', $sub_sec_id)->get();
	if ($sec3->isEmpty()) {

		if ($sub_sec_id == 1) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub_point4 = 0;
			$sub_point5 = 0;
			$sub1 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub1->isEmpty()) {
				foreach ($sub1 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '8') {
							$sub_point3 = 1;
						} else {
							$sub_point3 = 0;
						}
					}
					if ($question == 4) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '8') {
							$sub_point4 = 1;
						} else {
							$sub_point4 = 0;
						}
					}
					if ($question == 5) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3 + $sub_point4 + $sub_point5;
				if ($sum_sub_point == '5') {
					$p = 12;
				} else if ($sum_sub_point >= '4' and $sum_sub_point < '5') {
					$p = 10;
				} else if ($sum_sub_point >= '3' and $sum_sub_point < '4') {
					$p = 8;
				} else {
					$p = 0;
				}
			}
			$question_point = 12;
		}
		if ($sub_sec_id == 2) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub_point4 = 0;
			$sub2 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub2->isEmpty()) {
				foreach ($sub2 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point3 = 1;
						} else {
							$sub_point3 = 0;
						}
					}
					if ($question == 4) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point4 = 1;
						} else {
							$sub_point4 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3 + $sub_point4;
				if ($sum_sub_point == '4') {
					$p = 9;
				} else if ($sum_sub_point >= '3' and $sum_sub_point < '4') {
					$p = 6;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == 3) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub3 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub3->isEmpty()) {
				foreach ($sub3 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '8') {
							$sub_point1 = 2;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '6') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2;
				if ($sum_sub_point == '3') {
					$p = 9;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 6;
				} else if ($sum_sub_point >= '1' and $sum_sub_point < '2') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '4' or $sub_sec_id == '6' or $sub_sec_id == '9') {
			$score = 0;
			$sec3_survey4 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey4->isEmpty()) {
				$score = $sec3_survey4[0]->score;
				if ($score == '12') {
					$p = 12;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
			$question_point = 12;
		}
		if ($sub_sec_id == '5') {
			$score = 0;
			$sec3_survey5 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey5->isEmpty()) {
				$score = $sec3_survey5[0]->score;
				if ($score == '12') {
					$p = 9;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '7') {
			$score = 0;
			$sec3_survey5 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey5->isEmpty()) {
				$score = $sec3_survey5[0]->score;
				if ($score == '8') {
					$p = 12;
				} else {
					$p = 0;
				}
			}
			$question_point = 12;
		}
		if ($sub_sec_id == 8) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub3 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub3->isEmpty()) {
				foreach ($sub3 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '8') {
							$sub_point1 = 2;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '6') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2;
				if ($sum_sub_point == '3') {
					$p = 9;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 6;
				} else if ($sum_sub_point >= '1' and $sum_sub_point < '2') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == 10) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub10 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub10->isEmpty()) {
				foreach ($sub10 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 6;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 4;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 2;
						}
					}
				}
				if ($p != '6' or $p != '4' or $p != '2') {
					$p = 0;
				}
			}
			$question_point = 6;
		}
		if ($sub_sec_id == '11' or $sub_sec_id == '12') {
			$score = 0;
			$sec3_survey11 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey11->isEmpty()) {
				$score = $sec3_survey11[0]->score;
				if ($sum_sub_point >= '6') {
					$p = 6;
				} else if ($sum_sub_point >= '4' and $sum_sub_point < '6') {
					$p = 4;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '4') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
			$question_point = 6;
		}

		$sec3_insert = DB::table('mnw_progoti.cal_section_point')->insert(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	} else {
		$id = $sec3[0]->id;
		// dd('update');

		if ($sub_sec_id == 1) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub_point4 = 0;
			$sub_point5 = 0;
			$sub1 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub1->isEmpty()) {
				foreach ($sub1 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '8') {
							$sub_point3 = 1;
						} else {
							$sub_point3 = 0;
						}
					}
					if ($question == 4) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '8') {
							$sub_point4 = 1;
						} else {
							$sub_point4 = 0;
						}
					}
					if ($question == 5) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3 + $sub_point4 + $sub_point5;
				if ($sum_sub_point == '5') {
					$p = 12;
				} else if ($sum_sub_point >= '4' and $sum_sub_point < '5') {
					$p = 10;
				} else if ($sum_sub_point >= '3' and $sum_sub_point < '4') {
					$p = 8;
				} else {
					$p = 0;
				}
			}
			$question_point = 12;
		}
		if ($sub_sec_id == 2) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub_point4 = 0;
			$sub2 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub2->isEmpty()) {
				foreach ($sub2 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point1 = 1;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '12') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point3 = 1;
						} else {
							$sub_point3 = 0;
						}
					}
					if ($question == 4) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '10') {
							$sub_point4 = 1;
						} else {
							$sub_point4 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2 + $sub_point3 + $sub_point4;
				if ($sum_sub_point == '4') {
					$p = 9;
				} else if ($sum_sub_point >= '3' and $sum_sub_point < '4') {
					$p = 6;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == 3) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub3 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub3->isEmpty()) {
				foreach ($sub3 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '8') {
							$sub_point1 = 2;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '6') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2;
				if ($sum_sub_point == '3') {
					$p = 9;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 6;
				} else if ($sum_sub_point >= '1' and $sum_sub_point < '2') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '4' or $sub_sec_id == '6' or $sub_sec_id == '9') {
			$score = 0;
			$sec3_survey4 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey4->isEmpty()) {
				$score = $sec3_survey4[0]->score;
				if ($score == '12') {
					$p = 12;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
			$question_point = 12;
		}
		if ($sub_sec_id == '5') {
			$score = 0;
			$sec3_survey5 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey5->isEmpty()) {
				$score = $sec3_survey5[0]->score;
				if ($score == '12') {
					$p = 9;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '7') {
			$score = 0;
			$sec3_survey5 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey5->isEmpty()) {
				$score = $sec3_survey5[0]->score;
				if ($score == '8') {
					$p = 12;
				} else {
					$p = 0;
				}
			}
			$question_point = 12;
		}
		if ($sub_sec_id == 8) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub3 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub3->isEmpty()) {
				foreach ($sub3 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score == '8') {
							$sub_point1 = 2;
						} else {
							$sub_point1 = 0;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '6') {
							$sub_point2 = 1;
						} else {
							$sub_point2 = 0;
						}
					}
				}
				$sum_sub_point = $sub_point1 + $sub_point2;
				if ($sum_sub_point == '3') {
					$p = 9;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '3') {
					$p = 6;
				} else if ($sum_sub_point >= '1' and $sum_sub_point < '2') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == 10) {
			$sub_point1 = 0;
			$sub_point2 = 0;
			$sub_point3 = 0;
			$sub10 = DB::table('mnw_progoti.survey_data')->select('question')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->groupBy('question')->get();
			if (!$sub10->isEmpty()) {
				foreach ($sub10 as $row) {
					$question = $row->question;
					if ($question == 1) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 6;
						}
					}
					if ($question == 2) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 4;
						}
					}
					if ($question == 3) {
						$checkScore = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('question', $question)->where('sec_no', $secid)->where('sub_id', $sub_sec_id)->get();
						$score = $checkScore[0]->score;
						// dd($score);
						if ($score >= '5') {
							$p = 2;
						}
					}
				}
				if ($p != '6' or $p != '4' or $p != '2') {
					$p = 0;
				}
			}
			$question_point = 6;
		}
		if ($sub_sec_id == '11' or $sub_sec_id == '12') {
			$score = 0;
			$sec3_survey11 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sub_sec_id', $sub_sec_id)->where('sec_no', $secid)->get();
			if (!$sec3_survey11->isEmpty()) {
				$score = $sec3_survey11[0]->score;
				if ($sum_sub_point >= '6') {
					$p = 6;
				} else if ($sum_sub_point >= '4' and $sum_sub_point < '6') {
					$p = 4;
				} else if ($sum_sub_point >= '2' and $sum_sub_point < '4') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
			$question_point = 6;
		}

		$sec3_insert = DB::table('mnw_progoti.cal_section_point')->where('id', $id)->update(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	}
}
function SectionFour($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id)
{
	$question_point = 0;
	$sec4 = DB::table('mnw_progoti.cal_section_point')->where('event_id', $eventid)->where('section', $secid)->where('sub_sec_id', $sub_sec_id)->get();
	if ($sec4->isEmpty()) {
		if (($sub_sec_id == '1') or ($sub_sec_id == '2')) {
			$sec4_survey1 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec4_survey1->isEmpty()) {
				$sec4_survey1_count = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->count();
				$branchcount = ($sec4_survey1_count / 2);
				$fst1 = 0;
				$snd1 = 0;
				$fst2 = 0;
				$snd2 = 0;
				$fst3 = 0;
				$snd3 = 0;
				$fst4 = 0;
				$snd4 = 0;
				$tscore1 = 0;
				$tscore2 = 0;
				$tscore3 = 0;
				$tscore4 = 0;
				$tscore = 0;
				foreach ($sec4_survey3 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst1 = $r->score;
					} else if ($question == '2') {
						$snd1 = $r->score;
					}
					if ($question == '3') {
						$fst2 = $r->score;
					} else if ($question == '4') {
						$snd2 = $r->score;
					}
					if ($question == '5') {
						$fst3 = $r->score;
					} else if ($question == '6') {
						$snd3 = $r->score;
					}
					if ($question == '7') {
						$fst4 = $r->score;
					} else if ($question == '8') {
						$snd4 = $r->score;
					}
				}

				if ($fst1 > 0) {
					$tscore1 = ($snd1 * 100) / $fst1;
				}
				if ($fst2 > 0) {
					$tscore2 = ($snd2 * 100) / $fst2;
				}
				if ($fst3 > 0) {
					$tscore3 = ($snd3 * 100) / $fst3;
				}
				if ($fst4 > 0) {
					$tscore4 = ($snd4 * 100) / $fst4;
				}

				$tscore = round(($tscore1 + $tscore2 + $tscore3 + $tscore4) / $branchcount);

				if ($tscore >= '80') {
					$p = 6;
				} else if ($tscore >= '70' and $tscore <= '79') {
					$p = 4;
				} else if ($tscore >= '60' and $tscore <= '69') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
		}
		if (($sub_sec_id == '3') or ($sub_sec_id == '4')) {
			$sec4_survey3 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();

			// dd($branchcount);
			if (!$sec4_survey3->isEmpty()) {
				$sec4_survey3_count = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->count();
				$branchcount = ($sec4_survey3_count / 2);
				$fst1 = 0;
				$snd1 = 0;
				$fst2 = 0;
				$snd2 = 0;
				$fst3 = 0;
				$snd3 = 0;
				$fst4 = 0;
				$snd4 = 0;
				$tscore1 = 0;
				$tscore2 = 0;
				$tscore3 = 0;
				$tscore4 = 0;
				$tscore = 0;
				foreach ($sec4_survey3 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst1 = $r->score;
					} else if ($question == '2') {
						$snd1 = $r->score;
					}
					if ($question == '3') {
						$fst2 = $r->score;
					} else if ($question == '4') {
						$snd2 = $r->score;
					}
					if ($question == '5') {
						$fst3 = $r->score;
					} else if ($question == '6') {
						$snd3 = $r->score;
					}
					if ($question == '7') {
						$fst4 = $r->score;
					} else if ($question == '8') {
						$snd4 = $r->score;
					}
				}

				if ($fst1 > 0) {
					$tscore1 = ($snd1 * 100) / $fst1;
				}
				if ($fst2 > 0) {
					$tscore2 = ($snd2 * 100) / $fst2;
				}
				if ($fst3 > 0) {
					$tscore3 = ($snd3 * 100) / $fst3;
				}
				if ($fst4 > 0) {
					$tscore4 = ($snd4 * 100) / $fst4;
				}

				$tscore = round(($tscore1 + $tscore2 + $tscore3 + $tscore4) / $branchcount);
				// dd($tscore);

				if ($tscore == '100') {
					$p = 9;
				} else if ($tscore >= '80' and $tscore < '100') {
					$p = 6;
				} else if ($tscore >= '60' and $tscore <= '79') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '5') {
			$sec4_survey5 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec4_survey5->isEmpty()) {
				$sec4_survey5_count = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->count();
				$branchcount = ($sec4_survey5_count / 2);
				$fst1 = 0;
				$snd1 = 0;
				$fst2 = 0;
				$snd2 = 0;
				$fst3 = 0;
				$snd3 = 0;
				$fst4 = 0;
				$snd4 = 0;
				$tscore1 = 0;
				$tscore2 = 0;
				$tscore3 = 0;
				$tscore4 = 0;
				$tscore = 0;
				foreach ($sec4_survey3 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst1 = $r->score;
					} else if ($question == '2') {
						$snd1 = $r->score;
					}
					if ($question == '3') {
						$fst2 = $r->score;
					} else if ($question == '4') {
						$snd2 = $r->score;
					}
					if ($question == '5') {
						$fst3 = $r->score;
					} else if ($question == '6') {
						$snd3 = $r->score;
					}
					if ($question == '7') {
						$fst4 = $r->score;
					} else if ($question == '8') {
						$snd4 = $r->score;
					}
				}

				if ($fst1 > 0) {
					$tscore1 = ($snd1 * 100) / $fst1;
				}
				if ($fst2 > 0) {
					$tscore2 = ($snd2 * 100) / $fst2;
				}
				if ($fst3 > 0) {
					$tscore3 = ($snd3 * 100) / $fst3;
				}
				if ($fst4 > 0) {
					$tscore4 = ($snd4 * 100) / $fst4;
				}

				$tscore = round(($tscore1 + $tscore2 + $tscore3 + $tscore4) / $branchcount);

				if ($tscore >= '95') {
					$p = 6;
				} else if ($tscore >= '80' and $tscore <= '94') {
					$p = 4;
				} else if ($tscore >= '70' and $tscore <= '79') {
					$p = 2;
				} else {
					$p = 0;
				}
			}
			$question_point = 6;
		}

		$sec4_insert = DB::table('mnw_progoti.cal_section_point')->insert(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	} else {
		$id = $sec4[0]->id;

		if (($sub_sec_id == '1') or ($sub_sec_id == '2')) {

			$sec4_survey1 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec4_survey1->isEmpty()) {
				$fst = 0;
				$snd = 0;
				$tscore = 0;
				foreach ($sec4_survey1 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst = $r->score;
					} else if ($question == '2') {
						$snd = $r->score;
					}
				}
				if ($fst > 0) {
					$tscore = ($snd * 100) / $fst;
				}

				if ($tscore >= '80') {
					$p = 6;
				} else if ($tscore >= '70' and $tscore <= '79') {
					$p = 4;
				} else if ($tscore >= '60' and $tscore <= '69') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
		}
		if (($sub_sec_id == '3') or ($sub_sec_id == '4')) {
			$sec4_survey3 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec4_survey3->isEmpty()) {
				$fst = 0;
				$snd = 0;
				$tscore = 0;
				foreach ($sec4_survey3 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst = $r->score;
					} else if ($question == '2') {
						$snd = $r->score;
					}
				}
				if ($fst > 0) {
					$tscore = ($snd * 100) / $fst;
				}

				if ($tscore == '100') {
					$p = 9;
				} else if ($tscore >= '80' and $tscore < '100') {
					$p = 6;
				} else if ($tscore >= '60' and $tscore <= '79') {
					$p = 3;
				} else {
					$p = 0;
				}
			}
			$question_point = 9;
		}
		if ($sub_sec_id == '5') {
			$sec4_survey5 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec4_survey5->isEmpty()) {
				$fst = 0;
				$snd = 0;
				$tscore = 0;
				foreach ($sec4_survey5 as $r) {
					$question = $r->question;
					if ($question == '1') {
						$fst = $r->score;
					} else if ($question == '2') {
						$snd = $r->score;
					}
				}
				if ($fst > 0) {
					$tscore = ($snd * 100) / $fst;
				}

				if ($tscore >= '95') {
					$p = 6;
				} else if ($tscore >= '80' and $tscore <= '94') {
					$p = 4;
				} else if ($tscore >= '70' and $tscore <= '79') {
					$p = 2;
				} else {
					$p = 0;
				}
			}
			$question_point = 6;
		}

		$sec4_insert = DB::table('mnw_progoti.cal_section_point')->where('id', $id)->update(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	}
}
function SectionFive($eventid, $secid, $question, $sub_sec_id, $orgno, $monitorno, $area_id)
{
	$question_point = 0;
	$sec5 = DB::table('mnw_progoti.cal_section_point')->where('area_id', $area_id)->where('event_id', $eventid)->where('section', $secid)->where('sub_sec_id', $sub_sec_id)->get();
	if ($sec5->isEmpty()) {
		if ($sub_sec_id == '1' or $sub_sec_id == '3' or $sub_sec_id == '5' or $sub_sec_id == '9' or $sub_sec_id == '10' or $sub_sec_id == '11') {
			$val = 0;
			//$sec5_survey1 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey1 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey1->isEmpty()) {
				$val = $sec5_survey1[0]->score;
			}
			if ($val == '6') {
				$p = 6;
			} else {
				$p = 0;
			}
			$question_point = 6;
		}
		if ($sub_sec_id == '2') {
			//$sec5_survey2 = DB::select( DB::raw("select * from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey2 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey2->isEmpty()) {
				$ques1 = 0;
				$ques2 = 0;
				$tscore = 0;
				foreach ($sec5_survey2 as $r) {
					$q1 = $r->question;
					if ($q1 == '1') {
						$ques1 =  $r->score;
					} else if ($q1 == '2') {
						$ques2 =  $r->score;
					}
				}
				if ($ques2 > 0) {
					$tscore = ($ques2 * 100) / $ques1;
				}
				if ($tscore >= '80') {
					$p = 6;
				} else if ($tscore >= '65' and $tscore <= '79') {
					$p = 4;
				} else if ($tscore >= '50' and $tscore <= '64') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
		}
		if ($sub_sec_id == '4') {
			$score = 0;
			//$sec5_survey4 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey4 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();

			if (!$sec5_survey4->isEmpty()) {
				$score = $sec5_survey4[0]->score;
				if ($score == '10') {
					$p = 12;
				} else {
					$p = 0;
				}

				$question_point = 12;
			}
		}
		if ($sub_sec_id == '6') {
			$score = 0;
			//$sec5_survey4 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey6 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();

			if (!$sec5_survey6->isEmpty()) {
				$score = $sec5_survey6[0]->score;
				if ($score >= '7') {
					$p = 9;
				} else if ($score <= '6' and $score >= '5') {
					$p = 6;
				} else if ($score >= '4' and $score < '5') {
					$p = 3;
				} else {
					$p = 0;
				}

				$question_point = 9;
			}
		}
		if ($sub_sec_id == '7' or $sub_sec_id == '8' or $sub_sec_id == '12') {
			$val = 0;
			//$sec5_survey1 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey7 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey7->isEmpty()) {
				$val = $sec5_survey7[0]->score;
			}
			if ($val == '9') {
				$p = 9;
			} else {
				$p = 0;
			}
			$question_point = 9;
		}

		$sec5_insert = DB::table('mnw_progoti.cal_section_point')->insert(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	} else {
		$id = $sec5[0]->id;
		if ($sub_sec_id == '1' or $sub_sec_id == '3' or $sub_sec_id == '5' or $sub_sec_id == '9' or $sub_sec_id == '10' or $sub_sec_id == '11') {
			$val = 0;
			//$sec5_survey1 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey1 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey1->isEmpty()) {
				$val = $sec5_survey1[0]->score;
			}
			if ($val == '6') {
				$p = 6;
			} else {
				$p = 0;
			}
			$question_point = 6;
		}
		if ($sub_sec_id == '2') {
			//$sec5_survey2 = DB::select( DB::raw("select * from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey2 = DB::table('mnw_progoti.survey_data')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey2->isEmpty()) {
				$ques1 = 0;
				$ques2 = 0;
				$tscore = 0;
				foreach ($sec5_survey2 as $r) {
					$q1 = $r->question;
					if ($q1 == '1') {
						$ques1 =  $r->score;
					} else if ($q1 == '2') {
						$ques2 =  $r->score;
					}
				}
				if ($ques2 > 0) {
					$tscore = ($ques2 * 100) / $ques1;
				}
				if ($tscore >= '80') {
					$p = 6;
				} else if ($tscore >= '65' and $tscore <= '79') {
					$p = 4;
				} else if ($tscore >= '50' and $tscore <= '64') {
					$p = 2;
				} else {
					$p = 0;
				}
				$question_point = 6;
			}
		}
		if ($sub_sec_id == '4') {
			$score = 0;
			//$sec5_survey4 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey4 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();

			if (!$sec5_survey4->isEmpty()) {
				$score = $sec5_survey4[0]->score;
				if ($score == '10') {
					$p = 12;
				} else {
					$p = 0;
				}

				$question_point = 12;
			}
		}
		if ($sub_sec_id == '6') {
			$score = 0;
			//$sec5_survey4 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey6 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();

			if (!$sec5_survey6->isEmpty()) {
				$score = $sec5_survey6[0]->score;
				if ($score >= '7') {
					$p = 9;
				} else if ($score <= '6' and $score >= '5') {
					$p = 6;
				} else if ($score >= '4' and $score < '5') {
					$p = 3;
				} else {
					$p = 0;
				}

				$question_point = 9;
			}
		}
		if ($sub_sec_id == '7' or $sub_sec_id == '8' or $sub_sec_id == '12') {
			$val = 0;
			//$sec5_survey1 = DB::select( DB::raw("select sum(score) as score from mnw_progoti.survey_data where event_id='$eventid' and sec_no='$secid' and sub_sec_id='$sub_sec_id'"));

			$sec5_survey7 = DB::table('mnw_progoti.survey_data')->selectRaw('sum(score) as score')->where('event_id', $eventid)->where('sec_no', $secid)->where('sub_sec_id', $sub_sec_id)->get();
			if (!$sec5_survey7->isEmpty()) {
				$val = $sec5_survey7[0]->score;
			}
			if ($val == '9') {
				$p = 9;
			} else {
				$p = 0;
			}
			$question_point = 9;
		}

		$sec5_insert = DB::table('mnw_progoti.cal_section_point')->where('id', $id)->update(['area_id' => $area_id, 'event_id' => $eventid, 'section' => $secid, 'point' => $p, 'qno' => $sub_sec_id, 'sub_sec_id' => $sub_sec_id, 'question_point' => $question_point, 'sub_id' => $sub_sec_id]);
	}
}
