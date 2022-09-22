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
        $products = Product::paginate(5);

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
            'is_sale' => 'required|integer|between:0,1',
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
            'product_price' => 'required|numeric',
            'description' => 'nullable|string',
            'is_sale' => 'required|integer|between:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $inputs = $request->post();

        if ($image = $request->file('product_image')) {
            $destinationPath = 'images/';
            $productImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $productImage);
            $inputs['product_image'] = $productImage;
        } else {
            $inputs['product_image'] = '';
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
