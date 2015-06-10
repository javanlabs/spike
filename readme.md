## Instalasi

1. Clone
2. Run `composer update`
3. Copy .env.example ke .env, sesuaikan isinya dengan konfigurasi development Anda
4. Run `php artisan migrate`
5. Run `php artisan assessment:import` untuk mendapatkan data ISDA dari file excel yang tersedia
