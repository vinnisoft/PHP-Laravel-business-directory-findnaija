@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-graph text-success"></i>
                    </div>
                    <div>Update Favorite Business</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('favorite-business.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-business-time fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>    
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">Favorite Business Detail</h5>
                {{ Form::open(['url' => route('favorite-business.update', $favoriteBusiness->id), 'id' => 'updateFavoriteBusinessForm', 'enctype' => 'multipart/form-data']) }}
                @method('PUT')
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="position-relative form-group">
                                {!! Form::label('key_words[]', 'Select Business') !!}
                                <div class="select-with-icon">
                                    {!! Form::select('business_id', $businesses, $favoriteBusiness->business_id, ['class' => 'form-control keyWords', 'data-error' => 'FavoriteBusiness']) !!}
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
                                {!! Form::textarea('description', $favoriteBusiness->description, ['class' => 'form-control', 'placeholder' => 'Enter Business description', 'rows' => 5]) !!}
                            </div>
                        </div>
                        <div class="col-md-12 {{ count($favoriteBusiness->photos) > 0 ? '' : 'd-none' }} images">
                            @if (count($favoriteBusiness->photos) > 0)
                                @foreach ($favoriteBusiness->photos as $photo)
                                    <img class="preview-images" src="{{ $photo->photo }}" alt="">
                                @endforeach
                            @endif                            
                        </div>
                    </div>               
                    {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updateFavoriteBusinessFormBtn']) !!}
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
        $("#updateFavoriteBusinessForm").validate({
            rules: {
                business_id: {
                    required: true
                },
                description: {
                    required: true
                },
                "images[]": {
                    required: false
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
                $('#updateFavoriteBusinessFormBtn').html('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('favorite-business.update', $favoriteBusiness->id) }}",
                    data : formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#updateFavoriteBusinessFormBtn').html('Update'); 
                            window.location.href = "{{ route('favorite-business.index') }}";
                        } else {
                            $('#updateFavoriteBusinessFormBtn').html('Update'); 
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush