<?php

namespace Database\Seeders;

use App\Models\CompanyTree;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreeCompany extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyTree::insert([
            // ============================
            // COCA-COLA TREE (ROOT ID = 1)
            // ============================
            [
                'company_tree_id'   => 1,
                'company_id'        => 1,
                'company_parent_id' => 1,
                'holding'           => 1,
                'levels'            => 1,
                'status'            => 1,
            ],

            // Level 2
            [
                'company_tree_id'   => 1,
                'company_id'        => 2,
                'company_parent_id' => 1,
                'holding'           => 1,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 3,
                'company_parent_id' => 2,
                'holding'           => 0,
                'levels'            => 3,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 4,
                'company_parent_id' => 2,
                'holding'           => 0,
                'levels'            => 3,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 5,
                'company_parent_id' => 2,
                'holding'           => 0,
                'levels'            => 3,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 6,
                'company_parent_id' => 2,
                'holding'           => 0,
                'levels'            => 3,
                'status'            => 1,
            ],

            // Serviços
            [
                'company_tree_id'   => 1,
                'company_id'        => 8,
                'company_parent_id' => 1,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 12,
                'company_parent_id' => 1,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 13,
                'company_parent_id' => 1,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 14,
                'company_parent_id' => 1,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 1,
                'company_id'        => 15,
                'company_parent_id' => 1,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            // ============================
            // NESTLÉ TREE (ROOT ID = 16)
            // ============================
            [
                'company_tree_id'   => 16,
                'company_id'        => 16,
                'company_parent_id' => 16,
                'holding'           => 1,
                'levels'            => 1,
                'status'            => 1,
            ],

            [
                'company_tree_id'   => 16,
                'company_id'        => 17,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 18,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 19,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 20,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 21,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 22,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 23,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 24,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 25,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 26,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 27,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
            [
                'company_tree_id'   => 16,
                'company_id'        => 28,
                'company_parent_id' => 16,
                'holding'           => 0,
                'levels'            => 2,
                'status'            => 1,
            ],
        ]);
    }
}
