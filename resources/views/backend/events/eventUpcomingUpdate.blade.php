@extends('backend.layouts.master')

@section('title','Event Update')
@section('style')
<style>
    /* .multiselect {
  width: 200px;
} */

    .selectBox {
        position: relative;
    }

    .selectBox select {
        width: 100%;
        font-weight: bold;
    }

    .overSelect {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    #checkboxes {
        display: none;
        border: 1px #dadada solid;
    }

    #checkboxes label {
        display: block;
    }

    #checkboxes label:hover {
        background-color: #1e90ff;
    }

</style>
<link href="{{ asset('ui/backend/js/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Upcoming Event Update</h5>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card card-custom">
                {{-- <div class="card-header flex-wrap py-5">
            <div class="card-title">
              <h3 class="card-label">Form </h3>
            </div>
        </div> --}}
                <div class="card-body">
                    <!--begin: Datatable-->
                    <div class="row">
                        <div class="col-md-8 col-xs-12 col-sm-12 offset-md-2">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin-bottom: 0rem;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (Session::has('success'))
                            <div class="alert alert-success" role="success">
                                {{ Session::get('success') }}
                            </div>
                            @endif
                            @if (Session::has('error'))
                            <div class="alert alert-danger" role="success">
                                {{ Session::get('error') }}
                            </div>
                            @endif
                            <form action="{{route('event.update')}}" method="post" onsubmit="return confirm('Are you sureyou want to submit?');">
                            @csrf
                            <input type="hidden" value="{{ $Event->id }}" name="id">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Event Cycle</label>
                                        <select name="cycle" class="form-control">
                                     <?php
                                         $date = date('Y');
                                         $d = $date+1;
                                         for ($i=1; $i < 3; $i++)
                                        {
                                        ?>
                                            <option value="<?php echo $date."-Cycle-".$i; ?>" {{ $date."-Cycle-".$i == $Event->event_cycle  ? 'selected' : ''}}><?php echo $date."-Cycle-".$i; ?></option>
                                        <?php
                                        }
                                        for ($j=1; $j < 3; $j++) {
                                        ?>
                                            <option value="<?php echo $d."-Cycle-".$j; ?>" {{ $date."-Cycle-".$i == $Event->event_cycle  ? 'selected' : ''}}><?php echo $d."-Cycle-".$j; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Area Code</label>
                                        {{-- <select class="form-control area" id="FormControlSelect1" name="area_id" required> --}}
                                        <select class="form-control area select2" id="FormControlSelect1" name="area_id" >
                                            <option value="">Select area</option>
                                            @foreach ($data as $item)
                                            <option value="{{ $item->area_id }}" {{ $item->area_id == $Event->area_id  ? 'selected' : ''}}>{{ $item->area_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <div class="multiselect">
                                            <div class="selectBox" onclick="showCheckboxes()">
                                                <label for="exampleFormControlSelect1">Select Branch</label>
                                                <select class="form-control" id="FormControlSelect2">
                                                    <option value="" selected>Selected Branch*</option>
                                                </select>
                                                <div class="overSelect"></div>
                                            </div>
                                            <div id="checkboxes" style="display: block">
                                                @foreach ($branches as $branch)
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox"  id="division_id"  checked  name="branch_id[]" value="{{ $branch['branch_id'] }}" onclick="return false">
                                                        {{ $branch['branch_name'] }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="validationCustom01">Date Start</label>
                                            <input type="date" class="form-control" id="validationCustom01"
                                                value="{{ $Event->datestart }}" name="datestart" >
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="validationCustom02">Date End</label>
                                            <input type="date" class="form-control" id="validationCustom02"
                                                value="{{ $Event->dateend }}" name="dateend"  >
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="formGroupExampleInput">Monitor 1</label>
                                        <input type="text" class="form-control" id="formGroupExampleInput" name="monitor1" value="{{ $Event->monitor1_code }}" autocomplete="off" >
                                    </div>
                                    <div class="form-group">
                                        <label for="formGroupExampleInput2">Monitor 2</label>
                                        <input type="text" class="form-control" id="formGroupExampleInput2" name="monitor2" value="{{ $Event->monitor2_code }}" autocomplete="off" >
                                    </div>
                                </div><!-- /.box-body -->


                                <div class="box-footer">
                                    <button type="submit" class="btn btn-secondary btn-block">Update</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
</div>
@endsection

@section('script')
<script>
    // multiple selection
    var expanded = false;

    function showCheckboxes() {
        var checkboxes = document.getElementById("checkboxes");
        if (!expanded) {
            checkboxes.style.display = "block";
            expanded = true;
        } else {
            checkboxes.style.display = "none";
            expanded = false;
        }
    }



    // dependency dropdown
    $(document).ready(function () {
        $('#FormControlSelect1').on('change', function () {
            let id = $(this).val();
            $('#FormControlSelect2').empty();
            $('#FormControlSelect2').append(
                `<option value="" disabled selected>Processing...</option>`);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: 'fetch/' + id,
                success: function (response) {
                    var response = JSON.parse(response);
                    console.log(response);
                    $('#FormControlSelect2').empty();
                    $('#FormControlSelect2').append(
                        `<option value="" disabled selected>Select Sub Category*</option>`
                        );
                    $('#checkboxes').empty();
                    var count = 0;
                    response.forEach(element => {
                        count += 1;
                    });
                    if (count < 5) {
                        response.forEach(element => {
                            $('#checkboxes').append(`<label class="checkbox-inline">
                    <input type="checkbox"  id="division_id"  checked  name="branch_id[]" value="${element['branch_id']}" onclick="return false">
                    ${element['branch_name']}
                                </label>
                                `);
                        });

                    } else {
                        response.forEach(element => {
                            $('#checkboxes').append(`<label class="checkbox-inline">
                    <input type="checkbox" id="division_id"  name="branch_id[]" value="${element['branch_id']}">
                    ${element['branch_name']}
                                </label>
                                `);
                        });
                    }
                }
            });
        });
    });
    // maximum 4 select 
    $(document).ready(function () {
        $('.select2').select2();
        $(document).on('click', 'input[type=checkbox]', function () {
            if ($('input[type=checkbox]:checked').length > 4) {
                $(this).prop('checked', false);
                alert("allowed only 4");
            }
        });
    });

</script>
<script src="{{ asset('ui/backend/js/select2/js/select2.min.js') }}"></script>
@endsection
