/**
 * useAdSlot.js - 广告位 Composable
 *
 * 功能说明：
 * - 封装广告数据筛选逻辑
 * - 提供响应式的广告查询方法
 * - 供页面在实现卡片广告时复用筛选逻辑
 *
 * 使用方式：
 * import { useAdSlot } from '../../composables/useAdSlot';
 *
 * const { getActiveAds } = useAdSlot();
 * const headerAds = getActiveAds('header');
 * const betweenPostsAds = getActiveAds('between_posts', 3);
 */
import { computed } from 'vue';
import { sampleAdvertisements } from '../data/advertisements';

export function useAdSlot() {
  /**
   * 检查广告是否在有效期内
   * @param {Object} ad - 广告对象
   * @returns {boolean}
   */
  const isAdValid = (ad) => {
    const now = new Date();
    const start = new Date(ad.start_date);
    const end = new Date(ad.end_date);
    return now >= start && now <= end;
  };

  /**
   * 获取指定位置且激活的广告
   * @param {string} position - 广告位置 (header, sidebar, footer, between_posts, popup)
   * @param {number} limit - 最大返回数量，默认不限制
   * @returns {Array} 符合条件的广告数组
   */
  const getActiveAds = (position, limit = null) => {
    let ads = sampleAdvertisements.filter(ad => {
      if (ad.position !== position || !ad.is_active) return false;
      return isAdValid(ad);
    });

    if (limit !== null) {
      ads = ads.slice(0, limit);
    }

    return ads;
  };

  /**
   * 获取所有激活的广告（按位置分组）
   * @returns {Object} 以位置为键的广告数组对象
   */
  const adsByPosition = computed(() => {
    const positions = ['header', 'sidebar', 'footer', 'between_posts', 'popup', 'in_content', 'video_bottom'];
    const result = {};

    positions.forEach(pos => {
      result[pos] = sampleAdvertisements.filter(ad => {
        if (ad.position !== pos || !ad.is_active) return false;
        return isAdValid(ad);
      });
    });

    return result;
  });

  /**
   * 检查指定位置是否有可用广告
   * @param {string} position - 广告位置
   * @returns {boolean}
   */
  const hasActiveAd = (position) => {
    return getActiveAds(position).length > 0;
  };

  /**
   * 获取单个广告（用于需要展示单个广告的场景）
   * @param {string} position - 广告位置
   * @returns {Object|null} 广告对象或null
   */
  const getSingleAd = (position) => {
    const ads = getActiveAds(position, 1);
    return ads.length > 0 ? ads[0] : null;
  };

  return {
    getActiveAds,
    adsByPosition,
    hasActiveAd,
    getSingleAd,
    isAdValid
  };
}