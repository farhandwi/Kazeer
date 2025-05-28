<?php
namespace App\Livewire\Pages;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Foods;
use App\Models\Category;

#[Layout('components.layouts.page')]
#[Title('MyFOOD - Home')]
class HomePage extends Component
{
    public $term = '';
    public $isCustomerDataComplete = false; // Ubah default ke false
    public $promos;
    public $foodsByCategory;
    public $favorites;
    public $categories;
    public $tableNumber;

    protected $listeners = ['saved-user-info' => 'handleUserInfoSaved'];

    public function mount()
    {
        // Inisialisasi data
        $this->categories = Category::all();
        
        $foods = new Foods();
        $this->promos = $foods->getPromo();
        $this->favorites = $foods->getFavoriteFood();
        $this->foodsByCategory = $foods->getFoodsByCategory();
        
        $this->tableNumber = session('table_number');
        
        // Cek apakah data customer sudah lengkap
        $name = session('name');
        $phone = session('phone');
        
        // Jika nama dan phone sudah ada, maka data sudah lengkap (modal tidak perlu ditampilkan)
        if ($name && $phone) {
            $this->isCustomerDataComplete = false;
        } else {
            $this->isCustomerDataComplete = true; // Tampilkan modal jika data belum lengkap
        }
    }

    public function handleUserInfoSaved()
    {
        // Tutup modal setelah data berhasil disimpan
        $this->isCustomerDataComplete = false;
        
        // Optional: Show success message
        session()->flash('message', 'Data berhasil disimpan!');
    }

    public function render()
    {
        $searchResult = collect();
        
        if (!empty(trim($this->term))) {
            $foods = new Foods();
            $searchResult = $foods->search(trim($this->term))->get();
        }
        
        return view('home', [
            'searchResult' => $searchResult
        ]);
    }
}