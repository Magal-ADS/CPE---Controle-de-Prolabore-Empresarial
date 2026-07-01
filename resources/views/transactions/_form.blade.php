@csrf

@php
    $amountValue = old('amount');

    if ($amountValue === null && $transaction->amount !== null) {
        $amountValue = number_format((float) $transaction->amount, 2, ',', '.');
    }
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <div>
        <label for="type" class="form-label">Tipo</label>
        <select id="type" name="type" class="form-input">
            <option value="income" @selected(old('type', $transaction->type) === 'income')>Entrada</option>
            <option value="expense" @selected(old('type', $transaction->type) === 'expense')>Saida</option>
        </select>
        @error('type')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="amount" class="form-label">Valor</label>
        <input
            id="amount"
            name="amount"
            type="text"
            inputmode="decimal"
            value="{{ $amountValue }}"
            class="form-input"
            data-currency-input
            autocomplete="off"
            placeholder="0,00"
        >
        @error('amount')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="transaction_date" class="form-label">Data</label>
        <input id="transaction_date" name="transaction_date" type="date" value="{{ old('transaction_date', optional($transaction->transaction_date)->format('Y-m-d') ?? $transaction->transaction_date) }}" class="form-input">
        @error('transaction_date')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="attachment" class="form-label">Comprovante</label>
        <input id="attachment" name="attachment" type="file" class="form-input file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white dark:file:bg-white dark:file:text-slate-950">
        @error('attachment')
            <p class="form-error">{{ $message }}</p>
        @enderror
        @if ($transaction->attachment_path)
            <a href="{{ \Illuminate\Support\Facades\Storage::url($transaction->attachment_path) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                Ver comprovante atual
            </a>
        @endif
    </div>

    <div class="lg:col-span-2">
        <label for="description" class="form-label">Descricao</label>
        <textarea id="description" name="description" rows="5" class="form-input">{{ old('description', $transaction->description) }}</textarea>
        @error('description')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6 flex flex-col gap-3 sm:flex-row">
    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 dark:border-slate-700 dark:text-slate-200 dark:hover:border-white dark:hover:text-white">
        Cancelar
    </a>
</div>

@push('scripts')
    <script>
        (() => {
            const currencyInput = document.querySelector('[data-currency-input]');

            if (!currencyInput) {
                return;
            }

            const groupInteger = (digits) => {
                const sanitized = digits.replace(/\D/g, '').replace(/^0+(?=\d)/, '');

                if (sanitized === '') {
                    return '0';
                }

                return sanitized.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            const formatCurrencyValue = (value) => {
                const digits = value.replace(/\D/g, '');

                if (digits === '') {
                    return '';
                }

                const paddedDigits = digits.padStart(3, '0');
                const integerPart = groupInteger(paddedDigits.slice(0, -2));
                const decimalPart = paddedDigits.slice(-2);

                return `${integerPart},${decimalPart}`;
            };

            currencyInput.addEventListener('input', () => {
                currencyInput.value = formatCurrencyValue(currencyInput.value);
            });

            currencyInput.addEventListener('blur', () => {
                currencyInput.value = formatCurrencyValue(currencyInput.value);
            });

            if (currencyInput.value.trim() !== '') {
                currencyInput.value = formatCurrencyValue(currencyInput.value);
            }
        })();
    </script>
@endpush
