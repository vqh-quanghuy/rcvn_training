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
        // Get Params from search 
        $customerName = \Request::get('name') ?: null;
        $customerEmail = \Request::get('email') ?: null;
        $customerStatus = \Request::get('customer_status');
        if(!is_numeric($customerStatus) && $customerStatus === '') $customerStatus = null;
        $customerAddress = \Request::get('address') ?: null;

        $customerName = preg_replace('/[^a-z0-9 _]+/i', '', $customerName);
        $customerEmail = preg_replace('/[^a-z0-9 _]+/i', '', $customerEmail);
        $customerAddress = preg_replace('/[^a-z0-9 _]+/i', '', $customerAddress);

        $validator = Validator::make([
            'customer_name' => $customerName, 
            'customer_email' => $customerEmail,
            'customer_status' => $customerStatus,
            'customer_address' => $customerAddress,
        ], [
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|string',
            'customer_status' => 'nullable|integer',
            'customer_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $per_page = intval(\Request::get('per_page')) ?: 10;
        $customers = Customer::orderBy('created_at', 'desc');
        if(!empty($customerName)) $customers = $customers->where('customer_name', 'like', "%{$customerName}%");
        if(!empty($customerEmail)) $customers = $customers->where('email', 'like', "%{$customerEmail}%");
        if(!is_null($customerStatus)) $customers = $customers->where('is_active', $customerStatus);
        if(!empty($customerAddress)) $customers = $customers->where('address', 'like', "%{$customerAddress}%");
        
        $customers = $customers->paginate($per_page);

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
