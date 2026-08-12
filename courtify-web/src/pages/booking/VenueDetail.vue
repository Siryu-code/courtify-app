<template>
  <div class="venue-detail-page pb-4">
    <TopBar type="back" @go-back="$router.back()" />

    <!-- Image Slider Section dengan overlay badge & counter -->
    <div class="position-relative">
      <!-- Gunakan VenueImageSlider dari komponen venue -->
      <VenueImageSlider :images="venue.images || []" />
      <!-- Status Badge di kiri atas -->
      <span
        class="badge position-absolute top-0 start-0 m-3 py-2 px-3 rounded-pill fw-bold text-white"
        :class="venue.status === 'maintenance' ? 'bg-danger' : 'bg-success'"
      >
        {{ venue.status === 'maintenance' ? 'DALAM PERBAIKAN' : 'TERSEDIA' }}
      </span>
      <!-- Image counter di kanan bawah -->
      <span
        class="position-absolute bottom-0 end-0 m-3 bg-dark bg-opacity-50 text-white rounded-pill px-3 py-1 small"
        v-if="venue.images && venue.images.length"
      >
        {{ currentImageIndex }} / {{ venue.images.length }}
      </span>
    </div>

    <!-- Price & Name -->
    <div class="container mt-3">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h4 class="fw-bold text-primary mb-0">
            Rp{{ formatPrice(venue.price_per_hour) }}
            <small class="fs-6 text-muted fw-normal">/Jam</small>
          </h4>
          <h3 class="fw-bold">{{ venue.name }}</h3>
        </div>
      </div>

      <!-- Location & Rating -->
      <div class="d-flex align-items-center text-muted small mb-2 gap-3">
        <span>📍 {{ venue.location }}</span>
        <span class="badge bg-light text-dark">
          {{ venue.type === 'indoor' ? 'Indoor' : 'Outdoor' }}
        </span>
        <span class="d-flex align-items-center">
          ⭐ {{ venue.rating?.toFixed(1) || 'N/A' }}
          <span class="ms-1">({{ venue.review_count || 0 }} reviews)</span>
        </span>
      </div>

      <!-- Description -->
      <div class="mt-3">
        <h5 class="fw-bold">Tentang Lapangan Ini</h5>
        <p class="text-muted">{{ venue.description || 'Tidak ada deskripsi.' }}</p>
      </div>

      <!-- Facilities -->
      <div class="mt-3">
        <h5 class="fw-bold mb-3">Fasilitas</h5>
        <div class="row g-3" v-if="venue.facilities && venue.facilities.length">
          <div
            class="col-6 col-md-3"
            v-for="(facility, idx) in venue.facilities"
            :key="idx"
          >
            <div class="card border h-100 shadow-sm text-center p-3 facility-card">
              <i :class="facility.icon || 'bi bi-check-circle'" class="fs-4 text-primary mb-1"></i>
              <div class="fw-bold small">{{ facility.name }}</div>
              <div class="text-muted x-small" v-if="facility.quantity">{{ facility.quantity }}</div>
            </div>
          </div>
        </div>
        <p v-else class="text-muted">Fasilitas belum tersedia.</p>
      </div>

      <!-- BOOK NOW Button -->
      <div class="d-grid mt-4 mb-5">
        <button
          class="btn btn-primary btn-lg fw-bold text-uppercase py-3"
          @click="handleBookNow"
        >
          Book Now
        </button>
      </div>
    </div>

    <BottomNavbar activeRoute="explore" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import VenueImageSlider from '@/components/venue/VenueImageSlider.vue'
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
// Asumsi: Pinia store untuk user dan booking, dan service API
import { useUserStore } from '@/stores/user'
import { useBookingStore } from '@/stores/booking'
import axios from '@/services/api' // atau axios instance

export default {
  name: 'VenueDetail',
  components: {
    TopBar,
    BottomNavbar,
    VenueImageSlider
  },
  emits: ['navigate', 'show-login-modal'],
  setup() {
    const route = useRoute()
    const router = useRouter()
    const userStore = useUserStore()
    const bookingStore = useBookingStore()

    const venueId = ref(route.params.id)
    const venue = reactive({
      name: '',
      location: '',
      type: 'indoor',
      price_per_hour: 0,
      status: 'available',
      images: [],
      rating: 0,
      review_count: 0,
      description: '',
      facilities: []
    })
    const currentImageIndex = ref(1) // akan diupdate dari Swiper event jika perlu
    const isLoading = ref(false)

    const fetchVenue = async () => {
      isLoading.value = true
      try {
        // Ganti dengan panggilan API nyata: const res = await axios.get(`/api/venues/${venueId.value}`);
        // const data = res.data.data;
        // Isi venue dari data
        // Simulasi data
        const data = {
          name: 'Court A - Premium',
          location: 'Courtify Arena Main, Downtown',
          type: 'indoor',
          price_per_hour: 800000,
          status: 'available',
          images: [
            'https://placehold.co/800x400/0d6efd/white?text=Image+1',
            'https://placehold.co/800x400/198754/white?text=Image+2',
            'https://placehold.co/800x400/dc3545/white?text=Image+3'
          ],
          rating: 4.8,
          review_count: 124,
          description:
            'Lapangan indoor premium dengan pencahayaan profesional dan lantai vinyl anti-slip. Cocok untuk pertandingan resmi maupun latihan.',
          facilities: [
            { name: 'Kamar Mandi', quantity: '2 Unit', icon: 'bi bi-droplet' },
            { name: 'Parkir Gratis', quantity: null, icon: 'bi bi-car-front' },
            { name: 'Locker Room', quantity: null, icon: 'bi bi-lock' },
            { name: 'Cafe', quantity: '1', icon: 'bi bi-cup-hot' }
          ]
        }
        Object.assign(venue, data)
        // set default images jika tidak ada
        if (!venue.images.length) {
          venue.images = ['https://placehold.co/800x400/e9ecef/6c757d?text=No+Image']
        }
      } catch (error) {
        console.error('Gagal memuat venue:', error)
      } finally {
        isLoading.value = false
      }
    }

    const handleBookNow = () => {
      // Simpan venueId dan harga ke booking store
      bookingStore.setVenue({
        id: venueId.value,
        name: venue.name,
        price_per_hour: venue.price_per_hour
      })

      if (!userStore.isLoggedIn) {
        // emit event ke parent untuk tampilkan login modal
        // atau panggil metode dari Pinia untuk set modal
        // Asumsikan ada event 'show-login-modal' yang di-handle di App.vue
        this.$emit('show-login-modal')
        return
      }
      router.push(`/venues/${venueId.value}/schedule`)
    }

    const formatPrice = (val) => {
      return Number(val).toLocaleString('id-ID')
    }

    onMounted(() => {
      fetchVenue()
    })

    return {
      venue,
      currentImageIndex,
      formatPrice,
      handleBookNow,
      isLoading
    }
  }
}
</script>

<style scoped>
.facility-card {
  transition: transform 0.2s;
}
.facility-card:hover {
  transform: translateY(-2px);
}
.x-small {
  font-size: 0.75rem;
}
</style>