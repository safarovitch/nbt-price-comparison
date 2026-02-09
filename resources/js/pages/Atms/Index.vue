<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const { __ } = useTrans();

interface Organization {
    uuid: string;
    name: string;
    logo: string;
}

interface DeviceLocation {
    id: string;
    type: 'atm' | 'terminal' | 'branch';
    city: string | null;
    address: string | null;
    latitude: number | null;
    longitude: number | null;
    working_hours: string | null;
    landmark: string | null;
    organization: Organization | null;
}

const props = defineProps<{
    locations: DeviceLocation[];
    cities: string[];
    types: string[];
}>();

const selectedType = ref<string>('all');
const selectedCity = ref<string>('all');
const searchQuery = ref<string>('');
const mapContainer = ref<HTMLElement | null>(null);
const selectedLocation = ref<DeviceLocation | null>(null);

let map: L.Map | null = null;
let markersLayer: L.LayerGroup | null = null;

const typeLabels: Record<string, string> = {
    all: 'All',
    atm: 'ATM',
    terminal: 'Terminal',
    branch: 'Branch',
};

const typeIcons: Record<string, string> = {
    atm: 'fa-solid fa-money-bill-transfer',
    terminal: 'fa-solid fa-credit-card',
    branch: 'fa-solid fa-building-columns',
};

const typeBadgeClasses: Record<string, string> = {
    atm: 'bg-primary',
    terminal: 'bg-success',
    branch: 'bg-info',
};

const typeMarkerColors: Record<string, string> = {
    atm: '#0d6efd',
    terminal: '#198754',
    branch: '#0dcaf0',
};

const filteredLocations = computed(() => {
    return props.locations.filter((location) => {
        // Filter by type
        if (selectedType.value !== 'all' && location.type !== selectedType.value) {
            return false;
        }

        // Filter by city
        if (selectedCity.value !== 'all' && location.city !== selectedCity.value) {
            return false;
        }

        // Filter by search query
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            const matchesAddress = location.address?.toLowerCase().includes(query);
            const matchesCity = location.city?.toLowerCase().includes(query);
            const matchesOrg = location.organization?.name?.toLowerCase().includes(query);
            const matchesLandmark = location.landmark?.toLowerCase().includes(query);

            if (!matchesAddress && !matchesCity && !matchesOrg && !matchesLandmark) {
                return false;
            }
        }

        return true;
    });
});

// Locations with valid coordinates for map
const mappableLocations = computed(() => {
    return filteredLocations.value.filter(
        (loc) => loc.latitude !== null && loc.longitude !== null
    );
});

const locationsByCity = computed(() => {
    const grouped: Record<string, DeviceLocation[]> = {};
    filteredLocations.value.forEach((location) => {
        const city = location.city || 'Unknown';
        if (!grouped[city]) {
            grouped[city] = [];
        }
        grouped[city].push(location);
    });
    return grouped;
});

const totalCount = computed(() => filteredLocations.value.length);

const getTypeCount = (type: string) => {
    if (type === 'all') return props.locations.length;
    return props.locations.filter((l) => l.type === type).length;
};

