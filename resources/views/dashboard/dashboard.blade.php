@extends('panel')

@section('title', 'Dashboard')

@section('subcontent')
    <div class="d-flex flex-column justify-content-between border rounded-1 h-100" style="background: #fff">
        <div class="p-3">
            <div class="card custom-card">
                <div class="card-img-top d-flex align-items-center justify-content-center text-center fs-2">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="card-body">
                    <a class="stretched-link" href="{{ route('users.index') }}"></a>
                    <p class="card-text">Manage users</p>
                </div>
            </div>

            <div class="card custom-card ms-3">
                <div class="card-img-top d-flex align-items-center justify-content-center text-center fs-2">
                    <i class="fa-solid fa-address-card"></i>
                </div>

                <div class="card-body">
                    <a class="stretched-link" href="{{ route('clients.index') }}"></a>
                    <p class="card-text">Manage clients</p>
                </div>
            </div>

            <div class="card custom-card ms-3">
                <div class="card-img-top d-flex align-items-center justify-content-center text-center fs-2">
                    <i class="fa-solid fa-file"></i>
                </div>

                <div class="card-body">
                    <a class="stretched-link" href="{{ route('projects.index') }}"></a>
                    <p class="card-text">Manage projects</p>
                </div>
            </div>
        </div>

        <div class="border-top p-3" style="height: 7rem;">
            <div>Number of users: {{ $userCount }}</div>
            <div>Number of clients: {{ $clientCount }}</div>
            <div>Number of projects: {{ $projectCount }}</div>
        </div>
    </div>
@endsection
