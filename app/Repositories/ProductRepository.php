<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($perPage)
    {
        return Product::paginate($perPage);
    }

    public function findByCode($code)
    {
        return Product::where('code', $code)->firstOrFail();
    }

    public function updateByCode($code, array $data)
    {
        $product = $this->findByCode($code);
        $product->update($data);
        return $product;
    }
}
