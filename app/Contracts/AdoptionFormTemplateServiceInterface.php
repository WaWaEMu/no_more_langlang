<?php

namespace App\Contracts;

interface AdoptionFormTemplateServiceInterface
{
    /**
     * Get all templates owned by the given user.
     */
    public function getByUser(int $userId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Create a new form template.
     */
    public function create(int $userId, array $data): \App\Models\AdoptionFormTemplate;

    /**
     * Find a single template by ID (with ownership check).
     */
    public function findOrFail(int $id, int $userId): \App\Models\AdoptionFormTemplate;

    /**
     * Update an existing template.
     */
    public function update(int $id, int $userId, array $data): \App\Models\AdoptionFormTemplate;

    /**
     * Delete a template.
     */
    public function delete(int $id, int $userId): void;
}