// Create custom marker icon
const createMarkerIcon = (type: string) => {
    const color = typeMarkerColors[type] || '#6c757d';
    return L.divIcon({
        className: 'custom-marker',
        html: `
            <div style="
                background-color: ${color};
                width: 32px;
                height: 32px;
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                display: flex;
                align-items: center;
                justify-content: center;
                border: 3px solid white;
                box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            ">
                <i class="${typeIcons[type] || 'fa-solid fa-location-dot'}" style="
                    transform: rotate(45deg);
                    color: white;
                    font-size: 12px;
                "></i>
            </div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32],
    });
};

// Initialize map
const initMap = () => {
    if (!mapContainer.value || map) return;

    // Default center: Dushanbe, Tajikistan
    const defaultCenter: [number, number] = [38.5598, 68.7740];

    map = L.map(mapContainer.value, {
        center: defaultCenter,
        zoom: 12,
        scrollWheelZoom: true,
    });

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    // Create markers layer
    markersLayer = L.layerGroup().addTo(map);

    // Initial markers
    updateMarkers();
};

// Update markers when filtered locations change
const updateMarkers = () => {
    if (!map || !markersLayer) return;

    // Clear existing markers
    markersLayer.clearLayers();

    // Add markers for filtered locations
    const bounds: L.LatLngBounds | null = mappableLocations.value.length > 0
        ? L.latLngBounds([])
        : null;

    mappableLocations.value.forEach((location) => {
        if (location.latitude === null || location.longitude === null) return;

        const marker = L.marker(
            [location.latitude, location.longitude],
            { icon: createMarkerIcon(location.type) }
        );

        // Create popup content
        const popupContent = `
            <div class="map-popup">
                <h6 class="fw-bold mb-1">${location.organization?.name || 'Unknown'}</h6>
                <span class="badge ${typeBadgeClasses[location.type] || 'bg-secondary'} mb-2">${typeLabels[location.type] || location.type}</span>
                <p class="mb-1 small"><i class="fa-solid fa-location-dot text-danger me-1"></i>${location.address || 'N/A'}</p>
                ${location.working_hours ? `<p class="mb-0 small text-muted"><i class="fa-regular fa-clock me-1"></i>${location.working_hours}</p>` : ''}
                <a href="https://www.google.com/maps?q=${location.latitude},${location.longitude}" target="_blank" class="btn btn-sm btn-outline-primary mt-2 w-100">
                    <i class="fa-solid fa-directions me-1"></i>Directions
                </a>
            </div>
        `;

        marker.bindPopup(popupContent, { maxWidth: 250 });

        // Highlight on click
        marker.on('click', () => {
            selectedLocation.value = location;
        });

        markersLayer!.addLayer(marker);

        if (bounds) {
            bounds.extend([location.latitude, location.longitude]);
        }
    });

    // Fit map to bounds if there are markers
    if (bounds && bounds.isValid()) {
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
    }
};

// Focus map on specific location
const focusOnLocation = (location: DeviceLocation) => {
    if (!map || !location.latitude || !location.longitude) return;

    selectedLocation.value = location;
    map.setView([location.latitude, location.longitude], 16);

    // Find and open the marker popup
    if (markersLayer) {
        markersLayer.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
                const latLng = layer.getLatLng();
                if (latLng.lat === location.latitude && latLng.lng === location.longitude) {
                    layer.openPopup();
                }
            }
        });
    }
};

// Watch for filter changes and update markers
watch([selectedType, selectedCity, searchQuery], () => {
    nextTick(() => {
        updateMarkers();
    });
});

onMounted(() => {
    nextTick(() => {
        initMap();
    });
});
</script>

<template>

    <Head :title="__('ATMs & Branches')" />

    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-4">
            <div class="container px-4">
                <!-- Header & Filters Top Bar -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <Link href="/" class="text-decoration-none text-muted mb-1 d-inline-block small">
                                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Back') }}
                            </Link>
                            <h2 class="fw-bold mb-0">{{ __('ATMs & Branches') }}</h2>
                        </div>

                        <!-- Type Tabs inside Filter Bar -->
                        <div class="d-none d-lg-block">
                            <ul class="nav nav-pills gap-2 flex-nowrap overflow-auto pb-1">
                                <li class="nav-item">
                                    <button class="nav-link px-3 py-2" :class="{ active: selectedType === 'all' }" @click="selectedType = 'all'">
                                        {{ __('All') }}
                                        <span class="badge ms-1" :class="selectedType === 'all' ? 'bg-white text-primary' : 'bg-secondary'">{{ getTypeCount('all') }}</span>
                                    </button>
                                </li>
                                <li v-for="type in types" :key="type" class="nav-item">
                                    <button class="nav-link px-3 py-2 text-nowrap" :class="{ active: selectedType === type }" @click="selectedType = type">
                                        <i :class="typeIcons[type]" class="me-1"></i>
                                        {{ typeLabels[type] || type }}
                                        <span class="badge ms-1" :class="selectedType === type ? 'bg-white text-primary' : 'bg-secondary'">{{ getTypeCount(type) }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Search -->
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fa-solid fa-search text-muted"></i>
                                </span>
                                <input v-model="searchQuery" type="text" class="form-control border-start-0 ps-0" :placeholder="__('Search address, bank...')" />
                            </div>
                        </div>

                        <!-- City Filter -->
                        <div class="col-md-3">
                            <select v-model="selectedCity" class="form-select">
                                <option value="all">{{ __('All Cities') }}</option>
                                <option v-for="city in cities" :key="city" :value="city">
                                    {{ city }}
                                </option>
                            </select>
                        </div>

                        <!-- Type Mobile Filter -->
                        <div class="col-md-4 d-lg-none">
                            <select v-model="selectedType" class="form-select">
                                <option value="all">{{ __('All Types') }}</option>
                                <option v-for="type in types" :key="type" :value="type">
                                    {{ typeLabels[type] || type }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 d-none d-lg-flex align-items-center text-muted small">
                            <span>
                                {{ __('Showing') }} <strong>{{ totalCount }}</strong> {{ __('locations') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Main Content split view -->
                <div class="row g-4 overflow-hidden mt-2" style="height: calc(100vh - 280px); min-height: 600px;">
                    <!-- Left: Scrollable List -->
                    <div class="col-lg-4 d-flex flex-column h-100">
                        <div class="bg-white rounded-4 shadow-sm h-100 overflow-hidden d-flex flex-column">
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="mb-0 fw-bold">{{ __('Locations List') }}</h6>
                            </div>
                            <div class="flex-grow-1 overflow-y-auto p-3 pt-0 custom-scrollbar">
                                <div v-if="filteredLocations.length > 0">
                                    <div v-for="(cityLocations, cityName) in locationsByCity" :key="cityName" class="mb-4">
                                        <div class="sticky-top bg-white py-2 mb-2 border-bottom z-1">
                                            <span class="fw-bold small text-uppercase text-muted">{{ cityName }} ({{ cityLocations.length }})</span>
                                        </div>

                                        <div class="d-flex flex-column gap-2">
                                            <div v-for="location in cityLocations" :key="location.id" class="card border shadow-none rounded-3 location-card-compact" :class="{ 'border-primary bg-light-primary': selectedLocation?.id === location.id }" @click="focusOnLocation(location)">
                                                <div class="p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div v-if="location.organization?.logo" class="org-logo-sm">
                                                                <img :src="location.organization.logo" :alt="location.organization.name" />
                                                            </div>
                                                            <div v-else class="org-logo-placeholder-sm">
                                                                <i class="fa-solid fa-building-columns"></i>
                                                            </div>
                                                            <h6 class="mb-0 fw-bold small text-truncate" style="max-width: 150px;">
                                                                {{ location.organization?.name || __('Unknown') }}
                                                            </h6>
                                                        </div>
                                                        <span class="badge x-small" :class="typeBadgeClasses[location.type] || 'bg-secondary'">
                                                            <i :class="typeIcons[location.type]" class="me-1"></i>
                                                            {{ typeLabels[location.type] || location.type }}
                                                        </span>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                                        {{ location.address }}
                                                    </div>
                                                    <div v-if="location.working_hours" class="x-small text-muted d-flex align-items-center">
                                                        <i class="fa-regular fa-clock text-success me-1"></i>
                                                        {{ location.working_hours }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-5">
                                    <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                                    <h6>{{ __('No locations found') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Map -->
                    <div class="col-lg-8 h-100">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative">
                            <div ref="mapContainer" class="h-100 w-100" style="z-index: 1;"></div>

                            <!-- Map Floating Controls Info -->
                            <div class="position-absolute top-0 end-0 m-3 z-3">
                                <div class="badge bg-white text-primary shadow-sm p-2 border border-primary-subtle d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    <span>{{ mappableLocations.length }} {{ __('locations pinned') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.bg-light-gray {
    background-color: #f8f9fa;
}

.bg-light-primary {
    background-color: #f0f7ff;
}

.location-card-compact {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid #dee2e6;
}

.location-card-compact:hover {
    border-color: var(--bs-primary);
    background-color: #f8f9fa;
    transform: translateX(4px);
}

.location-card-compact.border-primary {
    border-width: 2px !important;
}

.org-logo-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: white;
    border-radius: 4px;
    border: 1px solid #eee;
}

.org-logo-sm img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.org-logo-placeholder-sm {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    font-size: 0.8rem;
}

.nav-pills .nav-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-radius: 50px;
}

.nav-pills .nav-link.active {
    background-color: var(--bs-primary);
    color: white;
}

.badge.x-small {
    font-size: 0.65rem;
    padding: 0.35em 0.65em;
}

.x-small {
    font-size: 0.75rem;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #bbb;
}

.z-1 {
    z-index: 1;
}

.z-3 {
    z-index: 3;
}
</style>

<style>
/* Global styles for Leaflet popups */
.map-popup h6 {
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.map-popup .badge {
    font-size: 0.7rem;
}

.map-popup p {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.custom-marker {
    background: none !important;
    border: none !important;
}

/* Leaflet container fix */
.leaflet-container {
    height: 100%;
    width: 100%;
}
</style>
