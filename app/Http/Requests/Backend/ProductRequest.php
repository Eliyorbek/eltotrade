<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|unique:products,sku,' . $productId,
            'barcode'         => 'nullable|string|unique:products,barcode,' . $productId,
            'description'     => 'nullable|string|max:2000',
            'purchase_price'  => 'required|numeric|min:0',
            'sale_price'      => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'min_stock'       => 'nullable|integer|min:0',
            'unit'            => 'required|string|max:50',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'          => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'   => 'Kategoriya tanlash majburiy',
            'name.required'          => 'Mahsulot nomi majburiy',
            'purchase_price.required'=> 'Kelish narxi majburiy',
            'sale_price.required'    => 'Sotish narxi majburiy',
            'stock.required'         => 'Miqdor majburiy',
            'unit.required'          => 'O\'lchov birligi majburiy',
            'barcode.unique'         => 'Bu barcode allaqachon mavjud',
            'sku.unique'             => 'Bu SKU allaqachon mavjud',
        ];
    }
}
