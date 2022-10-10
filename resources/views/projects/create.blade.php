@extends('panel')

@section('title', 'Create project')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Create project
		</div>

		<div class="p-3">
			<form action="{{ route('projects.store') }}" method="post">
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
					<label for="inputDeadline" class="form-label">Deadline</label>
					<input
						class="form-control"
						type="date"
						id="inputDeadline"
						name="deadline"
						min="{{ (new Datetime())->format('Y-m-d') }}"
					>
				</div>

				@error('deadline')
					<div class="alert alert-danger mb-3">{{ $message }}</div>
				@enderror

				<div class="mb-3">
					<label for="selectAssignedUser" class="form-label">Assigned user</label>
					<select class="form-select" id="selectAssignedUser" name="user_email">
						@foreach ($users as $user)
							<option>{{ $user->email }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-3">
					<label for="selectAssignedClient" class="form-label">Assigned client</label>
					<select class="form-select" id="selectAssignedClient" name="company">
						@foreach ($clients as $client)
							<option>{{ $client->company }}</option>
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

				<button class="btn btn-primary">Create</button>
			</form>
		</div>
    </div>
@endsection
