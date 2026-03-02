<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;

class HomepageSettingController extends Controller
{
    /**
     * Display homepage settings dashboard
     */
    public function index()
    {
        $sections = HomepageSetting::getAllSections();
        $availableSections = [
            'hero' => 'Hero Section',
            'about' => 'About Us Section',
            'features' => 'Why Choose Us Features',
            'featured_courses' => 'Featured Courses Section',
            'testimonials' => 'Testimonials Section',
            'stats' => 'Statistics Section',
            'services' => 'Services Section',
            'galleries' => 'Galleries Section',
            'carousel' => 'Carousel Management',
            'cta' => 'Call-to-Action Section',
            'contact' => 'Contact Section',
            'footer' => 'Footer Section'
        ];

        return view('admin.homepage-settings.index', compact('sections', 'availableSections'));
    }

    /**
     * Edit a specific section
     */
    public function editSection($section)
    {
        $settings = HomepageSetting::getSection($section);
        
        $sectionLabels = [
            'hero' => 'Hero Section',
            'about' => 'About Us Section',
            'features' => 'Why Choose Us Features',
            'featured_courses' => 'Featured Courses Section',
            'testimonials' => 'Testimonials Section',
            'stats' => 'Statistics Section',
            'services' => 'Services Section',
            'galleries' => 'Galleries Section',
            'cta' => 'Call-to-Action Section',
            'contact' => 'Contact Section',
            'footer' => 'Footer Section'
        ];

        if (!isset($sectionLabels[$section])) {
            abort(404, 'Section not found');
        }

        return view('admin.homepage-settings.edit-section', [
            'section' => $section,
            'sectionLabel' => $sectionLabels[$section],
            'settings' => $settings
        ]);
    }

    /**
     * Update a specific setting
     */
    public function updateSetting(Request $request, $section, $key)
    {
        $request->validate([
            'value' => 'nullable|string',
            'textarea_value' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'field_key' => 'nullable|string|max:100',
            'field_type' => 'nullable|string|in:text,textarea,image'
        ]);

        // Allow using custom field key from the form
        $actualKey = $request->input('field_key') ?? $key;
        
        // Remove the 'manual_entry_' prefix if it exists
        if (str_starts_with($actualKey, 'manual_entry_')) {
            $actualKey = str_replace('manual_entry_' . time(), '', $actualKey);
        }

        // Only include fields that are in the request
        $data = [
            'section' => $section,
            'key' => $actualKey,
            'is_active' => (bool) $request->input('is_active', 0),
            'data_type' => $request->input('field_type') ?? 'text'
        ];

        // Handle different value sources
        if ($request->has('textarea_value') && !empty($request->input('textarea_value'))) {
            $data['value'] = $request->input('textarea_value');
        } elseif ($request->has('value')) {
            $data['value'] = $request->input('value');
        }

        // Include optional fields
        if ($request->has('button_text')) {
            $data['button_text'] = $request->input('button_text');
        }
        if ($request->has('button_link')) {
            $data['button_link'] = $request->input('button_link');
        }
        if ($request->has('title')) {
            $data['title'] = $request->input('title');
        }
        if ($request->has('description')) {
            $data['description'] = $request->input('description');
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'homepage-' . $section . '-' . $actualKey . '-' . time() . '.' . $file->getClientOriginalExtension();
            $data['image_path'] = 'storage/' . $file->storeAs('uploads/courses', $filename, 'public');
        }

        HomepageSetting::updateOrCreate(
            ['section' => $section, 'key' => $actualKey],
            $data
        );

        return redirect()->route('admin.homepage-settings.edit-section', $section)
            ->with('success', 'Setting updated successfully');
    }

