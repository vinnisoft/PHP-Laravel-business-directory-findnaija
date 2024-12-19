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
                    <div>Business Report Detail</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ url()->previous() }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-backward fa-w-20"></i></span>Back</a>
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4>Business Report Detail</h4>                    
                </div>
                <div class="row">
                    <div class="col-md-8 mt-2">
                        <h5 class="business-name">
                            {{ getBusinessNameById($report->business_id) }}                            
                        </h5>
                        <hr>
                        <p class="pt-2">{{ $report->reason }}</p>
                    </div>
                    <div class="col-md-4 mt-4">
                        <div class="row information">
                            <div class="col-md-6"><b>User </b></div>
                            <div class="col-md-6"><span>{{ getUserNameById($report->user_id) }}</span></div>
                        </div>    
                        <div class="row information">
                            <div class="col-md-6"><b>Category </b></div>
                            <div class="col-md-6"><span>{{ $report->category }}</span></div>
                        </div>
                        <div class="row information">
                            <div class="col-md-6"><b>Date </b></div>
                            <div class="col-md-6"><span>{{ $report->created_at }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
