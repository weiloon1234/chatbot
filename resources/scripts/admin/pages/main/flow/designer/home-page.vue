<template>
    <div
        class="w-full h-[100dvh]"
    >
        <designer-canvas
            :nodes="nodes"
            :edges="edges"
        />
    </div>
</template>

<script setup>
import {ref} from "vue";
import DesignerCanvas from "#/shared/components/flow/designer-canvas.vue";

const nodes = ref([
    {
        id: '1',
        type: 'nodeType1',
        position: { x: 250, y: 5 },
        data: {
            label: 'Node 1'
        }
    },
    { id: '2', label: 'Node 2', position: { x: 100, y: 200 } },
    { id: '3', label: 'Node 3', position: { x: 400, y: 250 } },
    { id: '4', label: 'Node 4', position: { x: 400, y: 500 } },
]);

const edges = ref([
    { id: 'e1-2', source: '1', target: '2', animated: true },
    { id: 'e1-3', source: '1', target: '3' },
    { id: 'e1-4', source: '1', target: '4' },
]);
</script>
