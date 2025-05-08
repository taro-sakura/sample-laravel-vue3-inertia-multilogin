<template>
    <AdminLayout title="二要素認証">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                二要素認証
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="!$page.props.auth.user.two_factor_enabled">
                            <p class="text-sm text-gray-600">
                                二要素認証が有効になっていません。
                            </p>
                        </div>

                        <div v-else>
                            <p class="text-sm font-semibold text-gray-600">
                                二要素認証が有効になりました。QRコードをスキャンして認証アプリを設定してください。
                            </p>

                            <div class="mt-4">
                                <div v-html="qrCode" />
                            </div>

                            <p class="mt-4 text-sm font-semibold text-gray-600">
                                リカバリーコードを保存してください。これらのコードは、二要素認証デバイスにアクセスできない場合に使用できます。
                            </p>

                            <div class="mt-4 p-4 font-mono text-sm bg-gray-100 rounded-lg">
                                <div v-for="code in recoveryCodes" :key="code" class="mt-1">
                                    {{ code }}
                                </div>
                            </div>

                            <div class="mt-4">
                                <PrimaryButton @click="$inertia.visit(route('admin.profile.show'))">
                                    完了
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
    qrCode: String,
    recoveryCodes: Array,
});
</script> 