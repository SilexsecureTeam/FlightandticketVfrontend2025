<?php

namespace App\Helpers;

class StatusHelper
{
    public const VERIFICATION_STATUSES = ['verified', 'unverified'];
    public const ACCOUNT_STATUSES = ['active', 'disabled', 'blocked'];


    // Listing Status
    public const TRANSACTION_TYPES = ['Sale', 'Rent', 'Lease', 'Booking'];
    public const DURATIONS = ['Hour', 'Day', 'Week', 'Month', 'Year', 'One_time_payment'];
    public const LISTING_FEE_PAYMENT_STATUSES = ['Paid', 'Pending', 'Failed'];
    public const STATUSES = ['Draft', 'Awaiting_review', 'Incomplete', 'Approved', 'Returned', 'Delisted'];
    public const ORDERSTATUSES = ['PROCESSING','CONFIRMED', 'COMPLETED', 'CANCELLED', 'FAILED'];

}
