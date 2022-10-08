@extends('layout')

@section('content')
	<div class="row h-100">
		<div class="custom-sidebar col-2">
			<div>
				<div class="custom-header py-3 text-center">
					CRM
				</div>

				<nav class="custom-list w-100">
					<a
						class="custom-list-item d-block text-decoration-none"
						href="{{ route('dashboard') }}"
					>
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-gauge-high"></i>
						</span>
						Dashboard
					</a>
					<a
						class="custom-list-item d-block text-decoration-none"
						href="{{ route('users.index') }}"
					>
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-user"></i>
						</span>
						Users
					</a>
					<a
						class="custom-list-item d-block text-decoration-none"
						href="{{ route('clients.index') }}"
					>
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-address-card"></i>
						</span>
						Clients
					</a>
					<a
						class="custom-list-item d-block text-decoration-none"
						href="{{ route('projects.index') }}"
					>
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-file"></i>
						</span>
						Projects
					</a>
					<a
						class="custom-list-item d-block text-decoration-none"
						href="{{ route('tasks.index') }}"
					>
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-list-check"></i>
						</span>
						Tasks
					</a>
				</nav>
			</div>

			<div>
				<form class="custom-list d-table w-100" action="{{ route('logout') }}" method="post">
					@csrf
					<button class="text-start custom-list-item border-0 w-100 px-0">
						<span class="d-inline-block text-center w-25">
							<i class="fa-solid fa-right-from-bracket"></i>
						</span>
						Logout
					</button>
				</form>

				<div class="custom-bottom text-end pe-3">
					<button class="custom-hider p-0 m-0 bg-transparent border-0">
						<i class="fa-solid fa-chevron-left"></i>
					</button>
				</div>
			</div>
		</div>

		<div class="custom-filler col-2"></div>

		<div class="custom-content col-10">
			<nav class="custom-topbar">
				<button type="button" class="custom-item custom-shower">
					<i class="fa-solid fa-bars"></i>
				</button>

				<div class="d-flex align-items-center my-0">
					<button type="button" class="custom-item text-decoration-none">
						<i class="fa-regular fa-bell"></i>
					</button>
					<a class="custom-item text-decoration-none" href="{{ route('users.show', Auth::user()) }}">
						<i class="fa-regular fa-circle-user"></i>
					</a>
				</div>
			</nav>

			<div class="custom-main p-5">
				@yield('subcontent')
			</div>
		</div>
	</div>
@endsection
