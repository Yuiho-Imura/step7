<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB; 

class SalesController extends Controller
{
    public function purchase(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

   
    try {
        
        Sale::processPurchase($productId, $quantity);

        return response()->json(['message' => '購入成功']);
    } catch (\Exception $e) {
        return response()->json(['message' => '購入処理中にエラーが発生しました: ' . $e->getMessage()], 500);
    }
}
}




