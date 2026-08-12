import api from './api'

/**
 * Ambil daftar venue dengan filter opsional.
 * @param {Object} params - { search?, sort? }
 * @returns {Promise<Array>} - array venue
 */
export async function getVenues(params = {}) {
  try {
    const query = {}
    if (params.search) query.search = params.search
    if (params.sort) query.sort = params.sort

    const response = await api.get('/venues', { params: query })
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal memuat daftar venue.'
    throw new Error(message)
  }
}

/**
 * Ambil detail satu venue.
 * @param {number} id
 * @returns {Promise<Object>} - detail venue (dengan images, facilities)
 */
export async function getVenueDetail(id) {
  try {
    const response = await api.get(`/venues/${id}`)
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Venue tidak ditemukan.'
    throw new Error(message)
  }
}

/**
 * Ambil slot waktu yang tersedia pada tanggal dan durasi tertentu.
 * @param {number} id - venue id
 * @param {string} date - format YYYY-MM-DD
 * @param {number} duration - durasi dalam jam (1-23)
 * @returns {Promise<Array>} - array slot string "HH:mm-HH:mm"
 */
export async function getAvailableSlots(id, date, duration) {
  try {
    const response = await api.get(`/venues/${id}/slots`, {
      params: { date, duration }
    })
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal memuat slot yang tersedia.'
    throw new Error(message)
  }
}