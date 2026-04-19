<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Transfer Service APIs Test
 * Tests all transfer functionalities including Dpay, Local Bank, and Transaction Split
 */
class TransferServiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $beneficiary;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with balance
        $this->user = User::factory()->create([
            'balance' => 100000,
            'pin' => '1234',
            'pin_state' => 1,
        ]);

        $this->beneficiary = User::factory()->create([
            'balance' => 5000,
        ]);

        $this->actingAs($this->user);
    }

    /**
     * Test Dpay transfer resolve with phone (default)
     */
    public function test_dpay_resolve_with_phone(): void
    {
        $response = $this->postJson(route('user.dpay.resolve'), [
            'recipient' => $this->beneficiary->phone,
            'type' => 'phone',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'resolved_by',
                    'account_name',
                    'account_number',
                    'phone',
                ],
            ]);
    }

    /**
     * Test Dpay transfer resolve with username
     */
    public function test_dpay_resolve_with_username(): void
    {
        $response = $this->postJson(route('user.dpay.resolve'), [
            'recipient' => $this->beneficiary->username,
            'type' => 'username',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'resolved_by',
                    'account_name',
                    'account_number',
                ],
            ]);
    }

    /**
     * Test Dpay transfer submit with sufficient balance
     */
    public function test_dpay_transfer_submit_success(): void
    {
        $response = $this->post(route('user.dpay.submit'), [
            'recipient' => $this->beneficiary->phone,
            'type' => 'phone',
            'amount' => 5000,
            'narration' => 'Test transfer',
            'save_beneficiary' => false,
        ]);

        $response->assertRedirect(route('user.dpay.preview'));
        $this->assertEquals(5000, session('dpay_transfer.amount'));
    }

    /**
     * Test Dpay transfer submit with insufficient balance
     */
    public function test_dpay_transfer_submit_insufficient_balance(): void
    {
        $this->user->update(['balance' => 100]);

        $response = $this->post(route('user.dpay.submit'), [
            'recipient' => $this->beneficiary->phone,
            'type' => 'phone',
            'amount' => 5000,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    /**
     * Test Dpay transfer confirm
     */
    public function test_dpay_transfer_confirm(): void
    {
        // First submit the transfer
        $this->post(route('user.dpay.submit'), [
            'recipient' => $this->beneficiary->phone,
            'type' => 'phone',
            'amount' => 5000,
        ]);

        // Then confirm
        $response = $this->post(route('user.dpay.confirm'));

        $response->assertRedirect(route('user.dpay.index'));
        
        // Verify balance was deducted
        $this->user->refresh();
        $this->assertEquals(95000, $this->user->balance);
    }

    /**
     * Test local bank transfer split info calculation
     */
    public function test_local_transfer_split_info(): void
    {
        $response = $this->postJson(route('user.othertransfer.split-info'), [
            'amount' => 25000,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_amount',
                    'chunk_size',
                    'chunks',
                    'chunk_count',
                    'requires_split',
                    'currency_symbol',
                    'currency_text',
                ],
            ]);

        $data = $response->json('data');
        $this->assertTrue($data['requires_split']);
        $this->assertEquals(3, $data['chunk_count']); // 10k + 10k + 5k
    }

    /**
     * Test transaction split with amount exactly at threshold
     */
    public function test_transaction_split_at_threshold(): void
    {
        $response = $this->postJson(route('user.othertransfer.split-info'), [
            'amount' => 10000,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertFalse($data['requires_split']);
        $this->assertEquals(1, $data['chunk_count']);
    }

    /**
     * Test transaction split with amount below threshold
     */
    public function test_transaction_split_below_threshold(): void
    {
        $response = $this->postJson(route('user.othertransfer.split-info'), [
            'amount' => 5000,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertFalse($data['requires_split']);
        $this->assertEquals(1, $data['chunk_count']);
    }

    /**
     * Test transaction split with large odd amount
     */
    public function test_transaction_split_odd_amount(): void
    {
        $response = $this->postJson(route('user.othertransfer.split-info'), [
            'amount' => 33333.50,
        ]);

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertTrue($data['requires_split']);
        $this->assertEquals(4, $data['chunk_count']); // 10k + 10k + 10k + 3.33
    }

    /**
     * Test user to user transfer with balance check
     */
    public function test_user_to_user_transfer(): void
    {
        $response = $this->post(route('user.usertransfer'), [
            'type' => 1,
            'beneficiary' => $this->beneficiary->account_number,
            'amount' => 5000,
        ]);

        $response->assertRedirect(route('user.usertransfer.preview'));
        $this->assertEquals(5000, session('amount'));
    }

    /**
     * Test user to user transfer with invalid beneficiary
     */
    public function test_user_to_user_transfer_invalid_beneficiary(): void
    {
        $response = $this->post(route('user.usertransfer'), [
            'type' => 1,
            'beneficiary' => 'invalid_account',
            'amount' => 5000,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test transferring to self
     */
    public function test_transfer_to_self_rejected(): void
    {
        $response = $this->post(route('user.usertransfer'), [
            'type' => 1,
            'beneficiary' => $this->user->account_number,
            'amount' => 5000,
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test local bank transfer with account resolution
     */
    public function test_local_bank_transfer_resolve_account(): void
    {
        $response = $this->postJson(route('user.othertransfer.resolve'), [
            'bank_name' => 'Access Bank',
            'bank_code' => '044',
            'account_number' => '1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'account_name',
                    'account_number',
                    'bank_name',
                    'bank_code',
                ],
            ]);
    }

    /**
     * Test transfer history retrieval
     */
    public function test_get_transfer_history(): void
    {
        $response = $this->get(route('user.othertransfer'));

        $response->assertStatus(200)
            ->assertViewHasAll([
                'pageTitle',
                'user',
                'log',
                'banks',
                'settings',
            ]);
    }

    /**
     * Test admin settings for transaction split
     */
    public function test_admin_transaction_split_settings(): void
    {
        // Create admin user
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.transaction-split.update'), [
                'transaction_split_enabled' => true,
                'transaction_split_threshold' => 15000,
                'transaction_split_description' => 'Test split feature',
            ]);

        $response->assertRedirect();

        // Verify settings were saved
        $general = gs();
        $this->assertTrue($general->transaction_split_enabled);
        $this->assertEquals(15000, $general->transaction_split_threshold);
    }

    /**
     * Test admin test split calculation endpoint
     */
    public function test_admin_test_split_calculation(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('admin.settings.transaction-split.test'), [
                'amount' => 30000,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'original_amount',
                    'threshold',
                    'chunks',
                    'chunk_count',
                    'requires_split',
                ],
            ]);
    }

    /**
     * Test Dpay transfer with saved beneficiary
     */
    public function test_dpay_transfer_with_saved_beneficiary(): void
    {
        $response = $this->post(route('user.dpay.submit'), [
            'recipient' => $this->beneficiary->phone,
            'type' => 'phone',
            'amount' => 5000,
            'save_beneficiary' => true,
        ]);

        $response->assertRedirect(route('user.dpay.preview'));
        $this->assertTrue(session('dpay_transfer.save_beneficiary'));
    }

    /**
     * Test Dpay transfer validation
     */
    public function test_dpay_transfer_validation(): void
    {
        // Missing required fields
        $response = $this->postJson(route('user.dpay.submit'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['recipient', 'type', 'amount']);
    }

    /**
     * Test Dpay preview after timeout
     */
    public function test_dpay_preview_session_timeout(): void
    {
        $response = $this->get(route('user.dpay.preview'));

        // Should redirect since session has expired
        $response->assertRedirect(route('user.dpay.index'));
    }
}
