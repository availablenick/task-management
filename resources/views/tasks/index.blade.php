@extends('panel')

@section('title', 'Tasks')

@section('subcontent')
	<div>
		<a class="btn btn-primary" href="{{ route('tasks.create') }}">
			Create task
		</a>
	</div>

    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Task list
		</div>

		<div class="p-3">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Title</th>
						<th>Description</th>
						<th>Project</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tasks as $task)
						<tr class="align-middle">
							<td>
								<a href="{{ route('tasks.show', $task) }}">
									{{ $task->title }}
								</a>
							</td>
							<td>{{ $task->description }}</td>
							<td>{{ $task->project->title }}</td>
							<td>
								<a class="btn btn-primary" href="{{ route('tasks.edit', $task) }}">
									Edit
								</a>
								<form class="d-inline" action="{{ route('tasks.destroy', $task) }}" method="post">
									@method('DELETE')
									@csrf
									<button
										class="custom-btn-delete btn btn-danger"
										data-message="Delete this task?"
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
