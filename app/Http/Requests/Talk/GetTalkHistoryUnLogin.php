<?php

namespace App\Http\Requests\Talk;

use Illuminate\Foundation\Http\FormRequest;

class GetTalkHistoryUnLogin extends FormRequest
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
            'service_id' => 'required|string',
            'touch_id' => 'required|string',
            'item' => 'required|integer',
            'page' => 'required|integer',
        ];
    }
}
