<template>
  <div class="success-page">
    <TopBar type="close" @close-modal="$router.push('/')" />

    <!-- Header dengan background navy -->
    <div class="success-header d-flex flex-column align-items-center justify-content-center position-relative">
      <div class="check-icon bg-white rounded-circle shadow d-flex align-items-center justify-content-center">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
      </div>
    </div>

    <div class="container px-4 pb-5">
      <!-- Success Message -->
      <div class="text-center mt-5 pt-4">
        <h3 class="fw-bold">Booking Confirmed!</h3>
        <p class="text-muted">Your court has been successfully reserved. Get ready to play.</p>
      </div>

      <!-- Booking ID -->
      <div class="bg-light rounded-3 p-3 text-center mb-4">
        <span class="text-uppercase fw-bold text-muted small">Booking ID</span>
        <h5 class="fw-bold font-monospace mb-0">CRT-{{ String(booking.id).slice(-4).padStart(4, '0') }}</h5>
      </div>

      <!-- Mini Booking Details -->
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
              <i class="bi bi-geo-alt fs-4 text-primary me-2"></i>
              <div>
                <div class="text-uppercase small text-muted fw-bold">Venue</div>
                <div class="fw-bold">{{ booking.venue?.name }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card border-0 shadow-sm p-3 text-center">
            <i class="bi bi-calendar3 text-muted fs-5"></i>
            <div class="text-uppercase small text-muted fw-bold">Date</div>
            <div class="fw-bold">{{ formatDate(booking.start_time) }}</div>
          </div>
        </div>
        <div class="col-6">
          <div class="card border-0 shadow-sm p-3 text-center">
            <i class="bi bi-clock text-muted fs-5"></i>
            <div class="text-uppercase small text-muted fw-bold">Time</div>
            <div class="fw-bold">{{ formatTime(booking.start_time, booking.end_time) }}</div>
          </div>
        </div>
      </div>

      <!-- Booking Summary dengan context success -->
      <BookingSummary
        v-if="booking"
        :booking="booking"
        context="success"
      />

      <!-- Action Buttons -->
      <div class="mt-4 d-grid gap-2">
        <router-link to="/history" class="btn btn-primary btn-lg fw-bold text-uppercase">
          View History →
        </router-link>
        <router-link to="/" class="btn btn-outline-dark btn-lg fw-bold text-uppercase">
          Back to Home
        </router-link>
      </div>

      <!-- View Detail / Receipt -->
      <div class="text-center mt-3">
        <a
          href="#"
          class="text-decoration-none fw-semibold"
          @click.prevent="showDetailModal = true"
        >
          Lihat Detail Booking
        </a>
      </div>
    </div>

    <!-- Detail Booking Modal -->
    <DetailBookingModal
      v-if="booking"
      :booking="booking"
      :isOpen="showDetailModal"
      @close="showDetailModal = false"
    />

    <BottomNavbar activeRoute="home" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/services/api'
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import BookingSummary from '@/components/booking/BookingSummary.vue'
import DetailBookingModal from '@/components/modals/DetailBookingModal.vue'

export default {
  name: 'BookingSuccess',
  components: {
    TopBar,
    BottomNavbar,
    BookingSummary,
    DetailBookingModal
  },
  emits: ['navigate'],
  setup() {
    const route = useRoute()
    const bookingId = route.params.id
    const booking = ref(null)
    const showDetailModal = ref(false)

    const fetchBooking = async () => {
      try {
        // const res = await axios.get(`/api/bookings/${bookingId}`);
        // booking.value = res.data.data;
        // Simulasi
        booking.value = {
          id: 5521,
          venue: { name: 'Court A - Premium', location: 'Jakarta Selatan', image: 'https://placehold.co/200x150' },
          start_time: '2026-10-14T15:00:00',
          end_time: '2026-10-14T17:00:00',
          total_price: 200000,
          promo: { name: 'Diskon Awal', type: 'percent', value: 20 },
          customer_name: 'Alex Johnson',
          customer_phone: '0812-3456-7890',
          created_at: new Date().toISOString(),
          user: { email: 'alex@example.com' }
        }
      } catch (err) {
        console.error('Gagal mengambil detail booking:', err)
      }
    }

    const formatDate = (dateStr) => {
      if (!dateStr) return ''
      const d = new Date(dateStr)
      if (isNaN(d)) return dateStr
      const day = d.getDate()
      const month = d.toLocaleDateString('id-ID', { month: 'short' })
      return `${day} ${month}`
    }

    const formatTime = (start, end) => {
      if (!start || !end) return ''
      const s = new Date(start)
      const e = new Date(end)
      if (isNaN(s) || isNaN(e)) return ''
      const opts = { hour: '2-digit', minute: '2-digit', hour12: false }
      return `${s.toLocaleTimeString('id-ID', opts)} - ${e.toLocaleTimeString('id-ID', opts)}`
    }

    onMounted(() => {
      fetchBooking()
    })

    return {
      booking,
      showDetailModal,
      formatDate,
      formatTime
    }
  }
}
</script>

<style scoped>
.success-header {
  background: #001f3f;
  height: 150px;
  padding-top: 0;
}
.check-icon {
  width: 80px;
  height: 80px;
  margin-top: 100px; /* tengah overlap */
}
@media (min-width: 768px) {
  .success-header {
    height: 180px;
  }
  .check-icon {
    width: 90px;
    height: 90px;
    margin-top: 130px;
  }
}
</style>