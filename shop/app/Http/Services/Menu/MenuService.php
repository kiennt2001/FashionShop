<?php

namespace App\Http\Services\Menu;

use App\Models\Menu;
use Illuminate\Support\Str;


use Illuminate\Http\Request;

class MenuService{

    public function getParent(){
        return Menu::where("parent_id",0)->get(); //Lấy toàn bộ sản phẩm có parent_id = 0
    }
    public function show()
    {
        return Menu::select('name', 'id')
            ->where('parent_id', 0)
            ->orderbyDesc('id')
            ->get();
    }
    public function getAll(){
        return Menu::orderbyDesc('id')->paginate(20);
    }
    public function create($request){
        try{
            Menu::create([
                'name' => (string) $request->input('name'),
                'parent_id' => (int) $request->input('parent_id'),
                'description' => (string) $request->input('description'),
                'content' => (string) $request->input('content'),
                'active' => (string) $request->input('active'),
            ]);
            session()->flash('success','Tạo danh mục thành công');
        }catch(\Exception $e){
            session()->flash('error', $e->getMessage());
            return false;
        }
        return true;
    }
    public function destroy($request){
        $id = (int) $request->input('id');
        $menu = Menu::where('id', $id)->first();
        if($menu){
            return Menu::where('id', $id)->orWhere('parent_id', $id)->delete();
        }
        return false;
    }

    public function update($request, $menu): bool{
        if($request->input('parent_id') != $menu->id){
            $menu->parent_id = (int) $request->input('parent_id');
        }
        $menu->name = (string) $request->input('name');
        $menu->description = (string) $request->input('description');
        $menu->content = (string) $request->input('content');
        $menu->active = (int) $request->input('active');
        $menu->save();

        session()->flash('success','Cập nhật thành công danh mục!');
        return true;
    }
    public function getId($id)
    {
        return Menu::where('id', $id)->where('active', 1)->firstOrFail();
    }

    public function getProduct($menu, $request)
    {
        $query = $menu->products()
            ->select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1);

        if ($request->input('price')) {
            $query->orderBy('price', $request->input('price'));
        }

        return $query
            ->orderByDesc('id')
            ->paginate(4)
            ->withQueryString();
    }
}