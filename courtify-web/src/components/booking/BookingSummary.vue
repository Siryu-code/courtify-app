<template>
  <div class="card shadow-sm border-0 booking-summary">
    <!-- Venue Info Box -->
    <div class="d-flex align-items-start p-3 border-bottom">
      <img
        :src="booking.venue.image || 'https://placehold.co/100x75/e9ecef/6c757d?text=Venue'"
        :alt="booking.venue.name"
        class="rounded me-3"
        style="width: 100px; height: 75px; object-fit: cover"
      />
      <div>
        <h6 class="fw-bold mb-1">{{ booking.venue.name }}</h6>
        <p class="text-muted small mb-0">{{ booking.venue.location }}</p>
        <p class="text-muted x-small mb-0">{{ booking.venue.address || 'Downtown Sports Complex' }}</p>
      </div>
    </div>

    <!-- Booking Details Grid -->
    <div class="row g-0 text-center border-bottom">
      <div class="col-4 p-3">
        <i class="bi bi-calendar3 text-muted fs-5"></i>
        <div class="text-uppercase small text-muted fw-bold">Date</div>
        <div class="fw-bold">{{ formattedDate }}</div>
      </div>
      <div class="col-4 p-3 border-start">
        <i class="bi bi-clock text-muted fs-5"></i>
        <div class="text-uppercase small text-muted fw-bold">Time</div>
        <div class="fw-bold">{{ formattedTime }}</div>
      </div>
      <div class="col-4 p-3 border-start">
        <i class="bi bi-hourglass-split text-muted fs-5"></i>
        <div class="text-uppercase small text-muted fw-bold">Duration</div>
        <div class="fw-bold">{{ durationDisplay }}</div>
      </div>
    </div>

    <!-- Price Breakdown -->
    <div class="p-3">
      <div class="d-flex justify-content-between mb-2">
        <span>{{ courtFeeLabel }}</span>
        <span>Rp{{ formatPrice(courtFee) }}</span>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <!-- Contextual row: Admin Fee (checkout) atau Promo (success) -->
        <span v-if="context === 'checkout'">Admin Fee</span>
        <span v-else-if="booking.promo">{{ booking.promo.name }}</span>
        <span v-else>Promo</span>

        <!-- Value untuk checkout: adminFee, untuk success: promo diskon -->
        <span v-if="context === 'checkout'">Rp{{ formatPrice(adminFee) }}</span>
        <span
          v-else-if="booking.promo"
          class="text-success fw-semibold"
        >
          -{{ promoDisplay }}
        </span>
        <span v-else>-</span>
      </div>
      <hr />
      <div class="d-flex justify-content-between align-items-center">
        <span class="fw-bold">Total Price</span>
        <span class="fw-bold text-primary fs-5">Rp{{ formatPrice(totalPrice) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'BookingSummary',
  props: {
    booking: {
      type: Object,
      required: true,
      validator: (v) =>
        v &&
        v.venue &&
        typeof v.venue.name === 'string' &&
        v.start_time &&
        v.end_time &&
        typeof v.total_price === 'number'
    },
    context: {
      type: String,
      default: 'checkout',
      validator: (val) => ['checkout', 'success'].includes(val)
    },
    adminFee: {
      type: Number,
      default: 0
    },
    courtFeeLabel: {
      type: String,
      default: 'Court Fee'
    }
  },
  setup(props) {
    const formattedDate = computed(() => {
      return formatDate(props.booking.start_time, 'short')
    })
    const formattedTime = computed(() => {
      return formatTimeRange(props.booking.start_time, props.booking.end_time)
    })
    const durationDisplay = computed(() => {
      if (props.booking.duration) {
        return `${props.booking.duration} Jam`
      }
      // Hitung dari start dan end
      const start = new Date(props.booking.start_time)
      const end = new Date(props.booking.end_time)
      if (isNaN(start) || isNaN(end)) return '-'
      const diffMs = end - start
      const hours = Math.round(diffMs / (1000 * 60 * 60))
      return `${hours} Jam`
    })

    const courtFee = computed(() => {
      // Court fee adalah total dikurangi admin fee (checkout) atau total + diskon (success)
      if (props.context === 'checkout') {
        return props.booking.total_price - props.adminFee
      }
      // success: total_price sudah final, diskon hanya tampilan
      return props.booking.total_price
    })

    const totalPrice = computed(() => {
      // Checkout: total = courtFee + adminFee
      if (props.context === 'checkout') {
        return courtFee.value + props.adminFee
      }
      // Success: total_price langsung
      return props.booking.total_price
    })

    const promoDisplay = computed(() => {
      if (!props.booking.promo) return ''
      if (props.booking.promo.type === 'percent') {
        return `${props.booking.promo.value}%`
      }
      return `Rp${props.booking.promo.value.toLocaleString('id-ID')}`
    })

    function formatDate(dateStr, style) {
      if (!dateStr) return ''
      const d = new Date(dateStr)
      if (isNaN(d)) return dateStr
      const day = d.getDate()
      const month = d.toLocaleDateString('id-ID', { month: 'short' })
      if (style === 'short') return `${day} ${month}`
      return `${day} ${month} ${d.getFullYear()}`
    }

    function formatTimeRange(startStr, endStr) {
      if (!startStr || !endStr) return ''
      const s = new Date(startStr)
      const e = new Date(endStr)
      if (isNaN(s) || isNaN(e)) return ''
      const options = { hour: '2-digit', minute: '2-digit', hour12: false }
      return `${s.toLocaleTimeString('id-ID', options)} - ${e.toLocaleTimeString('id-ID', options)}`
    }

    function formatPrice(val) {
      return Number(val).toLocaleString('id-ID')
    }

    return {
      formattedDate,
      formattedTime,
      durationDisplay,
      courtFee,
      totalPrice,
      promoDisplay,
      formatPrice
    }
  }
}
</script>

<style scoped>
.x-small {
  font-size: 0.75rem;
}
.booking-summary {
  border-radius: 12px;
}
</style>