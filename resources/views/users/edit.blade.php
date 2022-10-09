@extends('panel')

@section('title', 'Edit user ' . $user->email)

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Edit user {{ $user->email }}
		</div>

		<div class="p-3">
			<form action="{{ route('users.update', $user) }}" method="post" enctype="multipart/form-data">
				@method('PUT')
				@csrf
				<div class="mb-3">
					<label for="inputName" class="form-label">Name</label>
					<input
						class="form-control"
						type="text"
						id="inputName"
						name="name"
						value="{{ old('name', $user->name) }}"
					>
				</div>

				@error('name')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputEmail" class="form-label">Email</label>
					<input
						class="form-control"
						type="text"
						id="inputEmail"
						name="email"
						value="{{ old('email', $user->email) }}"
					>
				</div>

				@error('email')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="inputAvatar" class="form-label">Avatar</label>
					<input class="form-control" type="file" name="avatar" id="inputAvatar" >
				</div>

				<button class="btn btn-primary">Edit</button>
			</form>
		</div>
    </div>
@endsection
