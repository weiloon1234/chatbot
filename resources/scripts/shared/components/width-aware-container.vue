<template>
    <div ref="wContainer">
        <slot :width="width" :container-ready="containerReady" />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const wContainer = ref();
const width = ref(0);
const containerReady = ref(false);

onMounted(() => {
    width.value = wContainer.value.clientWidth;
    containerReady.value = true;
});
</script>
