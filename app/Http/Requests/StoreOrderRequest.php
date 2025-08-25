<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:10',
        ];

        // Add customer details if not authenticated
        if (!auth()->check()) {
            $rules['customer_email'] = 'required|email';
            $rules['customer_whatsapp'] = 'required|string|min:10|max:15';
        }

        // Add game ID validation if enabled and product requires it
        if ($this->product_id) {
            $product = \App\Models\Product::find($this->product_id);
            $gameIdCheckEnabled = Setting::get('game_id_check_enabled', true);
            
            if ($product && $product->requires_game_id && $gameIdCheckEnabled) {
                $rules['game_id'] = 'required|string|min:3|max:50';
            }
        }

        // Add reCAPTCHA validation if enabled
        if (Setting::get('recaptcha_enabled', false)) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product is not available.',
            'customer_email.required' => 'Email address is required.',
            'customer_email.email' => 'Please provide a valid email address.',
            'customer_whatsapp.required' => 'WhatsApp number is required.',
            'customer_whatsapp.min' => 'WhatsApp number must be at least 10 digits.',
            'game_id.required' => 'Game ID is required for this product.',
            'game_id.min' => 'Game ID must be at least 3 characters.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Maximum quantity is 10.',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.recaptcha' => 'reCAPTCHA verification failed.',
        ];
    }
}