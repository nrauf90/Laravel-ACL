@extends('laravel-acl::admin.layouts.app')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>Roles</h2>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Create Role</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach($role->permissions as $permission)
                                <span style="display: inline-block; background: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin: 0.25rem;">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection 