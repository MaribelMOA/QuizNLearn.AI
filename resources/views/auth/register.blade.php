<x-guest-layout>

    <form method="POST" action="{{ route('register') }} " id="register-form" >
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <!-- Plan de Pago -->
        <div class="mt-4">
            <x-input-label for="plan_id" :value="__('Select Plan')" />
            <div class="flex justify-center space-x-4 mt-4">
                @foreach($plans as $plan)
                    <div class="relative flex justify-center items-center w-72">
                        <input type="radio"
                               id="plan_{{ $plan->id }}"
                               name="plan_id"
                               value="{{ $plan->id }}"
                               class="sr-only peer"
                               required
                               data-is-paid="{{ $plan->price > 0 ? 'true' : 'false' }}">

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

        <div class="mt-4 hidden" id="payment-method-section">
            <x-input-label for="payment_method" :value="__('Select Payment Method')" class="text-sm"/>

            <div class="flex flex-wrap gap-6 justify-around mt-2">
                <!-- PayPal -->
                <div class="relative">
                    <input type="radio"
                           id="paypal"
                           name="payment_method"
                           value="paypal"
                           class="absolute opacity-0 peer"
                    >

                    <label for="paypal"
                           class="block w-48 p-4 border-2 border-gray-300 rounded-lg cursor-pointer
                    bg-white text-center transition-all duration-200
                    hover:bg-gray-100 hover:border-gray-500
                    peer-checked:bg-blue-100 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-300">
                        <div class="text-lg font-semibold text-gray-800">PayPal</div>
                        <div class="text-sm text-gray-500 mt-1">Fast and secure</div>
                    </label>
                </div>

                <!-- Tarjeta -->
                <div class="relative">
                    <input type="radio"
                           id="stripe"
                           name="payment_method"
                           value="stripe"
                           class="absolute opacity-0 peer"
                    >

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
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const planInputs = document.querySelectorAll('input[name="plan_id"]');
            const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
            const paymentSection = document.getElementById('payment-method-section');

            function togglePaymentRequirement() {
                const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
                const isPaid = selectedPlan && selectedPlan.dataset.isPaid === 'true';

                // Si el plan es de pago, se debe mostrar la sección de métodos de pago
                if (paymentSection) {
                    paymentSection.classList.toggle('hidden', !isPaid);
                }
            }

            // Ejecutar al cargar la página en caso de que un plan ya esté seleccionado
            togglePaymentRequirement();

            // Añadir listener para actualizar cuando el usuario cambie de plan
            planInputs.forEach(input => {
                input.addEventListener('change', togglePaymentRequirement);
            });
        });

        document.getElementById('register-form').addEventListener('submit', function (e) {
            const selectedPlan = document.querySelector('input[name="plan_id"]:checked');
            const isPaid = selectedPlan && selectedPlan.dataset.isPaid === 'true';
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');

            if (isPaid && !selectedPayment) {
                e.preventDefault();

                    Swal.fire({
                        icon: 'error',
                        title: '¡Opps!',
                        text: 'Make sure to choose a payment methos for your plan',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
            }
        });

    </script>




</x-guest-layout>
