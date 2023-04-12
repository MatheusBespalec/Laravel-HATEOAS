<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerPostRequest;
use App\Http\Requests\CustomerPutRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Gender;
use App\Models\State;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customerQuery = Customer::query();

        if ($request->has('name')) {
            $customerQuery->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->has('cpf')) {
            $customerQuery->where('cpf', 'like', '%' . $request->input('cpf') . '%');
        }
        if ($request->has('birthdate_from')) {
            $date = DateTime::createFromFormat('Y-m-d', $request->input('birthdate_from'));
            if ($date) {
                $customerQuery->whereDate('birthdate', '>=', $request->input('birthdate_from'));
            }
        }
        if ($request->has('birthdate_until')) {
            $date = DateTime::createFromFormat('Y-m-d', $request->input('birthdate_until'));
            if ($date) {
                $customerQuery->whereDate('birthdate', '>=', $request->input('birthdate_until'));
            }
        }
        if ($request->has('gender')) {
            $gender = Gender::query()->where('abbreviation', $request->input('gender'))->first();
            if ($gender) {
                $customerQuery->where('gender_id', '=', $gender->id);
            }
        }
        if ($request->has('address') || $request->has('state') || $request->has('city')) {
            $customerQuery->whereHas('address', function ($query) use ($request) {
                if ($request->has('address')) {
                    $query->where('address', 'like', '%' . $request->input('address') . '%');
                }
                if ($request->has('state')) {
                    $state = State::query()->where('acronym', $request->input('state'))->first();
                    if ($state) {
                        $query->where('state_id', '=', $state->id);
                    }
                }
                if ($request->has('city')) {
                    $query->where('city', 'like', '%' . $request->input('city') . '%');
                }
            });
        }

        return new CustomerCollection($customerQuery->orderByDesc('id')->paginate(3));
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
    public function update(CustomerPutRequest $request, string $id)
    {
        $customer = Customer::query()->with(['address.state', 'gender'])->findOrFail($id);
        $validationRules = (new CustomerPostRequest)->rules();
        $validationRules['cpf'] = 'required|string|max:11|unique:customers,cpf,' . $id;
        $newCustomerData = $request->validate($validationRules);

        DB::beginTransaction();
        try {
            $customer->fill($newCustomerData);
            $customer->address->fill($newCustomerData['address']);

            if ($customer->address->state->acronym !== $newCustomerData['address']['state']) {
                $state = State::query()->where('acronym', $newCustomerData['address']['state'])->first();
                $customer->address->state_id = $state->id;
            }

            if ($customer->address->isDirty()) {
                $customer->address->save();
            }

            if ($customer->gender->abbreviation !== $newCustomerData['gender']) {
                $gender = Gender::query()->where('abbreviation', $newCustomerData['gender'])->first();
                $customer->address->gender_id = $gender->id;
            }

            if ($customer->isDirty()) {
                $customer->save();
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return new CustomerResource($customer);
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
