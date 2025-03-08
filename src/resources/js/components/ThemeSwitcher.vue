<template>
  <div class="theme-switcher">
    <label class="theme-switcher__label">Theme:</label>
    <div class="theme-switcher__options">
      <button
        v-for="theme in themes"
        :key="theme.name"
        :class="[
          'theme-switcher__button',
          { 'theme-switcher__button--active': currentTheme === theme.name }
        ]"
        :style="{ '--theme-color': theme.color }"
        @click="switchTheme(theme.name)"
      >
        <span class="theme-switcher__icon" :class="theme.icon"></span>
        {{ theme.label }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ThemeSwitcher',
  data() {
    return {
      currentTheme: 'modern',
      themes: [
        { name: 'modern', label: 'Modern', color: '#2563eb', icon: '🎯' },
        { name: 'dark', label: 'Dark', color: '#8b5cf6', icon: '🌙' },
        { name: 'nature', label: 'Nature', color: '#059669', icon: '🌿' },
        { name: 'retro', label: 'Retro', color: '#f97316', icon: '🎮' },
        { name: 'neon', label: 'Neon', color: '#f0abfc', icon: '⚡' }
      ]
    }
  },
  methods: {
    switchTheme(themeName) {
      // Remove current theme
      document.head.querySelector('link[data-theme]')?.remove();

      // Add new theme
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = `/themes/${themeName}.css`;
      link.dataset.theme = themeName;
      document.head.appendChild(link);

      this.currentTheme = themeName;
      localStorage.setItem('portfolio-theme', themeName);
    }
  },
  mounted() {
    // Load saved theme or default to modern
    const savedTheme = localStorage.getItem('portfolio-theme') || 'modern';
    this.switchTheme(savedTheme);
  }
}
</script>

<style scoped>
.theme-switcher {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin: 1rem 0;
}

.theme-switcher__label {
  font-weight: 600;
  color: #4b5563;
}

.theme-switcher__options {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.theme-switcher__button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 9999px;
  background: white;
  color: #4b5563;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.theme-switcher__button:hover {
  border-color: var(--theme-color);
  color: var(--theme-color);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.theme-switcher__button--active {
  background: var(--theme-color);
  border-color: var(--theme-color);
  color: white;
}

.theme-switcher__icon {
  font-size: 1rem;
}
</style> 