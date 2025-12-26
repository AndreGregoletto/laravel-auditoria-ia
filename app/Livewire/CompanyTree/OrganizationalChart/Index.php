<?php

namespace App\Livewire\CompanyTree\OrganizationalChart;

use App\Models\CompanyTree;
use Livewire\Component;

class Index extends Component
{
    public int $treeId;
    public array $tree = [];

    public function mount(int $company_tree): void
    {
        $this->treeId = $company_tree;

        $nodes = CompanyTree::query()
            ->where('company_tree_id', $this->treeId)
            ->with('company')
            ->get();

        $map = [];
        foreach ($nodes as $n) {
            $map[(int)$n->company_id] = [
                'id'         => (int)$n->company_id,
                'parent_id'  => (int)$n->company_parent_id,
                'company_id' => $n->company_id,
                'levels'     => (bool) $n->levels,
                'status'     => (bool) $n->status,
                'holding'    => (bool) $n->holding,
                'company'    => (object) $n->company,
                'children' => [],
            ];
        }

        $roots = [];
        foreach ($map as $id => &$node) {
            if ($node['parent_id'] === $node['id'] || !isset($map[$node['parent_id']])) {
                $roots[] = &$node;
            } else {
                $map[$node['parent_id']]['children'][] = &$node;
            }
        }
        unset($node);

        if (count($roots) === 1) {
            $this->tree = $roots[0];
        } else {
            $this->tree = [
                'id' => 0,
                'parent_id' => 0,
                'name' => 'Group',
                'active' => true,
                'holding' => true,
                'children' => $roots,
            ];
        }
    }

    public function render()
    {
        return view('livewire.company-tree.organizational-chart.index')->layout('layouts.app');
    }
}
