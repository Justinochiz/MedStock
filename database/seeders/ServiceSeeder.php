<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Surgery',
                'description' => 'Professional surgical services performed by experienced surgeons with state-of-the-art equipment. Comprehensive pre and post-operative care included.',
                'type' => 'Surgical',
                'price' => 2500.00,
                'img_path' => 'images/surgery1.jpg',
            ],
            [
                'name' => 'Infusion Therapy',
                'description' => 'Specialized intravenous infusion therapy administered by certified nurses. Includes fluid replacement, medication delivery, and nutrition support.',
                'type' => 'Therapy',
                'price' => 350.00,
                'img_path' => 'images/Infusion Therapy.jpg',
            ],
            [
                'name' => 'Post-Operative Care',
                'description' => 'Comprehensive post-operative care and monitoring. Recovery support, wound care, pain management, and follow-up assessments included.',
                'type' => 'Care',
                'price' => 700.00,
                'img_path' => 'images/Post-Operative Care.jpg',
            ],
            [
                'name' => 'AED/CPR Training Class',
                'description' => 'Certified training in CPR and Automated External Defibrillator (AED) usage. Hands-on practice and certification provided for groups and individuals.',
                'type' => 'Training',
                'price' => 100.00,
                'img_path' => 'images/CPR Training Class.jpg',
            ],
            [
                'name' => 'Blood Pressure Screening',
                'description' => 'Quick and accurate hypertension screening. Professional blood pressure measurement and health assessment with recommendations.',
                'type' => 'Screening',
                'price' => 25.00,
                'img_path' => 'images/Blood Pressure Screening.jpg',
            ],
            [
                'name' => 'Mobile EKG Services',
                'description' => 'On-site electrocardiogram services for patients. Professional-grade EKG equipment with trained technicians for accurate cardiac monitoring.',
                'type' => 'Diagnostic',
                'price' => 275.00,
                'img_path' => 'images/Mobile EKG Services.jpg',
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );
        }
    }
}
