<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- Quick Actions -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <Link 
                    href="/orders"
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 hover:from-indigo-100 hover:to-indigo-200 dark:hover:from-indigo-800/30 dark:hover:to-indigo-700/30 transition-all duration-200 group"
                >
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center">
                        <div class="rounded-full bg-indigo-100 dark:bg-indigo-800/50 p-3 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-700/50 transition-colors">
                            <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100">My Orders</h3>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">View order history</p>
                    </div>
                </Link>
                
                <Link 
                    href="/cart"
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 hover:from-emerald-100 hover:to-emerald-200 dark:hover:from-emerald-800/30 dark:hover:to-emerald-700/30 transition-all duration-200 group"
                >
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center">
                        <div class="rounded-full bg-emerald-100 dark:bg-emerald-800/50 p-3 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-700/50 transition-colors">
                            <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Shopping Cart</h3>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">View cart items</p>
                    </div>
                </Link>
                
                <Link 
                    href="/products"
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-800/30 dark:hover:to-purple-700/30 transition-all duration-200 group"
                >
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center">
                        <div class="rounded-full bg-purple-100 dark:bg-purple-800/50 p-3 group-hover:bg-purple-200 dark:group-hover:bg-purple-700/50 transition-colors">
                            <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.001 3.001 0 01-3.75-.615A2.993 2.993 0 000 11.25c0-.85.343-1.617.896-2.184a3.001 3.001 0 013.204-5.152 2.993 2.993 0 01-.128-1.789A3.011 3.011 0 016.75 1.5a3.001 3.001 0 012.25 1.016z" />
                            </svg>
                        </div>
                        <h3 class="mt-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Browse Products</h3>
                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Shop our catalog</p>
                    </div>
                </Link>
            </div>
            
            <!-- Recent Orders -->
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white dark:bg-gray-900 p-6"
            >
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Orders</h2>
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No recent orders</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start shopping to see your orders here.</p>
                    <div class="mt-6">
                        <Link 
                            href="/products" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Start Shopping
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
