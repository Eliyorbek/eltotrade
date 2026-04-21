@extends('backend.inc.app')
@section('title', 'Xodimlar')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Xodimlar</h4>
                <small class="text-muted">Jami: {{ $employees->total() }} ta xodim</small>
            </div>
            @can('users.create')
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Yangi Xodim
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Xodim</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Role</th>
                        <th>Maosh</th>
                        <th>Status</th>
                        @canany(['users.edit', 'users.delete'])
                            <th class="text-end pe-3">Amallar</th>
                        @endcanany
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>

                            {{-- USER --}}
                            <td>
                                @if($employee->user)
                                    <div class="fw-semibold">{{ $employee->user->name }}</div>
                                    <small class="text-muted">{{ $employee->address ?? '—' }}</small>
                                @else
                                    <div class="text-danger">User biriktirilmagan</div>
                                @endif
                            </td>

                            <td>{{ $employee->user?->email ?? '—' }}</td>
                            <td>{{ $employee->phone ?? '—' }}</td>

                            {{-- ROLE --}}
                            <td>
                                @foreach($employee->user?->roles ?? [] as $role)
                                    @php
                                        $color = match($role->name) {
                                            'admin'   => 'danger',
                                            'manager' => 'primary',
                                            'seller'  => 'success',
                                            default   => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>

                            <td>{{ number_format($employee->salary, 0) }} UZS</td>

                            <td>
                            <span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $employee->status === 'active' ? 'Faol' : 'Nofaol' }}
                            </span>
                            </td>

                            @canany(['users.edit', 'users.delete'])
                                <td class="text-end pe-3 d-flex align-items-center">
                                    @can('users.edit')
                                        <a href="{{ route('employees.edit', $employee) }}"
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('users.delete')
                                        @if($employee->user && $employee->user_id !== auth()->id())
                                            <form action="{{ route('employees.destroy', $employee) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('{{ $employee->user->name }} ni o\'chirishni tasdiqlaysizmi?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                                Xodimlar yo'q
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        {{ $employees->firstItem() }}–{{ $employees->lastItem() }} / {{ $employees->total() }}
                    </small>
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
