<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Models\AdoptionCase;

interface AdoptionCaseServiceInterface
{
    /**
     * Get list of adoption cases for the user based on role.
     *
     * @param User $user
     * @param string $role 'adopter' or 'owner'
     * @return Collection
     */
    public function getUserCases(User $user, string $role): Collection;

    /**
     * Create a new adoption case.
     *
     * @param array $data
     * @param User $owner
     * @return AdoptionCase
     */
    public function createCase(array $data, User $owner): AdoptionCase;

    /**
     * Submit a tracking report for an adoption case.
     *
     * @param AdoptionCase $case
     * @param array $data
     * @param User $reporter
     * @return \App\Models\TrackingReport
     */
    public function submitReport(AdoptionCase $case, array $data, User $reporter): \App\Models\TrackingReport;

    /**
     * Get all tracking reports for an adoption case.
     *
     * @param AdoptionCase $case
     * @return Collection
     */
    public function getReports(AdoptionCase $case): Collection;

    /**
     * Create a manual adoption case (bypassing the application flow).
     *
     * @param array $data
     * @param User $creator
     * @return AdoptionCase
     */
    public function createManualCase(array $data, User $creator): AdoptionCase;
}
