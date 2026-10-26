<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}

if (!function_exists('get_invoice_bg_src')) {
    /**
     * Get the file path or URI suitable for mPDF rendering of invoice backgrounds.
     */
    function get_invoice_bg_src(): ?string
    {
        $custom = get_setting('invoice_bg_image');
        if ($custom && file_exists(storage_path('app/public/' . $custom))) {
            $path = storage_path('app/public/' . $custom);
        } elseif ($custom && file_exists(public_path('storage/' . $custom))) {
            $path = public_path('storage/' . $custom);
        } elseif (file_exists(public_path('assets/images/Invoice_Insaf-signature.jpg'))) {
            $path = public_path('assets/images/Invoice_Insaf-signature.jpg');
        } elseif (file_exists(public_path('assets/images/Invoice_Insaf.jpeg'))) {
            $path = public_path('assets/images/Invoice_Insaf.jpeg');
        } else {
            return null;
        }

        return 'file:///' . str_replace('\\', '/', $path);
    }
}

if (!function_exists('get_report_bg_src')) {
    /**
     * Get the file path or URI suitable for mPDF rendering of report backgrounds.
     */
    function get_report_bg_src(): ?string
    {
        $custom = get_setting('report_bg_image');
        if ($custom && file_exists(storage_path('app/public/' . $custom))) {
            $path = storage_path('app/public/' . $custom);
        } elseif ($custom && file_exists(public_path('storage/' . $custom))) {
            $path = public_path('storage/' . $custom);
        } elseif (file_exists(public_path('assets/images/Invoice_Insaf_02.jpg'))) {
            $path = public_path('assets/images/Invoice_Insaf_02.jpg');
        } else {
            return null;
        }

        return 'file:///' . str_replace('\\', '/', $path);
    }
}

if (!function_exists('get_invoice_bg_url')) {
    /**
     * Get the web URL for invoice background preview in browser.
     */
    function get_invoice_bg_url(): ?string
    {
        $custom = get_setting('invoice_bg_image');
        if ($custom && Storage::disk('public')->exists($custom)) {
            return asset('storage/' . $custom);
        }
        if (file_exists(public_path('assets/images/Invoice_Insaf-signature.jpg'))) {
            return asset('assets/images/Invoice_Insaf-signature.jpg');
        }
        if (file_exists(public_path('assets/images/Invoice_Insaf.jpeg'))) {
            return asset('assets/images/Invoice_Insaf.jpeg');
        }
        return null;
    }
}

if (!function_exists('get_report_bg_url')) {
    /**
     * Get the web URL for report background preview in browser.
     */
    function get_report_bg_url(): ?string
    {
        $custom = get_setting('report_bg_image');
        if ($custom && Storage::disk('public')->exists($custom)) {
            return asset('storage/' . $custom);
        }
        if (file_exists(public_path('assets/images/Invoice_Insaf_02.jpg'))) {
            return asset('assets/images/Invoice_Insaf_02.jpg');
        }
        return null;
    }
}
