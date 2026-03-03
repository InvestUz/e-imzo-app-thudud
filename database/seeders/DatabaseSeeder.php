<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['name' => 'Chilonzor',        'name_uz' => 'Chilonzor tumani',        'code' => 'CHI', 'is_active' => true],
            ['name' => 'Olmazar',          'name_uz' => 'Olmazar tumani',          'code' => 'OLM', 'is_active' => true],
            ['name' => 'Shayxontohur',     'name_uz' => 'Shayxontohur tumani',     'code' => 'SHY', 'is_active' => true],
            ['name' => 'Yunusobod',        'name_uz' => 'Yunusobod tumani',        'code' => 'YUN', 'is_active' => true],
            ['name' => "Mirzo Ulug'bek",   'name_uz' => "Mirzo Ulug'bek tumani",   'code' => 'MIR', 'is_active' => true],
            ['name' => 'Mirobod',          'name_uz' => 'Mirobod tumani',          'code' => 'MIB', 'is_active' => true],
            ['name' => 'Yakkasaroy',       'name_uz' => 'Yakkasaroy tumani',       'code' => 'YAK', 'is_active' => true],
            ['name' => 'Uchtepa',          'name_uz' => 'Uchtepa tumani',          'code' => 'UCH', 'is_active' => true],
            ['name' => 'Yashnobod',        'name_uz' => 'Yashnobod tumani',        'code' => 'YSH', 'is_active' => true],
            ['name' => 'Sergeli',          'name_uz' => 'Sergeli tumani',          'code' => 'SER', 'is_active' => true],
            ['name' => 'Bektemir',         'name_uz' => 'Bektemir tumani',         'code' => 'BEK', 'is_active' => true],
            ['name' => 'Yangihayot',       'name_uz' => 'Yangihayot tumani',       'code' => 'YNG', 'is_active' => true],
        ];

        foreach ($districts as $d) {
            District::firstOrCreate(['code' => $d['code']], $d);
        }

        $this->command->info('12 Toshkent tumanlari yaratildi.');

        $this->call(CommissionMembersSeeder::class);
    }
}
