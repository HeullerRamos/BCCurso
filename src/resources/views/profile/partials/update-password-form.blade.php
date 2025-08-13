<section>
    <div class="mb-4">
        <h5 class="mb-3">
            <i class="fas fa-lock"></i> {{ __('Atualizar Senha') }}
        </h5>
        <p class="text-muted small">
            {{ __('Tenha certeza de que a senha utilizada seja longa e aleatória para estar seguro.') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="row">
            <div class="col-12 mb-3">
                <label for="current_password" class="form-label">{{ __('Senha Atual') }} *</label>
                <input id="current_password" name="current_password" type="password" 
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                       autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">{{ __('Nova Senha') }} *</label>
                <input id="password" name="password" type="password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Mínimo 8 caracteres, incluindo maiúsculas, minúsculas e números.</div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">{{ __('Confirmar Senha') }} *</label>
                <input id="password_confirmation" name="password_confirmation" type="password" 
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn custom-button">
                <i class="fas fa-shield-alt"></i> {{ __('Atualizar Senha') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show mb-0 py-2" role="alert">
                    <small><i class="fas fa-check-circle"></i> {{ __('Senha atualizada com sucesso!') }}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </form>
</section>
