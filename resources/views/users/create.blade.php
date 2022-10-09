@extends('panel')

@section('title', 'Create user')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Create user
		</div>

		<div class="p-3">
			<form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="mb-3">
					<label for="inputName" class="form-label">Name</label>
					<input class="form-control" type="text" id="inputName" name="name">
				</div>

				@error('name')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputEmail" class="form-label">Email</label>
					<input class="form-control" type="text" id="inputEmail" name="email">
				</div>

				@error('email')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputPassword" class="form-label">Password</label>
					<input
						class="form-control"
						type="password"
						name="password"
						id="inputPassword"
						aria-describedby="passwordHelpBlock"
					>
					<div id="passwordHelpBlock" class="form-text">
						Your password must be at least 8 characters long.
					</div>
				</div>

				@error('password')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputPasswordConfirmation" class="form-label">Password confirmation</label>
					<input class="form-control" type="password" name="password_confirmation" id="inputPasswordConfirmation">
				</div>

				<div class="mb-3">
					<label for="inputAvatar" class="form-label">Avatar</label>
					<input class="form-control" type="file" name="avatar" id="inputAvatar">
				</div>

				<button class="btn btn-primary">Create</button>
			</form>
		</div>
    </div>
@endsection
