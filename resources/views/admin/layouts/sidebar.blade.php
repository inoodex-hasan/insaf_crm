<style>
    #sidebar-scroll::-webkit-scrollbar {
        width: 6px;
    }
    #sidebar-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    #sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #9ca3af;
        border-radius: 3px;
    }
    #sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
    }
    .dark #sidebar-scroll::-webkit-scrollbar-track {
        background: #1b2e4b;
    }
    .dark #sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }
    .dark #sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
    }
    #sidebar-scroll {
        overscroll-behavior: contain;
    }
</style>
<nav class="sidebar fixed bottom-0 top-0 z-50 h-full min-h-[100vh] w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300 ltr:left-0 rtl:right-0 dark:bg-[#0e1726] dark:text-white-dark"
    :class="$store.app.sidebar ? 'translate-x-0' : '-translate-x-full ltr:ml-[-260px] rtl:mr-[-260px]'">
    <div class="flex h-full flex-col bg-white dark:bg-[#0e1726]">
        <div class="sidebar-header flex items-center justify-between px-4 py-3">
            <a href="{{ route('tyro-dashboard.index') }}" class="main-logo flex shrink-0 items-center">
                <img class="ml-[5px] w-8 flex-none" src="{{ get_setting('app_logo') ? asset('storage/' . get_setting('app_logo')) : asset('assets/images/logo.svg') }}" alt="Logo" />
                <span class="align-middle text-2xl font-semibold ltr:ml-1.5 rtl:mr-1.5 dark:text-white-light lg:inline">Dashboard</span>
            </a>

            <!-- Desktop Pin / Stick Toggle Button -->
            <button type="button"
                    class="sidebar-pin-btn hidden lg:flex h-8 w-8 items-center justify-center rounded-full transition-all duration-200 hover:bg-gray-500/10 dark:text-white-light dark:hover:bg-dark-light/10 shrink-0"
                    :class="$store.app.sidebarPinned ? 'text-primary' : 'text-gray-400 hover:text-primary'"
                    @click="$store.app.toggleSidebarPin()"
                    :title="$store.app.sidebarPinned ? 'Unpin Sidebar (Mini Hover Mode)' : 'Pin Sidebar (Stick Full)'">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                     class="transition-transform duration-300"
                     :class="$store.app.sidebarPinned ? 'rotate-0' : '-rotate-45 opacity-70'">
                    <line x1="12" y1="17" x2="12" y2="22"></line>
                    <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a1 1 0 0 0 0-2H8a1 1 0 0 0 0 2h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V17z" :fill="$store.app.sidebarPinned ? 'currentColor' : 'none'"></path>
                </svg>
            </button>

            <!-- Mobile Close Button -->
            <button type="button" class="collapse-icon flex h-8 w-8 items-center rounded-full transition duration-300 hover:bg-gray-500/10 rtl:rotate-180 dark:text-white-light dark:hover:bg-dark-light/10 lg:hidden" @click="$store.app.toggleSidebar()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="m-auto h-5 w-5">
                    <path d="M11 17L6 12L11 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path opacity="0.5" d="M21 12H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>

        <div class="relative flex-1 overflow-y-auto scroll-smooth perfect-scrollbar" id="sidebar-scroll" style="scrollbar-width: thin; scrollbar-color: #9ca3af #f1f1f1;">
            <ul class="space-y-0.5 p-3 font-semibold pb-20">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('tyro-dashboard.index') }}" class="group">
                        <div class="flex items-center">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z" fill="currentColor" />
                                <path d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z" fill="currentColor" />
                            </svg>
                            <span class="px-3">Dashboard</span>
                        </div>
                    </a>
                </li>

                <!-- Editor -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor" stroke-width="1.5" fill="none" />
                                    <path d="M14.06 4.94l3.75 3.75" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <span class="px-3">Editor</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Countries</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.countries.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Add Country</a></li>
                                <li><a href="{{ route('admin.countries.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Country List</a></li>
                                <li><a href="{{ route('admin.country-remarks.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b] {{ request()->routeIs('admin.country-remarks.*') ? 'font-semibold text-primary' : '' }}">Country Remarks</a></li>
                            </ul>
                        </li>
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Universities</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.universities.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Add University</a></li>
                                <li><a href="{{ route('admin.universities.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">University List</a></li>
                            </ul>
                        </li>
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Courses</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.search-program.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b] text-primary font-semibold">Search Programs</a></li>
                                <li><a href="{{ route('admin.courses.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Add Course</a></li>
                                <li><a href="{{ route('admin.courses.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Course List</a></li>
                                <li><a href="{{ route('admin.program-settings.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Program Settings</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>



                <!-- Data Collection -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="12" cy="5" rx="9" ry="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M21 12C21 13.6569 16.9706 15 12 15C7.02944 15 3 13.6569 3 12" stroke="currentColor" stroke-width="2"/>
                                    <path d="M21 5V19C21 20.6569 16.9706 22 12 22C7.02944 22 3 20.6569 3 19V5" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span class="px-3">Data Collection</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.marketing.leads.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Data Submit</a></li>
                        <li><a href="{{ route('admin.marketing.leads.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Primary List</a></li>
                    </ul>
                </li>

                <!-- Digital Marketing -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 11V13M5 10L18 6V18L5 14V10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5 14L6 19C6.2 20 7 20.5 8 20L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="px-3">Digital Marketing</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.marketing.campaigns.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Campaigns</a></li>
                        <li><a href="{{ route('admin.marketing.videos.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Videos</a></li>
                        <li><a href="{{ route('admin.marketing.posters.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Posters</a></li>
                    </ul>
                </li>

                <!-- Documents -->
                <li class="nav-item">
                    <a href="{{ route('admin.marketing.documents.index') }}" class="group">
                        <div class="flex items-center">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2" />
                                <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" />
                                <path d="M9 15l2 2 4-4" stroke="currentColor" stroke-width="2" />
                            </svg>
                            <span class="px-3">Documents</span>
                        </div>
                    </a>
                </li>

                <!-- Search Student -->
                @can('*consultant|*application|*marketing|*accountant')
                <li class="nav-item">
                    <a href="{{ route('admin.student-search.index') }}" class="group {{ request()->routeIs('admin.student-search.*') ? 'active' : '' }}">
                        <div class="flex items-center">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                                <path d="M20 20L16 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M11 8V14M8 11H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            <span class="px-3">Search Student</span>
                        </div>
                    </a>
                </li>
                @endcan

                <!-- Search Program -->
                <li class="nav-item">
                    <a href="{{ route('admin.search-program.index') }}" class="group {{ request()->routeIs('admin.search-program.*') ? 'active' : '' }}">
                        <div class="flex items-center">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                <circle cx="12" cy="10" r="3"/>
                                <path d="m14.5 12.5 2.5 2.5"/>
                            </svg>
                            <span class="px-3">Search Program</span>
                        </div>
                    </a>
                </li>

                <!-- Learning Resources -->
                <li class="nav-item">
                    <a href="{{ route('admin.learning-resources.index') }}" class="group {{ request()->routeIs('admin.learning-resources.*') ? 'active' : '' }}">
                        <div class="flex items-center">
                            <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="px-3">Learning Resources</span>
                        </div>
                    </a>
                </li>

                <!-- Consulting -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="2" />
                                    <circle cx="17" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                                    <path d="M3 20C3 16.5 6 14 9 14C12 14 15 16.5 15 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <path d="M13 20C13 17.5 15 16 17 16C19 16 21 17.5 21 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span class="px-3">Consulting</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.student-search.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726] font-semibold text-primary">Search Student</a></li>
                        <li><a href="{{ route('admin.students.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Add Student</a></li>
                        <li><a href="{{ route('admin.students.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Student List</a></li>
                    </ul>
                </li>

                <!-- Application -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 21H5C3.89543 21 3 20.1046 3 19V5C3 3.89543 3.89543 3 5 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 8H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 12H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 16H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="px-3">Application</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.applications.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Add Application</a></li>
                        <li><a href="{{ route('admin.applications.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Application List</a></li>
                        <li><a href="{{ route('admin.applications.country-report') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Country-wise Report</a></li>
                    </ul>
                </li>

                <!-- Invoice -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 13H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <path d="M8 17H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <path d="M8 9H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span class="px-3">Invoice</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.invoices.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Generate Invoice</a></li>
                        <li><a href="{{ route('admin.invoices.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Invoice List</a></li>
                    </ul>
                </li>

                <!-- VFS Checklist -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor" stroke-width="2" />
                                    <path d="M9 14l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="px-3">VFS Checklist</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.vfs-checklist.overview') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">VFS Overview</a></li>
                        <li><a href="{{ route('admin.vfs-checklist.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Checklist List</a></li>
                        <li><a href="{{ route('admin.vfs-checklist.templates') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Manage List</a></li>
                    </ul>
                </li>

                <!-- My Commissions - All authenticated users -->
                @if (auth()->check())
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 6V18M9 9H13.5C14.3284 9 15 9.67157 15 10.5C15 11.3284 14.3284 12 13.5 12H10.5C9.67157 12 9 12.6716 9 13.5C9 14.3284 9.67157 15 10.5 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="px-3">My Commissions</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('my-commissions.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Dashboard</a></li>
                        <li><a href="{{ route('my-commissions.claimable') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Claim Commission</a></li>
                    </ul>
                </li>
                @endif

                <!-- Accounting -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.5 2H20V22H6.5A2.5 2.5 0 0 1 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="px-3 font-bold">Accounting</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.accounting-periods.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Accounting Periods</a></li>
                        <li><a href="{{ route('admin.chart-of-accounts.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Chart of Accounts</a></li>
                        <li><a href="{{ route('admin.journal-entries.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Journal Vouchers</a></li>
                        <li><a href="{{ route('admin.reports.balance-sheet') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Balance Sheet</a></li>
                    </ul>
                </li>

                <!-- Payments -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <path d="M2 10H22" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span class="px-3">Payments</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.payments.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Add Payment</a></li>
                        <li><a href="{{ route('admin.payments.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Payment List</a></li>
                    </ul>
                </li>

                <!-- Invoices -->
                <!-- <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="px-3">Invoices</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.invoices.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Student Invoices</a></li>
                    </ul>
                </li> -->

                <!-- Operations -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                <span class="px-3">Operations</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Salaries</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.salaries.generate') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Generate Salary</a></li>
                                <li><a href="{{ route('admin.salaries.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Salary List</a></li>
                            </ul>
                        </li>
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Expenses</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.expenses.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Add Expense</a></li>
                                <li><a href="{{ route('admin.expenses.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Expense List</a></li>
                            </ul>
                        </li>
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Bank</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.office-accounts.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Accounts</a></li>
                            </ul>
                        </li>
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Currencies</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                @if(auth()->user()->hasRole('accountant'))
                                    <li><a href="{{ route('admin.currencies.create') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Add Currency</a></li>
                                @endif
                                <li><a href="{{ route('admin.currencies.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Manage Currency</a></li>
                            </ul>
                        </li>
                        <!-- <li><a href="{{ route('admin.budgets.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Budgets</a></li> -->
                        <li class="nav-item" x-data="{ childOpen: false }">
                            <a href="javascript:;" @click="childOpen = !childOpen" class="group flex items-center justify-between rounded-md px-3 py-2 hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="text-sm">Reports</span>
                                <svg class="h-3 w-3 transition-transform" :class="childOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul x-show="childOpen" x-collapse class="mt-1 space-y-1 rounded-md bg-white p-2 shadow-sm dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.reports.summary') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-[#1b2e4b]">Financial Summary</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- Commissions -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 6V18M9 9H13.5C14.3284 9 15 9.67157 15 10.5C15 11.3284 14.3284 12 13.5 12H10.5C9.67157 12 9 12.6716 9 13.5C9 14.3284 9.67157 15 10.5 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="px-3">Commissions</span>
                                @php
                                    $pendingCount = \App\Models\Commission::claimed()->count() + \App\Models\Commission::underReview()->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="ml-1 bg-danger text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li>
                            <a href="{{ route('admin.commissions.pending') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">
                                <span class="flex items-center justify-between w-full">
                                    <span>Pending Review</span>
                                    @php
                                        $pendingReviewCount = \App\Models\Commission::claimed()->count() + \App\Models\Commission::underReview()->count();
                                    @endphp
                                    @if($pendingReviewCount > 0)
                                        <span class="bg-warning text-white text-xs rounded-full px-2 py-0.5">
                                            {{ $pendingReviewCount }}
                                        </span>
                                    @endif
                                </span>
                            </a>
                        </li>
                        <li><a href="{{ route('admin.commissions.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">All Commissions</a></li>
                    </ul>
                </li>
                <!-- Administration -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.5" d="M12 15a3 3 0 100-6 3 3 0 000 6z" fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.121 17.659c.032.085.097.158.18.194l.003.002c.198.088.435-.004.529-.204.03-.065.062-.132.094-.197.105-.209.346-.312.569-.245.068.02.137.04.205.063.228.077.375.31.344.548-.009.071-.02.144-.032.215-.04.241.104.475.34.55a4.342 4.342 0 01.705.315c.218.122.316.395.231.624l-.025.07c-.085.23-.339.351-.568.27-.07-.024-.138-.05-.208-.072-.225-.073-.473.023-.585.228-.035.064-.07.13-.108.194-.123.212-.046.48.167.603.064.037.129.071.193.109.215.126.31.398.225.626-.145.394-.33.766-.554 1.111-.137.211-.407.284-.63.17l-.066-.034c-.218-.11-.49-.057-.643.125-.047.056-.093.113-.143.167-.163.178-.186.446-.057.653l.044.07c.143.232.083.535-.135.698a4.33 4.33 0 01-.84.484c-.233.1-.515-.004-.634-.233l-.037-.073c-.116-.226-.395-.316-.624-.213l-.208.094c-.22.1-.336.353-.274.587l.02.075c.063.242-.083.491-.324.557a4.343 4.343 0 01-.767.121c-.25.016-.474-.165-.52-.413l-.014-.076c-.042-.243-.278-.403-.523-.357l-.226.042c-.239.045-.476-.102-.55-.34-.021-.067-.04-.134-.06-.202-.071-.238.082-.48.323-.555a4.342 4.342 0 01.703-.316c.218-.121.317-.393.232-.622l-.025-.07c-.085-.23-.339-.351-.568-.27-.069.023-.137.049-.206.072-.225.074-.473-.019-.585-.224-.035-.065-.07-.131-.107-.196-.122-.211-.045-.478.168-.601.063-.037.128-.072.192-.11.215-.126.31-.397.225-.625-.146-.395-.331-.768-.556-1.113-.137-.211-.408-.284-.632-.169l-.065.034c-.218.11-.489.058-.642-.124-.047-.056-.094-.113-.144-.168-.163-.177-.186-.444-.058-.651l.044-.07c.143-.232.084-.535-.134-.698a4.33 4.33 0 01-.84-.484c-.233-.101-.516.004-.635.232l-.037.073c-.116.225-.395.315-.624.212l-.207-.094c-.22-.099-.336-.352-.275-.585l.02-.076c.063-.242-.082-.49-.323-.557a4.343 4.343 0 01-.767-.12c-.25-.017-.474.164-.52.412l-.015.076c-.041.244-.277.405-.522.36l-.225-.042c-.238-.045-.476.101-.551.338-.021.068-.041.136-.061.204-.071.238.081.48.322.556.234.08.472.196.704.317.218.121.317.393.232.622l-.026.071c-.084.229-.337.35-.565.269-.067-.022-.133-.046-.199-.068-.226-.075-.476.019-.59.223-.035.064-.071.129-.109.193-.123.213-.046.482.168.605.064.037.129.072.193.11.215.126.311.398.226.627-.146.394-.331.766-.556 1.111z" fill="currentColor" />
                                </svg>
                                <span class="px-3">Administration</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('tyro-dashboard.users.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Users</a></li>
                        <li><a href="{{ route('tyro-dashboard.roles.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Roles</a></li>
                        <li><a href="{{ route('tyro-dashboard.privileges.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Privileges</a></li>
                    </ul>
                </li>

                <!-- Settings -->
                <li class="nav-item" x-data="{ open: false }">
                    <a href="javascript:;" @click="open = !open" class="group">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <span class="px-3">Settings</span>
                            </div>
                            <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 rounded-md bg-gray-100 p-2 dark:bg-[#1b2e4b]">
                        <li><a href="{{ route('admin.settings.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Settings</a></li>
                        <li><a href="{{ route('admin.program-settings.index') }}" class="block rounded-md px-3 py-2 text-sm hover:bg-white hover:shadow-sm dark:hover:bg-[#0e1726]">Program Taxonomies</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
