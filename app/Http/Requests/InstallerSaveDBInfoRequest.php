<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallerSaveDBInfoRequest extends FormRequest
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
            'db_host' => 'required',
            'db_name' => 'required',
            'db_username' => 'required',
            // 'db_password' => 'required', // Info: Left this commented out in case users really want empty passwords
        ];
    }
}
