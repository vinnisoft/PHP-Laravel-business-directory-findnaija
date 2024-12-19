@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Add Category</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('category.index') }}" class="btn-shadow btn btn-info"><span
                            class="btn-icon-wrapper pr-2 opacity-7"><i
                                class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Category Detail</h5>
                {{ Form::open(['url' => route('category.store'), 'id' => 'addCategoryForm', 'enctype' => 'multipart/form-data']) }}
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('group_id', 'Select Category Group') !!}
                            {!! Form::select('group_id', $categoryGroup, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('cat_icon', 'Icon') !!}
                            {!! Form::file('cat_icon', ['class' => 'form-control', 'id' => 'icon']) !!}
                        </div>
                        <div class="icon my-2 previewIcon"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('cat_graphic_image', 'Graphic Image') !!}
                            {!! Form::file('cat_graphic_image', ['class' => 'form-control', 'id' => 'graphic']) !!}
                        </div>
                        <div class="graphic my-2 previewGraphic"></div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">Service</h5>
                            <button type="button" class="btn btn-info appendServiceSec"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="serviceSection">
                            @include('admin.components.sub-category', ['index' => 1])
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">Options</h5>
                            <button type="button" class="btn btn-info appendOptionSec"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="optionSection">
                            @include('admin.components.options', ['index' => 1])
                        </div>
                    </div>
                </div>
                {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addCategoryFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    @include('admin.category.script')
    <script>
        $("#addCategoryForm").validate({
            rules: {
                name: {
                    required: true
                },
                cat_icon: {
                    required: true
                },
                group_id: {
                    required: true
                },
                cat_graphic_image: {
                    required: false
                }
            },
            messages: {
                name: {
                    required: "Please enter name!"
                },
                cat_icon: {
                    required: "Please choose an icon!"
                },
                group_id: {
                    required: "Please choose a category group!"
                },
                cat_graphic_image: {
                    required: "Please choose a graphic image!"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $('#addCategoryFormBtn').html('Processing');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('category.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#addCategoryFormBtn').html('Add');
                            window.location.href = "{{ route('category.index') }}";
                        } else {
                            $('#addCategoryFormBtn').html('Add');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
