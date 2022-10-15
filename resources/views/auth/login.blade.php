@extends('layout')

@section('title', 'Login')

@section('content')
	<div class="row h-100 d-flex justify-content-center align-items-center">
		<div class="col-5">
			<h1 class="mb-5">Login</h1>
			<form
				class="card p-3 bg-light"
				action="{{ route('authenticate') }}"
				method="post"
			>
				@csrf
				<div class="mb-3">
					<label class="form-label" for="email">Email address</label>
					<input class="form-control" type="email" name="email" id="email" placeholder="name@example.com">
				</div>

				<div class="mb-3">
					<label class="form-label" for="password">Password</label>
					<input class="form-control" type="password" name ="password" id="password">
				</div>

				@error('password')
					<div class="alert alert-danger">
						{{ $message }}
					</div>
				@enderror

				@error('email')
					<div class="alert alert-danger">
						{{ $message }}
					</div>
				@enderror

				<div>
					<button type="submit" class="btn btn-dark">Log in</button>
				</div>
			</div>
		</div>
	</div>
@endsection
