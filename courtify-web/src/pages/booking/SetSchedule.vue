<template>
  <div class="set-schedule-page pb-5">
    <TopBar type="back" @go-back="$router.back()" />

    <!-- Multi-Day Promo (opsional) -->
    <div class="bg-light py-3 text-center mb-3">
      <h6 class="fw-bold text-uppercase text-muted">Butuh Sewa Untuk Event Berhari-Hari?</h6>
      <a href="#" class="fw-bold text-primary text-decoration-none" @click.prevent="onMultiDay">
        BERLIH KE MULTI-DAY >>>
      </a>
    </div>

    <div class="container">
      <!-- Select Date Section -->
      <h5 class="fw-bold">Select Date</h5>
      <div class="date-slider mb-4">
        <swiper
          :slides-per-view="'auto'"
          :space-between="10"
          :centered-slides="false"
          @swiper="onSwiper"
          class="date-swiper"
        >
          <swiper-slide
            v-for="(date, idx) in dates"
            :key="idx"
            class="date-item text-center rounded-3"
            :class="{ active: selectedDate === date.full }"
            @click="selectDate(date.full)"
          >
            <div class="fw-bold text-uppercase small">{{ date.month }}</div>
            <div class="fw-bold fs-5">{{ date.day }}</div>
            <div class="small">{{ date.dayName }}</div>
          </swiper-slide>
        </swiper>
      </div>

      <!-- Duration Section -->
      <div class="mb-4">
        <h5 class="fw-bold">Durasi Booking</h5>
        <select
          v-model="duration"
          class="form-select"
          @change="onDurationChange"
        >
          <option :value="null" disabled>Pilih Durasi</option>
          <option v-for="n in 23" :key="n" :value="n">{{ n }} Jam</option>
        </select>
      </div>

      <!-- Start Time Section -->
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="fw-bold mb-0">Start Time</h5>
          <span
            class="badge bg-success"
            v-if="availableSlots.length > 0"
          >
            TERSEDIA
          </span>
        </div>
        <p class="text-muted small">ISI DURASI (1 - 23 JAM)</p>

        <!-- Time Slots List -->
        <div v-if="availableSlots.length" class="list-group">
          <label
            v-for="(slot, idx) in availableSlots"
            :key="idx"
            class="list-group-item d-flex justify-content-between align-items-center slot-item"
            :class="{ 'border-primary bg-light': selectedSlot === slot.start_time }"
          >
            <span>{{ formatSlot(slot) }}</span>
            <input
              type="radio"
              name="timeSlot"
              :value="slot.start_time"
              v-model="selectedSlot"
              class="form-check-input"
              @change="onSlotSelect"
            />
          </label>
        </div>
        <div v-else class="alert alert-secondary">
          Tidak ada slot tersedia untuk tanggal dan durasi yang dipilih.
        </div>
      </div>

      <!-- Total Price & Confirm -->
      <div class="mt-4 d-grid gap-2">
        <div class="d-flex justify-content-between align-items-center bg-light rounded p-3">
          <span class="fw-bold">Total Price</span>
          <span class="fw-bold text-primary fs-4">
            Rp{{ formatPrice(totalPrice) }}
          </span>
        </div>
        <button
          class="btn btn-primary btn-lg fw-bold text-uppercase py-3"
          :disabled="!isFormValid"
          @click="confirmBooking"
        >
          Confirm ✓
        </button>
        <div v-if="errorMessage" class="text-danger small text-center">{{ errorMessage }}</div>
      </div>
    </div>

    <BottomNavbar activeRoute="explore" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'

