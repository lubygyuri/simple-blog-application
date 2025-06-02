<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeftIcon, ChevronRightIcon, MoreHorizontalIcon } from 'lucide-vue-next';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationData {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

const props = defineProps<{
    data: PaginationData;
    showInfo?: boolean;
}>();

const isEllipsis = (label: string): boolean => {
    return label === '...';
};

const prevPage = computed(() => {
    const prevLink = props.data.links.find((link) => link.label.includes('Previous'));
    return prevLink?.url;
});

const nextPage = computed(() => {
    const nextLink = props.data.links.find((link) => link.label.includes('Next'));
    return nextLink?.url;
});

computed(() => {
    return props.data.links.filter((link) => !link.label.includes('Previous') && !link.label.includes('Next') && link.label !== '...');
});

computed(() => {
    return props.data.links.some((link) => link.label === '...');
});
</script>

<template>
    <div v-if="data.last_page > 1" class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div v-if="showInfo" class="text-muted-foreground text-sm">
            Showing {{ data.from }} to {{ data.to }} of {{ data.total }} results
        </div>

        <nav class="flex items-center space-x-1">
            <Button v-if="prevPage" variant="outline" size="sm" asChild class="cursor-pointer">
                <Link :href="prevPage">
                    <ChevronLeftIcon class="mr-1 h-4 w-4" />
                    Previous
                </Link>
            </Button>

            <Button v-else variant="outline" size="sm" disabled class="cursor-not-allowed">
                <ChevronLeftIcon class="mr-1 h-4 w-4" />
                Previous
            </Button>

            <div class="flex items-center space-x-1">
                <template v-for="link in data.links" :key="link.label">
                    <template v-if="!link.label.includes('Previous') && !link.label.includes('Next')">
                        <div v-if="isEllipsis(link.label)" class="text-muted-foreground px-3 py-2 text-sm">
                            <MoreHorizontalIcon class="h-4 w-4" />
                        </div>

                        <Button
                            v-else-if="link.url"
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            asChild
                            class="min-w-[2.5rem] cursor-pointer"
                        >
                            <Link :href="link.url">
                                {{ link.label }}
                            </Link>
                        </Button>

                        <Button v-else variant="default" size="sm" disabled class="min-w-[2.5rem] cursor-default">
                            {{ link.label }}
                        </Button>
                    </template>
                </template>
            </div>

            <Button v-if="nextPage" variant="outline" size="sm" asChild class="cursor-pointer">
                <Link :href="nextPage">
                    Next
                    <ChevronRightIcon class="ml-1 h-4 w-4" />
                </Link>
            </Button>

            <Button v-else variant="outline" size="sm" disabled class="cursor-not-allowed">
                Next
                <ChevronRightIcon class="ml-1 h-4 w-4" />
            </Button>
        </nav>

        <div v-if="!showInfo" class="text-muted-foreground text-sm sm:hidden">
            Page {{ data.current_page }} of {{ data.last_page }}
        </div>
    </div>
</template>

<style scoped>

</style>
