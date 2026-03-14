
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #5E2C82;
            padding: 15px;
            position: relative;
        }

        .header h2 {
            margin: 0;
            color: white;
            font-size: 18px;
            text-align: left;
        }

        .icons {
            position: absolute;
            right: 15px;
            top: 15px;
        }

        .icons img {
            width: 20px;
            margin-left: 10px;
        }

        .success-icon {
            font-size: 50px;
            color: #32CD32;
            border: 3px solid #32CD32;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: inline-block;
            line-height: 80px;
            margin-top: 20px;
        }

        .details h1 {
            font-size: 22px;
            margin: 20px 0;
            font-weight: normal;
        }

        .details .amount {
            font-size: 30px;
            color: #FFA500;
            margin-bottom: 15px;
        }

        .details .account-info {
            font-size: 16px;
        }

        .footer {
            margin-top: 20px;
            font-size: 13px;
            color: #C0C0C0;
        }

        .buttons {
            margin: 20px 0;
        }

        .buttons button {
            background-color: #5E2C82;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            margin: 5px;
        }

        .buttons .ok-btn {
            background-color: #FFA500;
            color: black;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="success-icon">&#10003;</div>
    <div class="details">
        <h1>Transaction Successful</h1>
        <p id="accoutname">IMRAN AHMED</p>
        <span style="text-col" id="accoutnumber">***5467</span>
        <p>Money Transferred</p>
        <p class="amount" id="amount">Rs.40,000</p>
        <p>to <span id="accountHolder"></span> - Account Number: ****<span id="accountNumber">8881</span></p>
        <p id="bank" >jazzCash</p>
        <p id="datetime">01 Oct 2024 &nbsp; 02:35 PM</p>
        <p id="transaction-id">Transaction ID (TID): 988907</p>
    </div>

    <div class="footer">
        <p>Transactions conducted after 09:00 PM and during holidays will show up in receiver's statement 
           in the next working day, but balance will be updated in real time.</p>
    </div>
</div>

<script>
    function getCurrentDateTime() {
        const now = new Date();
        const options = { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: true };
        const dateTimeString = now.toLocaleString('en-GB', options).replace(',', ''); // Formatting the date and time
        document.getElementById('datetime').textContent = dateTimeString;
    }

    // Get URL parameters and display the values
    function displayReceiptDetails() {
        const urlParams = new URLSearchParams(window.location.search);
        document.getElementById('bank').textContent = urlParams.get('bank') || 'jazzCash';
        document.getElementById('accountHolder').textContent = urlParams.get('accountHolder') || 'SULTAN KHAN';
        document.getElementById('accountNumber').textContent = urlParams.get('accountNumber') || '8881';
        document.getElementById('amount').textContent = 'Rs.' + (urlParams.get('amount') || '40,000');
        
        document.getElementById('accoutnumber').textContent = urlParams.get('accoutnumber');
        document.getElementById('accoutname').textContent = urlParams.get('accoutname');
        
        
    }

    window.onload = function() {
        getCurrentDateTime();
        displayReceiptDetails();
        
      var randomTransactionId = Math.floor(Math.random() * 1000000); // Random number up to 999999

      // Update the Transaction ID on the page
      document.getElementById('transaction-id').textContent = 'Transaction ID (TID): ' + randomTransactionId;

    };
</script>

</body>
</html>