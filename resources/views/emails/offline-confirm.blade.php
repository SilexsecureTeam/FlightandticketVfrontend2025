<div style="min-height: 100vh; background: linear-gradient(to bottom right, #f0f7ff, #e1effe); padding: 64px 16px;">
    <div style="max-width: 768px; margin: 0 auto;">
        <!-- Success Card -->
        <div
            style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <!-- Top Success Banner -->
            <div style="background-color: #97050C; padding: 24px 32px; position: relative; overflow: hidden;">
                <div
                    style="position: absolute; inset: 0; background-image: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23fff\" fill-opacity=\"0.1\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E'); opacity: 0.1;">
                </div>
                <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                    <div style="background-color: #ffffff; border-radius: 9999px; padding: 8px; margin-bottom: 16px;">
                        <svg style="width: 48px; height: 48px; color: #10b981;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                </div>
                <h1 style="text-align: center; font-size: 20px; font-weight: 700; color: #ffffff;">Offline Reservation
                    Confirmed!</h1>
                <p style="text-align: center; color: #dcfce7; margin-top: 8px;">Your Offline Reservation has been
                    Confirmed</p>
            </div>

            <!-- Payment Details -->
            <div style="padding: 32px; margin-bottom: 24px;">
                <!-- Transaction ID -->
                <div
                    style="display: flex; flex-direction: column; justify-content: space-between; align-items: center; padding: 16px; background-color: #f9fafb; border-radius: 12px; margin-bottom: 24px;">
                    <div style="color: #4b5563;">Booking ID : </div>
                    <div style="font-weight: 700; color: #111827;">{{ $bookingData['pnr'] }}</div>
                </div>

                <!-- Amount Paid -->
                <div style="text-align: center; margin-bottom: 16px;">
                    <span
                        style="font-size: 18px; font-weight: 700; color: #111827;">₦{{ number_format($bookingData['price']['grandTotal'] + $bookingData['commission'] , 2) }}</span>
                    <p style="color: #6b7280; margin-top: 4px;">Total Amount Paid</p>
                </div>

                <!-- Payment Instructions Alert -->
                <div
                    style="background-color: #fefce8; border-radius: 12px; padding: 20px; border: 1px solid #fef08a; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="flex-shrink: 0;">
                            <svg style="height: 24px; width: 24px; color: #ca8a04;" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div style="margin-left: 12px; width: 100%;">
                            <h3 style="font-size: 14px; font-weight: 500; color: #854d0e;">Important Payment Information
                            </h3>
                            <div style="margin-top: 8px; font-size: 14px; color: #a16207;">
                                <p>Please make payment to one of the accounts below:</p>

                                <div style="margin-top: 12px;">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                        <tr>
                                            <td width="100%"
                                                style="padding-right: 8px; padding-bottom: 16px; vertical-align: top;">
                                                <!-- First Bank Account -->
                                                <div
                                                    style="background-color: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #fef9c3; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                                    <p style="font-weight: 700; color: #854d0e;">Primary Account</p>
                                                    <p style="font-weight: 600; margin-top: 4px;">Wema Bank</p>
                                                    <div
                                                        style="display: flex; justify-content: space-between; margin-top: 4px;">
                                                        <p>Account Number:</p>
                                                        <p style="font-weight: 700;">0125048653</p>
                                                    </div>
                                                    <p style="margin-top: 4px;">MYMORE TRAVELS SERVICES LTD</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%"
                                                style="padding-left: 8px; padding-bottom: 16px; vertical-align: top;">
                                                <!-- Second Bank Account -->
                                                <div
                                                    style="background-color: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #fef9c3; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                                    <p style="font-weight: 700; color: #854d0e;">Alternative Account</p>
                                                    <p style="font-weight: 600; margin-top: 4px;">Alternative Bank</p>
                                                    <div
                                                        style="display: flex; justify-content: space-between; margin-top: 4px;">
                                                        <p>Account Number:</p>
                                                        <p style="font-weight: 700;">0505820902</p>
                                                    </div>
                                                    <p style="margin-top: 4px;">Mymore Travels Limited</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div
                                    style="margin-top: 16px; padding: 12px; background-color: #fef2f2; border-radius: 8px; border: 1px solid #fee2e2;">
                                    <p style="font-weight: 600; color: #dc2626;">⚠️ If payment is not confirmed within
                                        48 hours, your flight reservation will be cancelled.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Grid -->
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                    <div style="background-color: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-size: 16px; font-weight: 500; color: #374151;">Payment Details</h3>
                    </div>

                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td width="50%"
                                style="padding: 24px; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb;">
                                <!-- Payment Date -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td width="24" valign="top">
                                            <svg style="height: 20px; width: 20px; color: #9ca3af;"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </td>
                                        <td style="padding-left: 2px;">
                                            <p style="font-size: 12px; font-weight: 500; color: #6b7280;">Payment Date
                                            </p>
                                            <p
                                                style="margin-top: 4px; font-size: 12px; font-weight: 500; color: #111827;">
                                                {{ now()->format('M d, Y h:i A') }}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" style="padding: 24px; border-bottom: 1px solid #e5e7eb;">
                                <!-- Payment Type -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td width="24" valign="top">
                                            <svg style="height: 20px; width: 20px; color: #9ca3af;"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </td>
                                        <td style="padding-left: 12px;">
                                            <p style="font-size: 12px; font-weight: 500; color: #6b7280;">Payment Type
                                            </p>
                                            <p
                                                style="margin-top: 4px; font-size: 12px; font-weight: 500; color: #111827;">
                                                Offline Payment</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" style="padding: 24px; border-right: 1px solid #e5e7eb;">
                                <!-- Email -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td width="24" valign="top">
                                            <svg style="height: 20px; width: 20px; color: #9ca3af;"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                            </svg>
                                        </td>
                                        <td style="padding-left: 2px;">
                                            <p style="font-size: 12px; font-weight: 500; color: #6b7280;">Email</p>
                                            <p
                                                style="margin-top: 4px; font-size: 12px; font-weight: 500; color: #111827; word-break: break-all;">
                                                {{ $bookingData['travelers'][0]['contact']['emailAddress'] }}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%" style="padding: 24px;">
                                <!-- Status -->
                                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                    <tr>
                                        <td width="24" valign="top">
                                            <svg style="height: 20px; width: 20px; color: #9ca3af;"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </td>
                                        <td style="padding-left: 16px;">
                                            <p style="font-size: 14px; font-weight: 500; color: #6b7280;">Status</p>
                                            <div style="margin-top: 8px;">
                                                <span
                                                    style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 9999px; font-size: 14px; font-weight: 500; background-color: #fef9c3; color: #854d0e;">
                                                    <svg style="margin-right: 6px; height: 8px; width: 8px; color: #ca8a04;"
                                                        fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Pending
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Flight Details Summary -->
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                    <div style="background-color: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-size: 16px; font-weight: 500; color: #374151;">Flight Details</h3>
                    </div>

                    <div style="padding: 20px;">
                        @if (!empty($bookingData['itineraries']))
                            @foreach ($bookingData['itineraries'] as $index => $itinerary)
                                <div style="margin-bottom: 16px;">
                                    <p style="font-weight: 600; color: #111827; margin-bottom: 8px;">
                                        {{ $index === 0 ? 'Departure' : 'Return' }} Flight
                                    </p>
                                    <p style="color: #4b5563; margin-bottom: 4px;">
                                        {{ $itinerary['segments'][0]['departure']['city_country'] ?? 'N/A' }}
                                        <span style="margin: 0 8px;">→</span>
                                        {{ end($itinerary['segments'])['arrival']['city_country'] ?? 'N/A' }}
                                    </p>
                                    <p style="color: #6b7280; font-size: 14px;">
                                        {{ \Carbon\Carbon::parse($itinerary['segments'][0]['departure']['at'])->format('D, M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p style="color: #6b7280;">Flight details not available</p>
                        @endif
                    </div>
                </div>

                <!-- Traveler Summary -->
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                    <div style="background-color: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-size: 16px; font-weight: 500; color: #374151;">Travelers</h3>
                    </div>

                    <div style="padding: 20px;">
                        @if (!empty($bookingData['travelers']))
                            @foreach ($bookingData['travelers'] as $traveler)
                                <div
                                    style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <p style="font-weight: 500; color: #111827; margin-bottom: 4px;">
                                        {{ $traveler['name']['firstName'] }}  {{ $traveler['name']['lastName'] }}
                                    </p>
                                    <p style="color: #6b7280; font-size: 14px;">
                                        @if (!empty($bookingData['travelType']))
                                            @foreach ($bookingData['travelType'] as $type)
                                                @if ($type['travelerId'] == $traveler['id'])
                                                    {{ ucfirst(strtolower($type['travelerType'])) }}
                                                    @break
                                                @endif
                                            @endforeach
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p style="color: #6b7280;">Traveler details not available</p>
                        @endif
                    </div>
                </div>

                <!-- Email Notice -->
                <div
                    style="background-color: #eff6ff; border-radius: 12px; padding: 16px; display: flex; align-items: flex-start; gap: 12px; margin-bottom: 24px;">
                    <svg style="width: 20px; height: 20px; color: #3b82f6; margin-top: 2px;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <div style="flex: 1; font-size: 14px; color: #97050C;">
                        We've sent a confirmation email to your registered email address with all the transaction
                        details.
                    </div>
                </div>
            </div>

            <!-- Support Section -->
            <div style="background-color: #f9fafb; padding: 24px 32px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; color: #4b5563;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span>Need help? <span
                            style="color: #97050C; font-weight: 500;">support@mymoretravels.com</span></span>
                </div>
            </div>
        </div>
    </div>
</div>
