<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    post: Object,
});
</script>

<template>
    <Head :title="post.title" />

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <Link :href="route('posts.index')" class="text-blue-600 hover:underline mb-4 inline-block">&larr; 返回列表</Link>
                
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ post.title }}</h1>
                
                <div class="flex items-center text-sm text-gray-500 mb-8">
                    <span>作者：{{ post.author?.name }}</span>
                    <span class="mx-2">|</span>
                    <span>发布于：{{ post.published_at }}</span>
                    <span class="mx-2">|</span>
                    <span>浏览：{{ post.views_count }}</span>
                </div>

                <div class="prose max-w-none" v-html="post.content"></div>
            </div>
        </div>
    </div>
</template>
