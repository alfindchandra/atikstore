import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Get CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("CSRF token not found");
}

// Response interceptor for global error handling
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    window.location.href = "/login";
                    break;
                case 403:
                    window.showAlert(
                        "error",
                        "Anda tidak memiliki akses untuk melakukan tindakan ini"
                    );
                    break;
                case 419:
                    window.showAlert(
                        "error",
                        "Halaman telah kadaluarsa, silakan refresh"
                    );
                    setTimeout(() => window.location.reload(), 2000);
                    break;
                case 422:
                    // Validation errors will be handled by forms
                    break;
                case 500:
                    window.showAlert(
                        "error",
                        "Terjadi kesalahan server, silakan coba lagi"
                    );
                    break;
                default:
                    window.showAlert(
                        "error",
                        "Terjadi kesalahan, silakan coba lagi"
                    );
            }
        }
        return Promise.reject(error);
    }
);
