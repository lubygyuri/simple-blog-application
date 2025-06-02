<script setup lang="ts">
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircleIcon, XCircleIcon, InfoIcon } from 'lucide-vue-next';

const page = usePage();
const showMessage = ref(false);
const message = ref('');
const type = ref<'success' | 'error' | 'info'>('success');

const autoHide = () => {
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
};

const hideMessage = () => {
    showMessage.value = false;
};

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.message) {
            message.value = flash.message;
            type.value = 'info';
            showMessage.value = true;
            autoHide();
        } else if (flash?.success) {
            message.value = flash.success;
            type.value = 'success';
            showMessage.value = true;
            autoHide();
        } else if (flash?.error) {
            message.value = flash.error;
            type.value = 'error';
            showMessage.value = true;
            autoHide();
        }
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="showMessage" class="fixed top-4 right-4 z-50 w-full max-w-sm">
            <div
                :class="[
                    'rounded-lg border p-4 shadow-lg',
                    type === 'success'
                        ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20'
                        : type === 'error'
                          ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20'
                          : 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20',
                ]"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <CheckCircleIcon v-if="type === 'success'" class="h-5 w-5 text-green-400" />
                        <XCircleIcon v-else-if="type === 'error'" class="h-5 w-5 text-red-400" />
                        <InfoIcon v-else class="h-5 w-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <p
                            :class="[
                                'text-sm font-medium',
                                type === 'success'
                                    ? 'text-green-800 dark:text-green-400'
                                    : type === 'error'
                                      ? 'text-red-800 dark:text-red-400'
                                      : 'text-blue-800 dark:text-blue-400',
                            ]"
                        >
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button
                                @click="hideMessage"
                                :class="[
                                    'inline-flex rounded-md p-1.5 focus:ring-2 focus:ring-offset-2 focus:outline-none',
                                    type === 'success'
                                        ? 'text-green-500 hover:bg-green-100 focus:ring-green-600 dark:hover:bg-green-900/30'
                                        : type === 'error'
                                          ? 'text-red-500 hover:bg-red-100 focus:ring-red-600 dark:hover:bg-red-900/30'
                                          : 'text-blue-500 hover:bg-blue-100 focus:ring-blue-600 dark:hover:bg-blue-900/30',
                                ]"
                            >
                                <span class="sr-only">Dismiss</span>
                                <XCircleIcon class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>

</style>
