<template>
  <div class="card history-card shadow-sm mb-3 border-0">
    <div class="card-body p-3">
      <!-- Status & Total Harga -->
      <div class="d-flex justify-content-between align-items-start mb-2">
        <span class="badge" :class="statusBadgeClass">
          {{ statusLabel }}
        </span>
        <span class="fw-bold text-primary">
          Rp{{ formatPrice(booking.total_price) }}
        </span>
      </div>

      <!-- Nama Venue -->
      <h6 class="fw-bold mb-1">{{ booking.venue.name }}</h6>

      <!-- Lokasi & Tipe -->
      <p class="text-muted small mb-1">
        📍 {{ booking.venue.location }} ·
        <span class="badge bg-light text-dark">
          {{ booking.venue.type === 'indoor' ? 'Indoor' : 'Outdoor' }}
        </span>
      </p>

      <!-- Rating -->
      <div class="mb-2">
        <span
          v-if="booking.rating !== null && booking.rating !== undefined"
          class="text-warning me-1"
        >
          ⭐{{ booking.rating }}
        </span>
        <span v-else class="text-muted small">Belum dinilai</span>
      </div>

      <!-- Jadwal -->
      <p class="small text-muted mb-3">
        <span class="me-1">⏰</span>
        {{ formattedSchedule }}
      </p>

      <!-- Tombol Aksi -->
      <div class="d-flex gap-2">
        <button
          class="btn btn-primary btn-sm"
          @click="$emit('rebook', booking.id)"
        >
          Rebook
        </button>
        <button
          class="btn btn-outline-primary btn-sm"
          @click="$emit('view-detail', booking.id)"
        >
          Lihat Detail
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HistoryCard',
  props: {
    booking: {
      type: Object,
      required: true,
      validator: (v) =>
        v &&
        typeof v.id !== 'undefined' &&
        v.venue &&
        typeof v.venue.name === 'string' &&
        v.start_time &&
        v.end_time
    }
  },
  emits: ['rebook', 'view-detail'],
  computed: {
    // Status dihitung on-the-fly berdasarkan waktu sekarang
    statusComputed() {
      const now = new Date()
      const start = new Date(this.booking.start_time)
      const end = new Date(this.booking.end_time)

      if (isNaN(start.getTime()) || isNaN(end.getTime())) {
        return 'pending' // fallback
      }

      if (now < start) return 'pending'
      if (now >= start && now <= end) return 'ongoing'
      return 'completed'
    },
    statusBadgeClass() {
      switch (this.statusComputed) {
        case 'pending':
          return 'bg-secondary text-white'
        case 'ongoing':
          return 'bg-warning text-dark'
        case 'completed':
          return 'bg-success text-white'
        default:
          return 'bg-secondary text-white'
      }
    },
    statusLabel() {
      switch (this.statusComputed) {
        case 'pending':
          return 'Pending'
        case 'ongoing':
          return 'Berlangsung'
        case 'completed':
          return 'Selesai'
        default:
          return 'Pending'
      }
    },
    formattedSchedule() {
      const startStr = this.formatDateTime(this.booking.start_time)
      const endTime = this.formatTimeOnly(this.booking.end_time)
      return `${startStr} - ${endTime}`
    }
  },
  methods: {
    formatPrice(value) {
      return Number(value).toLocaleString('id-ID')
    },
    formatDateTime(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return dateString
      // Contoh: "3 Aug 2026, 10:00"
      const day = date.getDate()
      const month = date.toLocaleDateString('en-US', { month: 'short' })
      const year = date.getFullYear()
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      return `${day} ${month} ${year}, ${hours}:${minutes}`
    },
    formatTimeOnly(dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return ''
      const hours = String(date.getHours()).padStart(2, '0')
      const minutes = String(date.getMinutes()).padStart(2, '0')
      return `${hours}:${minutes}`
    }
  }
}
</script>

<style scoped>
.history-card {
  border-radius: 12px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.history-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}
</style>