<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = intval(\Request::get('per_page')) ?: 10;
        $customers = Customer::orderBy('created_at', 'desc')->paginate($per_page);

        return response()->json([
            'status' => true,
            'data' => $customers,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255|min:5',
            'tel_num' => 'required|string|max:14',
            'email' => 'required|string|email|max:255|unique:customers',
            'address' => 'required|string|max:255',
            'password' => 'required|string|confirmed|min:8',
            'is_active' => 'required|integer|between:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $item = new Customer();
        $item->customer_name = $request->customer_name;
        $item->address = $request->address;
        $item->tel_num = $request->tel_num;
        $item->email = $request->email;
        $item->is_active = $request->is_active;
        $item->password = Hash::make($request->password);
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Created',
            'user' => $item,
        ], Response::HTTP_CREATED);
    }

    public function detail(Customer $customer)
    {
        return response()->json([
            'status' => true,
            'data' => $customer,
        ], Response::HTTP_OK);
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255|min:5',
            'tel_num' => 'required|string|max:14',
            'email' => 'required|string|email|max:255|unique:customers,email,'.$customer->customer_id.',customer_id',
            'address' => 'required|string|max:255',
            'is_active' => 'required|integer|between:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }
        $inputs = $request->post();
        // $inputs['password'] = Hash::make($request->post('password'));
        $customer->fill($inputs)->save();   

        return response()->json([
            'status' => true,
            'message' => 'Updated',
        ], Response::HTTP_ACCEPTED);
    }
}
