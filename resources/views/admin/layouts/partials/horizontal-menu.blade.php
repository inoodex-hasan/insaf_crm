<style>
    .horizontal-menu .nav-item .sub-menu::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 0;
        right: 0;
        height: 20px;
        display: block;
    }

    /* Arrow Rotation Logic */
    .horizontal-menu .nav-item .right_arrow svg,
    .horizontal-menu .nav-item a>svg {
        transition: all 0.3s ease-in-out !important;
    }

    /* By default top level points down, nested points down.
       On hover, we want them both to point RIGHT (0deg) to show they are "pointing" at the expanded content. */
    .horizontal-menu .nav-item:hover>a .right_arrow svg,
    .horizontal-menu .nav-item:hover>a>svg {
        transform: rotate(0deg) !important;
    }

    /* Ensure rotate-90 doesn't block the hover state */
    .horizontal-menu .nav-item:hover>a svg.rotate-90,
    .horizontal-menu .nav-item:hover>a .right_arrow svg.rotate-90 {
        transform: rotate(0deg) !important;
    }

    /* Fix sub-menu stacking: Hide nested child-menus by default and show only on item hover */
    .horizontal-menu .sub-menu .child-menu {
        display: none !important;
    }

    .horizontal-menu .sub-menu li:hover>.child-menu {
        display: block !important;
    }
