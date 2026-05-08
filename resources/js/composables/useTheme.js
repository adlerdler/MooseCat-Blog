import { ref, computed } from 'vue'

const themes = [
  { name: 'RED', color: '#CF202E', label: 'CONSTRUCT_01' },
  { name: 'ORANGE', color: '#FF6B00', label: 'SAFETY_02' },
  { name: 'BLUE', color: '#0066FF', label: 'ELECTRIC_03' },
  { name: 'GREEN', color: '#00CC44', label: 'MATRIX_04' },
  { name: 'PINK', color: '#FF007A', label: 'CYBER_05' },
]

const currentTheme = ref(themes[0])

function setTheme(theme) {
  currentTheme.value = theme
  localStorage.setItem('theme', theme.name)
  applyTheme(theme)
}

function applyTheme(theme) {
  document.documentElement.style.setProperty('--accent', theme.color)
}

function initTheme() {
  const saved = localStorage.getItem('theme')
  if (saved) {
    const found = themes.find(t => t.name === saved)
    if (found) {
      currentTheme.value = found
      applyTheme(found)
      return
    }
  }
  applyTheme(currentTheme.value)
}

export function useTheme() {
  return {
    themes: computed(() => themes),
    currentTheme: computed(() => currentTheme.value),
    setTheme,
    initTheme,
  }
}
