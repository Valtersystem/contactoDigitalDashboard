<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Importe o model Category
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    /**
     * Mostra uma lista de produtos.
     */
    public function index(): Response
    {
        return Inertia::render('Products/Index', [
            // Usamos with('category') para carregar a relação e evitar queries N+1
            'products' => Product::with('category')->latest()->paginate(10)
        ]);
    }

    /**
     * Mostra o formulário para criar um novo produto.
     */
    public function create(): Response
    {
        return Inertia::render('Products/Create', [
            // Passamos todas as categorias para o formulário
            'categories' => Category::all(['id', 'name'])
        ]);
    }

    /**
     * Guarda um novo produto na base de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'required|string|max:100|unique:products,sku',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tracking_type' => ['required', Rule::in(['BULK', 'SERIALIZED'])],
            'stock_quantity' => 'required|integer|min:0',
            'replacement_value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso.');
    }

    /**
     * Mostra o formulário para editar um produto.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::all(['id', 'name']) // Também passamos as categorias aqui
        ]);
    }

    /**
     * Atualiza um produto na base de dados.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => ['required', 'string', 'max:100', Rule::unique('products')->ignore($product->id)],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tracking_type' => ['required', Rule::in(['BULK', 'SERIALIZED'])],
            'stock_quantity' => 'required|integer|min:0',
            'replacement_value' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * Apaga um produto.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Adicionar lógica de verificação de alugueis aqui no futuro
        // if ($product->rentals()->exists()) {
        //     return back()->with('error', 'Não é possível apagar um produto que já foi alugado.');
        // }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto apagado com sucesso.');
    }
}
