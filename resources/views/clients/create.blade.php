@extends('panel')

@section('title', 'Create client')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Create client
		</div>

		<div class="p-3">
			<form action="{{ route('clients.store') }}" method="post">
				@csrf
				<div class="mb-3">
					<label for="inputCompany" class="form-label">Company</label>
					<input class="form-control" type="text" id="inputCompany" name="company">
				</div>

				@error('company')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputVat" class="form-label">VAT</label>
					<input class="form-control" type="text" id="inputVat" name="vat">
				</div>

				@error('vat')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputAddress" class="form-label">Address</label>
					<input class="form-control" type="text" id="inputAddress" name="address">
				</div>

				@error('address')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="form-check mb-3">
					<input class="form-check-input" type="checkbox" name="is_active" value="1" id="flexCheckDefault" checked>
					<label class="form-check-label" for="flexCheckDefault">
						Active
					</label>
				</div>

				@error('is_active')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<button class="btn btn-primary">Create</button>
			</form>
		</div>
    </div>
@endsection
