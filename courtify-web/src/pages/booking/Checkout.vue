<template>
  <div class="checkout-page pb-5">
    <TopBar type="back" @go-back="$router.back()" />

    <div class="container py-3">
      <!-- Page Title -->
      <h4 class="fw-bold">Confirm Booking</h4>
      <p class="text-muted small">Review your details and finalize your court reservation.</p>

      <!-- Your Details -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Your Details</h6>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Full Name</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
              <input
                v-model="form.customer_name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.customer_name }"
                placeholder="e.g. Alex Johnson"
              />
              <div v-if="errors.customer_name" class="invalid-feedback">{{ errors.customer_name }}</div>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-semibold">Phone Number</label>
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
              <input
                v-model="form.customer_phone"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.customer_phone }"
                placeholder="XXX-XXXX-XXXX"
              />
              <div v-if="errors.customer_phone" class="invalid-feedback">{{ errors.customer_phone }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pilih Voucher -->
      <div class="card shadow-sm border-0 mb-3" @click="showPromoModal = true">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Pilih Voucher</h6>
            <small class="text-muted" v-if="selectedPromo">{{ selectedPromo.name }}</small>
            <small class="text-muted" v-else>Choose Voucher</small>
          </div>
          <i class="bi bi-chevron-right"></i>
        </div>
      </div>

      <!-- Pilih Metode Pembayaran -->
      <div class="card shadow-sm border-0 mb-3" @click="showPaymentModal = true">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Pilih Metode Pembayaran</h6>
            <small class="text-muted" v-if="selectedPayment">{{ selectedPayment }}</small>
            <small class="text-muted" v-else>Choose Payment</small>
          </div>
          <i class="bi bi-pencil"></i>
        </div>
      </div>

      <!-- Booking Summary -->
      <BookingSummary
        v-if="bookingData"
        :booking="bookingData"
        context="checkout"
        :adminFee="adminFee"
      />

      <!-- Confirm Button -->
      <button
        class="btn btn-primary btn-lg w-100 mt-4 fw-bold text-uppercase"
        :disabled="isSubmitting"
        @click="submitBooking"
      >
        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
        Confirm & Book ✓
      </button>
      <div v-if="submitError" class="text-danger small mt-2 text-center">{{ submitError }}</div>
    </div>

    <!-- Promo Modal -->
    <Teleport to="body">
      <div v-if="showPromoModal" class="modal-mask" @click.self="showPromoModal = false">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold">Pilih Promo</h5>
              <button class="btn-close" @click="showPromoModal = false"></button>
            </div>
            <div
              v-for="promo in promos"
              :key="promo.id"
              class="d-flex justify-content-between align-items-center border rounded p-2 mb-2"
              :class="{ 'border-primary': selectedPromoId === promo.id }"
              @click="selectPromo(promo)"
            >
              <div>
                <strong>{{ promo.name }}</strong>
                <div class="small text-muted">
                  Exp. {{ formatDate(promo.end_date) }}
                </div>
              </div>
              <input
                type="radio"
                name="promo"
                :checked="selectedPromoId === promo.id"
                @change="selectPromo(promo)"
              />
            </div>
            <button class="btn btn-primary w-100 mt-2" @click="showPromoModal = false">Done</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Payment Method Modal -->
    <Teleport to="body">
      <div v-if="showPaymentModal" class="modal-mask" @click.self="showPaymentModal = false">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold">Pilih Metode Pembayaran</h5>
              <button class="btn-close" @click="showPaymentModal = false"></button>
            </div>
            <div
              v-for="method in paymentMethods"
              :key="method"
              class="d-flex justify-content-between align-items-center border rounded p-2 mb-2"
              :class="{ 'border-primary': selectedPayment === method }"
              @click="selectedPayment = method"
            >
              <span>{{ method }}</span>
              <input
                type="radio"
                name="payment"
                :checked="selectedPayment === method"
                @change="selectedPayment = method"
              />
            </div>
            <button class="btn btn-primary w-100 mt-2" @click="showPaymentModal = false">Done</button>
          </div>
        </div>
      </div>
    </Teleport>

    <BottomNavbar activeRoute="explore" @navigate="$emit('navigate', $event)" />
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookingStore } from '@/stores/booking'
import { useUserStore } from '@/stores/user'
import axios from '@/services/api'
import TopBar from '@/components/layout/TopBar.vue'
import BottomNavbar from '@/components/layout/BottomNavBar.vue'
import BookingSummary from '@/components/booking/BookingSummary.vue'

