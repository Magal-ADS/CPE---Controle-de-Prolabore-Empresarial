<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_transaction_amounts_normalized_as_currency(): void
    {
        $user = User::factory()->create();

        foreach ([
            '189133',
            '1.891,33',
            '1891,33',
            '1891.33',
            'R$ 1.891,33',
        ] as $amount) {
            $response = $this->actingAs($user)->post(route('transactions.store'), [
                'type' => 'income',
                'amount' => $amount,
                'transaction_date' => '2026-07-01',
                'description' => 'Lancamento de teste',
            ]);

            $response->assertRedirect(route('transactions.index'));
        }

        $this->assertSame(5, Transaction::query()->count());

        Transaction::query()->each(function (Transaction $transaction) use ($user): void {
            $this->assertSame('1891.33', $transaction->amount);
            $this->assertSame($user->id, $transaction->user_id);
        });
    }

    public function test_it_rejects_negative_amounts_after_normalization(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'income',
            'amount' => '-189133',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento de teste',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('transactions', 0);
    }
}
