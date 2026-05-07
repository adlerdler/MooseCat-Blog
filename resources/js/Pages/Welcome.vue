<script setup>
// 注意：这里不需要手动 import BlogPost，因为我们在 app.js 里已经全局注册了
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-8">
        <header class="max-w-4xl mx-auto mb-12 text-center">
            <h1 class="text-5xl font-extrabold text-gray-900 mb-4">Archyx Blog</h1>
            <p class="text-xl text-gray-600">这是通过 Pages/Welcome.vue 渲染的首页页面</p>
        </header>

        <main class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold mb-6 border-b pb-4">下面是一个通用组件示例：</h2>
                
                <!-- 直接使用 components/ 下的组件 -->
                <BlogPost />
            </div>
        </main>
    </div>
</template>
