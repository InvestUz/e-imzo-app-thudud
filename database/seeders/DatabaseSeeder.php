<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user (will be matched by PINFL from E-IMZO certificate)
        $user = User::create([
            'name' => 'Test Foydalanuvchi',
            'pinfl' => '12345678901234',
            'inn' => '123456789',
            'organization' => 'Test Tashkilot',
            'position' => 'Direktor',
            'certificate_valid_from' => now(),
            'certificate_valid_to' => now()->addYear(),
        ]);

        // Create sample documents
        Document::create([
            'title' => 'Shartnoma №1',
            'content' => "SHARTNOMA №1\n\nToshkent shahri\n" . now()->format('d.m.Y') . "\n\nUshbu shartnoma quyidagi tomonlar o'rtasida tuzildi:\n\n1. \"Test Tashkilot\" MChJ (keyingi o'rinlarda \"Buyurtmachi\" deb yuritiladi)\n\n2. Ijrochi tomon\n\nShartnoma predmeti:\nBuyurtmachi Ijrochiga dasturiy ta'minot ishlab chiqish bo'yicha xizmatlar ko'rsatishni topshiradi.\n\nShartnoma shartlari:\n- Ish muddati: 30 kun\n- To'lov summasi: 10,000,000 so'm\n\nTomonlar rekvizitlari:\n...",
            'user_id' => $user->id,
        ]);

        Document::create([
            'title' => 'Buyruq №15',
            'content' => "BUYRUQ №15\n\n" . now()->format('d.m.Y') . "\n\nTest Tashkilot MChJ direktori\n\nBUYURDIM:\n\n1. 2024-yil 1-mart kunidan boshlab yangi loyihani boshlash.\n\n2. Loyiha rahbari etib Abdullayev A.A.ni tayinlansin.\n\n3. Loyiha uchun 50,000,000 so'm mablag' ajratilsin.\n\nAsos: Direktorlar kengashi qarori.\n\nDirektor: _______________",
            'user_id' => $user->id,
        ]);

        Document::create([
            'title' => 'Hisobot - 2024 yil 1-chorak',
            'content' => "MOLIYAVIY HISOBOT\n2024-yil 1-chorak\n\nTest Tashkilot MChJ\n\n1. DAROMADLAR\n- Asosiy faoliyatdan: 150,000,000 so'm\n- Boshqa daromadlar: 5,000,000 so'm\nJAMI DAROMAD: 155,000,000 so'm\n\n2. XARAJATLAR\n- Ish haqi: 50,000,000 so'm\n- Ijara: 10,000,000 so'm\n- Kommunal xizmatlar: 5,000,000 so'm\n- Boshqa xarajatlar: 15,000,000 so'm\nJAMI XARAJAT: 80,000,000 so'm\n\n3. SOF FOYDA: 75,000,000 so'm\n\nHisobotni tayyorladi: Bosh hisobchi",
            'user_id' => $user->id,
        ]);

        $this->command->info('Seeding completed!');
        $this->command->info('Demo user PINFL: 12345678901234');
        $this->command->info('Note: Real authentication requires E-IMZO certificate with matching PINFL');
    }
}
