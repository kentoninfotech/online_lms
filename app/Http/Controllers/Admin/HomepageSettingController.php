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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        // Only include fields that are in the request
        $data = [
            'section' => $section,
            'key' => $key,
            'is_active' => (bool) $request->input('is_active', 0)
        ];

        // Only include fields if they exist in the request
        if ($request->has('value')) {
            $data['value'] = $request->input('value');
        }
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
            $filename = 'homepage-' . $section . '-' . $key . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $filename);
            $data['image_path'] = 'uploads/courses/' . $filename;
        }

        HomepageSetting::updateOrCreate(
            ['section' => $section, 'key' => $key],
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
                $file->move(public_path('uploads/courses'), $filename);
                HomepageSetting::where('section', $section)
                    ->where('key', $key)
                    ->update(['image_path' => 'uploads/courses/' . $filename]);
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
     * Reinitialize homepage with defaults
     */
    public function initializeDefaults()
    {
        $defaults = [
            'hero' => [
                'title' => 'Master Your Future with Expert-Led Courses',
                'subtitle' => 'Access world-class training from top facilitators',
                'description' => 'Learn at your own pace, earn certificates, and advance your career with industry-relevant skills.',
                'button_text' => 'Explore Courses',
                'button_link' => '#featured-courses'
            ],
            'about' => [
                'title' => 'About LearnSmart Academy',
                'content' => 'LearnSmart Academy is a leading online learning platform...',
                'stat1_label' => 'Expert Instructors',
                'stat1_value' => '200+',
                'stat2_label' => 'Success Stories',
                'stat2_value' => '50K+',
                'stat3_label' => 'Years Experience',
                'stat3_value' => '15+'
            ],
            'cta' => [
                'title' => 'Ready to Transform Your Career?',
                'description' => 'Start learning today and join thousands of professionals who\'ve achieved their goals.',
                'button_text' => 'Sign Up Free',
                'button_link' => '/register'
            ],
            'footer' => [
                'company_name' => 'LearnSmart Academy',
                'company_description' => 'Leading online learning platform for professional development',
                'phone' => '+234 (0) 812 345 6789',
                'email' => 'contact@learnsmart.com',
                'address' => 'Lagos, Nigeria'
            ]
        ];

        foreach ($defaults as $section => $settings) {
            foreach ($settings as $key => $value) {
                HomepageSetting::setSetting($section, $key, $value);
            }
        }

        return redirect()->route('admin.homepage-settings.index')
            ->with('success', 'Homepage defaults initialized successfully');
    }
}
