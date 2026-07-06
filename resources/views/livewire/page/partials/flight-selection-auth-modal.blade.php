@if ($showSelectionAuthModal)
<div class="flight-selection-auth-overlay" wire:click.self="closeSelectionAuthModal" style="z-index: 1000000000000000000000;">
    <div class="flight-selection-auth-modal" role="dialog" aria-modal="true" aria-labelledby="flight-selection-auth-title">
        <div class="flight-selection-auth-modal__header">
            <h3 id="flight-selection-auth-title" class="flight-selection-auth-modal__title">
                @if ($selectionAuthStep === 'otp')
                    Verify your email
                @else
                    Continue to flight details
                @endif
            </h3>
            <button type="button" class="flight-selection-auth-modal__close" wire:click="closeSelectionAuthModal" aria-label="Close">&times;</button>
        </div>

        <div class="flight-selection-auth-modal__body">
            @if ($selectionAuthMessage)
                <div class="flight-selection-auth-modal__message">{{ $selectionAuthMessage }}</div>
            @endif

            @if ($selectionAuthError)
                <div class="flight-selection-auth-modal__error">{{ $selectionAuthError }}</div>
            @endif

            @if ($selectionAuthStep === 'details')
                <p class="flight-selection-auth-modal__hint">Enter your name and email to receive a verification code before viewing flight details.</p>

                <div class="flight-selection-auth-modal__field">
                    <label for="guestName">Full name</label>
                    <input type="text" id="guestName" wire:model="guestName" class="flight-selection-auth-modal__input" style='color: black;' placeholder="Enter your full name">
                    @error('guestName') <span class="flight-selection-auth-modal__field-error">{{ $message }}</span> @enderror
                </div>

                <div class="flight-selection-auth-modal__field">
                    <label for="guestEmail">Email address</label>
                    <input type="email" id="guestEmail" wire:model="guestEmail" class="flight-selection-auth-modal__input" style='color: black;' placeholder="Enter your email">
                    @error('guestEmail') <span class="flight-selection-auth-modal__field-error">{{ $message }}</span> @enderror
                </div>

                <button type="button" class="flight-selection-auth-modal__btn" wire:click="sendSelectionOtp" wire:loading.attr="disabled" wire:target="sendSelectionOtp">
                    <span wire:loading.remove wire:target="sendSelectionOtp">Send OTP</span>
                    <span wire:loading wire:target="sendSelectionOtp">Sending...</span>
                </button>
            @else
                <p class="flight-selection-auth-modal__hint">We sent a 6-digit code to <strong>{{ $guestEmail }}</strong>. Enter it below to continue.</p>

                <div class="flight-selection-auth-modal__field">
                    <label for="guestOtp">OTP code</label>
                    <input type="text" id="guestOtp" wire:model="guestOtp" maxlength="6" class="flight-selection-auth-modal__input flight-selection-auth-modal__input--otp" style='color: black;' placeholder="Enter 6-digit OTP">
                    @error('guestOtp') <span class="flight-selection-auth-modal__field-error">{{ $message }}</span> @enderror
                </div>

                <button type="button" class="flight-selection-auth-modal__btn" wire:click="verifySelectionOtp" wire:loading.attr="disabled" wire:target="verifySelectionOtp">
                    <span wire:loading.remove wire:target="verifySelectionOtp">Verify &amp; Continue</span>
                    <span wire:loading wire:target="verifySelectionOtp">Verifying...</span>
                </button>

                <button type="button" class="flight-selection-auth-modal__link" wire:click="resendSelectionOtp" wire:loading.attr="disabled" wire:target="resendSelectionOtp">
                    <span wire:loading.remove wire:target="resendSelectionOtp">Resend OTP</span>
                    <span wire:loading wire:target="resendSelectionOtp">Resending...</span>
                </button>
            @endif
        </div>
    </div>
</div>
@endif
