#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

#define DHTPIN D2
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);

// Replace with your WiFi credentials
const char* ssid = "BG";
const char* password = "2026aphrod";

// Your PHP server URL
const char* server = "http://192.168.137.1/AB/insert_data1.php";

// Create a WiFi client object
WiFiClient client;

void setup() {
  Serial.begin(115200);
  dht.begin();

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("WiFi connected");
}

void loop() {
  // Read temperature and humidity
  float temp = dht.readTemperature();
  float hum  = dht.readHumidity();

  // Check if readings are valid
  if (isnan(temp) || isnan(hum)) {
    Serial.println("Failed to read from DHT sensor!");
    delay(2000);
    return;
  }

  // Send data only if WiFi is connected
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Use the new API with WiFiClient
    http.begin(client, server);                 // Specify destination
    http.addHeader("Content-Type", "application/json"); // Set content type

    // Create JSON string
    String jsonData = "{\"temperature\":" + String(temp) + ",\"humidity\":" + String(hum) + "}";

    // Send HTTP POST
    int httpResponseCode = http.POST(jsonData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      Serial.print("Server response: ");
      Serial.println(response);
    } else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
    }

    http.end(); 
  } else {
    Serial.println("WiFi not connected");
  }

  delay(10000); // Wait 10 seconds before next reading
}
