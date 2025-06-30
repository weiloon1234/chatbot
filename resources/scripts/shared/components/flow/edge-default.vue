<!-- components/DefaultEdgeOverride.vue -->
<template>
    <g>
        <path
            :d="edgePath"
            class="stroke-current text-gray-500"
            stroke-width="2"
            fill="none"
        />

        <foreignObject
            :x="labelX - 12"
            :y="labelY - 12"
            width="24"
            height="24"
            class="overflow-visible"
        >
            <div
                class="w-6 h-6 bg-red-500 border border-gray-300 rounded-sm shadow flex items-center justify-center cursor-pointer text-white hover:bg-white hover:text-red-500"
                @click.stop="remove"
            >
                <font-awesome-icon
                    icon="fas fa-trash"
                />
            </div>
        </foreignObject>
    </g>
</template>

<script setup>
import { computed } from 'vue'
import { getBezierPath, useVueFlow } from '@vue-flow/core'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    id: String,
    sourceX: Number,
    sourceY: Number,
    targetX: Number,
    targetY: Number,
    sourcePosition: String,
    targetPosition: String,
})

const { removeEdges } = useVueFlow()

// Dynamic edge path that updates with node drag
const edgePath = computed(() => {
    const [path] = getBezierPath({
        sourceX: props.sourceX,
        sourceY: props.sourceY,
        targetX: props.targetX,
        targetY: props.targetY,
        sourcePosition: props.sourcePosition,
        targetPosition: props.targetPosition,
    })
    return path
})

// Midpoint label for the "✕" button
const labelX = computed(() => (props.sourceX + props.targetX) / 2)
const labelY = computed(() => (props.sourceY + props.targetY) / 2)

const remove = () => {
    removeEdges([props.id])
}
</script>
