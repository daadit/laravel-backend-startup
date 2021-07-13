<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->productCategory = new ProductCategory();
    }

    public function list()
    {
        $data =  $this->productCategory->list();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditemukan',
            'user' => $data
        ]);
    }
}
