#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <Adafruit_SSD1306.h>
#include <ArduinoJson.h>

#define SS_PIN 5
#define RST_PIN 4

MFRC522 mfrc522(SS_PIN, RST_PIN);

// OLED
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// WiFi credentials
const char* ssid = "NBA";
const char* password = "kanyambo12";

// Server URL - Your PHP API endpoint
String serverName = "http://192.168.137.1/AB/api.php";

// Outputs
#define GREEN_LED 27
#define RED_LED 26
#define BUZZER 25

// Buttons
#define REG_BTN_PIN 33    // Register mode button
#define SEND_BTN_PIN 32   // Send to dashboard button

bool registerMode = false;
unsigned long lastScanTime = 0;
const unsigned long scanCooldown = 3000;

// Store last scanned animal details
String lastScannedTagId = "";
String lastScannedAnimalData = "";

void setup() {
  Serial.begin(115200);
  Serial.println("Smart Livestock RFID Tracker Starting...");

  pinMode(GREEN_LED, OUTPUT);
  pinMode(RED_LED, OUTPUT);
  pinMode(BUZZER, OUTPUT);
  pinMode(REG_BTN_PIN, INPUT_PULLUP);
  pinMode(SEND_BTN_PIN, INPUT_PULLUP);

  // Turn off all outputs
  digitalWrite(GREEN_LED, LOW);
  digitalWrite(RED_LED, LOW);
  noTone(BUZZER);

  // Initialize I2C for OLED
  Wire.begin(4, 15);
  
  // Initialize OLED
  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("OLED initialization failed!");
  }
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);
  display.println("Smart Livestock");
  display.println("RFID Tracker");
  display.display();
  delay(2000);

  // Initialize SPI and RFID
  SPI.begin();
  mfrc522.PCD_Init();
  delay(100);
  Serial.println("RFID Reader initialized");

  // Connect to WiFi
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Connecting WiFi...");
  display.println(ssid);
  display.display();

  WiFi.begin(ssid, password);
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi connected");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());
    
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("WiFi Connected!");
    display.println(WiFi.localIP());
    display.display();
    delay(2000);
    
    tone(BUZZER, 2000, 200);
    delay(250);
    noTone(BUZZER);
  } else {
    Serial.println("\nWiFi Failed!");
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("WiFi Failed!");
    display.println("Offline Mode");
    display.display();
    delay(2000);
  }

  displayReadyScreen();
}

void displayReadyScreen() {
  display.clearDisplay();
  display.setCursor(0, 0);
  if(registerMode) {
    display.println("MODE: REGISTER");
    display.println("Scan new tag");
  } else {
    display.println("MODE: SCAN");
    display.println("Ready to scan");
  }
  display.println("Send Btn: Send to Dash");
  if(WiFi.status() == WL_CONNECTED) {
    display.println("WiFi: Connected");
  } else {
    display.println("WiFi: Offline");
  }
  display.display();
}

void loop() {
  // Check register button
  if(digitalRead(REG_BTN_PIN) == LOW) {
    delay(50);
    if(digitalRead(REG_BTN_PIN) == LOW) {
      registerMode = !registerMode;
      tone(BUZZER, 2000, 100);
      delay(200);
      noTone(BUZZER);
      displayReadyScreen();
      
      while(digitalRead(REG_BTN_PIN) == LOW);
      delay(50);
    }
  }
  
  // Check send to dashboard button
  if(digitalRead(SEND_BTN_PIN) == LOW && !registerMode) {
    delay(50);
    if(digitalRead(SEND_BTN_PIN) == LOW) {
      if(lastScannedTagId != "") {
        sendToDashboard(lastScannedTagId);
        
        tone(BUZZER, 1500, 100);
        delay(200);
        noTone(BUZZER);
        
        while(digitalRead(SEND_BTN_PIN) == LOW);
        delay(50);
      } else {
        display.clearDisplay();
        display.setCursor(0, 0);
        display.println("No animal scanned");
        display.println("Scan an animal");
        display.println("first!");
        display.display();
        tone(BUZZER, 500, 300);
        delay(1500);
        displayReadyScreen();
        
        while(digitalRead(SEND_BTN_PIN) == LOW);
        delay(50);
      }
    }
  }
  
  // Check for RFID tag with cooldown
  if((millis() - lastScanTime) < scanCooldown) {
    delay(50);
    return;
  }
  
  if (!mfrc522.PICC_IsNewCardPresent()) {
    delay(50);
    return;
  }
  
  if (!mfrc522.PICC_ReadCardSerial()) {
    delay(50);
    return;
  }
  
  lastScanTime = millis();
  
  // Get tag ID
  String tagId = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    if (mfrc522.uid.uidByte[i] < 0x10) tagId += "0";
    tagId += String(mfrc522.uid.uidByte[i], HEX);
  }
  tagId.toUpperCase();

  Serial.println("=================================");
  Serial.println("TAG DETECTED!");
  Serial.print("Tag ID: ");
  Serial.println(tagId);
  Serial.println("=================================");

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Scanning...");
  display.print("Tag: ");
  display.println(tagId);
  display.display();
  
  if (registerMode) {
    registerNewTag(tagId);
  } else {
    queryAnimalByTag(tagId);
  }
  
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  delay(500);
}

