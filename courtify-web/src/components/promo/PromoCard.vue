<template>
  <div class="card promo-card shadow-sm h-100 border-0">
    <div class="card-body p-3 d-flex flex-column">
      <!-- Top badges -->
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge bg-success">Aktif</span>
        <span class="badge bg-primary">{{ discountText }}</span>
      </div>

      <!-- Judul & deskripsi -->
      <h6 class="fw-bold mb-1">{{ promo.name }}</h6>
      <p class="text-muted small line-clamp-2 flex-grow-1 mb-2">
        {{ promo.description }}
      </p>

      <!-- Tanggal berlaku -->
      <div class="d-flex align-items-center text-muted small mt-auto">
        <span class="me-1">📅</span>
        <span>{{ formattedDateRange }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PromoCard',
  props: {
    promo: {
      type: Object,
      required: true,
      validator: (v) =>
        v &&
        typeof v.id !== 'undefined' &&
        typeof v.name === 'string' &&
        typeof v.description === 'string' &&
        (v.type === 'percent' || v.type === 'fixed') &&
        typeof v.value === 'number'
    }
  },
  computed: {
    discountText() {
      if (this.promo.type === 'percent') {
        return `Diskon ${this.promo.value}%`
      }
      return `Potongan Rp${this.promo.value.toLocaleString('id-ID')}`
    },
    formattedDateRange() {
      const start = this.formatDate(this.promo.start_date)
      const end = this.formatDate(this.promo.end_date)
      return `${start} - ${end}`
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return dateString // fallback
      // Format: "Nov 1, 2023"
      return date.toLocaleDateString('en-ID', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
/* Membatasi deskripsi maksimal 2 baris */
.line-clamp-2 {
  display: -webkit-box;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Hover card */
.promo-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 12px;
}
.promo-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}
</style>