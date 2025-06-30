<template>
    <base-node
        :id="id"
        :data="data"
        class="normal-node"
    >
        {{ data?.content }}
    </base-node>
</template>

<script setup>
import BaseNode from "#/shared/components/flow/base-node.vue";
defineProps({
    id: {
        type: String,
        required: true,
    },
    data: {
        required: false,
        type: Object,
        default: () => null,
    },
});
</script>
