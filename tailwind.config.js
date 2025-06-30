/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    content: [
        './node_modules/@tiptap/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{vue,js,ts,jsx,tsx}',
        './resources/scripts/**/*.{vue,js,ts,jsx,tsx}',
    ],
    theme: {
        whitelist: [
            'rounded-xs', 'rounded-sm', 'rounded-md', 'rounded-lg', 'blockquote',
        ]
    },
};
