<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
     * バリデーション前に実行される前処理
     */
    protected function prepareForValidation()
    {
        if ($this->price) {
            $this->merge([
                'price' => str_replace(',', '', $this->price)
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => ['required', 'image'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'condition' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'categories.required' => 'カテゴリーを選択してください。',
            'categories.*.exists' => '選択されたカテゴリーが不正です。',
        ];
    }
}
