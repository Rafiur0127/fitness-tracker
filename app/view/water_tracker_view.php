<!DOCTYPE html>
<html>
<head>
    <title>Water Tracker</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 50px; }
        .tracker { padding: 20px; background: #e0f7fa; border-radius: 10px; display: inline-block; }
        input[type="number"] { width: 80px; padding: 5px; }
        button { padding: 5px 15px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="tracker">
        <h2>Water Intake Tracker</h2>
        <form method="post">
            <label>Water Drank (ml): </label>
            <input type="number" name="amount_ml" required min="50" max="2000" step="50">
            <br>
            <button type="submit">Log</button>
        </form>
        <p><strong>Total today:</strong> <?= htmlspecialchars($total_today) ?> ml</p>
    </div>
</body>
</html>
