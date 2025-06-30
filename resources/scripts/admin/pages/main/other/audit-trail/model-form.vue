<template>
    <div
        class="w-full flex flex-col space-y-4"
    >
        <admin-content-card
            v-if="model.operation === 'CREATED'"
        >
            <template #title>
                <div class="font-semibold">{{ $t('Created data') }}</div>
            </template>
            <pre><code class="language-html">{{ model.created_data }}</code></pre>
        </admin-content-card>
        <template v-else-if="model.operation === 'EDITED'">
            <admin-content-card>
                <template #title>
                    <div class="font-semibold">{{ $t('Old data') }}</div>
                </template>
                <pre><code class="language-html text-xs">{{ model.old_data }}</code></pre>
            </admin-content-card>
            <admin-content-card>
                <template #title>
                    <div class="font-semibold">{{ $t('Edited data') }}</div>
                </template>
                <pre><code class="language-html text-xs">{{ model.edited_data }}</code></pre>
            </admin-content-card>
        </template>
        <admin-content-card
            v-else-if="model.operation === 'DELETED'"
        >
            <template #title>
                <div class="font-semibold">{{ $t('Deleted data') }}</div>
            </template>
            <pre><code class="language-html text-xs">{{ model.old_data }}</code></pre>
        </admin-content-card>
    </div>
</template>

<script setup>
import AdminContentCard from "#/admin/components/admin-content-card.vue";

defineProps({
    model: {
        type: Object,
        required: false,
        default: () => null,
    },
});
</script>
