<?php

namespace App\Services;

use App\Contracts\AdoptionFormTemplateServiceInterface;
use App\Models\AdoptionFormTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AdoptionFormTemplateService implements AdoptionFormTemplateServiceInterface
{
    /**
     * Get all templates owned by the given user.
     */
    public function getByUser(int $userId): Collection
    {
        return AdoptionFormTemplate::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Create a new form template.
     */
    public function create(int $userId, array $data): AdoptionFormTemplate
    {
        return DB::transaction(function () use ($userId, $data) {
            // If this template is marked as default, unset existing defaults
            if (!empty($data['is_default'])) {
                AdoptionFormTemplate::where('user_id', $userId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            return AdoptionFormTemplate::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'schema' => $data['schema'],
                'is_default' => $data['is_default'] ?? false,
            ]);
        });
    }

    /**
     * Find a single template by ID (with ownership check).
     */
    public function findOrFail(int $id, int $userId): AdoptionFormTemplate
    {
        return AdoptionFormTemplate::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Update an existing template.
     */
    public function update(int $id, int $userId, array $data): AdoptionFormTemplate
    {
        return DB::transaction(function () use ($id, $userId, $data) {
            $template = $this->findOrFail($id, $userId);

            // If setting as default, unset existing defaults
            if (!empty($data['is_default'])) {
                AdoptionFormTemplate::where('user_id', $userId)
                    ->where('id', '!=', $id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'name' => $data['name'] ?? $template->name,
                'schema' => $data['schema'] ?? $template->schema,
                'is_default' => $data['is_default'] ?? $template->is_default,
            ]);

            return $template->fresh();
        });
    }

    /**
     * Delete a template.
     */
    public function delete(int $id, int $userId): void
    {
        $template = $this->findOrFail($id, $userId);
        $template->delete();
    }
}
