@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Add Custom Notification</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('custom-notification.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Custom Notification Detail</h5>
                {{ Form::open(['url' => route('custom-notification.store'), 'id' => 'addCustomNotificationForm', 'enctype' => 'multipart/form-data']) }}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('subject', 'Subject') !!}
                            {!! Form::text('subject', '', ['class' => 'form-control', 'placeholder' => 'Enter subject']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('message', 'Message') !!}
                            {!! Form::textarea('message', '', ['class' => 'form-control', 'placeholder' => 'Enter message', 'rows' => 5]) !!}
                        </div>
                    </div>
                    <div class="col-md-6 d-flex">
                        <div class="position-relative form-group mr-4">
                            {!! Form::checkbox('type[]', 'fcm', '', ['class' => '', 'id' => 'type']) !!}
                            {!! Form::label('type[]', 'Push Notification') !!}
                        </div>
                        <div class="position-relative form-group">
                            {!! Form::checkbox('type[]', 'mail', '', ['class' => '', 'id' => 'mail']) !!}
                            {!! Form::label('type[]', 'Email') !!}
                        </div>
                    </div>
                </div>                
                {!! Form::submit('Send', ['class' => 'mt-2 btn btn-primary', 'id' => 'addCustomNotification']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    <script>
        $("#addCustomNotificationForm").validate({
            rules: {
                subject: {
                    required: true
                },
                message: {
                    required: true
                }
            },
            messages: {
                subject: {
                    required: "Please enter subject!"
                },
                message: {
                    required: "Please enter message!"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $('#addCustomNotification').html('Processing');
                $('#addCustomNotification').attr('disabled', true);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('custom-notification.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#addCustomNotification').html('Sent');
                            window.location.href = "{{ route('custom-notification.index') }}";
                        } else {
                            $('#addCustomNotification').html('Send');
                            $('#addCustomNotification').attr('disabled', false);
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
