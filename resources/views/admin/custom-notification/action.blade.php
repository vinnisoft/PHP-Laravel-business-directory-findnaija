<div class="widget-content-right">
    <a href="{{ route('custom-notification.recend', $id) }}"><button class="border-0 btn-transition btn btn-outline-warning"><i class="fa fa-share-square"></i></button></a>
    <a href="{{ route('custom-notification.edit', $id) }}"><button class="border-0 btn-transition btn btn-outline-success"><i class="fa fa-edit"></i></button></a>
    <a href="#" class="deleteNotification" data-url="{{ route('custom-notification.destroy', $id) }}"><button class="border-0 btn-transition btn btn-outline-danger"><i class="fa fa-trash"></i></button></a>
</div>