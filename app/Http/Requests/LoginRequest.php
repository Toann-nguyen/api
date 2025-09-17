<?php

namespace App\Http\Requests;

use Hash;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
    /**
     * Xác thực login
     */
    public function authenticate()
    {
        //chan nguoi dung dang nhap nhieu lan trong thoi gian ngan
        $this->ensureIsNotRateLimited();
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->session->regenerate();

    }
    public function ensureIsNotRateLimited()
    {
        if($this->session->get('email') != $this->email){
            $this->session->put('email', $this->email);
        }
    if ($this->throttle->tooManyAttempts($this->throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $this->throttle->availableIn($this->throttleKey)]),
            ]);
        }

        $this->throttle->hit($this->throttleKey, 60);
    }
}
