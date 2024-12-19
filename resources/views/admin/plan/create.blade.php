@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Add Plan</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('plan.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Plan Detail</h5>
                {{ Form::open(['url' => route('plan.store'), 'id' => 'addPlanForm', 'enctype' => 'multipart/form-data']) }}
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('type', 'Type') !!}
                            {!! Form::text('type', '', ['class' => 'form-control', 'placeholder' => 'Enter type']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('price', 'Price') !!}
                            {!! Form::text('price', '', ['class' => 'form-control', 'placeholder' => 'Enter price']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('saving', 'Saving') !!}
                            {!! Form::text('saving', '', ['class' => 'form-control', 'placeholder' => 'Enter saving']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Enter description', 'id' => 'ckeditor']) !!}
                        </div>
                    </div>               
                </div>                
                {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addPlanFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
<script>
</script>
    @include('admin.plan.script')
    <script>
        $("#addPlanForm").validate({
            rules: {
                name: {
                    required: true
                },
                type: {
                    required: true
                },
                price: {
                    required: true
                },
                saving: {
                    required: true
                },
                description: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "Please enter name!"
                },
                type: {
                    required: "Please enter type!"
                },
                price: {
                    required: "Please enter price!"
                },
                saving: {
                    required: "Please enter saving!"
                },
                description: {
                    required: "Please enter description!"
                },
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $('#addPlanFormBtn').html('Processing');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('plan.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#addPlanFormBtn').html('Add');
                            window.location.href = "{{ route('plan.index') }}";
                        } else {
                            $('#addPlanFormBtn').html('Add');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
