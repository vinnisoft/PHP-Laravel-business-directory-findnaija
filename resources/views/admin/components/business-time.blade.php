{{-- <div class="row {{ $day }} mb-3">
    <div class="col-md-3">
        {!! Form::label("time[$day][start_time]", "$day Start Time") !!}
        {!! Form::time("time[$day][start_time]", @$data['start_time'], ['class' => 'form-control timePicker startTime', 'data-day' => "$day", 'required' => true, 'max' => '12:00']) !!}
    </div>
    <div class="col-md-3">
        {!! Form::label("time[$day][end_time]", "$day End Time") !!}
        {!! Form::time("time[$day][end_time]", @$data['end_time'], ['class' => "form-control timePicker $day endTime", 'data-day' => "$day", 'required' => true, 'max' => '12:00']) !!}
    </div>
    <div class="col-md-6 mt-1">
        <label class="switch mt-4">
            <input type="checkbox" class="switch-input" name="time[{{$day}}][status]" value="{{@$data['status']}}" {{ @$data['status'] == 1 ? 'checked' : '' }}>
            <span class="switch-label" data-on="On" data-off="Off"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
</div> --}}

<div class="row {{ $day }} mb-3">
    <div class="col-md-2">
        @php
            $startTime = @explode(' ', $data['start_time']);
            $endTime = @explode(' ', $data['end_time']);
        @endphp
        {!! Form::label("time[$day][start_time]", "$day Start Time") !!}
        <div class="d-flex">
            <div>
                {!! Form::select("time[$day][start_time]", times(), @$startTime[0], ['class' => 'form-control startTime', 'data-day' => $day, 'required' => true, 'style' => 'width:80px']) !!}
            </div>&nbsp;&nbsp;
            {!! Form::select("time[$day][start_type]", ['AM' => 'AM', 'PM' => 'PM'], @$startTime[1], ['class' => 'form-control startTime', 'data-day' => "$day", 'required' => true, 'style' => 'width:80px']) !!}
        </div>
    </div>
    <div class="col-md-2">
        {!! Form::label("time[$day][end_time]", "$day End Time") !!}
        <div class="d-flex">
            <div>
                {!! Form::select("time[$day][end_time]", times(), @$endTime[0], ['class' => "form-control $day endTime", 'data-day' => $day, 'required' => true, 'style' => 'width:80px']) !!}
            </div>&nbsp;&nbsp;
            {!! Form::select("time[$day][end_type]", ['AM' => 'AM', 'PM' => 'PM'], @$endTime[1], ['class' => 'form-control startTime', 'data-day' => "$day", 'required' => true, 'style' => 'width:80px']) !!}    </div>
        </div>
    <div class="col-md-2 mt-1">
        <label class="switch mt-4">
            <input type="checkbox" class="switch-input" name="time[{{$day}}][status]" value="{{@$data['status']}}" {{ @$data['status'] == 1 ? 'checked' : '' }}>
            <span class="switch-label" data-on="On" data-off="Off"></span>
            <span class="switch-handle"></span>
        </label>
    </div>
</div>
@push('customScript')
<script>
    $(document).on('change', '.startTime', function() {
        var day = $(this).data('day');
        var time = $(this).val();
        var number = parseInt(time.split(':')[0], 10);        
        $('.'+day+' .endTime option').each(function() {
            var endTimeNumber = parseInt($(this).val().split(':')[0], 10);
            if (endTimeNumber <= number) {
                $(this).prop('disabled', true);
            }
        });
    });
</script>
@endpush