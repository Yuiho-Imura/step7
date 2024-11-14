<?php
namespace App\Http\Controllers;

use App\Models\Product; 
use App\Models\Company; 
use Illuminate\Http\Request; 
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller 
{
    
    public function index(Request $request)
{
    $query = Product::query();
    
    
    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");
    }

    
    if($min_price = $request->min_price){
        $query->where('price', '>=', $min_price);
    }

    
    if($max_price = $request->max_price){
        $query->where('price', '<=', $max_price);
    }

    
    if($min_stock = $request->min_stock){
        $query->where('stock', '>=', $min_stock);
    }

    
    if($max_stock = $request->max_stock){
        $query->where('stock', '<=', $max_stock);
    }

    
    if($sort = $request->sort){
        $direction = $request->direction == 'desc' ? 'desc' : 'asc'; 
        $query->orderBy($sort, $direction);
    }

    
    $products = $query->paginate(10);

    $companies = Company::all();
    return view('products.index', ['products' => $products, 'companies' => $companies]);
    
}
   public function create()
{
    $companies = Company::all();
    return view('products.create', ['companies' => $companies]);
}

    public function store(ProductRequest $request) 
    {
        DB::beginTransaction();

    try {
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);
        
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }
        
        $product->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        return redirect('products');
    }

    public function show(Product $product)
    
    {
        
        return view('products.show', ['product' => $product]);
    
    }

    public function edit(Product $product)
    {
        
        $companies = Company::all();

        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();

    try {
        
        $product->product_name = $request->product_name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        $product->company_id = $request->company_id;
        

        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        $product->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        
    }

    public function destroy(Product $product)

    {

        try {
            
           $product->delete();
            return redirect()->route('products.index')->with('message', '商品が削除されました');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('message', '商品の削除中にエラーが発生しました: ' . $e->getMessage());
        } 


        
    }
}

