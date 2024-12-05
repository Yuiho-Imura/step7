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

        DB::beginTransaction();

        try {
            $product = Product::find($productId);

            if (!$product) {
                return response()->json(['message' => '商品が存在しません'], 404);
            }
            if ($product->stock < $quantity) {
                return response()->json(['message' => '商品が在庫不足です'], 400);
            }

          
            $product->stock -= $quantity;
            $product->save();

            $sale = new Sale([
                'product_id' => $productId,
            ]);
            $sale->save();

           
            DB::commit();

            return response()->json(['message' => '購入成功']);
        } catch (\Exception $e) {
           
            DB::rollBack();

            return response()->json(['message' => '購入処理中にエラーが発生しました: ' . $e->getMessage()], 500);
        }
    }
}




