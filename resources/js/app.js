import "./bootstrap";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";
import Swal from "sweetalert2";

// Make libraries available globally
window.Alpine = Alpine;
window.Chart = Chart;
window.Swal = Swal;

// Start Alpine
Alpine.start();

// Global utility functions
window.formatCurrency = function (amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
};

window.formatNumber = function (number) {
    return new Intl.NumberFormat("id-ID").format(number);
};

window.showAlert = function (type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: type,
        title: message,
    });
};

window.confirmDelete = function (callback, title = "Apakah Anda yakin?") {
    Swal.fire({
        title: title,
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};

// Auto-focus search inputs
document.addEventListener("DOMContentLoaded", function () {
    const searchInputs = document.querySelectorAll("[data-autofocus]");
    if (searchInputs.length > 0) {
        searchInputs[0].focus();
    }
});

// Barcode scanner support
let barcodeBuffer = "";
let barcodeTimeout;

document.addEventListener("keypress", function (e) {
    // Only process if focused on body or search input
    if (
        document.activeElement.tagName === "INPUT" &&
        !document.activeElement.classList.contains("barcode-input")
    ) {
        return;
    }

    clearTimeout(barcodeTimeout);

    if (e.key === "Enter") {
        if (barcodeBuffer.length > 0) {
            // Trigger barcode scan event
            const event = new CustomEvent("barcodeScan", {
                detail: { barcode: barcodeBuffer },
            });
            document.dispatchEvent(event);
            barcodeBuffer = "";
        }
    } else {
        barcodeBuffer += e.key;
        barcodeTimeout = setTimeout(() => {
            barcodeBuffer = "";
        }, 500);
    }
});

// Auto refresh for real-time data
function setupAutoRefresh() {
    const refreshElements = document.querySelectorAll("[data-auto-refresh]");

    refreshElements.forEach((element) => {
        const interval = parseInt(element.dataset.autoRefresh) || 30000;

        setInterval(() => {
            const url = element.dataset.refreshUrl || window.location.href;

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    const newElement = doc.querySelector(
                        `[data-auto-refresh="${element.dataset.autoRefresh}"]`
                    );

                    if (newElement) {
                        element.innerHTML = newElement.innerHTML;
                    }
                })
                .catch((error) => console.error("Auto refresh error:", error));
        }, interval);
    });
}

// Initialize auto refresh on page load
document.addEventListener("DOMContentLoaded", setupAutoRefresh);
