<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageSetting;

class HomepageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // Hero Section
            [
                'section' => 'hero',
                'key' => 'title',
                'title' => 'Hero Section Title',
                'value' => 'Master Your Future with Expert-Led Courses',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'hero',
                'key' => 'subtitle',
                'title' => 'Hero Section Subtitle',
                'value' => 'Professional Learning Platform',
                'data_type' => 'text',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'section' => 'hero',
                'key' => 'description',
                'title' => 'Hero Description',
                'description' => 'The main description under the hero title',
                'value' => 'Access world-class training from top facilitators. Learn at your own pace, earn certificates, and advance your career with industry-relevant skills.',
                'data_type' => 'textarea',
                'sort_order' => 3,
                'is_active' => true
            ],

            // About Section
            [
                'section' => 'about',
                'key' => 'title',
                'title' => 'About Section Title',
                'value' => 'About LearnSmart Academy',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'about',
                'key' => 'content',
                'title' => 'About Content',
                'value' => 'LearnSmart Academy is a leading online learning platform dedicated to transforming careers through world-class education and professional development courses.',
                'data_type' => 'textarea',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'section' => 'about',
                'key' => 'stat1_label',
                'title' => 'Statistic 1 Label',
                'value' => 'Expert Instructors',
                'data_type' => 'text',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'section' => 'about',
                'key' => 'stat1_value',
                'title' => 'Statistic 1 Value',
                'value' => '200+',
                'data_type' => 'text',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'section' => 'about',
                'key' => 'stat2_label',
                'title' => 'Statistic 2 Label',
                'value' => 'Active Learners',
                'data_type' => 'text',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'section' => 'about',
                'key' => 'stat2_value',
                'title' => 'Statistic 2 Value',
                'value' => '50K+',
                'data_type' => 'text',
                'sort_order' => 6,
                'is_active' => true
            ],

            // Features Section
            [
                'section' => 'features',
                'key' => 'title',
                'title' => 'Features Section Title',
                'value' => 'Why Choose LearnSmart?',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'features',
                'key' => 'subtitle',
                'title' => 'Features Subtitle',
                'value' => 'Premium education with professional support',
                'data_type' => 'text',
                'sort_order' => 2,
                'is_active' => true
            ],

            // CTA Section
            [
                'section' => 'cta',
                'key' => 'title',
                'title' => 'CTA Section Title',
                'value' => 'Ready to Transform Your Career?',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'cta',
                'key' => 'description',
                'title' => 'CTA Description',
                'value' => 'Start learning today and join thousands of professionals who\'ve achieved their goals.',
                'data_type' => 'textarea',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'section' => 'cta',
                'key' => 'button_text',
                'title' => 'CTA Button Text',
                'button_text' => 'Sign Up Free',
                'value' => 'Sign Up Free',
                'data_type' => 'text',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'section' => 'cta',
                'key' => 'button_link',
                'title' => 'CTA Button Link',
                'button_link' => '/register',
                'value' => '/register',
                'data_type' => 'text',
                'sort_order' => 4,
                'is_active' => true
            ],

            // Contact Section
            [
                'section' => 'contact',
                'key' => 'title',
                'title' => 'Contact Section Title',
                'value' => 'Get in Touch',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'contact',
                'key' => 'description',
                'title' => 'Contact Description',
                'value' => 'Have questions? Our support team is always ready to help.',
                'data_type' => 'textarea',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'section' => 'contact',
                'key' => 'phone',
                'title' => 'Phone Number',
                'value' => '+234 (0) 812 345 6789',
                'data_type' => 'text',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'section' => 'contact',
                'key' => 'email',
                'title' => 'Email Address',
                'value' => 'contact@learnsmart.com',
                'data_type' => 'text',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'section' => 'contact',
                'key' => 'address',
                'title' => 'Physical Address',
                'value' => 'Lagos, Nigeria',
                'data_type' => 'text',
                'sort_order' => 5,
                'is_active' => true
            ],

            // Footer Section
            [
                'section' => 'footer',
                'key' => 'company_name',
                'title' => 'Company Name',
                'value' => 'LearnSmart Academy',
                'data_type' => 'text',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'section' => 'footer',
                'key' => 'company_description',
                'title' => 'Company Description',
                'value' => 'Leading online learning platform for professional development',
                'data_type' => 'textarea',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'section' => 'footer',
                'key' => 'copyright_year',
                'title' => 'Copyright Year',
                'value' => date('Y'),
                'data_type' => 'text',
                'sort_order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($defaults as $setting) {
            HomepageSetting::updateOrCreate(
                ['section' => $setting['section'], 'key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Homepage settings seeded successfully!');
    }
}
