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
                'problem_solution' => [
                    'id' => 'Banyak masyarakat kesulitan melakukan konsultasi dini terkait gejala gangguan kesehatan mata karena terbatasnya akses ke spesialis. Proyek ini menghadirkan sistem pakar inferensi penyakit yang memandu pengguna melalui kuesioner gejala interaktif dan memberikan indikasi awal penyakit beserta saran penanganan pertama.',
                    'en' => 'Many individuals struggle to obtain early consultations for eye symptoms due to limited specialist access. This project provides an inference-based expert system that guides users through structured symptom questionnaires, delivering preliminary diagnoses and recommended next steps.',
                ],
                'challenges_learnings' => [
                    'id' => 'Tantangan utama adalah memetakan bobot gejala medis dan aturan inferensi pakar ke dalam representasi basis aturan yang konsisten, serta membangun UX yang mudah dipahami bagi pengguna awam saat menjawab pertanyaan gejala.',
                    'en' => 'The primary challenge was translating medical symptom weights and expert knowledge into consistent rule bases, while keeping the questionnaire UX clear and accessible for non-technical users.',
                ],
                'media_gallery' => [
                    [
                        'image' => '/images/projects/Eyecheck Preview.jpeg',
                        'caption' => [
                            'id' => 'Tampilan Utama & Skrining Gejala',
                            'en' => 'Main Screen & Symptom Screening',
                        ],
                    ],
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
                'problem_solution' => [
                    'id' => 'Pemesanan manual di kedai kopi sering mengalami antrean panjang dan kesalahan rekap pesanan. Syaku Coffee menyediakan katalog interaktif dengan visual responsif dan alur keranjang belanja yang cepat langsung dari perangkat pelanggan.',
                    'en' => 'Manual ordering at coffee shops often leads to long queues and order recording errors. Syaku Coffee provides an interactive catalog with responsive visuals and a frictionless cart checkout workflow.',
                ],
                'challenges_learnings' => [
                    'id' => 'Mengelola transisi status keranjang dan sinkronisasi preferensi menu minuman secara real-time di sisi client dengan performa rendering yang tetap ringan.',
                    'en' => 'Managing responsive cart state transitions and custom beverage options in real-time while maintaining lightweight frontend performance.',
                ],
                'media_gallery' => [
                    [
                        'image' => '/images/projects/Syaku App Preview.jpeg',
                        'caption' => [
                            'id' => 'Katalog & Menu Interaktif',
                            'en' => 'Interactive Menu & Catalog',
                        ],
                    ],
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
                'problem_solution' => [
                    'id' => 'Pencatatan presensi manual rentan manipulasi lokasi dan menyulitkan manajemen cuti karyawan. GlowAttend menyediakan sistem absensi berbasis geofencing GPS akurat dengan pembagian hak akses (Owner, Admin, Karyawan) dan approval cuti online.',
                    'en' => 'Manual attendance tracking is vulnerable to location manipulation and complicates leave request management. GlowAttend introduces accurate GPS geofenced check-ins with multi-role access and online leave request workflows.',
                ],
                'challenges_learnings' => [
                    'id' => 'Menangani validasi koordinat GPS dan toleransi akurasi radius perangkat mobile saat check-in, serta mengoptimalkan rekap kehadiran real-time tanpa membebani database.',
                    'en' => 'Handling GPS coordinate validation and geofence radius tolerances across varied mobile browsers while optimizing real-time attendance report queries.',
                ],
                'media_gallery' => [
                    [
                        'image' => '/images/projects/GlowAttend Preview.jpeg',
                        'caption' => [
                            'id' => 'Dasbor Monitoring Presensi',
                            'en' => 'Attendance Monitoring Dashboard',
                        ],
                    ],
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
            PortfolioProject::updateOrCreate(
                ['title->id' => $titleId],
                $data
            );
        }

        $this->command->info('Portfolio projects seeded successfully.');
    }
}
