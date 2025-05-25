@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Role</h5>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">Back to Roles</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
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
                                                <div class="row">
                                                    @foreach($controllerPermissions as $permission)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                    <div class="fw-bold">{{ $permission->title ?? $permission->name }}</div>
                                                                    <small class="text-muted">{{ $permission->description }}</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 