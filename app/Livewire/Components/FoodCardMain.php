<?php

namespace App\Livewire\Components;

use App\Models\Foods;
use Livewire\Component;

class FoodCardMain extends Component
{
    public $data;
    public $categories;
    public $food;
    public $showGridView = true;

    public function mount($data, $categories, $showGridView = true, Foods $foods)
    {
        $this->data = $data;
        $this->categories = $categories;
        $this->showGridView = $showGridView;
        $this->food = $foods->getFoodDetails($data->id)->first();
    }
    

    public function showDetails()
    {
        return $this->redirect('/food/' . $this->data->id, navigate: true);
    }

    public function addToCart()
    {
        $cartItems = session('cart_items', []);

        $existingItemIndex = collect($cartItems)->search(fn($item) => $item['id'] === $this->food->id);

        if ($existingItemIndex !== false) {
            $cartItems[$existingItemIndex]['quantity'] += 1;
        } else {
            $cartItems[] = array_merge(
                (array)$this->food,
                [
                    'quantity' => 1,
                    'selected' => true,
                ]
            );
        }

        session(['cart_items' => $cartItems]);
        session(['has_unpaid_transaction' => false]);

        $this->dispatch('toast',
            data: [
                'message1' => 'Item added to cart',
                'message2' => $this->food->name,
                'type' => 'success',
            ]
        );
    }

    public function orderNow()
    {
        $this->addToCart();
        return redirect()->route('payment.checkout');
    }

    public function render()
    {
        return view('components.food-card-main');
    }
}
