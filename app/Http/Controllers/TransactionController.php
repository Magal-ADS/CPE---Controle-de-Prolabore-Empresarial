<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\HeaderUtils;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Transaction::query()
            ->withoutAttachmentContent()
            ->with('user')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->string('start_date')))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->string('end_date')))
            ->latest('transaction_date')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        return view('transactions.create', [
            'transaction' => new Transaction([
                'transaction_date' => now()->toDateString(),
                'type' => 'income',
            ]),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            $this->fillAttachmentData($request->file('attachment'), $data);
        }

        Transaction::create($data);

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao cadastrada com sucesso.');
    }

    public function edit(Transaction $transaction): View
    {
        return view('transactions.edit', compact('transaction'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            if ($transaction->attachment_path) {
                Storage::disk('public')->delete($transaction->attachment_path);
            }

            $this->fillAttachmentData($request->file('attachment'), $data);
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao atualizada com sucesso.');
    }

    public function attachment(Transaction $transaction): Response|RedirectResponse
    {
        if ($transaction->attachment_content !== null) {
            return response(
                $transaction->attachment_content,
                200,
                $this->attachmentHeaders(
                    $transaction->attachmentFilename() ?? 'comprovante',
                    $transaction->attachment_mime ?? 'application/octet-stream',
                ),
            );
        }

        abort_unless($transaction->attachment_path, 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($transaction->attachment_path), 404);

        return response(
            $disk->get($transaction->attachment_path),
            200,
            $this->attachmentHeaders(
                $transaction->attachmentFilename() ?? 'comprovante',
                $disk->mimeType($transaction->attachment_path) ?: 'application/octet-stream',
            ),
        );
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->attachment_path) {
            Storage::disk('public')->delete($transaction->attachment_path);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('status', 'Movimentacao removida com sucesso.');
    }

    private function fillAttachmentData(UploadedFile $attachment, array &$data): void
    {
        $data['attachment_path'] = null;
        $data['attachment_name'] = $attachment->getClientOriginalName();
        $data['attachment_mime'] = $attachment->getMimeType() ?: $attachment->getClientMimeType();
        $data['attachment_size'] = $attachment->getSize();
        $data['attachment_content'] = $attachment->get();
    }

    private function attachmentHeaders(string $filename, string $mimeType): array
    {
        $filename = trim((string) preg_replace('/[\x00-\x1F\x7F\\\\\/]+/', '_', $filename));

        if ($filename === '') {
            $filename = 'comprovante';
        }

        $fallbackFilename = trim((string) preg_replace('/[^A-Za-z0-9._-]+/', '_', Str::ascii($filename)), '._-');

        if ($fallbackFilename === '') {
            $fallbackFilename = 'comprovante';
        }

        return [
            'Content-Type' => $mimeType,
            'Content-Disposition' => HeaderUtils::makeDisposition('inline', $filename, $fallbackFilename),
        ];
    }
}
