<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Mostra o dashboard da aplicação.
     */
    public function __invoke(): Response
    {
        // Contagem de Clientes Ativos
        $totalClients = Client::count();

        // Contagem de Produtos Ativos
        $totalProducts = Product::where('is_active', true)->count();

        // Contagem de Itens Atualmente Alugados
        // Para produtos BULK, somamos as quantidades. Para SERIALIZED, contamos os assets.
        $rentedBulkItems = RentalItem::whereHas('rental', fn ($q) => $q->where('status', 'Alugado'))
            ->whereHas('product', fn ($q) => $q->where('tracking_type', 'BULK'))
            ->sum('quantity_rented');
        $rentedSerializedItems = Asset::where('status', 'Alugado')->count();
        $totalItemsRented = $rentedBulkItems + $rentedSerializedItems;

        // Contagem de Aluguéis Atrasados
        $lateRentalsCount = Rental::where('status', 'Alugado')
            ->where('expected_return_date', '<', Carbon::now())
            ->count();

        // Últimos 5 aluguéis para mostrar numa lista
        $latestRentals = Rental::with('client:id,business_name')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalClients' => $totalClients,
                'totalProducts' => $totalProducts,
                'totalItemsRented' => $totalItemsRented,
                'lateRentalsCount' => $lateRentalsCount,
            ],
            'latestRentals' => $latestRentals,
        ]);
    }
}
