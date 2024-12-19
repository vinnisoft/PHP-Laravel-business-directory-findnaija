@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-graph text-success"></i>
                    </div>
                    <div>Add Interest</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('interest.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-business-time fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>    
        <div class="d-flex justify-content-center">
            <div class="main-card mb-3 card col-6">
                <div class="card-body">
                    <h5 class="card-title">Interest Detail</h5>
                    {{ Form::open(['url' => route('interest.store'), 'id' => 'addInterestForm']) }}
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    {!! Form::label('name', 'Name') !!}
                                    {!! Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                                </div>
                            </div>                                
                        </div>               
                        {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addInterestFormBtn']) !!}
                    {!! Form::close() !!}
                </div>
            </div>        
        </div>        
    </div>
@endsection
@push('customScript')
    <script>
        $("#addInterestForm").validate({
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
                $('#addInterestFormBtn').html('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('interest.store') }}",
                    data : serliseFromData,                    
                    dataType : 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#addInterestFormBtn').html('Add'); 
                            window.location.href = "{{ route('interest.index') }}";
                        } else {
                            $('#addInterestFormBtn').html('Add'); 
                            $('#errorMsg').html(data.msg);
                        }
                    }
                });
            }
        });
    </script>
@endpush