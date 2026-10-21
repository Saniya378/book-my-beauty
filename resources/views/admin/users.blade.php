@extends('layouts.admin')

@section('title', 'Users - Admin Console')

@section('content')
    <div class="admin-head">
        <div>
            <h1 class="admin-head__title">Users</h1>
            <p class="admin-head__subtitle">Customer, beautician and administrator accounts registered on BookMyBeauty</p>
        </div>
    </div>

    <form action="{{ route('admin.users') }}" method="GET" class="filters">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email address">

        <select name="role">
            <option value="">All roles</option>
            <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customers</option>
            <option value="beautician" {{ $role === 'beautician' ? 'selected' : '' }}>Beauticians</option>
            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrators</option>
        </select>

        <button type="submit" class="btn">
            <x-icon name="users" :size="15" />
            Apply filters
        </button>
        <a href="{{ route('admin.users') }}" class="btn btn--ghost">Reset</a>
    </form>

    <section class="panel">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="table__id">{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="pill pill--{{ $user->role }}">{{ $user->role }}</span></td>
                            <td>{{ $user->created_at?->format('j M Y') }}</td>
                            <td>
                                <span class="pill pill--{{ $user->is_active ? 'confirmed' : 'cancelled' }}">
                                    {{ $user->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td>
                                <div class="table__actions">
                                    @if ($user->role !== 'admin')
                                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="action-btn {{ $user->is_active ? 'action-btn--danger' : 'action-btn--approve' }}">
                                                {{ $user->is_active ? 'Suspend' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="table__note">System admin</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No users match this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel__foot">
            {{ $users->links('vendor.pagination.custom') }}
        </div>
    </section>
@endsection