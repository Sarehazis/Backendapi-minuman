<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $response = $this->default_response;
    
        try { 
            // Mengambil semua produk beserta kategori mereka
            $products = Product::with('category')->get();
            
            $response['success'] = true;
            $response['data'] = [
                'products' => $products,
            ];
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
    
        return response()->json($response);
    }

    public function store(StoreProductRequest $request)
    {
        $response = $this->default_response;

        try{
            
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->storeAs('project-images', $file->hashName(), 'public');
            } 

            
            $products = new Product();
            $products->name = $data['name'];
            $products->description = $data['description'];
            $products->price = $data['price'];
            $products->image = $path ?? null;
            $products->stock = $data['stock'];
            $products->category_id = $data['category_id'];
            $products->save();
            
            $response ['success'] = true;
            $response ['data'] = [
                'product' => $products->with('category')->find($products->id),
            ];

            $response['message'] = 'Product Drink created successfully';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function show(String $id)
    {
        $response = $this->default_response;

        try {
            $products = Product::with('category')->find($id);
            
            $response['success'] = true;
            $response['message'] = 'Product Drink found successfully';
            $response['data'] = [
                'product' => $products
            ];
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function update(UpdateProductRequest $request, String $id)
    {
        $response = $this->default_response;
     
        try {
            $data = $request->validated();
     
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->storeAs('project-images', $file->hashName(), 'public');
            }
     
            $products = Product::find($id);
     
            if ($products) {
                $products->name = $data['name'];
                $products->description = $data['description'];
                $products->stock = $data['stock'];
                $products->price = $data['price'];
     
                if ($request->hasFile('image')) {
                    $products->image = $path ?? null;
                }
                $products->category_id = $data['category_id'];
                $products->save();
     
                $response['success'] = true;
                $response['data'] = [
                    'products' => $products->with('category')->find($products->id),
                ];
                $response['message'] = 'Product Drink updated successfully';
            } else {
                $response['success'] = false;
                $response['message'] = 'Product Drink not found';
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
     
        return response()->json($response);
    }
    
    public function destroy(String $id)
    {
        $response = $this-> default_response;
 
        try{
            // Mencari data sesuai id
            $products = Product::find($id);
 
            if (!$products) {
                throw new Exception('Product Drink not found');
            }
            // Proses delete
            $products->delete();

            $products->delete();
            if ($products->image && Storage::disk('public')->exists($products->image)) {
                Storage::disk('public')->delete($products->image);
            }
 
            // Proses Update
            $response['success'] = true;
            $response['message'] = 'Product Drink Delete Succsessfully';
        }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
 
        return response()->json($response);
    }
}
