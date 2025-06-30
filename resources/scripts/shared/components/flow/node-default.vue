<template>
    <base-node
        :id="id"
        :data="data"
        class="node-default"
        @delete-node="deleteNode"
    >
        The content slot
    </base-node>
</template>

<script setup>
import { useVueFlow } from '@vue-flow/core'
import BaseNode from "#/shared/components/flow/base-node.vue";

const props = defineProps({
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

const { removeNodes } = useVueFlow();

const deleteNode = async () => {
    removeNodes([props.id]);
};
</script>
