<script>
    $(document).ready(function(){
        updateRemoveButtonState('serviceSectionRow', 'removeJobSec');
        updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
    });
    $(document).on('click', '.appendServiceSec', function() {
        var index = $('.serviceSectionRow').length;
        index = index + 1;
        $('.serviceSection').append(`@include('admin.components.sub-category', ['index' => '${index}'])`);
        updateRemoveButtonState('serviceSectionRow', 'removeJobSec');
    });

    $(document).on('click', '.removeJobSec', function() {
        var index = $('.serviceSectionRow').length;
        var url = $(this).data('url');
        var thisElement = $(this);
        if (index > 1) {
            if (url) {
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
                                    toastr.success(data.message);
                                    thisElement.parent().parent().remove();
                                    updateRemoveButtonState('serviceSectionRow', 'removeJobSec');
                                } else {
                                    toastr.error(data.message);
                                }
                            }
                        });
                    }
                });
            } else {
                $(this).parent().parent().remove();
                updateRemoveButtonState('serviceSectionRow', 'removeJobSec');
            }
        }
    });

    $(document).on('click', '.appendOptionSec', function() {
        var index = $('.optionSection').length;
        index = index + 1;
        $('.optionSection').append(`@include('admin.components.options', ['index' => '${index}'])`);
        updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
    });

    $(document).on('click', '.removeOptionSec', function() {
        var index = $('.optionSectionRow').length;
        var url = $(this).data('url');
        var thisElement = $(this);
        if (index > 1) {
            if (url) {
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
                                    toastr.success(data.message);
                                    thisElement.parent().parent().remove();
                                    updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
                                } else {
                                    toastr.error(data.message);
                                }
                            }
                        });
                    }
                });
            } else {
                $(this).parent().parent().remove();
                updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
            }
        }
    });

    function updateRemoveButtonState(lengthClass, removeClass) {
        var rowCount = $('.'+lengthClass).length;
        $('.'+removeClass).prop('disabled', rowCount <= 1);
    }
    icon.onchange = evt => {
        const [file] = icon.files
        if (file) {
            $('.previewIcon').html('<img class="shadow p-1" id="" src="'+URL.createObjectURL(file)+'" width="100" height="100" alt="" >')
        }
    }
    graphic.onchange = evt => {
        const [file] = graphic.files
        if (file) {
            $('.previewGraphic').html('<img class="shadow p-1" id="" src="'+URL.createObjectURL(file)+'" width="100" height="100" alt="" >')
        }
    }
</script>
