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
import { adPositions, getAdPositionByName, getAdPositionById, getActiveAdPositions } from '../data/ad_positions';

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
   * @param {string|number} positionOrId - 广告位置名称或ID
   * @param {number} limit - 最大返回数量，默认不限制
   * @returns {Array} 符合条件的广告数组
   */
  const getActiveAds = (positionOrId, limit = null) => {
    let targetPosition = positionOrId;

    if (typeof positionOrId === 'number') {
      const posConfig = getAdPositionById(positionOrId);
      targetPosition = posConfig ? posConfig.name : positionOrId;
    }

    let ads = sampleAdvertisements.filter(ad => {
      if (ad.position !== targetPosition || !ad.is_active) return false;
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
    const result = {};

    adPositions.forEach(pos => {
      result[pos.name] = sampleAdvertisements.filter(ad => {
        if (ad.position !== pos.name || !ad.is_active) return false;
        return isAdValid(ad);
      });
    });

    return result;
  });

  /**
   * 检查指定位置是否有可用广告
   * @param {string|number} positionOrId - 广告位置名称或ID
   * @returns {boolean}
   */
  const hasActiveAd = (positionOrId) => {
    return getActiveAds(positionOrId).length > 0;
  };

  /**
   * 获取单个广告（用于需要展示单个广告的场景）
   * @param {string|number} positionOrId - 广告位置名称或ID
   * @returns {Object|null} 广告对象或null
   */
  const getSingleAd = (positionOrId) => {
    const ads = getActiveAds(positionOrId, 1);
    return ads.length > 0 ? ads[0] : null;
  };

  /**
   * 获取广告位配置信息
   * @param {string|number} positionOrId - 广告位置名称或ID
   * @returns {Object|null} 广告位配置对象
   */
  const getPositionConfig = (positionOrId) => {
    if (typeof positionOrId === 'number') {
      return getAdPositionById(positionOrId);
    }
    return getAdPositionByName(positionOrId);
  };

  /**
   * 获取所有启用的广告位列表
   * @returns {Array} 广告位配置数组
   */
  const getAvailablePositions = () => {
    return getActiveAdPositions();
  };

  /**
   * 获取广告位的默认尺寸
   * @param {string|number} positionOrId - 广告位置名称或ID
   * @returns {Object} 包含 width 和 height 的对象
   */
  const getPositionSize = (positionOrId) => {
    const config = getPositionConfig(positionOrId);
    return config ? { width: config.default_width, height: config.default_height } : { width: 0, height: 0 };
  };

  return {
    getActiveAds,
    adsByPosition,
    hasActiveAd,
    getSingleAd,
    getPositionConfig,
    getAvailablePositions,
    getPositionSize,
    isAdValid,
    adPositions
  };
}