@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-culture"></i>
                    </div>
                    <div>All Business Reports</div>
                </div>
                <div class="page-title-actions">
                </div>
            </div>
        </div>
        <div class="main-card mb-3 card">
            <div class="card-body table-responsive">
                {!! $dataTable->table(['class' => 'table table-hover table-striped table-bordered']) !!}
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    {{ $dataTable->scripts() }}
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>       
        $(document).on('click', '.deleteBusinessReport', function() {
            var url = $(this).data('url');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == true) {
                                window.location.reload();
                            } else {
                                toastr.error(data.message);
                            }
                        }
                    });                    
                }
            });
            return false;
        });
    </script>
@endpush
