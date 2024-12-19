<div class="widget-content-right">
    @switch(Route::currentRouteName())
        @case('business.index')
            <a href="{{ route('business.show', $id) }}"><button class="border-0 btn-transition btn btn-outline-warning"><i class="fa fa-eye"></i></button></a>
            <a href="{{ route('business.edit', $id) }}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
            <a href="#" class="deleteBusiness" data-url="{{ route('business.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
        @break
        @case('new.business')
            <a href="{{ route('business.show', $id) }}"><button class="border-0 btn-transition btn btn-outline-warning"><i class="fa fa-eye"></i></button></a>
        @break
        @case('recomended.business')
            <a href="{{ route('business.create') }}?id={{$id}}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
        @break
    @endswitch
</div>