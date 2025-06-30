<template>
    <base-node
        :id="id"
        :data="data"
        class="qna-node"
    >
        <div
            v-if="data && data.content"
            class="w-full py-3"
        >
            {{ data?.content}}
        </div>
        <div v-for="(opt) in data.options" :key="opt.id" class="flex justify-between items-center space-x-2 my-1 relative border border-gray-300 rounded-sm p-2">
            <div>{{ opt.label }}</div>

            <Handle
                type="source"
                :position="Position.Right"
                :id="`answer-${opt.id}`"
                class="w-3 h-3 bg-blue-500 rounded-full"
            />
        </div>
    </base-node>
</template>

<script setup>
import BaseNode from "#/shared/components/flow/base-node.vue";
import {Handle, Position} from "@vue-flow/core";

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
