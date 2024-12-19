@extends('admin.layout.master')
@push('customLink')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>

    </style>
@endpush
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-culture"></i>
                    </div>
                    <div>Add Business</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ url()->previous() }}?type=all" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>        
        <div class="tab-content">
            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Business Detail</h5>
                        {{ Form::open(['url' => route('business.store'), 'id' => 'addBusinessForm', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-row">     
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('user_id', 'User') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('user_id', $users, @$recomendedBusiness->user_id, ['class' => 'form-control', 'placeholder' => 'Select User', 'data-error' => 'user_id']) !!}
                                            <i class="fa fa-angle-down"></i>                                            
                                        </div>
                                        <label class="user_id"></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('type', 'Business Type') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('type', businessType(), @$recomendedBusiness->type, ['class' => 'form-control', 'data-error' => 'type']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="type"></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('status_on', 'Status on App') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('status_on', businessStatusOn(), '', ['class' => 'form-control', 'data-error' => 'statusOn']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="statusOn"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('address', 'Business Address') !!}
                                        {!! Form::text('address', @$recomendedBusiness->business_address, ['class' => 'form-control', 'id' => 'address', 'placeholder' => 'Enter Address']) !!}
                                        {!! Form::hidden('latitude', @$recomendedBusiness->latitude, ['id' => 'latitude']) !!}
                                        {!! Form::hidden('longitude', @$recomendedBusiness->longitude, ['id' => 'longitude']) !!}
                                        {!! Form::hidden('continent', @$recomendedBusiness->continent, ['id' => 'continent']) !!}
                                        {!! Form::hidden('state', @$recomendedBusiness->state, ['id' => 'state']) !!}
                                        {!! Form::hidden('city', @$recomendedBusiness->city, ['id' => 'city']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('country', 'Country') !!}
                                        {!! Form::text('country',  @$recomendedBusiness->country, ['class' => 'form-control', 'id' => 'country', 'placeholder' => 'Enter country']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('name', 'Business Name') !!}
                                        {!! Form::text('name',  @$recomendedBusiness->business_name, ['class' => 'form-control', 'placeholder' => 'Enter Business Name']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('category', 'Business Category') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('category', $categories, null, ['class' => 'form-control categories', 'placeholder' => 'Select category', 'data-error' => 'category']) !!}
                                            <i class="fa fa-angle-down"></i>                                            
                                        </div>
                                        <label class="category"></label>
                                    </div>                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('options[]', 'Features') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('options[]', [], null, ['class' => 'form-control options', 'multiple' => 'multiple', 'data-error' => 'optionsError']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="optionsError"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('service[]', 'Service') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('service[]', [], null, ['class' => 'form-control removeValidation serviceDropdown subCategory', 'multiple' => 'multiple', 'data-error' => 'service']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <span class="service"></span>
                                    </div>
                                </div>                                                                
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between" style="width: 36%">
                                        {!! Form::label('', 'Select Days') !!}
                                        <div class="position-relative form-group">
                                            {!! Form::checkbox('', 'yes', '', ['id' => "allDays"]) !!}
                                            {!! Form::label("allDays", 'Select All') !!}
                                        </div>
                                    </div>
                                    <div class="mt-1" style="display: flex; justify-content:left">
                                        <div class="mr-4">
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'sunday']) !!}
                                            {!! Form::label("sunday", 'Sunday') !!}
                                        </div>
                                        <div class="mr-4">
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'monday']) !!}
                                            {!! Form::label("monday", 'Monday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'tuesday']) !!}
                                            {!! Form::label("tuesday", 'Tuesday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'wednesday']) !!}
                                            {!! Form::label("wednesday", 'Wednesday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'thursday']) !!}
                                            {!! Form::label("thursday", 'Thursday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'friday']) !!}
                                            {!! Form::label("friday", 'Friday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', '', ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'saturday']) !!}
                                            {!! Form::label("saturday", 'Saturday') !!}
                                        </div>
                                    </div>
                                    <label class="weekDaysError"></label>
                                    <div class="mt-2 mr-4 days"></div>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="position-relative form-group">
                                        {!! Form::label('detail', 'Business Detail') !!}
                                        {!! Form::textarea('detail', @$recomendedBusiness->detail, ['class' => 'form-control', 'placeholder' => 'Enter Business Detail', 'rows' => 5]) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('email', 'Business Email') !!}
                                        {!! Form::text('email', '', ['class' => 'form-control', 'placeholder' => 'Enter Business Email']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('buss_phone_number', 'Business Phone Number') !!}
                                        <div class="d-flex">
                                            <select name="buss_phone_code" id="bussPhoneCode" class="form-control" style="width: 80px">
                                                @foreach ($codes as $code)
                                                    <option value="{{ $code['dial_code'] }}">{{ $code['dial_code'] }}</option>
                                                @endforeach
                                            </select>
                                            {!! Form::text('buss_phone_number', @$recomendedBusiness->business_phone, ['class' => 'form-control', 'placeholder' => 'Enter Business Phone Number']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('languages[]', 'Languages') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('languages[]', $languages, null, ['class' => 'form-control removeValidation langDropdown', 'multiple' => 'multiple', 'data-error' => 'languages']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <span class="languages"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('key_words[]', 'Business Key Words') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('key_words[]', [], null, ['class' => 'form-control keyWords', 'multiple' => 'multiple', 'data-error' => 'keyWordsError']) !!}
                                        </div>
                                        <label class="keyWordsError"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('website', 'Website') !!}
                                        {!! Form::text('website', @$recomendedBusiness->website, ['class' => 'form-control', 'placeholder' => 'Enter Website']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_first_name', 'Owner’s Firstname') !!}
                                        {!! Form::text('owner_first_name', '', ['class' => 'form-control', 'placeholder' => 'Enter Owner’s Firstname']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_last_name', 'Owner’s Lastname') !!}
                                        {!! Form::text('owner_last_name', '', ['class' => 'form-control', 'placeholder' => 'Enter Owner’s Lastname']) !!}                                        
                                    </div>
                                </div>                                
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_phone_number', 'Owner Phone Number') !!}
                                        <div class="d-flex">
                                            <select name="owner_phone_code" id="ownerPhoneCode" class="form-control" style="width: 80px">
                                                @foreach ($codes as $code)
                                                    <option value="{{ $code['dial_code'] }}">{{ $code['dial_code'] }}</option>
                                                @endforeach
                                            </select>
                                            {!! Form::text('owner_phone_number', '', ['class' => 'form-control', 'placeholder' => 'Enter Owner Phone Number']) !!}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('images[]', 'Business images') !!}
                                        {!! Form::file('images[]', ['class' => 'form-control', 'id' => 'images', 'data-type' => 'images', 'multiple' => 'multiple']) !!}
                                    </div>
                                    <div class="d-none d-flex businessimages" style="flex-wrap: wrap;"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('business_logo', 'Business logo') !!}
                                        {!! Form::file('business_logo', ['class' => 'form-control', 'id' => 'images', 'data-type' => 'logo']) !!}
                                    </div>
                                    <div class="d-none d-flex businesslogo"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('nationald', 'National Id') !!}
                                        {!! Form::file('nationald', ['class' => 'form-control']) !!}
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('business_reg', 'Business Registration') !!}
                                        {!! Form::file('business_reg', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex ">
                                    <div class="position-relative form-group w-25 mr-2">
                                        {!! Form::label('video[type]', 'Video Type') !!}
                                        <div class="select-with-icon">
                                        {!! Form::select('video[type]', businessVideoType(), null, ['class' => 'form-control businessVideoType']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                    </div>
                                    <div class="position-relative form-group w-75">
                                        {!! Form::label('video[video]', 'Video') !!}
                                        {!! Form::file('video[video]', ['class' => 'form-control businessVideo', 'accept' => 'video/mp4,video/x-m4v,video/*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('registration_expire_date', 'Registration Expire Date') !!}
                                        {!! Form::date('registration_expire_date', '', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <iframe class="d-none" src="" id="businessVideoFrame" frameborder="0" width="50%" ></iframe>
                                </div>
                                <div class="col-md-12 socialMediaSection">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Social media details.</h6>
                                        <span class="btn btn-info appendSocialSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="hireJobSection">
                                        @include('admin.components.business-social-media-platform', ['index' => 1])
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Payment details.</h6>
                                        <span class="btn btn-info appendPaymentSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="paymentSection">
                                        @include('admin.components.business-payment', ['index' => 1])
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Price Menu details.</h6>
                                        <span class="btn btn-info appendPriceSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="priceSection">
                                        @include('admin.components.business-price-menu', ['index' => 1])
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="position-relative form-group">
                                        {!! Form::checkbox('hiring_for_buss', 'yes', '', ['class' => 'pt-4 checkHiring', 'id' => 'hiring_for_buss']) !!}
                                        {!! Form::label('hiring_for_buss', 'Hiring For Business') !!}
                                    </div>
                                </div>
                                <div class="col-md-12 hireJobMainSection" style="display: none">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Job hire details.</h6>
                                        <span class="btn btn-info appendJobSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="hireJobSection">
                                        @include('admin.components.job', ['index' => 1])
                                    </div>
                                </div>                                
                            </div>
                            {!! Form::hidden('recomendation', request()->id) !!}
                            {!! Form::submit('Submit', ['class' => 'mt-2 btn btn-primary', 'id' => 'addBusinessFormBtn']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>                
            </div>            
        </div>
    </div>
@endsection
@push('customScript')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
    <script type="text/javascript" src="{{ asset('assets/js/map.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    @include('admin.business.script')
    <script>
        $(document).on('change', '.weekDays', function(){
            var day = $(this).attr('id');
            if ($(this).is(":checked")) {
                $('.days').append(`@include('admin.components.business-time', ['day' => '${day}'])`);
                sortDays();
            } else {
                $('.'+day).remove();
                sortDays();
            }
            var selectedDays = $(".weekDays:checkbox:checked").length;
            if (selectedDays == 7) {
                $('#allDays').prop('checked', true);
            } else {
                $('#allDays').prop('checked', false);
            }
        });

        function sortDays() {
            var order = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            var daysContainer = $('.days');
            var days = daysContainer.children('.row').sort(function (a, b) {
                var dayA = $(a).attr('class').split(' ')[1];
                var dayB = $(b).attr('class').split(' ')[1];
                return order.indexOf(dayA) - order.indexOf(dayB);
            });
            return daysContainer.empty().append(days);
        }


        $("#addBusinessForm").validate({
            rules: {
                user_id: {
                    required: true
                },
                type: {
                    required: true
                },
                status_on: {
                    required: true
                },
                country: {
                    required: true
                },
                name: {
                    required: true
                },
                address: {
                    required: true
                },
                category: {
                    required: true
                },                
                // email: {
                //     required: true,
                //     pattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i,
                // },
                // buss_phone_number: {
                //     required: true,
                //     number: true,
                //     // pattern: /^\+\d{1,3} \d{8,15}$/
                // },
                // "weekDay[]": {
                //     required: true
                // },
                // detail: {
                //     required: true
                // },
                // languages: {
                //     required: true
                // },
                // website: {
                //     required: function(element) {
                //         return $("#website").val().length > 0;
                //     },
                //     url: true
                // },
                // owner_first_name: {
                //     required: true
                // },
                // owner_last_name: {
                //     required: true
                // },
                // identification: {
                //     required: true
                // },
                // owner_phone_number: {
                //     required: true,
                //     number: true,
                //     // pattern: /^\+\d{1,3} \d{8,15}$/
                // },
                // "service[]": {
                //     required: true
                // },
                // "languages[]": {
                //     required: true
                // },
                // "images[]": {
                //     required: true
                // },
                // nationald: {
                //     required: false
                // },
                // business_reg: {
                //     required: false
                // },
                // latitude: {
                //     required: true
                // },
                // longitude: {
                //     required: true
                // },
                // price: {
                //     required: true
                // },
                // continent: {
                //     required: true
                // },
                // state: {
                //     required: true
                // },
                // "options[]": {
                //     required: true
                // },
                // user_id: {
                //     required: false
                // },
                // "video[type]": {
                //     required: true
                // },
                // "video[video]": {
                //     required: true
                // },
            },
            // errorPlacement: function (error, element) {
            //     var name = element.attr("name");
            //     var showError = element.data("error");
            //     if (name === 'user_id' || name === 'category' || name === 'options[]' || name === 'service[]' || name === 'weekDay[]' || name === 'languages[]') {
            //         $('.'+showError).html(error);
            //     } else {
            //         error.insertAfter($(element));
            //     }
            // },
            messages: {
                user_id: {
                    required: "Please chose user!"
                },
                status_on: {
                    required: "Please chose status!"
                },
                type: {
                    required: "Please chose type!"
                },
                country: {
                    required: "Please enter country!"
                },
                name: {
                    required: "Please enter name!"
                },
                address: {
                    required: "Please enter address!"
                },
                category: {
                    required: "Please select category!"
                },                
                // // email: {
                // //     required: "Please enter email!",
                // //     pattern: "Enter a valid email"
                // // },
                // buss_phone_number: {
                //     required: "Please enter bussiness phone number!",
                //     number: "Please enter number only number!"
                //     // pattern: "Please enter number with country code!"
                // },
                // "weekDay[]": {
                //     url: "Please enter a valid url!"
                // },
                // detail: {
                //     required: "Please enter bussiness detail!"
                // },
                // languages: {
                //     required: "Please select languages!"
                // },
                // website: {
                //     url: "Please enter a valid url!"
                // },
                // owner_first_name: {
                //     required: "Please enter owner's first name!"
                // },
                // owner_last_name: {
                //     required: "Please enter owner's last name!"
                // },
                // identification: {
                //     required: "Please enter identification!"
                // },
                // owner_phone_number: {
                //     required: "Please enter owner phone number!",
                //     number: "Please enter number only number!"
                //     // pattern: "Please enter number with country code!"
                // },
                // "service[]": {
                //     required: "Please enter service!"
                // },
                // "languages[]": {
                //     required: "Please select language!"
                // },
                // "images[]": {
                //     required: "Please select image!"
                // },
                // nationald: {
                //     required: "Please select document!"
                // },
                // business_reg: {
                //     required: "Please select document!"
                // },
                // latitude: {
                //     required: "Please enter latitude!"
                // },
                // longitude: {
                //     required: "Please enter longitude!"
                // },
                // price: {
                //     required: "Please enter price!"
                // },
                // continent: {
                //     required: "Please enter continent!"
                // },
                // state: {
                //     required: "Please enter state!"
                // },
                // "options[]": {
                //     required: "Please select options!"
                // },
                // user_id: {
                //     required: "Please select user!"
                // },
                // "video[type]": {
                //     required: "Please select type!"
                // },
                // "video[video]": {
                //     required: "Please select video!"
                // },
            },
            submitHandler: function(form) {  
                // var weekDays = $('.weekDays').filter(':checked').length;
                // if (weekDays == 0) {
                //     $('.weekDaysError').html('Please check at least one day!');
                //     return false;
                // }
                var formData = new FormData(form);
                $('#addBusinessFormBtn').val('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('business.store') }}",
                    data : formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#addBusinessFormBtn').val('Submit'); 
                            toastr.success(data.message);
                            window.location.href = "{{ route('business.index') }}?type=all";
                        } else {
                            $('#addBusinessFormBtn').val('Submit'); 
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });        

    </script>
@endpush