<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactFormRequest $request)
    {
        try {
            // Get validated data
            $validated = $request->validated();
            
            // Verify captcha from session
            $sessionCaptcha = session('captcha_answer');
            if (!$sessionCaptcha || (int)$validated['captcha_answer'] !== (int)$sessionCaptcha) {
                return response()->json([
                    'success' => false,
                    'errors' => ['captcha_answer' => ['The captcha answer is incorrect.']]
                ], 422);
            }
            
            // Clear the captcha from session after successful validation
            session()->forget('captcha_answer');
            
            // Prepare contact data
            $contactData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'] ?? 'Contact Form Inquiry',
                'message' => $validated['message'],
                'submitted_at' => now(),
            ];

            // Send email to the configured contact email
            $contactEmail = config('mail.contact_email', config('mail.from.address'));
            Mail::to($contactEmail)->send(new ContactFormMail($contactData));

            Log::info('Contact form submitted successfully', [
                'name' => $contactData['name'],
                'email' => $contactData['email'],
                'subject' => $contactData['subject']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We\'ll get back to you soon.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again later.'
            ], 500);
        }
    }

    public function getCaptcha()
    {
        // Generate simple math captcha
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operations = [
            '+' => $num1 + $num2,
            '-' => max($num1, $num2) - min($num1, $num2), // Ensure positive result
            '×' => $num1 * $num2,
        ];
        
        $operation = array_rand($operations);
        $answer = $operations[$operation];
        
        // For subtraction, make sure we use the larger number first
        if ($operation === '-') {
            $question = max($num1, $num2) . ' ' . $operation . ' ' . min($num1, $num2);
        } else {
            $question = $num1 . ' ' . $operation . ' ' . $num2;
        }
        
        // Store answer in session
        session(['captcha_answer' => $answer]);
        
        return response()->json([
            'question' => $question
        ]);
    }
}