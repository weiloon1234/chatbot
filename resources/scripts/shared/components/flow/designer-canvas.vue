<template>
    <vue-flow
        v-model:nodes="localNodes"
        v-model:edges="localEdges"
        :node-types="nodeTypes"
        :edge-types="edgeTypes"
        :default-viewport="{ zoom: 0.5 }"
        :max-zoom="4"
        :min-zoom="0.1"
        @update:edges="onEdgesChange"
        @nodes-change="onNodeChanged"
        @connect="onConnect"
    />
</template>

<script setup>
import {VueFlow} from '@vue-flow/core';
import edgeDefault from "./edge-default.vue";
import nodeDefault from "./node-default.vue";
import normalNode from "./nodes/normal-node.vue";
import qnaNode from "./nodes/qna-node.vue";
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

const onNodeChanged = (updatedNode) => {
    const nodes = [...localNodes.value];
    const index = nodes.findIndex(node => node.id === updatedNode.id);
    if (index !== -1) {
        nodes[index] = updatedNode;
        emit('update:nodes', nodes);
    }
};

const onEdgesChange = (edges) => {
    emit('update:edges', edges);
};

const nodeTypes = {
    default: markRaw(nodeDefault),
    normalNode: markRaw(normalNode),
    qnaNode: markRaw(qnaNode),
};
const edgeTypes = {
    default: markRaw(edgeDefault),
};

const onConnect = ({ source, sourceHandle, target, targetHandle }) => {
    const id = sourceHandle
        ? `${source}-${sourceHandle}-${target}`
        : `${source}-${target}`;

    const isDuplicate = localEdges.value.some(
        e =>
            e.source === source &&
            e.target === target &&
            (e.sourceHandle || null) === (sourceHandle || null)
    );

    if (!isDuplicate) {
        const newEdge = {
            id,
            source,
            sourceHandle,
            target,
            targetHandle,
            type: 'default',
        };

        localEdges.value.push(newEdge);
        emit('update:edges', [...localEdges.value]);
    }
};
</script>
