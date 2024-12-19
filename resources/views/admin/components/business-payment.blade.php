<div class="row paymentSecRow">
    <div class="col-md-11">
        <div class="row">
            <div class="col-md-6">
                <div class="position-relative form-group">
                    {!! Form::label("payment[$index][type]", 'Enter Platform') !!}
                    {!! Form::text("payment[$index][type]", @$data->type, ['class' => 'form-control', 'placeholder' => 'Enter Platform']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="position-relative form-group">
                    {!! Form::label("payment[$index][url]", 'Enter URL') !!}
                    {!! Form::text("payment[$index][url]", @$data->url, ['class' => 'form-control', 'placeholder' => 'Enter URL']) !!}
                </div>
            </div>
        </div>    
    </div>
    <div class="col-md-1 mt-4">
        <span class="btn btn-danger w-100 removePaymentSec"><i class="fa fa-trash"></i></span>
    </div>   
</div> 
