<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $user_id = $this->user_id != '0' ? $this->user_id : 'NULL';
        $isRequired = $this->user_id != '0' ? 'nullable' : 'required';
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user_id . ',user_id,deleted_at,NULL',
            'password' => $isRequired . '|min:5|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => $isRequired . '|min:5',
            'status' => 'sometimes',
            'user_type' => 'required',
            'profile_picture' => 'sometimes',
            //'profile_picture' => 'sometimes|mimes:jpg,jpeg,png',
            'filesArray' => 'sometimes|array',
            'user_id' => 'sometimes',
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
