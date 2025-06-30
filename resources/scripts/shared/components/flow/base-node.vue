<template>
    <div class="flow-node">
        <!-- Top (target) handle -->
        <Handle
            v-if="!data?.isFirstNode"
            type="target"
            position="top"
            class="w-3 h-3 bg-blue-500 border-2 border-white rounded-full"
        />

        <!-- Bottom (source) handle -->
        <Handle
            type="source"
            position="bottom"
            class="w-3 h-3 bg-blue-500 border-2 border-white rounded-full"
        />
        <div
            class="flow-node-title"
        >
            <!-- Node Label -->
            <div class="font-semibold truncate pr-6">
                {{ data?.label ?? 'Node' }}
            </div>
            <!-- Delete Button -->
            <button
                @click="deleteNode"
                class="text-xs bg-red-500 hover:bg-red-600 text-white rounded-sm w-6 h-6 flex items-center justify-center shadow"
                title="Delete node"
            >
                <font-awesome-icon
                    icon="fas fa-trash"
                    class="text-white"
                />
            </button>
        </div>
        <div class="flow-node-content">
            <slot />
        </div>
    </div>
</template>

<script setup>
import {inject} from "vue";
import {useI18n} from "vue-i18n";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {Handle, useVueFlow} from "@vue-flow/core";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    data: {
        required: false,
        type: Object,
        default: () => null,
    }
});

const emit = defineEmits(['delete-node']);

const $i18n = useI18n();
const $helper = inject('$helper');
const { removeNodes } = useVueFlow();

const deleteNode = async () => {
    await $helper.alertConfirm({
        message: $i18n.t('Confirm delete node x?').toString().replace(/:x/i, props.data?.label ?? ' '),
        callback: (result) => {
            if (result.isConfirmed) {
                removeNodes([props.id]);
                emit('delete-node');
            }
        }
    });
};
</script>
