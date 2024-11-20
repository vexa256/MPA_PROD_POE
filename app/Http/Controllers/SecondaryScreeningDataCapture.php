<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Message as MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SecondaryScreeningDataCapture extends Controller
{
    public function recordNewScreening(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'screening_id' => 'required|string|max:20|unique:secondary_screenings_data',
            'traveller_name' => 'required|string|max:100',
            'age' => 'nullable|integer',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'departure_country' => 'required|string|max:100',
            'travel_destination' => 'required|string|max:100',
            'arrival_date' => 'required|date',
            'transit_countries' => 'nullable|json',
            'poe_name' => 'required|string|max:100',
            'poe_type' => 'required|string|max:20',
            'poe_district' => 'required|string|max:100',
            'poe_province' => 'required|string|max:100',
            'poeid' => 'required|string|max:50',
            'screener_id' => 'required|string|max:50',
            'screener_username' => 'required|string|max:50',
            'symptoms' => 'required|json',
            'travel_exposures' => 'required|json',
            'classification' => 'required|string|max:50',
            'confidence_level' => 'nullable|string|max:20',
            'recommended_action' => 'required|string',
            'suspected_diseases' => 'nullable|json',
            'endemic_warning' => 'nullable|string|max:255',
            'high_risk_alert' => 'required|boolean',
            'referral_status' => 'required|in:Not Referred,Referred',
            'referral_province' => 'nullable|string|max:100',
            'referral_district' => 'nullable|string|max:100',
            'referral_hospital' => 'nullable|string|max:100',
            'sync_status' => 'required|in:Pending,Synchronized',
            'data_version' => 'required|string|max:10',
            'additional_notes' => 'nullable|string|max:65535',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $screening = DB::table('secondary_screenings_data')->insert($validator->validated());

            DB::commit();

            $updated = DB::table('secondary_screenings')
                ->where('screening_id', $request->screening_id)
                ->update([
                    'status' => 'completed',
                ]);

            $emailSent = false;
            $emailError = null;

            // Send email alert directly for all classifications except "Non-Case"
            if ($request->classification !== 'Non-Case') {
                $emailResult = $this->sendScreeningAlert($request->all());
                $emailSent = $emailResult['sent'];
                $emailError = $emailResult['error'];
            }

            return response()->json([
                'message' => 'Screening data saved successfully',
                'data' => $screening,
                'email_sent' => $emailSent,
                'email_error' => $emailError,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving screening data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the screening data'], 500);
        }
    }

    private function sendScreeningAlert(array $screeningData)
    {
        try {

            $recipients = [
                env('MINISTRY_OF_HEALTH_EMAIL', 'atimothy@ecsahc.org', 'poe.screenings@rbc.gov.rw'),
                'nsekuye.olivier003@gmail.com',
                'chris@ecsahc.org', 'jessrurangwa@gmail.com', 'sandrine.uwamahoro@rbc.gov.rw', 'ruyangelaurent@gmail.com', 'ckayobotsi@gmail.com', 'adeline.kabeja@rbc.gov.rw', 'poe.screenings@rbc.gov.rw',
            ];
            $subject = 'Alert: ' . $screeningData['classification'] . ' - POE Screening Notification';

            $content = $this->generateEmailContent($screeningData);

            Mail::html($content, function (MailMessage $message) use ($recipients, $subject) {
                $message->to($recipients[0]) // Primary recipient
                    ->cc(array_slice($recipients, 1)) // CC other recipients
                    ->subject($subject);
            });

            Log::info('Screening alert email sent successfully to multiple recipients for classification: ' . $screeningData['classification']);
            return ['sent' => true, 'error' => null];
        } catch (\Exception $e) {
            Log::error('Failed to send screening alert email: ' . $e->getMessage());
            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }

    private function generateEmailContent(array $screeningData)
    {
        $dashboardUrl = env('POE_DASHBOARD_URL', 'https://poe-dashboard.example.com');

        return "
        <h1>Alert: {$screeningData['classification']} - POE Screening Notification</h1>

        <p>Dear Ministry of Health Officials,</p>

        <p>A traveler has been classified as <strong>{$screeningData['classification']}</strong> during a secondary screening at a Point of Entry. Please review the following information and take appropriate action.</p>

        <h2>Screening Details</h2>
        <ul>
            <li><strong>Screening ID:</strong> {$screeningData['screening_id']}</li>
            <li><strong>POE Name:</strong> {$screeningData['poe_name']}</li>
            <li><strong>POE Type:</strong> {$screeningData['poe_type']}</li>
            <li><strong>POE Location:</strong> {$screeningData['poe_district']}, {$screeningData['poe_province']}</li>
            <li><strong>Screening Date:</strong> " . date('Y-m-d H:i:s') . "</li>
        </ul>

        <h2>Traveler Information</h2>
        <ul>
            <li><strong>Name:</strong> {$screeningData['traveller_name']}</li>
            <li><strong>Age:</strong> {$screeningData['age']}</li>
            <li><strong>Gender:</strong> {$screeningData['gender']}</li>
            <li><strong>Departure Country:</strong> {$screeningData['departure_country']}</li>
            <li><strong>Travel Destination:</strong> {$screeningData['travel_destination']}</li>
            <li><strong>Arrival Date:</strong> {$screeningData['arrival_date']}</li>
        </ul>

        <h2>Health Assessment</h2>
        <ul>
            <li><strong>Classification:</strong> {$screeningData['classification']}</li>
            <li><strong>Confidence Level:</strong> {$screeningData['confidence_level']}</li>
            <li><strong>Symptoms:</strong> {$screeningData['symptoms']}</li>
            <li><strong>Suspected Diseases:</strong> {$screeningData['suspected_diseases']}</li>
            <li><strong>Endemic Warning:</strong> {$screeningData['endemic_warning']}</li>
            <li><strong>High Risk Alert:</strong> " . ($screeningData['high_risk_alert'] ? 'Yes' : 'No') . "</li>
        </ul>

        <h2>Recommended Action</h2>
        <p>{$screeningData['recommended_action']}</p>

        <h2>Additional Notes</h2>
        <p>{$screeningData['additional_notes']}</p>

        <h2>Referral Status</h2>
        <p>{$screeningData['referral_status']}</p>
        " . ($screeningData['referral_status'] === 'Referred' ? "
        <ul>
            <li><strong>Referral Province:</strong> {$screeningData['referral_province']}</li>
            <li><strong>Referral District:</strong> {$screeningData['referral_district']}</li>
            <li><strong>Referral Hospital:</strong> {$screeningData['referral_hospital']}</li>
        </ul>
        " : "") . "

        <p>For more detailed information and real-time updates, please check the <a href=\"{$dashboardUrl}\">National POE Screening Dashboard</a>.</p>

        <p>Please take immediate action as necessary based on the classification and recommended actions. If you have any questions or need additional information, please contact the screening officer or POE management.</p>

        <p>Thank you for your prompt attention to this matter.</p>

        <p>Best regards,<br>POE Screening System</p>
        ";
    }

}
