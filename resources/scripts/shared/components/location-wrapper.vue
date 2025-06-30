<template>
    <div v-if="permissionGranted">
        <slot />
    </div>
    <div v-else>
        <div v-if="permissionGranted === false">
            <div class="flex justify-center">
                <img :src="'/img/no-location.png'" class="w-25 text-center" />
            </div>
        </div>
        <div v-else>
            <div class="flex justify-center">
                <img :src="'/img/map-pin.gif'" class="w-25 text-center" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted } from 'vue';
import { useGpsStore } from '~/stores/gps.js';

const emit = defineEmits(['location-update', 'location-permission-granted', 'location-permission-failed']);

const $gpsStore = useGpsStore();

const permissionGranted = computed(() => {
    return $gpsStore.permissionGranted;
});

const initLocation = async () => {
    await $gpsStore.getLocation();
    await nextTick(() => {
        if ($gpsStore && $gpsStore.permissionGranted) {
            emit('location-permission-granted');
            emit('location-update', {
                lat: $gpsStore.lat,
                lng: $gpsStore.lng,
            });
        } else {
            emit('location-permission-failed');
            emit('location-update', {
                lat: null,
                lng: null,
            });
        }
    });
};

onMounted(async () => {
    await initLocation();
});
</script>
