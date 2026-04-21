@extends('backend.inc.app')
@section('title', isset($supplier) ? 'Ta\'minotchini Tahrirlash' : 'Yangi Ta\'minotchi')
@section('content')
    <div class="container-fluid" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    {{ isset($supplier) ? 'Ta\'minotchini Tahrirlash' : 'Yangi Ta\'minotchi Qo\'shish' }}
                </h4>
            </div>
            <a href="{{ route('warehouse.suppliers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Orqaba
            </a>
        </div>

        <form action="{{ isset($supplier) ? route('warehouse.supplierUpdate', $supplier) : route('warehouse.supplierStore') }}"
              method="POST">
            @csrf
            @if(isset($supplier))
                @method('PUT')
            @endif

            {{-- Asosiy Ma'lumotlar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 Asosiy Ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ta'minotchi Nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $supplier->name ?? '') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kontakt Odam</label>
                                <input type="text" name="contact_person" class="form-control"
                                       value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="tel" name="phone" class="form-control"
                                       value="{{ old('phone', $supplier->phone ?? '') }}"
                                       placeholder="+998901234567">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $supplier->email ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Manzili</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address', $supplier->address ?? '') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shahar</label>
                                <input type="text" name="city" class="form-control"
                                       value="{{ old('city', $supplier->city ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mamlakat</label>
                                <input type="text" name="country" class="form-control"
                                       value="{{ old('country', $supplier->country ?? 'O\'zbekiston') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Moliyaviy Ma'lumotlar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">💰 Moliyaviy Ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TIN (Vergi ID)</label>
                                <input type="text" name="tin" class="form-control"
                                       value="{{ old('tin', $supplier->tin ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Bank Hisob</label>
                                <input type="text" name="bank_account" class="form-control"
                                       value="{{ old('bank_account', $supplier->bank_account ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To'lov Shartlari</label>
                                <select name="payment_terms" class="form-select">
                                    <option value="">-- Tanlang --</option>
                                    <option value="cash" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'cash' ? 'selected' : '' }}>
                                        💰 Naqd
                                    </option>
                                    <option value="credit" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'credit' ? 'selected' : '' }}>
                                        🔄 Kredit
                                    </option>
                                    <option value="transfer" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'transfer' ? 'selected' : '' }}>
                                        📤 Transfer
                                    </option>
                                    <option value="check" {{ old('payment_terms', $supplier->payment_terms ?? '') == 'check' ? 'selected' : '' }}>
                                        ✓ Chek
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Yetkazib Berish Vaqti (kunlar)</label>
                                <input type="number" name="delivery_time" class="form-control"
                                       value="{{ old('delivery_time', $supplier->delivery_time ?? '') }}"
                                       min="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tugmalar --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i>
                        {{ isset($supplier) ? 'Yangilash' : 'Qo\'shish' }}
                    </button>
                    <a href="{{ route('warehouse.suppliers') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-1"></i> Bekor Qilish
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
