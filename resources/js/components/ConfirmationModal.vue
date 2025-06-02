<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { AlertTriangleIcon } from 'lucide-vue-next';

interface Props {
    isOpen: boolean;
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'danger' | 'warning' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    variant: 'danger',
});

const emit = defineEmits<{
    confirm: [];
    cancel: [];
    close: [];
}>();

const handleConfirm = () => {
    emit('confirm');
    emit('close');
};

const handleCancel = () => {
    emit('cancel');
    emit('close');
};

const handleBackdropClick = (event: MouseEvent) => {
    if (event.target === event.currentTarget) {
        handleCancel();
    }
};
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click="handleBackdropClick">
        <div class="border-sidebar-border/70 dark:border-sidebar-border w-full max-w-md rounded-lg border bg-white shadow-xl dark:bg-gray-800">
            <div class="p-6">
                <div class="mb-4 flex items-center space-x-3">
                    <div
                        :class="[
                            'flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full',
                            variant === 'danger'
                                ? 'bg-red-100 dark:bg-red-900/20'
                                : variant === 'warning'
                                  ? 'bg-yellow-100 dark:bg-yellow-900/20'
                                  : 'bg-blue-100 dark:bg-blue-900/20',
                        ]"
                    >
                        <AlertTriangleIcon
                            :class="[
                                'h-5 w-5',
                                variant === 'danger'
                                    ? 'text-red-600 dark:text-red-400'
                                    : variant === 'warning'
                                      ? 'text-yellow-600 dark:text-yellow-400'
                                      : 'text-blue-600 dark:text-blue-400',
                            ]"
                        />
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ title }}
                    </h3>
                </div>

                <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">
                    {{ message }}
                </p>

                <div class="flex justify-end space-x-3">
                    <Button variant="outline" @click="handleCancel" class="cursor-pointer">
                        {{ cancelText }}
                    </Button>

                    <Button :variant="variant === 'danger' ? 'destructive' : 'default'" @click="handleConfirm" class="cursor-pointer">
                        {{ confirmText }}
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
