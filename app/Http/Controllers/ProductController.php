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
   //追加↓
    if ($company_id = $request->company_id) {
        $query->where('company_id', $company_id);
    }
    
    if($sort = $request->sort){
        $direction = $request->direction == 'desc' ? 'desc' : 'asc'; 
        $query->orderBy($sort, $direction);
    }

    
    $products = $query->paginate(10);

    $companies = Company::all();

    if ($request->ajax()) {
        return view('products.index', compact('products', 'companies'))->renderSections()['content'];
    }
    
    return view('products.index', ['products' => $products, 'companies' => $companies]);
    
}
   public function create()
{
    $companies = Company::all();
    return view('products.create', ['companies' => $companies]);
}

    public function store(ProductRequest $request) 
    {
    
    try {
        $imgPath = null;
        if ($request->hasFile('img_path')) {
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $imgPath = '/storage/' . $filePath;
        }

        $productData = $request->only(['product_name', 'company_id', 'price', 'stock', 'comment']);
        Product::createProduct($productData, $imgPath);

        return redirect('products');
    } catch (\Exception $e) {
        return back()->withErrors('商品登録に失敗しました: ' . $e->getMessage());
    }
}
//lここから上修正した点

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
    

    try {
        $imgPath = null;
        if ($request->hasFile('img_path')) {
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $imgPath = '/storage/' . $filePath;
        }

        $productData = $request->only(['product_name', 'company_id', 'price', 'stock', 'comment']);
        $product->updateProduct($productData, $imgPath);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    } catch (\Exception $e) {
        return back()->withErrors('更新に失敗しました: ' . $e->getMessage());
    }
}
//ここから上修正点
//ここから下修正なし
    public function destroy(Product $product)

    {
        
        try {
           $product->delete();
           return response()->json(['message' => '削除しました。'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '削除に失敗しました。'], 500);
       }
     


        
    }
}

