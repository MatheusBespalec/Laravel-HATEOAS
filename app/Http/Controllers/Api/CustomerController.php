<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerPostRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Gender;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CustomerCollection(Customer::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerPostRequest $request)
    {
        $state = State::query()->where('acronym', $request->input('address.state'))->firstOrFail();
        $gender = Gender::query()->where('abbreviation', $request->input('gender'))->firstOrFail();

        DB::beginTransaction();
        try {
            $address = Address::create([
                'address' => $request->input('address.address'),
                'city' => $request->input('address.city'),
                'state_id' => $state->id
            ]);
    
            $customer = Customer::create([
                'name' => $request->input('name'),
                'cpf' => $request->input('cpf'),
                'birthdate' => $request->input('birthdate'),
                'address_id' => $address->id,
                'gender_id' => $gender->id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::query()->whereId($id)->with(['address.state', 'gender'])->firstOrFail();
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Customer::findOrFail($id)->delete();

        return response()->noContent();
    }
}
