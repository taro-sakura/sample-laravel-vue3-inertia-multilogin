<template>
    <ActionSection>
        <template #title>
            アカウントの削除
        </template>

        <template #description>
            アカウントを完全に削除します。
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                アカウントを削除すると、すべてのデータが完全に削除されます。削除する前に、保持したいデータをダウンロードしてください。
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    アカウントを削除
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    アカウントの削除
                </template>

                <template #content>
                    本当にアカウントを削除しますか？アカウントを削除すると、すべてのデータが完全に削除されます。

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="パスワード"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        キャンセル
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        アカウントを削除
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('admin.profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script> 