export default {
  name: 'SetSchedule',
  components: {
    TopBar,
    BottomNavbar,
    Swiper,
    SwiperSlide
  },
  emits: ['navigate'],
  setup() {
    const route = useRoute()
    const router = useRouter()
    const bookingStore = useBookingStore()

    const venueId = route.params.id
    const venue = ref({})
    const pricePerHour = ref(0)

    // Date slider
    const dates = ref([])
    const selectedDate = ref(null)

    // Duration
    const duration = ref(null)

    // Time slots
    const availableSlots = ref([])
    const selectedSlot = ref(null)

    // UI
    const isLoading = ref(false)
    const errorMessage = ref('')

    // Generate 7 hari ke depan
    const generateDates = () => {
      const today = new Date()
      const result = []
      for (let i = 0; i < 7; i++) {
        const d = new Date(today)
        d.setDate(today.getDate() + i)
        const year = d.getFullYear()
        const month = String(d.getMonth() + 1).padStart(2, '0')
        const day = String(d.getDate()).padStart(2, '0')
        const full = `${year}-${month}-${day}` // YYYY-MM-DD
        const monthAbbr = d.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase()
        const dayName = d.toLocaleDateString('id-ID', { weekday: 'short' })
        result.push({
          full,
          month: monthAbbr,
          day: d.getDate(),
          dayName
        })
      }
      dates.value = result
      // Set selectedDate ke hari ini
      if (!selectedDate.value) {
        selectedDate.value = result[0].full
      }
    }

    // Fetch venue info (price) & slot saat komponen dimount
    const fetchVenueInfo = async () => {
      // Ambil detail venue untuk price_per_hour (bisa dari store atau API)
      // Asumsikan dari bookingStore yang sudah di-set dari halaman sebelumnya
      const savedVenue = bookingStore.venue
      if (savedVenue && savedVenue.id == venueId) {
        venue.value = savedVenue
        pricePerHour.value = savedVenue.price_per_hour
      } else {
        // fallback: fetch dari API
        try {
          const res = await axios.get(`/api/venues/${venueId}`)
          venue.value = res.data.data
          pricePerHour.value = venue.value.price_per_hour
          bookingStore.setVenue({
            id: venueId,
            name: venue.value.name,
            price_per_hour: venue.value.price_per_hour
          })
        } catch (err) {
          console.error('Gagal memuat venue untuk jadwal:', err)
        }
      }
    }

    // Fetch available slots
    const fetchSlots = async () => {
      if (!selectedDate.value || !duration.value) {
        availableSlots.value = []
        return
      }
      isLoading.value = true
      errorMessage.value = ''
      try {
        // Contoh API call: GET /api/venues/{venueId}/slots?date={selectedDate}&duration={duration}
        // const res = await axios.get(`/api/venues/${venueId}/slots`, {
        //   params: { date: selectedDate.value, duration: duration.value }
        // })
        // availableSlots.value = res.data.data

        // Data simulasi
        const mockSlots = [
          { start_time: '06:00', end_time: '08:00' },
          { start_time: '08:00', end_time: '10:00' },
          { start_time: '10:00', end_time: '12:00' },
          { start_time: '13:00', end_time: '15:00' },
          { start_time: '15:00', end_time: '17:00' }
        ]
        availableSlots.value = mockSlots
      } catch (err) {
        errorMessage.value = 'Gagal mengambil slot waktu.'
      } finally {
        isLoading.value = false
      }
    }

    const selectDate = (date) => {
      selectedDate.value = date
      selectedSlot.value = null
      fetchSlots()
    }

    const onDurationChange = () => {
      selectedSlot.value = null
      fetchSlots()
    }

    const onSlotSelect = () => {
      // nothing khusus
    }

    // Format slot tampilan
    const formatSlot = (slot) => {
      return `${slot.start_time} - ${slot.end_time}`
    }

    const totalPrice = computed(() => {
      if (!pricePerHour.value || !duration.value) return 0
      return pricePerHour.value * duration.value
    })

    const isFormValid = computed(() => {
      return selectedDate.value && duration.value && selectedSlot.value
    })

    const confirmBooking = () => {
      if (!isFormValid.value) {
        errorMessage.value = 'Harap lengkapi semua pilihan: tanggal, durasi, dan jam mulai.'
        return
      }
      // Simpan ke booking store
      bookingStore.setSchedule({
        date: selectedDate.value,
        duration: duration.value,
        start_time: selectedSlot.value,
        total_price: totalPrice.value
      })
      // Navigasi ke halaman checkout
      router.push(`/venues/${venueId}/checkout`)
    }

    const onMultiDay = () => {
      alert('Fitur multi-day booking akan segera hadir!')
    }

    onMounted(() => {
      generateDates()
      fetchVenueInfo()
      // Inisialisasi fetchSlots jika date dan duration sudah terpilih
      if (selectedDate.value && duration.value) {
        fetchSlots()
      }
    })

    return {
      dates,
      selectedDate,
      duration,
      availableSlots,
      selectedSlot,
      totalPrice,
      isFormValid,
      errorMessage,
      selectDate,
      onDurationChange,
      onSlotSelect,
      formatSlot,
      confirmBooking,
      onMultiDay,
      formatPrice: (val) => Number(val).toLocaleString('id-ID')
    }
  }
}
</script>

<style scoped>
.date-swiper .swiper-slide {
  width: auto;
}
.date-item {
  width: 70px;
  padding: 0.75rem 0.5rem;
  background: #f0f0f0;
  cursor: pointer;
  transition: all 0.2s;
}
.date-item.active {
  background: #0d6efd;
  color: #fff;
}
.date-item.active .text-muted {
  color: rgba(255,255,255,0.7) !important;
}
.slot-item {
  cursor: pointer;
  transition: background 0.2s;
}
.slot-item:hover {
  background: #f8f9fa;
}
</style>