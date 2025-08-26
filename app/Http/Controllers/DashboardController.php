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
        'name' => 'Gesamtanzahl Partner',
        'value' => (string) $totalPartners,
        'icon' => 'users',
        'change' => $totalPartners > 0 ? '+' . $totalPartners . ' insgesamt' : 'Noch keine Partner',
        'changeType' => $totalPartners > 0 ? 'increase' : 'neutral'
    ],
    [
        'name' => 'Neu in diesem Monat',
        'value' => (string) $partnersThisMonth,
        'icon' => 'trending-up',
        'change' => $monthlyChange > 0 ? '+' . $monthlyChange . '%' : ($monthlyChange < 0 ? $monthlyChange . '%' : 'Keine Veränderung'),
        'changeType' => $monthlyChange > 0 ? 'increase' : ($monthlyChange < 0 ? 'decrease' : 'neutral')
    ],
    [
        'name' => 'Kategorien',
        'value' => (string) $categoriesCount,
        'icon' => 'chart-bar',
        'change' => $categoriesCount > 0 ? $categoriesCount . ' aktiv' : 'Keine Kategorien',
        'changeType' => 'neutral'
    ],
    [
        'name' => 'Abgedeckte Städte',
        'value' => (string) $citiesCount,
        'icon' => 'map',
        'change' => $citiesCount > 0 ? $citiesCount . ' Standorte' : 'Keine Städte',
        'changeType' => $citiesCount > 0 ? 'increase' : 'neutral'
    ]
];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentPartners' => $recentPartners,
            'totalUsers' => User::count(),
            'activeUsers' => User::whereDate('updated_at', '>=', Carbon::now()->subDays(7))->count(),
            'meta' => [
                'title' => 'findemich - Admin Dashboard',
                'description' => 'findemich Admin Dashboard - Manage business partners, view analytics, and oversee platform operations with comprehensive admin tools.',
                'keywords' => 'admin dashboard, business management, partner analytics, findemich administration'
            ]
        ]);
    }
}