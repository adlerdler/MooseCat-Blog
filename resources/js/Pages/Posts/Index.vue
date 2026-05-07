<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    posts: Object,
    filters: Object,
});
</script>

<template>
    <Head title="文章列表" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">最新文章</h1>

            <div v-if="posts.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="post in posts.data" :key="post.id" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2">
                            <Link :href="route('posts.show', post.slug)" class="text-blue-600 hover:underline">
                                {{ post.title }}
                            </Link>
                        </h2>
                        <p class="text-gray-600 mb-4">{{ post.excerpt }}</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>{{ post.author?.name }}</span>
                            <span>{{ post.published_at }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12">
                <p class="text-gray-500">暂无文章。</p>
            </div>
        </div>
    </div>
</template>
