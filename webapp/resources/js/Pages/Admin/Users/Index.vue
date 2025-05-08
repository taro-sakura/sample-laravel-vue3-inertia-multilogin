<template>
    <AdminLayout title="ユーザー管理">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ユーザー管理
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">ユーザー一覧</h3>
                            <Link
                                :href="route('admin.users.create')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                新規作成
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名前</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">メールアドレス</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(user.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                編集
                                            </Link>
                                            <button
                                                @click="confirmDelete(user)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                削除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="users.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    ユーザーを削除してもよろしいですか？
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
                        @click="deleteUser"
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
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    users: Object,
});

const confirmingUserDeletion = ref(false);
const userToDelete = ref(null);

const form = useForm({});

const confirmDelete = (user) => {
    userToDelete.value = user;
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    form.delete(route('admin.users.destroy', userToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    userToDelete.value = null;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ja-JP');
};
</script> 