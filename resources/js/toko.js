import React from "react";
import { createRoot } from "react-dom/client";
import POSInterface from "./components/POSInterface"; // Sesuaikan path jika berbeda

// Pastikan elemen dengan id 'pos-root' ada di DOM Anda
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("pos-root");
    if (container) {
        const root = createRoot(container);
        root.render(<POSInterface />);
    }
});
