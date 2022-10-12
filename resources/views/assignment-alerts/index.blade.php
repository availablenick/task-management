@extends('panel')

@section('title', 'Alerts')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Alerts
		</div>

		<div class="p-3">
			@forelse ($alerts as $alert)
				<div class="border-bottom">
					<div>
						Project:
						<a href="{{ route('projects.show', $alert->project) }}">
							{{ $alert->project->title }}
						</a>
					</div>
					<div>At: {{ $alert->created_at }}</div>
				</div>
			@empty
				There are no alerts
			@endforelse
		</div>
    </div>
@endsection
