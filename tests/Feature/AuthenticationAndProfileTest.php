<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthenticationAndProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
        Storage::fake('public');

        $dosenRole = Role::where('name', 'Dosen / Pengusul')->first();
        $adminRole = Role::where('name', 'Admin P3M')->first();

        $this->dosen = User::where('nidn_nim', '0617029201')->first();
        if (!$this->dosen) {
            $this->dosen = User::create([
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'email' => 'dosen.0617029201@harkatnegeri.ac.id',
                'nidn_nim' => '0617029201',
                'password' => Hash::make('0617029201'),
                'is_otp_verified' => true,
                'email_verified_at' => now(),
            ]);
        } else {
            $this->dosen->update([
                'password' => Hash::make('0617029201'),
                'is_otp_verified' => true,
            ]);
        }
        $this->dosen->roles()->syncWithoutDetaching([$dosenRole->id]);

        $this->admin = User::where('email', 'adminp3m@harkatnegeri.ac.id')->first();
        if (!$this->admin) {
            $this->admin = User::create([
                'name' => 'Staf Administrator P3M',
                'email' => 'adminp3m@harkatnegeri.ac.id',
                'nidn_nim' => '0699999901',
                'password' => Hash::make('0699999901'),
                'is_otp_verified' => true,
                'email_verified_at' => now(),
            ]);
        }
        $this->admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }

    /**
     * Test login using Full Name as identity and NIDN as password.
     */
    public function test_user_can_login_with_full_name_and_nidn_password(): void
    {
        $response = $this->post(route('login.post'), [
            'identity' => 'Sharfina Febbi Handayani',
            'password' => '0617029201',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->dosen);
    }

    /**
     * Test login using NIDN as identity and NIDN as password.
     */
    public function test_user_can_login_with_nidn_as_identity(): void
    {
        $response = $this->post(route('login.post'), [
            'identity' => '0617029201',
            'password' => '0617029201',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->dosen);
    }

    /**
     * Test login fails with invalid password.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'identity' => '0617029201',
            'password' => 'wrong_password_999',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test user can view and update personal profile, adding an active email.
     */
    public function test_user_can_view_and_update_profile_email(): void
    {
        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil');

        $updateResponse = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->put(route('profile.update'), [
                'name' => 'Sharfina Febbi Handayani, S.Kom., M.Kom.',
                'email' => 'sharfina.febbi@gmail.com',
                'phone_number' => '081299887766',
            ]);

        $updateResponse->assertRedirect(route('profile.edit'));

        $this->dosen->refresh();
        $this->assertEquals('sharfina.febbi@gmail.com', $this->dosen->email);
        $this->assertEquals('081299887766', $this->dosen->phone_number);
    }

    /**
     * Test user can upload avatar image.
     */
    public function test_user_can_upload_avatar_image(): void
    {
        $fakeImage = UploadedFile::fake()->image('profile_photo.jpg', 200, 200);

        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->put(route('profile.update'), [
                'name' => $this->dosen->name,
                'email' => $this->dosen->email,
                'avatar' => $fakeImage,
            ]);

        $response->assertRedirect(route('profile.edit'));

        $this->dosen->refresh();
        $this->assertNotNull($this->dosen->avatar);
        Storage::disk('public')->assertExists($this->dosen->avatar);
    }

    /**
     * Test user can change password securely.
     */
    public function test_user_can_change_password_securely(): void
    {
        $response = $this->actingAs($this->dosen)
            ->withSession(['otp_verified' => true])
            ->put(route('profile.update-password'), [
                'current_password' => '0617029201',
                'password' => 'SecretPass2026!',
                'password_confirmation' => 'SecretPass2026!',
            ]);

        $response->assertRedirect(route('profile.edit'));

        $this->dosen->refresh();
        $this->assertTrue(Hash::check('SecretPass2026!', $this->dosen->password));
    }

    /**
     * Test admin can access user management and create a new account with hashed password.
     */
    public function test_admin_can_create_new_user_account(): void
    {
        $dosenRole = Role::where('name', 'Dosen / Pengusul')->first();

        $response = $this->actingAs($this->admin)
            ->withSession(['otp_verified' => true])
            ->get(route('admin.users.index'));

        $response->assertStatus(200);

        $createResponse = $this->actingAs($this->admin)
            ->withSession(['otp_verified' => true])
            ->post(route('admin.users.store'), [
                'name' => 'Dosen Baru Riset, M.T.',
                'email' => 'dosen.baru@harkatnegeri.ac.id',
                'nidn_nim' => '0622019901',
                'password' => '0622019901',
                'roles' => [$dosenRole->id],
            ]);

        $createResponse->assertRedirect(route('admin.users.index'));

        $newUser = User::where('nidn_nim', '0622019901')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue(Hash::check('0622019901', $newUser->password));
        $this->assertNotEquals('0622019901', $newUser->password); // Must be hashed!
        $this->assertTrue($newUser->hasRole('Dosen / Pengusul'));
    }

    /**
     * Test non-admin user cannot access user management.
     */
    public function test_non_admin_cannot_access_user_management(): void
    {
        $dosenRole = Role::where('name', 'Dosen / Pengusul')->first();
        $pureDosen = User::create([
            'name' => 'Dosen Murni Tanpa Jabatan',
            'email' => 'dosen.murni@harkatnegeri.ac.id',
            'nidn_nim' => '0677788899',
            'password' => Hash::make('0677788899'),
            'is_otp_verified' => true,
        ]);
        $pureDosen->roles()->sync([$dosenRole->id]);

        $response = $this->actingAs($pureDosen)
            ->withSession(['otp_verified' => true])
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }
}
