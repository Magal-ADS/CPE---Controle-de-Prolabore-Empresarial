<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_it_stores_attachments_in_the_database_and_serves_them(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('comprovante.pdf', 'pdf-content');

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => '150,00',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento com comprovante',
            'attachment' => $file,
        ]);

        $response->assertRedirect(route('transactions.index'));

        $transaction = Transaction::query()->firstOrFail();

        $this->assertNull($transaction->attachment_path);
        $this->assertSame('comprovante.pdf', $transaction->attachment_name);
        $this->assertNotEmpty($transaction->attachment_mime);
        $this->assertSame(11, $transaction->attachment_size);
        $this->assertSame('pdf-content', $transaction->attachment_content);
        $this->assertStringStartsWith('base64:', $transaction->getRawOriginal('attachment_content'));

        $this->actingAs($user)
            ->get(route('transactions.attachment', $transaction))
            ->assertOk()
            ->assertHeader('Content-Type', $transaction->attachment_mime)
            ->assertContent('pdf-content');
    }

    public function test_create_and_edit_forms_are_ready_for_file_uploads(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => '99.90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento para formulario',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.create'))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="attachment"', false);

        $this->actingAs($user)
            ->get(route('transactions.edit', $transaction))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="attachment"', false);
    }

    public function test_index_shows_attachment_link_for_database_backed_attachments(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => '99.90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento com comprovante',
            'attachment_name' => 'comprovante.pdf',
            'attachment_mime' => 'application/pdf',
            'attachment_size' => 11,
            'attachment_content' => 'pdf-content',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee(route('transactions.attachment', $transaction), false)
            ->assertSee('Abrir comprovante');
    }

    public function test_it_serves_attachments_with_proxy_safe_headers_for_special_filenames(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => '99.90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento com nome especial',
            'attachment_name' => 'comprovante João "07/2026".pdf',
            'attachment_mime' => 'application/pdf',
            'attachment_size' => 11,
            'attachment_content' => 'pdf-content',
        ]);

        $response = $this->actingAs($user)
            ->get(route('transactions.attachment', $transaction));

        $response
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertContent('pdf-content');

        $this->assertStringContainsString('inline;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('filename=', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('filename*=', $response->headers->get('Content-Disposition'));
        $this->assertFalse($response->headers->has('Content-Length'));
    }

    public function test_it_rejects_attachments_larger_than_five_megabytes(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('comprovante.pdf', 5121, 'application/pdf');

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'amount' => '150,00',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento com comprovante grande',
            'attachment' => $file,
        ]);

        $response->assertSessionHasErrors('attachment');
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_it_updates_image_attachments_with_binary_content_safely(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => '99.90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento sem comprovante',
        ]);

        $file = UploadedFile::fake()->image('comprovante.jpg', 16, 16);
        $binaryContent = file_get_contents($file->getRealPath());

        $this->assertIsString($binaryContent);

        $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'type' => 'expense',
            'amount' => '99,90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento com imagem',
            'attachment' => $file,
        ]);

        $response->assertRedirect(route('transactions.index'));

        $transaction = $transaction->fresh();

        $this->assertSame('comprovante.jpg', $transaction->attachment_name);
        $this->assertNotEmpty($transaction->attachment_mime);
        $this->assertSame($binaryContent, $transaction->attachment_content);
        $this->assertStringStartsWith('base64:', $transaction->getRawOriginal('attachment_content'));

        $this->actingAs($user)
            ->get(route('transactions.attachment', $transaction))
            ->assertOk()
            ->assertHeader('Content-Type', $transaction->attachment_mime)
            ->assertContent($binaryContent);
    }

    public function test_it_decodes_attachment_content_loaded_as_a_resource(): void
    {
        $stream = fopen('php://temp', 'r+');

        $this->assertIsResource($stream);

        fwrite($stream, 'base64:'.base64_encode('image-bytes'));
        rewind($stream);

        $transaction = new Transaction;
        $transaction->setRawAttributes([
            'attachment_content' => $stream,
        ], true);

        $this->assertSame('image-bytes', $transaction->attachment_content);

        fclose($stream);
    }

    public function test_it_decodes_legacy_base64_attachment_content_without_prefix(): void
    {
        $transaction = new Transaction;
        $transaction->setRawAttributes([
            'attachment_content' => base64_encode('%PDF-legacy-content'),
        ], true);

        $this->assertSame('%PDF-legacy-content', $transaction->attachment_content);
    }

    public function test_it_decodes_postgres_hex_encoded_attachment_content(): void
    {
        $transaction = new Transaction;
        $transaction->setRawAttributes([
            'attachment_content' => '\\x'.bin2hex('base64:'.base64_encode('%PDF-postgres-content')),
        ], true);

        $this->assertSame('%PDF-postgres-content', $transaction->attachment_content);
    }

    public function test_it_keeps_serving_legacy_attachments_saved_on_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Storage::disk('public')->put('transactions/legacy.pdf', 'legacy-content');

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => '99.90',
            'transaction_date' => '2026-07-01',
            'description' => 'Lancamento legado',
            'attachment_path' => 'transactions/legacy.pdf',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.attachment', $transaction))
            ->assertOk()
            ->assertContent('legacy-content');
    }
}
