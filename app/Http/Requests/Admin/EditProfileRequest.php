<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user_id = $this->user()->user_id;
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user_id . ',user_id,deleted_at,NULL',
            'password' => 'nullable|min:5|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'nullable|min:5',
            'profile_picture' => 'sometimes',
            'filesArray' => 'sometimes|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'email.unique' => 'Email has already been taken',
            'email.required' => 'Email is required',
            'user_type.required' => 'User type is required',
            'password.required' => 'Password is required.',
            'password_confirmation.required' => 'Confirm password is required.',
            //'profile_picture.mimes' => 'Profile picture field must be a file of type: jpg, jpeg, png',
        ];
    }
}
