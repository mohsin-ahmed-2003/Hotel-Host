@extends('admin.layout')

@section('title', 'Add User')
@section('page-title', 'Add User')
@section('page-subtitle', 'Create a new user account')

@section('styles')
<style>
    .field-err { font-size: 12px; color: var(--danger); margin-top: 4px; display: none; }
    .form-control.err { border-color: var(--danger); }
</style>
@endsection

@section('content')
<div style="max-width:760px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">User Information</span>
            <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">← Back</a>
        </div>
        <div class="card-body">
            <form id="createUserForm" action="{{ route('admin.users.store') }}" method="POST" novalidate>
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="f_name" name="name" class="form-control" value="{{ old('name') }}" placeholder="John Doe" required>
                        <div class="field-err" id="e_name"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="f_email" name="email" class="form-control" value="{{ old('email') }}" placeholder="john@example.com" required>
                        <div class="field-err" id="e_email"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <select id="f_country" name="country_id" class="form-control" data-type="country-code" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                        data-phone-code="{{ $country->phone_code }}"
                                        data-name="{{ $country->country_name }}"
                                        {{ old('country_id')==$country->id ? 'selected':'' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="field-err" id="e_country"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" id="f_phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 555 000 0000" required>
                        <div class="field-err" id="e_phone"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" id="f_password" name="password" class="form-control" placeholder="Min 8 chars, upper, lower, number, symbol" required>
                        <div class="field-err" id="e_password"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" id="f_dob" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}" required>
                        <div class="field-err" id="e_dob"></div>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Gender</label>
                        <select id="f_gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="male"   {{ old('gender')=='male'   ? 'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender')=='female' ? 'selected':'' }}>Female</option>
                            <option value="other"  {{ old('gender')=='other'  ? 'selected':'' }}>Other</option>
                        </select>
                        <div class="field-err" id="e_gender"></div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <a href="{{ route('admin.users') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showErr(id, msg) {
        const el = document.getElementById('e_' + id);
        const input = document.getElementById('f_' + id);
        if (el) { el.textContent = msg; el.style.display = 'block'; }
        if (input) input.classList.add('err');
    }
    function clearErr(id) {
        const el = document.getElementById('e_' + id);
        const input = document.getElementById('f_' + id);
        if (el) el.style.display = 'none';
        if (input) input.classList.remove('err');
    }

    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valid = true;
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneReg = /^\+?[0-9\s\-\(\)]{7,20}$/;
        const pwReg    = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        const name = document.getElementById('f_name').value.trim();
        if (!name) { showErr('name', 'Full name is required.'); valid = false; } else clearErr('name');

        const email = document.getElementById('f_email').value.trim();
        if (!email) { showErr('email', 'Email address is required.'); valid = false; }
        else if (!emailReg.test(email)) { showErr('email', 'Enter a valid email address.'); valid = false; }
        else clearErr('email');

        const country = document.getElementById('f_country').value;
        if (!country) { showErr('country', 'Please select a country.'); valid = false; } else clearErr('country');

        const phone = document.getElementById('f_phone').value.trim();
        if (!phone) { showErr('phone', 'Phone number is required.'); valid = false; }
        else if (!phoneReg.test(phone)) { showErr('phone', 'Enter a valid phone number.'); valid = false; }
        else clearErr('phone');

        const pw = document.getElementById('f_password').value;
        if (!pw) { showErr('password', 'Password is required.'); valid = false; }
        else if (!pwReg.test(pw)) { showErr('password', 'Min 8 chars with uppercase, lowercase, number & symbol.'); valid = false; }
        else clearErr('password');

        const dob = document.getElementById('f_dob').value;
        if (!dob) { showErr('dob', 'Date of birth is required.'); valid = false; }
        else {
            const age = Math.floor((new Date() - new Date(dob)) / (365.25 * 24 * 60 * 60 * 1000));
            if (age < 18) { showErr('dob', 'User must be at least 18 years old.'); valid = false; }
            else clearErr('dob');
        }

        const gender = document.getElementById('f_gender').value;
        if (!gender) { showErr('gender', 'Please select a gender.'); valid = false; } else clearErr('gender');

        if (valid) this.submit();
    });
</script>
@endsection
