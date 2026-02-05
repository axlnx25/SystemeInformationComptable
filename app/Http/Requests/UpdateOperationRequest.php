<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOperationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $operation = $this->route('operation');
        return Auth::check() && $operation->journal->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numero_operation' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'reference' => 'nullable|string|max:255',
            'libelle' => 'sometimes|required|string|max:255',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
            'numero_compte_general' => 'sometimes|required|string|max:255',
            'journal_id' => 'sometimes|required|exists:journals,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ensure either debit OR credit is filled, not both and not neither
            $debit = $this->debit ?? $this->route('operation')->debit ?? 0;
            $credit = $this->credit ?? $this->route('operation')->credit ?? 0;

            if ($debit > 0 && $credit > 0) {
                $validator->errors()->add('debit', 'Une ligne ne peut pas avoir à la fois un débit et un crédit.');
            }

            if ($debit == 0 && $credit == 0) {
                $validator->errors()->add('debit', 'Une ligne doit avoir soit un débit, soit un crédit.');
            }
        });
    }
}
