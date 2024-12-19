<div class="widget-content-right">
    <a href="{{ route('business.index') }}?userId={{$id}}&type=all"><button class="border-0 btn-transition btn btn-outline-warning"><i class="fa fa-eye"></i></button></a>
    <a href="{{ route('users.edit', $id) }}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
    <a href="#" class="deleteUser" data-url="{{ route('users.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
</div>