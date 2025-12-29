<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Company extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Company::insert([
            [
                'name' => 'The Coca-Cola Company Brasil Ltda',
                'commercial_name' => 'Coca-Cola Brasil',
                'cnpj' => '10.000.000/0001-01',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola FEMSA Brasil S.A.',
                'commercial_name' => 'Coca-Cola FEMSA',
                'cnpj' => '10.000.000/0002-02',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Refrigerantes Bandeirantes S.A.',
                'commercial_name' => 'Coca-Cola Bandeirantes',
                'cnpj' => '10.000.000/0003-03',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Refrigerantes do Nordeste S.A.',
                'commercial_name' => 'Coca-Cola Nordeste',
                'cnpj' => '10.000.000/0004-04',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Refrigerantes do Sul Ltda',
                'commercial_name' => 'Coca-Cola Sul',
                'cnpj' => '10.000.000/0005-05',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Refrigerantes do Centro-Oeste Ltda',
                'commercial_name' => 'Coca-Cola Centro-Oeste',
                'cnpj' => '10.000.000/0006-06',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Refrigerantes do Norte S.A.',
                'commercial_name' => 'Coca-Cola Norte',
                'cnpj' => '10.000.000/0007-07',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Logística Brasil Ltda',
                'commercial_name' => 'Coca-Cola Logística',
                'cnpj' => '10.000.000/0008-08',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Distribuição Sudeste S.A.',
                'commercial_name' => 'Coca-Cola Sudeste',
                'cnpj' => '10.000.000/0009-09',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Distribuição Norte S.A.',
                'commercial_name' => 'Coca-Cola Norte Distribuição',
                'cnpj' => '10.000.000/0010-10',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Serviços Compartilhados Ltda',
                'commercial_name' => 'Coca-Cola Shared Services',
                'cnpj' => '10.000.000/0011-11',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Marketing Brasil Ltda',
                'commercial_name' => 'Coca-Cola Marketing',
                'cnpj' => '10.000.000/0012-12',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Supply Chain Brasil Ltda',
                'commercial_name' => 'Coca-Cola Supply',
                'cnpj' => '10.000.000/0013-13',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Tecnologia e Inovação Ltda',
                'commercial_name' => 'Coca-Cola Tech',
                'cnpj' => '10.000.000/0014-14',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Coca-Cola Participações Brasil Ltda',
                'commercial_name' => 'Coca-Cola Participações',
                'cnpj' => '10.000.000/0015-15',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Brasil Ltda',
                'commercial_name' => 'Nestlé Brasil',
                'cnpj' => '20.000.000/0001-01',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Waters Brasil S.A.',
                'commercial_name' => 'Nestlé Waters',
                'cnpj' => '20.000.000/0002-02',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Purina PetCare Brasil Ltda',
                'commercial_name' => 'Purina',
                'cnpj' => '20.000.000/0003-03',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Professional Brasil Ltda',
                'commercial_name' => 'Nestlé Professional',
                'cnpj' => '20.000.000/0004-04',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Health Science Brasil Ltda',
                'commercial_name' => 'Nestlé Health Science',
                'cnpj' => '20.000.000/0005-05',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Nutrição Infantil Ltda',
                'commercial_name' => 'Nestlé Nutrição',
                'cnpj' => '20.000.000/0006-06',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Chocolates Brasil Ltda',
                'commercial_name' => 'Nestlé Chocolates',
                'cnpj' => '20.000.000/0007-07',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Lácteos Brasil Ltda',
                'commercial_name' => 'Nestlé Lácteos',
                'cnpj' => '20.000.000/0008-08',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Cafés Brasil Ltda',
                'commercial_name' => 'Nescafé',
                'cnpj' => '20.000.000/0009-09',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Sorvetes Brasil Ltda',
                'commercial_name' => 'Nestlé Sorvetes',
                'cnpj' => '20.000.000/0010-10',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Alimentos e Bebidas Ltda',
                'commercial_name' => 'Nestlé Alimentos',
                'cnpj' => '20.000.000/0011-11',
                'publicity_trade' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Distribuição Brasil Ltda',
                'commercial_name' => 'Nestlé Distribuição',
                'cnpj' => '20.000.000/0012-12',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Logística Integrada Ltda',
                'commercial_name' => 'Nestlé Logística',
                'cnpj' => '20.000.000/0013-13',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Tecnologia e Inovação Ltda',
                'commercial_name' => 'Nestlé Tech',
                'cnpj' => '20.000.000/0014-14',
                'publicity_trade' => 0,
                'status' => 1,
            ],
            [
                'name' => 'Nestlé Participações Brasil Ltda',
                'commercial_name' => 'Nestlé Participações',
                'cnpj' => '20.000.000/0015-15',
                'publicity_trade' => 0,
                'status' => 1,
            ],
        ]);
    }
}
