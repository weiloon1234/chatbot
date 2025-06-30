<template>
    <div class="font-mono">{{ clock.date }} {{ clock.time }}</div>
</template>
<script setup>
import { ref, onMounted } from 'vue';

const clock = ref({
    time: '',
    date: '',
});

const week = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
const countdown = ref(null);

const zeroPadding = (num, digit) => {
    let zero = '';
    for (let i = 0; i < digit; i++) {
        zero += '0';
    }
    return (zero + num).slice(-digit);
};

const updateTime = () => {
    const cd = new Date();
    clock.value.time = zeroPadding(cd.getHours(), 2) + ':' + zeroPadding(cd.getMinutes(), 2) + ':' + zeroPadding(cd.getSeconds(), 2);
    clock.value.date = zeroPadding(cd.getFullYear(), 4) + '-' + zeroPadding(cd.getMonth() + 1, 2) + '-' + zeroPadding(cd.getDate(), 2) + ' ' + week[cd.getDay()];
};

onMounted(() => {
    countdown.value = setInterval(updateTime, 1000);
});
</script>
