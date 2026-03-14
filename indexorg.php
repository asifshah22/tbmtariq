<?php

if(!$_REQUEST['r']){
echo "Error: File not found Removed.";    
exit();    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }
        button {
            background-color: #ffb300;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        /* Search input styling */
        .search-input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        /* Dropdown styling */
        .dropdown {
            position: relative;
        }
        .options {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            z-index: 1;
            display: none; /* Initially hidden */
        }
        .option {
            padding: 10px;
            cursor: pointer;
        }
        .option:hover {
            background-color: #eee;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Transaction Details</h2>
    <form action="https://tbmtariq.tbmengineering.com/indexorg1.php" method="GET">
        <label for="bank">Bank Name:</label>
        <select id="accoutname" name="accoutname" required>
        <option value="">Select Transaction Type</option>
        <option value="SHAHNAWAZ GHURYANI">shahnawaz ghuryani</option>
        <option value="SALMAN SEMEJO">salman samejo</option>
        <option value="JAVED HUSSAIN">javed hussain</option>
        </select>
        <select id="accoutnumber" name="accoutnumber" required>
        <option value="PK12MEZN0016060105434516">shahnawaz ghuryani(accout)</option>
        <option value="PK11MEZN0016770668939867">salman samejo(accout)</option>
        <option value="PK13MEZN0012650966887789">javed hussain(accout)</option>
        </select>
        

        <div class="dropdown">
            <input type="text" id="bank" name="bank" class="search-input" placeholder="Search Bank..." required autocomplete="off" onfocus="showOptions()" oninput="filterOptions()">
            <div id="options" class="options"></div>
        </div>

        <label for="accountHolder">Account Holder:</label>
        <input type="text" id="accountHolder" name="accountHolder" required>

        <label for="accountNumber">Account Number:</label>
        <input type="text" id="accountNumber" name="accountNumber" value="" maxlength="4" required>

        <label for="amount">Amount (Rs):</label>
        <input type="text" id="amount" name="amount" required>

        <button type="submit">Submit</button>
    </form>
</div>

<script>
    // Array of bank names
    const banks = [
        "HBL KONNECT",
        "State Bank",
        "Habib Bank",
        "National Bank",
        "United Bank",
        "Standard Chartered",
        "Bank Alfalah",
        "Faysal Bank",
        "MCB Bank",
        "Mezan Bank",
        "Dubai Islamic",
        "Al Baraka",
        "Easypaisa",
        "JazzCash"
    ];

    const optionsDiv = document.getElementById("options");
    const searchInput = document.getElementById("bank");

    // Function to display options
    function showOptions() {
        optionsDiv.innerHTML = '';
        banks.forEach(bank => {
            const option = document.createElement('div');
            option.classList.add('option');
            option.textContent = bank;
            option.onclick = () => selectBank(bank);
            optionsDiv.appendChild(option);
        });
        optionsDiv.style.display = 'block';
    }

    // Function to filter options based on input
    function filterOptions() {
        const searchValue = searchInput.value.toLowerCase();
        const options = optionsDiv.getElementsByClassName('option');
        Array.from(options).forEach(option => {
            if (option.textContent.toLowerCase().includes(searchValue)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Function to select a bank and hide options
    function selectBank(bank) {
        searchInput.value = bank;
        optionsDiv.style.display = 'none';
    }

    // Hide options when clicking outside
    document.addEventListener('click', function(event) {
        if (!optionsDiv.contains(event.target) && event.target !== searchInput) {
            optionsDiv.style.display = 'none';
        }
    });
</script>

</body>
</html>