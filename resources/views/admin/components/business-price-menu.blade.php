<div class="row priceSecRow">
    <div class="col-md-11">
        <div class="row">
            <div class="col-md-6">
                <div class="position-relative form-group">
                    {!! Form::label("price[$index][menu]", 'Enter Menu') !!}
                    {!! Form::text("price[$index][menu]", @$data->menu, ['class' => 'form-control', 'placeholder' => 'Enter Menu']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="position-relative form-group">
                    {!! Form::label("price[$index][price]", 'Enter Price') !!}
                    {!! Form::text("price[$index][price]", @$data->price, ['class' => 'form-control', 'placeholder' => 'Enter Price']) !!}
                </div>
            </div>
        </div>    
    </div>
    <div class="col-md-1 mt-4">
        <span class="btn btn-danger w-100 removePaymentSec"><i class="fa fa-trash"></i></span>
    </div>   
</div> 
