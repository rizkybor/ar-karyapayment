<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NationalDay;

class NationalDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $data = [
            ['date_code' => '01-01', 'title' => 'Tahun Baru Masehi', 'message' => 'Selamat Tahun Baru!', 'icon' => null],
            ['date_code' => '01-27', 'title' => 'Isra Mi\'raj', 'message' => 'Peringatan Isra Mi\'raj Nabi Muhammad SAW', 'icon' => null],
            ['date_code' => '01-28', 'title' => 'Cuti Bersama Tahun Baru Imlek', 'message' => 'Selamat menikmati cuti bersama', 'icon' => null],
            ['date_code' => '01-29', 'title' => 'Tahun Baru Imlek', 'message' => 'Gong Xi Fa Cai! Selamat Imlek!', 'icon' => null],
            ['date_code' => '03-28', 'title' => 'Cuti Bersama Nyepi', 'message' => 'Selamat menjalankan Cuti Bersama', 'icon' => null],
            ['date_code' => '03-29', 'title' => 'Hari Suci Nyepi', 'message' => 'Selamat Hari Raya Nyepi', 'icon' => null],
            ['date_code' => '03-31', 'title' => 'Hari Raya Idul Fitri', 'message' => 'Selamat Hari Raya Idul Fitri 1446 H', 'icon' => null],
            ['date_code' => '04-01', 'title' => 'Cuti Bersama Lebaran', 'message' => 'Selamat menikmati cuti Lebaran', 'icon' => null],
            ['date_code' => '04-02', 'title' => 'Cuti Bersama Lebaran', 'message' => 'Selamat menikmati cuti Lebaran', 'icon' => null],
            ['date_code' => '04-03', 'title' => 'Cuti Bersama Lebaran', 'message' => 'Selamat menikmati cuti Lebaran', 'icon' => null],
            ['date_code' => '04-04', 'title' => 'Cuti Bersama Lebaran', 'message' => 'Selamat menikmati cuti Lebaran', 'icon' => null],
            ['date_code' => '04-07', 'title' => 'Cuti Bersama Lebaran', 'message' => 'Selamat menikmati cuti Lebaran', 'icon' => null],
            ['date_code' => '04-18', 'title' => 'Jumat Agung', 'message' => 'Selamat memperingati Jumat Agung', 'icon' => null],
            ['date_code' => '04-20', 'title' => 'Paskah', 'message' => 'Selamat Hari Paskah', 'icon' => null],
            ['date_code' => '05-01', 'title' => 'Hari Buruh', 'message' => 'Selamat Hari Buruh!', 'icon' => null],
            ['date_code' => '05-12', 'title' => 'Hari Waisak', 'message' => 'Selamat Hari Waisak', 'icon' => null],
            ['date_code' => '05-13', 'title' => 'Cuti Bersama Waisak', 'message' => 'Selamat menikmati cuti bersama', 'icon' => null],
            ['date_code' => '05-29', 'title' => 'Kenaikan Isa Almasih', 'message' => 'Selamat memperingati Kenaikan Isa Almasih', 'icon' => null],
            ['date_code' => '05-30', 'title' => 'Cuti Bersama Kenaikan Isa Almasih', 'message' => 'Selamat menikmati cuti bersama', 'icon' => null],
            ['date_code' => '06-01', 'title' => 'Hari Lahir Pancasila', 'message' => 'Mari amalkan nilai-nilai luhur Pancasila', 'icon' => null],
            ['date_code' => '06-06', 'title' => 'Idul Adha', 'message' => 'Selamat Hari Raya Idul Adha', 'icon' => null],
            ['date_code' => '06-09', 'title' => 'Cuti Bersama Idul Adha', 'message' => 'Selamat menikmati cuti Idul Adha', 'icon' => null],
            ['date_code' => '06-27', 'title' => 'Tahun Baru Islam', 'message' => 'Selamat Tahun Baru Islam 1447 H', 'icon' => null],
            ['date_code' => '08-17', 'title' => 'Hari Kemerdekaan', 'message' => 'Dirgahayu Republik Indonesia! Merdeka!', 'icon' => null],
            ['date_code' => '09-05', 'title' => 'Maulid Nabi Muhammad SAW', 'message' => 'Peringatan Maulid Nabi Muhammad SAW', 'icon' => null],
            ['date_code' => '12-25', 'title' => 'Hari Natal', 'message' => 'Selamat Hari Natal', 'icon' => null],
            ['date_code' => '12-26', 'title' => 'Cuti Bersama Hari Natal', 'message' => 'Selamat menikmati cuti Natal', 'icon' => null],
        ];

        foreach ($data as $item) {
            NationalDay::create($item);
        }
    }
}
