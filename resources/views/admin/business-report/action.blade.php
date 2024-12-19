<div class="widget-content-right">
    <a href="{{ route('business-report.show', $id) }}"><button class="border-0 btn-transition btn btn-outline-warning"><i class="fa fa-eye"></i></button></a>
    <a href="#" class="deleteBusinessReport" data-url="{{ route('business-report.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
</div>