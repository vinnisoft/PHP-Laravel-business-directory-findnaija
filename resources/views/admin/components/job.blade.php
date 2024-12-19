<div class="row hireJobSecRow">
    <div class="col-md-11">
        <div class="row">
            <div class="col-md-4">
                <div class="position-relative form-group">
                    {!! Form::label("hiring[$index][job_title]", 'Job Position Name') !!}
                    {!! Form::text("hiring[$index][job_title]", @$data->job_title, ['class' => 'form-control', 'placeholder' => 'Enter Owner Phone Number', 'required' => true]) !!}                                        
                </div>
            </div>
            <div class="col-md-4">
                <div class="position-relative form-group">
                    {!! Form::label("hiring[$index][requirement]", 'List out job requirements') !!}
                    {!! Form::text("hiring[$index][requirement]", @$data->requirement, ['class' => 'form-control', 'placeholder' => 'Enter Owner Phone Number', 'required' => true]) !!}                                        
                </div>
            </div>
            <div class="col-md-4">
                <div class="position-relative form-group">
                    {!! Form::label("hiring[$index][amount]", 'Job Pay Amount (optional)') !!}
                    {!! Form::text("hiring[$index][amount]", @$data->amount, ['class' => 'form-control', 'placeholder' => 'Enter Owner Phone Number', 'required' => true]) !!}                                        
                </div>
            </div>
        </div>    
    </div>
    <div class="col-md-1 mt-4">
        <span class="btn btn-danger w-100 removeJobSec"><i class="fa fa-trash"></i></span>
    </div>   
</div> 
