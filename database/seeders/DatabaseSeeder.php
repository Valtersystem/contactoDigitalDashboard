<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria um utilizador de teste padrão
        User::factory()->create([
            'name' => 'Utilizador de Teste',
            'email' => 'teste@exemplo.com',
        ]);

        // Cria Clientes
        Client::factory()->create([
            'name' => 'Ana Silva',
            'business_name' => 'Café Central',
            'nif' => '215478963',
            'email' => 'cafe.central@email.com',
        ]);
        Client::factory()->create([
            'name' => 'Carlos Pereira',
            'business_name' => 'Bar do Porto',
            'nif' => '289654123',
        ]);

        // Cria Categorias
        $catCopos = Category::create(['name' => 'Copos e Taças', 'slug' => Str::slug('Copos e Taças')]);
        $catEquipamento = Category::create(['name' => 'Equipamento de Bar', 'slug' => Str::slug('Equipamento de Bar')]);
        $catImpressoras = Category::create(['name' => 'Equipamento de Escritório', 'slug' => Str::slug('Equipamento de Escritório')]);

        // --- Cria Produtos ---

        // Exemplo de produto "Em Massa"
        Product::create([
            'name' => 'Copo Long Drink 300ml',
            'sku' => 'CP-LD-300',
            'category_id' => $catCopos->id,
            'tracking_type' => 'BULK',
            'stock_quantity' => 150,
            'replacement_value' => 2.50,
            'is_active' => true,
        ]);

        // Exemplo de produto "Por Número de Série"
        $impressora = Product::create([
            'name' => 'Impressora Brother DCPL2660DW',
            'sku' => 'BRO-DCP-L2660DW',
            'description' => 'Impressora multifunções 3 em 1 laser Monocromo WiFi com impressão automática a Doble Cara',
            'category_id' => $catImpressoras->id,
            'tracking_type' => 'SERIALIZED',
            'stock_quantity' => 0, // A quantidade é controlada pelos ativos
            'replacement_value' => 180.00,
            'is_active' => true,
        ]);

        // Adiciona 5 ativos (impressoras físicas) ao produto "Impressora"
        $impressora->assets()->createMany([
            ['serial_number' => 'DCPL2660DW-A001'],
            ['serial_number' => 'DCPL2660DW-A002'],
            ['serial_number' => 'DCPL2660DW-A003', 'status' => 'Em Manutenção'],
            ['serial_number' => 'DCPL2660DW-A004'],
            ['serial_number' => 'DCPL2660DW-A005'],
        ]);

        $shaker = Product::create([
            'name' => 'Shaker de Inox 500ml',
            'sku' => 'SHK-INOX-500',
            'category_id' => $catEquipamento->id,
            'tracking_type' => 'SERIALIZED',
            'stock_quantity' => 0,
            'replacement_value' => 15.00,
            'is_active' => true,
        ]);

        // Adiciona 10 ativos (shakers)
        for ($i = 1; $i <= 10; $i++) {
            $shaker->assets()->create([
                'serial_number' => 'SHK500-' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