void queryAnimalByTag(String tagId) {
  if (WiFi.status() != WL_CONNECTED) {
    display.clearDisplay();
    display.println("No WiFi");
    display.println(tagId);
    display.display();
    digitalWrite(RED_LED, HIGH);
    tone(BUZZER, 500, 500);
    delay(3000);
    digitalWrite(RED_LED, LOW);
    displayReadyScreen();
    return;
  }
  
  HTTPClient http;
  String url = serverName + "/animals";
  
  Serial.println("GET Request: " + url);
  http.begin(url);
  http.setTimeout(5000);
  
  int httpResponseCode = http.GET();
  
  if (httpResponseCode == 200) {
    String payload = http.getString();
    Serial.println("Response: " + payload);
    
    DynamicJsonDocument doc(4096);
    DeserializationError error = deserializeJson(doc, payload);
    
    if (!error && doc.is<JsonArray>()) {
      JsonArray animals = doc.as<JsonArray>();
      bool found = false;
      
      for (JsonObject animal : animals) {
        String rfidTag = animal["rfid_tag"].as<String>();
        rfidTag.toUpperCase();
        
        if (rfidTag == tagId) {
          found = true;
          
          lastScannedTagId = tagId;
          
          String name = animal["name"].as<String>();
          bool isSick = animal["is_sick"];
          bool isPregnant = animal["is_pregnant"];
          String ownerContact = animal["owner_contact"].as<String>();
          String animalType = animal["animal_type"].as<String>();
          String sex = animal["sex"].as<String>();
          String breed = animal["breed"].as<String>();
          
          display.clearDisplay();
          display.setCursor(0, 0);
          display.println("=== ANIMAL INFO ===");
          display.print("Name: ");
          display.println(name);
          display.print("Type: ");
          display.println(animalType);
          display.print("Status: ");
          if(isSick) {
            display.println("SICK!");
          } else {
            display.println("Healthy");
          }
          display.print("Pregnant: ");
          display.println(isPregnant ? "Yes" : "No");
          display.print("Contact: ");
          display.println(ownerContact);
          display.println();
          display.println("Press SEND to");
          display.println("send to dashboard");
          display.display();
          
          if(isSick) {
            digitalWrite(RED_LED, HIGH);
            for(int i = 0; i < 3; i++) {
              tone(BUZZER, 1000, 200);
              delay(300);
              noTone(BUZZER);
              delay(100);
            }
          } else {
            digitalWrite(GREEN_LED, HIGH);
            tone(BUZZER, 2000, 200);
          }
          
          delay(4000);
          digitalWrite(GREEN_LED, LOW);
          digitalWrite(RED_LED, LOW);
          noTone(BUZZER);
          break;
        }
      }
      
      if (!found) {
        lastScannedTagId = "";
        displayUnregisteredTag(tagId);
      }
    } else {
      displayError("JSON Parse Error");
    }
  } else {
    Serial.print("HTTP error code: ");
    Serial.println(httpResponseCode);
    displayError("Server Error: " + String(httpResponseCode));
  }
  
  http.end();
  displayReadyScreen();
}

