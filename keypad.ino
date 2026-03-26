#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ArduinoJson.h>
#include <Keypad.h>

// ==========================
// WiFi
// ==========================
const char* ssid = "jai";
const char* password = "123456789";

// ==========================
// MQTT
// ==========================
const char* mqttServer = "broker.hivemq.com";
const int mqttPort = 1883;

const char* topicLCD = "lcd/35";
const char* topicBuzzer = "buzzer/35";
const char* topicKeypad = "keypad/35";

// ==========================
// KEYPAD
// ==========================
const byte ROWS = 4;
const byte COLS = 4;

char keys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'}
};

// :warning: HINDURA PINS (nta conflict na LCD)
byte rowPins[ROWS] = {D0, D3, D4, D5};
byte colPins[COLS] = {D6, D7, D8, D0}; // ushobora no kubihindura

Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

// ==========================
// Objects
// ==========================
WiFiClient espClient;
PubSubClient client(espClient);
LiquidCrystal_I2C lcd(0x27, 16, 2);

// ==========================
const int buzzerPin = D7;
bool buzzerState = false;
String keypadInput = "";

// ==========================
// WiFi
// ==========================
void connectWiFi() {
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
  }
}

// ==========================
// Buzzer
// ==========================
void playTone() {
  tone(buzzerPin, 1000);
  delay(300);
  noTone(buzzerPin);
}

// ==========================
// MQTT callback
// ==========================
void mqttCallback(char* topic, byte* payload, unsigned int length) {

  char message[length + 1];
  for (int i = 0; i < length; i++) message[i] = payload[i];
  message[length] = '\0';

  // LCD
  if (strcmp(topic, topicLCD) == 0) {
    JsonDocument doc;
    deserializeJson(doc, message);
    const char* value = doc["value"];

    lcd.clear();
    lcd.print(value);
  }

  // Buzzer
  if (strcmp(topic, topicBuzzer) == 0) {
    JsonDocument doc;
    deserializeJson(doc, message);
    const char* state = doc["state"];

    buzzerState = (strcmp(state, "on") == 0);
  }
}

// ==========================
// MQTT connect
// ==========================
void connectMQTT() {
  while (!client.connected()) {
    if (client.connect("NodeMCU")) {
      client.subscribe(topicLCD);
      client.subscribe(topicBuzzer);
    } else {
      delay(2000);
    }
  }
}

// ==========================
// SETUP
// ==========================
void setup() {
  Serial.begin(115200);
  Wire.begin();

  pinMode(buzzerPin, OUTPUT);

  connectWiFi();

  client.setServer(mqttServer, mqttPort);
  client.setCallback(mqttCallback);

  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.print("Ready...");
}

// ==========================
// LOOP
// ==========================
void loop() {

  if (!client.connected()) connectMQTT();
  client.loop();

  // ======================
  // KEYPAD (REAL-TIME)
  // ======================
  char key = keypad.getKey();

  if (key) {
    Serial.print("Key: ");
    Serial.println(key);

    if (key == '*') {
      keypadInput = "";

      String json = "{\"value\":\"CLEARED\"}";
      client.publish(topicKeypad, json.c_str());
    }
    else {
      keypadInput += key;

      String json = "{\"value\":\"" + keypadInput + "\"}";
      client.publish(topicKeypad, json.c_str());
    }
  }

  // ======================
  // BUZZER
  // ======================
  if (buzzerState) {
    playTone();
  }
}
