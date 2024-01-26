<?php
namespace App\Http\Services;
use Illuminate\Support\Arr;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\OrderShipped;


use App\Jobs\SendMail;

use Psy\Readline\Hoa\Console;

class CartService
{
    public function create($request)
    {
        $qty = (int)$request->input('num_product');
        $product_id = (int)$request->input('product_id');

        if ($qty <= 0 || $product_id <= 0) {
            session()->flash('error', 'Số lượng hoặc Sản phẩm không chính xác');
            return false;
        }

        $carts = session()->get('carts');
        if (is_null($carts)) {
            session()->put('carts', [
                $product_id => $qty
            ]);
            return true;
        }

        $exists = Arr::exists($carts, $product_id);
        if ($exists) {
            $carts[$product_id] = $carts[$product_id] + $qty;
            session()->put('carts', $carts);
            return true;
        }

        $carts[$product_id] = $qty;
        session()->put('carts', $carts);
        return true;
    }

    public function getProduct()
    {
        $carts = session()->get('carts');
        if (is_null($carts)) return [];

        $productId = array_keys($carts);
        return Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->whereIn('id', $productId)
            ->get();
    }

    public function update($request)
    {
        session()->put('carts', $request->input('num_product'));

        return true;
    }
   
    public function remove($id)
    {
        $carts = session()->get('carts');
        unset($carts[$id]);

        session()->put('carts', $carts);
        return true;
    }

    public function addCart($request)
    {
        try {
            DB::beginTransaction();

            $carts = session()->get('carts');

            if (is_null($carts))
                return false;

            $customer = Customer::create([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'content' => $request->input('content')
            ]);

            $this->infoProductCart($carts, $customer->id);

            DB::commit();
            session()->flash('success', 'Đặt Hàng Thành Công');

            #Queue: Gửi mail sau 2 giây đặt hàng thành công
            SendMail::dispatch($request->input('email'), $carts, $this->getProduct())->delay(now()->addSeconds(2));

            session()->forget('carts');
        } catch (\Exception $err) {
            DB::rollBack();
            session()->flash('error', 'Đặt Hàng Lỗi, Vui lòng thử lại sau');
            return false;
        }

        return true;
    }

    protected function infoProductCart($carts, $customer_id)
    {
        $productId = array_keys($carts);
        $products = Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->whereIn('id', $productId)
            ->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'customer_id' => $customer_id,
                'product_id' => $product->id,
                'pty'   => $carts[$product->id],
                'price' => $product->price_sale != 0 ? $product->price_sale : $product->price
            ];
        }

        return Cart::insert($data);
    }

    public function getCustomer()
    {
        return Customer::orderByDesc('id')->paginate(15);
    }

    public function getProductForCart($customer)
    {
        return $customer->carts()->with(['product' => function ($query) {
            $query->select('id', 'name', 'thumb');
        }])->get();
    }
}