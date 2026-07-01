<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $amount = $this->input('amount');

        if (! is_string($amount)) {
            return;
        }

        $normalizedAmount = preg_replace('/[^\d,.\-]/', '', trim($amount));

        if ($normalizedAmount === null || $normalizedAmount === '') {
            return;
        }

        $lastComma = strrpos($normalizedAmount, ',');
        $lastDot = strrpos($normalizedAmount, '.');
        $decimalSeparator = null;

        if ($lastComma !== false || $lastDot !== false) {
            $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
        }

        if ($decimalSeparator !== null) {
            $parts = explode($decimalSeparator, $normalizedAmount, 2);
            $integerPart = preg_replace('/[^\d\-]/', '', $parts[0] ?? '');
            $fractionPart = preg_replace('/\D/', '', $parts[1] ?? '');

            $normalizedAmount = $integerPart;

            if ($fractionPart !== '') {
                $normalizedAmount .= '.'.substr($fractionPart, 0, 2);
            }
        } else {
            $normalizedAmount = preg_replace('/[^\d\-]/', '', $normalizedAmount);
        }

        $this->merge([
            'amount' => $normalizedAmount,
        ]);
    }
}
