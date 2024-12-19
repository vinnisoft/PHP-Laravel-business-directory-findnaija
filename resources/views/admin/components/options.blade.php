<div class="row optionSectionRow">
    @php
        $route = Route::currentRouteName();
        // echo '<pre>';
        //     print_r($data->id);
        //     die;
    @endphp
    <div class="col-md-5">
        <div class="position-relative form-group">
            {!! Form::label("option[$index][name]", 'Option') !!}
            {!! Form::text("option[$index][name]", @$data->name, ['class' => 'form-control', 'placeholder' => 'Enter Option']) !!}
        </div>
    </div>
    <div class="col-md-5">
        <div class="position-relative form-group">
            {!! Form::label("option[$index][icon]", 'Icon') !!}
            {!! Form::file("option[$index][icon]", ['class' => 'form-control', 'id' => "option[$index][icon]"]) !!}
        </div> 
    </div>
    {!! Form::hidden("option[$index][icon_name]", @$data->icon_name) !!}
    {!! Form::hidden("option[$index][id]", @$data->id) !!}
    @if (isset($data->icon))
        <div class="col-md-1 mt-4">
            <img src="{{ @$data->icon }}" alt="" style="width:40px;">
        </div>
    @endif
    <div class="col-md-{{ $route == 'category.edit' ? '1' : '2' }} mt-4">
        <button type="button" class="btn btn-danger w-100 removeOptionSec" data-url="{{ isset($data) ? route('deleteOption', @$data->id) : '' }}"><i class="fa fa-trash"></i></button>
    </div>   
</div> 
