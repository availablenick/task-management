@extends('panel')

@section('title', 'Create task')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Create task
		</div>

		<div class="p-3">
			<form action="{{ route('tasks.store') }}" method="post">
				@csrf
				<div class="mb-3">
					<label for="inputTitle" class="form-label">Title</label>
					<input class="form-control" type="text" id="inputTitle" name="title">
				</div>

				@error('title')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="textareaDescription" class="form-label">Description</label>
					<textarea class="form-control" id="textareaDescription" name="description" rows="3"></textarea>
				</div>

				<div class="mb-3">
					<label for="selectProject" class="form-label">Project</label>
					<select class="form-select" id="selectProject" name="project_title">
						@foreach ($projects as $project)
							<option>{{ $project->title }}</option>
						@endforeach
					</select>
				</div>

				<button class="btn btn-primary">Create</button>
			</form>
		</div>
    </div>
@endsection
