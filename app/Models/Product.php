<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Product extends Model
{
    
    use HasFactory;

    
    protected $fillable = [
        'product_name',
        'price',
        'stock',
        'company_id',
        'comment',
        'img_path',
    ];

    
    
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

   
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


   //ここからしたが追加分
   
    public static function createProduct(array $data, $imgPath = null)
    {
        return DB::transaction(function () use ($data, $imgPath) {
            $product = new self($data); 
            if ($imgPath) {
                $product->img_path = $imgPath; 
            }
            $product->save(); 
            return $product; 
        });
    }

   
    public function updateProduct(array $data, $imgPath = null)
    {
        return DB::transaction(function () use ($data, $imgPath) {
            $this->fill($data); 
            if ($imgPath) {
                $this->img_path = $imgPath; 
            }
            $this->save(); 
            return $this; 
        });
    }

    public function deleteProduct()
    {
        return DB::transaction(function () {
            $this->delete(); 
        });
    }
}




