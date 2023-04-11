<?php

namespace App\Hateoas;

use App\Models\Customer;
use GDebrauwer\Hateoas\Link;
use GDebrauwer\Hateoas\Traits\CreatesLinks;

class CustomerHateoas
{
    use CreatesLinks;

    public function self(Customer $customer) : ?Link
    {
        return $this->link('customers.show', ['customer' => $customer->id]);
    }

    public function delete(Customer $customer) : ?Link
    {
        return $this->link('customers.destroy', ['customer' => $customer->id]);
    }

    public function replace(Customer $customer) : ?Link
    {
        return $this->link('customers.update', ['customer' => $customer->id]);
    }
}
