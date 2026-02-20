@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            {{-- Карточка входа --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                {{-- Шапка --}}
                <div class="card-header bg-primary text-white text-center p-4 border-0">
                    <h4 class="fw-semibold mb-0">Вход в систему</h4>
                </div>

                {{-- Тело формы --}}
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label small text-secondary fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Email
                            </label>
                            <input type="email"
                                   class="form-control form-control-lg bg-light border-0 @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="email@example.com"
                                   required
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Пароль с кнопкой показа --}}
                        <div class="mb-4">
                            <label for="password" class="form-label small text-secondary fw-semibold">
                                <i class="bi bi-lock me-1"></i>Пароль
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control form-control-lg bg-light border-0 @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="••••••••"
                                       required>
                                <button class="btn btn-light border-0" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Запомнить меня --}}
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    Запомнить меня
                                </label>
                            </div>
                        </div>

                        {{-- Кнопка входа --}}
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold rounded-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Показать/скрыть пароль
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
@endpush
@endsection
