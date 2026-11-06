<?php

namespace Tests\Feature;

use App\Mail\RegistrationOtpMail;
use App\Models\Department;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class HospitalRegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    private array $validStep1 = [
        'hospital_name' => 'St. Nicholas Specialist Hospital',
        'hospital_type' => 'Specialist Hospital',
        'hospital_email' => 'contact@stnicholas.org',
        'hospital_phone' => '+2348011223344',
        'country' => 'Nigeria',
        'state' => 'Lagos',
        'lga' => 'Lagos Island',
        'hospital_address' => '57 Campbell Street, Lagos Island',
        'registration_number' => 'RC-998877',
        'website' => 'https://stnicholas.org',
    ];

    private array $validStep2 = [
        'first_name' => 'Adewale',
        'last_name' => 'Johnson',
        'email' => 'dr.adewale@stnicholas.org',
        'phone' => '+2348099887766',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ];

    public function test_verify_email_generates_and_mails_6_digit_otp(): void
    {
        Mail::fake();

        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
        ])->get('/register/verify-email');

        $response->assertStatus(200);
        $response->assertSeeText('Verify Your Email');
        $response->assertSeeText('dr.adewale@stnicholas.org');

        $response->assertSessionHas('register.otp');
        $otpData = session('register.otp');
        $this->assertIsArray($otpData);
        $this->assertEquals(6, strlen((string) $otpData['code']));

        Mail::assertSent(RegistrationOtpMail::class, function ($mail) {
            return $mail->hasTo('dr.adewale@stnicholas.org') &&
                   $mail->hospitalName === 'St. Nicholas Specialist Hospital' &&
                   $mail->adminName === 'Adewale Johnson';
        });
    }

    public function test_send_otp_route_from_review_dispatches_email_and_redirects(): void
    {
        Mail::fake();

        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
        ])->post('/register/send-otp');

        $response->assertRedirect('/register/verify-email');
        Mail::assertSent(RegistrationOtpMail::class);
    }

    public function test_complete_registration_fails_with_invalid_otp(): void
    {
        Mail::fake();

        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
            'register.otp' => [
                'code' => '654321',
                'expires_at' => now()->addMinutes(15),
                'sent_at' => now(),
            ],
        ])->post('/register/complete', [
            'otp' => '000000',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertDatabaseMissing('hospitals', [
            'name' => 'St. Nicholas Specialist Hospital',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'dr.adewale@stnicholas.org',
        ]);
        $this->assertFalse(Auth::check());
    }

    public function test_complete_registration_fails_with_expired_otp(): void
    {
        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
            'register.otp' => [
                'code' => '654321',
                'expires_at' => now()->subMinute(),
                'sent_at' => now()->subMinutes(16),
            ],
        ])->post('/register/complete', [
            'otp' => '654321',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertDatabaseMissing('hospitals', [
            'name' => 'St. Nicholas Specialist Hospital',
        ]);
    }

    public function test_complete_registration_succeeds_with_valid_otp(): void
    {
        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
            'register.otp' => [
                'code' => '789123',
                'expires_at' => now()->addMinutes(15),
                'sent_at' => now(),
            ],
        ])->post('/register/complete', [
            'otp' => '789123',
        ]);

        $response->assertRedirect(route('register.ready'));

        // Hospital created
        $hospital = Hospital::where('name', 'St. Nicholas Specialist Hospital')->first();
        $this->assertNotNull($hospital);
        $this->assertEquals('contact@stnicholas.org', $hospital->email);
        $this->assertEquals('active', $hospital->status);

        // Admin User created and email verified
        $admin = User::where('email', 'dr.adewale@stnicholas.org')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('hospital_admin', $admin->role);
        $this->assertEquals($hospital->id, $admin->hospital_id);
        $this->assertNotNull($admin->email_verified_at);

        // Default departments created
        $this->assertTrue(Department::where('hospital_id', $hospital->id)->where('slug', 'general-medicine')->exists());
        $this->assertTrue(Department::where('hospital_id', $hospital->id)->where('slug', 'reception')->exists());

        // Authenticated as the new administrator
        $this->assertTrue(Auth::check());
        $this->assertEquals($admin->id, Auth::id());
    }

    public function test_resend_otp_issues_fresh_code_and_resets_cooldown(): void
    {
        Mail::fake();

        $response = $this->withSession([
            'register.step1' => $this->validStep1,
            'register.step2' => $this->validStep2,
            'register.otp' => [
                'code' => '111111',
                'expires_at' => now()->addMinutes(14),
                'sent_at' => now()->subSeconds(35),
            ],
        ])->post('/register/resend-otp');

        $response->assertSessionHas('success');
        $newOtp = session('register.otp.code');
        $this->assertNotEmpty($newOtp);

        Mail::assertSent(RegistrationOtpMail::class);
    }
}
