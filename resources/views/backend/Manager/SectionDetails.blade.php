@extends('backend.layouts.master')

@section('title','Section Details')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <!--begin::Subheader-->
  <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
      <!--begin::Info-->
      <div class="d-flex align-items-center flex-wrap mr-2">
        <!--begin::Page Title-->
      <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Section Details</h5>
      </div>
      <!--end::Info-->
    </div>
  </div>
  <!--end::Subheader-->
  <!--begin::Entry-->
  <div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
      <!--begin::Dashboard-->
      <!--begin::Row-->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-custom gutter-b">
            <?php
            $sectioname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no=$sec and sub_sec_no='0' and qno=0"));
            if(!empty($sectioname))
            {
               $secname =  $sectioname[0]->qdesc;
            }
            else
            {
             $secname ='';
            }
            $subsectioname = DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subsec' and qno=0"));
            if(!empty($subsectioname))
            {
               $subsecname =  $subsectioname[0]->qdesc;
            }
            else
            {
             $subsecname ='';
            }
           ?>
            <!--begin::Form-->
            <div class="card-body">
              <p >Section <?php echo $sec." : ".$secname; ?>  </p>
              <p >Sub Section <?php echo $subsec." : ".$subsecname;  ?> </p>
              <div class=" table-responsive">
				<table style="text-align: center;font-size:13" style="font-size: 13" class="table" cellspacing="0" width="100%">
				
				<tr class="brac-color-pink">
					<th>SL</th>
					<?php
						if($sec!='4' and $sec!='5'){
						// 	if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
					?>
						<th>Branch Code</th>
						<th>Member No</th>
						<th>Member Name</th>
					<?php
						if(($sec=='2' and $subsec=='1') or ($sec=='2' and $subsec=='2') or ($sec=='2' and $subsec=='3') or ($sec=='2' and $subsec=='5') or ($sec=='1' and $subsec=='9') or ($sec=='1' and $subsec=='10') or ($sec=='3' and $subsec=='1') or ($sec=='3' and $subsec=='2') or ($sec=='3' and $subsec=='3') or ($sec=='3' and $subsec=='8')){
						?>
							<th>Question</th>
						<?php
						}}
						if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
						// 	if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
					?>
						<th>Branch Code</th>
						<th>Member No</th>
						<th>Member Name</th>
					<?php
					}
					?>
					<th>Marks</th>
					<th>Answer</th>
				</tr>
				
				<tbody>
				<?php 
				$name ='';
				$p=0;
				$qp =0;
				$id = 1;
				$memname ='';
				foreach ($survey_data as $row) 
				{
					$branchcode = $row->branchcode;
					if($branchcode){
						$branchcode=str_pad($branchcode, 4, "0", STR_PAD_LEFT);
					}
					$orgmemno = $row->orgmemno;
					$sec_no = $row->sec_no;
					$question = $row->question;
					$event= $row->event_id;
					$sub_id = $row->sub_id;
					//echo $event."/".$org."/".$orgmemno."/".$sec_no."*".$question."--";

					$membername = DB::select(DB::raw("select * from mnw_progoti.respondents where eventid='$event' and branchcode='$branchcode' and orgmemno='$orgmemno'"));
					// dd($membername);

					if(!empty($membername))
					{
						$memname = $membername[0]->membername;
					}
					?>
						<tr>
						<td><?php echo $id; ?></td>
						<?php 
							if($sec!='4' and $sec!='5'){
							// 	if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
						?>
						<td><?php echo $row->branchcode;?></td>
						<td><?php echo $row->orgmemno;?></td>
						<td><?php echo $memname;?></td>
						<?php
						if(($sec=='2' and $subsec=='1') or ($sec=='2' and $subsec=='2') or ($sec=='2' and $subsec=='3') or ($sec=='2' and $subsec=='5') or ($sec=='1' and $subsec=='9') or ($sec=='1' and $subsec=='10') or ($sec=='3' and $subsec=='1') or ($sec=='3' and $subsec=='2') or ($sec=='3' and $subsec=='3') or ($sec=='3' and $subsec=='8')){
							$question= DB::select(DB::raw("select * from mnw_progoti.def_questions where sec_no='$sec' and sub_sec_no='$subsec' and qno=$question"));
							$question_name=$question[0]->qdesc;
							// dd($question[0]->qdesc);
						?>
						<td>{{ $question_name }}</td>
						<?php
							}} 
							?>
						<?php 
							if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
							// 	if(($sec=='5' and $subsec=='4') or ($sec=='5' and $subsec=='6')){
						?>
						<td><?php echo $row->branchcode;?></td>
						<td><?php echo $row->orgmemno;?></td>
						<td><?php echo $memname;?></td>
						<?php
							}
							?>

						<td><?php echo $row->score;?></td>
						<td><?php
							if(($sec_no =='1' and $sub_id=='5') or ($sec_no =='1' and $sub_id=='6') or ($sec_no =='1' and $sub_id=='7') or ($sec_no =='1' and $sub_id=='8'))
							{
								if($row->score =='1')
								{
									echo "Full Match";
								}
								else if($row->score =='0')
								{
									echo "Partial Match";
								}
							}elseif ($sec_no =='3' and $sub_id=='10') {
								if($row->answer=='1')
								{
									echo "Within 30mints";
								}
								else if($row->answer=='0')
								{
									echo "30-60 mints";
								}
								else if($row->answer=='2')
								{
									echo "More than an hour";
								}
							}else
							{
								if($row->score =='0')
								{
									echo "No";
								}
								else if($row->score =='1')
								{
									echo "Yes";
								}
								else
								{
									echo $row->score;
								}
							}
						
						?></td>
						</tr>
					<?php
					$id++;
				
				}
				?>
				</tbody>
			</table>
	   
            </div>
            </div>
            <!--end::Form-->
          </div>
          <!--end::Advance Table Widget 4-->
        </div>
      </div>
      <!--end::Row-->
      <!--begin::Row-->
      
      <!--end::Row-->
      <!--end::Dashboard-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Entry-->
</div>
    
@endsection

@section('script')

@endsection