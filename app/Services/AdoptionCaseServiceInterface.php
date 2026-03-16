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

    /**
     * Get details of a single adoption case for a specific user.
     *
     * @param int $id
     * @param User $user
     * @return array Contains 'case' and 'role'
     */
    public function getCaseDetails(int $id, User $user): array;

    /**
     * Create a diary entry for an adoption case.
     *
     * @param AdoptionCase $case
     * @param array $data
     * @param User $author
     * @return \App\Models\CaseDiaryEntry
     */
    public function createDiaryEntry(AdoptionCase $case, array $data, User $author): \App\Models\CaseDiaryEntry;

    /**
     * Get all diary entries for an adoption case.
     *
     * @param AdoptionCase $case
     * @return Collection
     */
    public function getDiaryEntries(AdoptionCase $case): Collection;

    /**
     * Add a comment to a diary entry.
     *
     * @param \App\Models\CaseDiaryEntry $entry
     * @param string $content
     * @param User $author
     * @return \App\Models\DiaryComment
     */
    public function addDiaryComment(\App\Models\CaseDiaryEntry $entry, string $content, User $author): \App\Models\DiaryComment;

    /**
     * Update the tracking configuration for an adoption case.
     *
     * @param AdoptionCase $case
     * @param array $config
     * @return AdoptionCase
     */
    public function updateTrackingConfig(AdoptionCase $case, array $config): AdoptionCase;

    public function deleteCase(AdoptionCase $case): bool;
}
