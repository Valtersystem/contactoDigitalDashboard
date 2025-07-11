<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class AssetController extends Controller
{
    /**
     * Mostra a lista de ativos para um produto específico.
     */
    public function index(Product $product): Response
    {
        // Garante que só se pode gerir ativos de produtos serializados
        if ($product->tracking_type !== 'SERIALIZED') {
            abort(404);
        }

        return Inertia::render('Assets/Index', [
            'product' => $product,
            // Carrega os ativos associados a este produto
            'assets' => $product->assets()->latest()->paginate(15),
        ]);
    }

    /**
     * Guarda um novo ativo para um produto específico.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'serial_number' => 'required|string|max:50|unique:assets,serial_number',
            'status' => 'sometimes|string', // Opcional, o default é 'Disponível'
            'notes' => 'nullable|string',
        ]);

        $product->assets()->create($validated);

        return back()->with('success', 'Ativo adicionado com sucesso.');
    }

    /**
     * Apaga um ativo específico.
     */
    public function destroy(Asset $asset): RedirectResponse
    {
        // Adicionar lógica para verificar se o ativo está num aluguel no futuro
        // if ($asset->isRented()) {
        //     return back()->with('error', 'Não é possível apagar um ativo que está alugado.');
        // }

        $asset->delete();

        return back()->with('success', 'Ativo apagado com sucesso.');
    }
}
