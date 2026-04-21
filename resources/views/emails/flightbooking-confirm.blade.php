<!DOCTYPE html>
<html>

<head>
    <title>Flight Booking Confirmation - Mymore Travels</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f7f7fb;">
    <!-- Top Banner -->
    <div style="background: #808080; padding: 20px; text-align: center;">
        <img src="https://flightandticketing.com/images/logo.png" alt="Mymore Travels"
            style="max-width: 200px; height: auto;">
    </div>

    <!-- Header Section -->
    <div
        style="background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%); padding: 35px 25px; text-align: center; border-bottom: 1px solid #e0e6ff;">
        <h1 style="color: #97050C; margin: 0; font-weight: 300; letter-spacing: 1px; font-size: 28px;">Reservation
            Confirmation</h1>
    </div>

    <!-- Main Content Container -->
    <div
        style="background-color: white; padding: 0; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 20px;">
        <!-- Personal Greeting -->
        <div style="padding: 30px 30px 20px 30px; border-bottom: 1px solid #f0f0f7;">
            <h2 style="color: #333; font-weight: 400; margin-top: 0; font-size: 20px;">Dear
                {{ $bookingData['travelers'][0]['name']['firstName'] }}
                {{ $bookingData['travelers'][0]['name']['lastName'] }},
            </h2>
            <p style="font-size: 16px; color: #555; margin-bottom: 0;">Your reservation has been confirmed. Please find
                your booking details below.</p>
        </div>

        <!-- Booking Summary Box -->
        <div style="background-color: #f8faff; padding: 25px 30px; border-bottom: 1px solid #f0f0f7;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="50%" style="vertical-align: top;">
                        <p style="font-size: 13px; text-transform: uppercase; color: #888; margin: 0 0 5px 0;">Booking
                            Reference</p>
                        <p style="font-size: 20px; color: #97050C; font-weight: 500; margin: 0;">
                            {{ $bookingData['pnr'] ?? 'N/A' }}
                        </p>
                    </td>
                    {{-- <td width="50%" style="vertical-align: top; text-align: right;">
                        <p style="font-size: 13px; text-transform: uppercase; color: #888; margin: 0 0 5px 0;">Total Amount</p>
                        <p style="font-size: 20px; color: #97050C; font-weight: 500; margin: 0;">
                            {{ isset($bookingData['price']['grandTotal']) ? number_format($bookingData['price']['grandTotal'] + $bookingData['commission'], 2) : 'N/A' }}
                    {{ $bookingData['price']['currency'] ?? '' }}
                    </p>
                    </td> --}}
                </tr>
            </table>
        </div>



        <!-- Flight Itinerary Section -->
        <div style="padding: 30px 30px 10px 30px; border-bottom: 1px solid #f0f0f7;">
            <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 18px;">
                <img src="https://img.icons8.com/material-rounded/24/97050C/airplane-mode-on.png" alt="✈"
                    style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;">
                Flight Itinerary
            </h2>


            @if (!empty($bookingData['itineraries']))
            @foreach ($bookingData['itineraries'] as $index => $itinerary)
            <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f7;">
                <div style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 5px;">
                    {{ $index === 0 ? 'Depature' : 'Return' }} Flight
                </div>
                <div style="font-size: 13px; color: #0a0a0a;">
                    {{ $itinerary['segments'][0]['departure']['city_country'] ?? 'N/A' }}
                    <span style="margin: 0 8px; color: #888;">to</span>
                    {{ (isset($itinerary['segments']) && count($itinerary['segments']) > 0) ? (end($itinerary['segments'])['arrival']['city_country'] ?? 'N/A') : 'N/A' }}
                </div>
            </div>

            <!-- Itinerary Details -->
            <div class="mb-6 last:mb-0">

                @foreach ($itinerary['segments'] as $segIndex => $segment)
                <!-- Flight Card -->
                <div
                    style="margin-bottom: 30px; border: 1px solid #e7eeff; border-radius: 8px; overflow: hidden; background-color: #ffffff;">
                    <div
                        style="background-color: #f8faff; padding: 10px; border-bottom: 1px solid #e7eeff;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="20%" style="vertical-align: middle; text-align: center;">
                                    <img src="{{ !empty($segment['airline']['logo'])
                                                    ? url('/').'/storage/' . str_replace(' ', '%20', $segment['airline']['logo'])
                                                    : asset('images/default-airline-logo.png') }}"
                                        alt="{{ $segment['airline']['name'] ?? 'Airline' }}"
                                        style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td width="80%" style="vertical-align: middle; padding-left: 10px;">
                                    <span
                                        style="font-size: 16px; font-weight: 600; color: #333; display: block;">
                                        {{ $segment['airline']['name'] ?? 'Unknown Airline' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <!-- Departure -->
                                <td style="vertical-align: top; padding-right: 15px;">
                                    <!-- Airline Logo & Name at the top -->

                                    <!-- Departure Info below -->
                                    <div style="width: 100%;">
                                        <p
                                            style="font-size: 13px; text-transform: uppercase; color: #888; margin: 0;">
                                            Departure
                                        </p>
                                        <h3
                                            style="font-size: 14px; font-weight: bold; color: #333; margin: 0;">
                                            {{ $segment['departure']['city_country'] ?? 'N/A' }}
                                            ({{ $segment['departure']['iataCode'] ?? 'N/A' }})
                                        </h3>
                                        <p
                                            style="font-size: 12px; color: #97050C; margin: 0; font-weight: 500;">
                                            {{ $segment['departure']['airport'] ?? 'N/A' }}
                                        </p>
                                        <p style="font-size: 14px; color: #777; margin: 0;">
                                            {{ \Carbon\Carbon::parse($segment['departure']['at'])->format('D, M d, Y H:i') }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            <tr>

                                <!-- Flight Path -->
                                <td width="100%" style="vertical-align: middle; text-align: center;">
                                    <div style="position: relative; padding: 15px 0;">
                                        <img src="https://img.icons8.com/ios-filled/50/97050C/airplane-mode-on.png"
                                            alt="✈"
                                            style="width: 24px; height: 24px; transform: rotate(90deg); margin-top: 10px;">
                                    </div>
                                </td>
                            </tr>
                            <tr>

                                <!-- Arrival -->
                                <td width="100%"
                                    style="vertical-align: top; padding-left: 15px; text-align: right;">
                                    <div
                                        style="display: flex; align-items: center; justify-content: flex-end;">
                                        <div>
                                            <p
                                                style="font-size: 13px; text-transform: uppercase; color: #888; margin: 0 0 5px 0;">
                                                Arrival</p>
                                            <h3
                                                style="font-size: 14px; font-weight: bold; color: #333; margin: 0 0 5px 0;">
                                                {{ $segment['arrival']['city_country'] ?? 'N/A' }}
                                                ({{ $segment['arrival']['iataCode'] ?? 'N/A' }})
                                            </h3>
                                            <p
                                                style="font-size: 12px; color: #97050C; margin: 0 0 5px 0; font-weight: 500;">
                                                {{ $segment['arrival']['airport'] ?? 'N/A' }}
                                            </p>
                                            <p style="font-size: 14px; color: #777; margin: 0;">
                                                {{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('D, M d, Y H:i') }}
                                            </p>
                                        </div>


                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Flight Details Footer -->
                    {{-- <div style="background-color: #f8faff; padding: 15px; border-top: 1px solid #e7eeff;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="50%" style="vertical-align: top;">
                                            <p style="margin: 0; font-size: 13px; color: #666;">
                                                <strong style="color: #444;">Aircraft:</strong> {{ $segment['aircraft']['code'] ?? 'N/A' }}
                    </p>
                    </td>
                    <td width="50%" style="vertical-align: top; text-align: right;">
                        <p style="margin: 0; font-size: 13px; color: #666;">
                            <strong style="color: #444;">Stops:</strong> {{ $segment['numberOfStops'] > 0 ? $segment['numberOfStops'] : 'Non-stop' }}
                        </p>
                    </td>
                    </tr>
                    </table>
                </div> --}}
            </div>

            <!-- Layover Information -->
            @if (isset($itinerary['segments'][$segIndex + 1]))
            @php
            $currentArrival = \Carbon\Carbon::parse($segment['arrival']['at']);
            $nextDeparture = \Carbon\Carbon::parse(
            $itinerary['segments'][$segIndex + 1]['departure']['at'],
            );
            $layoverDiff = $currentArrival->diff($nextDeparture);
            $layoverHours = $layoverDiff->h;
            $layoverMinutes = $layoverDiff->i;
            $layoverDuration =
            ($layoverHours > 0 ? $layoverHours . 'h ' : '') .
            ($layoverMinutes > 0 ? $layoverMinutes . 'm' : '');

            $isNightLayover = $currentArrival->hour >= 18 || $nextDeparture->hour <= 6;
                $totalMinutes=$layoverHours * 60 + $layoverMinutes;

                if ($layoverHours>= 8) {
                $layoverType = $isNightLayover ? 'Overnight Stop' : 'Very Long Stopover';
                $severityColor = '#FEE2E2';
                $textColor = '#B91C1C';
                } elseif ($layoverHours >= 6) {
                $layoverType = $isNightLayover ? 'Night Stopover' : 'Long Stopover';
                $severityColor = '#FEF3C7';
                $textColor = '#B45309';
                } elseif ($layoverHours >= 4) {
                $layoverType = $isNightLayover ? 'Evening Break' : 'Extended Break';
                $severityColor = '#DBEAFE';
                $textColor = '#1E40AF';
                } elseif ($totalMinutes >= 90) {
                $layoverType = 'Comfortable Break';
                $severityColor = '#D1FAE5';
                $textColor = '#047857';
                } elseif ($totalMinutes >= 45) {
                $layoverType = '45 Min Break - Move Quickly';
                $severityColor = '#FEF3C7';
                $textColor = '#B45309';
                } else {
                $layoverType = 'Rush Transfer - Very Tight';
                $severityColor = '#FEE2E2';
                $textColor = '#B91C1C';
                }

                $warningMessage = '';
                if ($totalMinutes < 90) {
                    $warningMessage=$totalMinutes < 45
                    ? 'This is an extremely tight transfer. Check with your airline if this is feasible.'
                    : 'Pack light and be prepared to move quickly between gates.' ;
                    }
                    @endphp

                    <div
                    style="margin-bottom: 30px; padding: 15px; border-radius: 8px; background-color: {{ $severityColor }}; border: 1px solid {{ $textColor }};">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="24" style="vertical-align: top;">
                                @if ($isNightLayover)
                                <img src="https://img.icons8.com/material-rounded/24/{{ substr($textColor, 1) }}/moon.png"
                                    alt="Night" style="width: 24px; height: 24px;">
                                @else
                                <img src="https://img.icons8.com/material-rounded/24/{{ substr($textColor, 1) }}/sun.png"
                                    alt="Day" style="width: 24px; height: 24px;">
                                @endif
                            </td>
                            <td style="padding-left: 15px;">
                                <p
                                    style="margin: 0 0 5px 0; font-size: 16px; font-weight: 500; color: {{ $textColor }};">
                                    {{ $layoverType }}
                                    <span
                                        style="display: inline-block; margin-left: 10px; padding: 2px 8px; background-color: {{ $severityColor }}; border: 1px solid {{ $textColor }}; border-radius: 12px; font-size: 12px;">
                                        {{ $layoverDuration }}
                                    </span>
                                </p>
                                <p style="margin: 0; font-size: 14px; color: #555;">
                                    <span style="margin-right: 10px; font-size: 13px; color: #777;">
                                        Layover In
                                    </span>
                                    <img src="https://img.icons8.com/material-rounded/24/555555/marker.png"
                                        alt="Location"
                                        style="width: 16px; height: 16px; vertical-align: middle; margin-right: 5px;">
                                    {{ $segment['arrival']['city_country'] ?? 'N/A' }}
                                    ({{ $segment['arrival']['iataCode'] ?? 'N/A' }} )
                                    <span style="margin-left: 10px; font-size: 13px; color: #777;">
                                        {{ $currentArrival->format('H:i') }} -
                                        {{ $nextDeparture->format('H:i') }}
                                    </span>
                                </p>
                                @if ($warningMessage)
                                <p
                                    style="margin: 10px 0 0 0; font-size: 13px; color: {{ $textColor }}; font-style: italic;">
                                    {{ $warningMessage }}
                                </p>
                                @endif
                            </td>
                        </tr>
                    </table>
        </div>
        @endif
        @endforeach
        @endforeach
        @else
        <div
            style="text-align: center; padding: 25px; background-color: #f8faff; border-radius: 8px; margin-bottom: 30px;">
            <p style="font-size: 16px; color: #666; font-style: italic; margin: 0;">No itinerary available.</p>
        </div>
        @endif
    </div>

    <div style="padding: 30px 30px 10px 30px; border-bottom: 1px solid #f0f0f7;">
        <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 12px;">
            <img src="https://img.icons8.com/material-rounded/24/97050C/user.png" alt="👤"
                style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;">
            Traveler Details
        </h2>

        @foreach ($bookingData['travelers'] as $index => $traveler)
        <div style="margin-bottom: 1px; border: 1px solid #e7eeff; border-radius: 8px; overflow: hidden;">
            <!-- Traveler Header -->
            <div style="background-color: #f8faff; padding: 2px; border-bottom: 1px solid #e7eeff;">
                <p style="margin: 0; font-size: 12px; font-weight: 500; color: #333;">
                    Traveler {{ $index + 1 }}:
                    {{ strtoupper($traveler['name']['firstName']) }}
                    {{ strtoupper($traveler['name']['lastName']) }}
                </p>
            </div>

            <!-- Traveler Content -->
            {{--<div style="padding: 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="50%" style="vertical-align: top; padding-right: 15px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                        style="margin-bottom: 20px;">
                                        <tr>
                                            <td colspan="2">
                                                <p
                                                    style="font-size: 14px; text-transform: uppercase; color: #888; margin: 0 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                                    Personal Information</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%" style="padding: 5px 0;">
                                                <p style="margin: 0; font-size: 14px; color: #666;">Date of Birth:</p>
                                            </td>
                                            <td width="60%" style="padding: 5px 0;">
                                                <p style="margin: 0; font-size: 14px; color: #333;">
                                                    {{ $traveler['dateOfBirth'] ?? 'N/A' }}</p>
            </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 5px 0;">
                    <p style="margin: 0; font-size: 14px; color: #666;">Gender:</p>
                </td>
                <td width="60%" style="padding: 5px 0;">
                    <p style="margin: 0; font-size: 14px; color: #333;">
                        {{ $traveler['gender'] ?? 'N/A' }}
                    </p>
                </td>
            </tr>
            </table>
            </td>
            <td width="50%" style="vertical-align: top; padding-left: 15px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="margin-bottom: 20px;">
                    <tr>
                        <td colspan="2">
                            <p
                                style="font-size: 14px; text-transform: uppercase; color: #888; margin: 0 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                Contact Information</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" style="padding: 5px 0;">
                            <p style="margin: 0; font-size: 14px; color: #666;">Email:</p>
                        </td>
                        <td width="70%" style="padding: 5px 0;">
                            <p style="margin: 0; font-size: 14px; color: #333;">
                                {{ $traveler['contact']['emailAddress'] ?? 'N/A' }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" style="padding: 5px 0;">
                            <p style="margin: 0; font-size: 14px; color: #666;">Phone:</p>
                        </td>
                        <td width="70%" style="padding: 5px 0;">
                            <p style="margin: 0; font-size: 14px; color: #333;">
                                +{{ $traveler['contact']['phones'][0]['countryCallingCode'] ?? '' }}
                                {{ $traveler['contact']['phones'][0]['number'] ?? 'N/A' }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
            </tr>
            </table>

            <!-- Documents Section -->
            <p
                style="font-size: 14px; text-transform: uppercase; color: #888; margin: 20px 0 15px 0; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                Travel Documents</p>

            @if (!empty($traveler['documents']))
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach ($traveler['documents'] as $docIndex => $doc)
                @if ($docIndex % 2 == 0)
                <tr>
                    @endif
                    <td width="50%"
                        style="vertical-align: top; padding: 0 10px 20px {{ $docIndex % 2 == 0 ? '10px' : '0' }};">
                        <div
                            style="background-color: #f8faff; border-radius: 6px; padding: 15px; border: 1px solid #e7eeff;">
                            <p
                                style="font-size: 15px; font-weight: 500; color: #97050C; margin: 0 0 10px 0;">
                                {{ $doc['documentType'] ?? 'Document' }}
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="40%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #666;">Number:</p>
                                    </td>
                                    <td width="60%" style="padding: 4px 0;">
                                        <p
                                            style="margin: 0; font-size: 13px; color: #333; font-weight: 500;">
                                            {{ $doc['number'] ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #666;">Issued:</p>
                                    </td>
                                    <td width="60%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #333;">
                                            {{ $doc['issuanceDate'] ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #666;">Expires:
                                        </p>
                                    </td>
                                    <td width="60%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #333;">
                                            {{ $doc['expiryDate'] ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #666;">Country:
                                        </p>
                                    </td>
                                    <td width="60%" style="padding: 4px 0;">
                                        <p style="margin: 0; font-size: 13px; color: #333;">
                                            {{ $doc['issuanceCountry'] ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    @if ($docIndex % 2 == 1 || $loop->last)
                </tr>
                @endif
                @endforeach
            </table>
            @else
            <p style="font-size: 15px; color: #666; font-style: italic; margin: 0;">No document
                information available.</p>
            @endif
        </div>--}}
    </div>
    @endforeach
    </div>

    <!-- Rest of the email content (Traveler Details, Pricing Breakdown, etc.) remains the same -->

    <!-- Pricing Breakdown Section -->
    {{-- <div style="padding: 30px 30px 10px 30px; border-bottom: 1px solid #f0f0f7;">
                <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 18px;">
                    <img src="https://img.icons8.com/material-rounded/24/0047AB/money.png" alt="💰" style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;"> 
                    Pricing Breakdown
                </h2>
                
                <div style="border: 1px solid #e7eeff; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="padding: 15px 20px; border-bottom: 1px solid #e7eeff;">
                                <p style="margin: 0; font-size: 15px; color: #555;">Base Price</p>
                            </td>
                            <td style="padding: 15px 20px; border-bottom: 1px solid #e7eeff; text-align: right;">
                                <p style="margin: 0; font-size: 15px; font-weight: 500; color: #333;">{{ $bookingData['price']['base'] ?? 'N/A' }} {{ $bookingData['price']['currency'] ?? '' }}</p>
    </td>
    </tr>

    @if (!empty($bookingData['price']['taxes']))
    <tr>
        <td colspan="2" style="padding: 15px 20px 0 20px;">
            <p style="margin: 0 0 10px 0; font-size: 15px; color: #555;">Taxes and Fees</p>
        </td>
    </tr>
    @foreach ($bookingData['price']['taxes'] as $tax)
    <tr>
        <td style="padding: 5px 20px 5px 40px;">
            <p style="margin: 0; font-size: 14px; color: #777;">{{ $tax['code'] ?? 'Tax' }}</p>
        </td>
        <td style="padding: 5px 20px 5px 20px; text-align: right;">
            <p style="margin: 0; font-size: 14px; color: #333;">{{ $tax['amount'] ?? 'N/A' }} {{ $bookingData['price']['currency'] ?? '' }}</p>
        </td>
    </tr>
    @endforeach
    <tr>
        <td colspan="2" style="padding: 0 20px;">
            <div style="height: 1px; background-color: #e7eeff; margin: 10px 0;"></div>
        </td>
    </tr>
    @endif

    <tr style="background-color: #f8faff;">
        <td style="padding: 18px 20px;">
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #0047AB;">Total Amount</p>
        </td>
        <td style="padding: 18px 20px; text-align: right;">
            <p style="margin: 0; font-size: 18px; font-weight: 600; color: #0047AB;">{{ number_format($bookingData['price']['grandTotal'] + $bookingData['commission'], 2) ?? 'N/A' }} {{ $bookingData['price']['currency'] ?? '' }}</p>
        </td>
    </tr>
    </table>
    </div>
    </div> --}}

    <!-- Remarks Section -->
    <div style="padding: 30px 30px 30px 30px;">
        <h2 style="color: #333; font-weight: 500; margin-top: 0; margin-bottom: 25px; font-size: 18px;">
            <img src="https://img.icons8.com/material-rounded/24/0047AB/info.png" alt="ℹ️"
                style="vertical-align: middle; margin-right: 10px; width: 24px; height: 24px;">
            Important Information
        </h2>

        @if (!empty($bookingData['remarks']))
        <div style="background-color: #f8faff; border-radius: 8px; padding: 20px; border: 1px solid #e7eeff;">
            @foreach ($bookingData['remarks'] as $remark)
            <p
                style="font-size: 14px; color: #555; margin: 0 0 10px 0; padding-left: 15px; border-left: 3px solid #0047AB;">
                {{ $remark['text'] ?? 'No remarks' }}
            </p>
            @endforeach
        </div>
        @else
        <div style="background-color: #f8faff; border-radius: 8px; padding: 20px; border: 1px solid #e7eeff;">
            <p style="font-size: 14px; color: #666; font-style: italic; margin: 0;">No specific remarks for
                this booking.</p>
        </div>
        @endif
    </div>

    </div>

    <!-- Travel Tips Banner -->
    <div
        style="background: linear-gradient(135deg, #f0f4ff 0%, #e5eeff 100%); border-radius: 8px; padding: 25px; margin: 20px; text-align: center; border: 1px solid #d5e0ff;">
        <h3 style="color: #97050C; margin: 0 0 15px 0; font-weight: 500;">Travel Tips</h3>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="33%" style="vertical-align: top; padding: 0 10px; text-align: center;">
                    <div
                        style="display: inline-block; background-color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <img src="https://img.icons8.com/material-rounded/24/97050C/clock.png" alt="⏰"
                            style="vertical-align: middle; width: 18px; height: 18px;">
                    </div>
                    <p style="font-size: 10px; color: #555; margin: 0;">Arrive at the airport<br>2 hours before
                        departure</p>
                </td>
                <td width="33%" style="vertical-align: top; padding: 0 10px; text-align: center;">
                    <div
                        style="display: inline-block; background-color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <img src="https://img.icons8.com/material-rounded/24/97050C/lock.png" alt="🛂"
                            style="vertical-align: middle; width: 18px; height: 18px;">
                    </div>
                    <p style="font-size: 10px; color: #555; margin: 0;">Keep your ID and<br>boarding pass handy</p>
                </td>
                <td width="33%" style="vertical-align: top; padding: 0 10px; text-align: center;">
                    <div
                        style="display: inline-block; background-color: white; border-radius: 50%; width: 50px; height: 50px; line-height: 50px; margin-bottom: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                        <img src="https://img.icons8.com/material-rounded/24/97050C/suitcase.png" alt="🧳"
                            style="vertical-align: middle; width: 18px; height: 18px;">
                    </div>
                    <p style="font-size: 10px; color: #555; margin: 0;">Check luggage<br>requirements</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div
        style="background-color: #97050C; padding: 40px 20px; text-align: center; color: white; border-radius: 0 0 8px 8px; margin: 0 20px 20px 20px;">
        <h3 style="margin: 0 0 20px 0; font-weight: 300; letter-spacing: 1px; color: #ffffff;">Thank you for choosing
            Mymore Travels!</h3>
        <p style="margin: 0 0 25px 0; font-size: 14px; color: #f0f0f0;">Have a safe and pleasant journey</p>

        <div style="margin: 0 0 25px 0;">
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/facebook-f.png" alt="fb"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/twitter.png" alt="tw"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
            <a href="#"
                style="display: inline-block; width: 36px; height: 36px; line-height: 36px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 5px; text-decoration: none;">
                <img src="https://img.icons8.com/material-rounded/24/ffffff/instagram-new.png" alt="ig"
                    style="vertical-align: middle; width: 16px; height: 16px;">
            </a>
        </div>
        <p style="color: #f0f0f0; font-size: 12px; margin: 20px 0 0 0;">© 2025 Mymore Travels. All rights reserved.</p>
        <p style="color: #f0f0f0; font-size: 12px; margin: 5px 0 0 0;">Suite A061 H and A Plaza, Wuye, Abuja</p>
    </div>
</body>

</html>