export default {
  name: 'Checkout',
  components: { TopBar, BottomNavbar, BookingSummary },
  emits: ['navigate'],
  setup() {
    const route = useRoute()
    const router = useRouter()
    const bookingStore = useBookingStore()
    const userStore = useUserStore()

    const venueId = route.params.id
    const form = reactive({
      customer_name: userStore.user?.name || '',
      customer_phone: userStore.user?.no_hp || ''
    })
    const selectedPromoId = ref(null)
    const selectedPromo = ref(null)
    const selectedPayment = ref(null)
    const promos = ref([])
    const isSubmitting = ref(false)
    const submitError = ref('')
    const errors = reactive({})
    const showPromoModal = ref(false)
    const showPaymentModal = ref(false)
    const adminFee = ref(5000) // atau dari config

    // Dummy booking data dari store
    const bookingData = computed(() => {
      const b = bookingStore.schedule
      const venue = bookingStore.venue
      if (!b || !venue) return null
      // Hitung total sementara
      const hours = b.duration
      const subtotal = venue.price_per_hour * hours
      const totalPrice = subtotal + adminFee.value
      return {
        venue: {
          name: venue.name,
          location: venue.location || 'Courtify Arena',
          image: venue.image || null,
          address: 'Downtown Sports Complex'
        },
        start_time: `${b.date}T${b.start_time}:00`,
        end_time: computeEndTime(b.date, b.start_time, b.duration),
        total_price: totalPrice,
        duration: b.duration,
        promo: null // belum dipilih
      }
    })

    const paymentMethods = [
      'Visa (ending +4242)',
      'MasterCard (ending +5555)',
      'Dana / e-wallet'
    ]

    // Fetch available promos
    const fetchPromos = async () => {
      try {
        // const res = await axios.get('/api/promos');
        // promos.value = res.data.data;
        // Simulasi
        promos.value = [
          { id: 1, name: 'Diskon Awal', type: 'percent', value: 20, start_date: '2025-01-01', end_date: '2025-12-31' },
          { id: 2, name: 'Paket Hemat', type: 'fixed', value: 50000, start_date: '2025-03-01', end_date: '2025-06-30' }
        ]
      } catch (err) {
        console.error('Gagal mengambil promo:', err)
      }
    }

    const selectPromo = (promo) => {
      selectedPromoId.value = promo.id
      selectedPromo.value = promo
      showPromoModal.value = false
      // Update booking total di checkout (opsional)
    }

    const validate = () => {
      errors.customer_name = ''
      errors.customer_phone = ''
      if (!form.customer_name.trim()) {
        errors.customer_name = 'Nama lengkap wajib diisi'
      }
      if (!form.customer_phone.trim()) {
        errors.customer_phone = 'Nomor telepon wajib diisi'
      } else if (form.customer_phone.replace(/\D/g, '').length < 10) {
        errors.customer_phone = 'Format nomor telepon tidak valid'
      }
      if (!selectedPayment.value) {
        submitError.value = 'Pilih metode pembayaran'
        return false
      }
      return Object.keys(errors).every((k) => !errors[k])
    }

    const submitBooking = async () => {
      if (!validate()) return
      isSubmitting.value = true
      submitError.value = ''
      try {
        const payload = {
          venue_id: venueId,
          customer_name: form.customer_name,
          customer_phone: form.customer_phone,
          promo_id: selectedPromoId.value,
          payment_method: selectedPayment.value,
          start_time: bookingData.value.start_time,
          end_time: bookingData.value.end_time,
          // tambahkan durasi jika diperlukan backend
        }
        // const res = await axios.post('/api/bookings', payload);
        // const bookingId = res.data.data.id;
        // Simulasi sukses
        const bookingId = 5521
        bookingStore.clearBooking()
        router.push(`/booking/success/${bookingId}`)
      } catch (err) {
        submitError.value = 'Gagal menyelesaikan pemesanan. Silakan coba lagi.'
      } finally {
        isSubmitting.value = false
      }
    }

    function computeEndTime(date, startTime, hours) {
      if (!date || !startTime) return ''
      const [h, m] = startTime.split(':')
      const d = new Date(`${date}T${h}:${m}:00`)
      d.setHours(d.getHours() + hours)
      return d.toISOString()
    }

    function formatDate(dateStr) {
      if (!dateStr) return ''
      const d = new Date(dateStr)
      if (isNaN(d)) return dateStr
      return d.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' })
    }

    onMounted(() => {
      fetchPromos()
      // Pre-fill user data jika tersedia
    })

    return {
      form,
      selectedPromo,
      selectedPayment,
      promos,
      showPromoModal,
      showPaymentModal,
      isSubmitting,
      submitError,
      errors,
      adminFee,
      bookingData,
      paymentMethods,
      selectPromo,
      submitBooking,
      formatDate
    }
  }
}
</script>

<style scoped>
.modal-mask {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1055;
}
.modal-dialog {
  width: 90%;
  max-width: 400px;
  margin: 0;
}
</style>