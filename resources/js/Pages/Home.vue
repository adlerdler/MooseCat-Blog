<script setup>
</script>

<template>
    <div class="min-h-screen bg-construct-paper">
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-construct-black">
            <div class="absolute top-16 left-16 w-32 h-32 border-8 border-construct-red/30 rotate-12 hidden md:block"></div>
            <div class="absolute bottom-32 right-16 w-24 h-24 bg-construct-red/20 rotate-45 hidden md:block"></div>
            <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 hidden md:block"></div>

            <div class="relative z-10 text-center px-6">
                <div class="inline-block mb-8">
                    <span class="text-construct-red text-xs md:text-sm font-bold tracking-[0.4em] uppercase">
                        BLDG_SYSTEM // 2026
                    </span>
                </div>

                <h1 class="font-black text-6xl md:text-8xl lg:text-9xl text-white tracking-tighter leading-none mb-6">
                    ARCHYX
                </h1>

                <p class="text-white/70 text-lg md:text-xl tracking-wide max-w-2xl mx-auto mb-12">
                    探索建筑与技术的边界，构建未来的数字空间体验
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a
                        href="#about"
                        class="group relative px-8 py-4 bg-construct-red text-white font-bold tracking-widest uppercase overflow-hidden transition-all hover:shadow-[8px_8px_0px_#fff] hover:-translate-x-1 hover:-translate-y-1"
                    >
                        <span class="relative z-10">探索更多</span>
                        <div class="absolute inset-0 bg-white transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></div>
                    </a>

                    <a
                        href="#projects"
                        class="px-8 py-4 border-2 border-white/50 text-white font-bold tracking-widest uppercase hover:bg-white hover:text-construct-black transition-all"
                    >
                        查看项目
                    </a>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
                <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </section>

        <section id="about" class="py-24 md:py-32 bg-construct-paper">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <div>
                        <span class="text-construct-red text-xs font-bold tracking-[0.3em] uppercase mb-4 block">
                            ABOUT // 关于我们
                        </span>
                        <h2 class="font-black text-4xl md:text-6xl text-construct-black tracking-tight mb-8">
                            架构师的<br />数字实验室
                        </h2>
                        <p class="text-gray-600 text-lg leading-relaxed mb-8">
                            ARCHYX 是一个专注于建筑数字化与创新技术的研究平台。我们探索如何将传统建筑设计与现代数字技术相结合，创造出更智能、更可持续的建筑空间。
                        </p>
                        <div class="flex gap-8">
                            <div>
                                <span class="font-black text-4xl text-construct-red">15+</span>
                                <p class="text-sm text-gray-500 uppercase tracking-wider mt-1">年经验</p>
                            </div>
                            <div>
                                <span class="font-black text-4xl text-construct-red">100+</span>
                                <p class="text-sm text-gray-500 uppercase tracking-wider mt-1">项目</p>
                            </div>
                            <div>
                                <span class="font-black text-4xl text-construct-red">50+</span>
                                <p class="text-sm text-gray-500 uppercase tracking-wider mt-1">团队</p>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-square bg-construct-red/20 flex items-center justify-center">
                            <div class="w-3/4 h-3/4 border-4 border-construct-red flex items-center justify-center">
                                <span class="font-black text-6xl text-construct-red">AX</span>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-construct-black flex items-center justify-center">
                            <span class="text-white font-bold tracking-widest">EST. 2010</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="projects" class="py-24 md:py-32 bg-construct-black">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="text-construct-red text-xs font-bold tracking-[0.3em] uppercase mb-4 block">
                        PROJECTS // 精选项目
                    </span>
                    <h2 class="font-black text-4xl md:text-6xl text-white tracking-tight">
                        标志性建筑
                    </h2>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div class="group relative aspect-[3/4] bg-gray-800 overflow-hidden cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-t from-construct-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-white/20 font-black text-8xl">01</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                            <span class="text-construct-red text-xs font-bold tracking-widest uppercase block mb-2">
                                商业建筑
                            </span>
                            <h3 class="text-white text-xl font-bold mb-2">云端大厦</h3>
                            <p class="text-white/60 text-sm">2024 · 上海</p>
                        </div>
                        <div class="absolute inset-0 border-2 border-transparent group-hover:border-construct-red transition-colors"></div>
                    </div>

                    <div class="group relative aspect-[3/4] bg-gray-800 overflow-hidden cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-t from-construct-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-white/20 font-black text-8xl">02</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                            <span class="text-construct-red text-xs font-bold tracking-widest uppercase block mb-2">
                                文化建筑
                            </span>
                            <h3 class="text-white text-xl font-bold mb-2">艺术中心</h3>
                            <p class="text-white/60 text-sm">2023 · 北京</p>
                        </div>
                        <div class="absolute inset-0 border-2 border-transparent group-hover:border-construct-red transition-colors"></div>
                    </div>

                    <div class="group relative aspect-[3/4] bg-gray-800 overflow-hidden cursor-pointer">
                        <div class="absolute inset-0 bg-gradient-to-t from-construct-black/80 via-transparent to-transparent z-10"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-white/20 font-black text-8xl">03</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                            <span class="text-construct-red text-xs font-bold tracking-widest uppercase block mb-2">
                                住宅建筑
                            </span>
                            <h3 class="text-white text-xl font-bold mb-2">绿色家园</h3>
                            <p class="text-white/60 text-sm">2025 · 深圳</p>
                        </div>
                        <div class="absolute inset-0 border-2 border-transparent group-hover:border-construct-red transition-colors"></div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-construct-black border-t border-white/10 py-16">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-12">
                    <div class="md:col-span-2">
                        <h3 class="font-black text-4xl text-white tracking-tighter mb-4">ARCHYX</h3>
                        <p class="text-white/60 text-sm max-w-sm">
                            探索建筑与技术的边界，构建未来的数字空间体验
                        </p>
                    </div>
                    <div>
                        <h4 class="text-white font-bold tracking-widest uppercase text-sm mb-6">导航</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-white/60 hover:text-construct-red transition-colors text-sm">首页</a></li>
                            <li><a href="#about" class="text-white/60 hover:text-construct-red transition-colors text-sm">关于</a></li>
                            <li><a href="#projects" class="text-white/60 hover:text-construct-red transition-colors text-sm">项目</a></li>
                            <li><a href="#" class="text-white/60 hover:text-construct-red transition-colors text-sm">联系</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-bold tracking-widest uppercase text-sm mb-6">联系</h4>
                        <ul class="space-y-3">
                            <li class="text-white/60 text-sm">hello@archyx.com</li>
                            <li class="text-white/60 text-sm">+86 021 1234 5678</li>
                            <li class="text-white/60 text-sm">上海市浦东新区</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-12 pt-8 border-t border-white/10 text-center">
                    <p class="text-white/40 text-xs font-bold tracking-[0.2em] uppercase">
                        © 2026 ARCHYX. ALL RIGHTS RESERVED.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>