<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index()
    {   
        // Get Params from search
        $productName = \Request::get('name') ?: null;
        // Remove special chars from string
        $productName = preg_replace('/[^a-z0-9 _]+/i', '', $productName);
        $saleStatus = \Request::get('sale_status');
        if(!is_numeric($saleStatus) && $saleStatus === '') $saleStatus = null;
        $fromPrice = \Request::get('from_price') ?: null;
        $toPrice = \Request::get('to_price') ?: null;

        $validator = Validator::make([
            'product_name' => $productName, 
            'sale_status' => $saleStatus,
            'from_price' => $fromPrice,
            'to_price' => $toPrice,
        ], [
            'product_name' => 'nullable|string',
            'sale_status' => 'nullable|integer',
            'from_price' => 'nullable|numeric',
            'to_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $per_page = intval(\Request::get('per_page')) ?: 10;
        $products = Product::orderBy('created_at', 'desc');
        if(!empty($productName)) $products = $products->where('product_name', 'like', "%{$productName}%");
        if(!is_null($saleStatus)) $products = $products->where('is_sale', $saleStatus);
        if(!empty($fromPrice) && !empty($toPrice)) {
            $products = $products->whereBetween('product_price',  [$fromPrice, $toPrice]);
        } elseif (!empty($fromPrice)) {
            $products = $products->where('product_price', '>=', $fromPrice);
        } elseif (!empty($toPrice)) {
            $products = $products->where('product_price', '<=', $toPrice);
        }

        $products = $products->paginate($per_page);

        return response()->json([
            'status' => true,
            'data' => $products,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'product_price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_sale' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $productImage = '';
        if ($image = $request->file('product_image')) {
            $destinationPath = 'images/';
            $productImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $productImage);
        }


        $item = new Product();
        $item->product_name = $request->product_name;
        $item->product_image = $request->product_image;
        $item->product_price = $request->product_price;
        $item->product_image = $productImage;
        $item->description = $request->description;
        $item->is_sale = $request->is_sale;
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Created',
            'user' => $item,
        ], Response::HTTP_CREATED);
    }

    public function detail(Product $product)
    {
        return response()->json([
            'status' => true,
            'data' => $product,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_removed_image' => 'required|boolean',
            'product_price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_sale' => 'required|integer|between:0,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $inputs = $request->post();

        if($request->input('is_removed_image')) {
            $inputs['product_image'] = '';
        }
        if ($image = $request->file('product_image')) {
            $destinationPath = 'images/';
            $productImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $productImage);
            $inputs['product_image'] = $productImage;
        }

        $product->fill($inputs)->save();   

        return response()->json([
            'status' => true,
            'message' => 'Updated',
        ], Response::HTTP_ACCEPTED);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted',
        ], Response::HTTP_ACCEPTED);
    }
}
