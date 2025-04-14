<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'institutional_taxes',
                'name' => 'Tributos institucionais',
                'description' => 'Tributos relacionados a igreja como instituição pessoa jurídica.',
            ],
            [
                'slug' => 'employment_taxes',
                'name' => 'Tributos trabalhistas',
                'description' => 'Tributos relacionados aos funcionários que trabalham na igreja e possuem vínculo trabalhista no regime CLT.',
            ],
            [
                'slug' => 'employees',
                'name' => 'Funcionários',
                'description' => 'Valores pagos aos funcionários como salários, ajuda de custo, aluguel, plano de saúde entre outros.',
            ],
            [
                'slug' => 'pastoral_ministry',
                'name' => 'Ministério pastoral',
                'description' => 'Valores pagos ao ministério pastoral da igreja, como remunerações, planos de saúde, aluguel, adicionais e etc.',
            ],
            [
                'slug' => 'fixed_expenses',
                'name' => 'Despesas fixas',
                'description' => 'Despesas fixas como contas de água, energia, telefone, aluguel e etc.',
            ],
        ];

        foreach ($categories as $category) {
            $categoryId = DB::table('payment_category')->insertGetId([
                'slug' => $category['slug'],
                'name' => $category['name'],
                'description' => $category['description'],
            ]);


            $items = match ($category['slug']) {
                'institutional_taxes' => [

                ],
                'employees_taxes' => [
                    ['slug' => 'darf', 'name' => 'DARF', 'description' => 'Documento de arrecadação de receitas federais para pagamento de impostos como IRPJ, IRPF, CSLL, PIS, COFINS entre outros.'],
                    ['slug' => 'gps_inss', 'name' => 'GPS/INSS', 'description' => 'A GPS é usada para pagar contribuições previdenciárias ao INSS que são usadas para financiar aposentadorias, pensões, auxílios e outros benefícios previdenciários.'],
                ],
                'employees' => [
                    ['slug' => 'monthly_remuneration', 'name' => 'Remuneração Mensal', 'description' => 'Valores pagos como remuneração mensal ao funcionário.'],
                ],
                'pastoral_ministry' => [
                    ['slug' => 'prebend', 'name' => 'Prebenda pastoral', 'description' => 'Valores pagos como remuneração mensal ao pastor.'],
                    ['slug' => 'health_plan', 'name' => 'Plano de saúde', 'description' => 'Plano de saúde do pastor.'],
                    ['slug' => 'house_rent', 'name' => 'Aluguel de moradia', 'description' => 'Valores pagos para o aluguel da moradia do pastor.'],
                    ['slug' => '13th_salary', 'name' => 'Décimo terceiro', 'description' => 'Valores pagos como décimo terceiro salário ao pastor.'],
                ],
                'fixed_expenses' => [
                    ['slug' => 'water_sewage', 'name' => 'Água e esgoto', 'description' => 'Despesa fixa da companhia de água e esgoto'],
                    ['slug' => 'electrical_energy', 'name' => 'Energia elétrica', 'description' => 'Despesa fixa da companhia de energia elétrica'],
                    ['slug' => 'credit_card', 'name' => 'Cartão de crédito', 'description' => 'Despesa fixa do cartão de crédito.'],
                ],
                default => [],
            };

            foreach ($items as $item) {
                DB::table('payment_item')->insert([
                    'payment_category_id' => $categoryId,
                    'slug' => $item['slug'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                ]);
            }
        }
    }
}
