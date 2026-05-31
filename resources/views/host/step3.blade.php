@extends('host.layout')

@section('host-content')
<h1 class="host-title">Where's your place located?</h1>
<p class="host-subtitle">Your exact address will only be shared with guests after they've booked.</p>

<div class="row g-4">
    <div class="col-12">
        <div class="form-floating-airbnb" id="wrap_locationName">
            <input type="text" class="form-control-airbnb" name="location_name" id="locationInput"
                   value="{{ $room->location->location_name ?? '' }}"
                   placeholder=" "
                   autocomplete="off">
            <label for="locationInput">Full Address / Location Name</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_city">
            <input type="text" class="form-control-airbnb" name="city" id="cityInput"
                   value="{{ $room->location->city ?? '' }}" placeholder=" "
                   oninput="autoSave('city', this.value, 'room_locations', 'cityInput')">
            <label for="cityInput">City</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_state">
            <input type="text" class="form-control-airbnb" name="state" id="stateInput"
                   value="{{ $room->location->state ?? '' }}" placeholder=" "
                   oninput="autoSave('state', this.value, 'room_locations', 'stateInput')">
            <label for="stateInput">State / Province</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_country">
            <input type="text" class="form-control-airbnb" name="country" id="countryInput"
                   value="{{ $room->location->country ?? '' }}" placeholder=" "
                   oninput="autoSave('country', this.value, 'room_locations', 'countryInput')">
            <label for="countryInput">Country</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating-airbnb" id="wrap_zip">
            <input type="text" class="form-control-airbnb" name="zip_code" id="zipInput"
                   value="{{ $room->location->zip_code ?? '' }}" placeholder=" "
                   oninput="autoSave('zip_code', this.value, 'room_locations', 'zipInput')">
            <label for="zipInput">Zip Code</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <h5 class="fw-bold mb-3">Adjust Map Pin</h5>
    <div id="googleMap" style="width:100%;height:280px;background:#eee;border-radius:24px;border:1px solid var(--border);"></div>
    <p class="text-muted small mt-2">Drag the pin to set the exact location if needed.</p>
</div>

<div class="host-actions">
    <a href="{{ route('host.step', ['room' => $room->id, 'step' => 2]) }}" class="btn-prev">Back</a>
    <a href="{{ route('host.step', ['room' => $room->id, 'step' => 4]) }}" class="btn-next">
        <span class="btn-text">Save & Next</span>
        <div class="btn-spinner"></div>
    </a>
</div>
@endsection

@section('scripts')
<script>
    const ROOM_ID   = {{ $room->id }};
    const SAVE_MULTI_URL = '/host/' + ROOM_ID + '/save-multiple';

    let map, marker, autocomplete;
    const initialLat = parseFloat("{{ $room->location->latitude ?? 0 }}") || 20.5937;
    const initialLng = parseFloat("{{ $room->location->longitude ?? 0 }}") || 78.9629;

    // Save lat + lng atomically in one request
    function saveLatLng(lat, lng) {
        showToast('saving', 'Saving location...');
        fetch(SAVE_MULTI_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                step: 3,
                fields: [
                    { field: 'latitude',  value: lat, table: 'room_locations' },
                    { field: 'longitude', value: lng, table: 'room_locations' }
                ]
            })
        })
        .then(r => r.json())
        .then(d => { 
            if (d.success) {
                showToast('success', 'Location saved');
                if (d.step_valid !== null) updateStepperStatus(d.step, d.step_valid);
            } else throw new Error(); 
        })
        .catch(() => showToast('error', 'Error saving location'));
    }

    // Save all address fields at once after autocomplete
    function saveAddressFields(locName, city, state, country, zip, lat, lng) {
        showToast('saving', 'Saving address...');
        fetch(SAVE_MULTI_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                step: 3,
                fields: [
                    { field: 'location_name', value: locName,  table: 'room_locations' },
                    { field: 'city',          value: city,     table: 'room_locations' },
                    { field: 'state',         value: state,    table: 'room_locations' },
                    { field: 'country',       value: country,  table: 'room_locations' },
                    { field: 'zip_code',      value: zip,      table: 'room_locations' },
                    { field: 'latitude',      value: lat,      table: 'room_locations' },
                    { field: 'longitude',     value: lng,      table: 'room_locations' },
                ]
            })
        })
        .then(r => r.json())
        .then(d => { 
            if (d.success) {
                showToast('success', 'Address saved');
                if (d.step_valid !== null) updateStepperStatus(d.step, d.step_valid);
            } else throw new Error(); 
        })
        .catch(() => showToast('error', 'Error saving address'));
    }

    function initMap() {
        const locationInput = document.getElementById('locationInput');
        autocomplete = new google.maps.places.Autocomplete(locationInput, {
            fields: ['address_components', 'geometry', 'name', 'formatted_address']
        });
        autocomplete.addListener('place_changed', onPlaceChanged);

        map = new google.maps.Map(document.getElementById('googleMap'), {
            center: { lat: initialLat, lng: initialLng },
            zoom: initialLat !== 20.5937 ? 15 : 5,
            styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }],
            mapTypeControl: false,
            streetViewControl: false
        });

        marker = new google.maps.Marker({
            position: { lat: initialLat, lng: initialLng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        marker.addListener('dragend', onMarkerDragEnd);
    }

    function onPlaceChanged() {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        map.setCenter(place.geometry.location);
        map.setZoom(16);
        marker.setPosition(place.geometry.location);

        let city = '', state = '', country = '', zip = '';
        (place.address_components || []).forEach(comp => {
            const types = comp.types;
            if (types.includes('locality'))                      city    = comp.long_name;
            if (types.includes('administrative_area_level_1'))   state   = comp.long_name;
            if (types.includes('country'))                       country = comp.long_name;
            if (types.includes('postal_code'))                   zip     = comp.long_name;
        });

        document.getElementById('cityInput').value    = city;
        document.getElementById('stateInput').value   = state;
        document.getElementById('countryInput').value = country;
        document.getElementById('zipInput').value     = zip;

        const locName = place.formatted_address || locationInput.value;
        document.getElementById('locationInput').value = locName;

        saveAddressFields(locName, city, state, country, zip, lat, lng);
    }

    function onMarkerDragEnd() {
        const pos = marker.getPosition();
        const lat = pos.lat();
        const lng = pos.lng();
        map.panTo(pos);

        // Save lat+lng atomically
        saveLatLng(lat, lng);

        // Reverse geocode to update address fields
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: pos }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const place = results[0];
                let city = '', state = '', country = '', zip = '';
                (place.address_components || []).forEach(comp => {
                    const types = comp.types;
                    if (types.includes('locality'))                    city    = comp.long_name;
                    if (types.includes('administrative_area_level_1')) state   = comp.long_name;
                    if (types.includes('country'))                     country = comp.long_name;
                    if (types.includes('postal_code'))                 zip     = comp.long_name;
                });
                const locName = place.formatted_address;
                document.getElementById('locationInput').value = locName;
                document.getElementById('cityInput').value     = city;
                document.getElementById('stateInput').value    = state;
                document.getElementById('countryInput').value  = country;
                document.getElementById('zipInput').value      = zip;

                saveAddressFields(locName, city, state, country, zip, lat, lng);
            }
        });
    }

    if (typeof google !== 'undefined') initMap();
</script>
@endsection
