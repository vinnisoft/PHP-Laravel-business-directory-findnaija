@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Update Category Group</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('category-group.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Category Group Detail</h5>
                {{ Form::open(['url' => route('category-group.update', $categoryGroup->id), 'id' => 'updateCategoryGroupForm', 'enctype' => 'multipart/form-data']) }}
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $categoryGroup->name, ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updateCategoryGroupFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    @include('admin.category-group.script')
    <script>
        $("#updateCategoryGroupForm").validate({
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
                $('#updateCategoryGroupFormBtn').html('Processing');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('category-group.update', $categoryGroup->id) }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#updateCategoryGroupFormBtn').html('Add');
                            window.location.href = "{{ route('category-group.index') }}";
                        } else {
                            toastr.error(data.message);
                            $('#updateCategoryGroupFormBtn').html('Add');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
