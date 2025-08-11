<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Get real statistics
        $totalPartners = Partner::count();
        $partnersThisMonth = Partner::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $partnersLastMonth = Partner::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Calculate change percentage for this month
        $monthlyChange = $partnersLastMonth > 0
            ? round((($partnersThisMonth - $partnersLastMonth) / $partnersLastMonth) * 100, 1)
            : ($partnersThisMonth > 0 ? 100 : 0);

        // Get unique categories count
        $categoriesCount = Partner::distinct('category')->count('category');

        // Get unique cities count  
        $citiesCount = Partner::distinct('city')->count('city');

        // Get recent partners for activity
        $recentPartners = Partner::with('user')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'title' => $partner->title,
                    'created_at' => $partner->created_at,
                    'created_by' => $partner->user->name ?? 'Unknown',
                    'action' => 'created'
                ];
            });

        // Prepare stats data
        $stats = [
            [
                'name' => 'Total Partners',
                'value' => (string) $totalPartners,
                'icon' => 'users',
                'change' => $totalPartners > 0 ? '+' . $totalPartners . ' total' : 'No partners yet',
                'changeType' => $totalPartners > 0 ? 'increase' : 'neutral'
            ],
            [
                'name' => 'New This Month',
                'value' => (string) $partnersThisMonth,
                'icon' => 'plus',
                'change' => $monthlyChange > 0 ? '+' . $monthlyChange . '%' : ($monthlyChange < 0 ? $monthlyChange . '%' : 'No change'),
                'changeType' => $monthlyChange > 0 ? 'increase' : ($monthlyChange < 0 ? 'decrease' : 'neutral')
            ],
            [
                'name' => 'Categories',
                'value' => (string) $categoriesCount,
                'icon' => 'tag',
                'change' => $categoriesCount > 0 ? $categoriesCount . ' active' : 'No categories',
                'changeType' => 'neutral'
            ],
            [
                'name' => 'Cities Covered',
                'value' => (string) $citiesCount,
                'icon' => 'map',
                'change' => $citiesCount > 0 ? $citiesCount . ' locations' : 'No cities',
                'changeType' => $citiesCount > 0 ? 'increase' : 'neutral'
            ]
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentPartners' => $recentPartners,
            'totalUsers' => User::count(),
            'activeUsers' => User::whereDate('updated_at', '>=', Carbon::now()->subDays(7))->count()
        ]);
    }
}
