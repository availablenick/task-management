@extends('panel')

@section('title', 'Clients')

@section('subcontent')
	<div>
		<a class="btn btn-primary" href="{{ route('clients.create') }}">
			Create client
		</a>
	</div>

    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
        <div class="border-bottom p-3">
			Client list
		</div>

		<div class="p-3">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Company</th>
						<th>VAT</th>
						<th>Address</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($clients as $client)
						<tr class="align-middle">
							<td>
								<a href="{{ route('clients.show', $client) }}">
									{{ $client->company }}
								</a>
							</td>
							<td>{{ $client->vat }}</td>
							<td>{{ $client->address }}</td>
							<td>{{ $client->status }}</td>
							<td>
								<a class="btn btn-primary" href="{{ route('clients.edit', $client) }}">
									Edit
								</a>
								<form class="d-inline" action="{{ route('clients.destroy', $client) }}" method="post">
									@method('DELETE')
									@csrf
									<button
										class="custom-btn-delete btn btn-danger"
										data-message="Delete this client?"
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
