@extends('panel')

@section('title', $user->name . '\'s information')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			@php
				$avatarPath = $user->avatar_path ?? $user::DEFAULT_AVATAR_PATH;
			@endphp

			<img
				class="img-fluid"
				width="150"
				src="{{ asset('storage/' . $avatarPath) }}"
				alt="avatar"
			>
		</div>

		<div class="p-3">
			<div>{{ $user->name }}</div>
			<div>{{ $user->email }}</div>
			<div>
				<a href="{{ route('users.edit', $user) }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
			</div>
		</div>
    </div>
@endsection
