@extends('layout')

@section('title', 'Verify email')

@section('content')
	<div class="row d-flex justify-content-center align-items-center text-center h-100">
		<div class="d-flex flex-column justify-content-between col-5 rounded-1 shadow bg-light h-50 py-5">
			<div>
				<h1 class="fs-3 mb-4">Please, verify your email</h1>

				<p>
					An email was sent to <b>{{ Auth::user()->email }}</b>
				</p>

				<p>
					Just click on the link in that email to complete your signup.
					<br>
					If you don't see it, you may need to check your spam folder.
				</p>
			</div>
			<div>
				<p class="mb-3">
					If you can't find the email
				</p>

				<form class="mb-3" action="{{ route('verification.send') }}" method="post">
					@csrf
					<button class="btn btn-dark">Resend email</button>
				</form>

				<div>
					<b>{{ session('message') }}</b>
				</div>
			</div>
		</div>
	</div>
@endsection
