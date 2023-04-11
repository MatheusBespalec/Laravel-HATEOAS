<?php

namespace App\Http\Requests;

use App\Rules\CPFRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('customer'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['integer'],
            'name' => ['required', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'max:11', new CPFRule, 'unique:customers,cpf,'.$this->id],
            'birthdate' => ['required', 'date'],
            'address' => ['required'],
            'address.address' => ['required', 'string', 'max:100'],
            'address.city' => ['required', 'string', 'max:100'],
            'address.state' => ['required', 'string', 'max:2', 'exists:states,acronym'],
            'gender' => ['required', 'string', 'max:1', 'exists:genders,abbreviation'],
        ];
    }
}
