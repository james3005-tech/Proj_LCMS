<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – LCMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>

<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:520px;">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <h1>Create Account</h1>
            <p>Join the Legal Case Management System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="two-col">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" required placeholder="Juan dela Cruz">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone"
                           class="form-control"
                           value="{{ old('phone') }}" placeholder="+63 912 345 6789">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" required placeholder="you@example.com">
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           required placeholder="Min. 8 characters">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" required placeholder="Repeat password">
                </div>
            </div>

            <div class="form-group">
                <label for="role">Account Type</label>
                <select id="role" name="role" class="form-control" required onchange="toggleRoleFields(this.value)">
                    <option value="">Select your role</option>
                    <option value="admin"  {{ old('role') === 'admin'  ? 'selected' : '' }}>Admin</option>
                    <option value="lawyer" {{ old('role') === 'lawyer' ? 'selected' : '' }}>Lawyer</option>
                    <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client</option>
                </select>
            </div>

            {{-- Client fields --}}
            <div id="client-fields" style="display:none;">
                <div class="form-group">
                    <label for="address">Home Address</label>
                    <input type="text" id="address" name="address" class="form-control"
                           value="{{ old('address') }}" placeholder="Street, City, Province">
                </div>
            </div>

            {{-- Lawyer fields --}}
            <div id="lawyer-fields" style="display:none;">
                <div class="two-col">
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" class="form-control"
                               value="{{ old('specialization') }}" placeholder="e.g. Criminal Law">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" style="margin-top:0.5rem;">
                Create Account
            </button>
        </form>

        <p style="text-align:center; margin-top:1.2rem; font-size:0.85rem; color:var(--gray-600);">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--navy); font-weight:600;">Sign in</a>
        </p>
    </div>
</div>

<script>
function toggleRoleFields(role) {
    document.getElementById('client-fields').style.display = role === 'client' ? 'block' : 'none';
    document.getElementById('lawyer-fields').style.display = role === 'lawyer' ? 'block' : 'none';
}
// Init on page load (for old() values)
toggleRoleFields('{{ old("role", "") }}');
</script>
</body>
</html>
