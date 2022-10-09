@extends('panel')

@section('title', 'Users')

@section('subcontent')
	<div>
		<a class="btn btn-primary" href="{{ route('users.create') }}">
			Create user
		</a>
	</div>

    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			User list
		</div>

		<div class="p-3">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Avatar</th>
						<th>Name</th>
						<th>Email</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($users as $user)
						<tr class="align-middle">
							<td>
								@php
									$avatarPath = $user->avatar_path ?? $user::DEFAULT_AVATAR_PATH;
								@endphp

								<img
									class="img-fluid"
									width="50"
									src="{{ asset('storage/' . $avatarPath) }}"
									alt="avatar"
								>
							</td>
							<td>{{ $user->name }}</td>
							<td>
								<a href="{{ route('users.show', $user) }}">
									{{ $user->email }}
								</a>
							</td>
							<td>
								<a class="btn btn-primary" href="{{ route('users.edit', $user) }}">
									Edit
								</a>
								<form class="d-inline" action="{{ route('users.destroy', $user) }}" method="post">
									@method('DELETE')
									@csrf
									<button
										class="custom-btn-delete btn btn-danger"
										data-message="Delete this user?"
									>
										Delete
									</button>
								</form>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
    </div>
@endsection
