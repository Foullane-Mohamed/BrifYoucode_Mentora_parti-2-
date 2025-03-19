<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * @var StatisticsService
     */
    protected $statisticsService;

    /**
     * StatisticsController constructor.
     * 
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->middleware('permission:statistics.view');
    }

    /**
     * Get overall statistics.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOverallStatistics()
    {
        try {
            $statistics = $this->statisticsService->getOverallStatistics();
            
            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching overall statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category statistics.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryStatistics()
    {
        try {
            $statistics = $this->statisticsService->getCategoryStatistics();
            
            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching category statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag statistics.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTagStatistics()
    {
        try {
            $statistics = $this->statisticsService->getTagStatistics();
            
            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tag statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get enrollment statistics.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnrollmentStatistics()
    {
        try {
            $statistics = $this->statisticsService->getEnrollmentStatistics();
            
            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollment statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get monthly enrollment statistics.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthlyEnrollmentStatistics(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $statistics = $this->statisticsService->getMonthlyEnrollmentStatistics($year);
            
            return response()->json([
                'statistics' => $statistics,
                'year' => $year,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching monthly enrollment statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course statistics by level.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseStatisticsByLevel()
    {
        try {
            $statistics = $this->statisticsService->getCourseStatisticsByLevel();
            
            return response()->json([
                'statistics' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course statistics by level',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}