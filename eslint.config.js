import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';
import { noTsInScriptSetup } from './eslint-custom-rules.mjs';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vueTsConfigs.recommended,
    {
        ignores: ['vendor', 'node_modules', 'public', 'bootstrap/ssr', 'tailwind.config.js'],
    },
    {
        plugins: {
            local: {
                files: ['**/*.vue'],
                rules: {
                    'no-ts-in-script-setup': noTsInScriptSetup
                }
            }
        },
        rules: {
            'local/no-ts-in-script-setup': 'error',
            'vue/block-lang': 'off',
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
        },
    },
    prettier,
);
