<template>
  <nav class="bottom-navbar fixed-bottom d-flex align-items-center">
    <button
      v-for="item in menuItems"
      :key="item.route"
      class="nav-item d-flex flex-column align-items-center"
      :class="{ active: activeRoute === item.route }"
      @click="$emit('navigate', item.route)"
      :aria-label="item.label"
      :aria-current="activeRoute === item.route ? 'page' : undefined"
    >
      <!-- Icon SVG (simple) -->
      <component :is="item.icon" class="nav-icon" />
      <span class="nav-label">{{ item.label }}</span>
    </button>
  </nav>
</template>

<script>
import { defineComponent, h } from 'vue'

/**
 * Daftar menu navigasi bawah
 * Setiap item memiliki route, label, dan properti icon berupa functional component SVG
 */
const menuItems = [
  {
    route: 'home',
    label: 'Home',
    icon: defineComponent({
      render() {
        return h('svg', { width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
          h('path', { d: 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z' }),
          h('polyline', { points: '9 22 9 12 15 12 15 22' })
        ])
      }
    })
  },
  {
    route: 'explore',
    label: 'Explore',
    icon: defineComponent({
      render() {
        return h('svg', { width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
          h('circle', { cx: 11, cy: 11, r: 8 }),
          h('line', { x1: 21, y1: 21, x2: 16.65, y2: 16.65 })
        ])
      }
    })
  },
  {
    route: 'deals',
    label: 'Deals',
    icon: defineComponent({
      render() {
        return h('svg', { width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
          h('path', { d: 'M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z' }),
          h('line', { x1: 7, y1: 7, x2: 7.01, y2: 7 })
        ])
      }
    })
  },
  {
    route: 'history',
    label: 'History',
    icon: defineComponent({
      render() {
        return h('svg', { width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
          h('circle', { cx: 12, cy: 12, r: 10 }),
          h('polyline', { points: '12 6 12 12 16 14' })
        ])
      }
    })
  },
  {
    route: 'profile',
    label: 'Profile',
    icon: defineComponent({
      render() {
        return h('svg', { width: 24, height: 24, viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': 2, 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
          h('path', { d: 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2' }),
          h('circle', { cx: 12, cy: 7, r: 4 })
        ])
      }
    })
  }
]

export default {
  name: 'BottomNavbar',
  props: {
    /**
     * Nama rute yang sedang aktif, sesuai dengan nilai route dari menuItems.
     * Contoh: 'home', 'explore', 'deals', 'history', 'profile'
     */
    activeRoute: {
      type: String,
      default: 'home'
    }
  },
  emits: ['navigate'],
  data() {
    return {
      menuItems
    }
  }
}
</script>

<style scoped>
/* ---------- Bottom Navbar ---------- */
.bottom-navbar {
  height: 60px;
  background-color: var(--crt-white);
  border-top: 1px solid var(--crt-border);
  justify-content: space-around;
  padding: 0 8px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
  z-index: 1020;
}

/* ---------- Nav Item (button) ---------- */
.nav-item {
  border: none;
  background: transparent;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  padding: 8px 0;
  color: var(--crt-gray);
  cursor: pointer;
  transition: color 0.2s ease;
  -webkit-tap-highlight-color: transparent;
}

.nav-item:hover {
  color: var(--crt-primary);
}

.nav-item.active {
  color: var(--crt-primary);
}

/* ---------- Icon ---------- */
.nav-icon {
  width: 24px;
  height: 24px;
  margin-bottom: 2px;
}

/* ---------- Label ---------- */
.nav-label {
  font-size: 0.75rem; /* 12px */
  font-weight: 500;
  white-space: nowrap;
}

/* Responsive: sedikit lebih besar di desktop jika diperlukan */
@media (min-width: 768px) {
  .bottom-navbar {
    height: 64px;
  }

  .nav-icon {
    width: 26px;
    height: 26px;
  }

  .nav-label {
    font-size: 0.8125rem; /* 13px */
  }
}
</style>