<script setup>
/**
 * AdSlot.vue - 通用广告组件（Banner 样式）
 *
 * ⚠️ 字段变更备注（2026-05-24）：
 * - 广告数据已删除 position 字段，改用 position_id 关联广告位
 * - 通过 position_id 查找广告位名称进行匹配
 *
 * 功能说明：
 * - 按位置筛选广告
 * - 支持广告数量限制
 * - 自动检查广告有效期
 * - 提供统一的 Banner 横幅广告展示
 *
 * 使用方式：
 * <AdSlot position="header" />
 * <AdSlot position="footer" />
 * <AdSlot position="sidebar" :limit="2" />
 */
import { computed } from 'vue';
import { ArrowUpRight } from 'lucide-vue-next';
import { sampleAdvertisements } from '../../data/advertisements';
import { getAdPositionByName, getAdPositionById, adPositions } from '../../data/ad_positions';

const props = defineProps({
  position: {
    type: [String, Number],
    required: true,
    validator: (value) => {
      if (typeof value === 'number') {
        return value >= 1 && value <= 7;
      }
      return ['header', 'sidebar', 'footer', 'between_posts', 'popup', 'in_content', 'video_bottom'].includes(value);
    }
  },
  limit: {
    type: Number,
    default: 1
  }
});

const positionConfig = computed(() => {
  if (typeof props.position === 'number') {
    return getAdPositionById(props.position);
  }
  return getAdPositionByName(props.position);
});

const targetPositionId = computed(() => {
  if (typeof props.position === 'number') {
    return props.position;
  }
  return positionConfig.value?.id || null;
});

const targetPositionName = computed(() => {
  if (typeof props.position === 'string') {
    return props.position;
  }
  return positionConfig.value?.name || '';
});

const isAdValid = (ad) => {
  const now = new Date();
  const start = new Date(ad.start_date);
  const end = new Date(ad.end_date);
  return now >= start && now <= end;
};

const activeAds = computed(() => {
  return sampleAdvertisements
    .filter(ad => {
      if (ad.position_id !== targetPositionId.value || !ad.is_active) return false;
      return isAdValid(ad);
    })
    .slice(0, props.limit);
});

const getContainerClasses = () => {
  const width = positionConfig.value?.default_width || 0;
  const height = positionConfig.value?.default_height || 0;

  if (width && height) {
    return `w-full max-w-[${width}px] mx-auto`;
  }

  switch (props.position) {
    case 'header':
    case 'footer':
      return 'w-full max-w-6xl mx-auto';
    case 'sidebar':
      return 'w-full max-w-[300px] border-4 border-construct-black hover:border-construct-red transition-colors duration-300';
    case 'popup':
      return 'w-full max-w-[320px]';
    case 'video_bottom':
    case 'between_posts':
    default:
      return 'w-full';
  }
};

const getAdClasses = () => {
  switch (targetPositionName.value) {
    case 'header':
      return 'border-t-8 border-construct-black bg-construct-black';
    case 'footer':
      return 'bg-construct-black border-b-8 border-black';
    case 'sidebar':
      return 'overflow-hidden';
    case 'popup':
      return 'border-4 border-construct-black shadow-2xl';
    case 'video_bottom':
      return 'border-2 border-construct-black hover:border-construct-red bg-construct-black';
    case 'between_posts':
    default:
      return 'border-2 border-construct-black hover:border-construct-red';
  }
};

const getContentClasses = () => {
  switch (targetPositionName.value) {
    case 'header':
    case 'footer':
      return 'flex items-center justify-between px-8 md:px-16 py-6';
    default:
      return 'p-4';
  }
};

const getImageClasses = () => {
  const width = positionConfig.value?.default_width || 0;
  const height = positionConfig.value?.default_height || 0;

  if (width && height) {
    return `w-full h-full object-cover aspect-[${width}/${height}]`;
  }

  switch (targetPositionName.value) {
    case 'header':
    case 'footer':
      return 'w-32 h-16 md:w-48 md:h-20 object-cover';
    default:
      return 'w-full h-full object-cover';
  }
};
</script>

<template>
  <div v-if="activeAds.length > 0" :class="getContainerClasses()">
    <a
      v-for="ad in activeAds"
      :key="ad.id"
      :href="ad.link_url"
      target="_blank"
      rel="noopener noreferrer"
      class="block group"
    >
      <div
        class="relative overflow-hidden transition-all duration-300"
        :class="getAdClasses()"
      >
        <!-- Video Bottom Ad: Keep original size, only optimize style -->
        <template v-if="targetPositionName === 'video_bottom'">
          <div :class="getContentClasses()">
            <div class="flex items-center gap-4 md:gap-8">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-construct-red"></div>
                <span class="text-[10px] font-black tracking-widest text-construct-red uppercase">
                  SPONSORED
                </span>
              </div>
              <h4 class="font-display text-lg md:text-xl tracking-tight text-white group-hover:text-construct-red transition-colors">
                {{ ad.title }}
              </h4>
            </div>
            <div class="flex items-center gap-4 mt-2">
              <span class="text-xs md:text-sm text-white/60 uppercase tracking-wider">
                WATCH_NOW
              </span>
              <ArrowUpRight
                class="w-5 h-5 text-white/60 group-hover:text-construct-red group-hover:translate-x-1 group-hover:-translate-y-1 transition-all"
              />
            </div>
          </div>
        </template>

        <!-- Sidebar Ad: Image + Info layout -->
        <template v-else-if="targetPositionName === 'sidebar'">
          <div class="aspect-[4/3] bg-construct-black overflow-hidden">
            <img
              :src="ad.image_url"
              :alt="ad.title"
              class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500"
            />
          </div>
          <div class="p-4 bg-construct-black">
            <span class="text-[9px] font-black tracking-widest text-construct-red uppercase">
              SPONSORED
            </span>
            <h4 class="font-display text-sm tracking-tight text-white mt-1 line-clamp-2 group-hover:text-construct-red transition-colors">
              {{ ad.title }}
            </h4>
            <div class="flex items-center justify-between mt-3">
              <span class="text-[9px] font-bold tracking-widest text-white/40 uppercase">
                Learn More
              </span>
              <ArrowUpRight
                class="w-4 h-4 text-white/40 group-hover:text-construct-red group-hover:translate-x-1 group-hover:-translate-y-1 transition-all"
              />
            </div>
          </div>
        </template>

        <!-- Header/Footer Ad: Full width text banner -->
        <template v-else>
          <div :class="getContentClasses()">
            <div class="flex items-center gap-4 md:gap-8">
              <span class="text-[10px] font-black tracking-widest text-construct-red uppercase">
                SPONSORED
              </span>
              <h4 class="font-display text-lg md:text-xl tracking-tight text-white group-hover:text-construct-red transition-colors">
                {{ ad.title }}
              </h4>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-xs md:text-sm text-white/60 uppercase tracking-wider hidden md:block">
                AD_LEARN_MORE
              </span>
              <ArrowUpRight
                class="w-5 h-5 text-white/60 group-hover:text-construct-red group-hover:translate-x-1 group-hover:-translate-y-1 transition-all"
              />
            </div>
          </div>
        </template>
      </div>
    </a>
  </div>
</template>