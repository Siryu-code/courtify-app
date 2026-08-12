<template>
  <header class="topbar d-flex align-items-center justify-content-between">
    <!-- Left: Action Button -->
    <div class="topbar-left d-flex align-items-center">
      <button
        v-if="type === 'hamburger'"
        class="btn-icon"
        @click="$emit('toggle-menu')"
        aria-label="Toggle menu"
      >
        <!-- Simple hamburger SVG -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <button
        v-else-if="type === 'back'"
        class="btn-icon"
        @click="$emit('go-back')"
        aria-label="Go back"
      >
        <!-- Back arrow SVG -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <button
        v-else-if="type === 'close'"
        class="btn-icon"
        @click="$emit('close-modal')"
        aria-label="Close"
      >
        <!-- Close X SVG -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Center: Title -->
    <div class="topbar-center text-center">
      <h1 class="topbar-title">Courtify Arena</h1>
    </div>

    <!-- Right: Empty (for balanced flex) -->
    <div class="topbar-right d-flex align-items-center">
      <!-- Intentionally left empty to maintain centering of title -->
    </div>
  </header>
</template>

<script>
export default {
  name: 'TopBar',
  props: {
    /**
     * Tipe tombol kiri:
     * "hamburger" -> icon tiga garis, emit "toggle-menu"
     * "back" -> panah kiri, emit "go-back"
     * "close" -> silang, emit "close-modal"
     */
    type: {
      type: String,
      required: true,
      validator: (value) => ['hamburger', 'back', 'close'].includes(value)
    }
  },
  emits: ['toggle-menu', 'go-back', 'close-modal']
}
</script>

<style scoped>
/* ---------- Topbar Container ---------- */
.topbar {
  height: 56px;
  background-color: var(--crt-bg-light);
  border-bottom: 1px solid var(--crt-border);
  padding: 0 12px;
  position: sticky;
  top: 0;
  z-index: 1020; /* above Bootstrap's default */
}

@media (min-width: 768px) {
  .topbar {
    height: 60px;
    padding: 0 16px;
  }
}

/* ---------- Button Icon ---------- */
.btn-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  background: transparent;
  color: var(--crt-text-dark);
  border-radius: 50%;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.btn-icon:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.btn-icon:focus-visible {
  outline: 2px solid var(--crt-primary);
  outline-offset: 2px;
}

/* ---------- Title ---------- */
.topbar-title {
  font-size: 1.125rem; /* 18px */
  font-weight: 700;
  color: var(--crt-text-dark);
  margin: 0;
  white-space: nowrap;
  line-height: 1.2;
}

@media (min-width: 768px) {
  .topbar-title {
    font-size: 1.25rem; /* 20px */
  }
}

/* ---------- Layout sections ---------- */
.topbar-left,
.topbar-right {
  flex: 0 0 40px; /* fixed width untuk menjaga center tetap di tengah */
  display: flex;
  align-items: center;
}

.topbar-left {
  justify-content: flex-start;
}

.topbar-right {
  justify-content: flex-end;
}

.topbar-center {
  flex: 1;
  text-align: center;
}
</style>