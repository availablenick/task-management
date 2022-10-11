@extends('panel')

@section('title', $task->title . '\'s information')

@section('subcontent')
    <div class="border rounded-1 mt-2" style="background: #fff">
		<div class="p-3">
			<div>Title: {{ $task->deadline }}</div>
			<div>Description: {{ $task->description }}</div>
			<div>Project: {{ $task->project->title }}</div>
			<div>
				<a href="{{ route('tasks.edit', $task) }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
			</div>
		</div>
    </div>
@endsection
