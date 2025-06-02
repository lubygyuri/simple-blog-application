<script setup lang="ts">
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { PencilIcon, TrashIcon, MessageCircleIcon, LoaderCircle } from 'lucide-vue-next';
import { Textarea } from '@/components/ui/textarea';
import Paginator from '@/components/Paginator.vue';
import { formatDate } from '@/utils/dateUtils';

const props = defineProps({
    post: Object,
    comments: Object,
});

const postData = props.post.data;
const comments = ref(props.comments);
const showDeletePostModal = ref(false);
const showDeleteCommentModal = ref(false);
const commentToDelete = ref(null);

const breadcrumbs = [
    {
        title: 'Posts',
        href: route('posts.index'),
    },
    {
        title: postData.title,
        href: route('posts.show', { post: postData.id }),
    },
];

const commentForm = useForm({
    comment: '',
});

const submitComment = () => {
    commentForm.post(route('comments.store', { post: postData.id }), {
        onSuccess: () => {
            commentForm.reset();
        },
    });
};

const confirmDeletePost = () => {
    router.delete(route('posts.delete', { post: postData.id }));
    showDeletePostModal.value = false;
};

const confirmDeleteComment = (comment) => {
    commentToDelete.value = comment;
    showDeleteCommentModal.value = true;
};

const deleteComment = () => {
    if (commentToDelete.value) {
        router.delete(route('comments.delete', { comment: commentToDelete.value.id }));
        showDeleteCommentModal.value = false;
        commentToDelete.value = null;
    }
};

watch(
    () => props.comments,
    (newComments) => {
        if (newComments) {
            comments.value = newComments;
        }
    },
    { deep: true, immediate: true },
);
</script>

<template>
    <Head :title="postData.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <article
                class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-lg border bg-white shadow-sm dark:bg-gray-800"
            >
                <div class="border-b border-gray-200 px-6 py-8 dark:border-gray-700">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-300 dark:bg-gray-600">
                                <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
                                    {{ postData.user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>

                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900 dark:text-gray-100">
                                    {{ postData.user.name }}
                                </p>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(postData.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div v-if="postData.can_edit || postData.can_delete" class="flex space-x-3">
                            <Button v-if="postData.can_edit" variant="outline" size="sm" asChild class="cursor-pointer">
                                <Link :href="route('posts.edit', { post: postData.id })">
                                    <PencilIcon class="mr-2 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>

                            <Button
                                v-if="postData.can_delete"
                                variant="destructive"
                                size="sm"
                                @click="showDeletePostModal = true"
                                class="cursor-pointer"
                            >
                                <TrashIcon class="mr-2 h-4 w-4" />
                                Delete
                            </Button>
                        </div>
                    </div>

                    <h1 class="mb-4 text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ postData.title }}
                    </h1>
                </div>

                <div class="px-6 py-8">
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="leading-relaxed whitespace-pre-wrap text-gray-700 dark:text-gray-300">
                            {{ postData.content }}
                        </p>
                    </div>
                </div>
            </article>

            <div class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-lg border bg-white shadow-sm dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Comments ({{ comments.meta.total }})
                    </h3>
                </div>

                <div v-if="$page.props.auth.user" class="border-b border-gray-200 bg-gray-50 px-6 py-6 dark:border-gray-700 dark:bg-gray-900/50">
                    <form @submit.prevent="submitComment" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="comment">Add a comment</Label>

                            <Textarea
                                id="comment"
                                v-model="commentForm.comment"
                                :rows="3"
                                placeholder="Write a comment..."
                                required
                            />

                            <InputError :message="commentForm.errors.comment" />
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="commentForm.processing" class="cursor-pointer">
                                <LoaderCircle v-if="commentForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                                {{ commentForm.processing ? 'Posting...' : 'Post Comment' }}
                            </Button>
                        </div>
                    </form>
                </div>

                <div v-else class="border-b border-gray-200 bg-gray-50 px-6 py-6 text-center dark:border-gray-700 dark:bg-gray-900/50">
                    <p class="text-gray-600 dark:text-gray-400">
                        <Link :href="route('login')" class="cursor-pointer text-blue-600 hover:text-blue-500 dark:text-blue-400"> Login </Link>
                        to post a comment.
                    </p>
                </div>

                <div v-if="comments.data && comments.data.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div v-for="comment in comments.data" :key="comment.id" class="px-6 py-6">
                        <div class="flex space-x-3">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-300 dark:bg-gray-600">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                    {{ comment.user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ comment.user.name }}
                                        </p>

                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ formatDate(comment.created_at) }}
                                        </p>
                                    </div>

                                    <Button
                                        v-if="comment.can_delete"
                                        variant="ghost"
                                        size="sm"
                                        @click="confirmDeleteComment(comment)"
                                        class="cursor-pointer text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Delete
                                    </Button>
                                </div>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ comment.comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="px-6 py-12 text-center">
                    <MessageCircleIcon class="mx-auto h-8 w-8 text-gray-400" />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No comments yet. Be the first to comment!</p>
                </div>

                <div v-if="comments.meta.last_page > 1" class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <Paginator :data="comments.meta" :show-info="true" />
                </div>
            </div>
        </div>

        <ConfirmationModal
            :is-open="showDeletePostModal"
            title="Delete Post"
            message="Are you sure you want to delete this post? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            variant="danger"
            @confirm="confirmDeletePost"
            @cancel="showDeletePostModal = false"
            @close="showDeletePostModal = false"
        />

        <ConfirmationModal
            :is-open="showDeleteCommentModal"
            title="Delete Comment"
            message="Are you sure you want to delete this comment? This action cannot be undone."
            confirm-text="Delete"
            cancel-text="Cancel"
            variant="danger"
            @confirm="deleteComment"
            @cancel="showDeleteCommentModal = false"
            @close="showDeleteCommentModal = false"
        />
    </AppLayout>
</template>

<style scoped>

</style>
