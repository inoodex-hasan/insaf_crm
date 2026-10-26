@extends('admin.layouts.master')

@section('title', 'Application Settings')

@section('content')
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Application Settings</h5>
        </div>
        <div class="mb-5">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-5" x-data="{ activeTab: 'general' }">
                    <ul class="flex flex-wrap border-b border-gray-200 dark:border-gray-700">
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'general'"
                                :class="{ 'text-primary border-primary': activeTab === 'general', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'general' }"
                                class="inline-block p-4 border-b-2 rounded-t-lg">General</a>
                        </li>
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'contact'"
                                :class="{ 'text-primary border-primary': activeTab === 'contact', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'contact' }"
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">Contact</a>
                        </li>
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'pdf_templates'"
                                :class="{ 'text-primary border-primary': activeTab === 'pdf_templates', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'pdf_templates' }"
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">PDF Backgrounds</a>
                        </li>
                        {{-- <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'social'"
                                :class="{ 'text-primary border-primary': activeTab === 'social', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'social' }"
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">Social Media</a>
                        </li>  --}}
                        {{-- <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'system'"
                                :class="{ 'text-primary border-primary': activeTab === 'system', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'system' }"
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">System</a>
                        </li>
                        <li class="mr-2">
                            <a href="#" @click.prevent="activeTab = 'seo'"
                                :class="{ 'text-primary border-primary': activeTab === 'seo', 'text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'seo' }"
                                class="inline-block p-4 border-b-2 border-transparent rounded-t-lg">SEO</a>
                        </li> --}}
                    </ul>

                    <div class="mt-5">
                        <!-- General Tab -->
                        <div x-show="activeTab === 'general'">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="app_name">Application Name</label>
                                    <input id="app_name" type="text" name="app_name"
                                        value="{{ $settings['app_name'] ?? config('app.name') }}" class="form-input" />
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="app_logo">Application Logo</label>
                                    <input id="app_logo" type="file" name="app_logo"
                                        class="form-input file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90" />
                                    @if (isset($settings['app_logo']))
                                        <div class="mt-2">
                                            <img src="{{ Illuminate\Support\Str::startsWith($settings['app_logo'], 'http') ? $settings['app_logo'] : asset('storage/' . $settings['app_logo']) }}"
                                                alt="Logo" class="h-20 w-auto" />
                                        </div>
                                    @else
                                        <div class="mt-2 text-xs text-gray-500">Current: Default Logo</div>
                                    @endif
                                </div>
                                <div>
                                    <label for="app_favicon">Application Favicon</label>
                                    <input id="app_favicon" type="file" name="app_favicon"
                                        class="form-input file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90" />
                                    @if (isset($settings['app_favicon']))
                                        <div class="mt-2">
                                            <img src="{{ Illuminate\Support\Str::startsWith($settings['app_favicon'], 'http') ? $settings['app_favicon'] : asset('storage/' . $settings['app_favicon']) }}"
                                                alt="Favicon" class="h-10 w-auto" />
                                        </div>
                                    @else
                                        <div class="mt-2 text-xs text-gray-500">Current: Default Favicon</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div x-show="activeTab === 'contact'" style="display: none;">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_email">Contact Email</label>
                                    <input id="contact_email" type="email" name="contact_email"
                                        value="{{ $settings['contact_email'] ?? '' }}" class="form-input" />
                                </div>
                                <div>
                                    <label for="contact_phone">Contact Phone</label>
                                    <input id="contact_phone" type="text" name="contact_phone"
                                        value="{{ $settings['contact_phone'] ?? '' }}" class="form-input" />
                                </div>
                                <div class="col-span-2">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" rows="3" class="form-input">{{ $settings['address'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Backgrounds Tab -->
                        <div x-show="activeTab === 'pdf_templates'" style="display: none;">
                            <div style="background-color: rgba(67, 97, 238, 0.08); border-left: 4px solid #4361ee; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px;">
                                <p style="margin: 0; font-size: 13px; color: #4361ee; font-weight: 500;">
                                    <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                                    <strong>Recommended format:</strong> A4 portrait orientation (210mm &times; 297mm or 2480 &times; 3508 px at 300 DPI). Supported formats: JPG, PNG, WebP (Max 10MB).
                                </p>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 24px;">
                                <!-- 1. Invoice Background Card -->
                                <div style="border: 1px solid #e0e6ed; border-radius: 8px; padding: 20px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="dark:bg-[#1b2e4b] dark:border-gray-700">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid #f1f2f3;">
                                        <div>
                                            <h6 style="font-size: 16px; font-weight: 700; color: #4361ee; margin: 0;">Invoice Background</h6>
                                            <span style="font-size: 12px; color: #888ea8;">Student Invoices &amp; Payment Receipts</span>
                                        </div>
                                        @if(!empty($settings['invoice_bg_image']))
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7;">Custom Active</span>
                                        @else
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #f4f5f8; color: #515365; border: 1px solid #e0e6ed;">Default Template</span>
                                        @endif
                                    </div>

                                    <div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 16px;">
                                        <!-- Thumbnail Column (Fixed Width 140px, A4 Aspect Ratio) -->
                                        <div style="width: 140px; min-width: 140px; max-width: 140px; flex-shrink: 0;">
                                            @php $invBg = get_invoice_bg_url(); @endphp
                                            @if($invBg)
                                                <div style="border: 1px solid #e0e6ed; border-radius: 6px; overflow: hidden; background: #fafafa; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
                                                    <img src="{{ $invBg }}" alt="Invoice BG Preview" style="width: 100%; height: 198px; object-fit: cover; object-position: top; display: block;" />
                                                    <a href="{{ $invBg }}" target="_blank" style="display: block; text-align: center; padding: 5px; background: #3b3f5c; color: #fff; font-size: 11px; text-decoration: none; font-weight: 500;">
                                                        <i class="fas fa-external-link-alt" style="font-size: 10px; margin-right: 3px;"></i> View Full
                                                    </a>
                                                </div>
                                            @else
                                                <div style="width: 140px; height: 198px; border: 2px dashed #e0e6ed; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #888ea8; background: #fafafa;">
                                                    No Preview
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Controls Column -->
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="margin-bottom: 12px;">
                                                <label for="invoice_bg_image" style="display: block; font-size: 12px; font-weight: 600; color: #3b3f5c; margin-bottom: 6px;" class="dark:text-white-light">
                                                    Upload New Background
                                                </label>
                                                <input id="invoice_bg_image" type="file" name="invoice_bg_image" accept="image/jpeg,image/png,image/webp"
                                                    class="form-input" style="font-size: 12px; padding: 6px 10px;" />
                                            </div>

                                            @if(!empty($settings['invoice_bg_image']))
                                                <div style="margin-bottom: 12px; padding: 6px 10px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 4px;">
                                                    <label style="display: flex; align-items: center; font-size: 12px; color: #e53e3e; cursor: pointer; margin: 0;">
                                                        <input type="checkbox" name="reset_invoice_bg" value="1" style="margin-right: 6px;" />
                                                        <span>Reset to system default background</span>
                                                    </label>
                                                </div>
                                            @endif

                                            <div style="font-size: 11px; color: #888ea8; line-height: 1.6; background: #f9fafb; padding: 8px 10px; border-radius: 4px; border: 1px solid #f1f2f3;" class="dark:bg-[#0e1726] dark:border-gray-800">
                                                <div style="font-weight: 600; color: #515365; margin-bottom: 2px;" class="dark:text-gray-400">Used for:</div>
                                                <div>&bull; Student Fee Invoices</div>
                                                <div>&bull; Payment Collection Receipts</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Report Background Card -->
                                <div style="border: 1px solid #e0e6ed; border-radius: 8px; padding: 20px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="dark:bg-[#1b2e4b] dark:border-gray-700">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid #f1f2f3;">
                                        <div>
                                            <h6 style="font-size: 16px; font-weight: 700; color: #4361ee; margin: 0;">Report Background</h6>
                                            <span style="font-size: 12px; color: #888ea8;">Financial Reports &amp; Office Statements</span>
                                        </div>
                                        @if(!empty($settings['report_bg_image']))
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7;">Custom Active</span>
                                        @else
                                            <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #f4f5f8; color: #515365; border: 1px solid #e0e6ed;">Default Template</span>
                                        @endif
                                    </div>

                                    <div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 16px;">
                                        <!-- Thumbnail Column (Fixed Width 140px, A4 Aspect Ratio) -->
                                        <div style="width: 140px; min-width: 140px; max-width: 140px; flex-shrink: 0;">
                                            @php $repBg = get_report_bg_url(); @endphp
                                            @if($repBg)
                                                <div style="border: 1px solid #e0e6ed; border-radius: 6px; overflow: hidden; background: #fafafa; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
                                                    <img src="{{ $repBg }}" alt="Report BG Preview" style="width: 100%; height: 198px; object-fit: cover; object-position: top; display: block;" />
                                                    <a href="{{ $repBg }}" target="_blank" style="display: block; text-align: center; padding: 5px; background: #3b3f5c; color: #fff; font-size: 11px; text-decoration: none; font-weight: 500;">
                                                        <i class="fas fa-external-link-alt" style="font-size: 10px; margin-right: 3px;"></i> View Full
                                                    </a>
                                                </div>
                                            @else
                                                <div style="width: 140px; height: 198px; border: 2px dashed #e0e6ed; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #888ea8; background: #fafafa;">
                                                    No Preview
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Controls Column -->
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="margin-bottom: 12px;">
                                                <label for="report_bg_image" style="display: block; font-size: 12px; font-weight: 600; color: #3b3f5c; margin-bottom: 6px;" class="dark:text-white-light">
                                                    Upload New Background
                                                </label>
                                                <input id="report_bg_image" type="file" name="report_bg_image" accept="image/jpeg,image/png,image/webp"
                                                    class="form-input" style="font-size: 12px; padding: 6px 10px;" />
                                            </div>

                                            @if(!empty($settings['report_bg_image']))
                                                <div style="margin-bottom: 12px; padding: 6px 10px; background: #fff5f5; border: 1px solid #fed7d7; border-radius: 4px;">
                                                    <label style="display: flex; align-items: center; font-size: 12px; color: #e53e3e; cursor: pointer; margin: 0;">
                                                        <input type="checkbox" name="reset_report_bg" value="1" style="margin-right: 6px;" />
                                                        <span>Reset to system default background</span>
                                                    </label>
                                                </div>
                                            @endif

                                            <div style="font-size: 11px; color: #888ea8; line-height: 1.6; background: #f9fafb; padding: 8px 10px; border-radius: 4px; border: 1px solid #f1f2f3;" class="dark:bg-[#0e1726] dark:border-gray-800">
                                                <div style="font-weight: 600; color: #515365; margin-bottom: 2px;" class="dark:text-gray-400">Used for:</div>
                                                <div>&bull; Balance Sheet &amp; Financial Summary</div>
                                                <div>&bull; Expenses, Payments &amp; Journal Reports</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Tab -->
                        {{-- <div x-show="activeTab === 'social'" style="display: none;">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="social_facebook">Facebook URL</label>
                                    <input id="social_facebook" type="url" name="social_facebook"
                                        value="{{ $settings['social_facebook'] ?? '' }}" class="form-input"
                                        placeholder="https://facebook.com/..." />
                                </div>
                                <div>
                                    <label for="social_twitter">Twitter URL</label>
                                    <input id="social_twitter" type="url" name="social_twitter"
                                        value="{{ $settings['social_twitter'] ?? '' }}" class="form-input"
                                        placeholder="https://twitter.com/..." />
                                </div>
                                <div>
                                    <label for="social_linkedin">LinkedIn URL</label>
                                    <input id="social_linkedin" type="url" name="social_linkedin"
                                        value="{{ $settings['social_linkedin'] ?? '' }}" class="form-input"
                                        placeholder="https://linkedin.com/..." />
                                </div>
                            </div>
                        </div> --}}

                        <!-- System Tab -->
                        {{-- <div x-show="activeTab === 'system'" style="display: none;">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="currency_symbol">Currency Symbol</label>
                                    <input id="currency_symbol" type="text" name="currency_symbol"
                                        value="{{ $settings['currency_symbol'] ?? '$' }}" class="form-input" />
                                </div>
                                <div>
                                    <label for="date_format">Date Format</label>
                                    <select id="date_format" name="date_format" class="form-select">
                                        <option value="d/m/Y"
                                            {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>d/m/Y
                                            (31/12/2026)</option>
                                        <option value="Y-m-d"
                                            {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>Y-m-d
                                            (2026-12-31)</option>
                                        <option value="m/d/Y"
                                            {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>m/d/Y
                                            (12/31/2026)</option>
                                        <option value="d-M-Y"
                                            {{ ($settings['date_format'] ?? '') == 'd-M-Y' ? 'selected' : '' }}>d-M-Y
                                            (31-Dec-2026)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_registration" value="1"
                                            class="form-checkbox"
                                            {{ ($settings['enable_registration'] ?? '0') == '1' ? 'checked' : '' }} />
                                        <span class="ml-2">Enable User Registration</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="maintenance_mode" value="1"
                                            class="form-checkbox"
                                            {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }} />
                                        <span class="ml-2">Maintenance Mode</span>
                                    </label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <div class="mt-8">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
