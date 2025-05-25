@extends('laravel-acl::admin.layouts.app')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>Permissions</h2>
            <form action="{{ route('admin.permissions.sync') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">Sync Permissions</button>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Controller</th>
                    <th>Method</th>
                    <th>Roles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->controller }}</td>
                        <td>{{ $permission->method }}</td>
                        <td>
                            @foreach($permission->roles as $role)
                                <span style="display: inline-block; background: #e2e8f0; padding: 0.25rem 0.5rem; border-radius: 0.25rem; margin: 0.25rem;">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection 