<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteBuilderController extends Controller
{
    /**
     * Show site builder dashboard
     */
    public function index()
    {
        return view('admin.site-builder.index');
    }

    /**
     * Edit logos
     */
    public function editLogos()
    {
        $this->authorize('isAdmin');

        $logoSettings = [
            'logo_light' => HomepageSetting::getImagePath('branding', 'logo_light'),
            'logo_dark' => HomepageSetting::getImagePath('branding', 'logo_dark'),
            'favicon' => HomepageSetting::getImagePath('branding', 'favicon'),
            'site_name' => HomepageSetting::getSetting('branding', 'site_name'),
            'site_tagline' => HomepageSetting::getSetting('branding', 'site_tagline'),
            'logo_height' => HomepageSetting::getSetting('branding', 'logo_height'),
            'show_logo' => HomepageSetting::getSetting('branding', 'show_logo') ?? '1',
            'show_site_name' => HomepageSetting::getSetting('branding', 'show_site_name') ?? '1',
            'show_site_tagline' => HomepageSetting::getSetting('branding', 'show_site_tagline') ?? '1',
        ];

        return view('admin.site-builder.logos', compact('logoSettings'));
    }

    /**
     * Update logos
     */
    public function updateLogos(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'logo_height' => 'nullable|integer|min:20|max:200',
            'show_logo' => 'nullable|in:0,1',
            'show_site_name' => 'nullable|in:0,1',
            'show_site_tagline' => 'nullable|in:0,1',
            'logo_light' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'logo_dark' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        // Handle logo light upload
        if ($request->hasFile('logo_light')) {
            $file = $request->file('logo_light');
            $uploadDir = public_path('uploads/branding');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'logo-light-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'logo_light'],
                ['image_path' => 'uploads/branding/' . $filename, 'is_active' => true]
            );
        }

        // Handle logo dark upload
        if ($request->hasFile('logo_dark')) {
            $file = $request->file('logo_dark');
            $uploadDir = public_path('uploads/branding');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'logo-dark-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'logo_dark'],
                ['image_path' => 'uploads/branding/' . $filename, 'is_active' => true]
            );
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $uploadDir = public_path('uploads/branding');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'favicon-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'favicon'],
                ['image_path' => 'uploads/branding/' . $filename, 'is_active' => true]
            );
        }

        // Update text fields
        if ($validated['site_name']) {
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'site_name'],
                ['value' => $validated['site_name'], 'is_active' => true]
            );
        }

        if ($validated['site_tagline']) {
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'site_tagline'],
                ['value' => $validated['site_tagline'], 'is_active' => true]
            );
        }

        // Update logo height
        if ($validated['logo_height']) {
            HomepageSetting::updateOrCreate(
                ['section' => 'branding', 'key' => 'logo_height'],
                ['value' => $validated['logo_height'], 'is_active' => true]
            );
        }

        // Update visibility settings
        HomepageSetting::updateOrCreate(
            ['section' => 'branding', 'key' => 'show_logo'],
            ['value' => $request->has('show_logo') ? '1' : '0', 'is_active' => true]
        );

        HomepageSetting::updateOrCreate(
            ['section' => 'branding', 'key' => 'show_site_name'],
            ['value' => $request->has('show_site_name') ? '1' : '0', 'is_active' => true]
        );

        HomepageSetting::updateOrCreate(
            ['section' => 'branding', 'key' => 'show_site_tagline'],
            ['value' => $request->has('show_site_tagline') ? '1' : '0', 'is_active' => true]
        );

        return redirect()->route('admin.site-builder.logos')
            ->with('success', 'Logos and branding updated successfully!');
    }

    /**
     * Edit colors
     */
    public function editColors()
    {
        $this->authorize('isAdmin');

        $colorSettings = [
            'primary_color' => HomepageSetting::getSetting('branding', 'primary_color') ?? '#007bff',
            'secondary_color' => HomepageSetting::getSetting('branding', 'secondary_color') ?? '#6c757d',
            'success_color' => HomepageSetting::getSetting('branding', 'success_color') ?? '#28a745',
            'danger_color' => HomepageSetting::getSetting('branding', 'danger_color') ?? '#dc3545',
            'warning_color' => HomepageSetting::getSetting('branding', 'warning_color') ?? '#ffc107',
            'info_color' => HomepageSetting::getSetting('branding', 'info_color') ?? '#17a2b8',
            'background_color' => HomepageSetting::getSetting('branding', 'background_color') ?? '#ffffff',
            'text_color' => HomepageSetting::getSetting('branding', 'text_color') ?? '#333333',
        ];

        return view('admin.site-builder.colors', compact('colorSettings'));
    }

    /**
     * Update colors
     */
    public function updateColors(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'primary_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'secondary_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'success_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'danger_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'warning_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'info_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'background_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'text_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
        ]);

        foreach ($validated as $key => $color) {
            if ($color) {
                HomepageSetting::updateOrCreate(
                    ['section' => 'branding', 'key' => $key],
                    ['value' => $color, 'is_active' => true]
                );
            }
        }

        return redirect()->route('admin.site-builder.colors')
            ->with('success', 'Colors updated successfully!');
    }

    /**
     * Edit typography
     */
    public function editTypography()
    {
        $this->authorize('isAdmin');

        $typographySettings = [
            'primary_font' => HomepageSetting::getSetting('branding', 'primary_font') ?? 'Poppins',
            'secondary_font' => HomepageSetting::getSetting('branding', 'secondary_font') ?? 'Open Sans',
            'heading_size' => HomepageSetting::getSetting('branding', 'heading_size') ?? '32',
            'body_size' => HomepageSetting::getSetting('branding', 'body_size') ?? '14',
        ];

        $googleFonts = [
            'Poppins', 'Open Sans', 'Roboto', 'Lato', 'Montserrat', 'Inter', 'Ubuntu',
            'Playfair Display', 'Raleway', 'Source Sans Pro'
        ];

        return view('admin.site-builder.typography', compact('typographySettings', 'googleFonts'));
    }

    /**
     * Update typography
     */
    public function updateTypography(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'primary_font' => 'nullable|string|max:100',
            'secondary_font' => 'nullable|string|max:100',
            'heading_size' => 'nullable|numeric|min:16|max:72',
            'body_size' => 'nullable|numeric|min:12|max:24',
        ]);

        foreach ($validated as $key => $value) {
            if ($value) {
                HomepageSetting::updateOrCreate(
                    ['section' => 'branding', 'key' => $key],
                    ['value' => $value, 'is_active' => true]
                );
            }
        }

        return redirect()->route('admin.site-builder.typography')
            ->with('success', 'Typography settings updated successfully!');
    }

    /**
     * Edit page titles and content
     */
    public function editPageTitles()
    {
        $this->authorize('isAdmin');

        $pageSettings = [
            'landing_page_title' => HomepageSetting::getSetting('pages', 'landing_page_title') ?? 'Master Your Future with Expert-Led Courses',
            'landing_page_subtitle' => HomepageSetting::getSetting('pages', 'landing_page_subtitle') ?? 'Explore our most popular and highly-rated courses',
            'all_courses_page_title' => HomepageSetting::getSetting('pages', 'all_courses_page_title') ?? 'All Courses',
            'all_courses_page_subtitle' => HomepageSetting::getSetting('pages', 'all_courses_page_subtitle') ?? 'Explore our comprehensive catalog of courses',
        ];

        return view('admin.site-builder.page-titles', compact('pageSettings'));
    }

    /**
     * Update page titles and content
     */
    public function updatePageTitles(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'landing_page_title' => 'nullable|string|max:255',
            'landing_page_subtitle' => 'nullable|string|max:500',
            'all_courses_page_title' => 'nullable|string|max:255',
            'all_courses_page_subtitle' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $value) {
            if ($value) {
                HomepageSetting::updateOrCreate(
                    ['section' => 'pages', 'key' => $key],
                    ['value' => $value, 'is_active' => true]
                );
            }
        }

        return redirect()->route('admin.site-builder.page-titles')
            ->with('success', 'Page titles updated successfully!');
    }

    /**
     * Edit design & layout (main element and navbar styling)
     */
    public function editDesign()
    {
        $this->authorize('isAdmin');

        $designSettings = [
            // Main element
            'main_bg_color' => HomepageSetting::getSetting('design', 'main_bg_color') ?? '#ffffff',
            'main_bg_image' => HomepageSetting::getSetting('design', 'main_bg_image'),
            'main_bg_opacity' => HomepageSetting::getSetting('design', 'main_bg_opacity') ?? '100',
            // Navbar
            'navbar_bg_color' => HomepageSetting::getSetting('design', 'navbar_bg_color') ?? 'linear-gradient(135deg, #fff 0%, #f8f9fa 100%)',
            'navbar_text_color' => HomepageSetting::getSetting('design', 'navbar_text_color') ?? '#333',
            // First container
            'container_bg_color' => HomepageSetting::getSetting('design', 'container_bg_color') ?? '#f8f9fa',
        ];

        return view('admin.site-builder.design', compact('designSettings'));
    }

    /**
     * Update design & layout settings
     */
    public function updateDesign(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'main_bg_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'main_bg_image' => 'nullable|image|mimes:png,jpg,jpeg,gif,webp|max:5120',
            'main_bg_opacity' => 'nullable|numeric|min:0|max:100',
            'navbar_bg_color' => 'nullable|string|max:500',
            'navbar_text_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
            'container_bg_color' => 'nullable|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
        ]);

        // Handle main background image upload
        if ($request->hasFile('main_bg_image')) {
            $file = $request->file('main_bg_image');
            $uploadDir = public_path('uploads/branding');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = 'main-bg-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            HomepageSetting::updateOrCreate(
                ['section' => 'design', 'key' => 'main_bg_image'],
                ['value' => 'uploads/branding/' . $filename, 'is_active' => true]
            );
        }

        // Update color and text settings
        foreach (['main_bg_color', 'main_bg_opacity', 'navbar_bg_color', 'navbar_text_color', 'container_bg_color'] as $key) {
            if (isset($validated[$key])) {
                HomepageSetting::updateOrCreate(
                    ['section' => 'design', 'key' => $key],
                    ['value' => $validated[$key], 'is_active' => true]
                );
            }
        }

        return redirect()->route('admin.site-builder.design')
            ->with('success', 'Design and layout settings updated successfully!');
    }
}
