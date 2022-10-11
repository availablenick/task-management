@extends('panel')

@section('title', 'Edit task ' . $task->title)

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Edit task {{ $task->title }}
		</div>

		<div class="p-3">
			<form action="{{ route('tasks.update', $task) }}" method="post">
				@method('PUT')
				@csrf
				<div class="mb-3">
					<label for="inputTitle" class="form-label">Title</label>
					<input
						class="form-control"
						type="text"
						id="inputTitle"
						name="title"
						value="{{ old('title', $task->title) }}"
					>
				</div>

				@error('title')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="textareaDescription" class="form-label">Description</label>
					<textarea
						class="form-control"
						id="textareaDescription"
						name="description"
						rows="3"
					>{{ old('description', $task->description) }}</textarea>
				</div>

				<button class="btn btn-primary">Edit</button>
			</form>
		</div>
    </div>
@endsection