    /**
     * Store multiple settings at once
     */
    public function updateSection(Request $request, $section)
    {
        $validatedData = $request->validate([
            'settings.*' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        foreach ($request->all() as $key => $value) {
            if ($key !== '_token' && $key !== '_method' && !str_starts_with($key, 'image_')) {
                if (is_array($value)) {
                    // Handle multiple values
                    foreach ($value as $subKey => $subValue) {
                        HomepageSetting::setSetting($section, $key . '_' . $subKey, $subValue);
                    }
                } else {
                    HomepageSetting::setSetting($section, $key, $value);
                }
            }

            // Handle image uploads
            if ($request->hasFile('image_' . $key)) {
                $file = $request->file('image_' . $key);
                $filename = 'homepage-' . $section . '-' . $key . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = 'storage/' . $file->storeAs('uploads/courses', $filename, 'public');
                HomepageSetting::where('section', $section)
                    ->where('key', $key)
                    ->update(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.homepage-settings.edit-section', $section)
            ->with('success', 'Section updated successfully');
    }

    /**
     * Delete a setting
     */
    public function destroy($section, $key)
    {
        HomepageSetting::where('section', $section)
            ->where('key', $key)
            ->delete();

        return redirect()->route('admin.homepage-settings.edit-section', $section)
            ->with('success', 'Setting deleted successfully');
    }

    /**
     * Reinitialize homepage with defaults for a specific section
     */
    public function initializeDefaults(Request $request)
    {
        $section = $request->input('section');

        $defaults = [
            'hero' => [
                'title' => 'Master Your Future with Expert-Led Courses',
                'description' => 'Learn at your own pace, earn certificates, and advance your career with industry-relevant skills.',
                'button_text' => 'Explore Courses',
                'button_link' => '#featured-courses',
                'stat1_value' => '50K+',
                'stat1_label' => 'Active Learners',
                'stat2_value' => '200+',
                'stat2_label' => 'Expert Courses',
                'stat3_value' => '95%',
                'stat3_label' => 'Satisfaction'
            ],
            'about' => [
                'title' => 'About Us',
                'content' => 'Our platform is dedicated to transforming careers through world-class education.',
                'content_2' => 'We have helped thousands of professionals advance their careers.',
                'stat1_value' => '200+',
                'stat1_label' => 'Expert Instructors',
                'stat2_value' => '50K+',
                'stat2_label' => 'Success Stories',
                'stat3_value' => '15+',
                'stat3_label' => 'Years Experience',
                'stat4_value' => '1M+',
                'stat4_label' => 'Certificates Issued'
            ],
            'features' => [
                'section_title' => 'Why Choose Us?',
                'section_subtitle' => 'Premium education with professional support',
                'feature1_icon' => '🎓',
                'feature1_title' => 'Expert Instructors',
                'feature1_desc' => 'Learn from industry professionals with 10+ years of experience',
                'feature2_icon' => '⏰',
                'feature2_title' => 'Learn Anytime',
                'feature2_desc' => 'Access courses 24/7 from anywhere at your own pace',
                'feature3_icon' => '🏆',
                'feature3_title' => 'Verified Certificates',
                'feature3_desc' => 'Earn industry-recognized certificates upon completion',
                'feature4_icon' => '👥',
                'feature4_title' => 'Community Support',
                'feature4_desc' => 'Connect with peers, ask questions, and grow together',
                'feature5_icon' => '💻',
                'feature5_title' => 'Interactive Content',
                'feature5_desc' => 'Videos, quizzes, projects, and live sessions',
                'feature6_icon' => '💰',
                'feature6_title' => 'Affordable Pricing',
                'feature6_desc' => 'Get premium education at competitive prices',
                'feature7_icon' => '🔐',
                'feature7_title' => 'Lifetime Access',
                'feature7_desc' => 'Access course materials forever after enrollment',
                'feature8_icon' => '📱',
                'feature8_title' => 'Mobile Friendly',
                'feature8_desc' => 'Learn on smartphone, tablet, or computer'
            ],
            'testimonials' => [
                'section_title' => 'What Our Students Say',
                'section_subtitle' => 'Join thousands of satisfied learners',
                'testimonial1_name' => 'Jane Doe',
                'testimonial1_title' => 'Software Engineer at TechCorp',
                'testimonial1_content' => 'This platform transformed my career. The courses are top-notch and the instructors are amazing!',
                'testimonial2_name' => 'John Smith',
                'testimonial2_title' => 'Data Analyst at DataWorks',
                'testimonial2_content' => 'I landed my dream job thanks to the skills I learned here. Highly recommend to anyone looking to upskill.',
                'testimonial3_name' => 'Emily Johnson',
                'testimonial3_title' => 'UX Designer at CreativeStudio',
                'testimonial3_content' => 'The community support is fantastic. I was able to connect with other learners and get help when needed.',
            ],
            'stats' => [
                'stat1_value' => '50K+',
                'stat1_label' => 'Active Learners',
                'stat2_value' => '200+',
                'stat2_label' => 'Expert Courses',
                'stat3_value' => '95%',
                'stat3_label' => 'Satisfaction Rate',
                'stat4_value' => '1M+',
                'stat4_label' => 'Certificates Issued'
            ],
            'cta' => [
                'title' => 'Ready to Transform Your Career?',
                'description' => 'Start learning today and join thousands of professionals who\'ve achieved their goals.',
                'button_text' => 'Sign Up Free',
                'button_link' => '/register'
            ],
            'contact' => [
                'title' => 'Get in Touch',
                'subtitle' => 'Have questions? Our support team is always ready to help.',
                'email_icon' => '📧',
                'email_label' => 'Email',
                'email_value' => 'info@coinmac.org',
                'phone_icon' => '📞',
                'phone_label' => 'Phone',
                'phone_value' => '+234 (0) 806 563 2882',
                'address_icon' => '📍',
                'address_label' => 'Address',
                'address_value' => 'Abuja, Nigeria',
                'hours_icon' => '⏰',
                'hours_label' => 'Support Hours',
                'hours_value' => 'Monday - Friday: 9am - 6pm',
                'whatsapp_link' => 'https://wa.me/2348065632882',
                'form_title' => 'Send us a Message',
                'form_name_label' => 'Full Name',
                'form_email_label' => 'Email',
                'form_phone_label' => 'Phone (Optional)',
                'form_subject_label' => 'Subject (Optional)',
                'form_message_label' => 'Message',
                'form_submit_text' => 'Send Message'
            ]
        ];

        // If specific section requested, only initialize that section
        if ($section && isset($defaults[$section])) {
            foreach ($defaults[$section] as $key => $value) {
                HomepageSetting::updateOrCreate(
                    ['section' => $section, 'key' => $key],
                    [
                        'section' => $section,
                        'key' => $key,
                        'value' => $value,
                        'is_active' => true,
                        'data_type' => 'text'
                    ]
                );
            }
            return redirect()->route('admin.homepage-settings.edit-section', $section)
                ->with('success', "Default fields for {$section} section initialized successfully");
        }

        // Otherwise initialize all defaults
        foreach ($defaults as $sectionName => $fields) {
            foreach ($fields as $key => $value) {
                HomepageSetting::updateOrCreate(
                    ['section' => $sectionName, 'key' => $key],
                    [
                        'section' => $sectionName,
                        'key' => $key,
                        'value' => $value,
                        'is_active' => true,
                        'data_type' => 'text'
                    ]
                );
            }
        }

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'All default homepage settings initialized successfully');
    }
}
