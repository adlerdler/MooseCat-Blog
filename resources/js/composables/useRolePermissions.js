/**
 * useRolePermissions.js - 角色权限管理
 * 
 * 功能说明：
 * - 统一管理角色和权限的相关操作函数
 * - 提供角色信息获取、权限检查等功能
 */
import { roles } from '../data/roles';
import { permissions } from '../data/permissions';
import { rolePermissions } from '../data/role_permissions';

export const useRolePermissions = () => {
  // 角色相关函数
  const getRoleById = (roleId) => {
    return roles.find(r => r.id === roleId);
  };

  const getRoleByValue = (value) => {
    return roles.find(r => r.value === value);
  };

  const getRole = (roleIdentifier) => {
    if (typeof roleIdentifier === 'number') {
      return roles.find(r => r.id === roleIdentifier);
    } else {
      return roles.find(r => r.value === roleIdentifier);
    }
  };

  const getRoleLabel = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.label : 'Unknown';
  };

  const getRoleColor = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.color : 'gray';
  };

  const getRoleDescription = (roleIdentifier) => {
    const role = getRole(roleIdentifier);
    return role ? role.description : '未知角色';
  };

  const getRoleStyle = (roleIdentifier) => {
    const colorMap = {
      admin: 'bg-red-600 text-white border-red-500',
      editor: 'bg-blue-600 text-white border-blue-500',
      author: 'bg-green-600 text-white border-green-500',
      moderator: 'bg-purple-600 text-white border-purple-500',
      subscriber: 'bg-gray-600 text-white border-gray-500',
      api: 'bg-yellow-500 text-gray-900 border-yellow-400',
      guest: 'bg-cyan-600 text-white border-cyan-500'
    };
    
    const role = getRole(roleIdentifier);
    const value = role ? role.value : 'guest';
    return colorMap[value] || 'bg-gray-600 text-white border-gray-500';
  };

  // 权限相关函数
  const availablePermissions = permissions.map(p => p.label);

  const getPermissionIdsByRoleId = (roleId) => {
    return rolePermissions
      .filter(rp => rp.roleId === roleId)
      .map(rp => rp.permissionId);
  };

  const getRoleIdsByPermissionId = (permissionId) => {
    return rolePermissions
      .filter(rp => rp.permissionId === permissionId)
      .map(rp => rp.roleId);
  };

  const hasPermission = (roleId, permissionId) => {
    return rolePermissions.some(rp => rp.roleId === roleId && rp.permissionId === permissionId);
  };

  // 便捷导出
  const adminRoles = roles;

  return {
    // 角色相关
    roles,
    adminRoles,
    getRoleById,
    getRoleByValue,
    getRoleLabel,
    getRoleColor,
    getRoleDescription,
    getRoleStyle,
    
    // 权限相关
    permissions,
    availablePermissions,
    
    // 角色权限关联
    rolePermissions,
    getPermissionIdsByRoleId,
    getRoleIdsByPermissionId,
    hasPermission
  };
};
