<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CourseRepositoryInterface
     */
    protected $courseRepository;

    /**
     * @var EnrollmentRepositoryInterface
     */
    protected $enrollmentRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * StatisticsService constructor.
     * 
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CourseRepositoryInterface $courseRepository
     * @param EnrollmentRepositoryInterface $enrollmentRepository
     * @param TagRepositoryInterface $tagRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CourseRepositoryInterface $courseRepository,
        EnrollmentRepositoryInterface $enrollmentRepository,
        TagRepositoryInterface $tagRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $enrollmentRepository;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Get overall statistics.
     * 
     * @return array
     */
    public function getOverallStatistics()
    {
        return [
            'total_users' => $this->userRepository->all()->count(),
            'total_mentors' => $this->userRepository->getUsersByRole('mentor')->count(),
            'total_students' => $this->userRepository->getUsersByRole('student')->count(),
            'total_courses' => $this->courseRepository->all()->count(),
            'published_courses' => $this->courseRepository->getPublishedCourses(100000)->count(),
            'total_enrollments' => $this->enrollmentRepository->all()->count(),
            'completed_enrollments' => $this->enrollmentRepository->all()->filter(function ($enrollment) {
                return $enrollment->completed_at !== null;
            })->count(),
            'total_categories' => $this->categoryRepository->all()->count(),
            'total_tags' => $this->tagRepository->all()->count(),
        ];
    }

    /**
     * Get category statistics.
     * 
     * @return array
     */
    public function getCategoryStatistics()
    {
        $categories = $this->categoryRepository->getCategoriesWithSubcategories();
        
        $stats = [];
        foreach ($categories as $category) {
            $courseCount = 0;
            foreach ($category->subcategories as $subcategory) {
                $courseCount += $subcategory->courses()->where('is_published', true)->count();
            }
            
            $stats[] = [
                'id' => $category->id,
                'name' => $category->name,
                'subcategories_count' => $category->subcategories->count(),
                'courses_count' => $courseCount,
            ];
        }
        
        return $stats;
    }

    /**
     * Get tag statistics.
     * 
     * @return array
     */
    public function getTagStatistics()
    {
        $tags = $this->tagRepository->all();
        
        $stats = [];
        foreach ($tags as $tag) {
            $coursesCount = $tag->courses()->where('is_published', true)->count();
            
            if ($coursesCount > 0) {
                $stats[] = [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'courses_count' => $coursesCount,
                ];
            }
        }
        
        // Sort by courses count
        usort($stats, function ($a, $b) {
            return $b['courses_count'] - $a['courses_count'];
        });
        
        return $stats;
    }

    /**
     * Get enrollment statistics.
     * 
     * @return array
     */
    public function getEnrollmentStatistics()
    {
        $enrollmentsByStatus = [
            'pending' => $this->enrollmentRepository->getEnrollmentsByStatus('pending')->count(),
            'approved' => $this->enrollmentRepository->getEnrollmentsByStatus('approved')->count(),
            'rejected' => $this->enrollmentRepository->getEnrollmentsByStatus('rejected')->count(),
        ];
        
        $completedEnrollments = $this->enrollmentRepository->all()->filter(function ($enrollment) {
            return $enrollment->completed_at !== null;
        })->count();
        
        $inProgressEnrollments = $enrollmentsByStatus['approved'] - $completedEnrollments;
        
        return [
            'by_status' => $enrollmentsByStatus,
            'completed' => $completedEnrollments,
            'in_progress' => $inProgressEnrollments,
        ];
    }

    /**
     * Get monthly enrollment statistics.
     * 
     * @param int $year
     * @return array
     */
    public function getMonthlyEnrollmentStatistics($year)
    {
        $stats = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $count = DB::table('enrollments')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
            
            $stats[$month] = $count;
        }
        
        return $stats;
    }

    /**
     * Get course statistics by level.
     * 
     * @return array
     */
    public function getCourseStatisticsByLevel()
    {
        $beginner = DB::table('courses')
            ->where('level', 'beginner')
            ->where('is_published', true)
            ->count();
            
        $intermediate = DB::table('courses')
            ->where('level', 'intermediate')
            ->where('is_published', true)
            ->count();
            
        $advanced = DB::table('courses')
            ->where('level', 'advanced')
            ->where('is_published', true)
            ->count();
        
        return [
            'beginner' => $beginner,
            'intermediate' => $intermediate,
            'advanced' => $advanced,
        ];
    }
}