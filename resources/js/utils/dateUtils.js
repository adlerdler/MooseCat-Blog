/**
 * dateUtils.js - 日期格式化工具函数
 * 
 * 提供统一的日期处理和格式化功能，确保项目中日期格式的一致性。
 */

/**
 * 格式化日期为 ISO 8601 字符串
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} ISO 8601 格式的日期字符串
 */
export function formatToISO(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToISO:', date);
    return new Date().toISOString();
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToISO:', date);
    return new Date().toISOString();
  }
  return dateObj.toISOString();
}

/**
 * 格式化日期为可读字符串（YYYY年MM月DD日）
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} 格式化后的日期字符串
 */
export function formatToChinese(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToChinese:', date);
    return '未知日期';
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToChinese:', date);
    return '未知日期';
  }
  
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const day = String(dateObj.getDate()).padStart(2, '0');
  
  return `${year}年${month}月${day}日`;
}

/**
 * 格式化日期为简洁格式（YYYY-MM-DD）
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} 格式化后的日期字符串
 */
export function formatToShort(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToShort:', date);
    return '0000-00-00';
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToShort:', date);
    return '0000-00-00';
  }
  
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const day = String(dateObj.getDate()).padStart(2, '0');
  
  return `${year}-${month}-${day}`;
}

/**
 * 格式化日期为完整格式（YYYY-MM-DD HH:mm:ss）
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} 格式化后的日期字符串
 */
export function formatToFull(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToFull:', date);
    return '0000-00-00 00:00:00';
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToFull:', date);
    return '0000-00-00 00:00:00';
  }
  
  const year = dateObj.getFullYear();
  const month = String(dateObj.getMonth() + 1).padStart(2, '0');
  const day = String(dateObj.getDate()).padStart(2, '0');
  const hours = String(dateObj.getHours()).padStart(2, '0');
  const minutes = String(dateObj.getMinutes()).padStart(2, '0');
  const seconds = String(dateObj.getSeconds()).padStart(2, '0');
  
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

/**
 * 格式化日期为相对时间（如 "2小时前"）
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} 相对时间字符串
 */
export function formatToRelative(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToRelative:', date);
    return '未知时间';
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToRelative:', date);
    return '未知时间';
  }
  
  const now = new Date();
  const diff = now.getTime() - dateObj.getTime();
  
  const minute = 60 * 1000;
  const hour = 60 * minute;
  const day = 24 * hour;
  const week = 7 * day;
  const month = 30 * day;
  const year = 365 * day;
  
  if (diff < minute) {
    return '刚刚';
  } else if (diff < hour) {
    return `${Math.floor(diff / minute)}分钟前`;
  } else if (diff < day) {
    return `${Math.floor(diff / hour)}小时前`;
  } else if (diff < week) {
    return `${Math.floor(diff / day)}天前`;
  } else if (diff < month) {
    return `${Math.floor(diff / week)}周前`;
  } else if (diff < year) {
    return `${Math.floor(diff / month)}个月前`;
  } else {
    return `${Math.floor(diff / year)}年前`;
  }
}

/**
 * 格式化日期为英文可读格式（如 "April 28, 2026"）
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {string} 格式化后的日期字符串
 */
export function formatToEnglish(date) {
  if (!date) {
    console.warn('Invalid date provided to formatToEnglish:', date);
    return 'Unknown Date';
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  if (isNaN(dateObj.getTime())) {
    console.warn('Invalid date provided to formatToEnglish:', date);
    return 'Unknown Date';
  }
  
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return dateObj.toLocaleDateString('en-US', options);
}

/**
 * 解析任意日期格式为 Date 对象
 * @param {string} dateStr - 日期字符串
 * @returns {Date|null} Date 对象或 null
 */
export function parseDate(dateStr) {
  if (!dateStr) return null;
  
  // 尝试多种常见格式
  const formats = [
    /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/,
    /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/,
    /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/,
    /^(\d{4})\.(\d{2})\.(\d{2})$/,
    /^(\d{4})-(\d{2})-(\d{2})$/,
    /^(\w+)\s+(\d+),\s+(\d+),\s+(\d+):(\d+)$/,
  ];
  
  for (const format of formats) {
    const match = dateStr.match(format);
    if (match) {
      // ISO 8601 格式
      if (format === formats[0]) {
        return new Date(dateStr);
      }
      // YYYY-MM-DD HH:mm:ss
      if (format === formats[1]) {
        return new Date(
          parseInt(match[1]),
          parseInt(match[2]) - 1,
          parseInt(match[3]),
          parseInt(match[4]),
          parseInt(match[5]),
          parseInt(match[6])
        );
      }
      // YYYY-MM-DD HH:mm
      if (format === formats[2]) {
        return new Date(
          parseInt(match[1]),
          parseInt(match[2]) - 1,
          parseInt(match[3]),
          parseInt(match[4]),
          parseInt(match[5])
        );
      }
      // YYYY.MM.DD
      if (format === formats[3]) {
        return new Date(
          parseInt(match[1]),
          parseInt(match[2]) - 1,
          parseInt(match[3])
        );
      }
      // YYYY-MM-DD
      if (format === formats[4]) {
        return new Date(
          parseInt(match[1]),
          parseInt(match[2]) - 1,
          parseInt(match[3])
        );
      }
      // Month Day, Year, HH:mm
      if (format === formats[5]) {
        const months = ['January', 'February', 'March', 'April', 'May', 'June',
          'July', 'August', 'September', 'October', 'November', 'December'];
        const monthIndex = months.indexOf(match[1]);
        if (monthIndex !== -1) {
          return new Date(
            parseInt(match[3]),
            monthIndex,
            parseInt(match[2]),
            parseInt(match[4]),
            parseInt(match[5])
          );
        }
      }
    }
  }
  
  // 最后尝试直接解析
  const date = new Date(dateStr);
  return isNaN(date.getTime()) ? null : date;
}

/**
 * 判断日期是否有效
 * @param {Date|string} date - 日期对象或日期字符串
 * @returns {boolean} 是否有效
 */
export function isValidDate(date) {
  if (!date) {
    return false;
  }
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  return !isNaN(dateObj.getTime());
}

/**
 * 获取两个日期之间的天数差
 * @param {Date|string} date1 - 第一个日期
 * @param {Date|string} date2 - 第二个日期
 * @returns {number} 天数差
 */
export function getDaysDifference(date1, date2) {
  if (!date1 || !date2) {
    return 0;
  }
  const d1 = typeof date1 === 'string' ? new Date(date1) : date1;
  const d2 = typeof date2 === 'string' ? new Date(date2) : date2;
  
  if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
    return 0;
  }
  
  const diff = Math.abs(d1.getTime() - d2.getTime());
  return Math.floor(diff / (1000 * 60 * 60 * 24));
}

/**
 * 比较两个日期
 * @param {Date|string} date1 - 第一个日期
 * @param {Date|string} date2 - 第二个日期
 * @returns {number} -1 (date1 < date2), 0 (相等), 1 (date1 > date2)
 */
export function compareDates(date1, date2) {
  if (!date1 || !date2) {
    return 0;
  }
  const d1 = typeof date1 === 'string' ? new Date(date1) : date1;
  const d2 = typeof date2 === 'string' ? new Date(date2) : date2;
  
  if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
    return 0;
  }
  
  const t1 = d1.getTime();
  const t2 = d2.getTime();
  
  if (t1 < t2) return -1;
  if (t1 > t2) return 1;
  return 0;
}

/**
 * 对日期数组进行排序
 * @param {Array} dates - 日期数组
 * @param {string} [order='asc'] - 排序顺序 ('asc' | 'desc')
 * @returns {Array} 排序后的日期数组
 */
export function sortDates(dates, order = 'asc') {
  return [...dates].sort((a, b) => {
    const result = compareDates(a, b);
    return order === 'desc' ? -result : result;
  });
}
