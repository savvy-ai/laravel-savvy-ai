import axios from 'axios'

declare global {
  interface Window {
    csrfTokenValue: any
  }
}

export default axios.create({
  baseURL: '/',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})
