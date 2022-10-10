@extends('panel')

@section('title', $project->title . '\'s information')

@section('subcontent')
    <div class="border rounded-1 mt-2" style="background: #fff">
		<div class="p-3">
			<div>Title: {{ $project->deadline }}</div>
			<div>Description: {{ $project->description }}</div>
			<div>Deadline: {{ $project->formattedDeadline }}</div>
			<div>Status: {{ $project->formattedStatus }}</div>
			<div>Client: {{ $project->client->company }}</div>
			<div>Assigned user: {{ $project->user->email }}</div>
			<div>
				<a href="{{ route('projects.edit', $project) }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
			</div>
		</div>
    </div>
@endsection
