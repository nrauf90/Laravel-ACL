@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Permissions</h5>
                    <form action="{{ route('admin.permissions.sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Sync Permissions</button>
                    </form>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="accordion" id="permissionsAccordion">
                        @php
                            $groupedPermissions = $permissions->groupBy('controller');
                        @endphp

                        @foreach($groupedPermissions as $controller => $controllerPermissions)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $controller }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $controller }}" aria-expanded="false" aria-controls="collapse{{ $controller }}">
                                        {{ $controller }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $controller }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $controller }}" data-bs-parent="#permissionsAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Method</th>
                                                        <th>Roles</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($controllerPermissions as $permission)
                                                        <tr>
                                                            <td>{{ $permission->title ?? $permission->name }}</td>
                                                            <td>{{ $permission->description }}</td>
                                                            <td>{{ $permission->method }}</td>
                                                            <td>
                                                                @foreach($permission->roles as $role)
                                                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 