</style>
<ul class="horizontal-menu border-t border-[#ebedf2] bg-white px-6 py-1.5 font-semibold text-black rtl:space-x-reverse dark:border-[#191e3a] dark:bg-[#0e1726] dark:text-white-dark lg:space-x-1.5 xl:space-x-8"
    x-show="$store.app.menu === 'horizontal'">
    <!-- Dashboard -->
    <li class="menu nav-item relative">
        <a href="{{ route('tyro-dashboard.index') }}" class="nav-link">
            <div class="flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="shrink-0">
                    <path opacity="0.5"
                        d="M2 12.2039C2 9.91549 2 8.77128 2.5192 7.82274C3.0384 6.87421 3.98695 6.28551 5.88403 5.10813L7.88403 3.86687C9.88939 2.62229 10.8921 2 12 2C13.1079 2 14.1106 2.62229 16.116 3.86687L18.116 5.10812C20.0131 6.28551 20.9616 6.87421 21.4808 7.82274C22 8.77128 22 9.91549 22 12.2039V13.725C22 17.6258 22 19.5763 20.8284 20.7881C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.7881C2 19.5763 2 17.6258 2 13.725V12.2039Z"
                        fill="currentColor" />
                    <path
                        d="M9 17.25C8.58579 17.25 8.25 17.5858 8.25 18C8.25 18.4142 8.58579 18.75 9 18.75H15C15.4142 18.75 15.75 18.4142 15.75 18C15.75 17.5858 15.4142 17.25 15 17.25H9Z"
                        fill="currentColor" />
                </svg>
                <span class="px-1">Dashboard</span>
            </div>
        </a>
    <!-- <li class="menu nav-item relative group">
        <a href="javascript:;" class="nav-link">
            <div class="flex items-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6V18M9 9H13.5C14.3284 9 15 9.67157 15 10.5C15 11.3284 14.3284 12 13.5 12H10.5C9.67157 12 9 12.6716 9 13.5C9 14.3284 9.67157 15 10.5 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="px-1">Currencies</span>
            </div>
            <div class="right_arrow">
                <svg class="h-4 w-4 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                    width="16" height="16" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
        </a>
        <ul class="sub-menu">
            @if (auth()->user()->hasRole('accountant'))
                <li><a href="{{ route('admin.currencies.create') }}">Add Currency</a></li>
            @endif
            <li><a href="{{ route('admin.currencies.index') }}">Manage Currency</a></li>
        </ul>
    </li> -->

    @if (auth()->check() && auth()->user()->hasRole(role: 'editor'))
        <li class="menu nav-item relative group">

            {{-- Parent Menu --}}
            <a href="javascript:;" class="nav-link flex items-center justify-between w-full">
                <div class="flex items-center">
                    <!-- Editor Icon -->
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" stroke="currentColor"
                            stroke-width="1.5" fill="none" />
                        <path d="M14.06 4.94l3.75 3.75" stroke="currentColor" stroke-width="1.5" />
                    </svg>

                    <span class="px-2">Editor</span>
                </div>

                <div class="right_arrow">
                    <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </a>

            {{-- Child Menu --}}
            <ul class="sub-menu">

                {{-- Countries --}}
                <li class="menu nav-item relative group">
                    <a href="javascript:;" class="flex justify-between items-center w-full">
                        Countries
                        <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                            viewBox="0 0 24 24" fill="none">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>

                    {{-- Flyout submenu --}}
                    <ul
                        class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 group-hover:block">

                        <li><a href="{{ route('admin.countries.create') }}"
                                class="block px-4 py-2 hover:bg-gray-100">Add
                                Country</a></li>
                        <li><a href="{{ route('admin.countries.index') }}"
                                class="block px-4 py-2 hover:bg-gray-100">Country
                                List</a></li>

                    </ul>
                </li>

                {{-- Universities --}}
                <li class="menu nav-item relative group">
                    <a href="javascript:;" class="flex justify-between items-center w-full">
                        Universities
                        <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                            viewBox="0 0 24 24" fill="none">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>

                    {{-- Flyout submenu --}}
                    <ul
                        class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 group-hover:block">

                        <li><a href="{{ route('admin.universities.create') }}"
                                class="block px-4 py-2 hover:bg-gray-100">Add
                                University</a></li>
                        <li><a href="{{ route('admin.universities.index') }}"
                                class="block px-4 py-2 hover:bg-gray-100">University
                                List</a></li>

                    </ul>
                </li>

                {{-- Courses --}}
                <li class="menu nav-item relative group">
                    <a href="javascript:;" class="flex justify-between items-center w-full">
                        Courses
                        <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                            viewBox="0 0 24 24" fill="none">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>

                    {{-- Flyout submenu --}}
                    <ul
                        class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50">

                        <li><a href="{{ route('admin.courses.create') }}" class="block px-4 py-2 hover:bg-gray-100">Add
                                Course</a></li>
                        <li><a href="{{ route('admin.courses.index') }}"
                                class="block px-4 py-2 hover:bg-gray-100">Course
                                List</a></li>

                    </ul>
                </li>

            </ul>
        </li>
    @endif


    <!-- Administration -->
    @if (auth()->check() && auth()->user()->hasRole('admin'))
        <li class="menu nav-item relative group">
            <a href="javascript:;" class="nav-link">
                <div class="flex items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <path opacity="0.5" d="M12 15a3 3 0 100-6 3 3 0 000 6z" fill="currentColor" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M18.121 17.659c.032.085.097.158.18.194l.003.002c.198.088.435-.004.529-.204.03-.065.062-.132.094-.197.105-.209.346-.312.569-.245.068.02.137.04.205.063.228.077.375.31.344.548-.009.071-.02.144-.032.215-.04.241.104.475.34.55a4.342 4.342 0 01.705.315c.218.122.316.395.231.624l-.025.07c-.085.23-.339.351-.568.27-.07-.024-.138-.05-.208-.072-.225-.073-.473.023-.585.228-.035.064-.07.13-.108.194-.123.212-.046.48.167.603.064.037.129.071.193.109.215.126.31.398.225.626-.145.394-.33.766-.554 1.111-.137.211-.407.284-.63.17l-.066-.034c-.218-.11-.49-.057-.643.125-.047.056-.093.113-.143.167-.163.178-.186.446-.057.653l.044.07c.143.232.083.535-.135.698a4.33 4.33 0 01-.84.484c-.233.1-.515-.004-.634-.233l-.037-.073c-.116-.226-.395-.316-.624-.213l-.208.094c-.22.1-.336.353-.274.587l.02.075c.063.242-.083.491-.324.557a4.343 4.343 0 01-.767.121c-.25.016-.474-.165-.52-.413l-.014-.076c-.042-.243-.278-.403-.523-.357l-.226.042c-.244.045-.42.274-.393.52l.008.077c.026.252-.15.484-.403.534a4.343 4.343 0 01-.775-.028c-.25-.034-.43-.257-.406-.508l.006-.077c.022-.247-.14-.475-.386-.54l-.22-.058c-.24-.065-.487.058-.584.29l-.022.054c-.114.283-.43.415-.705.298a4.333 4.333 0 01-.803-.45c-.244-.176-.328-.496-.188-.74l.03-.053c.125-.231.066-.52-.138-.684l-.167-.134c-.201-.161-.26-.445-.143-.675l.035-.069c.127-.249.034-.559-.21-.692a4.34 4.34 0 01-.697-.478c-.218-.184-.257-.5-.091-.73l.047-.066c.15-.21-.082-.52-.279-.652l-.183-.122c-.217-.145-.295-.436-.183-.67l.034-.07c.112-.236.4-.334.643-.22l.205.097c.228.106.505.01.62-.218l.094-.188c.114-.23.41-.303.626-.145l.056.04c.223.16.544.11.706-.112a4.337 4.337 0 01.32-.387c.189-.2.15-.52-.086-.685l-.066-.046c-.22-.153-.306-.449-.2-.686l.03-.067c.105-.239.387-.354.63-.259l.186.072c.23.09.5-.02.603-.245l.078-.17c.128-.278.44-.383.717-.245a4.33 4.33 0 01.605.353c.22.153.525.101.684-.117l.033-.044c.162-.216.444-.291.683-.133l.03.02z"
                            fill="currentColor" />
                    </svg>
                    <span class="px-1">Administration</span>
                </div>
                <div class="right_arrow">
                    <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </a>
            <ul class="sub-menu">
                <li><a href="{{ route('tyro-dashboard.users.index') }}">Users</a></li>
                <li><a href="{{ route('tyro-dashboard.roles.index') }}">Roles</a></li>
                <li><a href="{{ route('tyro-dashboard.privileges.index') }}">Privileges</a></li>
                {{-- <li><a href="{{ route('admin.settings.index') }}">Settings</a></li> --}}
                {{-- <li><a href="{{ route('admin.countries.index') }}">Countries</a></li>
                <li><a href="{{ route('admin.universities.index') }}">Universities</a></li>
                <li><a href="{{ route('admin.courses.index') }}">Courses</a></li> --}}
            </ul>
        </li>

        <li class="menu nav-item relative">
            <a href="javascript:;" class="nav-link">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756
                                                                                                                                                                                                                                                                                                                                                                                                            3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94
                                                                                                                                                                                                                                                                                                                                                                                                            3.31.826 2.37 2.37a1.724 1.724 0 001.065
                                                                                                                                                                                                                                                                                                                                                                                                            2.572c1.756.426 1.756 2.924 0 3.35a1.724
                                                                                                                                                                                                                                                                                                                                                                                                            1.724 0 00-1.066 2.573c.94 1.543-.826
                                                                                                                                                                                                                                                                                                                                                                                                            3.31-2.37 2.37a1.724 1.724 0 00-2.572
                                                                                                                                                                                                                                                                                                                                                                                                            1.065c-.426 1.756-2.924 1.756-3.35
                                                                                                                                                                                                                                                                                                                                                                                                            0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724
                                                                                                                                                                                                                                                                                                                                                                                                            1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924
                                                                                                                                                                                                                                                                                                                                                                                                            0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31
                                                                                                                                                                                                                                                                                                                                                                                                            2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="px-1">Settings</span>
                </div>
                <div class="right_arrow">
                    <svg class="h-4 w-4 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                        width="16" height="16" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
            </a>
            <ul class="sub-menu">

                {{-- <li><a href="{{ route('admin.marketing.leads.index') }}">Primary List</a></li> --}}
                <li><a href="{{ route('admin.settings.index') }}">Settings</a></li>

            </ul>
        </li>
    @endif

    @if (auth()->check() && auth()->user()->hasRole('marketing'))
        <!-- Marketing - Digital Marketing Only -->
        @canany(['*marketing'])
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M3 11V13M5 10L18 6V18L5 14V10Z" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5 14L6 19C6.2 20 7 20.5 8 20L9 19" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="px-1">Data Collection</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @can('*marketing')
                        <li><a href="{{ route('admin.marketing.leads.create') }}">Data Submit</a></li>
                    @endcan
                    @canany(['*marketing'])
                        <li><a href="{{ route('admin.marketing.leads.index') }}">Primary List</a></li>
                    @endcanany

                </ul>
            </li
        @endcanany
    @endif

    @if (auth()->check() && auth()->user()->hasRole('digital-marketing'))
        <!-- Digital Marketing -->
        @can('*digital_marketing')
            <li class="menu nav-item relative">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M3 11V13M5 10L18 6V18L5 14V10Z" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5 14L6 19C6.2 20 7 20.5 8 20L9 19" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="px-1">Digital Marketing</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @can('*digital_marketing')
                        <li><a href="{{ route('admin.marketing.campaigns.index') }}">Campaigns</a></li>
                        <li><a href="{{ route('admin.marketing.videos.index') }}">Videos</a></li>
                        <li><a href="{{ route('admin.marketing.posters.index') }}">Posters</a></li>
                    @endcan
                </ul>
            </li>
        @endcan
    @endif

    <!-- Documents - Separate Menu -->
    @if (auth()->check() && auth()->user()->hasRole('digital-marketing'))
        @can('*digital_marketing')
            <li class="menu nav-item relative">
                <a href="{{ route('admin.marketing.documents.index') }}" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                            <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 15l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span class="px-1">Documents</span>
                    </div>
                </a>
            </li>
        @endcan
    @endif

    @if (auth()->check() &&
            (auth()->user()->hasRole('consultant') ||
                auth()->user()->hasRole('application') ||
                auth()->user()->hasRole('marketing')))
        <!-- Consulting -->
        @canany(['*consultant', '*marketing'])
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="2" />
                            <circle cx="17" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                            <path d="M3 20C3 16.5 6 14 9 14C12 14 15 16.5 15 20" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M13 20C13 17.5 15 16 17 16C19 16 21 17.5 21 20" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span class="px-1">Consulting</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @canany(['*consultant', '*marketing'])
                        <li><a href="{{ route('admin.students.create') }}">Add Student</a></li>
                    @endcanany
                    @canany(['*consultant', '*application', '*marketing'])
                        <li><a href="{{ route('admin.students.index') }}">Student List</a></li>
                    @endcanany
                </ul>
            </li>
        @endcanany
    @endif

    @if (auth()->check() && (auth()->user()->hasRole('consultant') || auth()->user()->hasRole('application') || auth()->user()->hasRole('accountant')))
        <!-- Application -->
        @canany(['*consultant', '*application', '*accountant'])
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22Z"
                                stroke="currentColor" stroke-width="2" />
                            <path d="M12 12V16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span class="px-1">Application</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @canany(['*consultant', '*application', '*accountant'])
                        <li><a href="{{ route('admin.applications.create') }}">Add Application</a></li>
                        <li><a href="{{ route('admin.applications.index') }}">Application List</a></li>
                        <li><a href="{{ route('admin.applications.country-report') }}">Country-wise Report</a></li>
                    @endcanany
                </ul>
            </li>
             @can('*consultant')
            <li class="menu nav-item relative">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path
                                d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M14 2V8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M8 13H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M8 17H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M8 9H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span class="px-1">Invoice</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @canany(['*consultant'])
                        <li><a href="{{ route('admin.invoices.create') }}">Generate Invoice</a></li>
                        <li><a href="{{ route('admin.invoices.index') }}">Invoice List</a></li>
                    @endcanany
                </ul>
            </li>
            @endcan
        @endcanany
        @can('*application|*consultant')
            <li class="menu nav-item relative">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <rect x="9" y="3" width="6" height="4" rx="1" stroke="currentColor"
                                stroke-width="2" />
                            <path d="M9 14l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="px-1">VFS Checklist</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{ route('admin.vfs-checklist.overview') }}">VFS Overview</a></li>
                    <li><a href="{{ route('admin.vfs-checklist.index') }}">Checklist List</a></li>
                    <li><a href="{{ route('admin.vfs-checklist.templates') }}">Manage List</a></li>
                </ul>
            </li>
        @endcan
    @endif

    <!-- My Commissions - All authenticated users -->
    @if (auth()->check())
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="px-1">My Commissions</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{ route('my-commissions.index') }}">Dashboard</a></li>
                    <li><a href="{{ route('my-commissions.claimable') }}">Claim Commission</a></li>
                </ul>
            </li>
    @endif

    @if (auth()->check() && auth()->user()->hasRole('accountant'))
        <!-- Core Accounting -->
        @can('*accountant')
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg class="shrink-0" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.5"
                                d="M17 18.5H19C20.1046 18.5 21 17.6046 21 16.5V7.5C21 6.39543 20.1046 5.5 19 5.5H5C3.89543 5.5 3 6.39543 3 7.5V16.5C3 17.6046 3.89543 18.5 5 18.5H7M12 15.5V2.5M12 15.5L9 12.5M12 15.5L15 12.5M9 21.5H15"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="px-1 font-bold">Accounting</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    <li><a href="{{ route('admin.accounting-periods.index') }}">Accounting Periods</a></li>
                    <li><a href="{{ route('admin.chart-of-accounts.index') }}">Chart of Accounts</a></li>
                    <li><a href="{{ route('admin.journal-entries.index') }}">Journal Vouchers</a></li>
                    <!-- <li><a href="{{ route('admin.bank-reconciliations.index') }}">Bank Reconciliation</a></li> -->
                    <li><a href="{{ route('admin.reports.balance-sheet') }}">Balance Sheet</a></li>
                </ul>
            </li>
        @endcan

        <!-- Payments & Financial Operations -->
        @canany(['*accountant'])
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="px-1">Payments</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @can('*accountant')
                        <li><a href="{{ route('admin.payments.create') }}">Add Payment</a></li>
                        <li><a href="{{ route('admin.payments.index') }}">Payment List</a></li>
                    @endcan
                </ul>
            </li>

            <!-- Invoices Menu -->
            @canany(['*accountant'])
                <li class="menu nav-item relative group">
                    <a href="javascript:;" class="nav-link">
                        <div class="flex items-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                                <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="px-1">Invoices</span>
                        </div>
                        <div class="right_arrow">
                            <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="{{ route('admin.invoices.index') }}">Student Invoices</a></li>
                    </ul>
                </li>
            @endcanany

            <!-- Business Operations Consolidated -->
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path
                                d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                stroke="currentColor" stroke-width="2" />
                            <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <span class="px-1">Operations</span>
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    @can('*accountant')
                        {{-- Salaries Flyout --}}
                        <li class="menu nav-item relative group px-0">
                            <a href="javascript:;" class="flex justify-between items-center w-full">
                                Salaries
                                <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                                    viewBox="0 0 24 24" fill="none">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul
                                class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.salaries.generate') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Generate Salary</a></li>
                                <li><a href="{{ route('admin.salaries.index') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Salary
                                        List</a></li>
                            </ul>
                        </li>

                        {{-- Expenses Flyout --}}
                        <li class="menu nav-item relative group px-0">
                            <a href="javascript:;" class="flex justify-between items-center w-full">
                                Expenses
                                <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                                    viewBox="0 0 24 24" fill="none">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul
                                class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.expenses.create') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Add
                                        Expense</a></li>
                                <li><a href="{{ route('admin.expenses.index') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Expense
                                        List</a></li>
                            </ul>
                        </li>

                        {{-- Office Accounts & Transactions --}}
                        <li class="menu nav-item relative group px-0">
                            <a href="javascript:;" class="flex justify-between items-center w-full">
                                Bank
                                <svg class="h-3 w-3 rotate-90" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul
                                class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.office-accounts.index') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Accounts</a></li>
                            </ul>
                        </li>

                        <!-- Currencies -->
                         <li class="menu nav-item relative group px-0">
                            <a href="javascript:;" class="flex justify-between items-center w-full">
                                Currency
                                <svg class="h-3 w-3 rotate-90" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul
                                class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.currencies.create') }}"    
                                        class="block px-4 py-2 hover:bg-gray-100">Add Currency</a></li>
                                <li><a href="{{ route('admin.currencies.index') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Manage Currency   </a></li>
                            </ul>
                        </li>

                        <!-- Budgets & Reports -->
                        <li class="menu nav-item relative group px-0">
                            <a href="javascript:;" class="flex justify-between items-center w-full">
                                Reports
                                <svg class="h-3 w-3 rotate-90 transition-transform duration-300 group-hover:rotate-0"
                                    viewBox="0 0 24 24" fill="none">
                                    <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <ul
                                class="child-menu absolute ltr:left-full rtl:right-full top-0 ml-1 hidden min-w-[180px] bg-white shadow-lg rounded-md z-50 dark:bg-[#0e1726]">
                                <li><a href="{{ route('admin.reports.summary') }}"
                                        class="block px-4 py-2 hover:bg-gray-100">Financial Summary</a></li>

                            </ul>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Commissions - Separate Main Menu -->
        @can('*accountant')
            <li class="menu nav-item relative group">
                <a href="javascript:;" class="nav-link">
                    <div class="flex items-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="px-1">Commissions</span>
                        @php
                            $pendingCount = \App\Models\Commission::claimed()->count() + \App\Models\Commission::underReview()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-1 bg-danger text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </div>
                    <div class="right_arrow">
                        <svg class="h-4 w-4 rotate-90" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="{{ route('admin.commissions.pending') }}">
                            Pending Review
                            @php
                                $pendingReviewCount = \App\Models\Commission::claimed()->count() + \App\Models\Commission::underReview()->count();
                            @endphp
                            @if($pendingReviewCount > 0)
                                <span class="ml-2 bg-warning text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $pendingReviewCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li><a href="{{ route('admin.commissions.index') }}">All Commissions</a></li>
                </ul>
            </li>
        @endcan
    @endif

</ul>
