@extends('backend.inc.app')
@section('title', 'Kategoriyalar')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Kategoriyalar</h4>
                <small class="text-muted">Jami: {{ $categories->total() }} ta kategoriya</small>
            </div>
            @can('categories.create')
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Yangi Kategoriya
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Nomi</th>
                        <th>Slug</th>
                        <th>Mahsulotlar</th>
                        <th>Status</th>
                        @canany(['categories.edit', 'categories.delete'])
                            <th class="text-end pe-3">Amallar</th>
                        @endcanany
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $category->name }}</div>
                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                            </td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>
                                <span class="badge bg-info">{{ $category->products_count }} ta</span>
                            </td>
                            <td>
                            <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $category->status === 'active' ? 'Faol' : 'Nofaol' }}
                            </span>
                            </td>
                            @canany(['categories.edit', 'categories.delete'])
                                <td class="text-end pe-3 d-flex justify-content-center align-items-center">
                                    @can('categories.edit')
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('categories.delete')
                                        <form action="{{ route('categories.destroy', $category) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('{{ $category->name }} ni o\'chirishni tasdiqlaysizmi?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-25"></i>
                                Kategoriyalar yo'q
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        {{ $categories->firstItem() }}–{{ $categories->lastItem() }} / {{ $categories->total() }}
                    </small>
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
