<script>
    $(document).ready(function() {
        $('.langDropdown').select2();
        $('.serviceDropdown').select2();
        $('.options').select2();
        $('.keyWords').select2({
            tags: true,
            dropdownCssClass: 'custom-select2-dropdown'
        });
        $('.startTime').each(function() {
            // endTimeValidation($(this));

            var day = $(this).data('day');
            var time = $(this).val();
            var number = parseInt(time.split(':')[0], 10);
            $('.'+day+' .endTime option').each(function() {
                var endTimeNumber = parseInt($(this).val().split(':')[0], 10);
                if (endTimeNumber <= number) {
                    $(this).prop('disabled', true);
                }
            });
        });


        // businessVideoType($('.businessVideoType').val());
    });

    // $(document).on('change', '.startTime', function() {
    //     endTimeValidation($(this));
    // });

    // function endTimeValidation($this) {
    //     var startTime = $this.val();
    //     $this.closest('.row').find('.endTime').attr('min', startTime);
    //     var day = $this.data('day');
    //     var rules = {};socialMedia
    //     var messages = {};
    //     rules['time[' + day + '][end_time]'] = {
    //         required: true,
    //         min: startTime
    //     };
    //     messages['time[' + day + '][end_time]'] = {
    //         required: "Please select a time greater than to " + startTime + "."
    //     };
    //     $('#updateBusinessForm').rules('add', rules);
    //     $('#updateBusinessForm').validate().settings.messages['time[' + day + '][end_time]'] = messages['time[' + day +
    //         '][end_time]'];
    //     $('#updateBusinessForm').valid();
    // }

    $(document).on('change', '#images', function(e) {
        var type = $(this).data('type');
        var files = e.target.files;
        if (files.length > 0) {
            $('.business'+type).removeClass('d-none');
            for (var i = 0; i < files.length; i++) {
                var reader = new FileReader();
                var trashIcon = type == 'images' ? '<div class="business-images"><span class="btn btn-danger removeImage"><i class="fa fa-trash"></i></span></div>' : '';
                reader.onload = function(e) {
                    $('.business'+type).append('<div><img class="preview-images" src="' + e.target.result + '" />'+trashIcon+'</div>');
                };
                reader.readAsDataURL(files[i]);
            }
        } else {
            $('.business'+type).addClass('d-none');
        }
    });

    $(document).on('click', '.removeImage', function() {
        var id = $(this).data('id');
        var thisImage = $(this);
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
                if (id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        type: 'POST',
                        url: "{{ route('businessImage.delete') }}",
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == true) {
                                thisImage.parent().remove();
                            }
                        }
                    });
                } else {
                    thisImage.parent().remove();
                }
            }
        });
    });
    $(document).on('change', '.switch-input', function() {
        if ($(this).is(":checked")) {
            $(this).val('1')
        } else {
            $(this).val('0')
        }
    });

    $(document).on('change', '.checkHiring', function() {
        if ($(this).is(":checked")) {
            $('.hireJobMainSection').show();
        } else {
            $('.hireJobMainSection').hide();
        }
    });

    $(document).on('change', '.categories', function() {
        var categoryId = $(this).val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            type: 'get',
            url: "{{ route('getSubCategory') }}",
            data: {
                categoryId: categoryId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    $('.subCategory').html('');
                    $('.subCategory').html(data.subCatOptions);
                    $('.options').html(data.catOptions);
                }
            }
        });
    });

    $(document).on('change', '.removeValidation', function() {
        if ($(this).val() != '') {
            var error = $(this).data('error');
            $('.' + error).html('');
        }
    });

    $(document).on('click', '.appendSocialSec', function() {
        var index = $('.socialSecRow').length;
        index = index + 1;
        $('.socialMediaSection').append(`@include('admin.components.business-social-media-platform', ['index' => '${index}'])`);
    });

    $(document).on('click', '.removeSocialSec', function() {
        var index = $('.socialSecRow').length;
        if (index > 1) {
            $(this).parent().parent().remove();
        }
    });
    
    $(document).on('click', '.appendPaymentSec', function() {
        var index = $('.paymentSecRow').length;
        index = index + 1;
        $('.paymentSection').append(`@include('admin.components.business-payment', ['index' => '${index}'])`);
    });

    $(document).on('click', '.removePaymentSec', function() {
        var index = $('.paymentSecRow').length;
        console.log(index);
        if (index > 1) {
            $(this).parent().parent().remove();
        }
    });

    $(document).on('click', '.appendPriceSec', function() {
        var index = $('.priceSecRow').length;
        index = index + 1;
        $('.priceSection').append(`@include('admin.components.business-price-menu', ['index' => '${index}'])`);
    });

    $(document).on('click', '.removePaymentSec', function() {
        var index = $('.priceSecRow').length;
        console.log(index);
        if (index > 1) {
            $(this).parent().parent().remove();
        }
    });
    
    $(document).on('click', '.appendJobSec', function() {
        var index = $('.hireJobSecRow').length;
        index = index + 1;
        $('.hireJobSection').append(`@include('admin.components.job', ['index' => '${index}'])`);
    });

    $(document).on('click', '.removeJobSec', function() {
        var index = $('.hireJobSecRow').length;
        if (index == 1) {
            $('.checkHiring').prop('checked', false);
            $('.hireJobMainSection').hide();
        } else {
            $(this).parent().parent().remove();
        }
    });

    $('#bussPhoneCode').select2();
    $('#ownerPhoneCode').select2();

    $(document).on('change', '.businessVideo', function(event){
        const file = event.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            $('#businessVideoFrame').removeClass('d-none');
            $('#businessVideoFrame').attr('src', url);
        }
    });

    $(document).on('change', '.businessVideoType', function(){
        var type = $(this).val();
        businessVideoType(type);
    });

    function businessVideoType(type) {
        if (type == 'youtube') {
            $('.businessVideo').attr('type', 'text');
        } else {
            $('.businessVideo').attr('type', 'file');
        }
    }

    $(document).on('change', '#allDays', function() {
        if ($(this).is(":checked")) {
            $('.weekDays').prop('checked', true);
            let days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            for (let index = 0; index < days.length; index++) {
                $('.days').append(`@include('admin.components.business-time', ['day' => '${days[index]}'])`);
            }
        } else {
            $('.weekDays').prop('checked', false);
            $('.days').html('');
        }
    });
</script>
