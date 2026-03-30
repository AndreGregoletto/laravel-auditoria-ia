<?php

namespace App\Livewire\Register\BalanceSheet\Relater;

use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\PivotBalanceSheetReference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Copy extends Component
{
    public array $form = [];
    public array $companies = [];
    public array $companyTrees = [];
    public array $sourceOptions = [];

    public bool $showConfirmModal = false;

    public function mount(): void
    {
        $this->companies = Company::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->id => $item->commercial_name ?? $item->name
            ])
            ->toArray();

        $this->companyTrees = CompanyTree::query()
            ->where('levels', 1)
            ->with('company')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = $item->company?->commercial_name
                    ?? $item->company?->name
                    ?? 'Tree #' . $item->id;

                return [$item->company->id => $label];
            })
            ->toArray();

        $this->sourceOptions = $this->loadSourceOptions();

        $this->form = [
            'source_key'        => 'default',
            'target_company_id' => null,
            'target_tree_id'    => null,
        ];
    }

    private function loadSourceOptions(): array
    {
        $options = [
            'default' => __('reports.default_configuration'),
        ];

        $configs = PivotBalanceSheetReference::query()
            ->select('company_id', 'company_tree_id')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNotNull('company_id')
                  ->orWhereNotNull('company_tree_id');
            })
            ->distinct()
            ->get();

        foreach ($configs as $config) {
            $company = $config->company_id
                ? Company::find($config->company_id)
                : null;

            $tree = $config->company_tree_id
                ? CompanyTree::with('company')->find($config->company_tree_id)
                : null;

            $companyLabel = $company?->commercial_name ?? $company?->name;
            $treeLabel    = $tree?->company?->commercial_name ?? $tree?->company?->name ?? ($tree ? 'Tree #' . $tree->id : null);

            $key = ($config->company_id ?: 'null') . '|' . ($config->company_tree_id ?: 'null');

            if ($config->company_id && $config->company_tree_id) {
                $label = __('reports.company') . ': ' . $companyLabel . ' / ' . __('reports.tree') . ': ' . $treeLabel;
            } elseif ($config->company_id) {
                $label = __('reports.company') . ': ' . $companyLabel;
            } else {
                $label = __('reports.tree') . ': ' . $treeLabel;
            }

            $options[$key] = $label;
        }

        return $options;
    }

    public function openConfirm(): void
    {
        $this->validateForm();
        $this->showConfirmModal = true;
    }

    public function closeConfirm(): void
    {
        $this->showConfirmModal = false;
    }

    public function save(): void
    {
        $data = $this->validateForm();
        $this->showConfirmModal = false;

        [$sourceCompanyId, $sourceTreeId] = $this->parseSourceKey($data['source_key']);

        $targetCompanyId = $data['target_company_id'] ?: null;
        $targetTreeId    = $data['target_tree_id'] ?: null;

        DB::transaction(function () use ($sourceCompanyId, $sourceTreeId, $targetCompanyId, $targetTreeId) {
            $sourceQuery = PivotBalanceSheetReference::query()
                ->where('status', 1);

            $this->applyScopeFilter($sourceQuery, $sourceCompanyId, $sourceTreeId);

            $sourceItems = $sourceQuery->get();

            if ($sourceItems->isEmpty()) {
                throw new \RuntimeException(__('error.no_source_configuration_found'));
            }

            $targetQuery = PivotBalanceSheetReference::query()
                ->where('status', 1);

            $this->applyScopeFilter($targetQuery, $targetCompanyId, $targetTreeId);

            $targetQuery->update([
                'status' => 0,
                'alter_user_id' => Auth::id(),
                'updated_at' => now(),
            ]);

            $rows = $sourceItems->map(function ($item) use ($targetCompanyId, $targetTreeId) {
                return [
                    'balance_sheet_id' => $item->balance_sheet_id,
                    'value'            => $item->value,
                    'company_tree_id'  => $targetTreeId,
                    'company_id'       => $targetCompanyId,
                    'status'           => 1,
                    'create_user_id'   => Auth::id(),
                    'alter_user_id'    => Auth::id(),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            })->toArray();

            PivotBalanceSheetReference::insert($rows);
        });

        session()->flash('success', __('success.save'));

        $this->redirectRoute(
            'settings.register.asset-base-classification.relator.index'
            // navigate: true
        );
    }

    private function validateForm(): array
    {
        $data = $this->validate([
            'form.source_key'        => ['required', 'string'],
            'form.target_company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'form.target_tree_id'    => ['nullable', 'integer', 'exists:company_trees,id'],
        ])['form'];

        if (empty($data['target_company_id']) && empty($data['target_tree_id'])) {
            $this->addError('form.target_company_id', __('error.select_company_or_tree'));
            throw new \Illuminate\Validation\ValidationException(
                validator([], [])
            );
        }

        return $data;
    }

    private function parseSourceKey(string $sourceKey): array
    {
        if ($sourceKey === 'default') {
            return [null, null];
        }

        [$companyId, $treeId] = explode('|', $sourceKey);

        return [
            $companyId === 'null' ? null : (int) $companyId,
            $treeId === 'null' ? null : (int) $treeId,
        ];
    }

    private function applyScopeFilter($query, ?int $companyId, ?int $treeId): void
    {
        if ($companyId === null) {
            $query->whereNull('company_id');
        } else {
            $query->where('company_id', $companyId);
        }

        if ($treeId === null) {
            $query->whereNull('company_tree_id');
        } else {
            $query->where('company_tree_id', $treeId);
        }
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.relater.copy')
            ->layout('layouts.app');
    }
}