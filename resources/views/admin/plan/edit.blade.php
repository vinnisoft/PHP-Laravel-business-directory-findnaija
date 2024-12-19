@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Update Plan</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('plan.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Plan Detail</h5>
                {{ Form::open(['url' => route('plan.update', $plan->id), 'id' => 'updatePlanForm', 'enctype' => 'multipart/form-data']) }}
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $plan->name, ['class' => 'form-control', 'placeholder' => 'Enter name', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('type', 'Type') !!}
                            {!! Form::text('type', $plan->type, ['class' => 'form-control', 'placeholder' => 'Enter type', 'readonly' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('price', 'Price') !!}
                            {!! Form::text('price', $plan->price, ['class' => 'form-control', 'placeholder' => 'Enter price']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('saving', 'Saving') !!}
                            {!! Form::text('saving', $plan->saving, ['class' => 'form-control', 'placeholder' => 'Enter saving']) !!}
                        </div>
                    </div>
                    {{-- <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('description', 'Description') !!}
                            {!! Form::textarea('description', $plan->description, ['class' => 'form-control', 'placeholder' => 'Enter description', 'id' => 'ckeditor']) !!}
                        </div>
                    </div>--}}
                </div>
                {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updatePlanFormBtn']) !!}
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
        $("#updatePlanForm").validate({
            rules: {
                name: {
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
                $('#updatePlanFormBtn').html('Processing');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('plan.update', $plan->id) }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#updatePlanFormBtn').html('Update');
                            window.location.href = "{{ route('plan.index') }}";
                        } else {
                            $('#updatePlanFormBtn').html('Update');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
