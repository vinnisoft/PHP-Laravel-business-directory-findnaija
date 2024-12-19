@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-graph text-success"></i>
                    </div>
                    <div>Add Favorite Business</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('favorite-business.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-business-time fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>    
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Favorite Business Detail</h5>
                {{ Form::open(['url' => route('favorite-business.store'), 'id' => 'addFavoriteBusinessForm', 'enctype' => 'multipart/form-data']) }}
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                {!! Form::label('key_words[]', 'Select Business') !!}
                                <div class="select-with-icon">
                                    {!! Form::select('business_id', $businesses, null, ['class' => 'form-control keyWords', 'data-error' => 'FavoriteBusiness']) !!}
                                </div>
                                <label class="FavoriteBusiness"></label>
                            </div>
                            <div class="position-relative form-group">
                                {!! Form::label('images[]', 'images') !!}
                                {!! Form::file('images[]', ['class' => 'form-control', 'id' => 'images', 'multiple' => 'multiple']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                {!! Form::label('description', 'Business Description') !!}
                                {!! Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Enter Business description', 'rows' => 5]) !!}
                            </div>
                        </div>
                        <div class="col-md-12 d-none images"></div>
                    </div>               
                    {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addFavoriteBusinessFormBtn']) !!}
                {!! Form::close() !!}
            </div>
        </div>      
    </div>
@endsection
@push('customScript')
    <script>
        $(document).on('change', '#images', function(e) {
            var files = e.target.files;
            if (files.length > 0) {
                $('.images').removeClass('d-none');
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.images').append('<img class="preview-images" src="' + e.target.result + '" />');
                    };
                    reader.readAsDataURL(files[i]);
                }
            } else {
                $('.images').addClass('d-none');
            }
        });
        $("#addFavoriteBusinessForm").validate({
            rules: {
                business_id: {
                    required: true
                },
                description: {
                    required: true
                },
                "images[]": {
                    required: true
                }
            },
            messages: {
                business_id: {
                    required: "Please select business!"
                },
                description: {
                    required: "Please enter description!"
                },
                "images[]": {
                    required: "Please select image!"
                },
            },
            submitHandler: function(form) {    
                var formData = new FormData(form);
                $('#addFavoriteBusinessFormBtn').html('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('favorite-business.store') }}",
                    data : formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#addFavoriteBusinessFormBtn').html('Add'); 
                            window.location.href = "{{ route('favorite-business.index') }}";
                        } else {
                            $('#addFavoriteBusinessFormBtn').html('Add'); 
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush