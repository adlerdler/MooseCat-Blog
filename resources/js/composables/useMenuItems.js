/**
 * useMenuItems.js - 菜单数据管理
 *
 * 功能说明：
 * - 统一管理前台和后台菜单数据
 * - 提供菜单获取和树形转换函数
 *
 * 使用方式：
 * // 通过 options 传入数据（必须）
 * import { useMenuItems } from '../../composables/useMenuItems';
 * const { frontMenuItems, adminMenuItems } = useMenuItems({ menus: props.menus });
 */
export const useMenuItems = (options = {}) => {
  const menus = options.menus || [];

  const getMenusByType = (type) => {
    return menus.filter(menu => menu.type === type && menu.is_active);
  };

  const buildMenuTree = (type) => {
    const flatMenus = getMenusByType(type);
    const menuMap = {};
    const tree = [];

    flatMenus.forEach(menu => {
      menuMap[menu.id] = { ...menu, children: [] };
    });

    flatMenus.forEach(menu => {
      if (menu.parent_id === null) {
        tree.push(menuMap[menu.id]);
      } else {
        menuMap[menu.parent_id].children.push(menuMap[menu.id]);
      }
    });

    tree.sort((a, b) => a.sort_order - b.sort_order);
    tree.forEach(parent => {
      parent.children.sort((a, b) => a.sort_order - b.sort_order);
    });

    return tree;
  };

  const frontMenuItems = getMenusByType('front');
  const adminMenuItems = buildMenuTree('admin');

  return {
    menus,
    getMenusByType,
    buildMenuTree,
    frontMenuItems,
    adminMenuItems
  };
};