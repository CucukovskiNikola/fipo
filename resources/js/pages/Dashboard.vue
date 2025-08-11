<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import { route } from 'ziggy-js';

interface Partner {
    id: number;
    title: string;
    created_at: string;
    created_by: string;
    action: string;
}

interface Stat {
    name: string;
    value: string;
    icon: string;
    change: string;
    changeType: 'increase' | 'decrease' | 'neutral';
}

interface Props {
    stats: Stat[];
    recentPartners: Partner[];
    totalUsers: number;
    activeUsers: number;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const quickActions = [
    {
        title: 'Create Partner',
        description: 'Add a new partner location',
        icon: 'plus',
        route: 'partners.create'
    },
    {
        title: 'View All Partners',
        description: 'Browse and manage partners',
        icon: 'eye',
        route: 'partners.index'
    },
    {
        title: 'Edit Partners',
        description: 'Update partner information',
        icon: 'pencil',
        route: 'partners.index'
    },
    {
        title: 'Delete Partners',
        description: 'Remove partner locations',
        icon: 'trash',
        route: 'partners.index'
    },
    {
        title: 'Map Overview',
        description: 'View partners on map',
        icon: 'map',
        route: 'map-demo'
    }
];

const formatTimeAgo = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes === 1 ? '' : 's'} ago`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} hour${diffInHours === 1 ? '' : 's'} ago`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) return `${diffInDays} day${diffInDays === 1 ? '' : 's'} ago`;
    
    return date.toLocaleDateString();
};
</script>

<template>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <!-- Welcome Section -->
            <div
                class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                <h1 class="mb-2 text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Partner Management Dashboard
                </h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Manage your partner locations, categories, and view analytics from this central hub.
                </p>
            </div>

            <!-- Stats Grid -->
            <div
                class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                <h2 class="mb-6 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Quick Actions</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="action in quickActions" :key="action.title" :href="route(action.route)"
                        class="group rounded-lg border border-[#e3e3e0] p-6 transition-colors hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]">
                    <div class="flex items-start space-x-4">
                        <div
                            class="rounded-md bg-[#FDFDFC] p-2 group-hover:bg-[#f8f8f7] dark:bg-[#0a0a0a] dark:group-hover:bg-[#1a1a19]">
                            <Icon :name="action.icon" class="h-6 w-6 text-[#706f6c] dark:text-[#A1A09A]" />
                        </div>
                        <div>
                            <h3 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ action.title }}</h3>
                            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ action.description }}</p>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>

            <!-- Quick Actions -->

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="stat in props.stats" :key="stat.name"
                    class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ stat.name }}</p>
                            <p class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ stat.value }}</p>
                        </div>
                        <div class="rounded-md bg-[#FDFDFC] p-3 dark:bg-[#0a0a0a]">
                            <Icon :name="stat.icon" class="h-6 w-6 text-[#706f6c] dark:text-[#A1A09A]" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span :class="[
                            stat.changeType === 'increase'
                                ? 'text-green-600 dark:text-green-400'
                                : stat.changeType === 'decrease'
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-[#706f6c] dark:text-[#A1A09A]'
                        ]">
                            {{ stat.change }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Overview -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Activity -->
                <div
                    class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Recent Activity</h2>
                        <Link :href="route('partners.index')"
                            class="text-sm text-[#1b1b18] hover:text-[#706f6c] dark:text-[#EDEDEC] dark:hover:text-[#A1A09A]">
                        View all →
                        </Link>
                    </div>
                    <div class="space-y-4">
                        <div v-if="props.recentPartners.length === 0" class="text-center py-8">
                            <Icon name="building-office" class="mx-auto h-8 w-8 text-[#706f6c] dark:text-[#A1A09A]" />
                            <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">No recent partner activity</p>
                        </div>
                        <div v-else v-for="partner in props.recentPartners" :key="partner.id" class="flex items-center space-x-3 rounded-md bg-[#FDFDFC] p-3 dark:bg-[#0a0a0a]">
                            <div class="rounded-full bg-green-100 p-2 dark:bg-green-900/30">
                                <Icon name="building-office" class="h-4 w-4 text-green-600 dark:text-green-400" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                    "{{ partner.title }}" {{ partner.action }} by {{ partner.created_by }}
                                </p>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ formatTimeAgo(partner.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Overview -->
                <div
                    class="rounded-lg bg-white p-6 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
                    <h2 class="mb-6 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">System Overview</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between rounded-md bg-[#FDFDFC] p-3 dark:bg-[#0a0a0a]">
                            <div class="flex items-center space-x-3">
                                <Icon name="check-circle" class="h-5 w-5 text-green-500" />
                                <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Partner System</span>
                            </div>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Operational</span>
                        </div>
                        <div class="flex items-center justify-between rounded-md bg-[#FDFDFC] p-3 dark:bg-[#0a0a0a]">
                            <div class="flex items-center space-x-3">
                                <Icon name="users" class="h-5 w-5 text-blue-500" />
                                <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Total Users</span>
                            </div>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ props.totalUsers }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-md bg-[#FDFDFC] p-3 dark:bg-[#0a0a0a]">
                            <div class="flex items-center space-x-3">
                                <Icon name="clock" class="h-5 w-5 text-purple-500" />
                                <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Active This Week</span>
                            </div>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ props.activeUsers }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>