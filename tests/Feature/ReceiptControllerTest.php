<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReceiptControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    private function memberUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(3),
        ]);
        return $user;
    }

    private function validatedPayment(User $owner): Payment
    {
        return Payment::create([
            'user_id' => $owner->id,
            'method'  => 'mvola',
            'amount'  => 15000,
            'status'  => 'validated',
        ]);
    }

    private function mockPdf(): void
    {
        $pdfInstance = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfInstance->shouldReceive('setPaper')->andReturnSelf();
        $pdfInstance->shouldReceive('download')->andReturn(
            response()->make('fake pdf content', 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=recu-000001.pdf',
            ])
        );
        Pdf::shouldReceive('loadView')->andReturn($pdfInstance);
    }

    // ── Autorisation ──────────────────────────────────────────────────────

    public function test_owner_can_download_validated_receipt(): void
    {
        $this->mockPdf();
        $owner   = $this->memberUser();
        $payment = $this->validatedPayment($owner);

        $response = $this->actingAs($owner)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertOk();
    }

    public function test_admin_can_download_any_validated_receipt(): void
    {
        $this->mockPdf();
        $owner   = $this->memberUser();
        $payment = $this->validatedPayment($owner);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertOk();
    }

    public function test_super_admin_can_download_any_validated_receipt(): void
    {
        $this->mockPdf();
        $owner   = $this->memberUser();
        $payment = $this->validatedPayment($owner);

        /** @var User $superAdmin */
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertOk();
    }

    public function test_other_user_receives_403(): void
    {
        $owner   = $this->memberUser();
        $payment = $this->validatedPayment($owner);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $otherUser->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(1),
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $owner   = $this->memberUser();
        $payment = $this->validatedPayment($owner);

        $response = $this->get(route('membre.payment.receipt', $payment));

        $response->assertRedirect(route('member.login'));
    }

    // ── Statut du paiement ────────────────────────────────────────────────

    public function test_pending_payment_returns_404(): void
    {
        $owner   = $this->memberUser();
        $payment = Payment::create([
            'user_id' => $owner->id,
            'method'  => 'mvola',
            'amount'  => 15000,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertNotFound();
    }

    public function test_rejected_payment_returns_404(): void
    {
        $owner   = $this->memberUser();
        $payment = Payment::create([
            'user_id' => $owner->id,
            'method'  => 'mvola',
            'amount'  => 15000,
            'status'  => 'rejected',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('membre.payment.receipt', $payment));

        $response->assertNotFound();
    }
}