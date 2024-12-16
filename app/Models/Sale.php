<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 
                        // 'フィールド2',
                        // 'フィールド3',
                          ]; 

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

//ここから追加
    public static function processPurchase(int $productId, int $quantity): void
    {
        DB::transaction(function () use ($productId, $quantity) {
           
            $product = Product::find($productId);

            if (!$product) {
                throw new \Exception('商品が存在しません');
            }

            if ($product->stock < $quantity) {
                throw new \Exception('商品が在庫不足です');
            }

           
            $product->stock -= $quantity;
            $product->save();

            
            self::create(['product_id' => $productId]);
        });
    }


}

