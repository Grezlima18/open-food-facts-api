<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->paginate(10);
    }

    public function getProductByCode($code)
    {
        return $this->productRepository->findByCode($code);
    }

    public function updateProduct($code, array $data)
    {
        return $this->productRepository->updateByCode($code, $data);
    }

    public function deleteProduct($code)
    {
        return $this->productRepository->updateByCode($code, ['status' => 'trash']);
    }
}
