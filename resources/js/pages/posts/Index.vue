<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import { Button } from '@/components/ui/button';
import { Head, Link, router } from '@inertiajs/vue3';
import { PlusIcon, MessageCircleIcon, FileTextIcon } from 'lucide-vue-next';
import Paginator from '@/components/Paginator.vue';
import { formatDate } from '@/utils/dateUtils';

defineProps({
    posts: Object,
});

const showDeleteModal = ref(false);
const postToDelete = ref(null);

const breadcrumbs = [
    {
        title: 'Posts',
        href: route('posts.index'),
    },
];

const confirmDeletePost = (post) => {
    postToDelete.value = post;
    showDeleteModal.value = true;
};

const deletePost = () => {
    if (postToDelete.value) {
        router.delete(route('posts.delete', { post: postToDelete.value.id }));
        showDeleteModal.value = false;
        postToDelete.value = null;
    }
};
</script>

<template>
    <Head title="Blog Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Blog Posts</h1>

                <Button v-if="$page.props.auth.user" asChild class="cursor-pointer">
                    <Link :href="route('posts.create')">
                        <PlusIcon class="mr-2 h-4 w-4" />
                        Create Post
                    </Link>
                </Button>
            </div>

            <div v-if="posts.data.length > 0" class="space-y-6">
                <div
                    v-for="post in posts.data"
                    :key="post.id"
                    class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-lg border bg-white shadow-sm transition-shadow duration-200 hover:shadow-md dark:bg-gray-800"
                >
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300 dark:bg-gray-600">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ post.user.name.charAt(0).toUpperCase() }}
                                    </span>
                                </div>

                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ post.user.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(post.created_at) }}</p>
                                </div>
                            </div>

                            <div v-if="post.can_edit || post.can_delete" class="flex space-x-2">
                                <Button
                                    v-if="post.can_edit"
                                    variant="ghost"
                                    size="sm"
                                    asChild
                                    class="cursor-pointer text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <Link :href="route('posts.edit', { post: post.id })"> Edit </Link>
                                </Button>

                                <Button
                                    v-if="post.can_delete"
                                    variant="ghost"
                                    size="sm"
                                    @click="confirmDeletePost(post)"
                                    class="cursor-pointer text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Delete
                                </Button>
                            </div>
                        </div>

                        <Link :href="route('posts.show', { post: post.id })" class="group block cursor-pointer">
                            <h2
                                class="mb-2 text-xl font-semibold text-gray-900 transition-colors duration-200 group-hover:text-blue-600 dark:text-gray-100 dark:group-hover:text-blue-400"
                            >
                                {{ post.title }}
                            </h2>

                            <p class="line-clamp-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ post.content.substring(0, 200) }}{{ post.content.length > 200 ? '...' : '' }}
                            </p>
                        </Link>

                        <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <MessageCircleIcon class="mr-1 h-4 w-4" />
                            {{ post.comments.length || 0 }} {{ post.comments.length === 1 ? 'comment' : 'comments' }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="py-12 text-center">
                <FileTextIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No posts</h3>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new post.</p>

                <div v-if="$page.props.auth.user" class="mt-6">
                    <Button asChild class="cursor-pointer">
                        <Link :href="route('posts.create')">
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Create Post
                        </Link>
                    </Button>
                </div>
            </div>

            <Paginator v-if="posts.meta.last_page > 1" :data="posts.meta" :show-info="true" />
        </div>

        <ConfirmationModal
            :is-open="showDeleteModal"
            title="Delete Post"
            :message="`Are you sure you want to delete '${postToDelete?.title}'? This action cannot be undone.`"
            confirm-text="Delete"
            cancel-text="Cancel"
            variant="danger"
            @confirm="deletePost"
            @cancel="showDeleteModal = false"
            @close="showDeleteModal = false"
        />
    </AppLayout>
</template>

<style scoped>

</style>
