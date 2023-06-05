<?php

namespace App\Http\Requests\Expenses;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ];
    }

    public function messages() 
    {
        return [
            'name.required' => 'O nome do usuário é obrigatório.',
            'name.string' => 'O nome informado não está em um formato válido.',
            'name.max' => 'O nome do usuário deve ter no máximo 255 caracteres.',
            'email.required' =>'O e-mail do usuário é obrigatório.',
            'email.email' => 'O e-mail informado não está em um formato válido.',
            'name.max' => 'O e-mail do usuário deve ter no máximo 255 caracteres.',
            'name.unique' => 'Já existe um usuário cadastrado com o e-mail informado.',
            'password.required' =>'A senha do usuário é obrigatória.',
            'password.min' => 'A senha do usuário deve ter no mínimo 6 digitos.',
        ];
    }
}
