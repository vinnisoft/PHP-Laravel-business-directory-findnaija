@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-add-user"></i>
                    </div>
                    <div>Add User</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('users.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>        
        <div class="tab-content">
            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">User Detail</h5>
                        {{ Form::open(['url' => route('users.store'), 'id' => 'addUserForm']) }}
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('first_name', 'First Name') !!}
                                        {!! Form::text('first_name', '', ['class' => 'form-control', 'placeholder' => 'Enter first name']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('last_name', 'Last Name') !!}
                                        {!! Form::text('last_name', '', ['class' => 'form-control', 'placeholder' => 'Enter last name']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('email', 'Email') !!}
                                        {!! Form::email('email', '', ['class' => 'form-control', 'placeholder' => 'Enter email']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('country', 'Country') !!}
                                        {!! Form::text('country', '', ['class' => 'form-control', 'placeholder' => 'Enter country']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('phone_number', 'Phone Number') !!}
                                        {!! Form::text('phone_number', '', ['class' => 'form-control', 'placeholder' => 'Enter phone number']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('dob', 'Date of Birth') !!}
                                        {!! Form::date('dob', '', ['class' => 'form-control', 'placeholder' => 'Enter Date of Birth']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('password', 'Password') !!}
                                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter password']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('confirm_password', 'Confirm Password') !!}
                                        {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => 'Enter confirm password']) !!}                                        
                                    </div>
                                </div>                        
                            </div>                            
                            {!! Form::submit('Add', ['class' => 'mt-2 btn btn-primary', 'id' => 'addUserFormBtn']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>                
            </div>            
        </div>
    </div>
@endsection
@push('customScript')
    <script>
        $("#addUserForm").validate({
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                email: {
                    required: true
                },
                country: {
                    required: true
                },
                dob: {
                    required: true
                },
                phone_number: {
                    required: true,
                    number: true
                },
                password: {
                    required: true,
                    minlength: 8,
                    strongPassword: true
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                },
            },
            messages: {
                first_name: {
                    required: "Please enter first name!"
                },
                last_name: {
                    required: "Please enter last name!"
                },
                email: {
                    required: "Please enter email!"
                },
                country: {
                    required: "Please enter country!"
                },
                dob: {
                    required: "Please enter date of birth!"
                },
                phone_number: {
                    required: "Please enter phone number!",
                    number: "Please enter digits only!"
                },
                password: {
                    required: "Please enter password!",
                    minlength: "Password must be at least 8 characters long",
                    strongPassword: "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character"                    
                },
                confirm_password: {
                    required: "Please enter confirm password!",
                    equalTo: "Password and confirm password match be matched!"                   
                },
            },
            submitHandler: function(form) {    
                var serliseFromData = $(form).serialize();    
                $('#addUserFormBtn').val('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('users.store') }}",
                    data : serliseFromData,                    
                    dataType : 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#addUserFormBtn').val('Add'); 
                            toastr.success(data.message);
                            window.location.href = "{{ route('users.index') }}";
                        } else {
                            $('#addUserFormBtn').val('Add'); 
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });

        $.validator.addMethod("strongPassword", function (value, element) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d])/.test(value);
        }, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");
    </script>
@endpush