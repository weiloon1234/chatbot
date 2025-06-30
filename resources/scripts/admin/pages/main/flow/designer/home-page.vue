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
            content: 'Say hello to the world!',
            isFirstNode: true,
        }
    },
    {
        id: 'question1',
        type: 'qnaNode',
        position: { x: 250, y: 400 },
        data: {
            label: 'Language?',
            content: 'What language do you prefer?',
            options: [
                { id: 'opt1', label: 'English' },
                { id: 'opt2', label: 'Chinese' },
            ]
        }
    },
    {
        id: 'english',
        type: 'normalNode',
        position: { x: 1000, y: 250 },
        data: {
            label: 'English',
        }
    },
    {
        id: 'chinese',
        type: 'normalNode',
        position: { x: 1000, y: 500 },
        data: {
            label: 'Chinese',
        }
    }
]);

const defaultEdges = ref([
    {id: "1-question1", type: "default", source: "1", target: "question1"},
    {id: "question1-answer-opt1-english", type: "default", source: "question1", target: "english", sourceHandle: "answer-opt1"},
    {id: "question1-answer-opt2-chinese", type: "default", source: "question1", target: "chinese", sourceHandle: "answer-opt2"},
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
