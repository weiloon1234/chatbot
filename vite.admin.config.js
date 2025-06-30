import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import path from 'path';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import * as fs from 'fs';
import { writeFileSync } from 'fs';
import { config as DotEnvConfig } from 'dotenv';
import cp from 'child_process';
DotEnvConfig();

const role = 'admin';
const port = 3031;
const homeDir = process.env.HOME;
const host = process.env.APP_URL.split('//')[1];
let KEY_PATH = {};

if (process.env.APP_ENV === 'local') {
    if (process.env.VITE_CUSTOM_SSL_CERT_ENABLED === 'true') {
        if (process.env.VITE_CUSTOM_SSL_CERT) KEY_PATH.cert = process.env.VITE_CUSTOM_SSL_CERT;
        if (process.env.VITE_CUSTOM_SSL_CA) KEY_PATH.ca = process.env.VITE_CUSTOM_SSL_CA;
        if (process.env.VITE_CUSTOM_SSL_KEY) KEY_PATH.key = process.env.VITE_CUSTOM_SSL_KEY;
    } else {
        //Default, mac with valet
        KEY_PATH = {
            key: fs.readFileSync(path.resolve(homeDir, `.config/valet/Certificates/${host}.key`)),
            cert: fs.readFileSync(path.resolve(homeDir, `.config/valet/Certificates/${host}.crt`)),
        };
    }
}

export default defineConfig(({ command }) => {
    return {
        plugins: [
            tailwindcss(),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            {
                name: 'add-version-to-manifest',
                closeBundle() {
                    // Generate a timestamp-based version
                    const version = new Date().getTime();
                    const revision = String(cp.execSync('git rev-parse HEAD').toString().trim()).slice(0, 8);

                    const manifestPath = path.join(__dirname, `/public/build/${role}/.vite/`, 'manifest.json');
                    const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));

                    // Add the version to the manifest
                    manifest.version = version;
                    manifest.revision = revision;

                    writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
                },
            }
        ],
        server: {
            port,
            host,
            https: KEY_PATH,
            strictPort: true,
            cors: {
                origin: '*', // Allow all origins (use 'https://core11.test' for security)
                methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                allowedHeaders: ['Content-Type', 'Authorization'],
            },
            watch: {
                ignored: ['**/*.php'], // Ignore all PHP file changes
            }
        },
        resolve: {
            alias: {
                '#': path.resolve(__dirname, './resources/scripts'),
                '@': path.resolve(__dirname, './lang'),
                '~/shared': path.resolve(__dirname, './resources/scripts/shared'),
                '~/admin': path.resolve(__dirname, './resources/scripts/admin'),
            },
        },
        css: {
            postcss: {
                plugins: [tailwindcss, autoprefixer],
            },
        },
        base: command === 'serve' ? '' : `/build/${role}/`,
        publicDir: 'fake_dir_so_nothing_gets_copied',
        build: {
            manifest: true,
            outDir: `public/build/${role}/`,
            emptyOutDir: true,
            rollupOptions: {
                input: `resources/scripts/${role}/main.js`,
            },
            chunkSizeWarningLimit: 1600,
        },
        optimizeDeps: {
            include: ['vue', 'pinia', 'vue-i18n', 'moment', 'axios'],
        },
    };
});
