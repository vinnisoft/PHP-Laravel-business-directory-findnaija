@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-culture"></i>
                    </div>
                    <div>Add Language</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('business-language.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>    
        <div class="d-flex justify-content-center">
            <div class="main-card mb-3 card col-12">
                <div class="card-body">
                    <h5 class="card-title">Language Detail</h5>
                    {{ Form::open(['url' => route('business-language.store'), 'id' => 'addLanguageForm']) }}
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    {!! Form::label('name', 'Name') !!}
                                    {!! Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                                </div>
                            </div>                                
                        </div>               
                        {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addLanguageFormBtn']) !!}
                    {!! Form::close() !!}
                </div>
            </div>        
        </div>        
    </div>
@endsection
@push('customScript')
    <script>
        $("#addLanguageForm").validate({
            rules: {
                name: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Please enter name!"
                }
            },
            submitHandler: function(form) {    
                var serliseFromData = $(form).serialize();    
                $('#addLanguageFormBtn').html('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('business-language.store') }}",
                    data : serliseFromData,                    
                    dataType : 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#addLanguageFormBtn').html('Add'); 
                            window.location.href = "{{ route('business-language.index') }}";
                        } else {
                            $('#addLanguageFormBtn').html('Add'); 
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush