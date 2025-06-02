<script setup lang="ts">
import { computed, ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { AlertTriangleIcon, TrashIcon, LoaderCircle } from 'lucide-vue-next'
import { formatDate } from '@/utils/dateUtils';

const props = defineProps({
    post: Object,
})

const postData = props.post.data
const showDeleteModal = ref(false)

const breadcrumbs = [
    {
        title: 'Posts',
        href: route('posts.index'),
    },
    {
        title: postData.title,
        href: route('posts.show', { post: postData.id }),
    },
    {
        title: 'Edit',
        href: route('posts.edit', { post: postData.id }),
    },
]

const form = useForm({
    title: postData.title,
    content: postData.content
})

const submit = () => {
    form.put(route('posts.update', { post: postData.id }))
}

const confirmDelete = () => {
    router.delete(route('posts.delete', { post: postData.id }))
    showDeleteModal.value = false
}

const hasChanges = computed(() => {
    return form.title !== postData.title || form.content !== postData.content
})
</script>

<template>
    <Head :title="`Edit: ${postData.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Edit Post</h1>

            <div v-if="hasChanges" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <AlertTriangleIcon class="h-5 w-5 text-yellow-400" />
                    </div>

                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Unsaved Changes</h3>

                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            You have unsaved changes. Make sure to save your work before leaving this page.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-sidebar-border/70 dark:border-sidebar-border">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div class="grid gap-2">
                        <Label for="title">Title *</Label>

                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Enter post title..."
                            required
                        />

                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="content">Content *</Label>

                        <Textarea
                            id="content"
                            v-model="form.content"
                            :rows="12"
                            placeholder="Write your post content here..."
                            class="min-h-[300px]"
                            required
                        />

                        <InputError :message="form.errors.content" />
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-muted-foreground">
                            Last updated: {{ formatDate(postData.updated_at) }}
                        </div>

                        <div class="flex items-center space-x-3">
                            <Button variant="outline" asChild class="cursor-pointer">
                                <Link :href="route('posts.show', { post: postData.id })">
                                    Cancel
                                </Link>
                            </Button>

                            <Button
                                type="submit"
                                :disabled="form.processing || !hasChanges"
                                class="cursor-pointer"
                            >
                                <LoaderCircle v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                {{ form.processing ? 'Updating...' : 'Update Post' }}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-red-200 dark:border-red-800">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-red-900 dark:text-red-400 mb-2">Danger Zone</h3>

                    <p class="text-sm text-red-700 dark:text-red-300 mb-4">
                        Once you delete this post, there is no going back. Please be certain.
                    </p>

                    <Button
                        variant="destructive"
                        @click="showDeleteModal = true"
                        class="cursor-pointer"
                    >
                        <TrashIcon class="h-4 w-4 mr-2" />
                        Delete Post
                    </Button>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Post"
            message="Are you sure you want to delete this post? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            variant="danger"
            @confirm="confirmDelete"
            @cancel="showDeleteModal = false"
            @close="showDeleteModal = false"
        />
    </AppLayout>
</template>

<style scoped>

</style>
