<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_digit_only_amount_as_currency_with_two_decimals(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => '189133',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento de teste',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $transaction = Transaction::query()->sole();

        $this->assertSame('1891.33', $transaction->amount);
        $this->assertSame($user->id, $transaction->user_id);
    }
}
