<?php

// まずは必要なモジュールを読み込んでいます。今回はProductとCompanyの情報と、リクエストの情報が必要です。
namespace App\Http\Controllers;

use App\Models\Product; // Productモデルを現在のファイルで使用できるようにするための宣言です。
use App\Models\Company; // Companyモデルを現在のファイルで使用できるようにするための宣言です。
use Illuminate\Http\Request; // Requestクラスという機能を使えるように宣言します
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;


// Requestクラスはブラウザに表示させるフォームから送信されたデータをコントローラのメソッドで引数として受け取ることができます。

class ProductController extends Controller //コントローラークラスを継承します（コントローラーの機能が使えるようになります）
{
    
    public function index(Request $request)
{
    // Productモデルに基づいてクエリビルダを初期化
    $query = Product::query();
    // この行の後にクエリを逐次構築していきます。
    // そして、最終的にそのクエリを実行するためのメソッド（例：get(), first(), paginate() など）を呼び出すことで、データベースに対してクエリを実行します。

    // 商品名の検索キーワードがある場合、そのキーワードを含む商品をクエリに追加
    if($search = $request->search){
        $query->where('product_name', 'LIKE', "%{$search}%");
    }

    // 最小価格が指定されている場合、その価格以上の商品をクエリに追加
    if($min_price = $request->min_price){
        $query->where('price', '>=', $min_price);
    }

    // 最大価格が指定されている場合、その価格以下の商品をクエリに追加
    if($max_price = $request->max_price){
        $query->where('price', '<=', $max_price);
    }

    // 最小在庫数が指定されている場合、その在庫数以上の商品をクエリに追加
    if($min_stock = $request->min_stock){
        $query->where('stock', '>=', $min_stock);
    }

    // 最大在庫数が指定されている場合、その在庫数以下の商品をクエリに追加
    if($max_stock = $request->max_stock){
        $query->where('stock', '<=', $max_stock);
    }

    // ソートのパラメータが指定されている場合、そのカラムでソートを行う
    if($sort = $request->sort){
        $direction = $request->direction == 'desc' ? 'desc' : 'asc'; // directionがdescでない場合は、デフォルトでascとする
        $query->orderBy($sort, $direction);
    }

    // 上記の条件(クエリ）に基づいて商品を取得し、10件ごとのページネーションを適用
    $products = $query->paginate(10);

    $companies = Company::all();
    return view('products.index', ['products' => $products, 'companies' => $companies]);
    

    // 商品一覧ビューを表示し、取得した商品情報をビューに渡す
   //return view('products.index', ['products' => $products]);
    //return view('products.index', Compact('products','companies'));
}

    public function create()
    {
        // 商品作成画面で会社の情報が必要なので、全ての会社の情報を取得
        $companies = Company::all();
        

        // 商品作成画面を表示します。その際に、先ほど取得した全ての会社情報を画面に渡す
        return view('products.create', compact('companies'));
    }

    // 送られたデータをデータベースに保存するメソッドです
    public function store(ProductRequest $request) // フォームから送られたデータを　$requestに代入して引数として渡している
    {
        DB::beginTransaction();

    try {
        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェック
        // ->validate()メソッドは送信されたリクエストデータが
        // 特定の条件を満たしていることを確認
        //$request->validate([
            //'product_name' => 'required', //requiredは必須という意味
           // 'company_id' => 'required',
            //'price' => 'required',
            //'stock' => 'required',
            //'comment' => 'nullable', //'nullable'はそのフィールドが未入力でもOKという意味
           //'img_path' => 'nullable|image|max:2048',
       // ]);
        // '|'はパイプと呼ばれる記号で、バリデーションルールを複数指定するときに使います
        // 'image'はそのフィールドが画像ファイルであることを指定するルールです
        
        // フォームが一部空欄のまま送信ボタンを押しても、フォームの画面にリダイレクトされ
        // フォームの値が未入力である旨の警告メッセージが表示されます
       // 新しく商品を作ります。そのための情報はリクエストから取得します。
        $product = new Product([
            'product_name' => $request->get('product_name'),
            'company_id' => $request->get('company_id'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'comment' => $request->get('comment'),
        ]);
        //new Product([]) によって新しい「Product」（レコード）を作成しています。
        //new を使うことで新しいインスタンスを作成することができます

        // リクエストに画像が含まれている場合、その画像を保存します。
        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }
        // $request->hasFile('img_path')は、ブラウザにアップロードされたファイルが存在しているかを確認
        // getClientOriginalName()はアップロードしたファイル名を取得するメソッドです。
       // storeAs('products', $filename, 'public')は
       //  アップロードされたファイルを特定の場所に特定の名前で保存するためのメソッドです
       //　今回はstorage/app/publicにproducts" ディレクトリが作られ保存されます
       //'products'：これはファイルを保存するディレクトリ（フォルダ）の名前を示しています。
       // この場合は 'products' という名前のディレクトリにファイルが保存されます。
    //$filename：これは保存するファイルの名前を示しています。
    // getClientOriginalName() メソッドで取得したオリジナルのファイル名がここに入ります。
    // 'public' ファイルのアクセス権限を示しています。'public' は公開設定で、誰でもこのファイルにアクセスすることができるようになります。

        // 作成したデータベースに新しいレコードとして保存します。
        $product->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect('products');
    }

    public function show(Product $product)
    //(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {
        // 商品詳細画面を表示します。その際に、商品の詳細情報を画面に渡します。
        return view('products.show', ['product' => $product]);
    //　ビューへproductという変数が使えるように値を渡している
    // ['product' => $product]でビューでproductを使えるようにしている
    // compact('products')と行うことは同じであるためどちらでも良い
    }

    public function edit(Product $product)
    {
        // 商品編集画面で会社の情報が必要なので、全ての会社の情報を取得します。
        $companies = Company::all();

        // 商品編集画面を表示します。その際に、商品の情報と会社の情報を画面に渡します。
        return view('products.edit', compact('product', 'companies'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();

    try {
        // リクエストされた情報を確認して、必要な情報が全て揃っているかチェック
       // $request->validate([
           //'product_name' => 'required',
            //'price' => 'required',
            //'stock' => 'required',
      // ]);


        // 商品の情報を更新
        $product->product_name = $request->product_name;
        //productモデルのproduct_nameをフォームから送られたproduct_nameの値に書き換える
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->comment = $request->comment;
        $product->company_id = $request->company_id;
        //$product->img_path = $request->img_path;

        if($request->hasFile('img_path')){ 
            $filename = $request->img_path->getClientOriginalName();
            $filePath = $request->img_path->storeAs('products', $filename, 'public');
            $product->img_path = '/storage/' . $filePath;
        }

        // 更新した商品を保存:$productに対して行われた変更をデータベースに保存するためのメソッド（機能）
        $product->save();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        return back();
    }

        // 全ての処理が終わったら、商品一覧画面に戻ります。
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
        // ビュー画面にメッセージを代入した変数(success)を送ります
    }

    public function destroy(Product $product)
//(Product $product) 指定されたIDで商品をデータベースから自動的に検索し、その結果を $product に割り当てます。
    {

        try {
            // 商品を削除します。
           $product->delete();
            return redirect()->route('products.index')->with('message', '商品が削除されました');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('message', '商品の削除中にエラーが発生しました: ' . $e->getMessage());
        } 


        
    }
}

