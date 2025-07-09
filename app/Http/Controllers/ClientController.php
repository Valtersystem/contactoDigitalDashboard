<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response; // Importe Response
use Illuminate\Http\RedirectResponse; // Importe RedirectResponse
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Clients/Index', [
            // Adicionei paginação para o futuro
            'clients' => Client::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response // <-- Adicione o tipo de retorno
    {
        // Este método apenas mostra a página Vue do formulário de criação
        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // <-- Adicione o tipo de retorno
    {
        // 1. Validar os dados recebidos do formulário
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'nif' => 'required|string|max:9|unique:clients,nif',
            'business_name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // 2. Criar o novo cliente na base de dados
        Client::create($validated);

        // 3. Redirecionar de volta para a lista de clientes com uma mensagem de sucesso
        return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
    }

        public function edit(Client $client): Response
    {
        // O Laravel automaticamente encontra o cliente pelo ID na URL (Route Model Binding)
        // e passa-o como a variável $client.
        return Inertia::render('Clients/Edit', [
            'client' => $client
        ]);
    }

    /**
     * Atualiza o cliente na base de dados.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        // 1. Validar os dados. Note a regra 'Rule::unique' que ignora o cliente atual.
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'nif' => ['required', 'string', 'max:9', Rule::unique('clients')->ignore($client->id)],
            'business_name' => 'required|string|max:191',
            'email' => ['nullable', 'email', 'max:191', Rule::unique('clients')->ignore($client->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // 2. Atualiza os dados do cliente
        $client->update($validated);

        // 3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
    }

        public function destroy(Client $client): RedirectResponse
    {
        // Opcional, mas recomendado: verificar se o cliente tem alugueis associados.
        // Se tiver, impede a exclusão para manter a integridade dos dados.
        if ($client->rentals()->exists()) {
            // Redireciona de volta com uma mensagem de erro.
            return back()->with('error', 'Não é possível apagar um cliente que já possui alugueis.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente apagado com sucesso.');
    }
}
