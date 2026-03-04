<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration and email verification flow
     */
    public function test_user_registration()
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'user_type', 'email_verified_at'],
                'email_sent',
            ])
            ->assertJson([
                'user' => [
                    'email' => 'john@example.com',
                    'name' => 'John Doe',
                    'user_type' => 'student',
                ],
            ]);

        // Verify user was created
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test user cannot login without email verification
     */
    public function test_user_cannot_login_without_verification()
    {
        Mail::fake();

        // Register user
        $this->postJson('/api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'user_type' => 'instructor',
        ]);

        // Try to login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'SecurePassword123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'email_verified' => false,
            ]);
    }

    /**
     * Test resending verification email
     */
    public function test_resend_verification_email()
    {
        Mail::fake();

        // Register user
        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'user_type' => 'parent',
        ]);

        // Resend verification email
        $response = $this->postJson('/api/auth/resend-verification', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['email_sent' => true]);
    }

    /**
     * Test email verification with signed URL
     */
    public function test_verify_email_with_signed_url()
    {
        // Create unverified user
        $user = User::factory()->create([
            'email' => 'verify@test.com',
            'email_verified_at' => null,
        ]);

        // Generate verification token
        $hash = sha1($user->getEmailForVerification());

        $response = $this->postJson('/api/auth/verify-email', [
            'id' => $user->id,
            'hash' => $hash,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verified successfully']);

        // Verify the user's email was marked as verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    /**
     * Test login after email verification
     */
    public function test_user_can_login_after_email_verification()
    {
        // Create verified user
        $user = User::factory()->create([
            'email' => 'verified@test.com',
            'password' => bcrypt('SecurePassword123'),
        ]);

        // User should be able to login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'verified@test.com',
            'password' => 'SecurePassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token', 'user']);
    }

    /**
     * Test invalid email format during registration
     */
    public function test_registration_with_invalid_email()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Invalid Email',
            'email' => 'not-an-email',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test weak password during registration
     */
    public function test_registration_with_weak_password()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Weak Password',
            'email' => 'weak@test.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'user_type' => 'student',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test duplicate email during registration
     */
    public function test_registration_with_duplicate_email()
    {
        // Create first user
        User::factory()->create([
            'email' => 'duplicate@test.com',
        ]);

        // Try to register with same email
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Duplicate Email',
            'email' => 'duplicate@test.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test resend verification for non-existent user
     */
    public function test_resend_verification_for_invalid_email()
    {
        $response = $this->postJson('/api/auth/resend-verification', [
            'email' => 'nonexistent@test.com',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Test cannot verify email twice
     */
    public function test_cannot_verify_email_twice()
    {
        // Create verified user
        $user = User::factory()->create([
            'email' => 'already-verified@test.com',
        ]);

        $hash = sha1($user->getEmailForVerification());

        // Try to verify again
        $response = $this->postJson('/api/auth/verify-email', [
            'id' => $user->id,
            'hash' => $hash,
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Email already verified']);
    }

    /**
     * Test logout functionality
     */
    public function test_user_logout()
    {
        // Create and authenticate user
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }

    /**
     * Test registration with different user types
     */
    public function test_registration_with_different_user_types()
    {
        Mail::fake();

        $userTypes = ['student', 'instructor', 'parent'];

        foreach ($userTypes as $type) {
            $response = $this->postJson('/api/auth/register', [
                'name' => 'User ' . ucfirst($type),
                'email' => "{$type}@example.com",
                'password' => 'SecurePassword123',
                'password_confirmation' => 'SecurePassword123',
                'user_type' => $type,
            ]);

            $response->assertStatus(201);

            $user = User::where('email', "{$type}@example.com")->first();
            $this->assertEquals($type, $user->user_type);
        }
    }
}
