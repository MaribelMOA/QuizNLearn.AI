<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('XP Store') }}
            </h2>
            <div class="flex items-center bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 rounded-full shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-300 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-white font-bold text-lg">{{ Auth::user()->xp }} XP</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Resources Dashboard -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 dark:text-indigo-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Your Resources</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Quiz Creations -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 transform transition-all hover:scale-105">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $availableCreations }}</span>
                        </div>
                        <h4 class="text-gray-600 dark:text-gray-300 font-medium">Quiz Creations</h4>
                        <div class="mt-2 bg-gray-200 dark:bg-gray-600 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full" style="width: {{ min(100, $availableCreations * 10) }}%"></div>
                        </div>
                    </div>

                    <!-- Study Mode Uses -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 transform transition-all hover:scale-105">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $studyModeUses }}</span>
                        </div>
                        <h4 class="text-gray-600 dark:text-gray-300 font-medium">Study Mode Uses</h4>
                        <div class="mt-2 bg-gray-200 dark:bg-gray-600 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-green-500 h-full rounded-full" style="width: {{ min(100, $studyModeUses * 10) }}%"></div>
                        </div>
                    </div>

                    <!-- Arena Mode Uses -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 transform transition-all hover:scale-105">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $arenaModeUses }}</span>
                        </div>
                        <h4 class="text-gray-600 dark:text-gray-300 font-medium">Arena Mode Uses</h4>
                        <div class="mt-2 bg-gray-200 dark:bg-gray-600 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full rounded-full" style="width: {{ min(100, $arenaModeUses * 10) }}%"></div>
                        </div>
                    </div>

                    <!-- Summary Creations -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 transform transition-all hover:scale-105">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-amber-100 dark:bg-amber-900 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $availableSummaryCreations }}</span>
                        </div>
                        <h4 class="text-gray-600 dark:text-gray-300 font-medium">Summary Creations</h4>
                        <div class="mt-2 bg-gray-200 dark:bg-gray-600 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ min(100, $availableSummaryCreations * 10) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Upgrade Plan Button -->
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('profile.edit') }}" class="group relative inline-flex items-center justify-center px-8 py-3 overflow-hidden font-bold rounded-full bg-gradient-to-br from-purple-600 to-blue-500 text-white shadow-md hover:shadow-xl transition-all duration-300">
                        <span class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-600 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        <span class="relative">Upgrade My Plan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Shop -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-white">Features Shop</h3>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        @foreach($grouped as $type)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 shadow-md">
                                <div class="flex items-center mb-4">
                                    @if(strtolower($type->name) == 'quiz features')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif(strtolower($type->name) == 'study features')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    @elseif(strtolower($type->name) == 'arena features')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    @elseif(strtolower($type->name) == 'summary features')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    @endif
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $type->name }}</h4>
                                </div>

                                <p class="text-gray-600 dark:text-gray-300 mb-6">{{ $type->description }}</p>

                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($type->features as $feature)
                                        <form method="POST" action="{{ route('xp.purchaseFeature') }}" class="confirm-purchase-form">
                                            @csrf
                                            <input type="hidden" name="feature_id" value="{{ $feature->id }}">

                                            <button class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between group">
                                                <div class="flex items-center">
                                                    <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-lg mr-4">
                                                        @if(strpos(strtolower($feature->name), 'quiz') !== false)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @elseif(strpos(strtolower($feature->name), 'study') !== false)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                            </svg>
                                                        @elseif(strpos(strtolower($feature->name), 'arena') !== false)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                        @elseif(strpos(strtolower($feature->name), 'summary') !== false)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="text-left">
                                                        <h5 class="font-semibold text-gray-900 dark:text-white">{{ $feature->name }}</h5>
                                                    </div>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-3 py-1 rounded-full font-semibold flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $feature->xp_price }}
                                                    </div>
                                                    <div class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- XP Packages -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-white">XP Packages</h3>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($xpPrices as $package)
                            <div class="bg-gradient-to-b from-white to-gray-50 dark:from-gray-700 dark:to-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-600 transform transition-all hover:scale-105 hover:shadow-xl">
                                <div class="p-1">
                                    <div class="bg-gradient-to-r from-amber-500 to-yellow-500 text-white p-4 rounded-t-lg">
                                        <div class="flex justify-between items-center">
                                            <h4 class="text-xl font-bold">XP Package</h4>
                                            <div class="bg-white text-yellow-500 rounded-full h-10 w-10 flex items-center justify-center font-bold text-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6 text-center">
                                        <div class="mb-4">
                                            <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $package->xp_amount }}</span>
                                            <span class="text-xl text-amber-500 font-bold">XP</span>
                                        </div>

                                        <div class="mb-6">
                                            <span class="text-2xl font-bold text-gray-800 dark:text-gray-200">${{ number_format($package->price, 2) }}</span>
                                            <span class="text-gray-500 dark:text-gray-400">USD</span>
                                        </div>

                                        <form method="POST" action="{{ route('xp.purchasePackage') }}" class="confirm-purchase-form">
                                            @csrf
                                            <input type="hidden" name="xp_price_id" value="{{ $package->id }}">
                                            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Purchase Package
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
        });
        @endif

        @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
        });
        @endif

        @if (session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: '{{ session('info') }}',
            confirmButtonColor: '#3085d6',
        });
        @endif

        @if (session('missing_payment'))
        Swal.fire({
            icon: 'warning',
            title: 'No Payment Method',
            text: 'You need to add a valid payment method before making a purchase.',
            showCancelButton: true,
            confirmButtonText: 'Go to Profile',
            cancelButtonText: 'Cancel Purchase',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('profile.edit') }}";
            }
        });
        @endif

        document.querySelectorAll('.confirm-purchase-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent immediate submission

                Swal.fire({
                    title: 'Confirm Purchase?',
                    text: "Are you sure you want to proceed with this purchase?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, buy it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit form if confirmed
                    }
                });
            });
        });
    </script>

</x-app-layout>
