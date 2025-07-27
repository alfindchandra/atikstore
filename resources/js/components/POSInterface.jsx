import React, { useState, useEffect, useRef } from "react";

const POSInterface = () => {
    const [cart, setCart] = useState([]);
    const [searchQuery, setSearchQuery] = useState("");
    const [searchResults, setSearchResults] = useState([]);
    const [paidAmount, setPaidAmount] = useState("");
    const [isProcessingTransaction, setIsProcessingTransaction] =
        useState(false);
    const [showPaymentModal, setShowPaymentModal] = useState(false);
    const searchInputRef = useRef(null);

    // Calculate totals
    const subtotal = cart.reduce(
        (sum, item) => sum + item.quantity * item.price,
        0
    );
    const total = subtotal;
    const change = parseFloat(paidAmount || 0) - total;

    // Search products
    const handleSearch = async (query) => {
        if (query.length < 2) {
            setSearchResults([]);
            return;
        }

        try {
            const response = await fetch(
                `/pos/search?query=${encodeURIComponent(query)}`
            );
            const data = await response.json();
            setSearchResults(data);
        } catch (error) {
            console.error("Search error:", error);
            setSearchResults([]);
        }
    };

    // Add item to cart
    const addToCart = (product, unitId, unitPrice, unitSymbol) => {
        const existingItemIndex = cart.findIndex(
            (item) => item.product_id === product.id && item.unit_id === unitId
        );

        if (existingItemIndex >= 0) {
            const newCart = [...cart];
            newCart[existingItemIndex].quantity += 1;
            setCart(newCart);
        } else {
            const newItem = {
                id: Date.now() + Math.random(),
                product_id: product.id,
                unit_id: unitId,
                name: product.name,
                unit_symbol: unitSymbol,
                price: unitPrice,
                quantity: 1,
            };
            setCart([...cart, newItem]);
        }

        setSearchQuery("");
        setSearchResults([]);
        searchInputRef.current?.focus();
    };

    // Update cart item quantity
    const updateQuantity = (itemId, newQuantity) => {
        if (newQuantity <= 0) {
            removeFromCart(itemId);
            return;
        }

        setCart(
            cart.map((item) =>
                item.id === itemId ? { ...item, quantity: newQuantity } : item
            )
        );
    };

    // Remove item from cart
    const removeFromCart = (itemId) => {
        setCart(cart.filter((item) => item.id !== itemId));
    };

    // Clear cart
    const clearCart = () => {
        setCart([]);
        setPaidAmount("");
        setShowPaymentModal(false);
    };

    // Process transaction
    const processTransaction = async () => {
        if (cart.length === 0) {
            alert("Keranjang masih kosong!");
            return;
        }

        if (parseFloat(paidAmount) < total) {
            alert("Jumlah pembayaran kurang!");
            return;
        }

        setIsProcessingTransaction(true);

        try {
            const response = await fetch("/pos/process", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    items: cart.map((item) => ({
                        product_id: item.product_id,
                        unit_id: item.unit_id,
                        quantity: item.quantity,
                    })),
                    paid_amount: parseFloat(paidAmount),
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                if (window.Swal) {
                    window.Swal.fire({
                        icon: "success",
                        title: "Transaksi Berhasil!",
                        text: `No. Transaksi: ${data.transaction.transaction_number}`,
                        showCancelButton: true,
                        confirmButtonText: "Cetak Struk",
                        cancelButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(
                                `/pos/receipt/${data.transaction.id}/print`,
                                "_blank"
                            );
                        }
                    });
                }

                clearCart();
            } else {
                alert("Error: " + data.message);
            }
        } catch (error) {
            console.error("Transaction error:", error);
            alert("Terjadi kesalahan saat memproses transaksi");
        } finally {
            setIsProcessingTransaction(false);
        }
    };

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(amount);
    };

    // Handle barcode scan (Enter key)
    const handleKeyPress = (e) => {
        if (e.key === "Enter" && searchQuery) {
            handleSearch(searchQuery);
        }
    };

    useEffect(() => {
        const delayedSearch = setTimeout(() => {
            if (searchQuery) {
                handleSearch(searchQuery);
            }
        }, 300);

        return () => clearTimeout(delayedSearch);
    }, [searchQuery]);

    return (
        <div className="min-h-screen bg-gray-50 p-4">
            <div className="max-w-7xl mx-auto">
                {/* Header */}
                <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div className="flex justify-between items-center">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900">
                                Point of Sale
                            </h1>
                            <p className="text-gray-600">
                                Kasir Toko Kelontong
                            </p>
                        </div>
                        <div className="text-right">
                            <p className="text-sm text-gray-500">Tanggal</p>
                            <p className="text-lg font-semibold">
                                {new Date().toLocaleDateString("id-ID")}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Product Search & Cart */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Search Product */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h2 className="text-lg font-semibold mb-4">
                                Cari Produk
                            </h2>
                            <div className="relative">
                                <input
                                    ref={searchInputRef}
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) =>
                                        setSearchQuery(e.target.value)
                                    }
                                    onKeyPress={handleKeyPress}
                                    placeholder="Scan barcode atau ketik nama produk..."
                                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    autoFocus
                                />
                                <div className="absolute right-3 top-3">
                                    <i className="fas fa-search text-gray-400"></i>
                                </div>
                            </div>

                            {/* Search Results */}
                            {searchResults.length > 0 && (
                                <div className="mt-4 border border-gray-200 rounded-lg max-h-60 overflow-y-auto">
                                    {searchResults.map((product) => (
                                        <div
                                            key={product.id}
                                            className="p-3 border-b border-gray-100 last:border-b-0"
                                        >
                                            <div className="flex justify-between items-start">
                                                <div className="flex-1">
                                                    <h4 className="font-medium text-gray-900">
                                                        {product.name}
                                                    </h4>
                                                    <p className="text-sm text-gray-500">
                                                        {product.category}
                                                    </p>
                                                    {product.barcode && (
                                                        <p className="text-xs text-gray-400">
                                                            Barcode:{" "}
                                                            {product.barcode}
                                                        </p>
                                                    )}
                                                </div>
                                                <div className="ml-4">
                                                    <div className="space-y-1">
                                                        {product.units.map(
                                                            (unit) => (
                                                                <button
                                                                    key={
                                                                        unit.unit_id
                                                                    }
                                                                    onClick={() =>
                                                                        addToCart(
                                                                            product,
                                                                            unit.unit_id,
                                                                            unit.price,
                                                                            unit.unit_symbol
                                                                        )
                                                                    }
                                                                    className="block w-full text-right bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded text-sm"
                                                                >
                                                                    <div className="font-medium">
                                                                        {formatCurrency(
                                                                            unit.price
                                                                        )}
                                                                    </div>
                                                                    <div className="text-xs text-gray-600">
                                                                        per{" "}
                                                                        {
                                                                            unit.unit_symbol
                                                                        }
                                                                    </div>
                                                                </button>
                                                            )
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Shopping Cart */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <div className="flex justify-between items-center mb-4">
                                <h2 className="text-lg font-semibold">
                                    Keranjang Belanja
                                </h2>
                                {cart.length > 0 && (
                                    <button
                                        onClick={clearCart}
                                        className="text-red-600 hover:text-red-700 text-sm"
                                    >
                                        <i className="fas fa-trash mr-1"></i>
                                        Kosongkan
                                    </button>
                                )}
                            </div>

                            {cart.length === 0 ? (
                                <div className="text-center py-8">
                                    <i className="fas fa-shopping-cart text-gray-400 text-4xl mb-3"></i>
                                    <p className="text-gray-500">
                                        Keranjang masih kosong
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {cart.map((item) => (
                                        <div
                                            key={item.id}
                                            className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                        >
                                            <div className="flex-1">
                                                <h4 className="font-medium text-gray-900">
                                                    {item.name}
                                                </h4>
                                                <p className="text-sm text-gray-500">
                                                    {formatCurrency(item.price)}{" "}
                                                    per {item.unit_symbol}
                                                </p>
                                            </div>
                                            <div className="flex items-center space-x-3">
                                                <div className="flex items-center space-x-2">
                                                    <button
                                                        onClick={() =>
                                                            updateQuantity(
                                                                item.id,
                                                                item.quantity -
                                                                    1
                                                            )
                                                        }
                                                        className="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center"
                                                    >
                                                        <i className="fas fa-minus text-xs"></i>
                                                    </button>
                                                    <input
                                                        type="number"
                                                        value={item.quantity}
                                                        onChange={(e) =>
                                                            updateQuantity(
                                                                item.id,
                                                                parseFloat(
                                                                    e.target
                                                                        .value
                                                                ) || 0
                                                            )
                                                        }
                                                        className="w-16 text-center border border-gray-300 rounded px-2 py-1"
                                                        min="0"
                                                        step="0.1"
                                                    />
                                                    <button
                                                        onClick={() =>
                                                            updateQuantity(
                                                                item.id,
                                                                item.quantity +
                                                                    1
                                                            )
                                                        }
                                                        className="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center"
                                                    >
                                                        <i className="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>
                                                <div className="w-20 text-right">
                                                    <p className="font-semibold">
                                                        {formatCurrency(
                                                            item.quantity *
                                                                item.price
                                                        )}
                                                    </p>
                                                </div>
                                                <button
                                                    onClick={() =>
                                                        removeFromCart(item.id)
                                                    }
                                                    className="text-red-600 hover:text-red-700 p-1"
                                                >
                                                    <i className="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Payment Panel */}
                    <div className="space-y-6">
                        {/* Total Summary */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h2 className="text-lg font-semibold mb-4">
                                Ringkasan
                            </h2>
                            <div className="space-y-3">
                                <div className="flex justify-between">
                                    <span className="text-gray-600">
                                        Subtotal:
                                    </span>
                                    <span className="font-medium">
                                        {formatCurrency(subtotal)}
                                    </span>
                                </div>
                                <div className="border-t pt-3">
                                    <div className="flex justify-between text-lg font-bold">
                                        <span>Total:</span>
                                        <span className="text-blue-600">
                                            {formatCurrency(total)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Payment */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h2 className="text-lg font-semibold mb-4">
                                Pembayaran
                            </h2>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Jumlah Dibayar
                                    </label>
                                    <input
                                        type="number"
                                        value={paidAmount}
                                        onChange={(e) =>
                                            setPaidAmount(e.target.value)
                                        }
                                        placeholder="0"
                                        className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                                    />
                                </div>

                                {paidAmount && (
                                    <div className="p-3 bg-gray-50 rounded-lg">
                                        <div className="flex justify-between">
                                            <span className="text-gray-600">
                                                Kembalian:
                                            </span>
                                            <span
                                                className={`font-bold text-lg ${
                                                    change >= 0
                                                        ? "text-green-600"
                                                        : "text-red-600"
                                                }`}
                                            >
                                                {formatCurrency(change)}
                                            </span>
                                        </div>
                                    </div>
                                )}

                                {/* Quick Amount Buttons */}
                                <div className="grid grid-cols-2 gap-2">
                                    {[50000, 100000, 200000, 500000].map(
                                        (amount) => (
                                            <button
                                                key={amount}
                                                onClick={() =>
                                                    setPaidAmount(
                                                        amount.toString()
                                                    )
                                                }
                                                className="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm font-medium"
                                            >
                                                {formatCurrency(amount)}
                                            </button>
                                        )
                                    )}
                                </div>

                                <button
                                    onClick={processTransaction}
                                    disabled={
                                        cart.length === 0 ||
                                        parseFloat(paidAmount) < total ||
                                        isProcessingTransaction
                                    }
                                    className={`w-full py-4 rounded-lg font-semibold text-lg ${
                                        cart.length === 0 ||
                                        parseFloat(paidAmount) < total ||
                                        isProcessingTransaction
                                            ? "bg-gray-300 text-gray-500 cursor-not-allowed"
                                            : "bg-blue-600 hover:bg-blue-700 text-white"
                                    }`}
                                >
                                    {isProcessingTransaction ? (
                                        <span>
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                            Memproses...
                                        </span>
                                    ) : (
                                        <span>
                                            <i className="fas fa-cash-register mr-2"></i>
                                            Proses Transaksi
                                        </span>
                                    )}
                                </button>
                            </div>
                        </div>

                        {/* Quick Actions */}
                        <div className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold mb-4">
                                Aksi Cepat
                            </h3>
                            <div className="space-y-2">
                                <button
                                    onClick={() =>
                                        searchInputRef.current?.focus()
                                    }
                                    className="w-full px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg font-medium"
                                >
                                    <i className="fas fa-search mr-2"></i>
                                    Fokus Pencarian
                                </button>
                                <button
                                    onClick={() =>
                                        (window.location.href = "/products")
                                    }
                                    className="w-full px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium"
                                >
                                    <i className="fas fa-box mr-2"></i>
                                    Kelola Produk
                                </button>
                                <button
                                    onClick={() =>
                                        (window.location.href = "/stock")
                                    }
                                    className="w-full px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg font-medium"
                                >
                                    <i className="fas fa-warehouse mr-2"></i>
                                    Kelola Stok
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default POSInterface;
