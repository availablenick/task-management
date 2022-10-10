@extends('panel')

@section('title', $client->company . '\'s information')

@section('subcontent')
    <div class="border rounded-1 mt-2"
		style="background: #fff"
	>
		<div class="p-3">
			<div>Company: {{ $client->company }}</div>
			<div>VAT: {{ $client->vat }}</div>
			<div>Address: {{ $client->address }}</div>
			<div>Status: {{ $client->is_active ? 'Active' : 'Inactive' }}</div>
			<div>
				<a href="{{ route('clients.edit', $client) }}">
					<i class="fa-solid fa-pen-to-square"></i>
				</a>
			</div>
		</div>
    </div>
@endsection
