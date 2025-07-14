<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetManagementController extends Controller
{
    /**
     * Mostra uma lista de ativos danificados e perdidos.
     */
    public function index(): Response
    {
        $assets = Asset::whereIn('status', ['Em Manutenção', 'Perdido'])
            ->with('product:id,name,replacement_value') // Carrega o produto associado para mostrar o nome
            ->latest()
            ->paginate(20);

        return Inertia::render('AssetManagement/Index', [
            'assets' => $assets,
        ]);
    }

    /**
     * Atualiza o status de um ativo.
     */
    public function updateStatus(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Disponível,Em Manutenção,Perdido',
        ]);

        $asset->update(['status' => $validated['status']]);

        return back()->with('success', 'Status do ativo atualizado com sucesso.');
    }
}
