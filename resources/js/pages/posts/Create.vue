<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea';
import { Head, Link, useForm } from '@inertiajs/vue3'
import { LoaderCircle } from 'lucide-vue-next'

const breadcrumbs = [
    {
        title: 'Posts',
        href: route('posts.index'),
    },
    {
        title: 'Create',
        href: route('posts.create'),
    },
]

const form = useForm({
    title: '',
    content: ''
})

const submit = () => {
    form.post(route('posts.store'))
}
</script>

<template>
    <Head title="Create Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Create New Post</h1>

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

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Button variant="outline" asChild class="cursor-pointer">
                            <Link :href="route('posts.index')">
                                Cancel
                            </Link>
                        </Button>

                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer"
                        >
                            <LoaderCircle v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Post' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>

</style>
