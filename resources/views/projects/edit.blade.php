@extends('panel')

@section('title', 'Edit project ' . $project->title)

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Edit project {{ $project->title }}
		</div>

		<div class="p-3">
			<form action="{{ route('projects.update', $project) }}" method="post">
				@method('PUT')
				@csrf
				<div class="mb-3">
					<label for="inputTitle" class="form-label">Title</label>
					<input
						class="form-control"
						type="text"
						id="inputTitle"
						name="title"
						value="{{ old('title', $project->title) }}"
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
					>{{ old('description', $project->description) }}</textarea>
				</div>

				<div class="mb-3">
					<label for="inputDeadline" class="form-label">Deadline</label>
					<input
						class="form-control"
						type="date"
						id="inputDeadline"
						name="deadline"
						min="{{ (new Datetime())->format('Y-m-d') }}"
						value="{{ old('deadline', $project->deadline) }}"
					>
				</div>

				@error('deadline')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="selectAssignedUser" class="form-label">Assigned user</label>
					<select class="form-select" id="selectAssignedUser" name="user_email">
						@foreach ($users as $user)
							@if ($user->id === $project->user_id)
								<option selected>{{ $user->email }}</option>
							@else
								<option>{{ $user->email }}</option>
							@endif
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="selectAssignedClient" class="form-label">Assigned client</label>
					<select class="form-select" id="selectAssignedClient" name="company">
						@foreach ($clients as $client)
							@if ($client->id === $project->client_id)
								<option selected>{{ $client->company }}</option>
							@else
								<option>{{ $client->company }}</option>
							@endif
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="selectStatus" class="form-label">Status</label>
					<select class="form-select" id="selectStatus" name="status">
						<option value="{{ \App\Models\Project::OPEN_STATUS }}">
							Open
						</option>
						<option value="{{ \App\Models\Project::CLOSED_STATUS }}">
							Closed
						</option>
					</select>
				</div>

				<button class="btn btn-primary">Edit</button>
			</form>
		</div>
    </div>
@endsection
