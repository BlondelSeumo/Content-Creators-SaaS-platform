<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'recipient_user_id' => '',
            'post_id' => '',
            'taxes' => '',
            'amount' => 'required',
            'provider' => 'required',
            'transaction_type' => 'required',
            'billing_address' => 'min:10|max:255',
            'first_name' => 'min:3|max:255',
            'last_name' => 'min:3|max:255',
            'country' => 'min:4|max:255',
            'state' => 'min:2|max:255',
            'postcode' => 'min:2|max:255',
            'city' => 'min:2|max:255',
        ];
    }
}
