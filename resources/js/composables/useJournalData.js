/**
 * useJournalData.js - 日记数据 Composable
 * 
 * 功能说明：
 * - 提供日记相关的工具函数
 * - 供 Journals.vue 和 JournalForm.vue 使用
 */
import { journals } from '../data/journals';

export function useJournalData() {
  const getMoodTypes = () => ['excited', 'happy', 'calm', 'thoughtful', 'motivated', 'sad', 'anxious', 'neutral'];
  
  const getWeatherTypes = () => ['sunny', 'cloudy', 'rainy', 'snowy', 'windy', 'clear', 'stormy'];
  
  const getMoodLabel = (mood) => {
    const moodMap = {
      'excited': '兴奋',
      'happy': '开心',
      'calm': '平静',
      'thoughtful': '沉思',
      'motivated': '充满动力',
      'sad': '悲伤',
      'anxious': '焦虑',
      'neutral': '平静'
    };
    return moodMap[mood] || mood;
  };
  
  const getWeatherLabel = (weather) => {
    const weatherMap = {
      'sunny': '晴天',
      'cloudy': '多云',
      'rainy': '雨天',
      'snowy': '雪天',
      'windy': '大风',
      'clear': '晴朗',
      'stormy': '暴风雨'
    };
    return weatherMap[weather] || weather;
  };
  
  const getJournalsByUserId = (userId) => {
    return journals
      .filter(j => j.user_id === userId)
      .sort((a, b) => new Date(b.date) - new Date(a.date));
  };

  return {
    getMoodTypes,
    getWeatherTypes,
    getMoodLabel,
    getWeatherLabel,
    getJournalsByUserId
  };
}
