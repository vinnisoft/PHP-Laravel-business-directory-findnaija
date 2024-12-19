<div class="row serviceSectionRow">
    @php
        $route = Route::currentRouteName();
    @endphp
    <div class="col-md-5">
        <div class="position-relative form-group">
            {!! Form::label("service[$index][name]", 'Service') !!}
            {!! Form::text("service[$index][name]", @$data->name, ['class' => 'form-control', 'placeholder' => 'Enter Service']) !!}
        </div> 
    </div>
    <div class="col-md-5">
        <div class="position-relative form-group">
            {!! Form::label("service[$index][icon]", 'Icon') !!}
            {!! Form::file("service[$index][icon]", ['class' => 'form-control', 'id' => "service[$index][icon]"]) !!}
        </div> 
    </div>
    {!! Form::hidden("service[$index][icon_name]", @$data->icon_name) !!}
    {!! Form::hidden("service[$index][id]", @$data->id) !!}
    @if (isset($data->icon))
        <div class="col-md-1 mt-4">
            <img src="{{ @$data->icon }}" alt="" style="width:40px;">
        </div>
    @endif
    <div class="col-md-{{ $route == 'category.edit' ? '1' : '2' }} mt-4">
        <button type="button" class="btn btn-danger w-100 removeJobSec" data-url="{{ isset($data) ? route('deleteSubCategory', $data->id) : '' }}"><i class="fa fa-trash"></i></button>
    </div>   
</div> 
