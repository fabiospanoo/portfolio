<x-admin-layout title="2fa // admin">
    <div class="admin-card admin-auth">
        <p class="admin-eyebrow">./ two-factor</p>
        <h2 class="admin-title">Verification code</h2>
        <p class="admin-hint mt-2">Open your authenticator app and enter the 6-digit code for this site.</p>

        <form action="{{ route('admin.2fa.verify') }}" method="POST" class="admin-form mt-4">
            @csrf

            <div class="mb-4">
                <label for="code" class="form-label"><span class="text-comment">./ </span>code</label>
                <input type="text" class="form-control admin-code-input" id="code" name="code"
                    inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9 ]*"
                    placeholder="000000" autofocus required>
                @error('code') <div class="admin-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-quaternary w-100">Verify</button>
        </form>

        <form action="{{ route('admin.2fa.reset') }}" method="POST" class="mt-3 text-center" onsubmit="return confirm('Reset 2FA and configure a new QR code?');">
            @csrf
            <button type="submit" class="btn btn-link text-comment p-0" style="font-size: 0.82rem; text-decoration: none;">
                Lost authenticator app? Reset 2FA
            </button>
        </form>
    </div>
</x-admin-layout>
