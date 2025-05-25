@extends('laravel-acl::admin.layouts.app')

@section('content')
    <div class="card">
        <h2 style="margin-bottom: 1rem;">Create Role</h2>

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Role Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div class="checkbox-group">
                    @foreach($permissions as $permission)
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
@endsection 