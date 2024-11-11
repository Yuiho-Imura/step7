<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [
            'product_name' => 'required', //requiredは必須
            'company_id' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'comment' => 'nullable', 
           'img_path' => 'nullable|image|max:2048',
        ];
    }


    public function attributes()
    {
        return [
            'product_name' => '商品名',
            'company_id' => 'メーカーID',
            'price' => '価格',
            'stock' => '在庫数',
            'comment' => 'コメント',
            'img_path' => '画像',
        ];
    }
    
        public function messages()
    {
        return [
            'product_name.required' => ':attributeは必須項目です。',
            'price.required' => ':attributeは必須項目です。',
            'price.numeric' => ':attributeは数値で入力してください。',
            'stock.required' => ':attributeは必須項目です。',
            'stock.numeric' => ':attributeは数値で入力してください。',
            'img_path.max' => ':attributeは2048以下で入力してください。',

        ];
    }
}




