@extends('backend.inc.app')
@section('title', 'Yangi Xodim')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Yangi Xodim</h4>
                <small class="text-muted">Yangi xodim qo'shish</small>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        {{-- Asosiy ma'lumotlar --}}
                        <div class="col-12">
                            <h6 class="fw-semibold text-muted text-uppercase mb-0">
                                <i class="fas fa-user me-1"></i> Asosiy Ma'lumotlar
                            </h6>
                            <hr class="mt-2">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ism Familiya <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Ism Familiya">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" placeholder="email@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Parol <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kamida 6 ta belgi">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="">— Tanlang —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Qo'shimcha ma'lumotlar --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase mb-0">
                                <i class="fas fa-info-circle me-1"></i> Qo'shimcha Ma'lumotlar
                            </h6>
                            <hr class="mt-2">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefon</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" placeholder="+998 90 000 00 00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maosh (UZS)</label>
                            <input type="number" name="salary" class="form-control"
                                   value="{{ old('salary', 0) }}" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Manzil</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ old('address') }}" placeholder="Shahar, ko'cha...">
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

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Saqlash
                            </button>
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary ms-2 mt-2">
                                Bekor qilish
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
