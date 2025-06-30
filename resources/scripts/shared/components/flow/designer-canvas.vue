<template>
    <vue-flow
        :nodes="localNodes"
        :edges="localEdges"
        :node-types="nodeTypes"
        :edge-types="edgeTypes"
        :default-viewport="{ zoom: 0.5 }"
        :max-zoom="4"
        :min-zoom="0.1"
        @update:nodes="onNodesChange"
        @update:edges="onEdgesChange"
        @connect="onConnect"
    />
</template>

<script setup>
import {VueFlow} from '@vue-flow/core';
import edgeDefault from "./edge-default.vue";
import nodeDefault from "./node-default.vue";
import nodeType1 from "./node-type-1.vue";
import {ref, watch, markRaw} from "vue";

const props = defineProps({
    nodes: {
        type: Array,
        required: false,
        default: () => [],
    },
    edges: {
        type: Array,
        required: false,
        default: () => [],
    }
});

const emit = defineEmits(['update:nodes', 'update:edges']);

const localNodes = ref([...props.nodes]);
const localEdges = ref([...props.edges]);
// Watch for external changes and sync
watch(() => props.nodes, (newVal) => {
    localNodes.value = [...newVal];
});

watch(() => props.edges, (newVal) => {
    localEdges.value = [...newVal];
});

// Emit changes when Vue Flow updates them
const onNodesChange = (newNodes) => {
    localNodes.value = newNodes;
    emit('update:nodes', newNodes);
}

const onEdgesChange = (newEdges) => {
    localEdges.value = newEdges;
    emit('update:edges', newEdges);
}

const nodeTypes = {
    default: markRaw(nodeDefault),
    nodeType1: markRaw(nodeType1),
};
const edgeTypes = {
    default: markRaw(edgeDefault),
};

const onConnect = ({ source, target }) => {
    const newEdge = {
        id: `e${source}-${target}`,
        source,
        target,
        type: 'default',
    };

    // Avoid duplicate
    if (!localEdges.value.find(e => e.source === source && e.target === target)) {
        localEdges.value.push(newEdge);
        emit('update:edges', [...localEdges.value]);
    }
};
</script>
