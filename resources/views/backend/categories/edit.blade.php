@extends('backend.inc.app')
@section('title', 'Kategoriyani Tahrirlash')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Kategoriyani Tahrirlash</h4>
                <small class="text-muted">{{ $category->name }}</small>
            </div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomi <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $category->name) }}">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>
                                    ✅ Faol
                                </option>
                                <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>
                                    ❌ Nofaol
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Tavsif</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> Yangilash
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn mt-2 btn-outline-secondary ms-2">
                                Bekor qilish
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
