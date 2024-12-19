@extends('admin.layout.master')
@push('customLink')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-culture"></i>
                    </div>
                    <div>Update Business</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('business.index') }}?type=all" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>        
        <div class="tab-content">
            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Business Detail</h5>
                        {{ Form::open(['url' => route('business.update', $business->id), 'id' => 'updateBusinessForm', 'enctype' => 'multipart/form-data']) }}
                        @method('PUT')
                            <div class="form-row">                                
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('user_id', 'User') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('user_id', $users, @$business->user_id, ['class' => 'form-control', 'placeholder' => 'Select User', 'data-error' => 'user_id']) !!}
                                            <i class="fa fa-angle-down"></i>                                            
                                        </div>
                                        <label class="user_id"></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('type', 'Business Type') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('type', businessType(), @$business->type, ['class' => 'form-control', 'data-error' => 'type']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="type"></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('status_on', 'Status on App') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('status', businessStatusOn(), $business->status, ['class' => 'form-control', 'data-error' => 'statusOn']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="statusOn"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('address', 'Business Address') !!}
                                        {!! Form::text('address', $business->address, ['class' => 'form-control', 'id' => 'address', 'placeholder' => 'Enter Address']) !!}
                                        {!! Form::hidden('latitude', $business->latitude, ['id' => 'latitude']) !!}                                    
                                        {!! Form::hidden('longitude', $business->longitude, ['id' => 'longitude']) !!}                                    
                                        {!! Form::hidden('continent', $business->continent, ['id' => 'continent']) !!}                                    
                                        {!! Form::hidden('state', $business->state, ['id' => 'state']) !!}
                                        {!! Form::hidden('city', @$business->city, ['id' => 'city']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('country', 'Country') !!}
                                        {!! Form::text('country', $business->country, ['class' => 'form-control', 'placeholder' => 'Enter country']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('name', 'Business Name') !!}
                                        {!! Form::text('name', $business->name, ['class' => 'form-control', 'placeholder' => 'Enter Business Name']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('category', 'Business Category') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('category', $categories, $business->category, ['class' => 'form-control categories', 'placeholder' => 'Select category', 'data-error' => 'category']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <label class="category"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('options', 'Features') !!}
                                        <div class="select-with-icon">
                                            <select name="options[]" class="form-control removeValidation options" multiple data-error="optionsError">
                                                @foreach ($allOptions as $option)
                                                    <option value="{{$option->id}}" {{ in_array($option->id, $businessOptions) ? 'selected' : '' }}>{{$option->name}}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-angle-down"></i>
                                            <label class="optionsError"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('service[]', 'Service') !!}
                                        <div class="select-with-icon">
                                            <select name="service[]" class="form-control removeValidation serviceDropdown subCategory" multiple data-error="service">
                                                @foreach ($allService as $service)
                                                    <option value="{{$service->id}}" {{ in_array($service->id, $businessService) ? 'selected' : '' }}>{{$service->name}}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <span class="service"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between" style="width: 36%">
                                        {!! Form::label('', 'Select Days') !!}
                                        <div class="position-relative form-group">
                                            @php
                                                $daysCount = count($businessTimes) == 7 ? true : false;
                                            @endphp
                                            {!! Form::checkbox('', 'yes', $daysCount, ['id' => "allDays"]) !!}
                                            {!! Form::label("allDays", 'Select All') !!}
                                        </div>
                                    </div>
                                    <div class="mt-1" style="display: flex; justify-content:left">
                                        @php
                                            $sunday = array_key_exists('sunday', $businessTimes);
                                            $monday = array_key_exists('monday', $businessTimes);
                                            $tuesday = array_key_exists('tuesday', $businessTimes);
                                            $wednesday = array_key_exists('wednesday', $businessTimes);
                                            $thursday = array_key_exists('thursday', $businessTimes);
                                            $friday = array_key_exists('friday', $businessTimes);
                                            $saturday = array_key_exists('saturday', $businessTimes);
                                        @endphp
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $sunday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'sunday']) !!}
                                            {!! Form::label('sunday', 'Sunday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $monday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'monday']) !!}
                                            {!! Form::label('monday', 'Monday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $tuesday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'tuesday']) !!}
                                            {!! Form::label('tuesday', 'Tuesday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $wednesday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'wednesday']) !!}
                                            {!! Form::label('wednesday', 'Wednesday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $thursday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'thursday']) !!}
                                            {!! Form::label('thursday', 'Thursday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $friday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'friday']) !!}
                                            {!! Form::label('friday', 'Friday') !!}
                                        </div>
                                        <div class="mr-4">                                            
                                            {!! Form::checkbox("weekDay[]", 'yes', $saturday, ['class' => 'pt-4 weekDays', 'data-error' => 'weekDaysError', 'id' => 'saturday']) !!}
                                            {!! Form::label('saturday', 'Saturday') !!}
                                        </div>
                                    </div>
                                    <span class="weekDaysError"></span>
                                    <div class="mt-2 mr-4 days">
                                        @if ($sunday)
                                            @include('admin.components.business-time', ['day' => 'sunday', 'data' => $businessTimes['sunday']])
                                        @endif
                                        @if ($monday)
                                            @include('admin.components.business-time', ['day' => 'monday', 'data' => $businessTimes['monday']])
                                        @endif 
                                        @if ($tuesday)
                                            @include('admin.components.business-time', ['day' => 'tuesday', 'data' => $businessTimes['tuesday']])
                                        @endif
                                        @if ($wednesday)
                                            @include('admin.components.business-time', ['day' => 'wednesday', 'data' => $businessTimes['wednesday']])
                                        @endif
                                        @if ($thursday)
                                            @include('admin.components.business-time', ['day' => 'thursday', 'data' => $businessTimes['thursday']])
                                        @endif
                                        @if ($friday)
                                            @include('admin.components.business-time', ['day' => 'friday', 'data' => $businessTimes['friday']])
                                        @endif 
                                        @if ($saturday)
                                            @include('admin.components.business-time', ['day' => 'saturday', 'data' => $businessTimes['saturday']])
                                        @endif 
                                    </div>
                                </div>
                                <div class="col-md-12 mt-4">
                                    <div class="position-relative form-group">
                                        {!! Form::label('detail', 'Business Detail') !!}
                                        {!! Form::textarea('detail', $business->detail, ['class' => 'form-control', 'placeholder' => 'Enter Business Detail', 'rows' => 5]) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('email', 'Business Email') !!}
                                        {!! Form::text('email', $business->email, ['class' => 'form-control', 'placeholder' => 'Enter Business Email']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('buss_phone_number', 'Business Phone Number') !!}
                                        <div class="d-flex">
                                            <select name="buss_phone_code" id="bussPhoneCode" class="form-control" style="width: 80px">
                                                @foreach ($codes as $code)
                                                    <option {{ ($business->buss_phone_code ?? '+234') == $code['dial_code'] ? 'selected' : '' }} value="{{ $code['dial_code'] }}">{{ $code['dial_code'] }}</option>
                                                @endforeach
                                            </select>
                                            {!! Form::text('buss_phone_number', $business->buss_phone_number, ['class' => 'form-control', 'placeholder' => 'Enter Business Phone Number']) !!}
                                        </div>
                                    </div>
                                </div>            
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('languages[]', 'Languages') !!}
                                        <div class="select-with-icon">
                                            {!! Form::select('languages[]', $languages, $bussLang, ['class' => 'form-control removeValidation langDropdown', 'multiple' => 'multiple', 'data-error' => 'languages']) !!}
                                            <i class="fa fa-angle-down"></i>
                                        </div>
                                        <span class="languages"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('key_words[]', 'Business Key Words') !!}
                                        <div class="select-with-icon">
                                            <select name="key_words[]" class="form-control removeValidation keyWords" multiple data-error="keyWordsError">
                                                @foreach ($business->keyWords->pluck('keyword') as $keyWord)
                                                    <option value="{{$keyWord}}" selected>{{$keyWord}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label class="keyWordsError"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('website', 'Website') !!}
                                        {!! Form::text('website', $business->website, ['class' => 'form-control', 'placeholder' => 'Enter Website']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_first_name', 'Owner’s Firstname') !!}
                                        {!! Form::text('owner_first_name', $business->owner_first_name, ['class' => 'form-control', 'placeholder' => 'Enter Owner’s Firstname']) !!}                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_last_name', 'Owner’s Lastname') !!}
                                        {!! Form::text('owner_last_name', $business->owner_last_name, ['class' => 'form-control', 'placeholder' => 'Enter Owner’s Lastname']) !!}                                        
                                    </div>
                                </div>                               
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('owner_phone_number', 'Owner Phone Number') !!}
                                        <div class="d-flex">
                                            <select name="owner_phone_code" id="ownerPhoneCode" class="form-control" style="width: 80px">
                                                @foreach ($codes as $code)
                                                    <option {{ $business->owner_phone_code == $code['dial_code'] ? 'selected' : '' }} value="{{ $code['dial_code'] }}">{{ $code['dial_code'] }}</option>
                                                @endforeach
                                            </select>
                                            {!! Form::text('owner_phone_number', $business->owner_phone_number, ['class' => 'form-control', 'placeholder' => 'Enter Owner Phone Number']) !!}
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('images[]', 'Business images') !!}
                                        {!! Form::file('images[]', ['class' => 'form-control', 'id' => 'images', 'data-type' => 'images', 'multiple' => 'multiple']) !!}
                                    </div>
                                    <div class="d-flex businessimages {{ count($businessImages) > 0 ? '' : 'd-none' }}" style="flex-wrap: wrap;">
                                        @if (count($businessImages) > 0)
                                            @foreach ($businessImages as $businessImage)
                                                <div>
                                                    <img class="preview-images" src="{{$businessImage->image}}" />
                                                    <div class="business-images">
                                                        <span class="btn btn-danger removeImage" data-id="{{ $businessImage->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div> 
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('business_logo', 'Business logo') !!}
                                        {!! Form::file('business_logo', ['class' => 'form-control', 'data-type' => 'logo', 'id' => 'images']) !!}
                                    </div>
                                    <div class="businesslogo {{ isset($business->logo) > 0 ? '' : 'd-none' }}">
                                        @if (!empty($business->logo))
                                            <img class="preview-images" src="{{$business->logo}}" />
                                        @endif
                                    </div>
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
                                    {!! Form::hidden('video[id]', @$business->video->id) !!}
                                    <div class="position-relative form-group w-25 mr-2">
                                        {!! Form::label('video[type]', 'Video Type') !!}
                                        {!! Form::select('video[type]', businessVideoType(), @$business->video->type, ['class' => 'form-control businessVideoType']) !!}
                                    </div>
                                    <div class="position-relative form-group w-75">
                                        {!! Form::label('video[video]', 'Business Video') !!}
                                        @if (isset($business->video->type) && $business->video->type == 'youtube')
                                            {!! Form::text('video[video]', @$business->video->video, ['class' => 'form-control businessVideo', 'accept' => 'video/mp4,video/x-m4v,video/*']) !!}
                                        @else
                                            {!! Form::file('video[video]', ['class' => 'form-control businessVideo', 'accept' => 'video/mp4,video/x-m4v,video/*']) !!}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        {!! Form::label('registration_expire_date', 'Registration Expire Date') !!}
                                        {!! Form::date('registration_expire_date', $business->registration_expire_date, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @if (isset($business->video->video))
                                        <iframe src="{{ @$business->video->video }}" id="businessVideoFrame" frameborder="0" width="50%" ></iframe>
                                    @else
                                        <iframe class="d-none" src="" id="businessVideoFrame" frameborder="0" width="50%" ></iframe>
                                    @endif
                                </div>
                                <div class="col-md-12 socialMediaSection">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Social media details.</h6>
                                        <span class="btn btn-info appendSocialSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="hireJobSection">
                                        @forelse ($business->socialAccount as $socialAccount)
                                            @include('admin.components.business-social-media-platform', ['index' => $loop->iteration, 'data' => $socialAccount])
                                        @empty
                                            @include('admin.components.business-social-media-platform', ['index' => 1])                                            
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Payment details.</h6>
                                        <span class="btn btn-info appendPaymentSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="paymentSection">
                                        @forelse ($business->paymentOption as $paymentOption)
                                            @include('admin.components.business-payment', ['index' => $loop->iteration, 'data' => $paymentOption])
                                        @empty
                                            @include('admin.components.business-payment', ['index' => 1])                                            
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Price Menu details.</h6>
                                        <span class="btn btn-info appendPriceSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="priceSection">
                                        @forelse ($business->priceMenu as $priceMenu)
                                            @include('admin.components.business-price-menu', ['index' => $loop->iteration, 'data' => $priceMenu])
                                        @empty
                                            @include('admin.components.business-price-menu', ['index' => 1])
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="position-relative form-group">                                        
                                        {!! Form::checkbox('hiring_for_buss', 'yes', strtolower($business->hiring_for_buss) == 'yes' ? true : false, ['class' => 'pt-4 checkHiring', 'id' => 'hiring_for_buss']) !!}
                                        {!! Form::label('hiring_for_buss', 'Hiring For Business') !!}
                                    </div>
                                </div>
                                <div class="col-md-12 hireJobMainSection" style="display: {{ count($businessHirings) > 0 ? 'block' : 'none' }}">
                                    <div class="d-flex justify-content-between">
                                        <h6>Fill up Job hire details.</h6>
                                        <span class="btn btn-info appendJobSec"><i class="fa fa-plus"></i></span>
                                    </div>
                                    <div class="hireJobSection">
                                        @if (count($businessHirings) > 0)
                                            @foreach ($businessHirings as $hiring)
                                                @include('admin.components.job', ['index' => $loop->iteration, 'data' => $hiring])
                                            @endforeach
                                        @else
                                            @include('admin.components.job', ['index' => 1])
                                        @endif
                                    </div>
                                </div>                                
                            </div>                            
                            {!! Form::submit('Update', ['class' => 'mt-2 btn btn-primary', 'id' => 'updateBusinessFormBtn']) !!}
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
        $(document).on('change', '.weekDays', function() {
            var day = $(this).attr('id');
            var selectedDays = $(".weekDays:checkbox:checked").length;
            if (selectedDays == 7) {
                $('#allDays').prop('checked', true);
            } else {
                $('#allDays').prop('checked', false);
            }
            if ($(this).is(":checked")) {
                $('.days').append(`@include('admin.components.business-time', ['day' => '${day}'])`);
                sortDays();
            } else {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, remove it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.' + day).remove('');
                        sortDays();
                        Swal.fire({
                            title: "Removed!",
                            text: "Your file has been removed.",
                            icon: "success"
                        });
                    } else {
                        $(this).prop('checked', true);
                    }
                });
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

        $("#updateBusinessForm").validate({
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
                email: {
                    required: false
                },
                // buss_phone_number: {
                //     required: true,
                //     number: true
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
                //     number: true
                //     // pattern: /^\+\d{1,3} \d{8,15}$/
                // },
                // "service[]": {
                //     required: true
                // },
                // "languages[]": {
                //     required: true
                // },
                // "images[]": {
                //     required: false
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
                // options: {
                //     required: true
                // },
                // user_id: {
                //     required: false
                // },
                // "video[type]": {
                //     required: true
                // },
                // "video[video]": {
                //     required: false
                // },
            },
            // errorPlacement: function (error, element) {
            //     var name = element.attr("name");
            //     var showError = element.data("error");
            //     if (name === 'user_id' || name === 'category' || name === 'weekDay[]' || name === 'service[]' || name === 'languages[]') {
            //         $('.'+showError).html(error); // Escape special characters in the class name
            //     } else {
            //         error.insertAfter($(element));
            //     }
            // },
            messages: {
                user_id: {
                    required: "Please chose user!"
                },
                type: {
                    required: "Please chose type!"
                },
                status_on: {
                    required: "Please chose status!"
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
                // email: {
                //     required: "Please enter email!"
                // },
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
                //     required: "Please enter website!"
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
                // options: {
                //     required: "Please enter options!"
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
                var formData = new FormData(form);                
                $('#updateBusinessFormBtn').val('Processing');    
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type : 'POST',
                    url : "{{ route('business.update', $business->id) }}",
                    data : formData,                    
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success : function(data){
                        if (data.status == true) {
                            $('#updateBusinessFormBtn').val('Add'); 
                            toastr.success(data.message);
                            window.location.href = "{{ route('business.index') }}?type=all";
                        } else {
                            $('#updateBusinessFormBtn').val('Add'); 
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });
    </script>
@endpush