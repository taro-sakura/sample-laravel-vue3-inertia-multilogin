<template>
    <AdminLayout title="ログ管理">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ログ管理
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">ログファイル一覧</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ファイル名</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">サイズ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">最終更新日</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs" :key="log.name">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ log.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatSize(log.size) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(log.last_modified) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.logs.show', log.name)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                表示
                                            </Link>
                                            <Link
                                                :href="route('admin.logs.download', log.name)"
                                                class="text-green-600 hover:text-green-900 mr-4"
                                            >
                                                ダウンロード
                                            </Link>
                                            <button
                                                @click="confirmDelete(log)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                削除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingLogDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    ログファイルを削除してもよろしいですか？
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    この操作は取り消すことができません。
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">キャンセル</SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteLog"
                    >
                        削除
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    logs: Array,
});

const confirmingLogDeletion = ref(false);
const logToDelete = ref(null);

const form = useForm({});

const confirmDelete = (log) => {
    logToDelete.value = log;
    confirmingLogDeletion.value = true;
};

const deleteLog = () => {
    form.delete(route('admin.logs.destroy', logToDelete.value.name), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingLogDeletion.value = false;
    logToDelete.value = null;
};

const formatDate = (timestamp) => {
    return new Date(timestamp * 1000).toLocaleString('ja-JP');
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script> 