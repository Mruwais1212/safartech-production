@php
    // Parse initial room data if provided
    $initialRoomData = [];
    if (isset($roomData) && is_string($roomData)) {
        $initialRoomData = json_decode($roomData, true) ?? [];
    } elseif (isset($roomData) && is_array($roomData)) {
        $initialRoomData = $roomData;
    }
    
    // Determine localStorage key based on prefix
    $storageKey = '';
    if ($prefix === 'hotel_') {
        $storageKey = 'HOTEL_ONLY';
    } elseif ($prefix === 'flight_') {
        $storageKey = 'FLIGHT_ONLY';
    } else {
        $storageKey = 'FLIGHT_HOTEL';
    }
@endphp

<div class="form-group">
    <label for="travelers">{{ $label ?? __('dashboard.Travelers') }}</label>
    <div class="dropdown">
        <input type="text" id="{{ $prefix }}travelers" class="form-control dropdown-input" placeholder="{{ $placeholder ?? __('dashboard.Travelers') }}" readonly>
        <div class="dropdown-menu input-dropdown dropdown-content">
            <div class="px-3 py-2">
                @if($showPassengersOnly ?? false)
                    <!-- Passengers-only version -->
                    <div class="form-group row">
                        <div class="col-md-5 d-flex align-items-center">
                            <label for="{{ $prefix }}adults">{{ __('dashboard.Adults') }}</label>
                        </div>
                        <div class="col-md-7">
                            <div class="custom-number-input">
                                <button type="button" class="decrement-btn" data-target="{{ $prefix }}adults">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <button type="button" class="increment-btn" data-target="{{ $prefix }}adults">
                                    <i class="bi bi-chevron-up"></i>
                                </button>
                                <input type="number" class="form-control dropdown-item-input"
                                    name="{{ $name ?? 'adults' }}" value="{{ $defaultAdults ?? 1 }}" id="{{ $prefix }}adults" min="1" max="6" />
                            </div>
                        </div>
                    </div>
                    <span class="text-danger" id="{{ $prefix }}adults-error" style="font-size: x-small;"></span>

                    <div class="form-group row">
                        <div class="col-md-5 d-flex align-items-center">
                            <label for="{{ $prefix }}children">{{ __('dashboard.Children') }}</label>
                        </div>
                        <div class="col-md-7">
                            <div class="custom-number-input">
                                <button type="button" class="decrement-btn" data-target="{{ $prefix }}children">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <button type="button" class="increment-btn" data-target="{{ $prefix }}children">
                                    <i class="bi bi-chevron-up"></i>
                                </button>
                                <input type="number" class="form-control dropdown-item-input children-count-input"
                                    name="{{ $nameChildren ?? 'children' }}" value="{{ $defaultChildren ?? 0 }}" id="{{ $prefix }}children" min="0" max="4" 
                                    data-prefix="{{ $prefix }}" />
                            </div>
                        </div>
                    </div>
                    <span class="text-danger" id="{{ $prefix }}children-error" style="font-size: x-small;"></span>
                    
                    <!-- Children Ages Container -->
                    <div class="children-ages-container" id="{{ $prefix }}children_ages" style="margin-top: 10px;">
                        <!-- Ages will be dynamically generated here -->
                    </div>
                    @if ( $prefix === 'hotel_')
                        
                    @else
                        <div class="form-group row">
                            <div class="col-md-5 d-flex align-items-center">
                                <label for="{{ $prefix }}babies">{{ __('dashboard.Babies') }}</label>
                            </div>
                            <div class="col-md-7">
                                <div class="custom-number-input">
                                    <button type="button" class="decrement-btn" data-target="{{ $prefix }}babies">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <button type="button" class="increment-btn" data-target="{{ $prefix }}babies">
                                        <i class="bi bi-chevron-up"></i>
                                    </button>
                                    <input type="number" class="form-control dropdown-item-input"
                                        name="{{ $nameBabies ?? 'babies' }}" value="{{ $defaultBabies ?? 0 }}" id="{{ $prefix }}babies" min="0" max="4" />
                                </div>
                            </div>
                        </div>
                        <span class="text-danger" id="{{ $prefix }}babies-error" style="font-size: x-small;"></span>
                    @endif
                    @else
                    <!-- Full version with rooms -->
                    <!-- Rooms Input -->
                    <div class="form-group row">
                        <div class="col-md-5 d-flex align-items-center">
                            <label for="{{ $prefix }}rooms">{{ __('dashboard.Rooms') }}</label>
                        </div>
                        <div class="col-md-7">
                            <div class="custom-number-input">
                                <button type="button" class="decrement-btn" data-target="{{ $prefix }}rooms">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <button type="button" class="increment-btn" data-target="{{ $prefix }}rooms">
                                    <i class="bi bi-chevron-up"></i>
                                </button>
                                <input type="number" class="form-control room-count-input"  min="1" max="6"
                                    name="{{ $name ?? 'rooms' }}" value="{{ $defaultRooms ?? 1 }}" id="{{ $prefix }}rooms" />
                            </div>
                        </div>
                    </div>
                    <span class="text-danger" id="{{ $prefix }}rooms-error" style="font-size: x-small;"></span>
                    
                    <!-- Dynamic Room Forms Container -->
                    <div id="{{ $prefix }}rooms-container">
                        <!-- Room forms will be dynamically generated here -->
                    </div>
                @endif

                <br>
                <button type="button" class="btn btn-warning closeButton dropdown-close-button w-100">{{ __('dashboard.Done') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
    .text-danger {
        font-size: small !important;
    }
    
    .room-section {
        margin-bottom: 15px;
    }
    
    .room-section h6 {
        color: #f39c12 !important;
        font-weight: bold !important;
        margin-bottom: 10px !important;
    }
    
    .room-section hr {
        border-color: #f39c12;
        opacity: 0.3;
    }
    
    .room-section .form-group {
        margin-bottom: 8px;
    }
    
    .dropdown-content {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
 <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Travelers input component initialized', '{{ $prefix }}');
    
    @if($showPassengersOnly ?? false)
        // Initialize passengers-only version
        const storageKey = '{{ $storageKey }}';
        
        // Try to load data from localStorage for passengers
        const STORAGE_KEYS = {
            FLIGHT_HOTEL: 'ai_trip_flight_hotel_form',
            HOTEL_ONLY: 'ai_trip_hotel_only_form',
            FLIGHT_ONLY: 'ai_trip_flight_only_form'
        };
        
        let savedPassengerData = null;
        const savedData = localStorage.getItem(STORAGE_KEYS[storageKey]);
        if (savedData) {
            try {
                savedPassengerData = JSON.parse(savedData);
            } catch (e) {
                console.log('Error parsing localStorage data:', e);
            }
        }
        
        // Set passenger values if found in localStorage
        if (savedPassengerData) {
            if (savedPassengerData.adults !== undefined) {
                $('#{{ $prefix }}adults').val(savedPassengerData.adults);
            }
            if (savedPassengerData.children !== undefined) {
                $('#{{ $prefix }}children').val(savedPassengerData.children);
            }
            if (savedPassengerData.babies !== undefined) {
                $('#{{ $prefix }}babies').val(savedPassengerData.babies);
            }
        }
        
        const childrenCount = parseInt($('#{{ $prefix }}children').val()) || 0;
        
        // Handle children ages if available
        let childAges = [];
        if (savedPassengerData && savedPassengerData.child_ages) {
            childAges = savedPassengerData.child_ages;
        }
        
        generateChildrenAgeInputs('{{ $prefix }}', childrenCount, childAges);
        
        // Handle children count changes
        $('#{{ $prefix }}children').on('input change', function() {
            const childrenCount = parseInt($(this).val()) || 0;
            generateChildrenAgeInputs('{{ $prefix }}', childrenCount, []);
        });
    @else
        // Initialize with rooms version
        const initialRoomData = @json($initialRoomData);
        const storageKey = '{{ $storageKey }}';
        
        // Try to load data from localStorage if no initial data provided
        let roomDataToUse = initialRoomData;
        if (Object.keys(initialRoomData).length === 0) {
            const STORAGE_KEYS = {
                FLIGHT_HOTEL: 'ai_trip_flight_hotel_form',
                HOTEL_ONLY: 'ai_trip_hotel_only_form',
                FLIGHT_ONLY: 'ai_trip_flight_only_form'
            };
            
            const savedData = localStorage.getItem(STORAGE_KEYS[storageKey]);
            if (savedData) {
                try {
                    const parsedData = JSON.parse(savedData);
                    if (storageKey === 'FLIGHT_HOTEL' && parsedData.roomsData) {
                        roomDataToUse = parsedData.roomsData;
                    } else if (storageKey === 'HOTEL_ONLY' && parsedData.hotelRoomsData) {
                        // Transform hotel room data keys from hotel_room_X_ to room_X_ for component compatibility
                        const transformedData = {};
                        for (const [key, value] of Object.entries(parsedData.hotelRoomsData)) {
                            const transformedKey = key.replace(/^hotel_room_/, 'room_');
                            transformedData[transformedKey] = value;
                        }
                        roomDataToUse = transformedData;
                        console.log('Transformed hotel room data in component:', roomDataToUse);
                    }
                } catch (e) {
                    console.log('Error parsing localStorage data:', e);
                }
            }
        } else {
            console.log('Using data passed from controller:', roomDataToUse);
        }
        
        console.log('Room data to use:', roomDataToUse);
        
        // Set room count from initial data if available
        let initialRoomCount = {{ $defaultRooms ?? 1 }};
        if (roomDataToUse && Object.keys(roomDataToUse).length > 0) {
            // Calculate room count from data
            const roomNumbers = Object.keys(roomDataToUse)
                .filter(key => key.includes('_adults'))
                .map(key => parseInt(key.match(/(\d+)/)[1]))
                .filter(num => !isNaN(num));
            
            if (roomNumbers.length > 0) {
                initialRoomCount = Math.max(...roomNumbers);
                $('#{{ $prefix }}rooms').val(initialRoomCount);
            }
        }
        
        generateRoomForms('{{ $prefix }}rooms-container', initialRoomCount, '{{ $prefix }}', roomDataToUse);
        
        // Make generateRoomForms globally available for external calls
        window.generateRoomForms = generateRoomForms;
        
        // Handle room count changes
        $('#{{ $prefix }}rooms').on('input change', function(e) {
            e.preventDefault();
            const roomCount = parseInt($(this).val());
            const max = parseInt($(this).attr('max')) || 6;
            const min = parseInt($(this).attr('min')) || 1;
            console.log('Minimum rooms allowed is', min, roomCount);
            if (roomCount > max) {
                $(this).val(max);
                $('#{{ $prefix }}rooms-error').text('{{ __("dashboard.Maximum rooms allowed is") }} ' + max);
                generateRoomForms('{{ $prefix }}rooms-container', max, '{{ $prefix }}', {});
            } else if (roomCount < min) {
                $(this).val(min);
                console.log('Minimum rooms allowed is', min);
                $('#{{ $prefix }}rooms-error').text('{{ __("dashboard.Minimum rooms allowed is") }} ' + min);

                generateRoomForms('{{ $prefix }}rooms-container', min, '{{ $prefix }}', {});
            } else {

                
                $('#{{ $prefix }}rooms-error').text('');
                generateRoomForms('{{ $prefix }}rooms-container', roomCount, '{{ $prefix }}', {});
            }
        });
    @endif

    // Common functions for both versions
    function generateChildrenAgeInputs(prefix, childrenCount = null) {
        if (childrenCount === null) {
            childrenCount = parseInt($(`#${prefix}children`).val()) || 0;
        }
        
        const container = document.getElementById(`${prefix}children_ages`);
        if (!container) return;
        
        // Store existing age values before clearing
        const existingAges = {};
        for (let j = 1; j <= 4; j++) {
            const ageInput = document.getElementById(`${prefix}child_${j}_age`);
            if (ageInput) {
                existingAges[j] = ageInput.value;
            }
        }
        
        container.innerHTML = '';
        
        if (childrenCount > 0) {
            const agesDiv = document.createElement('div');
            agesDiv.className = 'row';
            agesDiv.innerHTML = '<div class="col-12"><small class="text-muted mt-2 d-block text-start">{{ __("dashboard.Children Ages") }}</small></div>';
            
            for (let j = 1; j <= childrenCount; j++) {
                const ageValue = existingAges[j] || '';
                const ageField = document.createElement('div');
                ageField.className = 'col-md-4 mb-2';
                
                // Generate age options from 1 to 18
                let ageOptions = '<option value="" disabled selected>{{ __("dashboard.Age") }}</option>';
                for (let age = 0; age <= 18; age++) {
                    const selected = ageValue == age ? 'selected' : '';
                    if(age === 0){
                        ageOptions += `<option value="1" ${selected}>{{ __("dashboard.Less than 1 year") }}</option>`;
                    } else {
                        ageOptions += `<option value="${age}" ${selected}>${age}</option>`;
                    }
                }
                
                ageField.innerHTML = `
                    <div class="form-group my-1">
                        <label for="${prefix}child_${j}_age" class="form-label" style="font-size: 12px;">{{ __('dashboard.Child') }} ${j} {{ __('dashboard.Age') }}</label>
                        <select class="form-control form-control-sm child-age-select" 
                                id="${prefix}child_${j}_age" 
                                name="${prefix}child_ages[]" 
                                style="font-size: 12px; height: 32px; padding:0px 10px">
                            ${ageOptions}
                        </select>
                        <span class="text-danger child-age-error" style="font-size: 10px;"></span>
                    </div>
                `;
                agesDiv.appendChild(ageField);
            }
            
            container.appendChild(agesDiv);
            
            // Add validation for child age inputs
            attachChildAgeValidation();
        }
    }

    @unless($showPassengersOnly ?? false)
    // Room-specific functions
    function generateRoomForms(containerId, roomCount, prefix = '', initialData = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            return;
        }
        
        // Store existing values before clearing
        const existingValues = {};
        for (let i = 1; i <= 10; i++) {
            const adultsInput = document.getElementById(`${prefix}room_${i}_adults`);
            const childrenInput = document.getElementById(`${prefix}room_${i}_children`);
            const babiesInput = document.getElementById(`${prefix}room_${i}_babies`);
            
            if (adultsInput) {
                existingValues[i] = {
                    // Enforce at least 1 adult
                    adults: Math.max(1, parseInt(adultsInput.value) || 1).toString(),
                    children: childrenInput ? (parseInt(childrenInput.value) || 0).toString() : '0',
                    babies: babiesInput ? (parseInt(babiesInput.value) || 0).toString() : '0'
                };
            }
        }
        
        container.innerHTML = '';
        
        for (let i = 1; i <= roomCount; i++) {
            const roomDiv = document.createElement('div');
            roomDiv.className = 'room-section';
            
            // Priority: initial data > existing values > defaults
            let adults = '1';
            let children = '0';
            let babies = '0';
            
            if (initialData && initialData[`room_${i}_adults`] !== undefined) {
                // Enforce at least 1 adult even from initial data
                adults = Math.max(1, parseInt(initialData[`room_${i}_adults`]) || 1).toString();
                children = (initialData[`room_${i}_children`] || 0).toString();
                babies = (initialData[`room_${i}_babies`] || 0).toString();
            } else if (existingValues[i]) {
                adults = existingValues[i].adults;
                children = existingValues[i].children;
                babies = existingValues[i].babies;
            } else if (i === 1) {
                adults = '1';
            }
            
            roomDiv.innerHTML = `
                <hr style="margin: 15px 0;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="color: #f39c12; font-weight: bold;">{{ __('dashboard.Room') }} ${i}</h6>
                    ${roomCount > 1 ? `<button type="button" class="btn btn-sm btn-outline-danger delete-room-btn" data-room="${i}" data-prefix="${prefix}" style="padding:6px 8px 2px 8px; font-size: 12px;"><i class="bi bi-trash"></i> {{ __('dashboard.Delete') }}</button>` : ''}
                </div>
                
                <!-- Adults for Room ${i} -->
                <div class="form-group row">
                    <div class="col-md-5 d-flex align-items-center">
                        <label>{{ __('dashboard.Adults') }}</label>
                    </div>
                    <div class="col-md-7">
                        <div class="custom-number-input">
                            <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_adults">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <button type="button" class="increment-btn" data-target="${prefix}room_${i}_adults">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                            <input type="number" class="form-control dropdown-item-input room-input"
                                name="${prefix}room_${i}_adults" value="${adults}" id="${prefix}room_${i}_adults" min="1" max="6" />
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="${prefix}room-${i}-adults-error" style="font-size: x-small;"></span>
                
                <!-- Children for Room ${i} -->
                <div class="form-group row">
                    <div class="col-md-5 d-flex align-items-center">
                        <label>{{ __('dashboard.Children') }}</label>
                    </div>
                    <div class="col-md-7">
                        <div class="custom-number-input">
                            <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_children">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <button type="button" class="increment-btn" data-target="${prefix}room_${i}_children">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                            <input type="number" class="form-control dropdown-item-input room-input children-count-input"
                                name="${prefix}room_${i}_children" value="${children}" id="${prefix}room_${i}_children" min="0" max="4" 
                                data-room="${i}" data-prefix="${prefix}" />
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="${prefix}room-${i}-children-error" style="font-size: x-small;"></span>
                
                <!-- Children Ages Container -->
                <div class="children-ages-container" id="${prefix}room_${i}_children_ages" style="margin-top: 10px;">
                    <!-- Ages will be dynamically generated here -->
                </div>
                
               
            `;
             if(prefix !== 'hotel_'){
                roomDiv.innerHTML += `
                <!-- Babies for Room ${i} -->
                <div class="form-group row">
                    <div class="col-md-5 d-flex align-items-center">
                        <label>{{ __('dashboard.Babies') }}</label>
                    </div>
                    <div class="col-md-7">
                        <div class="custom-number-input">
                            <button type="button" class="decrement-btn" data-target="${prefix}room_${i}_babies">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <button type="button" class="increment-btn" data-target="${prefix}room_${i}_babies">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                            <input type="number" class="form-control dropdown-item-input room-input"
                                name="${prefix}room_${i}_babies" value="${babies}" id="${prefix}room_${i}_babies" min="0" max="4" />
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="${prefix}room-${i}-babies-error" style="font-size: x-small;"></span>
                `;
            }
            container.appendChild(roomDiv);
        }
        
        // Attach event listeners for the new inputs
        attachRoomInputListeners(prefix);
        
        // Generate children age inputs for each room
        for (let i = 1; i <= roomCount; i++) {
            const childrenCount = parseInt(document.getElementById(`${prefix}room_${i}_children`)?.value) || 0;
            const childAges = initialData && initialData[`room_${i}_child_ages`] ? initialData[`room_${i}_child_ages`] : [];
            generateRoomChildrenAgeInputs(i, childrenCount, prefix, childAges);
        }
    }

    function generateRoomChildrenAgeInputs(roomNumber, childrenCount, prefix = '', initialChildAges = []) {
        const container = document.getElementById(`${prefix}room_${roomNumber}_children_ages`);
        if (!container) return;
        
        // Store existing age values before clearing
        const existingAges = {};
        for (let j = 1; j <= 4; j++) {
            const ageInput = document.getElementById(`${prefix}room_${roomNumber}_child_${j}_age`);
            if (ageInput) {
                existingAges[j] = ageInput.value;
            }
        }
        
        container.innerHTML = '';
        
        if (childrenCount > 0) {
            const agesDiv = document.createElement('div');
            agesDiv.className = 'row';
            agesDiv.innerHTML = '<div class="col-12"><small class="text-muted mt-2 d-block text-start">{{ __("dashboard.Children Ages") }}</small></div>';
            
            for (let j = 1; j <= childrenCount; j++) {
                // Priority: initial data > existing values > empty
                let ageValue = '';
                if (initialChildAges && initialChildAges[j-1] !== undefined) {
                    ageValue = initialChildAges[j-1];
                } else if (existingAges[j]) {
                    ageValue = existingAges[j];
                }
                
                const ageField = document.createElement('div');
                ageField.className = 'col-md-4 mb-2';
                
                // Generate age options from 0 to 18
                let ageOptions = '<option value="" disabled selected>{{ __("dashboard.Age") }}</option>';
                for (let age = 0; age <= 18; age++) {
                    const selected = ageValue == age ? 'selected' : '';
                    //ageOptions += `<option value="${age}" ${selected}>${age}</option>`;
                    if(age === 0){
                        ageOptions += `<option value="1" ${selected}>{{ __("dashboard.Less than 1 year") }}</option>`;
                    } else {
                        ageOptions += `<option value="${age}" ${selected}>${age}</option>`;
                    }
                }
                
                ageField.innerHTML = `
                    <div class="form-group my-1">
                        <label for="${prefix}room_${roomNumber}_child_${j}_age" class="form-label" style="font-size: 12px;">{{ __('dashboard.Child') }} ${j} {{ __('dashboard.Age') }}</label>
                        <select class="form-control form-control-sm child-age-select" 
                                id="${prefix}room_${roomNumber}_child_${j}_age" 
                                name="${prefix}room_${roomNumber}_child_ages[]" 
                                style="font-size: 12px; height: 32px; padding:0px 10px">
                            ${ageOptions}
                        </select>
                        <span class="text-danger child-age-error" style="font-size: 10px;"></span>
                    </div>
                `;
                agesDiv.appendChild(ageField);
            }
            
            container.appendChild(agesDiv);
        }
    }

    function attachRoomInputListeners(prefix = '') {
        // Remove existing handlers for this prefix to avoid duplicates
        $(document)
            .off(`click.roomCount_${prefix}`, `[data-target="${prefix}rooms"].increment-btn`)
            .off(`click.roomCount_${prefix}`, `[data-target="${prefix}rooms"].decrement-btn`)
            .off(`click.roomTravelers_${prefix}`, `[data-target^="${prefix}room_"]`)
            .off(`input.roomInputs_${prefix} change.roomInputs_${prefix}`, `[name^="${prefix}room_"]`)
            .off(`click.deleteRoom_${prefix}`, `.delete-room-btn[data-prefix="${prefix}"]`);

        // Handle increment/decrement for room count input
        $(document).on(`click.roomCount_${prefix}`, `[data-target="${prefix}rooms"].increment-btn:not(.processed)`, function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            $(this).addClass('processed');
            
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const currentValue = parseInt(input.val())-1 || 1;
            const maxValue = parseInt(input.attr('max')) || 6;
            
            if (currentValue < maxValue) {
                const newValue = currentValue + 1;
                input.val(newValue).trigger('change');
                
                // Clear error message
                const errorId = targetId.replace(/_/g, '-') + '-error';
                $('#' + errorId).text('');
                
                // Regenerate room forms with new count (preserve nothing intentionally here)
                const containerId = `${prefix}rooms-container`;
                generateRoomForms(containerId, newValue, prefix, {});
                
                // Trigger localStorage saving and traveler summary update
                if (prefix === 'hotel_') {
                    if (typeof updateHotelTravelerSummary === 'function') {
                        setTimeout(updateHotelTravelerSummary, 200);
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 300);
                    }
                } else if (prefix === '') {
                    if (typeof updateFlightHotelTravelerSummary === 'function') {
                        setTimeout(updateFlightHotelTravelerSummary, 200);
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 300);
                    }
                }
            }
            
            setTimeout(() => $(this).removeClass('processed'), 100);
        });

        $(document).on(`click.roomCount_${prefix}`, `[data-target="${prefix}rooms"].decrement-btn:not(.processed)`, function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            $(this).addClass('processed');
            
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const currentValue = parseInt(input.val()) || 1;
            const minValue = parseInt(input.attr('min')) || 1;
            
            if (currentValue >= minValue) {
                const newValue = currentValue ;
                input.val(newValue).trigger('change');
                
                // Clear error message
                const errorId = targetId.replace(/_/g, '-') + '-error';
                $('#' + errorId).text('');
                
                // Regenerate room forms with new count (preserve nothing intentionally here)
                const containerId = `${prefix}rooms-container`;
                generateRoomForms(containerId, newValue, prefix, {});
                
                // Trigger localStorage saving and traveler summary update
                if (prefix === 'hotel_') {
                    if (typeof updateHotelTravelerSummary === 'function') {
                        setTimeout(updateHotelTravelerSummary, 200);
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 300);
                    }
                } else if (prefix === '') {
                    if (typeof updateFlightHotelTravelerSummary === 'function') {
                        setTimeout(updateFlightHotelTravelerSummary, 200);
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 300);
                    }
                }
            }
            
            setTimeout(() => $(this).removeClass('processed'), 100);
        });
        
        // Handle increment/decrement for room travelers (adults, children, babies within rooms)
        $(document).on(`click.roomTravelers_${prefix}`, `[data-target^="${prefix}room_"]:not(.processed)`, function(e) {
            if (!$(this).hasClass('increment-btn') && !$(this).hasClass('decrement-btn')) return;
            
            e.preventDefault();
            e.stopImmediatePropagation();
            
            $(this).addClass('processed');
            
            const targetId = $(this).data('target');
            const input = $('#' + targetId);
            const currentValue = parseInt(input.val()) || 0;
            const isIncrement = $(this).hasClass('increment-btn');
            const maxValue = parseInt(input.attr('max')) || (targetId.includes('adults') ? 6 : 4);
            const minValue = parseInt(input.attr('min')) || (targetId.includes('adults') ? 1 : 0);
            
            if (isIncrement && currentValue < maxValue) {
                input.val(currentValue + 1).trigger('change');
                // Handle children ages if needed
                if (targetId.includes('_children')) {
                    const roomNumber = targetId.match(/\d+/)[0];
                    generateRoomChildrenAgeInputs(roomNumber, currentValue + 1, prefix, []);
                }
                
                // Trigger localStorage saving and traveler summary update
                if (prefix === 'hotel_') {
                    if (typeof updateHotelTravelerSummary === 'function') {
                        updateHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100); // Small delay to ensure values are updated
                    }
                } else if (prefix === '') {
                    if (typeof updateFlightHotelTravelerSummary === 'function') {
                        updateFlightHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100);
                    }
                }
            } else if (!isIncrement && currentValue > minValue) {
                input.val(currentValue - 1).trigger('change');
                // Handle children ages if needed
                if (targetId.includes('_children')) {
                    const roomNumber = targetId.match(/\d+/)[0];
                    generateRoomChildrenAgeInputs(roomNumber, currentValue - 1, prefix, []);
                }
                
                // Trigger localStorage saving and traveler summary update
                if (prefix === 'hotel_') {
                    if (typeof updateHotelTravelerSummary === 'function') {
                        updateHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100); // Small delay to ensure values are updated
                    }
                } else if (prefix === '') {
                    if (typeof updateFlightHotelTravelerSummary === 'function') {
                        updateFlightHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100);
                    }
                }
            }
            
            setTimeout(() => $(this).removeClass('processed'), 100);
        });
        
        // Handle direct input changes for room inputs
        $(document).on(`input.roomInputs_${prefix} change.roomInputs_${prefix}`, `[name^="${prefix}room_"]`, function(e) {
            e.preventDefault();
            const value = parseInt($(this).val());
            const max = parseInt($(this).attr('max'));
            const min = parseInt($(this).attr('min'));
            const errorId = $(this).attr('id').replace(/_/g, '-') + '-error';
            
            // Enforce min/max in UI (adults min 1 always)
            if (!isNaN(max) && value > max) {
                $(this).val(max);
                const fieldType = $(this).attr('name').includes('adults') ? 'adults' : 
                                $(this).attr('name').includes('children') ? 'children' : 'babies';
                const message = getErrorMessage(fieldType, max);
                $('#' + errorId).text(message);
            } else if (!isNaN(min) && value < min) {
                $(this).val(min);
            }
            
            // Handle children age inputs
            if ($(this).attr('name').includes('children')) {
                const roomNumber = $(this).data('room');
                const childrenValue = parseInt($(this).val()) || 0;
                generateRoomChildrenAgeInputs(roomNumber, childrenValue, prefix, []);
            }
            
            // Trigger localStorage saving and traveler summary update for hotel case
            if (prefix === 'hotel_') {
                if (typeof updateHotelTravelerSummary === 'function') {
                    updateHotelTravelerSummary();
                }
                if (typeof saveFormData === 'function') {
                    saveFormData();
                }
            } else if (prefix === '') {
                // Flight+Hotel case
                if (typeof updateFlightHotelTravelerSummary === 'function') {
                    updateFlightHotelTravelerSummary();
                }
                if (typeof saveFormData === 'function') {
                    saveFormData();
                }
            }
        });

        // Handle room deletion (namespaced to avoid multiple deletions)
        $(document).on(`click.deleteRoom_${prefix}`, `.delete-room-btn[data-prefix="${prefix}"]`, function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const roomNumber = parseInt($(this).data('room'));
            const roomsInputId = `#${prefix}rooms`;
            const containerId = `${prefix}rooms-container`;
            
            // Get current room count and decrease by 1
            const currentCount = parseInt($(roomsInputId).val()) || 1;
            if (currentCount > 1) {
                // First, collect all existing room data before deletion
                const existingRoomData = {};
                for (let i = 1; i <= currentCount; i++) {
                    const adultsInput = document.getElementById(`${prefix}room_${i}_adults`);
                    const childrenInput = document.getElementById(`${prefix}room_${i}_children`);
                    const babiesInput = document.getElementById(`${prefix}room_${i}_babies`);
                    
                    if (adultsInput) {
                        // Enforce at least 1 adult when collecting
                        existingRoomData[`room_${i}_adults`] = Math.max(1, parseInt(adultsInput.value) || 1);
                        existingRoomData[`room_${i}_children`] = parseInt(childrenInput ? childrenInput.value : '0') || 0;
                        existingRoomData[`room_${i}_babies`] = parseInt(babiesInput ? babiesInput.value : '0') || 0;
                        
                        // Collect child ages
                        const childrenCount = existingRoomData[`room_${i}_children`];
                        if (childrenCount > 0) {
                            const childAges = [];
                            for (let j = 1; j <= childrenCount; j++) {
                                const ageSelect = document.getElementById(`${prefix}room_${i}_child_${j}_age`);
                                if (ageSelect && ageSelect.value) {
                                    childAges.push(parseInt(ageSelect.value));
                                }
                            }
                            if (childAges.length > 0) {
                                existingRoomData[`room_${i}_child_ages`] = childAges;
                            }
                        }
                    }
                }
                
                // Remove the data for the deleted room and shift remaining rooms down
                const newRoomData = {};
                let newRoomIndex = 1;
                
                for (let i = 1; i <= currentCount; i++) {
                    if (i !== roomNumber) {
                        // Shift room data to fill the gap
                        if (existingRoomData[`room_${i}_adults`] !== undefined) {
                            newRoomData[`room_${newRoomIndex}_adults`] = existingRoomData[`room_${i}_adults`];
                            newRoomData[`room_${newRoomIndex}_children`] = existingRoomData[`room_${i}_children`];
                            newRoomData[`room_${newRoomIndex}_babies`] = existingRoomData[`room_${i}_babies`];
                            
                            if (existingRoomData[`room_${i}_child_ages`]) {
                                newRoomData[`room_${newRoomIndex}_child_ages`] = existingRoomData[`room_${i}_child_ages`];
                            }
                        }
                        newRoomIndex++;
                    }
                }
                
                const newCount = currentCount - 1;
                $(roomsInputId).val(newCount);
                
                // Regenerate room forms with preserved data
                generateRoomForms(containerId, newCount, prefix, newRoomData);
                
                // Clear any error messages
                $(`#${prefix}rooms-error`).text('');
            }
        });
    }
    @endunless

    // Common functions
    function attachChildAgeValidation() {
        $(document).on('change', '.child-age-select', function() {
            const value = $(this).val();
            const errorSpan = $(this).siblings('.child-age-error');
            
            // Clear previous error
            errorSpan.text('');
            $(this).removeClass('is-invalid');
            
            if (!value || value === '') {
                $(this).addClass('is-invalid');
                errorSpan.text('{{ __("dashboard.Please select an age") }}');
            }
            
            // Trigger localStorage saving for room-based children ages
            const elementId = $(this).attr('id');
            if (elementId && (elementId.includes('room_') || elementId.includes('child_'))) {
                // Determine prefix from element ID
                let prefix = '';
                if (elementId.startsWith('hotel_')) {
                    prefix = 'hotel_';
                } else if (elementId.startsWith('flight_')) {
                    prefix = 'flight_';
                }
                
                // Trigger appropriate functions based on prefix
                if (prefix === 'hotel_') {
                    if (typeof updateHotelTravelerSummary === 'function') {
                        updateHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100);
                    }
                } else if (prefix === '' || elementId.includes('room_')) {
                    // Flight+Hotel case or general room case
                    if (typeof updateFlightHotelTravelerSummary === 'function') {
                        updateFlightHotelTravelerSummary();
                    }
                    if (typeof saveFormData === 'function') {
                        setTimeout(saveFormData, 100);
                    }
                }
            }
        });
    }

    function getErrorMessage(fieldType, maxValue) {
        const messages = {
            'adults': '{{ __("dashboard.Maximum adults allowed is") }} ' + maxValue,
            'children': '{{ __("dashboard.Maximum children allowed is") }} ' + maxValue,
            'babies': '{{ __("dashboard.Maximum babies allowed is") }} ' + maxValue
        };
        return messages[fieldType] || '';
    }

    // Handle increment/decrement buttons for general inputs (adults, children, babies - NOT rooms)
    // $(document).on('click', '.increment-btn:not(.processed)', function(e) {
    //     e.preventDefault();
    //     e.stopImmediatePropagation();
        
    //     $(this).addClass('processed');
        
    //     const targetId = $(this).data('target');
    //     const input = $('#' + targetId);
        
    //     // Skip all room-related inputs - they are handled by attachRoomInputListeners
    //     if (targetId.includes('room')) {
    //         $(this).removeClass('processed');
    //         return;
    //     }
        
    //     const currentValue = parseInt(input.val()) || 0;
    //     const maxValue = parseInt(input.attr('max')) || (targetId.includes('adults') ? 6 : 4);
        
    //     if (currentValue < maxValue) {
    //         input.val(currentValue + 1).trigger('change');
    //         // Clear error message
    //         const errorId = targetId.replace(/_/g, '-') + '-error';
    //         $('#' + errorId).text('');
            
    //         // Handle children age inputs for passengers-only version
    //         if (targetId.includes('children') && !targetId.includes('room_')) {
    //             const prefix = targetId.replace('children', '');
    //             generateChildrenAgeInputs(prefix, currentValue + 1);
    //         }
    //     }
        
    //     setTimeout(() => $(this).removeClass('processed'), 100);
    // });

    // $(document).on('click', '.decrement-btn:not(.processed)', function(e) {
    //     e.preventDefault();
    //     e.stopImmediatePropagation();
        
    //     $(this).addClass('processed');
        
    //     const targetId = $(this).data('target');
    //     const input = $('#' + targetId);
        
    //     // Skip all room-related inputs - they are handled by attachRoomInputListeners
    //     if (targetId.includes('room')) {
    //         $(this).removeClass('processed');
    //         return;
    //     }
        
    //     const currentValue = parseInt(input.val()) || 0;
    //     const minValue = parseInt(input.attr('min')) || (targetId.includes('adults') ? 1 : 0);
        
    //     if (currentValue > minValue) {
    //         input.val(currentValue - 1).trigger('change');
    //         // Clear error message
    //         const errorId = targetId.replace(/_/g, '-') + '-error';
    //         $('#' + errorId).text('');
            
    //         // Handle children age inputs for passengers-only version
    //         if (targetId.includes('children') && !targetId.includes('room_')) {
    //             const prefix = targetId.replace('children', '');
    //             generateChildrenAgeInputs(prefix, currentValue - 1);
    //         }
    //     }
        
    //     setTimeout(() => $(this).removeClass('processed'), 100);
    // });
});
</script>