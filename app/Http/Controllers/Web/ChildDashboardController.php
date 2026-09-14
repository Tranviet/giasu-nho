<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Subject;
use App\Services\QuotaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildDashboardController extends Controller
{
    /**
     * Display the Child Dashboard / Home Screen.
     */
    public function index(Request $request, QuotaService $quotaService): View
    {
        $user = $request->user();

        // 1. Resolve Child Profile (Use logged in parent's child, or friendly fallback demo child)
        if ($user && $user->children()->exists()) {
            $child = $user->children()->first();
            $quotaRemaining = $quotaService->getRemainingQuota($user);
        } else {
            // Demo child profile for direct browser inspection & guest preview
            $child = new Child([
                'id' => 1,
                'name' => 'Bé Minh',
                'grade' => 2,
                'avatar' => 'tiger',
            ]);
            $quotaRemaining = 10;
        }

        // 2. Fetch Backing Subjects from Database
        $dbSubjects = \Illuminate\Support\Facades\Schema::hasTable('subjects')
            ? Subject::withCount('topics')->get()
            : collect();

        $subjectsList = [];

        // Add Active Subjects backed by database
        foreach ($dbSubjects as $dbSubj) {
            $isMath = $dbSubj->code === 'math';
            $subjectsList[] = [
                'code' => $dbSubj->code,
                'name' => $dbSubj->name,
                'icon' => $isMath ? '🧮' : '✍️',
                'topics_count' => $dbSubj->topics_count,
                'is_locked' => false,
                // Mock progress for demo visual display until child completes exercises
                // TODO: calculate from ChildTopicMastery once child is actively learning
                'progress' => $isMath ? 60 : 40,
            ];
        }

        // 3. Add Requested Additional Subjects in Locked / Coming Soon State
        // (As instructed: do not hardcode mock topics/exercises if no backend data exists yet)
        $subjectsList[] = [
            'code' => 'english',
            'name' => 'Tiếng Anh',
            'icon' => '🇬🇧',
            'topics_count' => 0,
            'is_locked' => true,
            'progress' => 0,
        ];

        $subjectsList[] = [
            'code' => 'science',
            'name' => 'Khoa học',
            'icon' => '🔬',
            'topics_count' => 0,
            'is_locked' => true,
            'progress' => 0,
        ];

        $subjectsList[] = [
            'code' => 'reading',
            'name' => 'Đọc sách',
            'icon' => '📚',
            'topics_count' => 0,
            'is_locked' => true,
            'progress' => 0,
        ];

        // 4. Stubbed Reward & Gamification Data
        // TODO: In V2, wire up to child_badges and streaks table
        $stars = 85;
        $streak = 3;
        $completedToday = 3;
        $goalToday = 5;

        // 5. Recent Lesson (Continue where left off)
        $recentLesson = [
            'subject' => 'Toán học',
            'subject_code' => 'math',
            'topic_name' => 'Phép cộng có nhớ trong phạm vi 100',
            'completed_exercises' => 3,
            'total_exercises' => 5,
            'percentage' => 60,
        ];

        return view('child.dashboard', [
            'child' => $child,
            'subjects' => $subjectsList,
            'quotaRemaining' => $quotaRemaining,
            'stars' => $stars,
            'streak' => $streak,
            'completedToday' => $completedToday,
            'goalToday' => $goalToday,
            'recentLesson' => $recentLesson,
        ]);
    }
}
