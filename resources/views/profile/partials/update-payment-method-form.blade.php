<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Plan and Payment Method') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('You can change your current subscription plan and payment method here.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('profile.updatePlan') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Plan -->
        <div>
            <x-input-label for="plan_id" :value="__('Select Plan')" />
            <select name="plan_id" id="plan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @if($user->plan_id == $plan->id) selected @endif>
                        {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('plan_id')" class="mt-2" />
        </div>

        <!-- Payment Method -->
        <div>
            <x-input-label for="payment_method" :value="__('Payment Method')" />
            <select name="payment_method" id="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="paypal" @if($user->payment_method == 'paypal') selected @endif>PayPal</option>
                <option value="stripe" @if($user->payment_method == 'stripe') selected @endif>Stripe</option>
            </select>
            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
        </div>
    </form>
</section>
