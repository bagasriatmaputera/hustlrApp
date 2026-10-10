<?php

namespace Database\Seeders;

use App\Models\Portfolio\PortfolioProject;
use Illuminate\Database\Seeder;

class PortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => ['id' => 'EyeCheck - Diagnosis Penyakit Mata', 'en' => 'EyeCheck - Eye Disease Diagnosis'],
                'description' => [
                    'id' => 'Aplikasi sistem pakar berbasis inferensi penyakit untuk membantu konsultasi dan identifikasi gejala penyakit mata secara dini.',
                    'en' => 'Web-based expert system application using disease inference reasoning to assist in consultation and early identification of eye disease symptoms.',
                ],
                'image' => '/images/projects/Eyecheck Preview.jpeg',
                'gif_image' => '/images/projects/Eyecheck Gif.gif',
                'tech_stack' => ['PHP', 'Laravel', 'MySQL', 'Tailwind CSS'],
                'live_url' => 'https://eyecheck.bagasr.my.id',
                'repo_url' => '',
                'featured' => true,
                'is_live' => true,
                'is_repo' => false,
                'order' => 1,
            ],
            [
                'title' => ['id' => 'Syaku Coffee', 'en' => 'Syaku Coffee'],
                'description' => [
                    'id' => 'Aplikasi katalog dan pemesanan online modern untuk Syaku Coffee dengan antarmuka yang responsif, visual interaktif, dan alur belanja yang mulus.',
                    'en' => 'Modern online catalog and ordering application for Syaku Coffee featuring a responsive interface, interactive visuals, and seamless shopping experience.',
                ],
                'image' => '/images/projects/Syaku App Preview.jpeg',
                'gif_image' => '/images/projects/Syaku App GIF.gif',
                'tech_stack' => ['React', 'JavaScript', 'Tailwind CSS', 'Vite'],
                'live_url' => 'https://syaku-coffe-demo.bagasr.my.id',
                'repo_url' => '',
                'featured' => true,
                'is_live' => true,
                'is_repo' => false,
                'order' => 2,
            ],
            [
                'title' => ['id' => 'GlowAttend - Sistem Presensi & Manajemen Karyawan', 'en' => 'GlowAttend - Attendance & Employee Management System'],
                'description' => [
                    'id' => 'Aplikasi absensi dan manajemen karyawan modern berbasis GPS geolokasi, multi-role dashboard (Owner, Admin, Karyawan), alur persetujuan cuti online, dan rekap kehadiran terintegrasi.',
                    'en' => 'Modern GPS geolocation-based attendance and employee management web application featuring multi-role access (Owner, Admin, Employee), online leave request approvals, and attendance reports.',
                ],
                'image' => '/images/projects/GlowAttend Preview.jpeg',
                'gif_image' => '/images/projects/GlowAttend GIF.gif',
                'tech_stack' => ['PHP', 'Laravel', 'Livewire', 'MySQL', 'Tailwind CSS'],
                'live_url' => 'https://glowattend-demo.bagasr.my.id',
                'repo_url' => 'https://github.com/bagasriatmaputera/glowAttendApp.git',
                'featured' => true,
                'is_live' => true,
                'is_repo' => true,
                'order' => 3,
            ],
        ];

        foreach ($projects as $data) {
            $titleId = $data['title']['id'];
            PortfolioProject::firstOrCreate(
                ['title->id' => $titleId],
                $data
            );
        }

        $this->command->info('Portfolio projects seeded successfully.');
    }
}
