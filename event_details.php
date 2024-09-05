<?php
// Define the function to fetch event by ID from the API
function fetch_event_by_id($eventId) {
    $api_url = "https://data.carinthia.com/api/v4/endpoints/557ea81f-6d65-6476-9e01-d196112514d2?include=image&token=9962098a5f6c6ae8d16ad5aba95afee0";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
        return null;
    }

    // Close cURL
    curl_close($ch);

    // Decode the JSON response
    $events = json_decode($response, true);

    // Loop through the events and find the one matching the $eventId
    if (isset($events['@graph'])) {
        foreach ($events['@graph'] as $event) {
            if ($event['@id'] == $eventId) {
                return $event; // Return the event that matches the ID
            }
        }
    }

    return null; // Return null if no event matches the ID
}

// Get the event ID from the URL (assuming it's passed as a GET parameter)
$eventId = isset($_GET['id']) ? $_GET['id'] : null;

if ($eventId) {
    // Fetch the event using the ID
    $event = fetch_event_by_id($eventId);

    if ($event) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($event['name']); ?> - Event Details</title>
            <link rel="stylesheet" href="style.css"> <!-- Add your CSS file here -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
        </head>
        <body>

        <!-- Main Content Container -->
        <div class="event-details-container">

            <!-- Event Image -->
            <div class="event-image">
                <img src="<?php echo htmlspecialchars($event['image'][0]['thumbnailUrl']); ?>" alt="<?php echo htmlspecialchars($event['name']); ?>">
            </div>

            <!-- Event Information and Description -->
            <div class="event-info">

                <!-- Event Title and Short Details -->
                <div class="event-short-details">
                    <h1><?php echo htmlspecialchars($event['name']); ?></h1>
                    <p><i class="fa fa-calendar"></i>
                        <?php
                        // Display the start date
                        $startDate = new DateTime($event['startDate']);
                        echo $startDate->format('d. F Y');

                        // If there's an end date, display it
                        if (!empty($event['endDate'])) {
                            $endDate = new DateTime($event['endDate']);
                            echo ' - ' . $endDate->format('d. F Y');
                        }
                        ?>
                    </p>
                    <p><i class="fa fa-map-marker"></i>
                        <?php echo isset($event['location']['name']) ? htmlspecialchars($event['location']['name']) : 'Location not specified'; ?>
                    </p>
                </div>

                <!-- Event Long Description -->
                <div class="event-description">
                    <p><?php echo !empty($event['description']) ? nl2br(htmlspecialchars($event['description'])) : 'No description available'; ?></p>
                </div>

                <!-- Additional Info (Location and Contact) -->
                <div class="event-location-info">
                    <h3>Information & Veranstalter</h3>
                    <div class="location-details">
                        <p><strong>Veranstaltungsort</strong><br>
                            <?php echo isset($event['location']['name']) ? htmlspecialchars($event['location']['name']) : 'Location not specified'; ?><br>
                            <?php echo isset($event['location']['address']) ? htmlspecialchars($event['location']['address']) : ''; ?><br>
                            <?php echo isset($event['location']['phone']) ? htmlspecialchars($event['location']['phone']) : ''; ?><br>
                            <?php echo isset($event['location']['email']) ? htmlspecialchars($event['location']['email']) : ''; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        </body>
        </html>
        <?php
    } else {
        echo '<p>Event not found.</p>';
    }
} else {
    echo '<p>No event ID provided.</p>';
}
?>
