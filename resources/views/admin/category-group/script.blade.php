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
        if (index > 1) {
            $(this).parent().parent().remove();
            updateRemoveButtonState('serviceSectionRow', 'removeJobSec');
        }
    });

    $(document).on('click', '.appendOptionSec', function() {
        var index = $('.optionSection').length;
        index = index + 1;
        $('.optionSection').append(`@include('admin.components.options', ['index' => '${index}'])`);
        updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
    });

    $(document).on('click', '.removeOptionSec', function() {
        var index = $('.optionSection').length;
        if (index > 1) {
            $(this).parent().parent().remove();
            updateRemoveButtonState('optionSectionRow', 'removeOptionSec');
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
</script>
