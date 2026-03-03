<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommissionMembersSeeder extends Seeder
{
    /**
     * 10 Commission members for Dalolatnoma + 1 admin + 1 demo moderator.
     * Default password for all: commission123
     * They can also log in via E-IMZO.
     */
    public function run(): void
    {
        // Grab the first active district as the default assignment district
        $district = District::where('is_active', true)->first()
                 ?? District::first();

        // ─── Commission members for Dalolatnoma signing ───────────────────
        $members = [
            ['name' => "Hokim o'rinbosari (Qurilish)", 'email' => 'hokim.qurilish@tutash.uz', 'role' => 'commission', 'commission_position' => 'hokim_qurilish', 'pinfl' => '10000000000001'],
            ['name' => "Qurilish bo'limi xodimi",      'email' => 'qurilish@tutash.uz',       'role' => 'commission', 'commission_position' => 'qurilish',      'pinfl' => '10000000000002'],
            ['name' => "Ekologiya bo'limi xodimi",     'email' => 'ekologiya@tutash.uz',      'role' => 'commission', 'commission_position' => 'ekologiya',     'pinfl' => '10000000000003'],
            ['name' => "Obodonlashtirish bo'limi",     'email' => 'obodonlashtirish@tutash.uz','role' => 'commission', 'commission_position' => 'obodonlashtirish','pinfl' => '10000000000004'],
            ['name' => "Kadastr agentligi xodimi",    'email' => 'kadastr@tutash.uz',        'role' => 'commission', 'commission_position' => 'kadastr',        'pinfl' => '10000000000005'],
            ['name' => "FVV (ChS) bo'limi xodimi",    'email' => 'fvv@tutash.uz',            'role' => 'commission', 'commission_position' => 'fvv',            'pinfl' => '10000000000006'],
            ['name' => "Sanepidqo'mita (SES) xodimi", 'email' => 'ses@tutash.uz',            'role' => 'commission', 'commission_position' => 'ses',            'pinfl' => '10000000000007'],
            ['name' => "Soliq inspeksiyasi xodimi",   'email' => 'soliq@tutash.uz',          'role' => 'commission', 'commission_position' => 'soliq',          'pinfl' => '10000000000008'],
            ['name' => "IIB vakili",                  'email' => 'iib@tutash.uz',            'role' => 'commission', 'commission_position' => 'iib',            'pinfl' => '10000000000009'],
            ['name' => "Hokim yordamchisi",           'email' => 'yordamchi@tutash.uz',      'role' => 'commission', 'commission_position' => 'yordamchi',      'pinfl' => '10000000000010'],
        ];

        foreach ($members as $m) {
            User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name'                => $m['name'],
                    'pinfl'               => $m['pinfl'],
                    'password'            => Hash::make('commission123'),
                    'role'                => $m['role'],
                    'commission_position' => $m['commission_position'],
                    'district_id'         => $district?->id,
                ]
            );
        }

        // ─── Admin account ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@tutash.uz'],
            [
                'name'     => 'Administrator',
                'pinfl'    => '10000000000099',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'is_regional_backup' => true,
            ]
        );

        // ─── Workflow approval staff (regional backup — see all districts) ─
        $staff = [
            [
                'name'  => 'Demo Moderator',
                'email' => 'moderator@tutash.uz',
                'role'  => 'moderator',
                'pinfl' => '10000000000098',
                'pass'  => 'moderator123',
            ],
            [
                'name'  => 'Shikoyat Mutaxassisi',
                'email' => 'shikoyat@tutash.uz',
                'role'  => 'complaint_officer',
                'pinfl' => '10000000000097',
                'pass'  => 'staff123',
            ],
            [
                'name'  => 'Demo Yurist',
                'email' => 'yurist@tutash.uz',
                'role'  => 'lawyer',
                'pinfl' => '10000000000096',
                'pass'  => 'staff123',
            ],
            [
                'name'  => 'Demo Ijrochi',
                'email' => 'ijrochi@tutash.uz',
                'role'  => 'executor',
                'pinfl' => '10000000000095',
                'pass'  => 'staff123',
            ],
            [
                'name'  => 'Tuman Boshlig\'i',
                'email' => 'boshlik@tutash.uz',
                'role'  => 'district_head',
                'pinfl' => '10000000000094',
                'pass'  => 'staff123',
            ],
        ];

        foreach ($staff as $s) {
            User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name'               => $s['name'],
                    'pinfl'              => $s['pinfl'],
                    'password'           => Hash::make($s['pass']),
                    'role'               => $s['role'],
                    'district_id'        => $district?->id,
                    'is_regional_backup' => true,   // can see all districts
                ]
            );
        }

        $this->command->info('10 komissiya a\'zolari + admin + 5 workflow staff yaratildi.');
        $this->command->info('');
        $this->command->info('Kirish ma\'lumotlari:');
        $this->command->info('  admin@tutash.uz          / admin123       (IT Admin)');
        $this->command->info('  moderator@tutash.uz      / moderator123   (Moderator — barcha tumanlar)');
        $this->command->info('  shikoyat@tutash.uz       / staff123       (Shikoyat mutaxassisi)');
        $this->command->info('  yurist@tutash.uz         / staff123       (Yurist)');
        $this->command->info('  ijrochi@tutash.uz        / staff123       (Ijrochi)');
        $this->command->info('  boshlik@tutash.uz        / staff123       (Tuman boshlig\'i)');
        $this->command->info('  hokim.qurilish@tutash.uz / commission123  (Komissiya - Dalolatnoma)');
        $this->command->info('  qurilish@tutash.uz       / commission123');
        $this->command->info('  ekologiya@tutash.uz      / commission123');
        $this->command->info('  obodonlashtirish@tutash.uz / commission123');
        $this->command->info('  kadastr@tutash.uz        / commission123');
        $this->command->info('  fvv@tutash.uz            / commission123');
        $this->command->info('  ses@tutash.uz            / commission123');
        $this->command->info('  soliq@tutash.uz          / commission123');
        $this->command->info('  iib@tutash.uz            / commission123');
        $this->command->info('  yordamchi@tutash.uz      / commission123');
    }
}
