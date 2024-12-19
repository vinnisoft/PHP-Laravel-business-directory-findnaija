<div class="widget-content-right">
    <a href="{{ route('category-group.edit', $id) }}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
    <a href="#" class="deleteCategoryGroup" data-url="{{ route('category-group.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
</div>