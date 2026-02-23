@extends('backend.inc.app')
@section('title', 'Xodimni Tahrirlash')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Xodimni Tahrirlash</h4>
                <small class="text-muted">
                    {{ $employee->user?->name ?? 'User topilmadi' }}
                </small>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaga
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        {{-- ASOSIY MA'LUMOTLAR --}}
                        <div class="col-12">
                            <h6 class="fw-semibold text-muted text-uppercase mb-0">
                                <i class="fas fa-user me-1"></i> Asosiy Ma'lumotlar
                            </h6>
                            <hr class="mt-2">
                        </div>

                        {{-- NAME --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Ism Familiya <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $employee->user?->name) }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $employee->user?->email) }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PASSWORD --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Yangi Parol
                                <small class="text-muted fw-normal">
                                    (bo'sh qoldirilsa o'zgarmaydi)
                                </small>
                            </label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Yangi parol kiriting...">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select name="role"
                                    class="form-select @error('role') is-invalid @enderror">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $employee->user?->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- QO'SHIMCHA MA'LUMOTLAR --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase mb-0">
                                <i class="fas fa-info-circle me-1"></i> Qo'shimcha Ma'lumotlar
                            </h6>
                            <hr class="mt-2">
                        </div>

                        {{-- PHONE --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefon</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $employee->phone) }}"
                                   placeholder="+998 90 000 00 00">
                        </div>

                        {{-- SALARY --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Maosh (UZS)</label>
                            <input type="number" name="salary" class="form-control"
                                   value="{{ old('salary', $employee->salary) }}"
                                   min="0">
                        </div>

                        {{-- ADDRESS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Manzil</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ old('address', $employee->address) }}">
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"
                                    {{ $employee->status === 'active' ? 'selected' : '' }}>
                                    ✅ Faol
                                </option>
                                <option value="inactive"
                                    {{ $employee->status === 'inactive' ? 'selected' : '' }}>
                                    ❌ Nofaol
                                </option>
                            </select>
                        </div>

                        {{-- BUTTONS --}}
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> Yangilash
                            </button>

                            <a href="{{ route('employees.index') }}"
                               class="btn btn-outline-secondary ms-2">
                                Bekor qilish
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
