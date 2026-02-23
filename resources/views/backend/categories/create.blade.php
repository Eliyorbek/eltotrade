@extends('backend.inc.app')
@section('title', 'Yangi Kategoriya')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Yangi Kategoriya</h4>
                <small class="text-muted">Yangi kategoriya qo'shish</small>
            </div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomi <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Kategoriya nomi">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    ✅ Faol
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ❌ Nofaol
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Tavsif</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Kategoriya haqida qisqacha...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Saqlash
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
