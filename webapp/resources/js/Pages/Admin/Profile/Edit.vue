<template>
    <AdminLayout title="プロフィール">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                プロフィール
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm />

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                    <DeleteUserForm />
                </template>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from './Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
</script> 