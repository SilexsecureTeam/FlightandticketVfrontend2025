<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f5ff;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f0f5ff;">
    <div style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        <!-- Success Card -->
        <div style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <!-- Top Success Banner -->
            <div style="background-color: #97050C; padding: 24px 32px; position: relative; overflow: hidden;">
                <!-- White circle with check mark -->
                <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                    <div style="background-color: #ffffff; border-radius: 50%; padding: 10px; display: inline-flex; justify-content: center; align-items: center;">
                        <svg style="width: 48px; height: 48px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h1 style="text-align: center; font-size: 28px; font-weight: bold; color: #ffffff; margin: 0;">Offline Reservation Confirmed!</h1>
                <p style="text-align: center; color: #e0f2fe; margin-top: 8px; margin-bottom: 0;">Your Offline Reservation has been Confirmed</p>
            </div>

            <!-- Payment Details -->
            <div style="padding: 24px 32px;">
                <!-- Transaction ID -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background-color: #f9fafb; border-radius: 12px; margin-bottom: 24px;">
                    <div style="color: #4b5563;">Order ID</div>
                    <div style="font-weight: 500; color: #111827;">{{ $payment->flightOrder->order_id ?? 'null'}}</div>
                </div>

                <!-- Amount Paid -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <span style="font-size: 32px; font-weight: bold; color: #111827;">NGN {{ number_format($payment->amount + $payment->commission ?? 0) }}</span>
                    <p style="color: #6b7280; margin-top: 4px;">Total Amount Paid</p>
                </div>

                <!-- Payment Instructions Alert -->
                <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start;">
                        <!-- Warning Icon -->
                        <div>
                            <svg style="width: 24px; height: 24px; color: #d97706;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div style="margin-left: 16px; width: 100%;">
                            <h3 style="font-size: 14px; font-weight: 500; color: #92400e; margin-top: 0; margin-bottom: 8px;">Important Payment Information</h3>
                            <div style="font-size: 14px; color: #92400e;">
                                <p style="margin-top: 0;">Please make payment to one of the accounts below:</p>
                                
                                <!-- Bank Accounts -->
                                <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 12px;">
                                    <!-- Primary Account -->
                                    <div style="flex: 1; min-width: 250px; background-color: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #fef3c7; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                        <p style="font-weight: bold; color: #92400e; margin-top: 0; margin-bottom: 4px;">Primary Account</p>
                                        <p style="font-weight: 600; margin-top: 4px; margin-bottom: 4px;">Wema Bank</p>
                                        <div style="display: flex; justify-content: space-between; margin-top: 4px; margin-bottom: 4px;">
                                            <p style="margin: 0;">Account Number:</p>
                                            <p style="font-weight: bold; margin: 0;">0125048653</p>
                                        </div>
                                        <p style="margin-top: 4px; margin-bottom: 0;">MYMORE TRAVELS SERVICES LTD</p>
                                    </div>
                                    
                                    <!-- Alternative Account -->
                                    <div style="flex: 1; min-width: 250px; background-color: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #fef3c7; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                        <p style="font-weight: bold; color: #92400e; margin-top: 0; margin-bottom: 4px;">Alternative Account</p>
                                        <p style="font-weight: 600; margin-top: 4px; margin-bottom: 4px;">Alternative Bank</p>
                                        <div style="display: flex; justify-content: space-between; margin-top: 4px; margin-bottom: 4px;">
                                            <p style="margin: 0;">Account Number:</p>
                                            <p style="font-weight: bold; margin: 0;">0505820902</p>
                                        </div>
                                        <p style="margin-top: 4px; margin-bottom: 0;">Mymore Travels Limited</p>
                                    </div>
                                </div>
                                
                                <!-- Warning -->
                                <div style="margin-top: 16px; padding: 12px; background-color: #fee2e2; border-radius: 8px; border: 1px solid #fecaca;">
                                    <p style="font-weight: 600; color: #dc2626; margin: 0;">⚠️ If payment is not confirmed within 48 hours, your flight reservation will be cancelled.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                    <!-- Header -->
                    <div style="background-color: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb;">
                        <h3 style="font-size: 16px; font-weight: 500; color: #4b5563; margin: 0;">Payment Details</h3>
                    </div>
                    
                    <!-- Details Grid -->
                    <div style="display: flex; flex-wrap: wrap;">
                        <!-- Payment Date -->
                        <div style="flex: 1; min-width: 250px; padding: 24px; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; display: flex; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; color: #9ca3af; margin-top: 4px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div style="margin-left: 16px;">
                                <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin-top: 0; margin-bottom: 4px;">Payment Date</p>
                                <p style="font-size: 16px; font-weight: 500; color: #111827; margin: 0;">{{ now()->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        
                        <!-- Payment Type -->
                        <div style="flex: 1; min-width: 250px; padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; color: #9ca3af; margin-top: 4px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <div style="margin-left: 16px;">
                                <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin-top: 0; margin-bottom: 4px;">Payment Type</p>
                                <p style="font-size: 16px; font-weight: 500; color: #111827; margin: 0;">{{ $payment->provider ?? 'Offline Payment' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap;">
                        <!-- Email -->
                        <div style="flex: 1; min-width: 250px; padding: 24px; border-right: 1px solid #e5e7eb; display: flex; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; color: #9ca3af; margin-top: 4px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            <div style="margin-left: 16px;">
                                <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin-top: 0; margin-bottom: 4px;">Email</p>
                                <p style="font-size: 16px; font-weight: 500; color: #111827; margin: 0; word-break: break-all;">{{ $payment->email ?? 'Email' }}</p>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        {{-- <div style="flex: 1; min-width: 250px; padding: 24px; display: flex; align-items: flex-start;">
                            <svg style="width: 20px; height: 20px; color: #9ca3af; margin-top: 4px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div style="margin-left: 16px;">
                                <p style="font-size: 14px; font-weight: 500; color: #6b7280; margin-top: 0; margin-bottom: 8px;">Status</p>
                                <div>
                                    <span style="display: inline-flex; align-items: center; background-color: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 9999px; font-size: 14px; font-weight: 500;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #059669; margin-right: 6px;"></span>
                                        Confirmed
                                    </span>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

              
                <!-- Action Button -->
             
            </div>

          
        </div>

        <!-- Download Button (hidden for PDF version) -->
        <!-- <div style="text-align: center; margin-top: 32px;">
            <button style="display: inline-flex; align-items: center; padding: 10px 20px; background-color: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </button>
        </div> -->
    </div>
</body>
</html>