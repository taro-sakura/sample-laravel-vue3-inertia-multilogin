<template>
    <FormSection @submitted="enableTwoFactorAuthentication">
        <template #title>
            二要素認証
        </template>

        <template #description>
            二要素認証を使用して、アカウントのセキュリティを強化します。
        </template>

        <template #form>
            <div v-if="!$page.props.auth.user.two_factor_enabled">
                <p class="text-sm text-gray-600">
                    二要素認証が有効になると、ログイン時に認証コードの入力を求められます。
                </p>

                <div class="mt-4">
                    <PrimaryButton type="submit" :disabled="form.processing">
                        有効にする
                    </PrimaryButton>
                </div>
            </div>

            <div v-else>
                <p class="text-sm text-gray-600">
                    二要素認証が有効になっています。
                </p>

                <div class="mt-4">
                    <PrimaryButton @click="showRecoveryCodes" :disabled="form.processing">
                        リカバリーコードを表示
                    </PrimaryButton>

                    <SecondaryButton @click="disableTwoFactorAuthentication" :disabled="form.processing" class="ml-2">
                        無効にする
                    </SecondaryButton>
                </div>
            </div>
        </template>
    </FormSection>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const form = useForm({});

const enableTwoFactorAuthentication = () => {
    form.post(route('admin.two-factor.enable'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const disableTwoFactorAuthentication = () => {
    form.delete(route('admin.two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const showRecoveryCodes = () => {
    form.get(route('admin.two-factor.recovery-codes'), {
        preserveScroll: true,
    });
};
</script> 