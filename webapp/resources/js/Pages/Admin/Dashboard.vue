<template>
    <AdminLayout title="ダッシュボード">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ダッシュボード
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- ユーザー数 -->
                            <div class="bg-blue-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-800">総ユーザー数</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ stats.total_users }}</p>
                            </div>

                            <!-- 管理者数 -->
                            <div class="bg-green-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-green-800">総管理者数</h3>
                                <p class="text-3xl font-bold text-green-600">{{ stats.total_admins }}</p>
                            </div>

                            <!-- ログ数 -->
                            <div class="bg-purple-100 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-purple-800">総ログ数</h3>
                                <p class="text-3xl font-bold text-purple-600">{{ stats.total_logs }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">クイックアクセス</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <Link
                                    :href="route('admin.users.index')"
                                    class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow"
                                >
                                    <h4 class="font-medium text-gray-900">ユーザー管理</h4>
                                    <p class="text-sm text-gray-600">ユーザーの一覧と管理</p>
                                </Link>

                                <Link
                                    :href="route('admin.logs.index')"
                                    class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow"
                                >
                                    <h4 class="font-medium text-gray-900">ログ管理</h4>
                                    <p class="text-sm text-gray-600">システムログの確認</p>
                                </Link>

                                <Link
                                    :href="route('admin.settings.index')"
                                    class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow"
                                >
                                    <h4 class="font-medium text-gray-900">設定</h4>
                                    <p class="text-sm text-gray-600">システム設定の管理</p>
                                </Link>

                                <Link
                                    :href="route('admin.profile.show')"
                                    class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow"
                                >
                                    <h4 class="font-medium text-gray-900">プロフィール</h4>
                                    <p class="text-sm text-gray-600">プロフィール情報の管理</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
});
</script> 