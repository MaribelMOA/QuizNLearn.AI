<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>



    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Selección de Plan --}}
        <div class="mt-4">
            <x-input-label for="plan_id" :value="__('Selected Plan')" />
            <div class="flex justify-center space-x-4 mt-4">
                @foreach($plans as $plan)
                    <div class="relative flex justify-center items-center w-72">
                        <input type="radio"
                               id="plan_{{ $plan->id }}"
                               name="plan_id"
                               value="{{ $plan->id }}"
                               class="sr-only peer"
                               required
                               data-is-paid="{{ $plan->price > 0 ? 'true' : 'false' }}"
                            {{ $currentPlanId == $plan->id ? 'checked' : '' }}>

                        <label for="plan_{{ $plan->id }}"
                               class="plan-label border-2 p-4 w-full rounded-lg cursor-pointer transition-all duration-200
                  bg-gray-50 border-gray-300 hover:bg-gray-100 hover:border-gray-500
                  peer-checked:bg-blue-100 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-300">
                            <div class="flex flex-col items-center">
                                <div class="text-xl font-semibold text-gray-800">{{ $plan->name }}</div>
                                <div class="text-sm text-gray-500">{{ $plan->quizzes_allowed }} quiz creations</div>
                                <div class="mt-2 text-lg font-bold text-gray-700">${{ number_format($plan->price, 2) }}</div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('plan_id')" class="mt-2" />
        </div>

        {{-- Selección de Método de Pago --}}
        <div class="mt-4" id="payment-method-section">
            <x-input-label for="payment_method" :value="__('Payment Method')" class="text-sm"/>

            <div class="flex flex-wrap gap-6 justify-around mt-2">
                {{-- PayPal --}}
                <div class="relative">
                    <input type="radio"
                           id="paypal"
                           name="payment_method"
                           value="paypal"
                           class="absolute opacity-0 peer"
                           required
                        {{ $user->payment_method_id === 'paypal' ? 'checked' : '' }}>

                    <label for="paypal"
                           class="block w-48 p-4 border-2 border-gray-300 rounded-lg cursor-pointer
                  bg-white text-center transition-all duration-200
                  hover:bg-gray-100 hover:border-gray-500
                  peer-checked:bg-blue-100 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-300">
                        <div class="text-lg font-semibold text-gray-800">PayPal</div>
                        <div class="text-sm text-gray-500 mt-1">Fast and secure</div>
                    </label>
                </div>

                {{-- Stripe --}}
                <div class="relative">
                    <input type="radio"
                           id="stripe"
                           name="payment_method"
                           value="stripe"
                           class="absolute opacity-0 peer"
                           required
                        {{ $user->payment_method_id === 'stripe' ? 'checked' : '' }}>

                    <label for="stripe"
                           class="block w-48 p-4 border-2 border-gray-300 rounded-lg cursor-pointer
                  bg-white text-center transition-all duration-200
                  hover:bg-gray-100 hover:border-gray-500
                  peer-checked:bg-blue-100 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-300">
                        <div class="text-lg font-semibold text-gray-800">Stripe</div>
                        <div class="text-sm text-gray-500 mt-1">Fast and simple</div>
                    </label>
                </div>
            </div>
            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />

        </div>




        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Succesful Update!',
                        text: 'Your account has been succesfully updated.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                </script>
            @endif
        </div>
    </form>
</section>
