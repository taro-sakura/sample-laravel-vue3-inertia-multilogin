<template>
    <FormSection @submitted="logoutOtherBrowserSessions">
        <template #title>
            他のブラウザセッション
        </template>

        <template #description>
            必要に応じて、他のすべてのブラウザセッションからログアウトできます。最近のセッションの一部が以下に表示されています。
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <div v-if="sessions && sessions.length > 0">
                    <div class="mt-4 space-y-6">
                        <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                            <div>
                                <svg
                                    v-if="session.agent.platform"
                                    class="w-8 h-8 text-gray-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>

                            <div class="ml-3">
                                <div class="text-sm text-gray-600">
                                    {{ session.agent.platform ? session.agent.platform : 'Unknown' }} -
                                    {{ session.agent.browser ? session.agent.browser : 'Unknown' }}
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        {{ session.ip_address }},

                                        <span v-if="session.is_current_device" class="text-green-500 font-semibold">このデバイス</span>
                                        <span v-else>最終アクセス {{ session.last_active }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="パスワード" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="current-password"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                完了しました。
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                他のブラウザセッションからログアウト
            </PrimaryButton>
        </template>
    </FormSection>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    sessions: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    password: '',
});

const logoutOtherBrowserSessions = () => {
    form.delete(route('admin.other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.recentlySuccessful = true;
        },
    });
};
</script> 