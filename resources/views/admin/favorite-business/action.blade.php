<div class="widget-content-right">
    <a href="{{ route('favorite-business.edit', $id) }}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
    <a href="#" class="deleteFavoriteBusiness" data-url="{{ route('favorite-business.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
</div>