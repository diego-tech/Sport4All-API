<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
             'name' => 'nullable|min:5|max:255',
             'email' => [
                'nullable',
                'email',
                'unique:clubs,email,' . $this->id

             ],
             'password' => 'nullable|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
             'description' => 'nullable|string',
             'first_hour' => 'nullable|date_format:H:i',
             'last_hour' => 'nullable|date_format:H:i',
             'web' => 'nullable|string|max:255',
             'tlf' => 'nullable|string|regex:/[0-9]{9}/',
             'club_img' => 'nullable|image',
             'club_banner' => 'nullable|image',
             
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
