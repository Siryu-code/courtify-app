<template>
  <div class="history-page">
    <TopBar type="hamburger" @toggle-menu="$emit('toggle-menu')" />

    <div class="text-center py-4">
      <h4 class="fw-bold">Riwayat Booking</h4>
      <p class="text-muted small">Lihat semua booking Anda</p>
    </div>

    <div class="container pb-4">
      <div v-if="bookings.length" class="d-flex flex-column gap-3">
        <HistoryCard
          v-for="booking in bookings"
          :key="booking.id"
          :booking="booking"
          @rebook="onRebook(booking.id)"
          @view-detail="onViewDetail(booking.id)"
        />
      </div>
      <div v-else class="text-center text-muted py-5">
        Belum ada riwayat booking
      </div>
    </div>

    <BottomNavbar activeRoute="history" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import HistoryCard from '@/components/history/HistoryCard.vue'

export default {
  name: 'HistoryPage',
  components: { TopBar, BottomNavbar, HistoryCard },
  emits: ['toggle-menu', 'navigate'],
  data() {
    return {
      bookings: []
    }
  },
  mounted() {
    this.fetchBookings()
  },
  methods: {
    async fetchBookings() {
      // Gunakan auth header di axios: const res = await axios.get('/api/bookings');
      // Simulasi
      this.bookings = [
        {
          id: 1,
          venue: { name: 'Futsal Champion', location: 'Jakarta', type: 'indoor' },
          start_time: new Date(Date.now() + 3600000).toISOString(),
          end_time: new Date(Date.now() + 7200000).toISOString(),
          total_price: 150000,
          rating: null,
          status: 'ongoing' // akan dihitung otomatis oleh HistoryCard
        }
      ]
    },
    onRebook(id) { /* logika rebook */ },
    onViewDetail(id) { this.$emit('view-detail', id) }
  }
}
</script>