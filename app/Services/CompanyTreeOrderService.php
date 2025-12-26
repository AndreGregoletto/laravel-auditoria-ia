<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CompanyTreeOrderService
{
    /**
     * Sorts the tree nodes in pre-order (DFS pre-order):
     * Parent, then all descendants, then next sibling.
     *
     * @param Collection $nodes Collection<App\Models\CompanyTree>
     * @param int|null $rootCompanyId If null, detect roots (company_parent_id == company_id OR parent does not exist)
     * @param callable|null $childrenSort fn($a,$b) to sort children (by name, company_id, etc.)
     */
    public static function orderPreOrder(
        Collection $nodes,
        ?int $rootCompanyId = null,
        ?callable $childrenSort = null
    ): Collection {
        $byCompanyId = $nodes->keyBy('company_id');

        $childrenByParent = $nodes->groupBy('company_parent_id');

        $childrenSort ??= function ($a, $b) {
            $nameA = $a->company?->name ?? '';
            $nameB = $b->company?->commercial_name ?? '';
            $cmp = strcasecmp($nameA, $nameB);
            return $cmp !== 0 ? $cmp : ($a->company_id <=> $b->company_id);
        };

        $childrenByParent = $childrenByParent->map(function ($group) use ($childrenSort) {
            return $group->sort($childrenSort)->values();
        });

        $roots = collect();

        if ($rootCompanyId !== null) {
            if ($byCompanyId->has($rootCompanyId)) {
                $roots = collect([$byCompanyId->get($rootCompanyId)]);
            }
        } else {
            $roots = $nodes->filter(fn ($n) => (int)$n->company_parent_id === (int)$n->company_id)->values();

            if ($roots->isEmpty()) {
                $roots = $nodes->filter(fn ($n) => ! $byCompanyId->has((int)$n->company_parent_id))->values();
            }

            $roots = $roots->sort($childrenSort)->values();
        }

        $ordered = collect();
        $visited = [];

        $walk = function ($node) use (&$walk, &$ordered, &$visited, $childrenByParent) {
            $id = (int) $node->company_id;

            if (isset($visited[$id])) {
                return;
            }
            $visited[$id] = true;

            $ordered->push($node);

            $children = $childrenByParent->get((int)$node->company_id, collect());

            foreach ($children as $child) {
                if ((int)$child->company_id === (int)$child->company_parent_id) {
                    continue;
                }
                $walk($child);
            }
        };

        foreach ($roots as $root) {
            $walk($root);
        }

        foreach ($nodes as $n) {
            if (!isset($visited[(int)$n->company_id])) {
                $walk($n);
            }
        }

        return $ordered;
    }
}
