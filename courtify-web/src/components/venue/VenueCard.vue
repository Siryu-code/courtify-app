<template>
  <div class="card venue-card shadow-sm mb-3 border-0">
    <div class="row g-0">
      <!-- Gambar & Status Badge -->
      <div class="col-md-4 position-relative">
        <div class="ratio ratio-16x9">
          <img
            :src="venue.image"
            :alt="venue.name"
            class="img-fluid rounded-start"
            style="object-fit: cover"
          />
        </div>
        <span
          class="badge position-absolute top-0 start-0 m-2"
          :class="statusBadgeClass"
        >
          {{ statusLabel }}
        </span>
      </div>

      <!-- Informasi Venue -->
      <div class="col-md-8">
        <div class="card-body d-flex flex-column h-100 p-3">
          <h5 class="card-title fw-bold mb-1">{{ venue.name }}</h5>

          <!-- Lokasi & Tipe -->
          <p class="card-text text-muted small mb-2">
            <span class="me-2">📍 {{ venue.location }}</span>
            <span class="badge bg-light text-dark">
              {{ venue.type === 'indoor' ? 'Indoor' : 'Outdoor' }}
            </span>
          </p>

          <!-- Rating -->
          <div class="mb-2">
            <span class="text-warning me-1">⭐</span>
            <span class="fw-semibold">
              {{ venue.rating ? venue.rating.toFixed(1) : 'N/A' }}
            </span>
          </div>

          <!-- Harga & Tombol -->
          <div class="mt-auto">
            <p class="fw-bold text-primary mb-2">
              Rp{{ formatPrice(venue.price_per_hour) }}/jam
            </p>
            <div class="d-flex gap-2">
              <button
                class="btn btn-outline-primary btn-sm"
                @click="$emit('view-detail', venue.id)"
              >
                Detail
              </button>
              <button
                class="btn btn-primary btn-sm"
                @click="$emit('book-now', venue.id)"
              >
                Book Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VenueCard',
  props: {
    venue: {
      type: Object,
      required: true,
      validator: (v) =>
        v &&
        typeof v.id !== 'undefined' &&
        typeof v.name === 'string' &&
        typeof v.location === 'string' &&
        typeof v.price_per_hour === 'number'
    }
  },
  emits: ['view-detail', 'book-now'],
  computed: {
    statusBadgeClass() {
      return this.venue.status === 'maintenance'
        ? 'bg-danger text-white'
        : 'bg-success text-white'
    },
    statusLabel() {
      return this.venue.status === 'maintenance' ? 'Dalam Perbaikan' : 'Tersedia'
    }
  },
  methods: {
    formatPrice(value) {
      return Number(value).toLocaleString('id-ID')
    }
  }
}
</script>

<style scoped>
/* Hover effect ringan */
.venue-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.venue-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

/* Supaya gambar menutupi area dengan rapi */
.ratio img {
  object-fit: cover;
  border-top-left-radius: var(--bs-border-radius);
  border-bottom-left-radius: 0;
}
@media (max-width: 767.98px) {
  .ratio img {
    border-radius: var(--bs-border-radius) var(--bs-border-radius) 0 0;
  }
}
</style>