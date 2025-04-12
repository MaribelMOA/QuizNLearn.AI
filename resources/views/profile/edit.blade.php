<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">



        <div class="w-full flex flex-col items-center space-y-6">

            <div class="w-full max-w-xl mx-auto p-6 bg-white dark:bg-gray-800 shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h2 class="text-sm font-medium text-black-500 dark:text-gray-300 uppercase tracking-wide">
                        Experience Points
                    </h2>
                    <p class="text-3xl font-extrabold text-primary dark:text-primary mt-2">
                        {{ Auth::user()->xp }} XP
                    </p>
                </div>
            </div>



            <div class="w-full max-w-xl p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>



            <div class="w-full max-w-xl p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="w-full max-w-xl p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
