@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/rates') }}">Rate</a> :
@endsection
@section("contentheader_description", $rate->$view_col)
@section("section", "Rates")
@section("section_url", url(config('laraadmin.adminRoute') . '/rates'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Rates Edit : ".$rate->$view_col)

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="box">
	<div class="box-header">
		
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($rate, ['route' => [config('laraadmin.adminRoute') . '.rates.update', $rate->id ], 'method'=>'PUT', 'id' => 'rate-edit-form']) !!}
					@la_form($module)
					
					{{--
					@la_input($module, 'DATE')
					@la_input($module, 'USD')
					@la_input($module, 'EUR')
					@la_input($module, 'GBP')
					@la_input($module, 'RUB')
					@la_input($module, 'UAH')
					--}}
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/rates') }}">Cancel</a></button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
	$("#rate-edit-form").validate({
		
	});
});
</script>
@endpush
