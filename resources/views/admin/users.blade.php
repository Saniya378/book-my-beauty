@extends('layouts.admin')

@section('title', 'Manage Users - Admin Console')

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">User Account Management</h2>
        <p style="font-size: 13px; opacity: 0.85; margin: 0;">Search, filter by role, activate or suspend user accounts across the platform.</p>
    </div>
</div>

<!-- Search & Filter Bar -->
<div style="background: white; padding: 20px 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 25px;">
    <form action="{{ route('admin.users') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
        <div style="flex: 2; min-width: 200px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email address..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <select name="role" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
                <option value="">All Roles</option>
                <option value="customer" {{ $role === 'customer' ? 'selected' : '' }}>Customers</option>
                <option value="beautician" {{ $role === 'beautician' ? 'selected' : '' }}>Beauticians</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administrators</option>
            </select>
        </div>

        <button type="submit" class="btn" style="padding: 10px 20px; font-size: 13px;">Filter Users</button>
        <a href="{{ route('admin.users') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 13px; text-decoration: none;">Reset</a>
    </form>
</div>

<!-- Users Table -->
<div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #1a1a2e; color: white; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <th style="padding: 14px 18px;">ID</th>
                    <th style="padding: 14px 18px;">User Name</th>
                    <th style="padding: 14px 18px;">Email Address</th>
                    <th style="padding: 14px 18px;">Role</th>
                    <th style="padding: 14px 18px;">Registered</th>
                    <th style="padding: 14px 18px;">Status</th>
                    <th style="padding: 14px 18px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 14px 18px;"><strong>#{{ $u->id }}</strong></td>
                    <td style="padding: 14px 18px;"><strong>{{ $u->name }}</strong></td>
                    <td style="padding: 14px 18px; color: #555;">{{ $u->email }}</td>
                    <td style="padding: 14px 18px;">
                        <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 4px;
                            {{ $u->role === 'beautician' ? 'background:#e8f0fe; color:#1967d2;' : ($u->role === 'admin' ? 'background:#fce8e6; color:#c5221f;' : 'background:#f1f3f4; color:#5f6368;') }}">
                            {{ $u->role }}
                        </span>
                    </td>
                    <td style="padding: 14px 18px; color: var(--text-gray); font-size: 12px;">
                        {{ $u->created_at ? $u->created_at->format('d M Y') : 'N/A' }}
                    </td>
                    <td style="padding: 14px 18px;">
                        <span style="font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 10px;
                            {{ $u->is_active ? 'background:#e8f5e9; color:#2e7d32;' : 'background:#ffebee; color:#c62828;' }}">
                            {{ $u->is_active ? 'Active' : 'Suspended' }}
                        </span>
                    </td>
                    <td style="padding: 14px 18px;">
                        @if($u->role !== 'admin')
                            <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="
                                    background: {{ $u->is_active ? '#ffebee' : '#e8f5e9' }};
                                    color: {{ $u->is_active ? '#c62828' : '#2e7d32' }};
                                    border: 1px solid {{ $u->is_active ? '#ffcdd2' : '#c8e6c9' }};
                                    padding: 5px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">
                                    {{ $u->is_active ? 'Suspend' : 'Reactivate' }}
                                </button>
                            </form>
                        @else
                            <span style="font-size: 11px; color: #999;">System Admin</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-gray);">
                        No users match your criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="padding: 15px 20px;">
       {{ $users->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
