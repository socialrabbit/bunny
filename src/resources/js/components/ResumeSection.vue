<template>
  <section class="resume-section" v-if="enabled && resumeData">
    <div class="resume-container">
      <div class="resume-preview">
        <div class="resume-header">
          <h2 class="resume-title">{{ resumeData.title }}</h2>
          <p class="resume-subtitle">{{ resumeData.subtitle }}</p>
        </div>
        
        <div class="resume-content">
          <div class="resume-info">
            <div class="resume-meta">
              <span class="resume-meta-item">
                <i class="fas fa-file-pdf"></i>
                {{ resumeData.fileType }}
              </span>
              <span class="resume-meta-item">
                <i class="fas fa-clock"></i>
                {{ formatDate(resumeData.lastUpdated) }}
              </span>
            </div>
            
            <p class="resume-description">{{ resumeData.description }}</p>
            
            <div class="resume-highlights" v-if="resumeData.highlights?.length">
              <h3 class="resume-highlights-title">Highlights</h3>
              <ul class="resume-highlights-list">
                <li v-for="highlight in resumeData.highlights" :key="highlight">
                  {{ highlight }}
                </li>
              </ul>
            </div>
          </div>
          
          <div class="resume-actions">
            <a 
              :href="resumeData.downloadUrl" 
              class="resume-download-btn"
              download
              @click="trackDownload"
            >
              <i class="fas fa-download"></i>
              Download Resume
            </a>
            
            <div class="resume-formats" v-if="resumeData.alternateFormats?.length">
              <span class="resume-formats-label">Also available in:</span>
              <div class="resume-format-buttons">
                <a
                  v-for="format in resumeData.alternateFormats"
                  :key="format.type"
                  :href="format.url"
                  class="resume-format-btn"
                  :title="'Download ' + format.type.toUpperCase() + ' version'"
                  download
                  @click="trackDownload(format.type)"
                >
                  {{ format.type.toUpperCase() }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ResumeSection',
  
  props: {
    enabled: {
      type: Boolean,
      default: true
    }
  },
  
  data() {
    return {
      resumeData: null
    }
  },
  
  async created() {
    try {
      const response = await fetch('/api/resume');
      this.resumeData = await response.json();
    } catch (error) {
      console.error('Error fetching resume data:', error);
    }
  },
  
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    
    trackDownload(format = 'pdf') {
      // Track download analytics if enabled
      this.$emit('resume-download', { format });
    }
  }
}
</script>

<style scoped>
.resume-section {
  padding: 3rem 0;
  background: var(--background-color, #ffffff);
}

.resume-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

.resume-preview {
  background: var(--card-background, #ffffff);
  border-radius: 1rem;
  box-shadow: var(--shadow, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
  overflow: hidden;
}

.resume-header {
  padding: 2rem;
  background: var(--primary-color, #2563eb);
  color: white;
}

.resume-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
}

.resume-subtitle {
  margin: 0.5rem 0 0;
  opacity: 0.9;
}

.resume-content {
  padding: 2rem;
  display: grid;
  gap: 2rem;
  grid-template-columns: 2fr 1fr;
}

.resume-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.resume-meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-color, #4b5563);
  font-size: 0.875rem;
}

.resume-description {
  color: var(--text-color, #4b5563);
  line-height: 1.6;
  margin: 0 0 1.5rem;
}

.resume-highlights-title {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 1rem;
  color: var(--text-color, #1f2937);
}

.resume-highlights-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.resume-highlights-list li {
  position: relative;
  padding-left: 1.5rem;
  margin-bottom: 0.5rem;
  color: var(--text-color, #4b5563);
}

.resume-highlights-list li::before {
  content: '•';
  position: absolute;
  left: 0.5rem;
  color: var(--primary-color, #2563eb);
}

.resume-actions {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.resume-download-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  background: var(--primary-color, #2563eb);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
}

.resume-download-btn:hover {
  background: var(--secondary-color, #1d4ed8);
  transform: translateY(-1px);
}

.resume-formats {
  border-top: 1px solid var(--border-color, #e5e7eb);
  padding-top: 1.5rem;
}

.resume-formats-label {
  display: block;
  font-size: 0.875rem;
  color: var(--text-color, #4b5563);
  margin-bottom: 0.75rem;
}

.resume-format-buttons {
  display: flex;
  gap: 0.5rem;
}

.resume-format-btn {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 0.375rem;
  font-size: 0.75rem;
  color: var(--text-color, #4b5563);
  text-decoration: none;
  transition: all 0.2s ease;
}

.resume-format-btn:hover {
  border-color: var(--primary-color, #2563eb);
  color: var(--primary-color, #2563eb);
}

@media (max-width: 640px) {
  .resume-content {
    grid-template-columns: 1fr;
  }
  
  .resume-actions {
    border-top: 1px solid var(--border-color, #e5e7eb);
    padding-top: 1.5rem;
  }
}
</style> 