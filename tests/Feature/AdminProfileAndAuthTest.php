<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileAndAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'Super Administrator',
            'description' => 'Full administrative access',
            'permissions' => ['*'],
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Dr. Zaid Al-Nuaimi',
            'email' => 'zaid@bluezone.com',
            'password' => Hash::make('SecretPassword123!'),
            'role_id' => $this->adminRole->id,
            'status' => 'active',
            'phone' => '+971501234567',
            'bio' => 'Chief Longevity Science Officer at Blue Zone Bioceuticals.',
            'preferences' => [
                'sound_enabled' => true,
                'theme' => 'dark',
                'locale' => 'en',
            ],
        ]);
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('BZ-OS');
        $response->assertSee('BLUE ZONE');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'zaid@bluezone.com',
            'password' => 'SecretPassword123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'zaid@bluezone.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Dr. Zaid Al-Nuaimi');
        $response->assertSee('zaid@bluezone.com');
        $response->assertSee('Super Administrator');
        $response->assertSee('+971501234567');
        $response->assertSee('Chief Longevity Science Officer');
    }

    public function test_admin_can_update_profile_information_and_avatar(): void
    {
        $avatarFile = UploadedFile::fake()->image('profile_pic.png', 400, 400);

        $response = $this->actingAs($this->admin)->put(route('admin.profile.update'), [
            'name' => 'Dr. Zaid Tariq',
            'email' => 'zaid.tariq@bluezone.com',
            'phone' => '+971509876543',
            'bio' => 'Updated bio for director of bioceuticals.',
            'avatar_file' => $avatarFile,
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertEquals('Dr. Zaid Tariq', $this->admin->name);
        $this->assertEquals('zaid.tariq@bluezone.com', $this->admin->email);
        $this->assertEquals('+971509876543', $this->admin->phone);
        $this->assertEquals('Updated bio for director of bioceuticals.', $this->admin->bio);
        $this->assertNotNull($this->admin->avatar);
    }

    public function test_admin_can_update_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.password'), [
            'current_password' => 'SecretPassword123!',
            'password' => 'NewStrongPass#2026',
            'password_confirmation' => 'NewStrongPass#2026',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('NewStrongPass#2026', $this->admin->password));
    }

    public function test_admin_cannot_update_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.password'), [
            'current_password' => 'IncorrectCurrentPass',
            'password' => 'NewStrongPass#2026',
            'password_confirmation' => 'NewStrongPass#2026',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('SecretPassword123!', $this->admin->password));
    }

    public function test_admin_can_update_acoustic_and_theme_preferences(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profile.preferences'), [
            'sound_enabled' => '0',
            'theme' => 'light',
            'locale' => 'ar',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertFalse($this->admin->preferences['sound_enabled']);
        $this->assertEquals('light', $this->admin->preferences['theme']);
        $this->assertEquals('ar', $this->admin->preferences['locale']);
    }
}
