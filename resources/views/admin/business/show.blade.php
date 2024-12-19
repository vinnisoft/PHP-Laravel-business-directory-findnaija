@extends('admin.layout.master')
@section('content')
<style>
    hr:not([size]) {
        height: 0px !important;
    }
</style>
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-culture"></i>
                    </div>
                    <div>Business Detail</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ url()->previous() }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4>Business Detail</h4>
                    <span class="mt-2 mr-3 approveRejectBusinessSec">
                        @if ($business->status == 'pending')
                            <button type="button" class="btn-lg btn btn-info approveRejectBusiness" data-id="{{ $business->id }}" data-status="approved">Approve</button>
                            <button type="button" class="btn-lg btn btn-danger approveRejectBusiness" data-id="{{ $business->id }}" data-status="rejected">Reject</button>
                        @else
                        <div class="d-flex">
                            {{-- <h3><span class="badge badge-{{ $business->status == '1' ? 'info' : 'danger' }}">{{ $business->status == '1' ? 'Approved' : 'Rejected' }}</span></h3>&nbsp; --}}
                            @php
                                switch ($business->status) {
                                    case 'pending':
                                        $class = 'primary';    
                                    break;
                                    case 'verified':
                                        $class = 'primary';    
                                    break;
                                    case 'unverified':
                                        $class = 'danger';    
                                    break;
                                    case 'rejected':
                                        $class = 'danger';    
                                    break;
                                    case 'verified_but_not_claimed':
                                        $class = 'info';    
                                    break;
                                    default:
                                        $class = '';
                                    break;
                                }
                            @endphp
                            <h3><span class="badge badge-{{ $class }}">{{ str_replace('_', ' ', $business->status) }}</span></h3>
                        </div>
                        @endif
                    </span>
                </div>
                <div>
                    <img src="{{ $business->logo }}" width="100" height="50" alt="">
                </div>
                <div class="row mt-2">
                    <div class="col-md-8">
                        <h5 class="business-name">
                            {{ $business->name }}
                            @if (!empty($business->rating_avg))
                                @for ($i = 0; $i < $business->rating_avg; $i++)
                                    <i class="fa fa-star yellow-star"></i>
                                @endfor
                                @for ($i = 0; $i < 5 - $business->rating_avg; $i++)
                                    <i class="fa fa-star"></i>
                                @endfor
                            @endif
                        </h5>
                        <p class="pt-4">{{ $business->detail }}</p>
                        <div class="photo-gallery">
                            <div class="photos d-flex justify-content-left">
                                @foreach ($business->images as $images)
                                    <div class="item">
                                        <a href="{{ $images->image }}" data-lightbox="photos">
                                            <img class="m-1 item" src="{{ $images->image }}" height="100" width="100" style="object-fit: cover;">
                                        </a>
                                        <span class="cover-icon {{ $images->is_cover == '1' ? '' : 'setCover' }}" data-id="{{ $images->id }}" style="background: {{ $images->is_cover == '1' ? 'green' : '' }}">
                                            <i class="fa fa-image"></i>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        @if (!empty($business->business_registration) && !empty($business->national_id))
                        <h5 class="mt-2">Documents</h5>
                        <div class="row">
                            @if (!empty($business->business_registration))
                                <div class="col-md-6">
                                    <h5 class="mt-2"><a href="{{ $business->business_registration }}" target="_blank"><i class=" fa fa-file-alt"></i> Business Registration</a></h5>
                                </div>
                            @endif
                            @if (!empty($business->national_id))
                                <div class="col-md-6">
                                    <h5 class="mt-2"><a href="{{ $business->national_id }}" target="_blank"><i class=" fa fa-file-alt"></i> National Id</a></h5>
                                </div>
                            @endif
                        </div>
                        @endif
                        <hr>
                        <h5 class="mt-2">Languages</h5>
                        @foreach ($business->languages as $language)
                            <span class="badge badge-secondary">{{ $language->language }}</span>
                        @endforeach
                        <hr>
                        <h5 class="mt-2">Services</h5>
                        @foreach ($business->services as $service)
                            <span class="business-service"><i class="fa fa-check"></i> {{ $service->name }}</span
                                class="business-service">
                        @endforeach
                        <hr>
                        <h5 class="mt-2">Options</h5>
                        @forelse ($business->options as $option)
                            <span class="business-service"><i class="fa fa-check"></i> {{ $option->option_name }}</span
                                class="business-service">
                        @empty
                            <span>No option found</span>
                        @endforelse
                        <hr>
                        <h5 class="mt-2">Keywords</h5>
                        @forelse ($business->keyWords as $keyWord)
                            <span class="business-service"><i class="fa fa-check"></i> {{ $keyWord->keyword }}</span
                                class="business-service">
                        @empty
                            <span>No key word found</span>
                        @endforelse
                        <hr>
                        <h5 class="mt-2">Available Positions</h5>
                        <table style="width: 100%;" id="example2" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Job Position Name</th>
                                    <th>List out job requirements</th>
                                    <th>Job Pay Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($business->hirings as $hirings)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $hirings->job_title }}</td>
                                        <td>{{ $hirings->requirement }}</td>
                                        <td>{{ $hirings->amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No hiring found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <hr>
                        <h5 class="mt-2">Reviews</h5>
                        <table style="width: 100%;" id="example2" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="width100">User</th>
                                    <th class="width100">Rating</th>
                                    <th class="">Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($business->reviews as $review)
                                    <tr>
                                        <td class="user-image d-flex">
                                            <img src="{{ getUserImageById($review->user_id) }}" alt="">
                                            <p class="pt-3 pl-2">{{ getUserNameById($review->user_id) }}</p>
                                        </td>
                                        <td>
                                            @for ($i = 0; $i < $review->rating; $i++)
                                                <i class="fa fa-star yellow-star"></i>
                                            @endfor
                                            @for ($i = 0; $i < 5 - $review->rating; $i++)
                                                <i class="fa fa-star"></i>
                                            @endfor
                                        </td>
                                        <td>{{ $review->comment }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No review found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <hr>
                        <h5 class="mt-2">Social Media Platform</h5>
                        <table style="width: 100%;" id="example2" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="width100">Sr.No</th>
                                    <th class="width100">Type</th>
                                    <th class="">URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($business->socialAccount as $socialAccount)
                                    <tr>
                                        <td class="user-image d-flex">{{ $loop->iteration }}</td>
                                        <td>{{ $socialAccount->type }}</td>
                                        <td>{{ $socialAccount->url }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No social account found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <h5 class="mt-2">Payment Options</h5>
                        <table style="width: 100%;" id="example2" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="width100">Sr.No</th>
                                    <th class="width100">Type</th>
                                    <th class="">URL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($business->paymentOption as $paymentOption)
                                    <tr>
                                        <td class="user-image d-flex">{{ $loop->iteration }}</td>
                                        <td>{{ $paymentOption->type }}</td>
                                        <td>{{ $paymentOption->url }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No payment option found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <h5 class="mt-2">Price Menus</h5>
                        <table style="width: 100%;" id="example2" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="width100">Sr.No</th>
                                    <th class="width100">Menu</th>
                                    <th class="">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($business->priceMenu as $priceMenu)
                                    <tr>
                                        <td class="user-image d-flex">{{ $loop->iteration }}</td>
                                        <td>{{ $priceMenu->menu }}</td>
                                        <td>${{ $priceMenu->price }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No price menu found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 mt-4">
                        <div class="row information">
                            <div class="col-md-6"><b>Country</b></div>
                            <div class="col-md-6"><span>{{ $business->country }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Address</b></div>
                            <div class="col-md-6"><span>{{ $business->address }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Category</b></div>
                            <div class="col-md-6"><span>{{ $business->category_name }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Business Phone Number</b></div>
                            <div class="col-md-6"><span>{{ $business->buss_phone_code.' '.$business->buss_phone_number }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>website</b></div>
                            <div class="col-md-6"><span>{{ $business->website }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Owner First Name</b></div>
                            <div class="col-md-6"><span>{{ $business->owner_first_name }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Owner Last Name</b></div>
                            <div class="col-md-6"><span>{{ $business->owner_last_name }}</span></div>
                        </div>                        
                        <div class="row information">
                            <div class="col-md-6"><b>Owner Phone Number</b></div>
                            <div class="col-md-6"><span>{{ $business->owner_phone_code.' '.$business->owner_phone_number }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Hiring For Business</b></div>
                            <div class="col-md-6"><span>{{ $business->hiring_for_buss }}</span></div>
                        </div>
                        @foreach ($business->times as $time)
                            <div class="row information">
                                <div class="col-md-6"><b>{{ ucfirst($time->day) }}</b></div>
                                <div class="col-md-6"><span>{{ date('H:i A', strtotime($time->start_time)) }} - {{ date('H:i A', strtotime($time->end_time)) }}</span></div>
                            </div>
                        @endforeach
                        @if (@$business->video->video)
                            <div class="row w-100">
                                <div><b>Video</b></div>
                                <iframe src="{{ @$business->video->video }}" frameborder="0" height="300" ></iframe>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).on('click', '.setCover', function() {
            var id = $(this).data('id');
            var thisImage = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                type: 'POST',
                url: "{{ route('coverImage') }}",
                data: { id: id },
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        toastr.success(data.message);
                        $('.cover-icon').css('background', '#636060');
                        $('.cover-icon').addClass('setCover');
                        thisImage.css('background', '#008000');
                        thisImage.removeClass('setCover');
                    } else {
                        toastr.error(data.message);
                    }
                }
            });
        });
        $(document).on('click', '.approveRejectBusiness', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            console.log(status);
            var selectOptionHtml = '';
            if (status == 'approved') {
                selectOptionHtml = `<select id="approvalOption" class="form-control">
                                        <option value="Verified">Verified</option>
                                        <option value="Unverified">Unverified</option>
                                        <option value="Verified_but_not_claimed">Verified but not claimed</option>
                                    </select>`;
            }

            Swal.fire({
                title: (status == 'approved' ? "Approve Business?" : "Reject Business?"),
                html: (status == 'approved' ? selectOptionHtml : '<textarea id="rejectionReason" class="swal2-input form-control" placeholder="Enter rejection reason" style="height: 200px;padding: 2px 8px;"></textarea>'),
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: (status == 'approved' ? "Yes, approve it!" : "Yes, reject it!")
            }).then((result) => {
                if (result.isConfirmed) {
                    var rejectionReason = (status == 'rejected') ? $('#rejectionReason').val() : $('#approvalOption').val();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: 'POST',
                        url: "{{ route('business.approve') }}",
                        data: {
                            id: id,
                            status: status,
                            rejection_reason: rejectionReason
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == true) {
                                toastr.success(data.message);
                                console.log(status);
                                console.log(rejectionReason);
                                var label = '';
                                if (status == 'approved') {
                                    label = rejectionReason.replace(/_/g, ' ');
                                } else {
                                    label = status;
                                }
                                $('.approveRejectBusinessSec').html('<h3><span class="badge badge-'+(status == 'rejected' ? 'danger' : 'info')+'">'+(label)+'</span></h3>');
                            } else {
                                toastr.error(data.message);
                            }
                        }
                    });
                }
            });
        });

    </script>
@endpush
