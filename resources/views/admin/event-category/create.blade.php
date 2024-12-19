@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Add Event Category</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('event-category.index') }}" class="btn-shadow btn btn-info"><span
                            class="btn-icon-wrapper pr-2 opacity-7"><i
                                class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Event Category Detail</h5>
                {{ Form::open(['url' => route('event-category.store'), 'id' => 'addEventCategoryForm', 'enctype' => 'multipart/form-data']) }}
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                        </div>
                    </div>                    
                </div>                
                {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addEventCategoryFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    @include('admin.event-category.script')
    <script>
        $("#addEventCategoryForm").validate({
            rules: {
                name: {
                    required: true
                },                
            },
            messages: {
                name: {
                    required: "Please enter name!"
                },               
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $('#addEventCategoryFormBtn').html('Processing');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('event-category.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#addEventCategoryFormBtn').html('Add');
                            window.location.href = "{{ route('event-category.index') }}";
                        } else {
                            $('#addEventCategoryFormBtn').html('Add');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
