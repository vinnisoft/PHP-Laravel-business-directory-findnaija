@extends('admin.layout.master')
@section('content')
    <div class="app-main__inner">
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="metismenu-icon pe-7s-edit"></i>
                    </div>
                    <div>All Categories</div>
                </div>
                <div class="page-title-actions">
                    <a href="{{ route('category.create') }}" class="btn-shadow btn btn-info"><span class="btn-icon-wrapper pr-2 opacity-7"><i class="fa fa-plus fa-w-20"></i></span>Add Category</a>
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
        $(document).on('change', '.setCategory', function() {
            var categoryId = $(this).data('id');
            var switchId = $(this).attr('id');
            var name = $(this).data('name');
            console.log('test', $('.'+name+'-checkbox:checked').length)

            if (name == 'graphic' && $('.'+name+'-checkbox:checked').length >= 6) {
                $(this).prop('checked', false);
                var value = '0';
                toastr.warning("You can't select more than 5 graphics. Please deselect some before adding more.");
                return;
            }
            if (name == 'category_on_home' && $('.'+name+'-checkbox:checked').length >= 8) {
                $(this).prop('checked', false);
                var value = '0';
                toastr.warning("You can't select more than 7 categories. Please deselect some before adding more.");
                return;
            }

            if ($(this).is(":checked")) {
                if (name == 'category') {
                    $('.category-checkbox').not(this).prop('checked', false);
                }
                var value = '1';
            } else {
                if ($('.'+name+'-checkbox:checked').length > 0) {
                    var value = '0';
                } else {
                    $(this).prop('checked', true);
                    var value = '1';
                    toastr.warning('At least one '+name+' must be selected.');
                    return;
                }
            }
            $.ajax({
                headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                type: 'POST',
                url: "{{ route('setCategory') }}",
                data: {
                    categoryId: categoryId,
                    value: value,
                    name: name,
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                }
            });       
        });
        $(document).on('click', '.deleteCategory', function() {
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
