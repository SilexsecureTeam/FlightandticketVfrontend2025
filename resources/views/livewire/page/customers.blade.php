<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with ALATPay</title>
    <script src="https://web.alatpay.ng/js/alatpay.js"></script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">Payment Form</h2>
        <form id="paymentForm" class="space-y-4">
            <input type="text" id="firstName" placeholder="First Name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="text" id="lastName" placeholder="Last Name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="email" id="email" placeholder="Email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="tel" id="phone" placeholder="Phone (Optional)" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="number" id="amount" placeholder="Amount (NGN)" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <button type="button" onclick="showPayment()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Pay with ALATPay</button>
        </form>
    </div>
    
    <script>
        function showPayment() {
            const firstName = document.getElementById("firstName").value;
            const lastName = document.getElementById("lastName").value;
            const email = document.getElementById("email").value;
            const phone = document.getElementById("phone").value;
            const amount = document.getElementById("amount").value;
            
            if (!firstName || !lastName || !email || !amount) {
                alert("Please fill in all required fields");
                return;
            }
            
            let popup = Alatpay.setup({
                apiKey: "b5a709370ea749e1909caac5e4517fb8", // Add your ALATPay API key
                businessId: "99fe948e-bafe-4bd3-92be-08dcabf3e16e", // Add your ALATPay business ID
                email: email,
                phone: phone,
                firstName: firstName,
                lastName: lastName,
                metadata: null,
                currency: "NGN",
                amount: parseFloat(amount),
                onTransaction: function (response) {
                    console.log("API response is ", response);
                },
                onClose: function () {
                    console.log("Payment gateway is closed");
                },
            });
            
            popup.show();
        }
    </script>
</body>
</html>