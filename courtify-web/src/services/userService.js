import api from './api'

/**
 * Ambil data profil user yang sedang login.
 * @returns {Promise<Object>} - user object
 */
export async function getProfile() {
  try {
    const response = await api.get('/user')
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal mengambil profil.'
    throw new Error(message)
  }
}

/**
 * Perbarui data profil (username, no_hp).
 * @param {Object} userData - { username?, no_hp? }
 * @returns {Promise<Object>} - user terbaru
 */
export async function updateProfile(userData) {
  try {
    const payload = {}
    if (userData.username) payload.username = userData.username
    if (userData.no_hp) payload.no_hp = userData.no_hp

    const response = await api.patch('/user', payload)
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal memperbarui profil.'
    throw new Error(message)
  }
}

/**
 * Upload foto profil.
 * @param {File} file - File gambar dari input type file
 * @returns {Promise<Object>} - user dengan foto_profil terbaru
 */
export async function updatePhoto(file) {
  try {
    const formData = new FormData()
    formData.append('photo', file) // atau 'foto_profil' sesuai backend

    const response = await api.post('/user/photo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data.data || response.data
  } catch (error) {
    const message =
      error.response?.data?.message ||
      'Gagal mengupload foto.'
    throw new Error(message)
  }
}