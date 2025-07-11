<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class RentalController extends Controller
{
    /**
     * Mostra o formulário para criar um novo aluguel.
     */
    public function create(): Response
    {
        // Carregamos todos os dados necessários para o formulário
        $clients = Client::orderBy('business_name')->get(['id', 'business_name']);

        // Carregamos produtos ativos, e para os serializados, carregamos os seus ativos disponíveis
        $products = Product::where('is_active', true)
            ->with(['assets' => function ($query) {
                $query->where('status', 'Disponível');
            }])
            ->orderBy('name')
            ->get();

        return Inertia::render('Rentals/Create', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    /**
     * Guarda um novo aluguel na base de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'rental_date' => 'required|date',
            'expected_return_date' => 'required|date|after_or_equal:rental_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.asset_id' => 'nullable|exists:assets,id',
        ]);

        // Usamos uma transação para garantir que todas as operações são bem sucedidas
        DB::transaction(function () use ($validated) {
            // 1. Cria o registo do aluguel
            $rental = Rental::create([
                'client_id' => $validated['client_id'],
                'rental_date' => $validated['rental_date'],
                'expected_return_date' => $validated['expected_return_date'],
                'notes' => $validated['notes'],
                'status' => 'Alugado',
            ]);

            // 2. Itera sobre os itens do "carrinho"
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Adiciona o item ao aluguel
                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $product->id,
                    'quantity_rented' => $item['quantity'] ?? null,
                    'asset_id' => $item['asset_id'] ?? null,
                ]);

                // 3. Atualiza o estoque
                if ($product->tracking_type === 'BULK') {
                    $product->decrement('stock_quantity', $item['quantity']);
                } else { // SERIALIZED
                    Asset::where('id', $item['asset_id'])->update(['status' => 'Alugado']);
                }
            }
        });

        return redirect()->route('rentals.index')->with('success', 'Aluguel registado com sucesso!');
    }

    /**
     * Mostra a lista de todos os alugueis.
     */
    public function index(): Response
    {
        // Vamos criar esta página mais tarde
        $rentals = Rental::with('client')->latest()->paginate(10);
        return Inertia::render('Rentals/Index', [
            'rentals' => $rentals,
        ]);
    }
}
