<?= $this->extend('pendaftar/registration/wizard_layout') ?>

<?php
$settingModel = new \App\Models\SettingModel();
$schoolLat = (float)($settingModel->getValue('maps_lat') ?: -6.2150);
$schoolLng = (float)($settingModel->getValue('maps_lng') ?: 106.8500);
$schoolName = $settingModel->getValue('school_name', 'Smart SPMB Pro');
?>

<?= $this->section('additional_css') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .select2-container--bootstrap-5 {
        z-index: 1000 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('step_content') ?>
<div class="p-4">
    <h4 class="wizard-step-title"><i data-lucide="map"></i> Langkah 2: Alamat & Kontak</h4>
    
    <form id="stepForm2" method="POST">
        <?= csrf_field() ?>
        
        <div class="row">
            <!-- Alamat Jalan -->
            <div class="col-md-8 form-group">
                <label for="street_address" class="form-label required-field">Alamat Jalan</label>
                <input type="text" class="form-control" id="street_address" name="street_address" value="<?= esc($stepData['street_address'] ?? '') ?>" placeholder="Nama jalan, perumahan, nomor rumah" required>
            </div>
            
            <!-- RT -->
            <div class="col-md-2 form-group">
                <label for="rt" class="form-label">RT</label>
                <input type="number" class="form-control" id="rt" name="rt" value="<?= esc($stepData['rt'] ?? '') ?>" placeholder="000" min="1" max="999">
            </div>

            <!-- RW -->
            <div class="col-md-2 form-group">
                <label for="rw" class="form-label">RW</label>
                <input type="number" class="form-control" id="rw" name="rw" value="<?= esc($stepData['rw'] ?? '') ?>" placeholder="000" min="1" max="999">
            </div>
        </div>

        <div class="row">
            <!-- Dusun/Kampung -->
            <div class="col-md-4 form-group">
                <label for="hamlet" class="form-label">Dusun/Dukuh/Kampung</label>
                <input type="text" class="form-control" id="hamlet" name="hamlet" value="<?= esc($stepData['hamlet'] ?? '') ?>" placeholder="Nama dusun/kampung">
            </div>

            <!-- Provinsi -->
            <div class="col-md-4 form-group">
                <label for="province" class="form-label required-field">Provinsi</label>
                <select class="form-select" id="province" name="province" required>
                    <option value="">-- Pilih Provinsi --</option>
                    <?php if (!empty($stepData['province'])): ?>
                        <option value="<?= esc($stepData['province']) ?>" selected><?= esc($stepData['province']) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Kabupaten/Kota -->
            <div class="col-md-4 form-group">
                <label for="district" class="form-label required-field">Kabupaten/Kota</label>
                <select class="form-select" id="district" name="district" required>
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    <?php if (!empty($stepData['district'])): ?>
                        <option value="<?= esc($stepData['district']) ?>" selected><?= esc($stepData['district']) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Kecamatan -->
            <div class="col-md-4 form-group">
                <label for="subdistrict" class="form-label required-field">Kecamatan</label>
                <select class="form-select" id="subdistrict" name="subdistrict" required>
                    <option value="">-- Pilih Kecamatan --</option>
                    <?php if (!empty($stepData['subdistrict'])): ?>
                        <option value="<?= esc($stepData['subdistrict']) ?>" selected><?= esc($stepData['subdistrict']) ?></option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Kelurahan/Desa -->
            <div class="col-md-4 form-group">
                <label for="village" class="form-label required-field">Kelurahan/Desa</label>
                <select class="form-select" id="village" name="village" required>
                    <option value="">-- Pilih Kelurahan/Desa --</option>
                    <?php if (!empty($stepData['village'])): ?>
                        <option value="<?= esc($stepData['village']) ?>" selected><?= esc($stepData['village']) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Kode Pos -->
            <div class="col-md-4 form-group">
                <label for="postal_code" class="form-label required-field">Kode Pos</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?= esc($stepData['postal_code'] ?? '') ?>" placeholder="5 digit kode pos" maxlength="5" required>
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <!-- Tempat Tinggal -->
            <div class="col-md-4 form-group">
                <label for="residence_type" class="form-label required-field">Tempat Tinggal</label>
                <select class="form-select" id="residence_type" name="residence_type" required>
                    <option value="">-- Pilih Jenis Tinggal --</option>
                    <?php foreach ($dapodikValues['jenis_tinggal'] as $jenis): ?>
                        <option value="<?= esc($jenis) ?>" <?= ($stepData['residence_type'] ?? '') === $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Jarak Rumah ke Sekolah -->
            <div class="col-md-4 form-group">
                <label for="distance_km" class="form-label required-field">Jarak ke Sekolah (KM)</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="distance_km" name="distance_km" value="<?= esc($stepData['distance_km'] ?? '0') ?>" placeholder="Contoh: 1.5" step="0.1" min="0" required>
                    <span class="input-group-text">KM</span>
                </div>
            </div>

            <!-- Transportasi ke Sekolah -->
            <div class="col-md-4 form-group">
                <label for="transport_mode" class="form-label required-field">Moda Transportasi</label>
                <select class="form-select" id="transport_mode" name="transport_mode" required>
                    <option value="">-- Pilih Moda Transportasi --</option>
                    <?php foreach ($dapodikValues['moda_transportasi'] as $moda): ?>
                        <option value="<?= esc($moda) ?>" <?= ($stepData['transport_mode'] ?? '') === $moda ? 'selected' : '' ?>><?= esc($moda) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estimasi Durasi Perjalanan -->
            <div class="col-md-4 form-group">
                <label for="travel_duration_minutes" class="form-label">Estimasi Waktu Tempuh</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="travel_duration_minutes" name="travel_duration_minutes" value="<?= esc($stepData['travel_duration_minutes'] ?? '') ?>" placeholder="Contoh: 30" min="0" max="999">
                    <span class="input-group-text">Menit</span>
                </div>
                <div class="help-text">Estimasi waktu tempuh dari rumah ke sekolah.</div>
            </div>
        </div>

        <div class="row">
            <!-- No. Telepon/WhatsApp -->
            <div class="col-md-6 form-group">
                <label for="phone_number" class="form-label required-field">No. Telepon / WhatsApp</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($stepData['phone_number'] ?? '') ?>" placeholder="Contoh: 081234567890" required>
                <div class="help-text">Nomor aktif untuk informasi pendaftaran.</div>
            </div>

            <!-- Email Aktif -->
            <div class="col-md-6 form-group">
                <label for="email" class="form-label required-field">Alamat Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc($stepData['email'] ?? '') ?>" placeholder="Contoh: nama@domain.com" required>
                <div class="help-text">Gunakan alamat email aktif.</div>
            </div>
        </div>

        <hr class="my-4">
        
        <h5 class="wizard-step-subtitle mb-3"><i data-lucide="map-pin"></i> Geolokasi & Koordinat Tempat Tinggal</h5>
        <div class="alert alert-info border-0 shadow-xs mb-3 d-flex align-items-start">
            <i data-lucide="info" class="me-2 mt-1 flex-shrink-0" style="width: 20px; height: 20px; color: var(--sp-info);"></i>
            <div>
                <strong class="text-dark">Lokasi Rumah Calon Siswa</strong>
                <p class="mb-0 small text-dark opacity-75">Tentukan titik koordinat rumah Anda pada peta di bawah ini. Titik koordinat digunakan untuk menghitung jarak ke sekolah pada jalur Seleksi secara akurat.</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-5 form-group">
                <label for="latitude" class="form-label">Lintang (Latitude)</label>
                <input type="text" class="form-control text-dark fw-bold" id="latitude" name="latitude" value="<?= esc($stepData['latitude'] ?? '') ?>" placeholder="Contoh: -6.2088" readonly>
            </div>
            <div class="col-md-5 form-group">
                <label for="longitude" class="form-label">Bujur (Longitude)</label>
                <input type="text" class="form-control text-dark fw-bold" id="longitude" name="longitude" value="<?= esc($stepData['longitude'] ?? '') ?>" placeholder="Contoh: 106.8456" readonly>
            </div>
            <div class="col-md-2 form-group d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" id="btn-get-gps" style="height: 38px;">
                    <i data-lucide="crosshair" class="me-1"></i> GPS
                </button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 form-group">
                <div id="map" class="rounded border shadow-xs" style="height: 350px; background: #e0e0e0; z-index: 1;"></div>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    $(document).ready(function() {
        // --- 1. Cascading Regional Dropdowns ---
        fetch('<?= base_url('api/wilayah/provinces') ?>')
            .then(res => res.json())
            .then(data => {
                const $prov = $('#province');
                const selectedVal = '<?= esc($stepData['province'] ?? '') ?>';
                $prov.empty().append('<option value="">-- Pilih Provinsi --</option>');
                data.forEach(item => {
                    const isSelected = item.name === selectedVal ? 'selected' : '';
                    $prov.append(`<option value="${item.id}" data-name="${item.name}" ${isSelected}>${item.name}</option>`);
                });
                
                if (selectedVal) {
                    const selectedId = $prov.find('option:selected').val();
                    if (selectedId) {
                        loadRegencies(selectedId, '<?= esc($stepData['district'] ?? '') ?>');
                    }
                }
            });

        $('#province').change(function() {
            const provId = $(this).val();
            const provName = $(this).find('option:selected').data('name') || '';
            $(this).find('option:selected').val(provName);

            $('#district').empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>');
            $('#subdistrict').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#village').empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');

            if (provId) {
                loadRegencies(provId);
            }
        });

        function loadRegencies(provId, selectedRegName = '') {
            fetch(`<?= base_url('api/wilayah/regencies') ?>?province_id=${provId}`)
                .then(res => res.json())
                .then(data => {
                    const $reg = $('#district');
                    $reg.empty().append('<option value="">-- Pilih Kabupaten/Kota --</option>');
                    data.forEach(item => {
                        const isSelected = item.name === selectedRegName ? 'selected' : '';
                        $reg.append(`<option value="${item.id}" data-name="${item.name}" ${isSelected}>${item.name}</option>`);
                    });
                    
                    if (selectedRegName) {
                        const selectedId = $reg.find('option:selected').val();
                        if (selectedId) {
                            loadDistricts(selectedId, '<?= esc($stepData['subdistrict'] ?? '') ?>');
                        }
                    }
                });
        }

        $('#district').change(function() {
            const regId = $(this).val();
            const regName = $(this).find('option:selected').data('name') || '';
            $(this).find('option:selected').val(regName);

            $('#subdistrict').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#village').empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');

            if (regId) {
                loadDistricts(regId);
            }
        });

        function loadDistricts(regId, selectedDistName = '') {
            fetch(`<?= base_url('api/wilayah/districts') ?>?regency_id=${regId}`)
                .then(res => res.json())
                .then(data => {
                    const $dist = $('#subdistrict');
                    $dist.empty().append('<option value="">-- Pilih Kecamatan --</option>');
                    data.forEach(item => {
                        const isSelected = item.name === selectedDistName ? 'selected' : '';
                        $dist.append(`<option value="${item.id}" data-name="${item.name}" ${isSelected}>${item.name}</option>`);
                    });

                    if (selectedDistName) {
                        const selectedId = $dist.find('option:selected').val();
                        if (selectedId) {
                            loadVillages(selectedId, '<?= esc($stepData['village'] ?? '') ?>');
                        }
                    }
                });
        }

        $('#subdistrict').change(function() {
            const distId = $(this).val();
            const distName = $(this).find('option:selected').data('name') || '';
            $(this).find('option:selected').val(distName);

            $('#village').empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');

            if (distId) {
                loadVillages(distId);
            }
        });

        function loadVillages(distId, selectedVilName = '') {
            fetch(`<?= base_url('api/wilayah/villages') ?>?district_id=${distId}`)
                .then(res => res.json())
                .then(data => {
                    const $vil = $('#village');
                    $vil.empty().append('<option value="">-- Pilih Kelurahan/Desa --</option>');
                    data.forEach(item => {
                        const isSelected = item.name === selectedVilName ? 'selected' : '';
                        $vil.append(`<option value="${item.id}" data-name="${item.name}" ${isSelected}>${item.name}</option>`);
                    });
                });
        }

        $('#village').change(function() {
            const vilName = $(this).find('option:selected').data('name') || '';
            $(this).find('option:selected').val(vilName);
        });

        // --- 2. Leaflet Map & Distance Zonasi Calculator ---
        const schoolLat = <?= $schoolLat ?>;
        const schoolLng = <?= $schoolLng ?>;
        let lat = parseFloat($('#latitude').val()) || schoolLat;
        let lng = parseFloat($('#longitude').val()) || schoolLng;

        const map = L.map('map').setView([lat, lng], 14);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.bindPopup("<b>Lokasi Rumah Anda</b><br>Seret marker ini ke lokasi rumah Anda.").openPopup();

        let schoolMarker = L.marker([schoolLat, schoolLng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(map);
        schoolMarker.bindPopup("<b><?= esc($schoolName) ?></b><br>Lokasi Sekolah").openPopup();

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // km
            const dLat = deg2rad(lat2-lat1);
            const dLon = deg2rad(lon2-lon1);
            const a =
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2)
                ;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const d = R * c;
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180);
        }

        function updateDurationEstimate() {
            const distance = parseFloat($('#distance_km').val()) || 0;
            const mode = $('#transport_mode').val() || '';
            
            if (distance <= 0) {
                $('#travel_duration_minutes').val('');
                return;
            }

            let minutesPerKm = 3; // Default fallback
            const lowerMode = mode.toLowerCase();
            
            if (lowerMode.includes('jalan kaki') || lowerMode.includes('jalan')) {
                minutesPerKm = 12;
            } else if (lowerMode.includes('sepeda') && !lowerMode.includes('motor')) {
                minutesPerKm = 4;
            } else if (lowerMode.includes('motor') || lowerMode.includes('ojek')) {
                minutesPerKm = 1.5;
            } else if (lowerMode.includes('mobil') || lowerMode.includes('pribadi')) {
                minutesPerKm = 2.0;
            } else if (lowerMode.includes('angkutan') || lowerMode.includes('umum') || lowerMode.includes('bus') || lowerMode.includes('pete-pete') || lowerMode.includes('angkot')) {
                minutesPerKm = 3.0;
            } else if (lowerMode.includes('jemputan') || lowerMode.includes('kereta') || lowerMode.includes('krl')) {
                minutesPerKm = 2.5;
            }

            let duration = Math.round(distance * minutesPerKm + 2);
            if (duration < 1) duration = 1;
            
            $('#travel_duration_minutes').val(duration);
        }

        function updateCoordinates(latVal, lngVal) {
            $('#latitude').val(latVal.toFixed(6));
            $('#longitude').val(lngVal.toFixed(6));
            
            const dist = calculateDistance(latVal, lngVal, schoolLat, schoolLng);
            $('#distance_km').val(dist.toFixed(2));
            updateDurationEstimate();
        }

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoordinates(e.latlng.lat, e.latlng.lng);
        });

        $('#distance_km, #transport_mode').on('input change', function() {
            updateDurationEstimate();
        });

        $('#btn-get-gps').click(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const gpsLat = position.coords.latitude;
                    const gpsLng = position.coords.longitude;
                    
                    marker.setLatLng([gpsLat, gpsLng]);
                    map.setView([gpsLat, gpsLng], 16);
                    updateCoordinates(gpsLat, gpsLng);
                }, function(err) {
                    Swal.fire('GPS Gagal', 'Tidak dapat mengambil lokasi GPS Anda. Silakan tentukan lokasi secara manual pada peta.', 'warning');
                });
            } else {
                Swal.fire('GPS Tidak Didukung', 'Browser Anda tidak mendukung geolokasi.', 'warning');
            }
        });
    });
</script>
<?= $this->endSection() ?>
