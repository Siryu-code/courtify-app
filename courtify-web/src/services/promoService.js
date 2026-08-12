import api from './api'

/**
 * Ambil semua promo yang sedang aktif.
 * Bisa di-cache di Pinia store untuk menghindari request berulang.
 * @returns {Promise<Array>} - array promo
 */
export async function getPromos() {
  try {
    const response = await api.get('/promos')
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal memuat promo.'
    throw new Error(message)
  }
}