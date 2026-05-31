@extends('admin.layout')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')
@section('page-subtitle', 'Manage application configuration')

@section('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }

    /* Sidebar nav */
    .settings-nav { display: flex; flex-direction: column; gap: 4px; }

    .settings-nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-family: inherit;
        transition: all 0.2s;
    }

    .settings-nav-item:hover { background: var(--bg-2); color: var(--text); }

    .settings-nav-item.active {
        background: var(--primary);
        color: #fff;
    }

    .settings-nav-item .nav-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.4;
        flex-shrink: 0;
    }

    .settings-nav-item.active .nav-dot { opacity: 1; background: #a5b4fc; }

    /* Section panel */
    .settings-panel { display: none; }
    .settings-panel.active { display: block; }

    /* Section header */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .section-title-wrap {}
    .section-title { font-size: 18px; font-weight: 800; color: var(--text); }
    .section-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

    /* Toggle switch */
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }

    .toggle-switch input { display: none; }

    .toggle-track {
        position: absolute;
        inset: 0;
        border-radius: 28px;
        background: var(--border);
        cursor: pointer;
        transition: background 0.3s;
        border: 2px solid transparent;
    }

    .toggle-track::after {
        content: '';
        position: absolute;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #fff;
        top: 2px; left: 2px;
        transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .toggle-switch input:checked + .toggle-track {
        background: var(--success);
    }

    .toggle-switch input:checked + .toggle-track::after {
        transform: translateX(24px);
    }

    .toggle-status {
        font-size: 12px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        transition: all 0.3s;
    }

    .toggle-status.on  { background: rgba(16,185,129,0.15); color: #10b981; }
    .toggle-status.off { background: var(--bg-2); color: var(--text-muted); }

    /* Form fields */
    .settings-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 24px;
    }

    @media (max-width: 600px) { .settings-form-grid { grid-template-columns: 1fr; } }

    .field-group { display: flex; flex-direction: column; gap: 6px; }
    .field-group.full { grid-column: 1 / -1; }

    .field-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .field-input {
        padding: 11px 14px;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        color: var(--text);
        background: var(--bg);
        font-family: inherit;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }

    .field-input.err { border-color: var(--danger); }
    .field-err { font-size: 12px; color: var(--danger); display: none; margin-top: 2px; }

    .field-hint {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
        line-height: 1.5;
    }

    /* Info box */
    .info-box {
        background: rgba(99,102,241,0.08);
        border: 1px solid rgba(99,102,241,0.2);
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-top: 20px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-box svg { flex-shrink: 0; margin-top: 1px; color: var(--primary); }

    /* Save btn */
    .btn-save-settings {
        margin-top: 24px;
        padding: 11px 28px;
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.2s, transform 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-save-settings:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-save-settings:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* File upload field */
    .file-upload-wrap {
        position: relative;
        border: 1.5px dashed var(--border);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: var(--bg);
    }

    .file-upload-wrap:hover { border-color: var(--primary); background: rgba(99,102,241,0.04); }

    .file-upload-wrap input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }

    .file-upload-preview {
        width: 56px; height: 56px;
        object-fit: contain;
        border-radius: 8px;
        margin: 0 auto 8px;
        display: block;
        border: 1px solid var(--border);
        background: var(--card);
        padding: 4px;
    }

    .file-upload-label {
        font-size: 13px;
        color: var(--text-muted);
        display: block;
    }

    .file-upload-label span {
        color: var(--primary);
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="settings-grid">

    <!-- Left nav -->
    <div class="card" style="overflow:visible;">
        <div class="card-body" style="padding:12px;">
            <div class="settings-nav">
                <button class="settings-nav-item active" onclick="switchSection('site', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Site Management
                </button>
                <button class="settings-nav-item" onclick="switchSection('map', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
                    Map Settings
                </button>
                <button class="settings-nav-item" onclick="switchSection('fees', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17l6-10M10 8.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm4 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/></svg>
                    Manage fee
                </button>
                <button class="settings-nav-item" onclick="switchSection('email', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email Settings
                </button>
                <button class="settings-nav-item" onclick="switchSection('recaptcha', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    reCAPTCHA
                </button>
                <button class="settings-nav-item" onclick="switchSection('twilio', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                    Twilio SMS
                </button>
                <button class="settings-nav-item" onclick="switchSection('social', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Social Login
                </button>
                <button class="settings-nav-item" onclick="switchSection('payment', this)">
                    <span class="nav-dot"></span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Payment API
                </button>
            </div>
        </div>
    </div>

    <!-- Right panels -->
    <div>

        <!-- Site Management Panel -->
        <div class="settings-panel active" id="section-site">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Site Management</div>
                            <div class="section-subtitle">Configure your site identity and branding</div>
                        </div>
                    </div>

                    <form id="siteForm" action="{{ route('admin.settings.site') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="settings-form-grid">

                            <div class="field-group full">
                                <label class="field-label">Site Name</label>
                                <input type="text" id="f_site_name" name="site_name"
                                       class="field-input"
                                       value="{{ $settings->get('site_name', 'Hotel Host') }}"
                                       placeholder="Hotel Host">
                                <div class="field-err" id="e_site_name"></div>
                                <div class="field-hint">Displayed in the browser tab and throughout the site</div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Site Logo</label>
                                <div class="file-upload-wrap" id="logoWrap">
                                    @if($settings->get('site_logo'))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('site_logo')) }}" class="file-upload-preview" id="logoPreview">
                                    @else
                                        <img src="" class="file-upload-preview" id="logoPreview" style="display:none;">
                                    @endif
                                    <input type="file" name="site_logo" accept="image/*" onchange="previewFile(this,'logoPreview')">
                                    <span class="file-upload-label"><span>Click to upload</span> or drag & drop<br>PNG, JPG, SVG (max 2MB)</span>
                                </div>
                                <div class="field-hint">Recommended: 200×60px transparent PNG or SVG</div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Site Favicon</label>
                                <div class="file-upload-wrap" id="faviconWrap">
                                    @if($settings->get('site_favicon'))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('site_favicon')) }}" class="file-upload-preview" id="faviconPreview">
                                    @else
                                        <img src="" class="file-upload-preview" id="faviconPreview" style="display:none;">
                                    @endif
                                    <input type="file" name="site_favicon" accept="image/*,.ico" onchange="previewFile(this,'faviconPreview')">
                                    <span class="file-upload-label"><span>Click to upload</span> or drag & drop<br>ICO, PNG (max 512KB)</span>
                                </div>
                                <div class="field-hint">Recommended: 32×32px or 64×64px ICO/PNG</div>
                            </div>

                             <div class="field-group">
                                <label class="field-label">Default Currency</label>
                                <select name="default_currency" class="field-input">
                                    @foreach($currencies as $cur)
                                        <option value="{{ $cur->currency_code }}" {{ $settings->get('default_currency') === $cur->currency_code ? 'selected' : '' }}>
                                            {{ $cur->currency_code }} ({{ $cur->symbol }}) - {{ $cur->currency_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="field-hint">Default currency for properties</div>
                            </div>

                            <div class="field-group full" style="margin-top:20px; border-top:1px solid var(--border); padding-top:20px;">
                                <h4 style="font-size:16px; margin-bottom:10px; color:var(--text);">Hero Section Configuration</h4>
                                <p style="font-size:13px; color:var(--text-muted); margin-bottom:15px;">Configure the dynamic media and text on the homepage.</p>
                                
                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom: 20px;">
                                    <div>
                                        <label class="field-label">Hero Title</label>
                                        <input type="text" name="hero_title" class="field-input" value="{{ $settings->get('hero_title', 'Discover Your Next Perfect Escape') }}" placeholder="Discover Your Next Perfect Escape">
                                        <div class="field-hint">The main title text displayed on the hero background</div>
                                    </div>
                                    <div>
                                        <label class="field-label">Hero Subtitle</label>
                                        <input type="text" name="hero_subtitle" class="field-input" value="{{ $settings->get('hero_subtitle', 'Book uniquely designed hotels, private villas, and compact workspaces tailored for you') }}" placeholder="Book uniquely designed hotels, private villas, and compact workspaces tailored for you">
                                        <div class="field-hint">The secondary subtitle text displayed below the title</div>
                                    </div>
                                </div>

                                <div style="display:flex; gap:20px; flex-wrap:wrap;">
                                    <div style="flex:1;">
                                        <label class="field-label">Hero Media Type</label>
                                        <select name="hero_media_type" id="heroMediaType" class="field-input">
                                            <option value="image" {{ $settings->get('hero_media_type', 'image') === 'image' ? 'selected' : '' }}>Image</option>
                                            <option value="video" {{ $settings->get('hero_media_type') === 'video' ? 'selected' : '' }}>Video</option>
                                        </select>
                                        <div class="field-hint">Select whether to display an image or an autoplaying video.</div>
                                    </div>
                                    
                                    <div style="flex:2;">
                                        <label class="field-label">Hero Media File</label>
                                        <div class="file-upload-wrap" id="heroMediaWrap">
                                            @if($settings->get('hero_media_file'))
                                                @if($settings->get('hero_media_type') === 'video')
                                                    <video src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('hero_media_file')) }}" class="file-upload-preview" id="heroMediaPreview" style="object-fit:cover;" autoplay loop muted></video>
                                                @else
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('hero_media_file')) }}" class="file-upload-preview" id="heroMediaPreview">
                                                @endif
                                            @else
                                                <img src="" class="file-upload-preview" id="heroMediaPreview" style="display:none;">
                                            @endif
                                            <input type="file" name="hero_media_file" id="heroMediaFile" accept="image/*">
                                            <span class="file-upload-label" id="heroMediaLabel"><span>Click to upload</span> or drag & drop<br>PNG, JPG (max 5MB)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn-save-settings">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Site Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Settings Panel -->
        <div class="settings-panel" id="section-map">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Map Settings</div>
                            <div class="section-subtitle">Configure Google Maps integration for property locations</div>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.map') }}" method="POST">
                        @csrf
                        <div class="settings-form-grid">
                            <div class="field-group">
                                <label class="field-label">Google Map Key</label>
                                <input type="text" name="map_key" class="field-input"
                                       value="{{ $settings->get('map_key') }}"
                                       placeholder="AIzaSy...">
                                <div class="field-hint">Used for location autocomplete and displaying maps on properties</div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Map Highlight Circle Radius (meters)</label>
                                <input type="number" name="map_radius" class="field-input"
                                       value="{{ $settings->get('map_radius', 400) }}"
                                       placeholder="400" min="10" max="50000">
                                <div class="field-hint">Radius of the neighborhood safety highlight circle drawn on the property details map. Default is 400 meters.</div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save-settings">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Map Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manage Fee Panel -->
        <div class="settings-panel" id="section-fees">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Manage Fee</div>
                            <div class="section-subtitle">Configure application-wide service fees and charges</div>
                        </div>
                    </div>

                    <form id="feesForm" action="{{ route('admin.settings.fees') }}" method="POST" novalidate>
                        @csrf
                        <div class="settings-form-grid">
                            <div class="field-group">
                                <label class="field-label">Service Fee (%)</label>
                                <div style="position: relative; display: flex; align-items: center; width: 100%;">
                                    <span style="position: absolute; left: 14px; font-weight: 700; color: var(--text-muted); font-size: 14px;">%</span>
                                    <input type="number" name="service_fee" class="field-input" style="padding-left: 36px;"
                                           value="{{ $settings->get('service_fee', '0') }}"
                                           placeholder="0.00" min="0" max="100" step="0.01">
                                </div>
                                <div class="field-hint">Service fee percentage charged on property nightly base price</div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save-settings">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Fees Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Settings Panel -->
        <div class="settings-panel" id="section-email">
            <div class="card">
                <div class="card-body">

                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Email Configuration</div>
                            <div class="section-subtitle">Configure SMTP email settings for the application</div>
                        </div>

                        <!-- Toggle -->
                        <div class="toggle-wrap">
                            <span class="toggle-label">Email Service</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="mailToggle"
                                    {{ $settings->get('mail_enabled') === '1' ? 'checked' : '' }}>
                                <div class="toggle-track"></div>
                            </label>
                            <span class="toggle-status {{ $settings->get('mail_enabled') === '1' ? 'on' : 'off' }}"
                                  id="toggleStatus">
                                {{ $settings->get('mail_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Use a Gmail account with an <strong>App Password</strong> (not your regular password). Enable 2FA on your Google account, then generate an App Password from <strong>Google Account → Security → App Passwords</strong>.</span>
                    </div>

                    <form id="emailForm" action="{{ route('admin.settings.email') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="settings-form-grid" id="emailFormFields"
                             style="{{ $settings->get('mail_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">

                            <div class="field-group">
                                <label class="field-label">Gmail Address (Username)</label>
                                <input type="email" id="f_username" name="mail_username"
                                       class="field-input"
                                       value="{{ $settings->get('mail_username') }}"
                                       placeholder="yourapp@gmail.com">
                                <div class="field-err" id="e_username"></div>
                                <div class="field-hint">The Gmail address used to send emails</div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">App Password</label>
                                <input type="password" id="f_password" name="mail_password"
                                       class="field-input"
                                       value="{{ $settings->get('mail_password') }}"
                                       placeholder="xxxx xxxx xxxx xxxx">
                                <div class="field-err" id="e_password"></div>
                                <div class="field-hint">16-character Google App Password</div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">From Email Address</label>
                                <input type="email" id="f_from" name="mail_from"
                                       class="field-input"
                                       value="{{ $settings->get('mail_from') }}"
                                       placeholder="noreply@yourapp.com">
                                <div class="field-err" id="e_from"></div>
                                <div class="field-hint">Email address shown to recipients</div>
                            </div>

                            <div class="field-group">
                                <label class="field-label">From Name</label>
                                <input type="text" id="f_from_name" name="mail_from_name"
                                       class="field-input"
                                       value="{{ $settings->get('mail_from_name', 'Hotel Host') }}"
                                       placeholder="Hotel Host">
                                <div class="field-err" id="e_from_name"></div>
                                <div class="field-hint">Sender name shown to recipients</div>
                            </div>

                            <div class="field-group full">
                                <label class="field-label">Email Site Logo</label>
                                <div class="file-upload-wrap">
                                    @if($settings->get('mail_email_logo'))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->get('mail_email_logo')) }}" class="file-upload-preview" id="emailLogoPreview">
                                    @else
                                        <img src="" class="file-upload-preview" id="emailLogoPreview" style="display:none;">
                                    @endif
                                    <input type="file" name="mail_email_logo" accept="image/*" onchange="previewFile(this,'emailLogoPreview')">
                                    <span class="file-upload-label"><span>Click to upload</span> or drag & drop<br>PNG, JPG (max 2MB)</span>
                                </div>
                                <div class="field-hint">Logo shown in email headers sent to users. Recommended: 200×60px</div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save-settings" id="saveBtn">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Email Settings
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- reCAPTCHA Panel -->
        <div class="settings-panel" id="section-recaptcha">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Google reCAPTCHA</div>
                            <div class="section-subtitle">Protect forms from bots using Google reCAPTCHA v2</div>
                        </div>
                        <div class="toggle-wrap">
                            <span class="toggle-label">reCAPTCHA</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="recaptchaToggle"
                                    {{ $settings->get('recaptcha_enabled') === '1' ? 'checked' : '' }}>
                                <div class="toggle-track"></div>
                            </label>
                            <span class="toggle-status {{ $settings->get('recaptcha_enabled') === '1' ? 'on' : 'off' }}" id="recaptchaStatus">
                                {{ $settings->get('recaptcha_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Get your keys from <strong>Google reCAPTCHA Admin Console</strong>. Select <strong>reCAPTCHA v2 "I'm not a robot"</strong>. Used on: Forgot Password page &amp; Profile password reset.</span>
                    </div>

                    <form action="{{ route('admin.settings.recaptcha') }}" method="POST" novalidate id="recaptchaForm">
                        @csrf
                        <div class="settings-form-grid" id="recaptchaFormFields" style="margin-top:24px;{{ $settings->get('recaptcha_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                            <div class="field-group">
                                <label class="field-label">Site Key</label>
                                <input type="text" name="recaptcha_site_key" class="field-input"
                                       value="{{ $settings->get('recaptcha_site_key') }}"
                                       placeholder="6Lc...">
                                <div class="field-hint">Public key used in the frontend widget</div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Secret Key</label>
                                <input type="text" name="recaptcha_secret_key" class="field-input"
                                       value="{{ $settings->get('recaptcha_secret_key') }}"
                                       placeholder="6Lc...">
                                <div class="field-hint">Private key used for server-side verification</div>
                            </div>
                        </div>
                        <button type="submit" class="btn-save-settings" id="recaptchaSaveBtn"
                                {{ $settings->get('recaptcha_enabled') !== '1' ? 'disabled' : '' }}>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save reCAPTCHA Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Twilio Panel -->
        <div class="settings-panel" id="section-twilio">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Twilio SMS Verification</div>
                            <div class="section-subtitle">Phone number OTP verification for signup and profile</div>
                        </div>
                        <div class="toggle-wrap">
                            <span class="toggle-label">Twilio SMS</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="twilioToggle"
                                    {{ $settings->get('twilio_enabled') === '1' ? 'checked' : '' }}>
                                <div class="toggle-track"></div>
                            </label>
                            <span class="toggle-status {{ $settings->get('twilio_enabled') === '1' ? 'on' : 'off' }}" id="twilioStatus">
                                {{ $settings->get('twilio_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Get your credentials from <strong>Twilio Console</strong>. Create a <strong>Verify Service</strong> and copy the Service SID. Used for: Phone verification on signup &amp; profile page.</span>
                    </div>

                    <form action="{{ route('admin.settings.twilio') }}" method="POST" novalidate>
                        @csrf
                        <div class="settings-form-grid" id="twilioFormFields" style="margin-top:24px;{{ $settings->get('twilio_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                            <div class="field-group full">
                                <label class="field-label">Twilio Account SID</label>
                                <input type="text" name="twilio_sid" class="field-input"
                                       value="{{ $settings->get('twilio_sid') }}"
                                       placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <div class="field-hint">Found in your Twilio Console dashboard</div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Twilio Auth Token</label>
                                <input type="password" name="twilio_token" class="field-input"
                                       value="{{ $settings->get('twilio_token') }}"
                                       placeholder="Your Auth Token">
                                <div class="field-hint">Keep this secret — never share publicly</div>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Twilio Verify Service SID</label>
                                <input type="text" name="twilio_service_sid" class="field-input"
                                       value="{{ $settings->get('twilio_service_sid') }}"
                                       placeholder="VAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <div class="field-hint">From Twilio Console → Verify → Services</div>
                            </div>
                        </div>
                        <button type="submit" class="btn-save-settings" id="twilioSaveBtn"
                                {{ $settings->get('twilio_enabled') !== '1' ? 'disabled' : '' }}>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Twilio Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Social Login Panel -->
        <div class="settings-panel" id="section-social">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Social Login</div>
                            <div class="section-subtitle">Allow users to sign in with Google, Facebook, or Apple</div>
                        </div>
                    </div>

                    <div class="info-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Enable each provider individually. When enabled, the login button appears on the frontend. Credentials are obtained from each provider's developer console.</span>
                    </div>

                    <form action="{{ route('admin.settings.social') }}" method="POST" novalidate id="socialForm">
                        @csrf

                        {{-- Google --}}
                        <div style="margin-top:28px;">
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Google</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Google Login</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="googleToggle" {{ $settings->get('google_login_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('google_login_enabled') === '1' ? 'on' : 'off' }}" id="googleStatus">
                                        {{ $settings->get('google_login_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            <div class="settings-form-grid" id="googleFields" style="margin-top:0;{{ $settings->get('google_login_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group">
                                    <label class="field-label">Client ID</label>
                                    <input type="text" name="google_client_id" class="field-input" value="{{ $settings->get('google_client_id') }}" placeholder="xxxx.apps.googleusercontent.com">
                                    <div class="field-hint">From Google Cloud Console → Credentials</div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Client Secret</label>
                                    <input type="password" name="google_client_secret" class="field-input" value="{{ $settings->get('google_client_secret') }}" placeholder="GOCSPX-...">
                                    <div class="field-hint">Keep this secret</div>
                                </div>
                            </div>
                        </div>

                        <hr style="border:none;border-top:1px solid var(--border);margin:24px 0;">

                        {{-- Facebook --}}
                        <div>
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Facebook</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Facebook Login</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="facebookToggle" {{ $settings->get('facebook_login_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('facebook_login_enabled') === '1' ? 'on' : 'off' }}" id="facebookStatus">
                                        {{ $settings->get('facebook_login_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            <div class="settings-form-grid" id="facebookFields" style="margin-top:0;{{ $settings->get('facebook_login_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group">
                                    <label class="field-label">App ID</label>
                                    <input type="text" name="facebook_client_id" class="field-input" value="{{ $settings->get('facebook_client_id') }}" placeholder="1234567890">
                                    <div class="field-hint">From Meta for Developers → Your App</div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">App Secret</label>
                                    <input type="password" name="facebook_client_secret" class="field-input" value="{{ $settings->get('facebook_client_secret') }}" placeholder="App Secret">
                                    <div class="field-hint">Keep this secret</div>
                                </div>
                            </div>
                        </div>

                        <hr style="border:none;border-top:1px solid var(--border);margin:24px 0;">

                        {{-- Apple --}}
                        <div>
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701" fill="currentColor"/></svg>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Apple</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Apple Login</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="appleToggle" {{ $settings->get('apple_login_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('apple_login_enabled') === '1' ? 'on' : 'off' }}" id="appleStatus">
                                        {{ $settings->get('apple_login_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            <div class="settings-form-grid" id="appleFields" style="margin-top:0;{{ $settings->get('apple_login_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group">
                                    <label class="field-label">Service ID (Client ID)</label>
                                    <input type="text" name="apple_client_id" class="field-input" value="{{ $settings->get('apple_client_id') }}" placeholder="com.yourapp.service">
                                    <div class="field-hint">From Apple Developer → Certificates → Service IDs</div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Private Key / Client Secret</label>
                                    <input type="password" name="apple_client_secret" class="field-input" value="{{ $settings->get('apple_client_secret') }}" placeholder="Generated JWT or .p8 key content">
                                    <div class="field-hint">Generated JWT signed with your .p8 key</div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save-settings" style="margin-top:28px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Social Login Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment API Panel -->
        <div class="settings-panel" id="section-payment">
            <div class="card">
                <div class="card-body">
                    <div class="section-header">
                        <div class="section-title-wrap">
                            <div class="section-title">Payment APIs</div>
                            <div class="section-subtitle">Configure PayPal payment gateway credentials</div>
                        </div>
                    </div>

                    <div class="info-box">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Enable PayPal to accept online payments. Obtain your keys from the <strong>PayPal Developer Dashboard</strong>. Ensure to set the Mode correctly for testing vs production.</span>
                    </div>

                    <form action="{{ route('admin.settings.payment') }}" method="POST" novalidate id="paymentForm">
                        @csrf

                        {{-- PayPal --}}
                        <div style="margin-top:28px;">
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <i class="fab fa-paypal" style="font-size:20px; color:#003087;"></i>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">PayPal API</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Enable PayPal</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="paypalToggle" {{ $settings->get('paypal_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('paypal_enabled') === '1' ? 'on' : 'off' }}" id="paypalStatus">
                                        {{ $settings->get('paypal_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="settings-form-grid" id="paypalFields" style="margin-top:0;{{ $settings->get('paypal_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group full">
                                    <label class="field-label">Environment Mode</label>
                                    <select name="paypal_mode" class="field-input">
                                        <option value="sandbox" {{ $settings->get('paypal_mode') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="live" {{ $settings->get('paypal_mode') === 'live' ? 'selected' : '' }}>Live (Production)</option>
                                    </select>
                                    <div class="field-hint">Select sandbox for testing or live for real payments.</div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Client ID</label>
                                    <input type="text" name="paypal_client_id" class="field-input" value="{{ $settings->get('paypal_client_id') }}" placeholder="PayPal Client ID">
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Secret Key</label>
                                    <input type="password" name="paypal_secret" class="field-input" value="{{ $settings->get('paypal_secret') }}" placeholder="PayPal Secret">
                                </div>
                            </div>
                        </div>

                        {{-- Stripe --}}
                        <div style="margin-top:40px;">
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <i class="fab fa-stripe" style="font-size:24px; color:#635bff;"></i>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Stripe API</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Enable Stripe</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="stripeToggle" {{ $settings->get('stripe_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('stripe_enabled') === '1' ? 'on' : 'off' }}" id="stripeStatus">
                                        {{ $settings->get('stripe_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="settings-form-grid" id="stripeFields" style="margin-top:0;{{ $settings->get('stripe_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group">
                                    <label class="field-label">Publishable Key</label>
                                    <input type="text" name="stripe_key" class="field-input" value="{{ $settings->get('stripe_key') }}" placeholder="pk_test_...">
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Secret Key</label>
                                    <input type="password" name="stripe_secret" class="field-input" value="{{ $settings->get('stripe_secret') }}" placeholder="sk_test_...">
                                </div>
                            </div>
                        </div>

                        {{-- Easebuzz --}}
                        <div style="margin-top:40px;">
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <i class="fas fa-bolt" style="font-size:24px; color:#ff9900;"></i>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Easebuzz API</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Enable Easebuzz</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="easebuzzToggle" {{ $settings->get('easebuzz_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('easebuzz_enabled') === '1' ? 'on' : 'off' }}" id="easebuzzStatus">
                                        {{ $settings->get('easebuzz_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="settings-form-grid" id="easebuzzFields" style="margin-top:0;{{ $settings->get('easebuzz_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group full">
                                    <label class="field-label">Environment Mode</label>
                                    <select name="easebuzz_env" class="field-input">
                                        <option value="sandbox" {{ $settings->get('easebuzz_env') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="live" {{ $settings->get('easebuzz_env') === 'live' ? 'selected' : '' }}>Live (Production)</option>
                                    </select>
                                    <div class="field-hint">Select sandbox for testing or live for real payments.</div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Merchant Key</label>
                                    <input type="text" name="easebuzz_merchant_key" class="field-input" value="{{ $settings->get('easebuzz_merchant_key') }}" placeholder="Enter Merchant Key">
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Salt</label>
                                    <input type="password" name="easebuzz_salt" class="field-input" value="{{ $settings->get('easebuzz_salt') }}" placeholder="Enter Salt">
                                </div>
                            </div>
                        </div>

                        {{-- Razorpay --}}
                        <div style="margin-top:40px;">
                            <div class="section-header" style="margin-bottom:12px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <i class="fas fa-credit-card" style="font-size:24px; color:#3395FF;"></i>
                                    <span style="font-size:15px;font-weight:700;color:var(--text);">Razorpay API</span>
                                </div>
                                <div class="toggle-wrap">
                                    <span class="toggle-label">Enable Razorpay</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="razorpayToggle" {{ $settings->get('razorpay_enabled') === '1' ? 'checked' : '' }}>
                                        <div class="toggle-track"></div>
                                    </label>
                                    <span class="toggle-status {{ $settings->get('razorpay_enabled') === '1' ? 'on' : 'off' }}" id="razorpayStatus">
                                        {{ $settings->get('razorpay_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="settings-form-grid" id="razorpayFields" style="margin-top:0;{{ $settings->get('razorpay_enabled') !== '1' ? 'opacity:0.45;pointer-events:none;' : '' }}">
                                <div class="field-group">
                                    <label class="field-label">Key ID</label>
                                    <input type="text" name="razorpay_key" class="field-input" value="{{ $settings->get('razorpay_key') }}" placeholder="rzp_test_...">
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Key Secret</label>
                                    <input type="password" name="razorpay_secret" class="field-input" value="{{ $settings->get('razorpay_secret') }}" placeholder="Enter Key Secret">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-save-settings" style="margin-top: 24px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Save Payment Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function switchSection(name, btn) {
        document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
        document.getElementById('section-' + name).classList.add('active');
        btn.classList.add('active');
        // Persist active section in URL hash so page reload stays on same section
        history.replaceState(null, '', '#' + name);
    }

    // Restore section from hash on load
    (function() {
        const hash = location.hash.replace('#', '');
        if (hash) {
            const panel = document.getElementById('section-' + hash);
            const btn   = document.querySelector('[onclick*="switchSection(\'' + hash + '\'"]');
            if (panel && btn) {
                document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
                document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
                panel.classList.add('active');
                btn.classList.add('active');
            }
        }
    })();

    // Append hash to each form action on submit so redirect lands back on same section
    document.querySelectorAll('form[action]').forEach(function(form) {
        form.addEventListener('submit', function() {
            const hash = location.hash;
            if (hash) this.action = this.action.split('#')[0] + hash;
        });
    });

    function previewFile(input, previewId) {
        let preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Hero Media File Type Toggle
    const heroMediaType = document.getElementById('heroMediaType');
    const heroMediaFile = document.getElementById('heroMediaFile');
    const heroMediaLabel = document.getElementById('heroMediaLabel');

    function updateHeroMediaAccept() {
        if(!heroMediaType || !heroMediaFile) return;
        
        let heroMediaPreview = document.getElementById('heroMediaPreview');
        const isVideo = heroMediaType.value === 'video';
        heroMediaFile.setAttribute('accept', isVideo ? 'video/mp4,video/webm,video/quicktime' : 'image/*');
        if(heroMediaLabel) {
            heroMediaLabel.innerHTML = `<span>Click to upload</span> or drag & drop<br>${isVideo ? 'MP4, WEBM, MOV (max 20MB)' : 'PNG, JPG, WEBP (max 5MB)'}`;
        }
        
        // If it's empty or we're switching types, convert the preview node type so it works correctly on upload.
        if (heroMediaPreview) {
             const newPreview = document.createElement(isVideo ? 'video' : 'img');
             newPreview.id = 'heroMediaPreview';
             newPreview.className = 'file-upload-preview';
             newPreview.style.display = heroMediaPreview.style.display;
             newPreview.src = heroMediaPreview.src;
             if(isVideo) {
                 newPreview.autoplay = true;
                 newPreview.loop = true;
                 newPreview.muted = true;
                 newPreview.style.objectFit = 'cover';
             }
             heroMediaPreview.parentNode.replaceChild(newPreview, heroMediaPreview);
        }
    }

    if(heroMediaType) {
        heroMediaType.addEventListener('change', updateHeroMediaAccept);
        updateHeroMediaAccept(); // Initialize
    }

    function applyToggleState(fields, saveBtn, on) {
        fields.style.opacity       = on ? '1'    : '0.45';
        fields.style.pointerEvents = on ? 'auto' : 'none';
        saveBtn.disabled = !on;
    }

    // Site form validation
    document.getElementById('siteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('f_site_name').value.trim();
        const errEl = document.getElementById('e_site_name');
        if (!name) {
            errEl.textContent = 'Site name is required.';
            errEl.style.display = 'block';
            document.getElementById('f_site_name').classList.add('err');
            return;
        }
        errEl.style.display = 'none';
        document.getElementById('f_site_name').classList.remove('err');
        this.submit();
    });

    // Email toggle
    const mailToggle   = document.getElementById('mailToggle');
    const toggleStatus = document.getElementById('toggleStatus');
    const formFields   = document.getElementById('emailFormFields');
    const saveBtn      = document.getElementById('saveBtn');

    applyToggleState(formFields, saveBtn, mailToggle.checked);

    mailToggle.addEventListener('change', function() {
        fetch('{{ route("admin.settings.email.toggle") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const on = data.enabled;
            toggleStatus.textContent = on ? 'Enabled' : 'Disabled';
            toggleStatus.className   = 'toggle-status ' + (on ? 'on' : 'off');
            applyToggleState(formFields, saveBtn, on);
        });
    });

    // reCAPTCHA toggle
    const recaptchaToggle    = document.getElementById('recaptchaToggle');
    const recaptchaFields    = document.getElementById('recaptchaFormFields');
    const recaptchaSaveBtn   = document.getElementById('recaptchaSaveBtn');

    applyToggleState(recaptchaFields, recaptchaSaveBtn, recaptchaToggle.checked);

    recaptchaToggle.addEventListener('change', function() {
        fetch('{{ route("admin.settings.recaptcha.toggle") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const st = document.getElementById('recaptchaStatus');
            st.textContent = data.enabled ? 'Enabled' : 'Disabled';
            st.className   = 'toggle-status ' + (data.enabled ? 'on' : 'off');
            applyToggleState(recaptchaFields, recaptchaSaveBtn, data.enabled);
        });
    });

    // Twilio toggle
    const twilioToggle  = document.getElementById('twilioToggle');
    const twilioFields  = document.getElementById('twilioFormFields');
    const twilioSaveBtn = document.getElementById('twilioSaveBtn');

    applyToggleState(twilioFields, twilioSaveBtn, twilioToggle.checked);

    twilioToggle.addEventListener('change', function() {
        fetch('{{ route("admin.settings.twilio.toggle") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const st = document.getElementById('twilioStatus');
            st.textContent = data.enabled ? 'Enabled' : 'Disabled';
            st.className   = 'toggle-status ' + (data.enabled ? 'on' : 'off');
            applyToggleState(twilioFields, twilioSaveBtn, data.enabled);
        });
    });

    // Email JS Validation
    function se(id, msg) {
        document.getElementById('e_' + id).textContent = msg;
        document.getElementById('e_' + id).style.display = 'block';
        document.getElementById('f_' + id).classList.add('err');
    }
    function ce(id) {
        document.getElementById('e_' + id).style.display = 'none';
        document.getElementById('f_' + id).classList.remove('err');
    }

    document.getElementById('emailForm').addEventListener('submit', function(e) {
        if (!mailToggle.checked) { e.preventDefault(); return; }
        e.preventDefault();
        let valid = true;
        const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const username = document.getElementById('f_username').value.trim();
        if (!username) { se('username', 'Gmail address is required.'); valid = false; }
        else if (!emailReg.test(username)) { se('username', 'Enter a valid Gmail address.'); valid = false; }
        else ce('username');

        const password = document.getElementById('f_password').value;
        if (!password || password.length < 4) { se('password', 'App password is required.'); valid = false; }
        else ce('password');

        const from = document.getElementById('f_from').value.trim();
        if (!from) { se('from', 'From email is required.'); valid = false; }
        else if (!emailReg.test(from)) { se('from', 'Enter a valid email address.'); valid = false; }
        else ce('from');

        const fromName = document.getElementById('f_from_name').value.trim();
        if (!fromName) { se('from_name', 'From name is required.'); valid = false; }
        else ce('from_name');

        if (valid) this.submit();
    });
    // Social login toggles
    ['google', 'facebook', 'apple'].forEach(function(provider) {
        const toggle  = document.getElementById(provider + 'Toggle');
        const fields  = document.getElementById(provider + 'Fields');
        const status  = document.getElementById(provider + 'Status');
        if (!toggle) return;

        toggle.addEventListener('change', function() {
            fetch('{{ url("admin/settings/social") }}/' + provider + '/toggle', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                status.textContent = data.enabled ? 'Enabled' : 'Disabled';
                status.className   = 'toggle-status ' + (data.enabled ? 'on' : 'off');
                fields.style.opacity       = data.enabled ? '1'    : '0.45';
                fields.style.pointerEvents = data.enabled ? 'auto' : 'none';
            });
        });
    });

    // Payment API toggles
    const paymentForm = document.getElementById('paymentForm');
    const paymentSaveBtn = paymentForm ? paymentForm.querySelector('.btn-save-settings') : null;

    ['paypal', 'stripe', 'easebuzz', 'razorpay'].forEach(function(provider) {
        const toggle = document.getElementById(provider + 'Toggle');
        const fields = document.getElementById(provider + 'Fields');
        const status = document.getElementById(provider + 'Status');
        
        if (!toggle || !fields) return;

        // Function to apply field state, but we won't disable the global save button here 
        // since one might be disabled and the other enabled.
        function applyProviderToggle(on) {
            fields.style.opacity = on ? '1' : '0.45';
            fields.style.pointerEvents = on ? 'auto' : 'none';
        }

        applyProviderToggle(toggle.checked);

        toggle.addEventListener('change', function() {
            fetch('{{ url("admin/settings/payment") }}/' + provider + '/toggle', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                status.textContent = data.enabled ? 'Enabled' : 'Disabled';
                status.className = 'toggle-status ' + (data.enabled ? 'on' : 'off');
                applyProviderToggle(data.enabled);
            });
        });
    });
</script>
@endsection
