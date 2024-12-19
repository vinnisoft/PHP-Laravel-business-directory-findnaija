@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>Update Category</div>
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
                {{ Form::open(['url' => route('category.update', $category->id), 'id' => 'updateCategoryForm', 'enctype' => 'multipart/form-data']) }}
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('group_id', 'Select Category Group') !!}
                            {!! Form::select('group_id', $categoryGroup, $category->group_id, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('name', 'Name') !!}
                            {!! Form::text('name', $category->name, ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('cat_icon', 'Icon') !!}
                            {!! Form::file('cat_icon', ['class' => 'form-control', 'id' => 'icon']) !!}
                            {!! Form::hidden('icon_path', $category->icon_path) !!}
                        </div>
                        <div class="icon my-2 previewIcon">
                            @if ($category->icon)
                                <img class="shadow p-1" id="" src="{{ $category->icon }}" width="100" height="100" alt="" >
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            {!! Form::label('cat_graphic_image', 'Graphic Image') !!}
                            {!! Form::file('cat_graphic_image', ['class' => 'form-control', 'id' => 'graphic']) !!}
                        </div>
                        <div class="graphic my-2 previewGraphic">
                            @if ($category->graphic_image)
                                <img class="shadow p-1" id="" src="{{ $category->graphic_image }}" width="100" height="100" alt="" >
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">Service</h5>
                            <button type="button" class="btn btn-info appendServiceSec"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="serviceSection">
                            @if (count($subCategories) > 0)
                                @foreach ($subCategories as $subCategory)
                                    @include('admin.components.sub-category', ['index' => $loop->iteration, 'data' => $subCategory])
                                @endforeach
                            @else
                                @include('admin.components.sub-category', ['index' => 1])
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">Options</h5>
                            <button type="button" class="btn btn-info appendOptionSec"><i class="fa fa-plus"></i></button>
                        </div>
                        <div class="optionSection">
                            @if (count($catOptions) > 0)
                                @foreach ($catOptions as $catOption)
                                    @include('admin.components.options', ['index' => $loop->iteration, 'data' => $catOption])
                                @endforeach
                            @else
                                @include('admin.components.options', ['index' => 1])
                            @endif
                        </div>
                    </div>
                </div>
                {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updateCategoryFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    @include('admin.category.script')
    <script>
        $("#updateCategoryForm").validate({
            rules: {
                name: {
                    required: true
                },
                group_id: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "Please enter name!"
                },
                group_id: {
                    required: "Please choose a category group!"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                $('#updateCategoryFormBtn').html('Processing');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: 'POST',
                    url: "{{ route('category.update', $category->id) }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('#updateCategoryFormBtn').html('Add');
                            window.location.href = "{{ route('category.index') }}";
                        } else {
                            toastr.error(data.message);
                            $('#updateCategoryFormBtn').html('Add');
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush
