@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="fa fa fa-user-plus text-success"></i>
                    </div>
                    <div>Update Sub Admin</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('admins.index') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>        
        <div class="tab-content">
            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Sub Admin Detail</h5>
                        {{ Form::open(['url' => route('admins.update', $user->id), 'id' => 'updateSubAdminForm']) }}
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('first_name', 'First Name') !!}
                                        {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'placeholder' => 'Enter first name']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('last_name', 'Last Name') !!}
                                        {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => 'Enter last name']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('email', 'Email') !!}
                                        {!! Form::email('email', $user->email, ['class' => 'form-control', 'placeholder' => 'Enter email', 'disabled' => true]) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('password', 'Password') !!}
                                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter password']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('confirm_password', 'Confirm Password') !!}
                                        {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => 'Enter confirm password']) !!}                                        
                                    </div>
                                </div>                        
                            </div>
                            <h5 class="card-title mt-3">Sub Admin Permissions</h5>
                            <div class="position-relative form-group">
                                {!! Form::checkbox('', 'yes', $user->hasAllPermissions(getAllRouteNames()), ['id' => "allPermissions"]) !!}
                                {!! Form::label("allPermissions", 'Select All') !!}
                            </div>
                            <hr>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            {!! Form::checkbox('permission[]', $permission->name, $user->hasPermissionTo($permission->name), ['id' => "permission$permission->id", 'class' => 'permission']) !!}
                                            {!! Form::label("permission$permission->id", str_replace('.', ' ', ucfirst($permission->name))) !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updateSubAdminFormBtn']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>                
            </div>            
        </div>
    </div>
@endsection
@push('customScript')
    <script>
        $(document).on('change', '#allPermissions', function() {
            if ($(this).is(":checked")) {
                $('.permission').prop('checked', true);
            } else {
                $('.permission').prop('checked', false);
            }
        });

        $(document).on('change', '.permission', function() {
            var checkedPermission = $(".permission:checkbox:checked").map(function(){
                return $(this).val();
            }).get();
            var totalPermissions = "{{ count(getAllRouteNames()) }}";
            if (checkedPermission.length == totalPermissions) {
                $('#allPermissions').prop('checked', true);
            } else {
                $('#allPermissions').prop('checked', false);
            }
        });

        $("#updateSubAdminForm").validate({
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
                password: {
                    minlength: {
                        depends: function(element) {
                            return $("#password").val().length > 0;
                        },
                        param: 8
                    },
                    strongPassword: {
                        depends: function(element) {
                            return $("#password").val().length > 0;
                        }
                    }
                },
                confirm_password: {
                    required: {
                        depends: function(element) {
                            return $("#password").val().length > 0;
                        }
                    },
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
                $('#updateSubAdminFormBtn').val('Processing');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'PUT',
                    url : "{{ route('admins.update', $user->id) }}",
                    data : serliseFromData,                    
                    dataType : 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#updateSubAdminFormBtn').val('Add');
                            toastr.success(data.message);
                            window.location.href = "{{ route('admins.index') }}";
                        } else {
                            $('#updateSubAdminFormBtn').val('Update');
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });
        
        $.validator.addMethod("strongPassword", function (value, element) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(value);
        }, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");
    </script>
@endpush