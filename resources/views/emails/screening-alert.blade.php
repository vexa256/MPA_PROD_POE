@component('mail::message')
    # Alert: {{ $screeningData['classification'] }} - POE Screening Notification

    Dear Ministry of Health Officials,

    A traveler has been classified as **{{ $screeningData['classification'] }}** during a secondary screening at a Point of
    Entry. Please review the following information and take appropriate action.

    ## Screening Details

    - **Screening ID**: {{ $screeningData['screening_id'] }}
    - **POE Name**: {{ $screeningData['poe_name'] }}
    - **POE Type**: {{ $screeningData['poe_type'] }}
    - **POE Location**: {{ $screeningData['poe_district'] }}, {{ $screeningData['poe_province'] }}
    - **Screening Date**: {{ date('Y-m-d H:i:s') }}

    ## Traveler Information

    - **Name**: {{ $screeningData['traveller_name'] }}
    - **Age**: {{ $screeningData['age'] }}
    - **Gender**: {{ $screeningData['gender'] }}
    - **Departure Country**: {{ $screeningData['departure_country'] }}
    - **Travel Destination**: {{ $screeningData['travel_destination'] }}
    - **Arrival Date**: {{ $screeningData['arrival_date'] }}

    ## Health Assessment

    - **Classification**: {{ $screeningData['classification'] }}

    - **Symptoms**: {{ $screeningData['symptoms'] }}
    - **Suspected Diseases**: {{ $screeningData['suspected_diseases'] }}
    - **Endemic Warning**: {{ $screeningData['endemic_warning'] }}
    - **High Risk Alert**: {{ $screeningData['high_risk_alert'] ? 'Yes' : 'No' }}

    ## Recommended Action

    {{ $screeningData['recommended_action'] }}

    ## Additional Notes

    {{ $screeningData['additional_notes'] }}

    ## Referral Status

    {{ $screeningData['referral_status'] }}
    @if ($screeningData['referral_status'] === 'Referred')
        - **Referral Province**: {{ $screeningData['referral_province'] }}
        - **Referral District**: {{ $screeningData['referral_district'] }}
        - **Referral Hospital**: {{ $screeningData['referral_hospital'] }}
    @endif

    For more detailed information and real-time updates, please check the National POE Screening Dashboard:

    @component('mail::button', ['url' => $dashboardUrl])
        View POE Dashboard
    @endcomponent

    Please take immediate action as necessary based on the classification and recommended actions. If you have any questions
    or need additional information, please contact the screening officer or POE management.

    Thank you for your prompt attention to this matter.

    Best regards,
    POE Screening System
@endcomponent
