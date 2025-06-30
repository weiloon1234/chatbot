<template>
    <div
        class="w-full h-[100dvh]"
    >
        <designer-canvas
            :nodes="defaultNodes"
            :edges="defaultEdges"
            @update:nodes="onNodesUpdate"
            @update:edges="onEdgesUpdate"
        />
    </div>
</template>

<script setup>
import {ref} from "vue";
import DesignerCanvas from "#/shared/components/flow/designer-canvas.vue";

const defaultNodes = ref([
    {
        id: '1',
        type: 'normalNode',
        position: { x: 250, y: 5 },
        data: {
            label: 'Node 1',
            isFirstNode: true,
        }
    },
    { id: '2', label: 'Node 2', position: { x: 100, y: 200 } },
    { id: '3', label: 'Node 3', position: { x: 400, y: 250 } },
    { id: '4', label: 'Node 4', position: { x: 400, y: 500 } },
    {
        id: 'question1',
        type: 'qnaNode',
        position: { x: 400, y: 800 },
        data: {
            label: 'What is your favorite color?',
            options: [
                { id: 'opt1', label: 'Red' },
                { id: 'opt2', label: 'Blue' },
                { id: 'opt3', label: 'Green' },
            ]
        }
    }
]);

const defaultEdges = ref([
    { id: 'e1-2', source: '1', target: '2', animated: true },
    { id: 'e1-3', source: '1', target: '3' },
    { id: 'e1-4', source: '1', target: '4' },
]);

const onNodesUpdate = (nodes) => {
    defaultNodes.value = nodes;
    // optionally send to server
    // saveFlow({ nodes });
};

const onEdgesUpdate = (edges) => {
    defaultEdges.value = edges;
    // optionally send to server
    // saveFlow({ edges });
};
</script>