void sendToDashboard(String tagId) {
  if (WiFi.status() != WL_CONNECTED) {
    displayError("No WiFi Connection");
    digitalWrite(RED_LED, HIGH);
    tone(BUZZER, 500, 500);
    delay(2000);
    digitalWrite(RED_LED, LOW);
    displayReadyScreen();
    return;
  }
  
  HTTPClient http;
  String url = serverName + "/send_to_dashboard";
  
  Serial.println("Sending animal to dashboard...");
  Serial.print("Tag ID: ");
  Serial.println(tagId);
  
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(5000);
  
  String jsonPayload = "{\"rfid_tag\":\"" + tagId + "\",\"source\":\"ESP32_RFID_Reader\"}";
  
  int httpResponseCode = http.POST(jsonPayload);
  
  if (httpResponseCode == 200) {
    String response = http.getString();
    Serial.println("Send to Dashboard Response: " + response);
    
    DynamicJsonDocument responseDoc(1024);
    DeserializationError error = deserializeJson(responseDoc, response);
    
    if (!error && responseDoc["success"] == true) {
      display.clearDisplay();
      display.setCursor(0, 0);
      display.println("SUCCESS!");
      display.println();
      display.println("Animal details");
      display.println("sent to");
      display.println("Dashboard!");
      display.display();
      
      digitalWrite(GREEN_LED, HIGH);
      tone(BUZZER, 2000, 300);
      delay(200);
      noTone(BUZZER);
      delay(2000);
      digitalWrite(GREEN_LED, LOW);
    } else {
      displayError("Send failed");
      digitalWrite(RED_LED, HIGH);
      tone(BUZZER, 500, 800);
      delay(800);
      noTone(BUZZER);
      digitalWrite(RED_LED, LOW);
      delay(2000);
    }
  } else {
    Serial.print("Send to Dashboard error code: ");
    Serial.println(httpResponseCode);
    displayError("Send Failed: " + String(httpResponseCode));
    digitalWrite(RED_LED, HIGH);
    tone(BUZZER, 500, 800);
    delay(800);
    noTone(BUZZER);
    digitalWrite(RED_LED, LOW);
    delay(2000);
  }
  
  http.end();
  displayReadyScreen();
}

void registerNewTag(String tagId) {
  if (WiFi.status() != WL_CONNECTED) {
    displayError("No WiFi");
    digitalWrite(RED_LED, HIGH);
    tone(BUZZER, 500, 500);
    delay(2000);
    digitalWrite(RED_LED, LOW);
    displayReadyScreen();
    return;
  }
  
  HTTPClient http;
  String url = serverName + "/register_mode";
  
  Serial.println("POST Request: " + url);
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(5000);
  
  String jsonPayload = "{\"tagId\":\"" + tagId + "\",\"action\":\"register\"}";
  
  int httpResponseCode = http.POST(jsonPayload);
  
  if (httpResponseCode == 200) {
    String response = http.getString();
    Serial.println("Register Response: " + response);
    
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("REGISTER MODE");
    display.println();
    display.print("Tag ID:");
    display.println(tagId);
    display.println();
    display.println("Ready for dashboard");
    display.println("registration");
    display.display();
    
    digitalWrite(GREEN_LED, HIGH);
    tone(BUZZER, 2000, 200);
    delay(250);
    noTone(BUZZER);
    delay(2000);
    digitalWrite(GREEN_LED, LOW);
    
    registerMode = false;
  } else {
    Serial.print("POST error code: ");
    Serial.println(httpResponseCode);
    displayError("Register Failed: " + String(httpResponseCode));
    digitalWrite(RED_LED, HIGH);
    tone(BUZZER, 500, 800);
    delay(800);
    noTone(BUZZER);
    digitalWrite(RED_LED, LOW);
    delay(2000);
  }
  
  http.end();
  displayReadyScreen();
}

void displayUnregisteredTag(String tagId) {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("UNREGISTERED TAG");
  display.println();
  display.print("ID: ");
  display.println(tagId);
  display.println();
  display.println("Use Dashboard");
  display.println("to Register");
  display.display();
  
  digitalWrite(RED_LED, HIGH);
  tone(BUZZER, 500, 500);
  delay(3000);
  digitalWrite(RED_LED, LOW);
  noTone(BUZZER);
}

void displayError(String message) {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("ERROR");
  display.println();
  display.println(message);
  display.display();
}