import api from './api'

/**
 * Ambil riwayat booking user yang login.
 * @returns {Promise<Array>} - array booking
 */
export async function getBookingHistory() {
  try {
    const response = await api.get('/bookings')
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal memuat riwayat booking.'
    throw new Error(message)
  }
}

/**
 * Ambil detail satu booking berdasarkan ID.
 * @param {number} id - booking ID
 * @returns {Promise<Object>} - detail booking
 */
export async function getBookingDetail(id) {
  try {
    const response = await api.get(`/bookings/${id}`)
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Booking tidak ditemukan.'
    throw new Error(message)
  }
}

/**
 * Buat booking baru.
 * @param {Object} bookingData - { venue_id, start_time, end_time, customer_name, customer_phone, promo_id?, payment_method? }
 * @returns {Promise<Object>} - booking yang baru dibuat (berisi id)
 */
export async function createBooking(bookingData) {
  try {
    const response = await api.post('/bookings', bookingData)
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal membuat booking. Silakan coba lagi.'
    throw new Error(message)
  }
}

/**
 * Kirim rating untuk booking yang sudah selesai.
 * @param {number} bookingId
 * @param {number} rating - 1 sampai 5
 * @returns {Promise<Object>} - booking yang telah diperbarui
 */
export async function submitRating(bookingId, rating) {
  try {
    const response = await api.post(`/bookings/${bookingId}/rating`, {
      rating
    })
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal mengirim rating.'
    throw new Error(message)
  }
}