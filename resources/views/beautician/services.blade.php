@extends('layouts.beautician')

@section('title', 'My Services - Beautician Studio')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">My services</h1>
            <p class="page-head__sub">Add treatments, adjust prices and hide services temporarily.</p>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <section class="panel">
            <div class="panel__head">
                <h2 class="panel__title">Add a new service</h2>
            </div>

            <div class="panel__body">
                <form action="{{ route('beautician.services.store') }}" method="POST">
                    @csrf

                    <div class="field">
                        <label for="service_name">Service name</label>
                        <input type="text" id="service_name" name="service_name" required placeholder="e.g. Bridal Makeup">
                    </div>

                    <div class="field">
                        <label for="price">Price (Rs.)</label>
                        <input type="number" id="price" name="price" required min="0" step="50" placeholder="3500">
                    </div>

                    <div class="field">
                        <label for="duration">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" required min="5" step="5" placeholder="60">
                    </div>

                    <div class="field">
                        <label for="description">Description (optional)</label>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Products used, preparation steps..."></textarea>
                    </div>

                    <button type="submit" class="btn btn--accent btn--block">Save service</button>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Listed services</h2>
                    <p class="panel__meta">{{ $services->count() }} service{{ $services->count() === 1 ? '' : 's' }} on your profile</p>
                </div>
            </div>

            <div class="panel__body panel__body--flush">
                @if ($services->isEmpty())
                    <div class="empty">
                        <strong>No services yet</strong>
                        Add your first treatment using the form.
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr>
                                        <td>
                                            <strong>{{ $service->service_name }}</strong>
                                            @if ($service->description)
                                                <span class="table__note">{{ Str::limit($service->description, 60) }}</span>
                                            @endif
                                        </td>
                                        <td>Rs. {{ number_format($service->price, 0) }}</td>
                                        <td>{{ $service->duration }} min</td>
                                        <td>
                                            <span class="pill pill--{{ $service->is_active ? 'confirmed' : 'cancelled' }}">
                                                {{ $service->is_active ? 'Active' : 'Hidden' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="table__actions">
                                                <form action="{{ route('beautician.services.toggle', $service->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="action-btn">
                                                        {{ $service->is_active ? 'Hide' : 'Activate' }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('beautician.services.destroy', $service->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this service permanently?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn--danger" title="Delete service">
                                                        <x-icon name="trash" :size="13" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection