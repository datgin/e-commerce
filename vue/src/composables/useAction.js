import axiosInstance from '@/configs/axios'
import { ref } from 'vue'

export default function useAction() {
  const loading = ref(false)
  const error = ref(null)
  const messages = ref(null)
  const data = ref([])

  const getAll = async (endpoint, payload = {}) => {
    loading.value = true
    error.value = null

    try {
      const response = await axiosInstance.get(endpoint, { params: payload })

      if (response.data.success) {
        messages.value = response.data.message || 'Thao tác thành công.'
        data.value = response.data
        return response.data
      }
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const getOne = async (endpoint, id) => {
    loading.value = true
    error.value = null
    try {
      const response = await axiosInstance.get(`${endpoint}/${id}`)

      if (response.data.success) {
        data.value = response.data
        return response.data
      }
    } catch (error) {
      error.value = error
    } finally {
      loading.value = false
    }
  }

  const create = async (endpoint, payload) => {
    loading.value = true
    error.value = null
    try {
      const response = await axiosInstance.post(endpoint, payload, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      if (response.data.success) {
        messages.value = response.messages
        return response.data
      }
    } catch (err) {
      error.value = err.response.data
      return err.response.data
    } finally {
      loading.value = false
    }
  }

  const update = async (endpoint, id, payload) => {
    loading.value = true
    error.value = null
    try {
      const response = await axiosInstance.post(`${endpoint}/${id}?_method=PUT`, payload, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })

      console.log(response)

      messages.value = response.messages
      return response.data
    } catch (err) {
      error.value = err.response.data
    } finally {
      loading.value = false
    }
  }

  const destroy = async (endpoint, id, payload = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await axiosInstance.delete(`${endpoint}/${id}`, { params: payload }) // Sử dụng DELETE

      messages.value = response.data.message
      return response.data
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  return { getAll, getOne, create, update, destroy, loading, error, messages, data }
}
