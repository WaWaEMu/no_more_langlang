<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Models\AdoptionCase;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AdoptionCaseServiceInterface;

class AdoptionCaseController extends Controller
{
    protected $adoptionCaseService;

    public function __construct(AdoptionCaseServiceInterface $adoptionCaseService)
    {
        $this->adoptionCaseService = $adoptionCaseService;
    }

    /**
     * Get list of adoption cases for the user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $request->query('role', 'owner');

        $cases = $this->adoptionCaseService->getUserCases($user, $role);

        return response()->json([
            'success' => true,
            'data' => $cases
        ]);
    }

    /**
     * Get a single adoption case with full details.
     * Only the owner or adopter can view.
     */
    public function show($id)
    {
        try {
            $result = $this->adoptionCaseService->getCaseDetails($id, Auth::user());

            return response()->json([
                'success' => true,
                'data' => $result['case'],
                'role' => $result['role'],
            ]);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], $statusCode);
        }
    }

    public function store(\App\Http\Requests\AdoptionCaseRequest $request)
    {
        try {
            $case = $this->adoptionCaseService->createCase($request->validated(), Auth::user());

            return response()->json([
                'success' => true,
                'message' => '結案成功，案件已建立',
                'data' => $case
            ], 201);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], $statusCode);
        }
    }

    /**
     * Submit a tracking report for an adoption case.
     * Only the adopter of the case can submit reports.
     */
    public function submitReport(\App\Http\Requests\TrackingReportRequest $request, $id)
    {
        try {
            $case = AdoptionCase::findOrFail($id);
            $user = Auth::user();

            // Only the adopter can submit reports
            if ($case->adopter_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '只有認養人可以提交回報'
                ], 403);
            }

            // Case must be active
            if ($case->status !== AdoptionCase::STATUS_ACTIVE) {
                return response()->json([
                    'success' => false,
                    'message' => '此案件已結束，無法提交回報'
                ], 400);
            }

            $report = $this->adoptionCaseService->submitReport($case, $request->validated() + ['images' => $request->file('images')], $user);

            return response()->json([
                'success' => true,
                'message' => '回報提交成功',
                'data' => $report->load('reporter:id,name')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }

    /**
     * Get all tracking reports for an adoption case.
     * Both adopter and owner can view reports.
     */
    public function getReports($id)
    {
        try {
            $case = AdoptionCase::findOrFail($id);
            $user = Auth::user();

            // Only adopter or owner can view reports
            if ($case->adopter_id !== $user->id && $case->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '無權限查看此案件的回報'
                ], 403);
            }

            $reports = $this->adoptionCaseService->getReports($case);

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }

    /**
     * Create a manual adoption case (bypassing the application flow).
     */
    public function storeManual(\App\Http\Requests\ManualCaseRequest $request)
    {
        try {
            $case = $this->adoptionCaseService->createManualCase(
                $request->validated() + ['pet_image' => $request->file('pet_image')],
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => '追蹤案件已建立',
                'data' => $case->load(['pet.images', 'owner', 'adopter'])
            ], 201);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], $statusCode);
        }
    }

    /**
     * Store a diary entry for an adoption case.
     * Both owner and adopter can create diary entries.
     */
    public function storeDiary(\App\Http\Requests\DiaryEntryRequest $request, $id)
    {
        try {
            $case = AdoptionCase::findOrFail($id);
            $user = Auth::user();

            if ($case->adopter_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '只有領養人可以發佈日誌'
                ], 403);
            }

            $entry = $this->adoptionCaseService->createDiaryEntry(
                $case,
                $request->validated() + ['photos' => $request->file('photos')],
                $user
            );

            return response()->json([
                'success' => true,
                'message' => '日誌發佈成功',
                'data' => $entry->load('author:id,name')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }

    /**
     * Get all diary entries for an adoption case.
     * Both owner and adopter can view diary entries.
     */
    public function getDiary($id)
    {
        try {
            $case = AdoptionCase::findOrFail($id);
            $user = Auth::user();

            if ($case->owner_id !== $user->id && $case->adopter_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '無權限查看此案件的日誌'
                ], 403);
            }

            $entries = $this->adoptionCaseService->getDiaryEntries($case);

            return response()->json([
                'success' => true,
                'data' => $entries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }

    /**
     * Store a comment on a diary entry.
     * Both owner and adopter of the related case can comment.
     */
    public function storeDiaryComment(\App\Http\Requests\DiaryCommentRequest $request, $entryId)
    {
        try {
            $entry = \App\Models\CaseDiaryEntry::findOrFail($entryId);
            $case = $entry->adoptionCase;
            $user = Auth::user();

            if ($case->owner_id !== $user->id && $case->adopter_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '無權限操作此案件'
                ], 403);
            }

            $comment = $this->adoptionCaseService->addDiaryComment(
                $entry,
                $request->validated()['content'],
                $user
            );

            return response()->json([
                'success' => true,
                'message' => '回覆成功',
                'data' => $comment->load('author:id,name')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }

    /**
     * Update tracking configuration for an adoption case.
     */
    public function updateTrackingConfig(Request $request, $id)
    {
        try {
            $case = AdoptionCase::findOrFail($id);
            $user = Auth::user();

            // Only the owner can update tracking config
            if ($case->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => '只有送養人可以修改追蹤設定'
                ], 403);
            }

            $validated = $request->validate([
                'frequency' => 'required|string|in:weekly,monthly,quarterly,none',
                'tracking_day' => 'nullable|integer|between:1,31',
                'tracking_start_month' => 'nullable|integer|between:1,12',
            ]);

            $case = $this->adoptionCaseService->updateTrackingConfig($case, $validated);

            return response()->json([
                'success' => true,
                'message' => '追蹤設定已更新',
                'data' => $case
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : '系統發生錯誤，請稍後再試'
            ], 500);
        }
    }
}
