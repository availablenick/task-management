@extends('panel')

@section('title', 'Projects')

@section('subcontent')
	<div>
		<a class="btn btn-primary" href="{{ route('projects.create') }}">
			Create project
		</a>
	</div>

    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Project list
		</div>

		<div class="p-3">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Deadline</th>
						<th>Status</th>
						<th>Client</th>
						<th>Assigned user</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($projects as $project)
						<tr class="align-middle">
							<td>
								<a href="{{ route('projects.show', $project) }}">
									{{ $project->title }}
								</a>
							</td>
							<td>{{ $project->formattedDeadline }}</td>
							<td>{{ $project->formattedStatus }}</td>
							<td>{{ $project->client->company }}</td>
							<td>{{ $project->user->email }}</td>
							<td>
								<a class="btn btn-primary" href="{{ route('projects.edit', $project) }}">
									Edit
								</a>
								<form class="d-inline" action="{{ route('projects.destroy', $project) }}" method="post">
									@method('DELETE')
									@csrf
									<button
										class="custom-btn-delete btn btn-danger"
										data-message="Delete this project?"
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
