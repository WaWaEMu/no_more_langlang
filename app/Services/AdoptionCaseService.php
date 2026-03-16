<?php

namespace App\Services;

use App\Models\AdoptionCase;
use App\Models\AdoptionApplication;
use App\Models\CaseDiaryEntry;
use App\Models\DiaryComment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use App\Models\TrackingReport;

class AdoptionCaseService implements AdoptionCaseServiceInterface
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get list of adoption cases for the user based on role.
     *
     * @param User $user
     * @param string $role 'adopter' or 'owner'
     * @return Collection
     */
    public function getUserCases(User $user, string $role): Collection
    {
        if ($role === 'adopter') {
            return AdoptionCase::forAdopter($user->id)
                ->latest('started_at')
                ->get();
        }

        return AdoptionCase::forOwner($user->id)
            ->latest('started_at')
            ->get();
    }

    public function createCase(array $data, User $owner): AdoptionCase
    {
        try {
            $pet = Pet::findOrFail($data['pet_id']);

            // Validate ownership
            if ($pet->user_id !== $owner->id) {
                throw new \Exception('無權限：您不是此寵物的所有者', 403);
            }

            // Validate pet is not already adopted
            if ($pet->status === Pet::STATUS_ADOPTED) {
                throw new \Exception('此寵物已被領養', 400);
            }

            // Validate application status if provided
            if (!empty($data['application_id'])) {
                $application = AdoptionApplication::find($data['application_id']);
                if (!$application) {
                    throw new \Exception('申請不存在', 404);
                }
                if ($application->status !== 'approved') {
                    throw new \Exception('申請狀態必須為已核准才能結案', 400);
                }
            }

            return AdoptionCase::finalize($data, $owner);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('寵物不存在', 404);
        } catch (\Exception $e) {
            // Re-throw the exception to be handled by the controller's catch block
            throw $e;
        }
    }

    /**
     * Submit a tracking report for an adoption case.
     */
    public function submitReport(AdoptionCase $case, array $data, User $reporter): TrackingReport
    {
        return DB::transaction(function () use ($case, $data, $reporter) {
            // 1. Handle image uploads
            $imagePaths = [];
            if (!empty($data['images'])) {
                $date = now()->format('Y_m_d');
                foreach ($data['images'] as $index => $image) {
                    $filename = "report_{$case->id}_{$index}_" . time() . '.' . $image->guessExtension();
                    $path = $image->storeAs("images/reports/{$date}", $filename, 'public');
                    $imagePaths[] = $path;
                }
            }

            // 2. Create the tracking report
            $report = TrackingReport::create([
                'adoption_case_id' => $case->id,
                'reporter_id' => $reporter->id,
                'content' => $data['content'],
                'images' => !empty($imagePaths) ? $imagePaths : null,
                'reported_at' => now(),
            ]);

            // 3. Update case tracking dates
            $case->update([
                'last_report_at' => now(),
                'next_report_due_at' => AdoptionCase::calculateNextReportDate($case->tracking_config),
            ]);

            // 4. Notify the pet owner (in-app + email)
            $this->notificationService->notifyTrackingReport($case->id, $reporter->id, $data['content']);

            return $report;
        });
    }

    /**
     * Get all tracking reports for an adoption case.
     */
    public function getReports(AdoptionCase $case): Collection
    {
        return $case->reports()->with('reporter:id,name')->get();
    }

    /**
     * Create a manual adoption case (bypassing the application flow).
     * Creates a simplified Pet record and an AdoptionCase in one transaction.
     */
    public function createManualCase(array $data, User $creator): AdoptionCase
    {
        return DB::transaction(function () use ($data, $creator) {
            // 1. Handle pet image upload
            $imagePath = null;
            if (!empty($data['pet_image'])) {
                $date = now()->format('Y_m_d');
                $filename = 'manual_' . time() . '.' . $data['pet_image']->guessExtension();
                $imagePath = $data['pet_image']->storeAs("images/{$date}", $filename, 'public');
            }

            // 2. Create Pet record with full details
            $typeMap = ['dog' => '狗狗', 'cat' => '貓咪'];

            $pet = Pet::create([
                'title' => $data['pet_name'] . ' 的追蹤紀錄',
                'name' => $data['pet_name'],
                'type' => $typeMap[$data['pet_type']] ?? $data['pet_type'],
                'user_id' => $creator->id,
                'status' => Pet::STATUS_ADOPTED,
                'gender' => $data['gender'],
                'age' => $data['age'],
                'color' => $data['color'],
                'fur_type' => $data['fur_type'],
                'is_neuter' => $data['is_neuter'],
                'is_stray' => $data['is_stray'],
                'city' => $data['city'],
                'town' => $data['town'],
            ]);

            // 3. Save pet image if uploaded
            if ($imagePath) {
                $pet->images()->create([
                    'path' => $imagePath,
                    'type' => 'preview',
                    'index' => 0,
                ]);
            }

            // 4. Determine owner_id and adopter_id based on role
            $ownerId = null;
            $adopterId = null;

            if ($data['role'] === 'owner') {
                $ownerId = $creator->id;
                $adopterId = $data['counterpart_id'] ?? null;
            } else {
                $adopterId = $creator->id;
                $ownerId = $data['counterpart_id'] ?? null;
            }

            // 5. Create the adoption case
            return AdoptionCase::createManual([
                'pet_id' => $pet->id,
                'owner_id' => $ownerId,
                'adopter_id' => $adopterId,
                'tracking_config' => $data['tracking_config'] ?? null,
            ]);
        });
    }

    /**
     * Get details of a single adoption case for a specific user.
     */
    public function getCaseDetails(int $id, User $user): array
    {
        $case = AdoptionCase::with([
            'pet.images',
            'pet.detail',
            'pet.adoptionFormTemplate',
            'owner:id,name,email',
            'adopter:id,name,email',
            'application.user:id,name',
            'reports.reporter:id,name',
        ])->findOrFail($id);

        if ($case->owner_id !== $user->id && $case->adopter_id !== $user->id) {
            throw new \Exception('無權限查看此案件', 403);
        }

        return [
            'case' => $case,
            'role' => $case->owner_id === $user->id ? 'owner' : 'adopter',
        ];
    }

    /**
     * Create a diary entry for an adoption case.
     */
    public function createDiaryEntry(AdoptionCase $case, array $data, User $author): CaseDiaryEntry
    {
        // Handle multi-photo upload
        $date = now()->format('Y_m_d');
        $photoPaths = [];

        foreach ($data['photos'] as $index => $photo) {
            $filename = "diary_{$case->id}_" . time() . "_{$index}." . $photo->guessExtension();
            $photoPaths[] = $photo->storeAs("images/diary/{$date}", $filename, 'public');
        }

        return CaseDiaryEntry::create([
            'adoption_case_id' => $case->id,
            'author_id' => $author->id,
            'photos' => $photoPaths,
            'content' => $data['content'] ?? null,
            'location' => $data['location'] ?? null,
        ]);
    }

    /**
     * Get all diary entries for an adoption case.
     */
    public function getDiaryEntries(AdoptionCase $case): Collection
    {
        return $case->diaryEntries()
            ->with(['author:id,name', 'comments.author:id,name'])
            ->get();
    }

    /**
     * Add a comment to a diary entry.
     */
    public function addDiaryComment(CaseDiaryEntry $entry, string $content, User $author): DiaryComment
    {
        return DiaryComment::create([
            'diary_entry_id' => $entry->id,
            'author_id' => $author->id,
            'content' => $content,
        ]);
    }

    /**
     * Update the tracking configuration for an adoption case.
     */
    public function updateTrackingConfig(AdoptionCase $case, array $config): AdoptionCase
    {
        $case->update([
            'tracking_config' => $config,
            'next_report_due_at' => AdoptionCase::calculateNextReportDate($config),
        ]);

        return $case;
    }

    public function deleteCase(AdoptionCase $case): bool
    {
        return DB::transaction(function () use ($case) {
            $pet = $case->pet;

            if ($case->source === AdoptionCase::SOURCE_MANUAL) {
                // Manual cases: delete the pet entirely (will cascade delete the case)
                return $pet->delete();
            } else {
                // Platform cases: revert pet status back to available
                if ($pet) {
                    $pet->update(['status' => Pet::STATUS_AVAILABLE]);
                }
                return $case->delete();
            }
        });
    }
}
