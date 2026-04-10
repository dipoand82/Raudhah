import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: true, // Izinkan akses dari alamat mana pun (Localhost atau Ngrok)
        hmr: {
            // Biarkan Vite mendeteksi host secara otomatis
        },
        strictPort: false, // Menghindari konflik jika port 5173 sedang dipakai
    },
});
// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     server: {
//         hmr: {
//             // Gunakan alamat ngrok kamu saat ini
//             host: 'thea-accoladed-jaxton.ngrok-free.dev',
//             protocol: 'wss',
//         },
//     },
// });
